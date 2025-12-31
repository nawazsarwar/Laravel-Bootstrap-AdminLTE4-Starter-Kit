<?php

Route::group(['prefix' => 'v1', 'as' => 'api.', 'namespace' => 'App\Http\Controllers\Api\V1\Admin', 'middleware' => ['auth:sanctum']], function () {
    // Users
    Route::apiResource('users', UsersApiController::class);
});
