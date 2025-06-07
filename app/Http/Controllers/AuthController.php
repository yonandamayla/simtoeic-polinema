<?php

namespace App\Http\Controllers;

use App\Models\AdminModel;
use App\Models\AlumniModel;
use App\Models\LecturerModel;
use App\Models\StaffModel;
use App\Models\StudentModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\UserModel;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            $user = Auth::user();
            return $this->redirectBasedOnRole($user);
        }
        return view('pages.auth-login2', ['type_menu' => 'auth']);
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'identity_number' => 'required',
            'password' => 'required',
        ]);

        $remember = $request->boolean('remember');

        // Log remember token status for debugging
        Log::debug('Login attempt with remember token', [
            'remember' => $remember,
            'identity_number' => $credentials['identity_number']
        ]);

        // Coba login langsung dengan UserModel terlebih dahulu
        $user = UserModel::where('identity_number', $credentials['identity_number'])->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            Auth::login($user, $remember);

            // Log remember token after login
            Log::debug('User logged in', [
                'user_id' => $user->user_id,
                'remember' => $remember,
                'remember_token' => $user->remember_token
            ]);

            return $this->redirectBasedOnRole($user);
        }

        // Jika tidak berhasil, coba dengan model spesifik
        $identityColumns = [
            'admin' => 'nidn',
            'lecturer' => 'nidn',
            'student' => 'nim',
            'staff' => 'nip',
            'alumni' => 'nik',
        ];

        $models = [
            'admin' => AdminModel::class,
            'lecturer' => LecturerModel::class,
            'student' => StudentModel::class,
            'staff' => StaffModel::class,
            'alumni' => AlumniModel::class,
        ];

        // For debugging
        $attempts = [];

        foreach ($models as $role => $model) {
            $identityColumn = $identityColumns[$role];
            try {
                $foundUser = $model::where($identityColumn, $credentials['identity_number'])->first();

                $attempts[$role] = [
                    'column' => $identityColumn,
                    'value' => $credentials['identity_number'],
                    'found' => $foundUser ? true : false
                ];

                if ($foundUser) {
                    $userRecord = $foundUser->user;
                    $attempts[$role]['user_record_found'] = $userRecord ? true : false;

                    if ($userRecord && Hash::check($credentials['password'], $userRecord->password)) {
                        Auth::login($userRecord, $remember);

                        // Log remember token after login
                        Log::debug('User logged in via specific model', [
                            'user_id' => $userRecord->user_id,
                            'role' => $role,
                            'remember' => $remember,
                            'remember_token' => $userRecord->remember_token
                        ]);

                        return $this->redirectBasedOnRole($userRecord);
                    }
                }
            } catch (\Exception $e) {
                $attempts[$role] = ['error' => $e->getMessage()];
                continue;
            }
        }

        // Log debug info
        Log::debug('Login attempts:', $attempts);

        return back()->withErrors([
            'identity_number' => 'Invalid credentials. Please check your identity number and password.',
        ])->withInput($request->except('password'));
    }

    /**
     * Redirect user based on role
     */
    private function redirectBasedOnRole($user)
    {
        // Check if user has role property
        if (isset($user->role)) {
            $role = $user->role;
        } else {
            // Try to determine role from relationships
            if ($user->admin) {
                $role = 'admin';
            } elseif ($user->lecturer) {
                $role = 'lecturer';
            } elseif ($user->student) {
                $role = 'student';
            } elseif ($user->staff) {
                $role = 'staff';
            } elseif ($user->alumni) {
                $role = 'alumni';
            } else {
                $role = null;
            }
        }

        // Redirect based on role
        if ($role === 'admin') {
            return redirect('/dashboard-admin');
        } elseif ($role === 'lecturer') {
            return redirect()->route('lecturer.dashboard');
        } elseif ($role === 'student') {
            return redirect()->route('student.dashboard');
        } elseif ($role === 'staff') {
            return redirect()->route('staff.dashboard');
        } elseif ($role === 'alumni') {
            return redirect()->route('alumni.dashboard');
        }

        // Default redirect
        return redirect()->route('dashboard');
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('auth.login');
    }
}
