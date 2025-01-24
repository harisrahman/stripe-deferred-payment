<?php

namespace App\Http\Controllers\Services;

use Exception;
use Stripe\Customer;
use Stripe\Stripe;
use Stripe\StripeClient;
use Stripe\Product;
use Stripe\Subscription;

class StripeService
{
	public string $api_version = "2023-10-16";
	public StripeClient $client;
	public bool $is_in_live_mode;

	public function __construct(string $secret_key)
	{
		$isInProduction = config("app.environment") !== "local";

		Stripe::setVerifySslCerts($isInProduction);
		$this->client = new StripeClient([
			"api_key" => $secret_key,
			"stripe_version" => $this->api_version,
		]);
		$this->is_in_live_mode = $this->is_key_live($secret_key);
	}

	public function create_customer(array $data): Customer|false
	{
		try {
			/**
			 * If all data exists then customerData array looks like this 
			 * 
			 * [
			 * "name" => $data["name"],
			 * "source" => $data["stripe_source_token"],
			 * "email" => $data["email"],
			 * "phone" => $data['phone'],
			 * ]
			 */
			$customerData = array_merge(
				array_key_exists("name", $data) ? ["name" => $data["name"]] : [],
				array_key_exists("stripe_source_token", $data) ? ["source" => $data["stripe_source_token"]] : [],
				array_key_exists("email", $data) ? ["email" => $data["email"]] : [],
				array_key_exists("phone", $data) ? ["phone" => $data["phone"]] : [],
			);

			return $this->client->customers->create($customerData);
		} catch (Exception $e) {
			return false;
		}
	}

	public function create_product(string $name, array $extra_params = []): Product|false
	{
		try {
			return $this->client->products->create([
				'name' => $name,
				...$extra_params,
			]);
		} catch (Exception $e) {
			return false;
		}
	}

	public function get_product(string $id): Product|false
	{
		try {
			return $this->client->products->retrieve($id);
		} catch (Exception $e) {
			return false;
		}
	}

	// Subscribe a customer to a subscription price
	public function deferred_subscribe_customer(
		string $customer_id,
		string $price_id,
		string $description,
		?array $metadata = []
	): Subscription|false {
		try {
			return $this->client->subscriptions->create([
				"customer" => $customer_id,
				"description" => $description,
				"items" => [
					[
						"price" => $price_id
					],
				],
				"payment_behavior" => "default_incomplete",
				"proration_behavior" => "create_prorations",
				'off_session' => true,
				"payment_settings" => [
					// Stripe sets subscription.default_payment_method when
					// a subscription payment succeeds.
					// https://docs.stripe.com/api/subscriptions/create#create_subscription-payment_settings-save_default_payment_method
					"save_default_payment_method" => "on_subscription"
				],
				"expand" => ["latest_invoice.payment_intent", "pending_setup_intent"],
				"metadata" => $metadata,
			]);
		} catch (Exception $e) {
			return false;
		}
	}

	public function is_key_live(string $secret_key): bool
	{
		return strpos($secret_key, "sk_live") !== false;
	}
}
