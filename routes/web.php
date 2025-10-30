<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Main application routes
Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

Route::get('dashboard', [App\Http\Controllers\Content\NewsEvents\DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

// Legacy route redirects for backward compatibility
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('users', function () {
        return redirect()->route('system.users.index');
    });
    Route::get('roles', function () {
        return redirect()->route('system.roles.index');
    });
    Route::get('configurations', function () {
        return redirect()->route('system.configurations.index');
    });
    Route::get('menu-manager', function () {
        return redirect()->route('system.menu.index');
    });
    Route::get('audit-log', function () {
        return redirect()->route('system.audit-log.index');
    });
    Route::get('content', function () {
        return redirect()->route('content.index');
    });
});

// API Fallback routes for Docker environment (if api.php routes not working)
Route::get('api/menu-items', [App\Http\Controllers\System\Menu\MenuController::class, 'api'])->name('fallback.api.menu-items');
Route::get('api/configurations/public', [App\Http\Controllers\System\Configurations\ConfigurationController::class, 'getPublic'])->name('fallback.api.configurations.public');

// Protected API fallback routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('api/system/configurations', [App\Http\Controllers\System\Configurations\ConfigurationController::class, 'api'])->name('fallback.api.system.configurations');
    Route::get('api/system/audit-log', [App\Http\Controllers\System\AuditLog\AuditLogController::class, 'getAudits'])->name('fallback.api.system.audit-log');
    Route::get('api/system/audit-log/stats', [App\Http\Controllers\System\AuditLog\AuditLogController::class, 'getStats'])->name('fallback.api.system.audit-log.stats');
    Route::get('api/system/users/{user}', [App\Http\Controllers\System\Users\UserController::class, 'show'])->name('fallback.api.system.users.show');
    Route::post('api/system/configurations/bulk-update', [App\Http\Controllers\System\Configurations\ConfigurationController::class, 'updateMultiple'])->name('fallback.api.system.configurations.bulk-update');
    Route::delete('api/system/users/{user}', [App\Http\Controllers\System\Users\UserController::class, 'destroy'])->name('fallback.api.system.users.destroy');
    Route::post('api/system/users/{user}/toggle-status', [App\Http\Controllers\System\Users\UserController::class, 'toggleStatus'])->name('fallback.api.system.users.toggle-status');
    Route::post('api/system/users/bulk-action', [App\Http\Controllers\System\Users\UserController::class, 'bulkAction'])->name('fallback.api.system.users.bulk-action');
});

// Test route untuk debug - TANPA MIDDLEWARE SAMA SEKALI
Route::get('test', function () {
    return Inertia::render('system/audit-log/AuditLog');
})->name('test');

Route::get('test-content-basic', function () {
    return Inertia::render('content/ContentBasic', [
        'contents' => ['data' => [], 'links' => [], 'meta' => []],
        'type' => 'news',
        'types' => ['news' => 'News', 'event' => 'Event'],
        'categories' => ['general' => 'General'],
        'statuses' => ['published' => 'Published'],
        'filters' => [],
        'bilingualEnabled' => false,
    ]);
})->name('test-content-basic');

Route::get('test-content', function () {
    return Inertia::render('content/ContentBasic', [
        'contents' => ['data' => [], 'links' => [], 'meta' => []],
        'type' => 'news',
        'types' => ['news' => 'News', 'event' => 'Event'],
        'categories' => ['general' => 'General'],
        'statuses' => ['published' => 'Published'],
        'filters' => [],
        'bilingualEnabled' => false,
    ]);
})->middleware(['auth', 'verified'])->name('test-content');

// Include modular route files
require __DIR__.'/modules/auth.php';
require __DIR__.'/modules/settings.php';
require __DIR__.'/modules/system.php';
require __DIR__.'/modules/content.php';
require __DIR__.'/modules/master.php';
require __DIR__.'/modules/reports.php';
