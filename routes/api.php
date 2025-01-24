<?php

use App\Http\Controllers\StripeSubscriptionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('stripe/subscription',  [StripeSubscriptionController::class, 'store'])->name("stripe.subscription.store");


Route::post('stripe/subscription/complete',  [StripeSubscriptionController::class, 'complete'])->name("stripe.subscription.complete");

