<?php

Route::view('/', 'welcome');
Route::get('userVerification/{token}', [App\Http\Controllers\UserVerificationController::class, 'approve'])->name('userVerification');
// Add auth routes here
Auth::routes();

// Authentication Routes
Route::get('login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::post('logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

Route::get('register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);

Route::get('password/reset', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');

Route::get('password/confirm', [App\Http\Controllers\Auth\ConfirmPasswordController::class, 'showConfirmForm'])->name('password.confirm');
Route::post('password/confirm', [App\Http\Controllers\Auth\ConfirmPasswordController::class, 'confirm']);

Route::get('email/verify', [App\Http\Controllers\Auth\VerificationController::class, 'show'])->name('verification.notice');
Route::get('email/verify/{id}/{hash}', [App\Http\Controllers\Auth\VerificationController::class, 'verify'])->name('verification.verify');
Route::post('email/resend', [App\Http\Controllers\Auth\VerificationController::class, 'resend'])->name('verification.resend');

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['auth', '2fa', 'admin']], function () {
    Route::get('/', [App\Http\Controllers\Admin\HomeController::class, 'index'])->name('home');
    // Permissions
    Route::delete('permissions/destroy', [App\Http\Controllers\Admin\PermissionsController::class, 'massDestroy'])->name('permissions.massDestroy');
    Route::resource('permissions', App\Http\Controllers\Admin\PermissionsController::class);

    // Roles
    Route::delete('roles/destroy', [App\Http\Controllers\Admin\RolesController::class, 'massDestroy'])->name('roles.massDestroy');
    Route::resource('roles', App\Http\Controllers\Admin\RolesController::class);

    // Users
    Route::delete('users/destroy', [App\Http\Controllers\Admin\UsersController::class, 'massDestroy'])->name('users.massDestroy');
    Route::post('users/parse-csv-import', [App\Http\Controllers\Admin\UsersController::class, 'parseCsvImport'])->name('users.parseCsvImport');
    Route::post('users/process-csv-import', [App\Http\Controllers\Admin\UsersController::class, 'processCsvImport'])->name('users.processCsvImport');
    Route::resource('users', App\Http\Controllers\Admin\UsersController::class);

    // Audit Logs
    Route::resource('audit-logs', App\Http\Controllers\Admin\AuditLogsController::class, ['except' => ['create', 'store', 'edit', 'update', 'destroy']]);

    Route::get('global-search', [App\Http\Controllers\Admin\GlobalSearchController::class, 'search'])->name('globalSearch');
});

Route::group(['prefix' => 'profile', 'as' => 'profile.', 'namespace' => 'Auth', 'middleware' => ['auth', '2fa']], function () {
    // Change password
    if (file_exists(app_path('Http/Controllers/Auth/ChangePasswordController.php'))) {
        Route::get('password', [App\Http\Controllers\Auth\ChangePasswordController::class, 'edit'])->name('password.edit');
        Route::post('password', [App\Http\Controllers\Auth\ChangePasswordController::class, 'update'])->name('password.update');
        Route::post('profile', [App\Http\Controllers\Auth\ChangePasswordController::class, 'updateProfile'])->name('password.updateProfile');
        Route::post('profile/destroy', [App\Http\Controllers\Auth\ChangePasswordController::class, 'destroy'])->name('password.destroyProfile');
        Route::post('profile/two-factor', [App\Http\Controllers\Auth\ChangePasswordController::class, 'toggleTwoFactor'])->name('password.toggleTwoFactor');
    }
});

Route::group(['as' => 'frontend.', 'namespace' => 'Frontend', 'middleware' => ['auth', '2fa']], function () {
    Route::get('/home', [App\Http\Controllers\Frontend\HomeController::class, 'index'])->name('home');

    // Permissions
    Route::delete('permissions/destroy', [App\Http\Controllers\Frontend\PermissionsController::class, 'massDestroy'])->name('permissions.massDestroy');
    Route::resource('permissions', App\Http\Controllers\Frontend\PermissionsController::class);

    // Roles
    Route::delete('roles/destroy', [App\Http\Controllers\Frontend\RolesController::class, 'massDestroy'])->name('roles.massDestroy');
    Route::resource('roles', App\Http\Controllers\Frontend\RolesController::class);

    // Users
    Route::delete('users/destroy', [App\Http\Controllers\Frontend\UsersController::class, 'massDestroy'])->name('users.massDestroy');
    Route::resource('users', App\Http\Controllers\Frontend\UsersController::class);

    Route::get('frontend/profile', [App\Http\Controllers\Frontend\ProfileController::class, 'index'])->name('profile.index');
    Route::post('frontend/profile', [App\Http\Controllers\Frontend\ProfileController::class, 'update'])->name('profile.update');
    Route::post('frontend/profile/destroy', [App\Http\Controllers\Frontend\ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('frontend/profile/password', [App\Http\Controllers\Frontend\ProfileController::class, 'password'])->name('profile.password');
    Route::post('profile/toggle-two-factor', [App\Http\Controllers\Frontend\ProfileController::class, 'toggleTwoFactor'])->name('profile.toggle-two-factor');
});

Route::group(['namespace' => 'Auth', 'middleware' => ['auth', '2fa']], function () {
    // Two Factor Authentication
    if (file_exists(app_path('Http/Controllers/Auth/TwoFactorController.php'))) {
        Route::get('two-factor', [App\Http\Controllers\Auth\TwoFactorController::class, 'show'])->name('twoFactor.show');
        Route::post('two-factor', [App\Http\Controllers\Auth\TwoFactorController::class, 'check'])->name('twoFactor.check');
        Route::get('two-factor/resend', [App\Http\Controllers\Auth\TwoFactorController::class, 'resend'])->name('twoFactor.resend');
    }
});
