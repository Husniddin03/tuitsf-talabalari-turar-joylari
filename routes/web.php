<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\WebController;
use Illuminate\Support\Facades\Route;
use Telegram\Bot\Laravel\Facades\Telegram;

Route::get('login', [LoginController::class, 'login'])->name('login');
Route::post('checkAuth', [LoginController::class, 'checkAuth'])->name('checkAuth');

// Route::middleware('auth')->group(function () {
Route::get('/', [WebController::class, 'index']);
Route::resource('web', WebController::class);
Route::resource('admin', AdminController::class);
Route::get('setwebhook', function () {
   $response = Telegram::setWebhook(['url' => 'https://1a64677b2bff.ngrok-free.app/api/telegram/webhook']);
});
// });
Route::post('logout', [LoginController::class, 'logout'])->name('logout');
