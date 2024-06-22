<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/orders/{order}/pay', [PaymentController::class, 'show'])->name('customer.orders.pay');
Route::post('/checkout/{order}',[PaymentController::class,'checkout'])->name('checkout');
Route::get('/success/pay/{order}', [PaymentController::class, 'success'])->name('success');
Route::get('/cancel/pay/{order}', [PaymentController::class, 'cancel'])->name('cancel');