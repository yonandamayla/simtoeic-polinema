<?php


namespace App\Http\Controllers;

use App\Models\AlumniModel;
use App\Models\ExamScheduleModel;
use App\Models\ExamResultModel;
use App\Models\AnnouncementModel;
use App\Models\ExamRegistrationModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AlumniController extends Controller
{
    /**
     * Display alumni dashboard
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
        // Get the latest published announcements that are visible to alumni
        $announcements = AnnouncementModel::where('announcement_status', 'published')
            ->where(function ($query) {
                $query->whereJsonContains('visible_to', 'alumni')
                    ->orWhereNull('visible_to')
                    ->orWhere('visible_to', '[]')
                    ->orWhere('visible_to', '');
            })
            ->orderBy('created_at', 'desc')
            ->first();
        // Get exam scores only for the current logged-in alumni
        $examScores = ExamResultModel::where('user_id', auth()->id())
            ->where('total_score', '>', 0) // Only show actual results in the table
            ->with(['user.alumni', 'schedule'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Enhanced profile completeness check
        $alumni = AlumniModel::where('user_id', auth()->id())->first();
        $user = Auth::guard('web')->user();

        $isComplete = true;
        $missingFiles = [];
        $completedItems = 0;
        $totalItems = 5; // Total required fields: photo, ktp_scan, home_address, current_address, phone_number

        // Check each required field and count completed ones
        if ($alumni && $alumni->photo) {
            $completedItems++;
        } else {
            $isComplete = false;
            $missingFiles[] = 'Profile Photo';
        }

        if ($alumni && $alumni->ktp_scan) {
            $completedItems++;
        } else {
            $isComplete = false;
            $missingFiles[] = 'ID Card (KTP) Scan';
        }

        if ($alumni && $alumni->home_address) {
            $completedItems++;
        } else {
            $isComplete = false;
            $missingFiles[] = 'Home Address';
        }

        if ($alumni && $alumni->current_address) {
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

        return view('users-alumni.alumni-dashboard', compact(
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
     * Display alumni profile
     */
    public function profile()
    {
        $alumni = AlumniModel::where('user_id', auth()->id())->first();

        return view('users-alumni.alumni-profile', [
            'type_menu' => 'profile',
            'alumni' => $alumni
        ]);
    }

    /**
     * Update alumni profile 
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::guard('web')->user();
        if (!$user || $user->role !== 'alumni') {
            abort(403, 'Unauthorized or insufficient permissions.');
        }

        $alumni = AlumniModel::where('user_id', $user->user_id)->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|min:10|max:15|regex:/^[0-9+\-\s]+$/',  // Improved validation
            'telegram_chat_id' => 'nullable|string|regex:/^[0-9]+$/',  // Only numbers allowed
            'home_address' => 'required|string',
            'current_address' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',  // 2MB for profile photo
            'ktp_scan' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',  // 5MB for KTP scan
            'ktm_scan' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',  // 5MB for ID card scan
        ]);

        $alumni->name = $request->input('name');
        $alumni->home_address = $request->input('home_address');
        $alumni->current_address = $request->input('current_address');

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
            if ($alumni->photo && Storage::disk('public')->exists(str_replace('storage/', '', $alumni->photo))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $alumni->photo));
            }
            $path = $request->file('photo')->store('alumni/photos', 'public');
            $alumni->photo = 'storage/' . $path;
        }

        // Handle KTP scan upload
        if ($request->hasFile('ktp_scan')) {
            if ($alumni->ktp_scan && Storage::disk('public')->exists(str_replace('storage/', '', $alumni->ktp_scan))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $alumni->ktp_scan));
            }
            $ktpPath = $request->file('ktp_scan')->store('alumni/ktp', 'public');
            $alumni->ktp_scan = 'storage/' . $ktpPath;
        }

        // Handle KTM/ID card scan upload
        if ($request->hasFile('ktm_scan')) {
            if ($alumni->ktm_scan && Storage::disk('public')->exists(str_replace('storage/', '', $alumni->ktm_scan))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $alumni->ktm_scan));
            }
            $ktmPath = $request->file('ktm_scan')->store('alumni/id_card', 'public');
            $alumni->ktm_scan = 'storage/' . $ktmPath;
        }

        // Simpan perubahan data alumni
        $alumni->save();

        return redirect()->route('alumni.profile')->with('success', 'Profile updated successfully!');
    }

    public function showRegistrationForm()
    {
        $user = Auth::guard('web')->user();
        if (!$user || $user->role !== 'alumni') {
            abort(403, 'Unauthorized or insufficient permissions.');
        }

        // Get alumni data      
        $alumni = AlumniModel::where('user_id', $user->user_id)->first();

        // Check profile completeness
        $isProfileComplete = true;
        if (
            !$alumni || !$alumni->photo || !$alumni->ktp_scan  || !$alumni->home_address
            || !$alumni->current_address || !$user->phone_number
        ) {
            $isProfileComplete = false;
        }

        // Get latest exam result (score > 0 means it's a real score)
        $examResults = ExamRegistrationModel::where('user_id', $user->user_id)
            ->where('score', '>', 0)
            ->latest()
            ->first();

        // Check if alumni is already registered for an upcoming exam
        $isRegistered = ExamRegistrationModel::where('user_id', $user->user_id)
            ->where('score', 0)  // 0 means "registered but not taken"
            ->whereHas('schedule', function ($query) {
                $query->where('exam_date', '>', now());
            })
            ->exists();

        return view('users-alumni.alumni-registration', [
            'type_menu' => 'registration',
            'user' => $user,
            'alumni' => $alumni,
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
