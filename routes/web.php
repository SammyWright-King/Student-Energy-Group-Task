<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MeterController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::controller(MeterController::class)->group(function() {
    Route::get('/', 'index')->name('home');
    Route::get('/{meter}', 'show')->name('meter.show');
    Route::get('/meter/new', 'new')->name('meter.new');
    Route::post('/meter/save', 'save')->name('meter.save');
    Route::get('/meter/{meter}/edit', 'edit')->name('meter.edit');
    Route::post('/meter/{meter}/update', 'update')->name('meter.update');
    Route::post('/meter/{meter}/reading/save/', 'saveReading')->name('meter.reading.save');
    Route::post('/meter/{meter}/reading/estimate', 'estimate')->name('estimate.reading');
    Route::post('/meter/{meter}/bulk-upload', 'bulkUpload')->name('bulk.upload');
});