<?php

use App\Http\Controllers\GoHighLevelController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\OpportunityController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TransactionController;

Route::get('/', function () {
    return redirect()->route('filament.admin.auth.login');
});

Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('token',[GoHighLevelController::class,'token'])->name('token');
    Route::get('connect',[GoHighLevelController::class,'connect'])->name('connect');
    Route::get('renewToken',[GoHighLevelController::class,'renewToken'])->name('renewToken');
    Route::get('finish',[GoHighLevelController::class,'finish'])->name('finish');
    Route::get('authorization',[GoHighLevelController::class,'authorization'])->name('authorization');
    Route::get('getToken',[GoHighLevelController::class,'getToken'])->name('get.token');

    //Contacts
    Route::prefix('contacts')->group(function () {
        Route::get('insert',[ContactController::class,'insert'])->name('contact.insert');
    });

    //Opportunities
    Route::prefix('opportunities')->group(function () {
        Route::get('get',[OpportunityController::class,'get'])->name('opportunity.get');
    });

    //Payments
    Route::prefix('transactions')->group(function () {
        Route::get('update',[TransactionController::class,'update'])->name('transactions.update');
    });
});

Route::get('/clear-optimize', function () {
    if (auth()->check()) {
        Artisan::call('optimize:clear');
        return 'Optimize cleared successfully!';
    } else {
        return 'Unauthorized';
    }
})->middleware('auth');