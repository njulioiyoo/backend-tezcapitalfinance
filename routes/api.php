<?php

use App\Http\Controllers\Api\AboutController;
use App\Http\Controllers\Api\ComplaintController;
use App\Http\Controllers\Api\ConfigurationController as ApiConfigurationController;
use App\Http\Controllers\Api\HomepageController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\ServiceController as ApiServiceController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\Content\Services\ServiceController;
use App\Http\Controllers\System\AuditLog\AuditLogController;
use App\Http\Controllers\System\Configurations\ConfigurationController;
use App\Http\Controllers\System\Menu\MenuController;
use App\Http\Controllers\System\RolesPermissions\PermissionController;
use App\Http\Controllers\System\RolesPermissions\RoleController;
use App\Http\Controllers\System\Users\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Public API endpoints (no authentication required)
Route::prefix('v1')->group(function () {
    // Homepage API
    Route::get('homepage', [HomepageController::class, 'index'])->name('api.homepage.index');
    Route::get('homepage/{section}', [HomepageController::class, 'getSection'])->name('api.homepage.section');
    
    // About API
    Route::get('about', [AboutController::class, 'index'])->name('api.about.index');
    Route::get('about/{section}', [AboutController::class, 'getSection'])->name('api.about.section');
    
    // Complaints API - Public endpoint for submitting complaints
    Route::post('complaints', [ComplaintController::class, 'store'])
        ->middleware(['throttle:complaints'])
        ->name('api.complaints.store');
    
    Route::get('news', [ContentController::class, 'newsApi'])->name('api.news.index');
    Route::get('news/{slug}', [ContentController::class, 'showNews'])->name('api.news.show');
    Route::get('services', [ApiServiceController::class, 'index'])->name('api.services.index');
    Route::get('services/{id}', [ApiServiceController::class, 'show'])->name('api.services.show');
    Route::get('services/slug/{slug}', [ApiServiceController::class, 'showBySlug'])->name('api.services.show-by-slug');
    Route::get('csrf-token', function () {
        return response()->json(['csrf_token' => csrf_token()]);
    })->name('api.csrf-token');
    
    Route::prefix('configurations')->name('api.configurations.')->group(function () {
        Route::get('/', [ApiConfigurationController::class, 'index'])->name('index');
        Route::get('/key/{key}', [ApiConfigurationController::class, 'show'])->name('show');
    });
    
    // Reports API
    Route::prefix('reports')->name('api.reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('financial', [ReportController::class, 'financial'])->name('financial');
        Route::get('annual', [ReportController::class, 'annual'])->name('annual');
        Route::get('{id}', [ReportController::class, 'show'])->name('show');
        Route::get('{id}/download', [ReportController::class, 'download'])->name('download');
    });
});

// Protected API endpoints - using web middleware for Inertia.js
Route::middleware(['web', 'auth', 'verified'])->group(function () {
    // Menu items API - requires authentication for permission filtering
    Route::get('menu-items', [MenuController::class, 'api'])->name('api.menu-items.index');
    // System API routes
    Route::prefix('system')->name('api.system.')->group(function () {
        // Users API
        Route::prefix('users')->name('users.')->middleware('permission:users.view')->group(function () {
            Route::get('/', [UserController::class, 'getData'])->name('index');
            Route::post('/', [UserController::class, 'store'])->middleware('permission:users.create')->name('store');
            Route::get('{user}', [UserController::class, 'show'])->name('show');
            Route::put('{user}', [UserController::class, 'update'])->middleware('permission:users.edit')->name('update');
            Route::delete('{user}', [UserController::class, 'destroy'])->middleware('permission:users.delete')->name('destroy');
            Route::post('bulk-action', [UserController::class, 'bulkAction'])->middleware('permission:users.delete')->name('bulk-action');
            Route::post('{user}/toggle-status', [UserController::class, 'toggleStatus'])->middleware('permission:users.edit')->name('toggle-status');
            Route::get('stats', [UserController::class, 'getStats'])->name('stats');
        });

        // Roles & Permissions API (Super Admin Only)
        Route::prefix('roles-permissions')->name('roles-permissions.')->group(function () {
            Route::apiResource('roles', RoleController::class)->except(['index'])->middleware('permission:roles.create,roles.edit,roles.delete');
            Route::apiResource('permissions', PermissionController::class)->middleware('permission:permissions.view,permissions.create,permissions.edit,permissions.delete');
        });

        // Audit Log API
        Route::prefix('audit-log')->name('audit-log.')->group(function () {
            Route::get('/', [AuditLogController::class, 'getAudits'])->name('index');
            Route::get('stats', [AuditLogController::class, 'getStats'])->name('stats');
            Route::get('filter-options', [AuditLogController::class, 'getFilterOptions'])->name('filter-options');
            Route::post('export', [AuditLogController::class, 'export'])->name('export');
        });

        // Menu API
        Route::prefix('menu')->name('menu.')->group(function () {
            Route::get('items/all', [MenuController::class, 'all'])->name('items.all');
            Route::post('items', [MenuController::class, 'store'])->name('items.store');
            Route::get('items/{menuItem}', [MenuController::class, 'show'])->name('items.show');
            Route::put('items/{menuItem}', [MenuController::class, 'update'])->name('items.update');
            Route::delete('items/{menuItem}', [MenuController::class, 'destroy'])->name('items.destroy');
            Route::post('items/reorder', [MenuController::class, 'reorder'])->name('items.reorder');
        });

        // Configurations API
        Route::prefix('configurations')->name('configurations.')->middleware('permission:configurations.view')->group(function () {
            Route::get('/', [ConfigurationController::class, 'api'])->name('index');
            Route::get('group/{group}', [ConfigurationController::class, 'getByGroup'])->name('group');
            Route::post('/', [ConfigurationController::class, 'store'])->middleware('permission:configurations.create')->name('store');
            Route::get('{configuration}', [ConfigurationController::class, 'show'])->name('show');
            Route::put('{configuration}', [ConfigurationController::class, 'update'])->middleware('permission:configurations.edit')->name('update');
            Route::delete('{configuration}', [ConfigurationController::class, 'destroy'])->middleware('permission:configurations.delete')->name('destroy');
            Route::post('bulk-update', [ConfigurationController::class, 'updateMultiple'])->middleware('permission:configurations.edit')->name('bulk-update');
        });

        // News & Events API
        Route::prefix('news-events')->name('news-events.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Content\NewsEvents\NewsEventController::class, 'getData'])->name('index');
            Route::post('/', [\App\Http\Controllers\Content\NewsEvents\NewsEventController::class, 'store'])->name('store');
            Route::get('/{content}', [\App\Http\Controllers\Content\NewsEvents\NewsEventController::class, 'show'])->name('show');
            Route::put('/{content}', [\App\Http\Controllers\Content\NewsEvents\NewsEventController::class, 'update'])->name('update');
            Route::delete('/{content}', [\App\Http\Controllers\Content\NewsEvents\NewsEventController::class, 'destroy'])->name('destroy');
            Route::post('/bulk-action', [\App\Http\Controllers\Content\NewsEvents\NewsEventController::class, 'bulkAction'])->name('bulk-action');
        });

        // Content API (unified news & events and services)
        Route::prefix('content')->name('content.')->group(function () {
            // News & Events Dashboard API
            Route::prefix('news-events')->name('news-events.')->middleware('permission:news-events.view')->group(function () {
                Route::get('dashboard', [\App\Http\Controllers\Content\NewsEvents\DashboardController::class, 'apiData'])->name('dashboard');
                Route::get('analytics/{type?}', [\App\Http\Controllers\Content\NewsEvents\DashboardController::class, 'getContentAnalytics'])->name('analytics');
            });
            
            // Services API
            Route::prefix('services')->name('services.')->middleware('permission:services.view')->group(function () {
                Route::get('/', [\App\Http\Controllers\Content\Services\ServiceController::class, 'getData'])->name('index');
                Route::post('/', [\App\Http\Controllers\Content\Services\ServiceController::class, 'store'])->middleware('permission:services.create')->name('store');
                Route::get('filter-options', [\App\Http\Controllers\Content\Services\ServiceController::class, 'getFilterOptions'])->name('filter-options');
                Route::get('stats', [\App\Http\Controllers\Content\Services\ServiceController::class, 'getStats'])->name('stats');
                Route::get('{service}', [\App\Http\Controllers\Content\Services\ServiceController::class, 'show'])->name('show');
                Route::put('{service}', [\App\Http\Controllers\Content\Services\ServiceController::class, 'update'])->middleware('permission:services.edit')->name('update');
                Route::delete('{service}', [\App\Http\Controllers\Content\Services\ServiceController::class, 'destroy'])->middleware('permission:services.delete')->name('destroy');
                Route::post('bulk-action', [\App\Http\Controllers\Content\Services\ServiceController::class, 'bulkAction'])->middleware('permission:services.delete')->name('bulk-action');
            });
            
            // Other content APIs
            Route::post('/', [ContentController::class, 'store'])->middleware('permission:content.create')->name('store');
            Route::put('{content}', [ContentController::class, 'update'])->middleware('permission:content.edit')->name('update');
            Route::delete('{content}', [ContentController::class, 'destroy'])->middleware('permission:content.delete')->name('destroy');
            Route::post('bulk-action', [ContentController::class, 'bulkAction'])->middleware('permission:content.delete')->name('bulk-action');
            Route::post('upload-image', [ContentController::class, 'uploadImage'])->middleware('permission:content.create,content.edit')->name('upload-image');
            Route::post('update-event-statuses', [ContentController::class, 'updateEventStatuses'])->middleware('permission:content.edit')->name('update-event-statuses');
        });
    });

    // Report API routes (placeholder for future implementation)
    Route::prefix('report')->name('api.report.')->group(function () {
        // Analytics routes (placeholder for future implementation)
        Route::prefix('analytics')->name('analytics.')->group(function () {
            // Route::get('dashboard', [AnalyticsController::class, 'dashboard'])->name('dashboard');
            // Route::get('traffic', [AnalyticsController::class, 'traffic'])->name('traffic');
        });

        // Financial Reports routes (placeholder for future implementation)
        Route::prefix('financial')->name('financial.')->group(function () {
            // Route::get('revenue', [FinancialController::class, 'revenue'])->name('revenue');
            // Route::get('expenses', [FinancialController::class, 'expenses'])->name('expenses');
        });
    });
});
