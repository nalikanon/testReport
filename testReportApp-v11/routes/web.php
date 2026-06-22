<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;

Route::get('/', function () {
    return redirect()->route('reports.index');
});

Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
Route::patch('/reports/{id}/status', [ReportController::class, 'updateStatus'])->name('reports.updateStatus');
