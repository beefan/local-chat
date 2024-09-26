<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SystemPromptController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/chat/history', [ChatController::class, 'chatHistory'])->name('chat.history');
    Route::get('/chat/{chat}', [ChatController::class, 'getChat'])->name('chat.get');
    Route::get('/chat', [ChatController::class, 'newChat'])->name('chat');
    Route::post('/chat', [ChatController::class, 'chat'])->name('chat.message');

    Route::get('/system-prompts', [SystemPromptController::class, 'index'])->name('system-prompts');
});

require __DIR__ . '/auth.php';
