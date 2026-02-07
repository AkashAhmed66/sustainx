<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // User Management Routes
    Route::middleware('permission:view users')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
    });
    Route::middleware('permission:create users')->group(function () {
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
    });
    Route::middleware('permission:edit users')->group(function () {
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    });
    Route::middleware('permission:delete users')->group(function () {
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });
    
    // Role Management Routes
    Route::middleware('permission:view roles')->group(function () {
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    });
    Route::middleware('permission:create roles')->group(function () {
        Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    });
    Route::middleware('permission:edit roles')->group(function () {
        Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    });
    Route::middleware('permission:delete roles')->group(function () {
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
    });
    
    // Permission Management Routes
    Route::middleware('permission:view permissions')->group(function () {
        Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
    });
    Route::middleware('permission:create permissions')->group(function () {
        Route::get('/permissions/create', [PermissionController::class, 'create'])->name('permissions.create');
        Route::post('/permissions', [PermissionController::class, 'store'])->name('permissions.store');
    });
    Route::middleware('permission:edit permissions')->group(function () {
        Route::get('/permissions/{permission}/edit', [PermissionController::class, 'edit'])->name('permissions.edit');
        Route::put('/permissions/{permission}', [PermissionController::class, 'update'])->name('permissions.update');
    });
    Route::middleware('permission:delete permissions')->group(function () {
        Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy');
    });
});

require __DIR__.'/auth.php';
