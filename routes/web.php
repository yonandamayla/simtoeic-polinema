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
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\ManageUsersController;

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
    Route::post('/login', [AuthController::class, 'login'])->name('process')->middleware('throttle:5,1'); // 5 attempts per minute
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Dashboard routes for each role (protected with auth & prevent-back-history middleware)
Route::middleware(['auth', 'prevent-back-history'])->group(function () {
    Route::get('/dashboard-admin', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    // Backward compatibility redirects for old dashboard URLs
    Route::get('/dashboard-student', function () {
        return redirect()->route('student.dashboard');
    });
    Route::get('/dashboard-staff', function () {
        return redirect()->route('staff.dashboard');
    });
    Route::get('/dashboard-alumni', function () {
        return redirect()->route('alumni.dashboard');
    });
    Route::get('/dashboard-lecturer', function () {
        return redirect()->route('lecturer.dashboard');
    });

    // Admin profile
    Route::get('/admin/profile', [AdminProfileController::class, 'show'])->name('admin.profile');
    Route::post('/admin/profile/update', [AdminProfileController::class, 'update'])->name('admin.profile.update');

    // Exam Results
    Route::get('/exam-results', [ExamResultController::class, 'index'])->name('exam-results.index');
    Route::get('/exam-results/data', [ExamResultController::class, 'getResults'])->name('exam-results.data');
    Route::post('/exam-results/import', [ExamResultController::class, 'import'])->name('exam-results.import.store');
    Route::get('/exam-results/{id}', [ExamResultController::class, 'show'])->name('exam-results.show');
    Route::put('/exam-results/{id}', [ExamResultController::class, 'update'])->name('exam-results.update');
    Route::delete('/exam-results/{id}', [ExamResultController::class, 'destroy'])->name('exam-results.destroy');
});

// Admin Notices Announcements route
Route::group(['prefix' => 'announcements', 'middleware' => ['auth', 'prevent-back-history']], function () {
    Route::get('/', function () {
        return view('users-admin.announcement.index', [
            'type_menu' => 'announcements',
            'announcements' => AnnouncementModel::all()
        ]);
    })->name('announcements.index');
    Route::post('/list', [AnnouncementController::class, 'list']);
    Route::get('/create', [AnnouncementController::class, 'create'])->name('announcements.create');
    Route::post('/store', [AnnouncementController::class, 'store'])->name('announcements.store');
    Route::get('/{id}/show_ajax', [AnnouncementController::class, 'show_ajax']);
    Route::get('/{id}/edit', [AnnouncementController::class, 'edit'])->name('announcements.edit');
    Route::get('/{id}/edit_dashboard', [AnnouncementController::class, 'edit_dashboard'])->name('announcements.edit_dashboard');
    Route::put('/{id}/update', [AnnouncementController::class, 'update'])->name('announcements.update');
    Route::get('/{id}/delete_ajax', [AnnouncementController::class, 'confirm_ajax']);
    Route::delete('/{id}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');
    Route::post('/upload', [AnnouncementController::class, 'upload'])->name('announcements.upload');
});

// Admin Manage Users route
Route::group(['prefix' => 'users', 'middleware' => ['auth', 'prevent-back-history']], function () {
    Route::get('/', function () {
        return view('users-admin.manage-user.index', ['type_menu' => 'users']);
    })->name('users.index');
    Route::post('/list', [ManageUsersController::class, 'list']);
    Route::get('/{id}/show_ajax', [ManageUsersController::class, 'show_ajax']);
    Route::get('/{id}/edit_ajax', [ManageUsersController::class, 'edit_ajax']);
    Route::put('/{id}/update_ajax', [ManageUsersController::class, 'update_ajax']);
    Route::get('/{id}/delete_ajax', [ManageUsersController::class, 'confirm_ajax']);
    Route::delete('/{id}/delete_ajax', [ManageUsersController::class, 'delete_ajax']);
    Route::post('/store', [ManageUsersController::class, 'store'])->name('users.store');
});

// Admin Verification Requests route
Route::group(['prefix' => 'admin/verification-requests', 'middleware' => ['auth', 'prevent-back-history']], function () {
    Route::get('/', [\App\Http\Controllers\VerificationRequestController::class, 'index'])->name('admin.verification.requests.index');
    Route::get('/data', [\App\Http\Controllers\VerificationRequestController::class, 'getData'])->name('admin.verification.requests.data');
    Route::get('/{id}', [\App\Http\Controllers\VerificationRequestController::class, 'show'])->name('admin.verification.requests.show');
    Route::post('/{id}/approve', [\App\Http\Controllers\VerificationRequestController::class, 'approve'])->name('admin.verification.requests.approve');
    Route::post('/{id}/reject', [\App\Http\Controllers\VerificationRequestController::class, 'reject'])->name('admin.verification.requests.reject');
    Route::get('/{id}/download', [\App\Http\Controllers\VerificationRequestController::class, 'downloadCertificate'])->name('admin.verification.requests.download');
});

// Student routes
Route::group(['prefix' => 'student', 'middleware' => ['auth', 'prevent-back-history']], function () {
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('student.dashboard');
    Route::get('/profile', [StudentController::class, 'profile'])->name('student.profile');
    Route::post('/profile/update', [StudentController::class, 'updateProfile'])->name('student.profile.update');
    Route::get('/registration', [StudentController::class, 'showRegistrationForm'])->name('student.registration.form');
    Route::post('/register-exam', [StudentController::class, 'registerExam'])->name('student.register.exam');
    Route::post('/certificate/update/{status}', [StudentController::class, 'updateCertificateStatus'])->name('student.certificate.update');

    // Request routes
    Route::get('/request', [StudentController::class, 'requestIndex'])->name('student.request.index');
    Route::get('/request/detail/{id}', [StudentController::class, 'getRequestDetail'])->name('student.request.detail');
    Route::get('/verification-request', [StudentController::class, 'showVerificationRequestForm'])->name('student.verification.request.form');
    Route::post('/verification-request', [StudentController::class, 'submitVerificationRequest'])->name('student.verification.request.submit');
});

// Staff routes
Route::group(['prefix' => 'staff', 'middleware' => ['auth', 'prevent-back-history']], function () {
    Route::get('/dashboard', [StaffController::class, 'dashboard'])->name('staff.dashboard');
    Route::get('/profile', [StaffController::class, 'profile'])->name('staff.profile');
    Route::post('/profile/update', [StaffController::class, 'updateProfile'])->name('staff.profile.update');
    Route::get('/registration', [StaffController::class, 'showRegistrationForm'])->name('staff.registration.form');
       Route::post('/certificate/update/{status}', [StaffController::class, 'updateCertificateStatus'])->name('staff.certificate.update');
});

// Alumni routes
Route::group(['prefix' => 'alumni', 'middleware' => ['auth', 'prevent-back-history']], function () {
    Route::get('/dashboard', [AlumniController::class, 'dashboard'])->name('alumni.dashboard');
    Route::get('/profile', [AlumniController::class, 'profile'])->name('alumni.profile');
    Route::post('/profile/update', [AlumniController::class, 'updateProfile'])->name('alumni.profile.update');
    Route::get('/registration', [AlumniController::class, 'showRegistrationForm'])->name('alumni.registration.form');
       Route::post('/certificate/update/{status}', [AlumniController::class, 'updateCertificateStatus'])->name('alumni.certificate.update');
});

// Lecturer routes
Route::group(['prefix' => 'lecturer', 'middleware' => ['auth', 'prevent-back-history']], function () {
    Route::get('/dashboard', [LecturerController::class, 'dashboard'])->name('lecturer.dashboard');
    Route::get('/profile', [LecturerController::class, 'profile'])->name('lecturer.profile');
    Route::post('/profile/update', [LecturerController::class, 'updateProfile'])->name('lecturer.profile.update');
    Route::get('/registration', [LecturerController::class, 'showRegistrationForm'])->name('lecturer.registration.form');
       Route::post('/certificate/update/{status}', [LecturerController::class, 'updateCertificateStatus'])->name('lecturer.certificate.update');
});

// admin routes
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/profile', [AdminProfileController::class, 'show'])->name('admin.profile');
    Route::post('/admin/profile/update', [AdminProfileController::class, 'update'])->name('admin.profile.update');
});

// Admin Verification Requests route
Route::group(['prefix' => 'admin/verification-requests', 'middleware' => ['auth', 'prevent-back-history']], function () {
    Route::get('/', [App\Http\Controllers\VerificationRequestController::class, 'index'])->name('admin.verification.requests.index');
    Route::get('/data', [App\Http\Controllers\VerificationRequestController::class, 'getData'])->name('admin.verification.requests.data');
    Route::get('/{id}', [App\Http\Controllers\VerificationRequestController::class, 'show'])->name('admin.verification.requests.show');
    Route::post('/{id}/approve', [App\Http\Controllers\VerificationRequestController::class, 'approve'])->name('admin.verification.requests.approve');
    Route::post('/{id}/reject', [App\Http\Controllers\VerificationRequestController::class, 'reject'])->name('admin.verification.requests.reject');
    Route::get('/{id}/download', [App\Http\Controllers\VerificationRequestController::class, 'downloadCertificate'])->name('admin.verification.download');
});



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

// Admin Telegram Settings route
Route::group(['prefix' => 'telegram', 'middleware' => ['auth', 'prevent-back-history']], function () {
    Route::get('/', [App\Http\Controllers\TelegramController::class, 'index'])->name('telegram.index');
    Route::post('/test', [App\Http\Controllers\TelegramController::class, 'testConnection'])->name('telegram.test');
    Route::post('/update-token', [App\Http\Controllers\TelegramController::class, 'updateToken'])->name('telegram.update-token');
    Route::post('/send-test', [App\Http\Controllers\TelegramController::class, 'sendTestMessage'])->name('telegram.send-test');
});
