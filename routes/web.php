<?php

Route::view('/', 'welcome');
Route::get('userVerification/{token}', [App\Http\Controllers\UserVerificationController::class, 'approve'])->name('userVerification');
Auth::routes();

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => '', 'middleware' => ['auth', '2fa', 'admin']], function () {
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

Route::group(['as' => 'frontend.', 'namespace' => '', 'middleware' => ['auth', '2fa']], function () {
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
