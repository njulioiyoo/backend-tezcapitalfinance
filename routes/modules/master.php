<?php

use App\Http\Controllers\Master\DepartmentController;
use App\Http\Controllers\Master\LocationController;
use Illuminate\Support\Facades\Route;

Route::prefix('master')->name('master.')->middleware(['auth', 'verified'])->group(function () {
    
    // Departments Management
    Route::prefix('departments')->name('departments.')->middleware('permission:departments.view')->group(function () {
        Route::get('/', [DepartmentController::class, 'index'])->name('index');
        Route::get('/data', [DepartmentController::class, 'getData'])->name('data');
        Route::get('/active', [DepartmentController::class, 'getActive'])->name('active');
        Route::post('/', [DepartmentController::class, 'store'])->middleware('permission:departments.create')->name('store');
        Route::get('/{id}', [DepartmentController::class, 'show'])->name('show');
        Route::put('/{id}', [DepartmentController::class, 'update'])->middleware('permission:departments.edit')->name('update');
        Route::delete('/{id}', [DepartmentController::class, 'destroy'])->middleware('permission:departments.delete')->name('destroy');
        Route::post('/bulk-action', [DepartmentController::class, 'bulkAction'])->middleware('permission:departments.delete')->name('bulk-action');
    });
    
    // Locations Management
    Route::prefix('locations')->name('locations.')->middleware('permission:locations.view')->group(function () {
        Route::get('/', [LocationController::class, 'index'])->name('index');
        Route::get('/data', [LocationController::class, 'getData'])->name('data');
        Route::get('/active', [LocationController::class, 'getActive'])->name('active');
        Route::post('/', [LocationController::class, 'store'])->middleware('permission:locations.create')->name('store');
        Route::get('/{id}', [LocationController::class, 'show'])->name('show');
        Route::put('/{id}', [LocationController::class, 'update'])->middleware('permission:locations.edit')->name('update');
        Route::delete('/{id}', [LocationController::class, 'destroy'])->middleware('permission:locations.delete')->name('destroy');
        Route::post('/bulk-action', [LocationController::class, 'bulkAction'])->middleware('permission:locations.delete')->name('bulk-action');
    });
});


