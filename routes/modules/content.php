<?php

use App\Http\Controllers\ContentController;
use App\Http\Controllers\Content\NewsEvents\NewsEventController;
use App\Http\Controllers\Content\NewsEvents\DashboardController;
use App\Http\Controllers\Content\AboutController;
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
        Route::get('/', [ContentController::class, 'index'])->defaults('type', 'service')->name('index');
        Route::post('/', [ContentController::class, 'store'])->middleware('permission:services.create')->name('store');
        Route::get('/{content}', [ContentController::class, 'show'])->name('show');
        Route::put('/{content}', [ContentController::class, 'update'])->middleware('permission:services.edit')->name('update');
        Route::delete('/{content}', [ContentController::class, 'destroy'])->middleware('permission:services.delete')->name('destroy');
        Route::post('/bulk-action', [ContentController::class, 'bulkAction'])->middleware('permission:services.delete')->name('bulk-action');
    });
});

