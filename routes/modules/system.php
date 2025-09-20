<?php

use App\Http\Controllers\System\AuditLog\AuditLogController;
use App\Http\Controllers\System\Configurations\ConfigurationController;
use App\Http\Controllers\System\Configurations\AboutConfigurationController;
use App\Http\Controllers\System\Menu\MenuController;
use App\Http\Controllers\System\RolesPermissions\PermissionController;
use App\Http\Controllers\System\RolesPermissions\RoleController;
use App\Http\Controllers\System\Services\ServiceController;
use App\Http\Controllers\System\Users\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('system')->name('system.')->group(function () {

    // Users Management
    Route::prefix('users')->name('users.')->middleware('permission:users.view')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::post('/', [UserController::class, 'store'])->middleware('permission:users.create')->name('store');
        Route::put('/{user}', [UserController::class, 'update'])->middleware('permission:users.edit')->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->middleware('permission:users.delete')->name('destroy');
        Route::post('/bulk-action', [UserController::class, 'bulkAction'])->middleware('permission:users.delete')->name('bulk-action');
    });

    // Roles & Permissions (Super Admin Only)
    Route::prefix('roles')->name('roles.')->middleware('permission:roles.view')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('index');
        Route::post('/', [RoleController::class, 'store'])->middleware('permission:roles.create')->name('store');
        Route::put('/{role}', [RoleController::class, 'update'])->middleware('permission:roles.edit')->name('update');
        Route::delete('/{role}', [RoleController::class, 'destroy'])->middleware('permission:roles.delete')->name('destroy');
    });

    Route::prefix('permissions')->name('permissions.')->middleware('permission:permissions.view')->group(function () {
        Route::get('/', [PermissionController::class, 'index'])->name('index');
        Route::post('/', [PermissionController::class, 'store'])->middleware('permission:permissions.create')->name('store');
        Route::put('/{permission}', [PermissionController::class, 'update'])->middleware('permission:permissions.edit')->name('update');
        Route::delete('/{permission}', [PermissionController::class, 'destroy'])->middleware('permission:permissions.delete')->name('destroy');
    });

    // Configurations
    Route::prefix('configurations')->name('configurations.')->middleware('permission:configurations.view')->group(function () {
        Route::get('/', [ConfigurationController::class, 'index'])->name('index');
        Route::get('/public', [ConfigurationController::class, 'getPublic'])->name('public');
        Route::post('/', [ConfigurationController::class, 'store'])->middleware('permission:configurations.create')->name('store');
        Route::put('/{configuration}', [ConfigurationController::class, 'update'])->middleware('permission:configurations.edit')->name('update');
        Route::delete('/{configuration}', [ConfigurationController::class, 'destroy'])->middleware('permission:configurations.delete')->name('destroy');
        
        // About Page Configuration
        Route::get('/about', [AboutConfigurationController::class, 'index'])->middleware('permission:about.view')->name('about.index');
        Route::put('/about', [AboutConfigurationController::class, 'update'])->middleware('permission:about.edit')->name('about.update');
    });

    // Menu Management
    Route::prefix('menu')->name('menu.')->middleware('permission:menu.view')->group(function () {
        Route::get('/', [MenuController::class, 'index'])->name('index');
        Route::get('/items', [MenuController::class, 'api'])->name('items');
        Route::post('/items', [MenuController::class, 'store'])->middleware('permission:menu.create')->name('items.store');
        Route::put('/items/{menuItem}', [MenuController::class, 'update'])->middleware('permission:menu.edit')->name('items.update');
        Route::delete('/items/{menuItem}', [MenuController::class, 'destroy'])->middleware('permission:menu.delete')->name('items.destroy');
        Route::post('/items/bulk-action', [MenuController::class, 'bulkAction'])->middleware('permission:menu.delete')->name('items.bulk-action');
    });


    // Audit Log
    Route::prefix('audit-log')->name('audit-log.')->middleware('permission:audit-log.view')->group(function () {
        Route::get('/', [AuditLogController::class, 'index'])->name('index');
        Route::get('/filter-options', [AuditLogController::class, 'getFilterOptions'])->name('filter-options');
        Route::get('/export', [AuditLogController::class, 'export'])->name('export');
    });


    // News & Events
    Route::prefix('news-events')->name('news-events.')->middleware('permission:news-events.view')->group(function () {
        Route::get('/', [\App\Http\Controllers\Content\NewsEvents\NewsEventController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Content\NewsEvents\NewsEventController::class, 'store'])->middleware('permission:news-events.create')->name('store');
        Route::put('/{content}', [\App\Http\Controllers\Content\NewsEvents\NewsEventController::class, 'update'])->middleware('permission:news-events.edit')->name('update');
        Route::delete('/{content}', [\App\Http\Controllers\Content\NewsEvents\NewsEventController::class, 'destroy'])->middleware('permission:news-events.delete')->name('destroy');
    });

});
