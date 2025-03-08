<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\UserController;

// Route d'authentification (à mettre en premier)
require __DIR__ . '/auth.php';

// Routes publiques
Route::get('/', [PageController::class, 'home'])->name('home');

// Routes nécessitant une authentification
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // Profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Administration
    Route::prefix('admin')->name('admin.')->group(function () {
        // Users
        Route::middleware(['can:viewAny,App\Models\User'])->group(function () {
            Route::get('/users', [UserController::class, 'index'])->name('users.index');
            Route::patch('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.update-role');
            Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        });

        // Pages
        Route::resource('pages', PageController::class)->except(['show']);
        Route::get('pages/{page}', [PageController::class, 'show'])->name('pages.show');
        Route::get('pages/{page}/preview', [PageController::class, 'preview'])->name('pages.preview');
        Route::patch('pages/{page}/publish', [PageController::class, 'publish'])->name('pages.publish');
    });
});

// Routes publiques pour les pages (doit être en dernier)
Route::get('/{slug}', [PageController::class, 'showBySlug'])
    ->name('pages.show')
    ->where('slug', '^(?!auth|admin|api|login|logout|register|password|email|verification|confirm|profile|dashboard).*$');
