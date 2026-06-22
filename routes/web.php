<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('reports.index');
});

// Laravel 7 syntax for defining routes
Route::get('/reports', 'ReportController@index')->name('reports.index');
