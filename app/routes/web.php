<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;
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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [TransactionController::class, 'index'])->name('home');
Route::get('/new', [TransactionController::class, 'new'])->name('transactionNew');
Route::get('/transaction/{id}', [TransactionController::class, 'show'])->name('transactionShow');
Route::post('/transaction/store', [TransactionController::class, 'store'])->name('transactionStore');
Route::post('/subscription/store', [TransactionController::class, 'subscriptionStore'])->name('subscriptionStore');
Route::post('/subscription/cancel', [TransactionController::class, 'subscriptionCancel'])->name('subscriptionCancel');
