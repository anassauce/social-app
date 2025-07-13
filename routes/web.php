<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ConnectionController;
use App\Http\Controllers\InvitationController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');
    
    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');
    
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    
    Route::prefix('posts')->name('posts.')->group(function () {
        Route::get('/', [PostController::class, 'index'])->name('index');
        Route::get('/create', function () {
            return view('posts.create');
        })->name('create');
        Route::get('/my-posts', [PostController::class, 'myPosts'])->name('my-posts');
        Route::get('/ai/generate', function () {
            return view('posts.ai-generate');
        })->name('ai.generate');
        Route::post('/', [PostController::class, 'store'])->name('store');
        Route::post('/ai/generate', [PostController::class, 'generateWithAI'])->name('ai.generate.post');
        Route::post('/ai/confirm', [PostController::class, 'confirmAIPost'])->name('ai.confirm');
        Route::get('/{post}', [PostController::class, 'show'])->name('show');
        Route::get('/{post}/edit', [PostController::class, 'edit'])->name('edit');
        Route::put('/{post}', [PostController::class, 'update'])->name('update');
        Route::delete('/{post}', [PostController::class, 'destroy'])->name('destroy');
        Route::post('/{post}/ai/improve', [PostController::class, 'improveWithAI'])->name('ai.improve');
        Route::post('/{post}/like', [PostController::class, 'like'])->name('like');
        Route::post('/{post}/comment', [PostController::class, 'comment'])->name('comment');
    });
    
    Route::prefix('connections')->name('connections.')->group(function () {
        Route::get('/', [ConnectionController::class, 'index'])->name('index');
        Route::delete('/{connection}', [ConnectionController::class, 'destroy'])->name('destroy');
        Route::post('/block/{user}', [ConnectionController::class, 'block'])->name('block');
        Route::post('/unblock/{user}', [ConnectionController::class, 'unblock'])->name('unblock');
        Route::get('/blocked', [ConnectionController::class, 'blocked'])->name('blocked');
        Route::get('/suggestions', [ConnectionController::class, 'suggestions'])->name('suggestions');
        Route::get('/mutual/{user}', [ConnectionController::class, 'mutualConnections'])->name('mutual');
    });
    
    Route::prefix('invitations')->name('invitations.')->group(function () {
        Route::get('/', [InvitationController::class, 'index'])->name('index');
        Route::get('/create', [InvitationController::class, 'create'])->name('create');
        Route::post('/', [InvitationController::class, 'store'])->name('store');
        Route::get('/pending', [InvitationController::class, 'pending'])->name('pending');
        Route::get('/sent', [InvitationController::class, 'sent'])->name('sent');
        Route::get('/stats', [InvitationController::class, 'stats'])->name('stats');
        Route::get('/{invitation}', [InvitationController::class, 'show'])->name('show');
        Route::post('/{invitation}/accept', [InvitationController::class, 'accept'])->name('accept');
        Route::post('/{invitation}/reject', [InvitationController::class, 'reject'])->name('reject');
        Route::delete('/{invitation}', [InvitationController::class, 'destroy'])->name('destroy');
    });
});

Route::fallback(function () {
    return view('errors.404');
});
