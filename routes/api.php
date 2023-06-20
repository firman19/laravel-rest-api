<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::resource('/blogs', BlogController::class);
    Route::resource('/comments', CommentController::class);
    Route::get('/blogs/comments/{id}', [BlogController::class, 'show_comments']);
    Route::post('/blogs/likes/{id}', [BlogController::class, 'like_dislike']);
    Route::post('/comments/likes/{id}', [CommentController::class, 'like_dislike']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/userinfo', [AuthController::class, 'getUserInfo']);
});