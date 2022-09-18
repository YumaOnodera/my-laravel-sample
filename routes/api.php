<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
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

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('user', static function (Request $request) {
        return $request->user();
    });
    Route::controller(UserController::class)->group(function () {
        Route::put('/users', 'update');
        Route::put('/users/update-email', 'updateEmail');
        Route::put('/users/update-password', 'updatePassword');
        Route::delete('/users/{id}', 'destroy');
    });
    Route::controller(PostController::class)->group(function () {
        Route::post('/posts', 'index');
        Route::get('/posts/{id}', 'show');
        Route::post('/posts/store', 'create');
        Route::put('/posts/{id}', 'update');
        Route::delete('/posts/{id}', 'destroy');
    });
});

Route::middleware(['auth:sanctum', 'verified', 'can:admin'])->group(function () {
    Route::controller(UserController::class)->group(function () {
        Route::post('/users', 'index');
        Route::get('/users/{id}', 'show');
        Route::post('/users/{id}/restore', 'restore');
    });
});
