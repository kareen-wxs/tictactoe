<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/game', [GameController::class, 'index'])->name('game');
Route::post('/api/telegram/send', [GameController::class, 'sendTelegramMessage'])->name('telegram.send');
Route::get('/api/promo/generate', [GameController::class, 'generatePromoCode'])->name('promo.generate');
