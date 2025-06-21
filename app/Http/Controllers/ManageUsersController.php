<?php

namespace App\Http\Controllers;

use App\Models\AdminModel;
use App\Models\AlumniModel;
use App\Models\LecturerModel;
use App\Models\StaffModel;
use App\Models\StudentModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class ManageUsersController extends Controller
{
    public function list(Request $request)
    {
        $user = UserModel::select('user_id', 'identity_number', 'role', 'exam_status', 'certificate_status', 'phone_number', 'telegram_chat_id', 'created_at', 'updated_at')
            ->with(['alumni', 'student', 'staff', 'lecturer', 'admin']);

        if ($request->has('role') && !empty($request->role)) {
            $user->where('role', $request->role);
        }

        return DataTables::of($user)
            ->addColumn('name', function ($user) {
                switch ($user->role) {
                    case 'student':
                        $profile = $user->student;
                        return $profile ? $profile->name : 'N/A';
                    case 'lecturer':
                        $profile = $user->lecturer;
                        return $profile ? $profile->name : 'N/A';
                    case 'staff':
                        $profile = $user->staff;
                        return $profile ? $profile->name : 'N/A';
                    case 'alumni':
                        $profile = $user->alumni;
                        return $profile ? $profile->name : 'N/A';
                    case 'admin':
                        $profile = $user->admin;
                        return $profile ? $profile->name : 'N/A';
                    default:
                        return 'N/A';
                }
            })
            ->addColumn('home_address', function ($user) {
                switch ($user->role) {
                    case 'student':
                        $profile = $user->student;
                        return $profile ? $profile->home_address : '-';
                    case 'lecturer':
                        $profile = $user->lecturer;
                        return $profile ? $profile->home_address : '-';
                    case 'staff':
                        $profile = $user->staff;
                        return $profile ? $profile->home_address : '-';
                    case 'alumni':
                        $profile = $user->alumni;
                        return $profile ? $profile->home_address : '-';
                    case 'admin':
                        $profile = $user->admin;
                        return $profile ? $profile->home_address : '-';
                    default:
                        return '-';
                }
            })
            ->addColumn('current_address', function ($user) {
                switch ($user->role) {
                    case 'student':
                        $profile = $user->student;
                        return $profile ? $profile->current_address : '-';
                    case 'lecturer':
                        $profile = $user->lecturer;
                        return $profile ? $profile->current_address : '-';
                    case 'staff':
                        $profile = $user->staff;
                        return $profile ? $profile->current_address : '-';
                    case 'alumni':
                        $profile = $user->alumni;
                        return $profile ? $profile->current_address : '-';
                    case 'admin':
                        $profile = $user->admin;
                        return $profile ? $profile->current_address : '-';
                    default:
                        return '-';
                }
            })
            ->editColumn('ktp_scan', function ($user) {
                switch ($user->role) {
                    case 'student':
                        $profile = $user->student;
                        return $profile ? asset($profile->ktp_scan) : '-';
                    case 'lecturer':
                        $profile = $user->lecturer;
                        return $profile ? asset($profile->ktp_scan) : '-';
                    case 'staff':
                        $profile = $user->staff;
                        return $profile ? asset($profile->ktp_scan) : '-';
                    case 'alumni':
                        $profile = $user->alumni;
                        return $profile ? asset($profile->ktp_scan) : '-';
                    default:
                        return '-';
                }
            })
            ->editColumn('photo', function ($user) {
                switch ($user->role) {
                    case 'student':
                        $profile = $user->student;
                        return $profile ? asset($profile->photo) : '-';
                    case 'lecturer':
                        $profile = $user->lecturer;
                        return $profile ? asset($profile->photo) : '-';
                    case 'staff':
                        $profile = $user->staff;
                        return $profile ? asset($profile->photo) : '-';
                    case 'alumni':
                        $profile = $user->alumni;
                        return $profile ? asset($profile->photo) : '-';
                    default:
                        return '-';
                }
            })
            ->addColumn('exam_status', function ($user) {
                $examStatus = $user->exam_status ?? '-';
                $badgeClass = '';
                switch (strtolower($examStatus)) {
                    case 'success':
                        $badgeClass = 'badge-success';
                        break;
                    case 'not_yet':
                        $badgeClass = 'badge-warning';
                        break;
                    case 'on_process':
                        $badgeClass = 'badge-info';
                        break;
                    case 'fail':
                        $badgeClass = 'badge-danger';
                        break;
                    default:
                        $badgeClass = 'badge-secondary';
                }
                return '<span class="badge ' . $badgeClass . '">' . ucfirst(str_replace('_', ' ', $examStatus)) . '</span>';
            })
            ->addColumn('certificate_status', function ($user) {
                $certificateStatus = $user->certificate_status ?? '-';
                $badgeClass = '';
                switch (strtolower($certificateStatus)) {
                    case 'taken':
                        $badgeClass = 'badge-success';
                        break;
                    case 'not_taken':
                        $badgeClass = 'badge-danger';
                        break;
                    default:
                        $badgeClass = 'badge-secondary';
                }
                return '<span class="badge ' . $badgeClass . '">' . ucfirst(str_replace('_', ' ', $certificateStatus)) . '</span>';
            })
            ->addColumn('action', function ($user) {
                $btn = '<button onclick="modalAction(\'' . url('users/' . $user->user_id . '/show_ajax') . '\')" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></button> ';
                $btn .= '<button onclick="modalAction(\'' . url('users/' . $user->user_id . '/edit_ajax') . '\')" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></button> ';
                $btn .= '<button onclick="modalAction(\'' . url('users/' . $user->user_id . '/delete_ajax') . '\')" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button> ';
                return $btn;
            })
            ->rawColumns(['action', 'exam_status', 'certificate_status', 'name', 'home_address', 'current_address', 'ktp_scan', 'photo'])
            ->make(true);
    }

    public function show_ajax($user_id)
    {
        $user = UserModel::with(['student', 'lecturer', 'staff', 'alumni', 'admin'])->findOrFail($user_id);

        $profile = null;
        if ($user->role === 'student') {
            $profile = StudentModel::where('user_id', $user->user_id)->first();
        } elseif ($user->role === 'lecturer') {
            $profile = LecturerModel::where('user_id', $user->user_id)->first();
        } elseif ($user->role === 'staff') {
            $profile = StaffModel::where('user_id', $user->user_id)->first();
        } elseif ($user->role === 'alumni') {
            $profile = AlumniModel::where('user_id', $user->user_id)->first();
        } elseif ($user->role === 'admin') {
            $profile = AdminModel::where('user_id', $user->user_id)->first();
        }

        return view('users-admin.manage-user.show', [
            'user' => $user,
            'profile' => $profile,
        ]);
    }

    public function edit_ajax($user_id)
    {
        $user = UserModel::with(['student', 'lecturer', 'staff', 'alumni', 'admin'])->findOrFail($user_id);

        $profile = null;
        if ($user->role === 'student') {
            $profile = StudentModel::where('user_id', $user->user_id)->first();
        } elseif ($user->role === 'lecturer') {
            $profile = LecturerModel::where('user_id', $user->user_id)->first();
        } elseif ($user->role === 'staff') {
            $profile = StaffModel::where('user_id', $user->user_id)->first();
        } elseif ($user->role === 'alumni') {
            $profile = AlumniModel::where('user_id', $user->user_id)->first();
        } elseif ($user->role === 'admin') {
            $profile = AdminModel::where('user_id', $user->user_id)->first();
        }

        return view('users-admin.manage-user.edit', [
            'user' => $user,
            'profile' => $profile,
        ]);
    }

    public function update_ajax(Request $request, $user_id)
    {
        try {
            // Get the current user to check if identity_number actually changed
            $currentUser = UserModel::findOrFail($user_id);

            // Validation rules
            $rules = [
                'role' => 'required|in:admin,student,lecturer,staff,alumni',
                'name' => 'required|min:3',
                'home_address' => 'nullable|string',
                'current_address' => 'nullable|string',
            ];

            // Only validate identity_number uniqueness if it's different from current
            if ($request->identity_number !== $currentUser->identity_number) {
                $rules['identity_number'] = 'required|min:5|unique:users,identity_number';
            } else {
                $rules['identity_number'] = 'required|min:5';
            }

            // Debug logging
            Log::info('Identity number validation check: ', [
                'user_id' => $user_id,
                'current_identity' => $currentUser->identity_number,
                'new_identity' => $request->identity_number,
                'is_different' => $request->identity_number !== $currentUser->identity_number,
                'validation_rule' => $rules['identity_number']
            ]);

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                Log::error('Validation failed for user update: ', [
                    'user_id' => $user_id,
                    'request_data' => $request->all(),
                    'validation_errors' => $validator->errors()->messages()
                ]);
                return response()->json([
                    'status' => false,
                    'msgField' => $validator->errors()->messages(),
                ], 422);
            }

            // Load with relationships
            $user = UserModel::with(['student', 'lecturer', 'staff', 'alumni', 'admin'])->findOrFail($user_id);

            // Update user basic info first
            $user->update([
                'role' => $request->role,
                'identity_number' => $request->identity_number,
            ]);

            // Get current profile based on role
            $profile = null;
            switch ($request->role) {
                case 'student':
                    $profile = $user->student ?: new StudentModel();
                    break;
                case 'lecturer':
                    $profile = $user->lecturer ?: new LecturerModel();
                    break;
                case 'staff':
                    $profile = $user->staff ?: new StaffModel();
                    break;
                case 'alumni':
                    $profile = $user->alumni ?: new AlumniModel();
                    break;
                case 'admin':
                    $profile = $user->admin ?: new AdminModel();
                    break;
            }

            if ($profile) {
                // Common profile data
                $profileData = [
                    'user_id' => $user->user_id,
                    'name' => $request->name,
                    'home_address' => $request->home_address,
                    'current_address' => $request->current_address,
                ];

                // Add role-specific fields
                switch ($request->role) {
                    case 'student':
                        $profileData['nim'] = $request->identity_number;
                        // Only set defaults if it's a new profile
                        if (!$profile->exists) {
                            $profileData['batch'] = date('Y');
                            $profileData['status'] = 'active';
                            $profileData['major'] = 'N/A';
                            $profileData['study_program'] = 'N/A';
                            $profileData['campus'] = 'malang';
                        }
                        break;
                    case 'lecturer':
                        $profileData['nidn'] = $request->identity_number;
                        break;
                    case 'staff':
                        $profileData['nip'] = $request->identity_number;
                        break;
                    case 'alumni':
                        $profileData['nik'] = $request->identity_number;
                        break;
                    case 'admin':
                        $profileData['nidn'] = $request->identity_number;
                        break;
                }

                $profile->fill($profileData);
                $profile->save();
            }

            return response()->json(['status' => true, 'message' => 'User updated successfully']);
        } catch (\Exception $e) {
            Log::error('Error in update_ajax: ', [
                'user_id' => $user_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    public function confirm_ajax($user_id)
    {
        $user = UserModel::findOrFail($user_id);

        $profile = null;
        if ($user->role === 'student') {
            $profile = StudentModel::where('user_id', $user->user_id)->first();
        } elseif ($user->role === 'lecturer') {
            $profile = LecturerModel::where('user_id', $user->user_id)->first();
        } elseif ($user->role === 'staff') {
            $profile = StaffModel::where('user_id', $user->user_id)->first();
        } elseif ($user->role === 'alumni') {
            $profile = AlumniModel::where('user_id', $user->user_id)->first();
        }

        return view('users-admin.manage-user.delete', [
            'user' => $user,
            'profile' => $profile
        ]);
    }

    public function delete_ajax($user_id)
    {
        $user = UserModel::findOrFail($user_id);

        if ($user) {
            $user->delete();

            return response()->json([
                'status' => true,
                'message' => 'User deleted successfully'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'User not found.'
            ]);
        }
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|min:3',
            'identity_number' => 'required|unique:users,identity_number|min:5',
            'role' => 'required|in:student,lecturer,staff,alumni,admin',
            'password' => ['required', 'min:8', 'regex:/[A-Za-z]/', 'regex:/[0-9]/'],
            'password_confirmation' => 'required|same:password'
        ];

        // Add student-specific validation rules if role is student
        if ($request->input('role') === 'student') {
            $rules['major'] = 'required';
            $rules['study_program'] = 'required';
            $rules['campus'] = 'required';
        }

        // Validate only relevant fields
        $validator = Validator::make($request->only(array_keys($rules)), $rules, [
            'password.regex' => 'Password must include at least one letter and one number',
            'password_confirmation.same' => 'Password confirmation must match password'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'msgField' => $validator->errors()->messages(),
            ], 422);
        }

        try {
            // Create the user
            $user = UserModel::create([
                'identity_number' => $request->identity_number,
                'role' => $request->role,
                'password' => Hash::make($request->password),
                'exam_status' => 'not_yet'
            ]);

            // Create appropriate profile based on role
            if ($request->role === 'student') {
                // Include all required fields for StudentModel
                StudentModel::create([
                    'user_id' => $user->user_id,
                    'name' => $request->name,
                    'nim' => $request->identity_number, // Using identity_number as NIM for students
                    'major' => $request->major,
                    'study_program' => $request->study_program,
                    'campus' => $request->campus,
                    'batch' => date('Y'),   // Add current year as batch
                    'status' => 'active',   // Add default status
                    'phone_number' => $request->phone_number ?? null,
                    'email' => $request->email ?? null
                ]);
            } elseif ($request->role === 'lecturer') {
                LecturerModel::create([
                    'user_id' => $user->user_id,
                    'name' => $request->name,
                    'nidn' => $request->identity_number, // Using identity_number as NIDN for lecturers
                    'phone_number' => $request->phone_number ?? null,
                    'email' => $request->email ?? null
                ]);
            } elseif ($request->role === 'staff') {
                StaffModel::create([
                    'user_id' => $user->user_id,
                    'name' => $request->name,
                    'nip' => $request->identity_number, // Using identity_number as NIP for staff
                    'phone_number' => $request->phone_number ?? null,
                    'email' => $request->email ?? null
                ]);
            } elseif ($request->role === 'alumni') {
                AlumniModel::create([
                    'user_id' => $user->user_id,
                    'name' => $request->name,
                    'nik' => $request->identity_number, // Using identity_number as NIK for alumni
                    'phone_number' => $request->phone_number ?? null,
                    'email' => $request->email ?? null
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'User created successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Create user error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Failed to create user. Please try again.',
            ], 500);
        }
    }
}
