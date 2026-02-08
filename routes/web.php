<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\SubsectionController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\FactoryTypeController;
use App\Http\Controllers\FactoryController;
use App\Http\Controllers\AssessmentController;
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
        Route::delete('/users', [UserController::class, 'bulkDelete'])->name('users.bulk-delete');
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
        Route::delete('/roles', [RoleController::class, 'bulkDelete'])->name('roles.bulk-delete');
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
        Route::delete('/permissions', [PermissionController::class, 'bulkDelete'])->name('permissions.bulk-delete');
    });

    // ESG Structure - Section Management Routes
    Route::middleware('permission:view sections')->group(function () {
        Route::get('/sections', [SectionController::class, 'index'])->name('sections.index');
    });
    Route::middleware('permission:create sections')->group(function () {
        Route::get('/sections/create', [SectionController::class, 'create'])->name('sections.create');
        Route::post('/sections', [SectionController::class, 'store'])->name('sections.store');
    });
    Route::middleware('permission:edit sections')->group(function () {
        Route::get('/sections/{section}/edit', [SectionController::class, 'edit'])->name('sections.edit');
        Route::put('/sections/{section}', [SectionController::class, 'update'])->name('sections.update');
    });
    Route::middleware('permission:delete sections')->group(function () {
        Route::delete('/sections/{section}', [SectionController::class, 'destroy'])->name('sections.destroy');
        Route::delete('/sections', [SectionController::class, 'bulkDelete'])->name('sections.bulk-delete');
    });

    // ESG Structure - Subsection Management Routes
    Route::middleware('permission:view subsections')->group(function () {
        Route::get('/subsections', [SubsectionController::class, 'index'])->name('subsections.index');
    });
    Route::middleware('permission:create subsections')->group(function () {
        Route::get('/subsections/create', [SubsectionController::class, 'create'])->name('subsections.create');
        Route::post('/subsections', [SubsectionController::class, 'store'])->name('subsections.store');
    });
    Route::middleware('permission:edit subsections')->group(function () {
        Route::get('/subsections/{subsection}/edit', [SubsectionController::class, 'edit'])->name('subsections.edit');
        Route::put('/subsections/{subsection}', [SubsectionController::class, 'update'])->name('subsections.update');
    });
    Route::middleware('permission:delete subsections')->group(function () {
        Route::delete('/subsections/{subsection}', [SubsectionController::class, 'destroy'])->name('subsections.destroy');
        Route::delete('/subsections', [SubsectionController::class, 'bulkDelete'])->name('subsections.bulk-delete');
    });

    // ESG Structure - Item Management Routes
    Route::middleware('permission:view items')->group(function () {
        Route::get('/items', [ItemController::class, 'index'])->name('items.index');
    });
    Route::middleware('permission:create items')->group(function () {
        Route::get('/items/create', [ItemController::class, 'create'])->name('items.create');
        Route::post('/items', [ItemController::class, 'store'])->name('items.store');
    });
    Route::middleware('permission:edit items')->group(function () {
        Route::get('/items/{item}/edit', [ItemController::class, 'edit'])->name('items.edit');
        Route::put('/items/{item}', [ItemController::class, 'update'])->name('items.update');
    });
    Route::middleware('permission:delete items')->group(function () {
        Route::delete('/items/{item}', [ItemController::class, 'destroy'])->name('items.destroy');
        Route::delete('/items', [ItemController::class, 'bulkDelete'])->name('items.bulk-delete');
    });

    // ESG Structure - Question Management Routes
    Route::middleware('permission:view questions')->group(function () {
        Route::get('/questions', [QuestionController::class, 'index'])->name('questions.index');
    });
    Route::middleware('permission:create questions')->group(function () {
        Route::get('/questions/create', [QuestionController::class, 'create'])->name('questions.create');
        Route::post('/questions', [QuestionController::class, 'store'])->name('questions.store');
    });
    Route::middleware('permission:edit questions')->group(function () {
        Route::get('/questions/{question}/edit', [QuestionController::class, 'edit'])->name('questions.edit');
        Route::put('/questions/{question}', [QuestionController::class, 'update'])->name('questions.update');
    });
    Route::middleware('permission:delete questions')->group(function () {
        Route::delete('/questions/{question}', [QuestionController::class, 'destroy'])->name('questions.destroy');
        Route::delete('/questions', [QuestionController::class, 'bulkDelete'])->name('questions.bulk-delete');
    });

    // Location - Country Management Routes
    Route::middleware('permission:view countries')->group(function () {
        Route::get('/countries', [CountryController::class, 'index'])->name('countries.index');
    });
    Route::middleware('permission:create countries')->group(function () {
        Route::get('/countries/create', [CountryController::class, 'create'])->name('countries.create');
        Route::post('/countries', [CountryController::class, 'store'])->name('countries.store');
    });
    Route::middleware('permission:edit countries')->group(function () {
        Route::get('/countries/{country}/edit', [CountryController::class, 'edit'])->name('countries.edit');
        Route::put('/countries/{country}', [CountryController::class, 'update'])->name('countries.update');
    });
    Route::middleware('permission:delete countries')->group(function () {
        Route::delete('/countries/{country}', [CountryController::class, 'destroy'])->name('countries.destroy');
        Route::delete('/countries', [CountryController::class, 'bulkDelete'])->name('countries.bulk-delete');
    });

    // Factory - Factory Type Management Routes
    Route::middleware('permission:view factory-types')->group(function () {
        Route::get('/factory-types', [FactoryTypeController::class, 'index'])->name('factory-types.index');
    });
    Route::middleware('permission:create factory-types')->group(function () {
        Route::get('/factory-types/create', [FactoryTypeController::class, 'create'])->name('factory-types.create');
        Route::post('/factory-types', [FactoryTypeController::class, 'store'])->name('factory-types.store');
    });
    Route::middleware('permission:edit factory-types')->group(function () {
        Route::get('/factory-types/{factory_type}/edit', [FactoryTypeController::class, 'edit'])->name('factory-types.edit');
        Route::put('/factory-types/{factory_type}', [FactoryTypeController::class, 'update'])->name('factory-types.update');
    });
    Route::middleware('permission:delete factory-types')->group(function () {
        Route::delete('/factory-types/{factory_type}', [FactoryTypeController::class, 'destroy'])->name('factory-types.destroy');
        Route::delete('/factory-types', [FactoryTypeController::class, 'bulkDelete'])->name('factory-types.bulk-delete');
    });

    // Factory - Factory Management Routes
    Route::middleware('permission:view factories')->group(function () {
        Route::get('/factories', [FactoryController::class, 'index'])->name('factories.index');
    });
    Route::middleware('permission:create factories')->group(function () {
        Route::get('/factories/create', [FactoryController::class, 'create'])->name('factories.create');
        Route::post('/factories', [FactoryController::class, 'store'])->name('factories.store');
    });
    Route::middleware('permission:edit factories')->group(function () {
        Route::get('/factories/{factory}/edit', [FactoryController::class, 'edit'])->name('factories.edit');
        Route::put('/factories/{factory}', [FactoryController::class, 'update'])->name('factories.update');
    });
    Route::middleware('permission:delete factories')->group(function () {
        Route::delete('/factories/{factory}', [FactoryController::class, 'destroy'])->name('factories.destroy');
        Route::delete('/factories', [FactoryController::class, 'bulkDelete'])->name('factories.bulk-delete');
    });

    // Assessment Management Routes
    Route::middleware('permission:view assessments')->group(function () {
        Route::get('/assessments', [AssessmentController::class, 'index'])->name('assessments.index');
        Route::get('/assessments/{assessment}', [AssessmentController::class, 'show'])->name('assessments.show');
    });
    Route::middleware('permission:create assessments')->group(function () {
        Route::get('/assessments/create', [AssessmentController::class, 'create'])->name('assessments.create');
        Route::post('/assessments', [AssessmentController::class, 'store'])->name('assessments.store');
    });
    Route::middleware('permission:edit assessments')->group(function () {
        Route::get('/assessments/{assessment}/edit', [AssessmentController::class, 'edit'])->name('assessments.edit');
        Route::put('/assessments/{assessment}', [AssessmentController::class, 'update'])->name('assessments.update');
    });
    Route::middleware('permission:delete assessments')->group(function () {
        Route::delete('/assessments/{assessment}', [AssessmentController::class, 'destroy'])->name('assessments.destroy');
        Route::delete('/assessments', [AssessmentController::class, 'bulkDelete'])->name('assessments.bulk-delete');
    });
});

require __DIR__.'/auth.php';
