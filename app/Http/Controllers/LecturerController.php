<?php

namespace App\Http\Controllers;

use App\Models\AnnouncementModel;
use App\Models\ExamRegistrationModel;
use App\Models\ExamResultModel;
use App\Models\ExamScheduleModel;
use App\Models\LecturerModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class LecturerController extends Controller
{
    /**
     * Display lecturer dashboard
     */
    public function dashboard()
    {
        $type_menu = 'dashboard';
        $user = auth()->user();

        $schedules = ExamScheduleModel::join('exam_result', 'exam_schedule.schedule_id', '=', 'exam_result.schedule_id')
            ->where('exam_result.user_id', auth()->id())
            ->select('exam_schedule.*')
            ->paginate(10);

        $examResults = ExamResultModel::where('user_id', auth()->id())
            ->where('total_score', '>', 0) // Only get actual exam results, not registration placeholders
            ->latest()
            ->first();
        // Get the latest published announcements that are visible to lecturers
        $announcements = AnnouncementModel::where('announcement_status', 'published')
            ->where(function ($query) {
                $query->whereJsonContains('visible_to', 'lecturer')
                    ->orWhereNull('visible_to')
                    ->orWhere('visible_to', '[]')
                    ->orWhere('visible_to', '');
            })
            ->orderBy('created_at', 'desc')
            ->first();
        // Get exam scores only for the current logged-in lecturer
        $examScores = ExamResultModel::where('user_id', auth()->id())
            ->where('total_score', '>', 0) // Only show actual results in the table
            ->with(['user.lecturer', 'schedule'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Enhanced profile completeness check
        $lecturer = LecturerModel::where('user_id', auth()->id())->first();
        $user = Auth::guard('web')->user();

        $isComplete = true;
        $missingFiles = [];
        $completedItems = 0;
        $totalItems = 5; // Total required fields: photo, ktp_scan, home_address, current_address, phone_number

        // Check each required field and count completed ones
        if ($lecturer && $lecturer->photo) {
            $completedItems++;
        } else {
            $isComplete = false;
            $missingFiles[] = 'Profile Photo';
        }

        if ($lecturer && $lecturer->ktp_scan) {
            $completedItems++;
        } else {
            $isComplete = false;
            $missingFiles[] = 'ID Card (KTP) Scan';
        }

        if ($lecturer && $lecturer->home_address) {
            $completedItems++;
        } else {
            $isComplete = false;
            $missingFiles[] = 'Home Address';
        }

        if ($lecturer && $lecturer->current_address) {
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

        // Check if the score is below or equal to 70
        $examFailed = false;
        if ($examResults && $examResults->score <= 70) {
            $examFailed = true;
        }

        return view('users-lecturer.lecturer-dashboard', compact(
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
            'user'
        ));
    }
    /**
     * Display lecturer profile
     */
    public function profile()
    {
        $user = Auth::guard('web')->user();
        $lecturer = LecturerModel::where('user_id', auth()->id())->first();

        // If lecturer record doesn't exist but user is a lecturer, create a new lecturer record
        if (!$lecturer && $user && $user->role === 'lecturer') {
            try {
                $lecturer = new LecturerModel();
                $lecturer->user_id = $user->user_id;
                $lecturer->name = $user->name ?? '';
                $lecturer->nidn = '-'; // Providing a dash as default value for nidn
                $lecturer->home_address = ''; // Adding default for home_address
                $lecturer->current_address = ''; // Adding default for current_address
                $lecturer->save();
            } catch (\Exception $e) {                // Log the error if saving failed
                \Illuminate\Support\Facades\Log::error('Failed to create lecturer record: ' . $e->getMessage());
            }
        }

        return view('users-lecturer.lecturer-profile', [
            'type_menu' => 'profile',
            'lecturer' => $lecturer
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::guard('web')->user();
        if (!$user || $user->role !== 'lecturer') {
            abort(403, 'Unauthorized or insufficient permissions.');
        }        // Find or create lecturer record
        $lecturer = LecturerModel::where('user_id', $user->user_id)->first();
        if (!$lecturer) {
            $lecturer = new LecturerModel();
            $lecturer->user_id = $user->user_id;
            // Set default values if necessary
            $lecturer->name = $user->name ?? '';
            $lecturer->nidn = '-'; // Providing a dash as default value for nidn
            $lecturer->home_address = ''; // Adding default for home_address
            $lecturer->current_address = ''; // Adding default for current_address
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|min:10|max:15|regex:/^[0-9+\-\s]+$/',
            'telegram_chat_id' => 'nullable|string|regex:/^[0-9]+$/',  // Only numbers allowed
            'home_address' => 'required|string',
            'current_address' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',  // 2MB for profile photo
            'ktp_scan' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',  // 5MB for KTP scan
            'ktm_scan' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',  // 5MB for ID card scan
        ]);

        $lecturer->name = $request->input('name');
        $lecturer->home_address = $request->input('home_address');
        $lecturer->current_address = $request->input('current_address');

        // Update phone number and telegram_chat_id
        $userModel = UserModel::find($user->user_id);
        if ($userModel) {
            $userModel->phone_number = $request->input('phone_number');
            $userModel->telegram_chat_id = $request->input('telegram_chat_id');
            $userModel->save();
        }

        // Handle photo upload  
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($lecturer->photo && Storage::disk('public')->exists(str_replace('storage/', '', $lecturer->photo))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $lecturer->photo));
            }
            $path = $request->file('photo')->store('lecturer/photos', 'public');
            $lecturer->photo = 'storage/' . $path;
        }

        // Handle KTP scan upload
        if ($request->hasFile('ktp_scan')) {
            if ($lecturer->ktp_scan && Storage::disk('public')->exists(str_replace('storage/', '', $lecturer->ktp_scan))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $lecturer->ktp_scan));
            }
            $ktpPath = $request->file('ktp_scan')->store('lecturer/ktp', 'public');
            $lecturer->ktp_scan = 'storage/' . $ktpPath;
        }

        // Handle KTM/ID card scan upload
        if ($request->hasFile('ktm_scan')) {
            if ($lecturer->ktm_scan && Storage::disk('public')->exists(str_replace('storage/', '', $lecturer->ktm_scan))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $lecturer->ktm_scan));
            }
            $ktmPath = $request->file('ktm_scan')->store('lecturer/id_card', 'public');
            $lecturer->ktm_scan = 'storage/' . $ktmPath;
        }

        // Save lecturer data
        $lecturer->save();

        return redirect()->route('lecturer.profile')->with('success', 'Profile updated successfully!');
    }

    public function showRegistrationForm()
    {
        $user = Auth::guard('web')->user();
        if (!$user || $user->role !== 'lecturer') {
            abort(403, 'Unauthorized or insufficient permissions.');
        }

        // Get lecturer data      
        $lecturer = lecturerModel::where('user_id', $user->user_id)->first();

        // Check profile completeness
        $isProfileComplete = true;
        if (
            !$lecturer || !$lecturer->photo || !$lecturer->ktp_scan  || !$lecturer->home_address
            || !$lecturer->current_address || !$user->phone_number
        ) {
            $isProfileComplete = false;
        }

        // Get latest exam result (score > 0 means it's a real score)
        $examResults = ExamRegistrationModel::where('user_id', $user->user_id)
            ->where('score', '>', 0)
            ->latest()
            ->first();

        // Check if lecturer is already registered for an upcoming exam
        $isRegistered = ExamRegistrationModel::where('user_id', $user->user_id)
            ->where('score', 0)  // 0 means "registered but not taken"
            ->whereHas('schedule', function ($query) {
                $query->where('exam_date', '>', now());
            })
            ->exists();

        return view('users-lecturer.lecturer-registration', [
            'type_menu' => 'registration',
            'user' => $user,
            'lecturer' => $lecturer,
            'examResults' => $examResults,
            'isProfileComplete' => $isProfileComplete,
            'isRegistered' => $isRegistered
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
