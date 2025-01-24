<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Services\StripeService;
use App\Http\Requests\CompleteStripeSubscriptionRequest;
use App\Http\Requests\StoreStripeSubscriptionRequest;
use Carbon\Carbon;
use Illuminate\Support\Str;

class StripeSubscriptionController extends Controller
{
	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		return view('stripe-subscription');
	}

		/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \App\Http\Requests\StoreStripeSubscriptionRequest  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(StoreStripeSubscriptionRequest $request) {
		$data = $request->validated();

		$stripe = new StripeService(config('services.stripe.secret'));

		$customer  = $stripe->create_customer($data);

		if (!$customer) return response()->json([
			"message" => "Customer couldn't be created."
		], 400);

		$subscription = $stripe->deferred_subscribe_customer($customer->id, config('services.stripe.test.product_price_id'), "Test deferred subscription.");

		if (!$subscription) return response()->json([
			"message" => "Customer couldn't be subscribed."
		], 400);

		// For subscriptions that don't collect a payment up front (for example, subscriptions with a free trial period), use the client secret from the pending_setup_intent
		if ($subscription->pending_setup_intent !== NULL) {
			$type = 'setup';
			$client_secret = $subscription->pending_setup_intent->client_secret;
		} else {
			$type = 'payment';
			$client_secret = $subscription->latest_invoice->payment_intent->client_secret;
		}

		return response()->json([
			// this should be order id from DB
			'order_id' => Str::uuid()->toString(),
			'type' => $type,
			'client_secret' => $client_secret,
		]);
	}

	/**
	 * Compelete the subsciption order
	 *
	 * @param  \App\Http\Requests\CompleteStripeSubscriptionRequest  $request
	 * @return \Illuminate\Http\Response
	 */
	public function complete(CompleteStripeSubscriptionRequest $request) {
		$data = $request->validated();

		// Create user in db, save order and send  

		return response()->json([
			'order_id' 		=> $data['order_id'],
			'token'         => Str::random(32),
            'refresh_token' => Str::random(32),
            'expires_at'    => Carbon::now()->addMinutes(100)->toDateTimeString()
		]);
	}
}
