<?php


namespace App\Http\Controllers;

use App\Models\StaffModel;
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

class StaffController extends Controller
{
    // Dashboard
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
        $announcements = AnnouncementModel::where('announcement_status', 'published')
            ->where(function ($query) {
                $query->whereJsonContains('visible_to', 'staff')
                    ->orWhereNull('visible_to')
                    ->orWhere('visible_to', '[]')
                    ->orWhere('visible_to', '');
            })
            ->orderBy('created_at', 'desc')
            ->first();
        // Get exam scores only for the current logged-in staff
        $examScores = ExamResultModel::where('user_id', auth()->id())
            ->where('total_score', '>', 0) // Only show actual results in the table
            ->with(['user.staff', 'schedule'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Enhanced profile completeness check
        $staff = StaffModel::where('user_id', auth()->id())->first();
        $user = Auth::guard('web')->user();

        $isComplete = true;
        $missingFiles = [];
        $completedItems = 0;
        $totalItems = 5; // Total required fields: photo, ktp_scan, home_address, current_address, phone_number

        // Check each required field and count completed ones
        if ($staff && $staff->photo) {
            $completedItems++;
        } else {
            $isComplete = false;
            $missingFiles[] = 'Profile Photo';
        }

        if ($staff && $staff->ktp_scan) {
            $completedItems++;
        } else {
            $isComplete = false;
            $missingFiles[] = 'ID Card (KTP) Scan';
        }

        if ($staff && $staff->home_address) {
            $completedItems++;
        } else {
            $isComplete = false;
            $missingFiles[] = 'Home Address';
        }

        if ($staff && $staff->current_address) {
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

        return view('users-staff.staff-dashboard', compact(
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

    public function profile()
    {
        $staff = StaffModel::where('user_id', auth()->id())->first();

        return view('users-staff.staff-profile', [
            'type_menu' => 'profile',
            'staff' => $staff
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::guard('web')->user();
        if (!$user || $user->role !== 'staff') {
            abort(403, 'Unauthorized or insufficient permissions.');
        }

        $staff = staffModel::where('user_id', $user->user_id)->firstOrFail();

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

        $staff->name = $request->input('name');
        $staff->home_address = $request->input('home_address');
        $staff->current_address = $request->input('current_address');

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
            if ($staff->photo && Storage::disk('public')->exists(str_replace('storage/', '', $staff->photo))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $staff->photo));
            }
            $path = $request->file('photo')->store('staff/photos', 'public');
            $staff->photo = 'storage/' . $path;
        }

        // Handle KTP scan upload
        if ($request->hasFile('ktp_scan')) {
            if ($staff->ktp_scan && Storage::disk('public')->exists(str_replace('storage/', '', $staff->ktp_scan))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $staff->ktp_scan));
            }
            $ktpPath = $request->file('ktp_scan')->store('staff/ktp', 'public');
            $staff->ktp_scan = 'storage/' . $ktpPath;
        }

        // Handle KTM/ID card scan upload
        if ($request->hasFile('ktm_scan')) {
            if ($staff->ktm_scan && Storage::disk('public')->exists(str_replace('storage/', '', $staff->ktm_scan))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $staff->ktm_scan));
            }
            $ktmPath = $request->file('ktm_scan')->store('staff/id_card', 'public');
            $staff->ktm_scan = 'storage/' . $ktmPath;
        }

        // Save the staff model
        $staff->save();

        return redirect()->route('staff.profile')->with('success', 'Profile updated successfully!');
    }

    public function showRegistrationForm()
    {
        $user = Auth::guard('web')->user();
        if (!$user || $user->role !== 'staff') {
            abort(403, 'Unauthorized or insufficient permissions.');
        }

        // Get staff data      
        $staff = StaffModel::where('user_id', $user->user_id)->first();

        // Check profile completeness
        $isProfileComplete = true;
        if (
            !$staff || !$staff->photo || !$staff->ktp_scan  || !$staff->home_address
            || !$staff->current_address || !$user->phone_number
        ) {
            $isProfileComplete = false;
        }

        // Get latest exam result (score > 0 means it's a real score)
        $examResults = ExamRegistrationModel::where('user_id', $user->user_id)
            ->where('score', '>', 0)
            ->latest()
            ->first();

        // Check if staff is already registered for an upcoming exam
        $isRegistered = ExamRegistrationModel::where('user_id', $user->user_id)
            ->where('score', 0)  // 0 means "registered but not taken"
            ->whereHas('schedule', function ($query) {
                $query->where('exam_date', '>', now());
            })
            ->exists();

        return view('users-staff.staff-registration', [
            'type_menu' => 'registration',
            'user' => $user,
            'staff' => $staff,
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
