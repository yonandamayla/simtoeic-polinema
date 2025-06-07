<?php

use App\Http\Controllers\AlumniController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LecturerController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StaffController;
use App\Models\StudentModel;
use App\Models\StaffModel;
use App\Models\AlumniModel;
use App\Models\LecturerModel;
use App\Http\Controllers\AdminProfileController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\ExamResultController;
use App\Models\AnnouncementModel;
use App\Models\UserModel;
use App\Http\Controllers\UserDataTableController;
use App\Http\Controllers\FaqController;
use App\Models\FaqModel;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Layout
Route::get('/layout-default-layout', function () {
    return view('pages.layout-default-layout', ['type_menu' => 'layout']);
});

// Bootstrap
Route::get('/bootstrap-alert', function () {
    return view('pages.bootstrap-alert', ['type_menu' => 'bootstrap']);
});
Route::get('/bootstrap-badge', function () {
    return view('pages.bootstrap-badge', ['type_menu' => 'bootstrap']);
});
Route::get('/bootstrap-breadcrumb', function () {
    return view('pages.bootstrap-breadcrumb', ['type_menu' => 'bootstrap']);
});
Route::get('/bootstrap-buttons', function () {
    return view('pages.bootstrap-buttons', ['type_menu' => 'bootstrap']);
});
Route::get('/bootstrap-card', function () {
    return view('pages.bootstrap-card', ['type_menu' => 'bootstrap']);
});
Route::get('/bootstrap-carousel', function () {
    return view('pages.bootstrap-carousel', ['type_menu' => 'bootstrap']);
});
Route::get('/bootstrap-collapse', function () {
    return view('pages.bootstrap-collapse', ['type_menu' => 'bootstrap']);
});
Route::get('/bootstrap-dropdown', function () {
    return view('pages.bootstrap-dropdown', ['type_menu' => 'bootstrap']);
});
Route::get('/bootstrap-form', function () {
    return view('pages.bootstrap-form', ['type_menu' => 'bootstrap']);
});
Route::get('/bootstrap-list-group', function () {
    return view('pages.bootstrap-list-group', ['type_menu' => 'bootstrap']);
});
Route::get('/bootstrap-media-object', function () {
    return view('pages.bootstrap-media-object', ['type_menu' => 'bootstrap']);
});
Route::get('/bootstrap-modal', function () {
    return view('pages.bootstrap-modal', ['type_menu' => 'bootstrap']);
});
Route::get('/bootstrap-nav', function () {
    return view('pages.bootstrap-nav', ['type_menu' => 'bootstrap']);
});
Route::get('/bootstrap-navbar', function () {
    return view('pages.bootstrap-navbar', ['type_menu' => 'bootstrap']);
});
Route::get('/bootstrap-pagination', function () {
    return view('pages.bootstrap-pagination', ['type_menu' => 'bootstrap']);
});
Route::get('/bootstrap-popover', function () {
    return view('pages.bootstrap-popover', ['type_menu' => 'bootstrap']);
});
Route::get('/bootstrap-progress', function () {
    return view('pages.bootstrap-progress', ['type_menu' => 'bootstrap']);
});
Route::get('/bootstrap-table', function () {
    return view('pages.bootstrap-table', ['type_menu' => 'bootstrap']);
});
Route::get('/bootstrap-tooltip', function () {
    return view('pages.bootstrap-tooltip', ['type_menu' => 'bootstrap']);
});
Route::get('/bootstrap-typography', function () {
    return view('pages.bootstrap-typography', ['type_menu' => 'bootstrap']);
});

// components
Route::get('/components-article', function () {
    return view('pages.components-article', ['type_menu' => 'components']);
});
Route::get('/components-avatar', function () {
    return view('pages.components-avatar', ['type_menu' => 'components']);
});
Route::get('/components-chat-box', function () {
    return view('pages.components-chat-box', ['type_menu' => 'components']);
});
Route::get('/components-empty-state', function () {
    return view('pages.components-empty-state', ['type_menu' => 'components']);
});
Route::get('/components-gallery', function () {
    return view('pages.components-gallery', ['type_menu' => 'components']);
});
Route::get('/components-hero', function () {
    return view('pages.components-hero', ['type_menu' => 'components']);
});
Route::get('/components-multiple-upload', function () {
    return view('pages.components-multiple-upload', ['type_menu' => 'components']);
});
Route::get('/components-pricing', function () {
    return view('pages.components-pricing', ['type_menu' => 'components']);
});
Route::get('/components-statistic', function () {
    return view('pages.components-statistic', ['type_menu' => 'components']);
});
Route::get('/components-tab', function () {
    return view('pages.components-tab', ['type_menu' => 'components']);
});
Route::get('/components-table', function () {
    return view('pages.components-table', ['type_menu' => 'components']);
});
Route::get('/components-user', function () {
    return view('pages.components-user', ['type_menu' => 'components']);
});
Route::get('/components-wizard', function () {
    return view('pages.components-wizard', ['type_menu' => 'components']);
});

// forms
Route::get('/forms-advanced-form', function () {
    return view('pages.forms-advanced-form', ['type_menu' => 'forms']);
});
Route::get('/forms-editor', function () {
    return view('pages.forms-editor', ['type_menu' => 'forms']);
});
Route::get('/forms-validation', function () {
    return view('pages.forms-validation', ['type_menu' => 'forms']);
});

// modules
Route::get('/modules-chartjs', function () {
    return view('pages.modules-chartjs', ['type_menu' => 'modules']);
});
Route::get('/modules-datatables', function () {
    return view('pages.modules-datatables', ['type_menu' => 'modules']);
});
Route::get('/modules-ion-icons', function () {
    return view('pages.modules-ion-icons', ['type_menu' => 'modules']);
});
Route::get('/modules-owl-carousel', function () {
    return view('pages.modules-owl-carousel', ['type_menu' => 'modules']);
});
Route::get('/modules-sparkline', function () {
    return view('pages.modules-sparkline', ['type_menu' => 'modules']);
});
Route::get('/modules-sweet-alert', function () {
    return view('pages.modules-sweet-alert', ['type_menu' => 'modules']);
});
Route::get('/modules-toastr', function () {
    return view('pages.modules-toastr', ['type_menu' => 'modules']);
});

// auth
Route::get('/auth-login2', function () {
    return view('pages.auth-login2', ['type_menu' => 'auth']);
});
Route::get('/auth-register', function () {
    return view('pages.auth-register', ['type_menu' => 'auth']);
});
Route::get('/auth-reset-password', function () {
    return view('pages.auth-reset-password', ['type_menu' => 'auth']);
});

// error
Route::get('/error-403', function () {
    return view('pages.error-403', ['type_menu' => 'error']);
});
Route::get('/error-404', function () {
    return view('pages.error-404', ['type_menu' => 'error']);
});
Route::get('/error-500', function () {
    return view('pages.error-500', ['type_menu' => 'error']);
});
Route::get('/error-503', function () {
    return view('pages.error-503', ['type_menu' => 'error']);
});

// features
Route::get('/features-post-create', function () {
    return view('pages.features-post-create', ['type_menu' => 'features']);
});
Route::get('/features-post', function () {
    return view('pages.features-post', ['type_menu' => 'features']);
});
Route::get('/features-profile', function () {
    return view('pages.features-profile', ['type_menu' => 'features']);
});
Route::get('/features-settings', function () {
    return view('pages.features-settings', ['type_menu' => 'features']);
});
Route::get('/features-setting-detail', function () {
    return view('pages.features-setting-detail', ['type_menu' => 'features']);
});

// Landing Page Route
Route::get('/', function () {
    return view('pages.landing-page');
});

// Authentication Routes
Route::prefix('auth')->name('auth.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('process');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Dashboard routes for each role (protected with auth & prevent-back-history middleware)
Route::middleware(['auth', 'prevent-back-history'])->group(function () {
    Route::get('/dashboard-admin', function () {
        return view('pages.dashboard-admin', ['type_menu' => 'dashboard']);
    })->name('admin.dashboard');

    Route::get('/dashboard-lecturer', function () {
        return view('pages.dashboard-lecturer', ['type_menu' => 'dashboard']);
    })->name('lecturer.dashboard');

    Route::get('/dashboard-student', function () {
        return view('pages.dashboard-student', ['type_menu' => 'dashboard']);
    })->name('student.dashboard');

    Route::get('/dashboard-staff', function () {
        return view('pages.dashboard-staff', ['type_menu' => 'dashboard']);
    })->name('staff.dashboard');

    Route::get('/dashboard-alumni', function () {
        return view('pages.dashboard-alumni', ['type_menu' => 'dashboard']);
    })->name('alumni.dashboard');

    // Admin profile
    Route::get('/admin/profile', [AdminProfileController::class, 'show'])->name('admin.profile');
    Route::post('/admin/profile/update', [AdminProfileController::class, 'update'])->name('admin.profile.update');

    // Exam Results
    Route::get('/exam-results', [ExamResultController::class, 'index'])->name('exam-results.index');
    Route::get('/exam-results/data', [ExamResultController::class, 'getResults'])->name('exam-results.data');
    Route::post('/exam-results/import', [ExamResultController::class, 'import'])->name('exam-results.import.store');
    Route::delete('/exam-results/delete-all', [ExamResultController::class, 'deleteAll'])->name('exam-results.delete-all');
    Route::get('/exam-results/{id}', [ExamResultController::class, 'show'])->name('exam-results.show');
    Route::put('/exam-results/{id}', [ExamResultController::class, 'update'])->name('exam-results.update');
    Route::delete('/exam-results/{id}', [ExamResultController::class, 'destroy'])->name('exam-results.destroy');
});

// Admin Notices Announcements route
Route::group(['prefix' => 'announcements'], function () {
    Route::get('/', function () {
        return view('users-admin.announcement.index', [
            'type_menu' => 'announcements',
            'announcements' => AnnouncementModel::all()
        ]);
    })->name('announcements.index');
    Route::post('/list', [AnnouncementController::class, 'list']);
    Route::get('/create', [AnnouncementController::class, 'create']);
    Route::post('/store', [AnnouncementController::class, 'store']);
    Route::get('/{id}/show_ajax', [AnnouncementController::class, 'show_ajax']);
    Route::get('/{id}/edit', [AnnouncementController::class, 'edit']);
    Route::put('/{id}/update', [AnnouncementController::class, 'update']);
    Route::get('/{id}/delete_ajax', [AnnouncementController::class, 'confirm_ajax']);
    Route::post('/{id}/delete_ajax', [AnnouncementController::class, 'delete_ajax']);
    Route::post('/upload', [AnnouncementController::class, 'upload'])->name('announcements.upload');
});

// Admin Manage Users route
Route::get('/users', function () {
    $staffCount = StaffModel::count();
    $studentCount = StudentModel::count();
    $alumniCount = AlumniModel::count();
    $lecturerCount = LecturerModel::count();

    return view('users-admin.manage-user.index', [
        'type_menu' => 'users',
        'staffCount' => $staffCount,
        'studentCount' => $studentCount,
        'alumniCount' => $alumniCount,
        'lecturerCount' => $lecturerCount,
    ]);
})->name('users');

// Manage Users - Staff
Route::group(['prefix' => 'manage-users/staff', 'middleware' => ['auth', 'prevent-back-history']], function () {
    Route::get('/', function () {
        return view('users-admin.manage-user.staff.index', ['type_menu' => 'staff']);
    })->name('staff.index');
    Route::post('/list', [StaffController::class, 'list']);
    Route::get('/{id}/show_ajax', [StaffController::class, 'show_ajax']);
    Route::get('/{id}/edit_ajax', [StaffController::class, 'edit_ajax']);
    Route::put('/{id}/update_ajax', [StaffController::class, 'update_ajax']);
    Route::get('/{id}/delete_ajax', [StaffController::class, 'confirm_ajax']);
    Route::post('/{id}/delete_ajax', [StaffController::class, 'delete_ajax']);
});

// Manage Users - Student
Route::group(['prefix' => 'manage-users/student', 'middleware' => ['auth', 'prevent-back-history']], function () {
    Route::get('/', function () {
        return view('users-admin.manage-user.student.index', [
            'type_menu' => 'student',
            'students' => StudentModel::all()
        ]);
    })->name('student.index');
    Route::post('/list', [StudentController::class, 'list']);
    Route::get('/{id}/show_ajax', [StudentController::class, 'show_ajax']);
    Route::get('/{id}/edit_ajax', [StudentController::class, 'edit_ajax']);
    Route::put('/{id}/update_ajax', [StudentController::class, 'update_ajax']);
    Route::get('/{id}/delete_ajax', [StudentController::class, 'confirm_ajax']);
    Route::post('/{id}/delete_ajax', [StudentController::class, 'delete_ajax']);
});

// Manage Users - Alumni
Route::group(['prefix' => 'manage-users/alumni', 'middleware' => ['auth', 'prevent-back-history']], function () {
    Route::get('/', function () {
        return view('users-admin.manage-user.alumni.index', ['type_menu' => 'alumni']);
    })->name('alumni.index');
    Route::post('/list', [AlumniController::class, 'list']);
    Route::get('/{id}/show_ajax', [AlumniController::class, 'show_ajax']);
    Route::get('/{id}/edit_ajax', [AlumniController::class, 'edit_ajax']);
    Route::put('/{id}/update_ajax', [AlumniController::class, 'update_ajax']);
    Route::get('/{id}/delete_ajax', [AlumniController::class, 'confirm_ajax']);
    Route::post('/{id}/delete_ajax', [AlumniController::class, 'delete_ajax']);
});

// Manage Users - Lecturer
Route::group(['prefix' => 'manage-users/lecturer', 'middleware' => ['auth', 'prevent-back-history']], function () {
    Route::get('/', function () {
        return view('users-admin.manage-user.lecturer.index', ['type_menu' => 'lecturer']);
    })->name('lecturer.index');
    Route::post('/list', [LecturerController::class, 'list']);
    Route::get('/{id}/show_ajax', [LecturerController::class, 'show_ajax']);
    Route::get('/{id}/edit_ajax', [LecturerController::class, 'edit_ajax']);
    Route::put('/{id}/update_ajax', [LecturerController::class, 'update_ajax']);
    Route::get('/{id}/delete_ajax', [LecturerController::class, 'confirm_ajax']);
    Route::post('/{id}/delete_ajax', [LecturerController::class, 'delete_ajax']);
});

// Registration - Admin
Route::prefix('registration')->middleware(['auth', 'prevent-back-history'])->group(function () {
    Route::get('/', [App\Http\Controllers\UserDataTableController::class, 'index'])->name('registration.index');
    Route::get('/users-data', [App\Http\Controllers\UserDataTableController::class, 'getUsers'])->name('users.data');
    Route::get('/{id}/show_ajax', [App\Http\Controllers\UserDataTableController::class, 'show_ajax']);
    Route::get('/{id}/edit_ajax', [App\Http\Controllers\UserDataTableController::class, 'edit_ajax']);
    Route::get('/{id}/delete_ajax', [App\Http\Controllers\UserDataTableController::class, 'confirm_ajax']);
    Route::post('/{id}/update_ajax', [App\Http\Controllers\UserDataTableController::class, 'update_ajax']);
    Route::post('/{id}/delete_ajax', [App\Http\Controllers\UserDataTableController::class, 'delete_ajax']);
    Route::delete('/delete-all', [App\Http\Controllers\UserDataTableController::class, 'deleteAll'])->name('registration.delete-all');
});
Route::post('/registration', [App\Http\Controllers\UserDataTableController::class, 'store'])->name('registration.store')->middleware(['auth', 'prevent-back-history']);

// Student routes
Route::group(['prefix' => 'student', 'middleware' => ['auth', 'prevent-back-history']], function () {
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('student.dashboard');
    Route::get('/profile', [StudentController::class, 'profile'])->name('student.profile');
    Route::post('/profile/update', [StudentController::class, 'updateProfile'])->name('student.profile.update');
    Route::get('/registration', [StudentController::class, 'showRegistrationForm'])->name('student.registration.form');
    Route::post('/register-exam', [StudentController::class, 'registerExam'])->name('student.register.exam');
});

// Staff routes
Route::group(['prefix' => 'staff', 'middleware' => ['auth', 'prevent-back-history']], function () {
    Route::get('/profile', [StaffController::class, 'profile'])->name('staff.profile');
    Route::post('/profile/update', [StaffController::class, 'updateProfile'])->name('staff.profile.update');
    Route::get('/dashboard', [StaffController::class, 'dashboard'])->name('staff.dashboard');
    Route::get('/registration', [StaffController::class, 'showRegistrationForm'])->name('staff.registration.form');
});

// Alumni routes
Route::group(['prefix' => 'alumni', 'middleware' => ['auth', 'prevent-back-history']], function () {
    Route::get('/dashboard', [AlumniController::class, 'dashboard'])->name('alumni.dashboard');
    Route::get('/profile', [AlumniController::class, 'profile'])->name('alumni.profile');
    Route::post('/profile/update', [AlumniController::class, 'updateProfile'])->name('alumni.profile.update');
    Route::get('/registration', [AlumniController::class, 'showRegistrationForm'])->name('alumni.registration.form');
});

// Lecturer routes
Route::group(['prefix' => 'lecturer', 'middleware' => ['auth', 'prevent-back-history']], function () {
    Route::get('/dashboard', [LecturerController::class, 'dashboard'])->name('lecturer.dashboard');
    Route::get('/profile', [LecturerController::class, 'profile'])->name('lecturer.profile');
    Route::post('/profile/update', [LecturerController::class, 'updateProfile'])->name('lecturer.profile.update');
    Route::get('/registration', [LecturerController::class, 'showRegistrationForm'])->name('lecturer.registration.form');
});

// admin routes
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/profile', [AdminProfileController::class, 'show'])->name('admin.profile');
    Route::post('/admin/profile/update', [AdminProfileController::class, 'update'])->name('admin.profile.update');
});

// Student Exam Registration routes
Route::get('/student/registration', [StudentController::class, 'showRegistrationForm'])->name('student.registration.form');
Route::post('/student/register-exam', [StudentController::class, 'registerExam'])->name('student.register.exam');

// Admin FAQs route
Route::group(['prefix' => 'faqs', 'middleware' => ['auth', 'prevent-back-history']], function () {
    Route::get('/', function () {
        return view('users-admin.faq.index', [
            'type_menu' => 'faqs'
        ]);
    })->name('faqs.index');
    Route::post('/list', [FaqController::class, 'list']);
    Route::get('/create', [FaqController::class, 'create']);
    Route::post('/store', [FaqController::class, 'store']);
    Route::get('/{id}/show_ajax', [FaqController::class, 'show_ajax']);
    Route::get('/{id}/edit', [FaqController::class, 'edit']);
    Route::put('/{id}/update', [FaqController::class, 'update']);
    Route::get('/{id}/delete_ajax', [FaqController::class, 'confirm_ajax']);
    Route::post('/{id}/delete_ajax', [FaqController::class, 'delete_ajax']);
});

Route::get('/faq', [FaqController::class, 'publicFaqList'])->name('public.faqs');
