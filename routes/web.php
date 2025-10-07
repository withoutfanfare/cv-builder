<?php

use App\Http\Controllers\CvPdfController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/cv/{cv}/pdf/{profile?}', [CvPdfController::class, 'download'])->name('cv.pdf');
