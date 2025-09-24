<?php

use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::prefix('reports')->name('reports.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [ReportController::class, 'index'])->middleware('permission:reports.view')->name('index');
    Route::post('/', [ReportController::class, 'store'])->middleware('permission:reports.create')->name('store');
    Route::put('/{report}', [ReportController::class, 'update'])->middleware('permission:reports.edit')->name('update');
    Route::post('/{report}', [ReportController::class, 'update'])->middleware('permission:reports.edit'); // Support POST with _method override
    Route::delete('/{report}', [ReportController::class, 'destroy'])->middleware('permission:reports.delete')->name('destroy');
    Route::get('/{report}/download', [ReportController::class, 'download'])->middleware('permission:reports.view')->name('download');
});

// Complaint routes
Route::prefix('complaints')->name('complaints.')->middleware(['auth', 'verified'])->group(function () {
    Route::put('/{complaint}/respond', [ReportController::class, 'respondToComplaint'])->middleware('permission:reports.edit')->name('respond');
});