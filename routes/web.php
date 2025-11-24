<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\StudentsVerifiyController;
use App\Http\Controllers\WebController;
use Illuminate\Support\Facades\Route;
use Telegram\Bot\Laravel\Facades\Telegram;

Route::get('login', [LoginController::class, 'login'])->name('login');
Route::post('checkAuth', [LoginController::class, 'checkAuth'])->name('checkAuth');

Route::middleware('auth')->group(function () {
   Route::get('/', [WebController::class, 'index']);
   Route::resource('web', WebController::class);
   Route::get('student/{id}', [AdminController::class, 'student'])->name('student');
   Route::post('newPassword/{id)', [AdminController::class, 'newPassword'])->name('newPassword');
   Route::resource('admin', AdminController::class);
   Route::get('setwebhook', function () {
      $response = Telegram::setWebhook(['url' => 'https://dd29ee0c1c9b.ngrok-free.app/api/telegram/webhook']);
   });
});
Route::post('logout', [LoginController::class, 'logout'])->name('logout');


Route::get('verifiy/login', [StudentsVerifiyController::class, 'login'])->name('verifiy.login');
Route::get('verifiy/forget', [StudentsVerifiyController::class, 'forget'])->name('verifiy.forget');
Route::post('verifiy/chekLogin', [StudentsVerifiyController::class, 'chekLogin'])->name('verifiy.chekLogin');
Route::post('verifiy/logout', [StudentsVerifiyController::class, 'logout'])->name('verifiy.logout');
Route::post('verifiy/sendMessage', [StudentsVerifiyController::class, 'sendMessage'])->name('verifiy.sendMessage');
Route::post('download', [StudentsVerifiyController::class, 'download'])->name('download');
Route::post('verifiy/update/{id}', [StudentsVerifiyController::class, 'update'])->name('verifiy.update');
Route::post('verifiy/newPassword/{id}', [StudentsVerifiyController::class, 'newPassword'])->name('verifiy.newPassword');
Route::get('verifiy/index', [StudentsVerifiyController::class, 'index'])->name('verifiy.index');
