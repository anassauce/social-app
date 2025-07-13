<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ConnectionController;
use App\Http\Controllers\InvitationController;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    
    Route::middleware('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/profile', [AuthController::class, 'profile']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
    });
});

Route::middleware('auth')->group(function () {
    
    Route::prefix('posts')->group(function () {
        Route::get('/', [PostController::class, 'index']);
        Route::post('/', [PostController::class, 'store']);
        Route::get('/my-posts', [PostController::class, 'myPosts']);
        Route::post('/generate-ai', [PostController::class, 'generateWithAI']);
        Route::get('/{post}', [PostController::class, 'show']);
        Route::put('/{post}', [PostController::class, 'update']);
        Route::delete('/{post}', [PostController::class, 'destroy']);
        Route::post('/{post}/improve-ai', [PostController::class, 'improveWithAI']);
    });

    Route::prefix('connections')->group(function () {
        Route::get('/', [ConnectionController::class, 'index']);
        Route::delete('/{connection}', [ConnectionController::class, 'destroy']);
        Route::post('/block/{user}', [ConnectionController::class, 'block']);
        Route::post('/unblock/{user}', [ConnectionController::class, 'unblock']);
        Route::get('/blocked', [ConnectionController::class, 'blocked']);
        Route::get('/suggestions', [ConnectionController::class, 'suggestions']);
        Route::get('/mutual/{user}', [ConnectionController::class, 'mutualConnections']);
    });

    Route::prefix('invitations')->group(function () {
        Route::get('/', [InvitationController::class, 'index']);
        Route::post('/', [InvitationController::class, 'store']);
        Route::get('/pending', [InvitationController::class, 'pending']);
        Route::get('/stats', [InvitationController::class, 'stats']);
        Route::get('/{invitation}', [InvitationController::class, 'show']);
        Route::post('/{invitation}/accept', [InvitationController::class, 'accept']);
        Route::post('/{invitation}/reject', [InvitationController::class, 'reject']);
        Route::delete('/{invitation}', [InvitationController::class, 'destroy']);
    });
});

Route::get('/health', function () {
    return response()->json([
        'success' => true,
        'message' => 'API is healthy',
        'timestamp' => now()
    ]);
});