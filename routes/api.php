<?php

use App\Http\Controllers\Balance\IndexController;
use App\Http\Controllers\ResetController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/balance', IndexController::class)->name('balance.index');

Route::post('/event', TransactionController::class)->name('transaction');

Route::post('reset', ResetController::class)->name('reset');
