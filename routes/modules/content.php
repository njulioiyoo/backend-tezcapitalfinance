<?php

use App\Http\Controllers\ContentController;
use App\Http\Controllers\Content\NewsEvents\NewsEventController;
use App\Http\Controllers\Content\AboutController;
use Illuminate\Support\Facades\Route;

Route::prefix('content')->name('content.')->middleware(['auth', 'verified'])->group(function () {
    // About Page
    Route::get('/about', [AboutController::class, 'index'])->name('about');
    
    // News & Events sub-routes
    Route::prefix('news-events')->name('news-events.')->group(function () {
        Route::get('/', [NewsEventController::class, 'index'])->name('index');
        Route::post('/', [NewsEventController::class, 'store'])->name('store');
        Route::get('/{content}', [NewsEventController::class, 'show'])->name('show');
        Route::put('/{content}', [NewsEventController::class, 'update'])->name('update');
        Route::delete('/{content}', [NewsEventController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-action', [NewsEventController::class, 'bulkAction'])->name('bulk-action');
    });
    
    // Partners sub-routes  
    Route::prefix('partners')->name('partners.')->group(function () {
        Route::get('/', [ContentController::class, 'index'])->defaults('type', 'partner')->name('index');
        Route::get('/create', [ContentController::class, 'create'])->defaults('type', 'partner')->name('create');
        Route::post('/', [ContentController::class, 'store'])->name('store');
        Route::get('/{content}', [ContentController::class, 'show'])->name('show');
        Route::get('/{content}/edit', [ContentController::class, 'edit'])->name('edit');
        Route::put('/{content}', [ContentController::class, 'update'])->name('update');
        Route::delete('/{content}', [ContentController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-action', [ContentController::class, 'bulkAction'])->name('bulk-action');
    });
    
    // Services sub-routes  
    Route::prefix('services')->name('services.')->group(function () {
        Route::get('/', [ContentController::class, 'index'])->defaults('type', 'service')->name('index');
        Route::post('/', [ContentController::class, 'store'])->name('store');
        Route::get('/{content}', [ContentController::class, 'show'])->name('show');
        Route::put('/{content}', [ContentController::class, 'update'])->name('update');
        Route::delete('/{content}', [ContentController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-action', [ContentController::class, 'bulkAction'])->name('bulk-action');
    });
});

