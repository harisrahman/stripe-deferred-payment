<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\StripeSubscriptionController;
use Illuminate\Support\Facades\Route;

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

Route::get('/',  [StripeSubscriptionController::class, 'create'])->name("stripe.subscription.create");

Route::get('/payment-intent/confirm',  [StripeSubscriptionController::class, 'confirm_payment_intent'])->name("stripe.payment_intent.confirm");
