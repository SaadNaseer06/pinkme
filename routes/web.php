<?php

use App\Http\Controllers\AdminCaseManagerController;
use App\Http\Controllers\EnrollProgramController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ReviewersController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SiteSettingController;
use App\Http\Controllers\SponsorController;
use App\Http\Controllers\SponsorshipProgramController;
use App\Http\Controllers\AdminSponsorController;
use App\Http\Controllers\AdminProgramRegistrationController;
use App\Http\Controllers\ChatMessageController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\SocialLoginController;
use App\Http\Controllers\CaseManagerController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProgramRegistrationController;
use App\Http\Controllers\WebinarController;
use App\Models\Application;
use App\Models\Program;

Route::get('/', function () {
    if (Auth::check()) {
        $roleName = Auth::user()->role->name ?? null;

        switch ($roleName) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'sponsor':
                return redirect()->route('sponsor.dashboard');
            case 'casemanager':
                return redirect()->route('case_manager.dashboard');
            case 'patient':
                return redirect()->route('patient.dashboard');
            default:
                return redirect()->route('register', ['tab' => 'login']);
        }
    }

    return view('auth.signup_login', [
        'initialTab' => 'login',
        'rememberedLogin' => request()->cookie('remembered_login'),
        'rememberedPassword' => request()->cookie('remembered_password'),
    ]);
});

Route::get('/privacy-policy', [PageController::class, 'privacy'])->name('policy.privacy');
Route::get('/terms-and-conditions', [PageController::class, 'terms'])->name('policy.terms');

Route::get('/login', [LoginController::class, 'showLoginForm'])
    ->middleware('redirect.by.role')
    ->name('login');

Route::middleware('guest')->group(function () {
    Route::get('/password/forgot', [ForgotPasswordController::class, 'showLinkRequestForm'])
        ->name('password.request');
    Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])
        ->name('password.email');
    Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])
        ->name('password.reset');
    Route::post('/password/reset', [ResetPasswordController::class, 'reset'])
        ->name('password.update');
});

Route::get('/auth/google/redirect', [SocialLoginController::class, 'redirectToGoogle'])
    ->middleware('guest')
    ->name('patient.google.redirect');

Route::get('/auth/google/callback', [SocialLoginController::class, 'handleGoogleCallback'])
    ->middleware('guest')
    ->name('patient.google.callback');

Route::get('/register', [RegisterController::class, 'show'])
    ->middleware('redirect.by.role')
    ->name('register');

Route::post('/register', [RegisterController::class, 'register']);


Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read_all');

    Route::get('/chat/conversations/{contact}', [ChatMessageController::class, 'index'])->name('chat.messages.index');
    Route::post('/chat/conversations/{contact}', [ChatMessageController::class, 'store'])->name('chat.messages.store');
});

Route::prefix('admin')->middleware(['role.restrict'])->group(function () {
    // Admin Routes
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/applications', [AdminController::class, 'applications'])->name('admin.applications');
    Route::get('/application/{id}', [AdminController::class, 'viewApplication'])->name('admin.viewApplication');
    Route::post('/applications/{id}/assign-reviewer', [AdminController::class, 'assignReviewer'])->name('admin.assignReviewer');
    Route::get('/applications/{id}/assigned-reviewer', [ReviewersController::class, 'getAssignedReviewer'])->name('admin.getAssignedReviewer');
    Route::get('reviewer/{id}', [AdminController::class, 'show'])->name('admin.reviewers.show');
    Route::get('/reviewers/{id}/applications', [ReviewersController::class, 'getApplications']);
    Route::post('/reviewers/{id}/remove', [ReviewersController::class, 'removeReviewer']);
    Route::get('/reviewers/{id}/edit', [AdminController::class, 'editReviewer'])->name('admin.reviewers.edit');
    Route::put('/reviewers/{id}', [AdminController::class, 'updateReviewer'])->name('admin.reviewers.update');
    Route::get('/reviewers/unassigned-applications', [AdminController::class, 'getUnassignedApplications']);

    // Unified Registrations (combines Program and Event registrations)
    Route::get('/registrations', [AdminController::class, 'registrations'])->name('admin.registrations.index');

    // Legacy routes (kept for backward compatibility)
    Route::get('/program-registration-requests', [AdminProgramRegistrationController::class, 'index'])->name('admin.program_registrations.index');
    Route::get('/program-registration-requests/{registration}', [AdminProgramRegistrationController::class, 'show'])->name('admin.program_registrations.show');
    Route::post('/program-registration-requests/{registration}/approve', [AdminProgramRegistrationController::class, 'approve'])->name('admin.program_registrations.approve');
    Route::post('/program-registration-requests/{registration}/reject', [AdminProgramRegistrationController::class, 'reject'])->name('admin.program_registrations.reject');
    Route::post('/program-registration-requests/{registration}/assign', [AdminProgramRegistrationController::class, 'assignCaseManager'])->name('admin.program_registrations.assign');

    // Sponsors' funding programs (table: sponsorship_programs)
    Route::get('sponsorship-programs/create', [SponsorshipProgramController::class, 'create'])->name('sp.create');
    Route::post('sponsorship-programs',        [SponsorshipProgramController::class, 'store'])->name('sp.store');

    // Enrollable programs/workshops (tables: programs, program_registrations)
    Route::resource('programs', ProgramController::class);

    // Case Managers (admin management of casemanager users)
    Route::resource('case-managers', AdminCaseManagerController::class)
        ->names('admin.case-managers');

    // Events (table: events) + attach funding programs (pivot: event_sponsorships)
    Route::resource('events', EventController::class);
    // Webinars (table: webinars) + registrations (table: webinar_registrations)
    Route::resource('webinars', WebinarController::class)->names('admin.webinars');

    // Event registration management
    Route::get('events-registrations', [EventController::class, 'registrations'])->name('events.registrations.index');
    Route::post('events-registrations/{registration}/approve', [EventController::class, 'approveRegistration'])->name('events.registrations.approve');
    Route::post('events-registrations/{registration}/reject', [EventController::class, 'rejectRegistration'])->name('events.registrations.reject');
    Route::post('events/{event}/sponsorships',                 [EventController::class, 'storeSponsorship'])->name('events.sponsorships.store');
    Route::delete('events/{event}/sponsorships/{sponsorship}', [EventController::class, 'destroySponsorship'])->name('events.sponsorships.destroy');
    // Reviewers routes
    Route::resource('reviewers', ReviewersController::class);
    // Additional reviewer routes
    Route::post('reviewers/{reviewer}/assign-applications', [ReviewersController::class, 'assignApplications'])
        ->name('reviewers.assign-applications');
    Route::get('available-reviewers', [ReviewersController::class, 'getAvailableReviewers'])
        ->name('reviewers.available');
    Route::get('reviewers/export', [ReviewersController::class, 'export'])
        ->name('reviewers.export');
    Route::delete('/applications/{id}', [AdminController::class, 'deleteApplication'])->name('admin.applications.delete');
    Route::get('/assigned', [AdminController::class, 'assigned'])->name('admin.assigned');
    Route::get('/reviewers', [AdminController::class, 'reviewers'])->name('admin.reviewers');
    Route::get('/patients', [AdminController::class, 'patients'])->name('admin.patients');
    Route::get('/patients/{patient}', [AdminController::class, 'showPatient'])->name('admin.patients.show');
    Route::get('/patients/{patient}/edit', [AdminController::class, 'editPatient'])->name('admin.patients.edit');
    Route::put('/patients/{patient}', [AdminController::class, 'updatePatient'])->name('admin.patients.update');
    Route::get('/patients/{patient}/applications', [AdminController::class, 'patientApplications'])->name('admin.patients.applications');
    Route::get('/programs-events', [AdminController::class, 'programsAndEvents'])->name('admin.programs-events');
    Route::get('/sponsors', [AdminController::class, 'sponsors'])->name('admin.sponsors');
    Route::get('/sponsors/create', [AdminSponsorController::class, 'create'])->name('admin.sponsors.create');
    Route::post('/sponsors', [AdminSponsorController::class, 'store'])->name('admin.sponsors.store');
    Route::get('/sponsors/{sponsor}', [AdminSponsorController::class, 'show'])->name('admin.sponsors.show');
    Route::get('/sponsors/{sponsor}/edit', [AdminSponsorController::class, 'edit'])->name('admin.sponsors.edit');
    Route::put('/sponsors/{sponsor}', [AdminSponsorController::class, 'update'])->name('admin.sponsors.update');
    Route::delete('/sponsors/{sponsor}', [AdminSponsorController::class, 'destroy'])->name('admin.sponsors.destroy');
    Route::get('/settings', [AdminController::class, 'settings'])->name('admin.settings');
    Route::put('/settings/{tab}', [SiteSettingController::class, 'update'])
        ->whereIn('tab', ['general', 'privacy', 'terms'])
        ->name('admin.settings.update');
    Route::post('/settings/upload', [SiteSettingController::class, 'upload'])->name('admin.settings.upload');
    Route::get('/admin/applications', [AdminController::class, 'applicationsIndex'])
        ->name('admin.applications.index');
    Route::get('/admin/applications/list', [AdminController::class, 'applicationsList'])
        ->name('admin.applications.list'); // AJAX endpoint
    Route::get('/admin/applications/export', [AdminController::class, 'applicationsExport'])
        ->name('admin.applications.export');
});


// API routes for AJAX calls
Route::prefix('api')->name('api.')->group(function () {
    Route::get('reviewers/search', [ReviewersController::class, 'getAvailableReviewers'])
        ->name('reviewers.search');

    Route::delete('reviewers/{reviewer}', [ReviewersController::class, 'destroy'])
        ->name('reviewers.destroy');
});


Route::prefix('sponsor')->middleware(['role.restrict'])->group(function () {
    // Sponsor Dashboard Routes
    Route::get('/dashboard', [SponsorController::class, 'dashboard'])->name('sponsor.dashboard');
    Route::get('/events', [SponsorController::class, 'events'])->name('sponsor.events');
    Route::get('/sponsorships', [SponsorController::class, 'sponsorships'])->name('sponsor.sponsorships');
    Route::get('/become-a-sponsor', [SponsorController::class, 'becomeASponsor'])->name('sponsor.becomeASponsor');
    Route::post('/sponsorships', [SponsorController::class, 'storeSponsorship'])->name('sponsor.sponsorships.store');

    // Event Registration Routes
    Route::get('/events/{event}', [SponsorController::class, 'showEvent'])->name('sponsor.events.show');
    Route::post('/events/{event}/register', [SponsorController::class, 'registerForEvent'])->name('sponsor.events.register');
    Route::delete('/events/{event}/cancel', [SponsorController::class, 'cancelEventRegistration'])->name('sponsor.events.cancel');
    Route::get('/my-event-registrations', [SponsorController::class, 'myEventRegistrations'])->name('sponsor.events.my-registrations');

    // Webinars
    Route::get('/webinars', [SponsorController::class, 'webinars'])->name('sponsor.webinars');
    Route::post('/webinars/{webinar}/register', [SponsorController::class, 'joinWebinar'])->name('sponsor.webinars.register');
    Route::delete('/webinars/{webinar}/cancel', [SponsorController::class, 'cancelWebinar'])->name('sponsor.webinars.cancel');

    Route::get('/reviews', [SponsorController::class, 'reviews'])->name('sponsor.reviews');
    Route::post('/reviews', [SponsorController::class, 'storeReview'])->name('sponsor.reviews.store');
    Route::get('/payment', [SponsorController::class, 'payment'])->name('sponsor.payment');
    Route::get('/setting', [SponsorController::class, 'setting'])->name('sponsor.setting');
    Route::put('/settings', [SponsorController::class, 'updateSettings'])->name('sponsor.settings.update');
    Route::put('/settings/password', [SponsorController::class, 'updatePassword'])->name('sponsor.settings.password');
    Route::put('/settings/notifications', [SponsorController::class, 'updateNotifications'])->name('sponsor.settings.notifications');
    Route::put('/settings/account', [SponsorController::class, 'updateAccount'])->name('sponsor.settings.account');
    Route::put('/settings/social', [SponsorController::class, 'updateSocial'])->name('sponsor.settings.social');
});


Route::prefix('case_manager')->middleware(['role.restrict'])->group(function () {
    // Case Manager Dashboard Routes
    Route::get('/dashboard', [CaseManagerController::class, 'dashboard'])->name('case_manager.dashboard');
    Route::get('/my-application', [CaseManagerController::class, 'myApplication'])->name('case_manager.myApplication');
    Route::get('/program-registrations', [CaseManagerController::class, 'programRegistrations'])->name('case_manager.program_registrations.index');
    Route::get('/program-registrations/{registration}', [CaseManagerController::class, 'showProgramRegistration'])->name('case_manager.program_registrations.show');
    Route::post('/program-registrations/{registration}/approve', [CaseManagerController::class, 'approveProgramRegistration'])->name('case_manager.program_registrations.approve');
    Route::post('/program-registrations/{registration}/reject', [CaseManagerController::class, 'rejectProgramRegistration'])->name('case_manager.program_registrations.reject');
    Route::get('/view-application/{id}', [CaseManagerController::class, 'viewAssignedApplication'])->name('case_manager.viewAssignedApplication');
    Route::post('/applications/{application}/approve', [CaseManagerController::class, 'approve'])->name('case_manager.applications.approve');
    Route::post('/applications/{application}/reject', [CaseManagerController::class, 'reject'])->name('case_manager.applications.reject');
    Route::post('/applications/{application}/request-missing', [CaseManagerController::class, 'requestMissing'])->name('case_manager.applications.request_missing');
    Route::get('/patient-profiles', [CaseManagerController::class, 'patientProfiles'])->name('case_manager.patientProfiles');
    Route::get('/patient-chats', [CaseManagerController::class, 'patientChats'])->name('case_manager.patientChats');
    Route::get('/setting', [CaseManagerController::class, 'setting'])->name('case_manager.setting');
    Route::put('/setting', [CaseManagerController::class, 'update'])->name('case_manager.settings.update');
    Route::put('/setting/password', [CaseManagerController::class, 'updatePassword'])->name('case_manager.setting.password');
    Route::put('/setting/notifications', [CaseManagerController::class, 'updateNotifications'])->name('case_manager.setting.notifications');
    Route::put('/setting/account', [CaseManagerController::class, 'updateAccount'])->name('case_manager.setting.account');
    Route::put('/setting/social', [CaseManagerController::class, 'updateSocial'])->name('case_manager.setting.social');
});


Route::prefix('patient')->middleware(['role.restrict'])->group(function () {
    Route::get('/programs/{id}', [ProgramController::class, 'show'])->name('patient.programs.show');
    Route::get('/my-applications', [PatientController::class, 'myApplications'])->name('patient.applications');
    Route::get('/dashboard', [PatientController::class, 'dashboard'])->name('patient.dashboard');
    Route::get('/my-application', [PatientController::class, 'myApplications'])->name('patient.myApplication');
    Route::get('/programs-and-aids', [PatientController::class, 'programsAndAids'])->name('patient.programsAndAids');
    Route::get('/patient-chats', [PatientController::class, 'patientChats'])->name('patient.patientChats');
    Route::get('/faq', [PatientController::class, 'faq'])->name('patient.faq');
    Route::get('/invoices', [PatientController::class, 'invoices'])->name('patient.invoices');
    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('/invoices/{invoice}/download', [InvoiceController::class, 'download'])->name('invoices.download');
    Route::get('/setting', [SettingsController::class, 'edit'])->name('patient.setting');
    Route::put('/', [SettingsController::class, 'update'])->name('patient.settings.update');
    Route::put('/password', [SettingsController::class, 'updatePassword'])->name('patient.settings.password');
    Route::put('/notifications', [SettingsController::class, 'updateNotifications'])->name('patient.settings.notifications');
    Route::put('/account', [SettingsController::class, 'updateAccount'])->name('patient.settings.account');
    Route::put('/social', [SettingsController::class, 'updateSocial'])->name('patient.settings.social');
    Route::get('/profile', [PatientController::class, 'profile'])->name('patient.profile');
    Route::get('/view-application/{id}', [ApplicationController::class, 'viewApplication'])->name('patient.viewApplication');
    Route::get('/application/{id}/edit', [ApplicationController::class, 'edit'])->name('patient.editApplication');
    Route::put('/application/{id}', [ApplicationController::class, 'update'])->name('patient.updateApplication');
    Route::get('/create-application', [ApplicationController::class, 'createApplication'])->name('patient.createApplication');
    Route::post('/store-application', [ApplicationController::class, 'storeApplication'])->name('patient.storeApplication');
    Route::post('/program/register', [ProgramRegistrationController::class, 'store'])->name('program.register');
    Route::get('/program-registrations/{registration}', [ProgramRegistrationController::class, 'show'])->name('patient.programRegistrations.show');

    // Webinars
    Route::get('/webinars', [PatientController::class, 'webinars'])->name('patient.webinars');
    Route::post('/webinars/{webinar}/register', [PatientController::class, 'joinWebinar'])->name('patient.webinars.register');
    Route::delete('/webinars/{webinar}/cancel', [PatientController::class, 'cancelWebinar'])->name('patient.webinars.cancel');
});
