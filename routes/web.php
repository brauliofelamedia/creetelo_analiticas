<?php

use App\Http\Controllers\GoHighLevelController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('token',[GoHighLevelController::class,'token'])->name('token');
    Route::get('connect',[GoHighLevelController::class,'connect'])->name('connect');
    Route::get('finish',[GoHighLevelController::class,'finish'])->name('finish');
    Route::get('authorization',[GoHighLevelController::class,'authorization'])->name('authorization');
});

