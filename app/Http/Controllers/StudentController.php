<?php

namespace App\Http\Controllers;

use App\Models\AnnouncementModel;
use Illuminate\Http\Request;
use App\Models\ExamScheduleModel;
use App\Models\ExamResultModel;
use App\Models\StudentModel;
use App\Models\UserModel;
use App\Models\ExamRegistrationModel;
use App\Models\VerificationRequestModel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class StudentController extends Controller
{
    public function dashboard()
    {
        $type_menu = 'dashboard';
        $user = auth()->user();

        $schedules = ExamScheduleModel::join('exam_result', 'exam_schedule.schedule_id', '=', 'exam_result.schedule_id')
            ->where('exam_result.user_id', auth()->id())
            ->select('exam_schedule.*')
            ->paginate(10);

        // Get exam results only if user has actual results (not just registration with score 0)
        $examResults = ExamResultModel::where('user_id', auth()->id())
            ->where('total_score', '>', 0) // Only get actual exam results, not registration placeholders
            ->latest()
            ->first();

        // Get all exam scores for history (both gratis and mandiri)
        $allExamScores = ExamResultModel::where('user_id', auth()->id())
            ->where('total_score', '>', 0)
            ->orderBy('exam_date', 'desc')
            ->get();

        // Check if student is eligible for certificate request
        $canRequestCertificate = false;
        $examCount = $allExamScores->count();
        $hasFailedScores = $allExamScores->where('total_score', '<', 500)->count() > 0;

        // Students can request verification letter if they don't have pending request
        // No exam requirements - anyone can request for various reasons (exam failure, special conditions, etc.)
        $existingRequest = VerificationRequestModel::where('user_id', auth()->id())
            ->where('status', 'pending')
            ->first();
        $canRequestCertificate = !$existingRequest;

        // Get all exam results for the current student to display in the scores table
        $examScores = ExamResultModel::where('user_id', auth()->id())
            ->where('total_score', '>', 0) // Only show actual results in the table
            ->with(['user.student', 'schedule'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get the latest published announcements that are visible to students
        // This includes both text announcements and PDF announcements
        $announcements = AnnouncementModel::where('announcement_status', 'published')
            ->where(function ($query) {
                $query->whereJsonContains('visible_to', 'student')
                    ->orWhereNull('visible_to')
                    ->orWhere('visible_to', '[]')
                    ->orWhere('visible_to', '');
            })
            ->orderBy('created_at', 'desc')
            ->first();

        // Enhanced profile completeness check
        $student = StudentModel::where('user_id', auth()->id())->first();
        $user = Auth::guard('web')->user();

        $isComplete = true;
        $missingFiles = [];
        $completedItems = 0;
        $totalItems = 6; // Total required fields: photo, ktp_scan, ktm_scan, home_address, current_address, phone_number

        // Check each required field and count completed ones
        if ($student && $student->photo) {
            $completedItems++;
        } else {
            $isComplete = false;
            $missingFiles[] = 'Profile Photo';
        }

        if ($student && $student->ktp_scan) {
            $completedItems++;
        } else {
            $isComplete = false;
            $missingFiles[] = 'ID Card (KTP) Scan';
        }

        if ($student && $student->ktm_scan) {
            $completedItems++;
        } else {
            $isComplete = false;
            $missingFiles[] = 'Student ID Card (KTM) Scan';
        }

        if ($student && $student->home_address) {
            $completedItems++;
        } else {
            $isComplete = false;
            $missingFiles[] = 'Home Address';
        }

        if ($student && $student->current_address) {
            $completedItems++;
        } else {
            $isComplete = false;
            $missingFiles[] = 'Current Address';
        }

        if ($user && $user->phone_number) {
            $completedItems++;
        } else {
            $isComplete = false;
            $missingFiles[] = 'Phone Number';
        }

        // Calculate accurate completion percentage
        $completionPercentage = $totalItems > 0 ? round(($completedItems / $totalItems) * 100) : 0;

        // Check if the total score is below 500 (fail threshold for TOEIC)
        $examFailed = false;
        if ($examResults && $examResults->total_score < 500) {
            $examFailed = true;
        }

        return view('users-student.student-dashboard', compact(
            'schedules',
            'type_menu',
            'examResults',
            'announcements',
            'examFailed',
            'isComplete',
            'missingFiles',
            'completedItems',
            'totalItems',
            'completionPercentage',
            'examScores',
            'allExamScores',
            'canRequestCertificate',
            'user'
        ));
    }

    public function profile()
    {
        // Get the currently authenticated student
        $student = StudentModel::where('user_id', auth()->id())->first();

        return view('users-student.student-profile', [
            'type_menu' => 'profile',
            'student' => $student
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::guard('web')->user();
        if (!$user || $user->role !== 'student') {
            abort(403, 'Unauthorized or insufficient permissions.');
        }

        $student = StudentModel::where('user_id', $user->user_id)->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255|regex:/^[a-zA-Z\s\.]+$/',  // Only letters, spaces, and dots
            'phone_number' => 'required|string|min:10|max:15|regex:/^[0-9+\-\s]+$/',  // Improved validation
            'telegram_chat_id' => 'nullable|string|regex:/^[0-9]+$/',  // Only numbers allowed
            'home_address' => [
                'required',
                'string',
                'max:500',
                'regex:/^[a-zA-Z0-9\s\.\,\-\/]+$/'  // Only alphanumeric, spaces, dots, commas, hyphens, slashes
            ],
            'current_address' => [
                'required',
                'string',
                'max:500',
                'regex:/^[a-zA-Z0-9\s\.\,\-\/]+$/'  // Only alphanumeric, spaces, dots, commas, hyphens, slashes
            ],
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',  // 2MB for profile photo
            'ktp_scan' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',  // 5MB for KTP scan
            'ktm_scan' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',  // 5MB for KTM scan
        ]);

        // Update student data (exclude major and study_program) with sanitization
        $student->name = htmlspecialchars(strip_tags(trim($request->input('name'))), ENT_QUOTES, 'UTF-8');
        $student->home_address = htmlspecialchars(strip_tags(trim($request->input('home_address'))), ENT_QUOTES, 'UTF-8');
        $student->current_address = htmlspecialchars(strip_tags(trim($request->input('current_address'))), ENT_QUOTES, 'UTF-8');
        // Major and study_program are intentionally not updated as they should be fixed values

        // Update phone number and telegram_chat_id in users table with sanitization
        $userModel = UserModel::find($user->user_id);
        if ($userModel) {
            $userModel->phone_number = htmlspecialchars(strip_tags(trim($request->input('phone_number'))), ENT_QUOTES, 'UTF-8');
            $userModel->telegram_chat_id = $request->input('telegram_chat_id') ? htmlspecialchars(strip_tags(trim($request->input('telegram_chat_id'))), ENT_QUOTES, 'UTF-8') : null;
            $userModel->save();
        }

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($student->photo && Storage::disk('public')->exists(str_replace('storage/', '', $student->photo))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $student->photo));
            }
            $path = $request->file('photo')->store('student/photos', 'public');
            $student->photo = 'storage/' . $path;
        }

        // Handle KTP scan upload
        if ($request->hasFile('ktp_scan')) {
            if ($student->ktp_scan && Storage::disk('public')->exists(str_replace('storage/', '', $student->ktp_scan))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $student->ktp_scan));
            }
            $ktpPath = $request->file('ktp_scan')->store('student/ktp', 'public');
            $student->ktp_scan = 'storage/' . $ktpPath;
        }

        // Handle KTM scan upload
        if ($request->hasFile('ktm_scan')) {
            if ($student->ktm_scan && Storage::disk('public')->exists(str_replace('storage/', '', $student->ktm_scan))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $student->ktm_scan));
            }
            $ktmPath = $request->file('ktm_scan')->store('student/ktm', 'public');
            $student->ktm_scan = 'storage/' . $ktmPath;
        }

        $student->save();

        return redirect()->route('student.profile')->with('success', 'Profile updated successfully!');
    }

    /**
     * Display the exam registration form.
     */
    public function showRegistrationForm()
    {
        $user = Auth::guard('web')->user();
        if (!$user || $user->role !== 'student') {
            abort(403, 'Unauthorized or insufficient permissions.');
        }

        // Get student data
        $student = StudentModel::where('user_id', $user->user_id)->first();

        // Check profile completeness
        $isProfileComplete = true;
        if (
            !$student || !$student->photo || !$student->ktp_scan || !$student->ktm_scan ||
            !$student->home_address || !$student->current_address || !$user->phone_number
        ) {
            $isProfileComplete = false;
        }

        // Get latest exam result (score > 0 means it's a real score)
        $examResults = ExamRegistrationModel::where('user_id', $user->user_id)
            ->where('score', '>', 0)
            ->latest()
            ->first();

        // Check if student is already registered for an upcoming exam
        $isRegistered = ExamRegistrationModel::where('user_id', $user->user_id)
            ->where('score', 0)  // 0 means "registered but not taken"
            ->whereHas('schedule', function ($query) {
                $query->where('exam_date', '>', now());
            })
            ->exists();

        return view('users-student.registration', [
            'type_menu' => 'registration',
            'user' => $user,
            'student' => $student,
            'examResults' => $examResults,
            'isProfileComplete' => $isProfileComplete,
            'isRegistered' => $isRegistered
        ]);
    }

    /**
     * Process a new exam registration.
     */
    public function registerExam(Request $request)
    {
        $user = Auth::guard('web')->user();
        if (!$user || $user->role !== 'student') {
            abort(403, 'Unauthorized or insufficient permissions.');
        }

        // Check if the student is eligible for free registration
        if ($user->exam_status !== 'not_yet') {
            $errorMessage = $user->exam_status === 'on_process'
                ? 'You have already registered for an exam. Please wait for the results to be uploaded.'
                : 'You are not eligible for free exam registration. Please use the paid option.';

            return redirect()->route('student.registration.form')
                ->with('error', $errorMessage);
        }

        // Check if student is already registered
        $isAlreadyRegistered = ExamRegistrationModel::where('user_id', $user->user_id)
            ->where('score', 0) // Use 0 as placeholder for "just registered"
            ->whereHas('schedule', function ($query) {
                $query->where('exam_date', '>', now());
            })
            ->exists();

        if ($isAlreadyRegistered) {
            return redirect()->route('student.registration.form')
                ->with('error', 'You are already registered for an upcoming exam.');
        }

        // Get upcoming exam schedules
        $upcomingSchedule = ExamScheduleModel::where('exam_date', '>', now())
            ->orderBy('exam_date')
            ->first();

        if (!$upcomingSchedule) {
            return redirect()->route('student.registration.form')
                ->with('error', 'No upcoming exam schedules available. Please try again later.');
        }

        ExamRegistrationModel::create([
            'user_id' => $user->user_id,
            'schedule_id' => $upcomingSchedule->schedule_id,
            'score' => 0,  // Use 0 as placeholder for "not taken yet"
            'cerfificate_url' => ''  // Include with empty value
        ]);

        // Update user exam status to 'on_process' after successful registration
        $userModel = UserModel::find($user->user_id);
        if ($userModel) {
            $userModel->exam_status = 'on_process';
            $userModel->save();
        }

        // Redirect with success message
        return redirect()->route('student.registration.form')
            ->with('success', 'You have successfully registered for the TOEIC exam. Please check your telegram for confirmation details.');
    }

    /**
     * Show request page with verification requests
     */
    public function requestIndex()
    {
        $user = Auth::guard('web')->user();
        if (!$user || $user->role !== 'student') {
            abort(403, 'Unauthorized or insufficient permissions.');
        }

        // Get all verification requests for this student
        $verificationRequests = VerificationRequestModel::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        // Get exam results for eligibility check
        $allExamScores = ExamResultModel::where('user_id', auth()->id())
            ->where('total_score', '>', 0)
            ->orderBy('exam_date', 'desc')
            ->get();

        // Check eligibility for new request
        $canRequestCertificate = false;
        $examCount = $allExamScores->count();
        $hasFailedScores = $allExamScores->where('total_score', '<', 500)->count() > 0;

        // Students can request verification letter if:
        // 1. They have taken 2+ exams with scores < 500, OR
        // 2. They have special conditions (mental disability, etc.) regardless of exam history
        // Check if there's no pending request
        $existingRequest = VerificationRequestModel::where('user_id', auth()->id())
            ->where('status', 'pending')
            ->first();

        $canRequestCertificate = !$existingRequest; // Anyone can request if no pending request exists

        return view('users-student.request-index', [
            'type_menu' => 'request',
            'verificationRequests' => $verificationRequests,
            'allExamScores' => $allExamScores,
            'canRequestCertificate' => $canRequestCertificate,
            'examCount' => $examCount,
            'hasFailedScores' => $hasFailedScores,
            'existingRequest' => $existingRequest
        ]);
    }

    /**
     * Show verification request form
     */
    public function showVerificationRequestForm()
    {
        $user = Auth::guard('web')->user();
        if (!$user || $user->role !== 'student') {
            abort(403, 'Unauthorized or insufficient permissions.');
        }

        // Get exam scores for display (optional)
        $allExamScores = ExamResultModel::where('user_id', auth()->id())
            ->where('total_score', '>', 0)
            ->orderBy('exam_date', 'desc')
            ->get();

        // Check if there's already a pending request
        $existingRequest = VerificationRequestModel::where('user_id', auth()->id())
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return redirect()->route('student.request.index')
                ->with('error', 'You already have a verification request that is being processed.');
        }

        return view('users-student.verification-request', [
            'type_menu' => 'verification_request',
            'examScores' => $allExamScores
        ]);
    }

    /**
     * Submit verification request
     */
    public function submitVerificationRequest(Request $request)
    {
        $user = Auth::guard('web')->user();
        if (!$user || $user->role !== 'student') {
            abort(403, 'Unauthorized or insufficient permissions.');
        }
        $request->validate([
            'comment' => 'required|string|max:1000',
            'certificate_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max - only 1 document required
            'additional_documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120' // Additional documents optional
        ]);        // Store the uploaded certificates
        $certificateFile = $request->file('certificate_file');
        $fileName = time() . '_' . $user->user_id . '_' . $certificateFile->getClientOriginalName();
        $filePath = $certificateFile->storeAs('verification_requests', $fileName, 'public');

        // Handle additional documents if any
        $additionalFiles = [];
        if ($request->hasFile('additional_documents')) {
            foreach ($request->file('additional_documents') as $index => $file) {
                $additionalFileName = time() . '_' . ($index + 2) . '_' . $user->user_id . '_' . $file->getClientOriginalName();
                $additionalFilePath = $file->storeAs('verification_requests', $additionalFileName, 'public');
                $additionalFiles[] = $additionalFilePath;
            }
        }

        // Create the request
        VerificationRequestModel::create([
            'user_id' => $user->user_id,
            'comment' => $request->comment,
            'certificate_file' => $filePath,
            'certificate_file_2' => !empty($additionalFiles) ? $additionalFiles[0] : null, // Store first additional file in old field for compatibility
            'status' => 'pending'
        ]);
        return redirect()->route('student.request.index')
            ->with('success', 'Verification request has been submitted successfully. Please wait for admin approval.');
    }

    /**
     * Get verification request detail for modal display
     */
    public function getRequestDetail($id)
    {
        $user = Auth::guard('web')->user();
        if (!$user || $user->role !== 'student') {
            abort(403, 'Unauthorized or insufficient permissions.');
        }

        // Get the request only if it belongs to current user
        $request = VerificationRequestModel::with(['approvedBy'])
            ->where('request_id', $id)
            ->where('user_id', $user->user_id) // Ensure user can only see their own requests
            ->first();

        if (!$request) {
            return response()->json([
                'success' => false,
                'message' => 'Request not found or access denied.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'request_id' => $request->request_id,
                'comment' => $request->comment,
                'status' => $request->status,
                'admin_notes' => $request->admin_notes,
                'approved_by' => $request->approvedBy ? $request->approvedBy->name : null,
                'created_at' => $request->formatted_created_at,
                'approved_at' => $request->formatted_approved_at
            ]
        ]);
    }

    public function updateCertificateStatus($status)
    {
        $authUser = Auth::user();
        $user = UserModel::find($authUser->user_id);
        if ($user) {
            $user->certificate_status = $status;
            $user->save();
            return redirect()->back()->with('success', 'Certifiate status updated successfully.');
        } else {
            return redirect()->back()->with('error', 'User not found.');
        }
    }
}
