<?php

use App\Http\Controllers\ContentController;
use App\Http\Controllers\Content\NewsEvents\NewsEventController;
use App\Http\Controllers\Content\NewsEvents\DashboardController;
use App\Http\Controllers\Content\AboutController;
use App\Http\Controllers\Content\Services\ServiceController;
use Illuminate\Support\Facades\Route;

Route::prefix('content')->name('content.')->middleware(['auth', 'verified'])->group(function () {
    // About Page
    Route::get('/about', [AboutController::class, 'index'])->middleware('permission:about.view')->name('about');
    
    // News & Events sub-routes
    Route::prefix('news-events')->name('news-events.')->middleware('permission:news-events.view')->group(function () {
        Route::get('/', [NewsEventController::class, 'index'])->name('index');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('/', [NewsEventController::class, 'store'])->middleware('permission:news-events.create')->name('store');
        Route::get('/{content}', [NewsEventController::class, 'show'])->name('show');
        Route::put('/{content}', [NewsEventController::class, 'update'])->middleware('permission:news-events.edit')->name('update');
        Route::delete('/{content}', [NewsEventController::class, 'destroy'])->middleware('permission:news-events.delete')->name('destroy');
        Route::post('/bulk-action', [NewsEventController::class, 'bulkAction'])->middleware('permission:news-events.delete')->name('bulk-action');
    });
    
    // Partners sub-routes  
    Route::prefix('partners')->name('partners.')->middleware('permission:partners.view')->group(function () {
        Route::get('/', [ContentController::class, 'index'])->defaults('type', 'partner')->name('index');
        Route::get('/create', [ContentController::class, 'create'])->defaults('type', 'partner')->middleware('permission:partners.create')->name('create');
        Route::post('/', [ContentController::class, 'store'])->middleware('permission:partners.create')->name('store');
        Route::get('/{content}', [ContentController::class, 'show'])->name('show');
        Route::get('/{content}/edit', [ContentController::class, 'edit'])->middleware('permission:partners.edit')->name('edit');
        Route::put('/{content}', [ContentController::class, 'update'])->middleware('permission:partners.edit')->name('update');
        Route::delete('/{content}', [ContentController::class, 'destroy'])->middleware('permission:partners.delete')->name('destroy');
        Route::post('/bulk-action', [ContentController::class, 'bulkAction'])->middleware('permission:partners.delete')->name('bulk-action');
    });
    
    // Services sub-routes  
    Route::prefix('services')->name('services.')->middleware('permission:services.view')->group(function () {
        Route::get('/', [ServiceController::class, 'index'])->name('index');
        Route::post('/', [ServiceController::class, 'store'])->middleware('permission:services.create')->name('store');
        Route::get('/data', [ServiceController::class, 'getData'])->name('data');
        Route::get('/{service}', [ServiceController::class, 'show'])->name('show');
        Route::put('/{service}', [ServiceController::class, 'update'])->middleware('permission:services.edit')->name('update');
        Route::delete('/{service}', [ServiceController::class, 'destroy'])->middleware('permission:services.delete')->name('destroy');
        Route::post('/bulk-action', [ServiceController::class, 'bulkAction'])->middleware('permission:services.delete')->name('bulk-action');
    });
    
    // Careers Management
    Route::prefix('careers')->name('careers.')->middleware('permission:view_careers')->group(function () {
        Route::get('/', [\App\Http\Controllers\Content\CareerController::class, 'index'])->name('index');
        Route::get('/data', [\App\Http\Controllers\Content\CareerController::class, 'getData'])->name('data');
        Route::post('/', [\App\Http\Controllers\Content\CareerController::class, 'store'])->middleware('permission:create_careers')->name('store');
        Route::get('/{id}', [\App\Http\Controllers\Content\CareerController::class, 'show'])->name('show');
        Route::put('/{id}', [\App\Http\Controllers\Content\CareerController::class, 'update'])->middleware('permission:edit_careers')->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Content\CareerController::class, 'destroy'])->middleware('permission:delete_careers')->name('destroy');
        Route::post('/bulk-action', [\App\Http\Controllers\Content\CareerController::class, 'bulkAction'])->middleware('permission:delete_careers')->name('bulk-action');
    });
    
    // Team Members Management
    Route::prefix('team-members')->name('team-members.')->middleware('permission:view_team-members')->group(function () {
        Route::get('/', [\App\Http\Controllers\Content\TeamMemberController::class, 'index'])->name('index');
        Route::get('/data', [\App\Http\Controllers\Content\TeamMemberController::class, 'getData'])->name('data');
        Route::post('/', [\App\Http\Controllers\Content\TeamMemberController::class, 'store'])->middleware('permission:create_team-members')->name('store');
        Route::get('/{id}', [\App\Http\Controllers\Content\TeamMemberController::class, 'show'])->name('show');
        Route::put('/{id}', [\App\Http\Controllers\Content\TeamMemberController::class, 'update'])->middleware('permission:edit_team-members')->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Content\TeamMemberController::class, 'destroy'])->middleware('permission:delete_team-members')->name('destroy');
        Route::post('/bulk-action', [\App\Http\Controllers\Content\TeamMemberController::class, 'bulkAction'])->middleware('permission:delete_team-members')->name('bulk-action');
    });
    
    // Workplace sub-routes  
    Route::prefix('workplace')->name('workplace.')->middleware('permission:workplace.view')->group(function () {
        Route::get('/', [ContentController::class, 'index'])->defaults('type', 'workplace')->name('index');
        Route::get('/create', [ContentController::class, 'create'])->defaults('type', 'workplace')->middleware('permission:workplace.create')->name('create');
        Route::post('/', [ContentController::class, 'store'])->middleware('permission:workplace.create')->name('store');
        Route::get('/{content}', [ContentController::class, 'show'])->name('show');
        Route::get('/{content}/edit', [ContentController::class, 'edit'])->middleware('permission:workplace.edit')->name('edit');
        Route::put('/{id}', [ContentController::class, 'update'])->middleware('permission:workplace.edit')->name('update');
        Route::delete('/{id}', [ContentController::class, 'destroy'])->middleware('permission:workplace.delete')->name('destroy');
        Route::post('/bulk-action', [ContentController::class, 'bulkAction'])->middleware('permission:workplace.delete')->name('bulk-action');
    });
});

