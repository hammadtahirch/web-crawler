<?php

use App\Http\Controllers\AvgReportController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [ReportController::class, 'create']);

Route::get('/delete_session_data', [ReportController::class, 'deleteDataAndSession']);

Route::resource('/report', ReportController::class, ['parameters' => [
    'destroy' => 'id',
]]);
Route::resource('/avg_report', AvgReportController::class, ['parameters' => [
    'destroy' => 'id',
]]);
