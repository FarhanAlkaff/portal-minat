<?php

use App\Http\Controllers\Admin\AuthController as AdminAuth;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MasterController;
use App\Http\Controllers\Admin\RegistrationController as AdminRegistration;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Route;

// Public
Route::get('/', [RegistrationController::class, 'landing'])->name('landing');
Route::get('/informasi', [RegistrationController::class, 'info'])->defaults('section', 'informasi')->name('info.pendaftaran');
Route::get('/persyaratan', [RegistrationController::class, 'info'])->defaults('section', 'persyaratan')->name('info.persyaratan');
Route::get('/alur-pendaftaran', [RegistrationController::class, 'info'])->defaults('section', 'alur-pendaftaran')->name('info.alur');
Route::get('/jadwal', [RegistrationController::class, 'info'])->defaults('section', 'jadwal')->name('info.jadwal');
Route::get('/faq', [RegistrationController::class, 'info'])->defaults('section', 'faq')->name('info.faq');

Route::get('/pendaftaran', [RegistrationController::class, 'create'])->name('registration.create');
Route::post('/pendaftaran', [RegistrationController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('registration.store');

Route::post('/xhr/validate-age', [RegistrationController::class, 'validateAge'])->name('api.validate-age');
Route::post('/xhr/check-duplicate', [RegistrationController::class, 'checkDuplicate'])->name('api.check-duplicate');

Route::match(['get', 'post'], '/pendaftaran/cek', [RegistrationController::class, 'check'])
    ->middleware('throttle:20,1')
    ->name('registration.check');

Route::get('/pendaftaran/hasil/{number}', [RegistrationController::class, 'result'])->name('registration.result');
Route::get('/pendaftaran/pdf/{number}', [RegistrationController::class, 'pdf'])->name('registration.pdf');
Route::get('/pendaftaran/surat-observasi/{number}', [RegistrationController::class, 'observationLetter'])->name('registration.observation-pdf');
Route::get('/verify/{token}', [RegistrationController::class, 'verify'])->name('verify');

// Admin Auth (Custom Login URL)
Route::middleware('guest')->group(function () {
    Route::get('/alazcaplg/login', [AdminAuth::class, 'showLogin'])->name('admin.login');
    Route::post('/alazcaplg/login', [AdminAuth::class, 'login'])->middleware('throttle:5,1')->name('admin.login.submit');
});

// Admin
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware(['auth', 'admin_timeout'])->group(function () {
        Route::post('/keep-alive', function () {
            session(['admin_last_activity' => now()->timestamp]);
            return response()->json(['status' => 'ok', 'time' => now()->timestamp]);
        })->name('keep-alive');
        Route::post('/logout', [AdminAuth::class, 'logout'])->name('logout');
        Route::get('/password', [AdminAuth::class, 'editPassword'])->name('password.edit');
        Route::put('/password', [AdminAuth::class, 'updatePassword'])->name('password.update');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('/dashboard/target', [DashboardController::class, 'saveTargets'])->name('dashboard.save-targets');

        Route::get('/registrations', [AdminRegistration::class, 'index'])->name('registrations.index');
        Route::get('/registrations/create', [AdminRegistration::class, 'create'])->name('registrations.create');
        Route::post('/registrations', [AdminRegistration::class, 'store'])->name('registrations.store');
        Route::get('/registrations/export', [AdminRegistration::class, 'export'])->name('registrations.export');
        Route::get('/registrations/import', [AdminRegistration::class, 'importForm'])->name('registrations.import');
        Route::post('/registrations/import', [AdminRegistration::class, 'importStore'])->name('registrations.import.store');
        Route::get('/registrations/import/template', [AdminRegistration::class, 'importTemplate'])->name('registrations.import.template');
        Route::get('/registrations/{registration}', [AdminRegistration::class, 'show'])->name('registrations.show');
        Route::post('/registrations/{registration}/validate', [AdminRegistration::class, 'validateAdmin'])->name('registrations.validate');
        Route::post('/registrations/{registration}/observation', [AdminRegistration::class, 'observation'])->name('registrations.observation');
        Route::post('/registrations/{registration}/observation-result', [AdminRegistration::class, 'observationResult'])->name('registrations.observation-result');
        Route::post('/registrations/{registration}/resend-wa', [AdminRegistration::class, 'resendWhatsapp'])->name('registrations.resend-wa');
        Route::delete('/registrations/{registration}', [AdminRegistration::class, 'destroy'])->name('registrations.destroy');

        // Super Admin Only
        Route::middleware('super_admin')->group(function () {
            // User / Admin Account Management (CRUD)
            Route::get('/users', [UserController::class, 'index'])->name('users.index');
            Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
            Route::post('/users', [UserController::class, 'store'])->name('users.store');
            Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
            Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
            Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

            // Master Data
            Route::get('/academic-years', [MasterController::class, 'years'])->name('years');
            Route::post('/academic-years', [MasterController::class, 'saveYear'])->name('years.save');
            Route::delete('/academic-years/{year}', [MasterController::class, 'destroyYear'])->name('years.destroy');

            Route::get('/grades', [MasterController::class, 'grades'])->name('grades');
            Route::post('/grades', [MasterController::class, 'saveGrade'])->name('grades.save');
            Route::delete('/grades/{grade}', [MasterController::class, 'destroyGrade'])->name('grades.destroy');
            Route::get('/grades/{grade}/age-rule', [MasterController::class, 'ageRule'])->name('grades.age-rule');
            Route::post('/grades/{grade}/age-rule', [MasterController::class, 'saveAgeRule'])->name('grades.age-rule.save');

            Route::get('/campuses', [MasterController::class, 'campuses'])->name('campuses');
            Route::post('/campuses', [MasterController::class, 'saveCampus'])->name('campuses.save');
            Route::delete('/campuses/{campus}', [MasterController::class, 'destroyCampus'])->name('campuses.destroy');

            Route::get('/categories', [MasterController::class, 'categories'])->name('categories');
            Route::post('/categories', [MasterController::class, 'saveCategory'])->name('categories.save');
            Route::delete('/categories/{category}', [MasterController::class, 'destroyCategory'])->name('categories.destroy');

            Route::get('/information', [MasterController::class, 'information'])->name('information');
            Route::post('/information', [MasterController::class, 'saveInformation'])->name('information.save');
            Route::post('/sliders', [MasterController::class, 'saveSlider'])->name('sliders.save');
            Route::delete('/sliders/{slider}', [MasterController::class, 'destroySlider'])->name('sliders.destroy');
        });
    });
});

Route::fallback(function () {
    return redirect()->route('landing');
});
