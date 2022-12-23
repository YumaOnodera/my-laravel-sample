<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\EmailResetController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::controller(UserController::class)->group(function () {
    Route::get('/users', 'index');
    Route::get('/users/{id}', 'show');
});

Route::controller(PostController::class)->group(function () {
    Route::get('/posts', 'index');
    Route::get('/posts/{id}', 'show');
});

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::controller(UserController::class)->group(function () {
        Route::get('/user', 'mySelf');
        Route::put('/users/{id}', 'update');
        Route::put('/users/{id}/update-password', 'updatePassword');
        Route::delete('/users/{id}', 'destroy');
    });
    Route::controller(EmailResetController::class)->group(function () {
        Route::post('/email-resets/send-reset-link', 'store');
        Route::put('/email-resets/{token}', 'update');
    });
    Route::controller(PasswordResetController::class)->group(function () {
        Route::put('/password-resets', 'update');
    });
    Route::controller(PostController::class)->group(function () {
        Route::post('/posts', 'store');
        Route::put('/posts/{id}', 'update');
        Route::delete('/posts/{id}', 'destroy');
    });
    Route::controller(CommentController::class)->group(function () {
        Route::get('/comments', 'index');
        Route::post('/comments', 'store');
        Route::delete('/comments/{id}', 'destroy');
    });
});

Route::middleware(['auth:sanctum', 'verified', 'can:admin'])->group(function () {
    Route::controller(UserController::class)->group(function () {
        Route::put('/users/{id}/update-permission', 'updatePermission');
        Route::post('/users/{id}/restore', 'restore');
    });
});
