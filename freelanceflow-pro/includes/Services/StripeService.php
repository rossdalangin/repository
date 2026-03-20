<?php

namespace FreelanceFlowPro\Services;

/**
 * Stripe Payment Driver
 */
class StripeService implements PaymentService {

	private $api_key;
	private $webhook_secret;

	public function __construct( $api_key = '', $webhook_secret = '' ) {
		$this->api_key = $api_key;
		$this->webhook_secret = $webhook_secret;
	}

	public function create_checkout_session( $plan_id, $user_id ) {
		if ( ! class_exists( '\Stripe\Stripe' ) ) {
			return false;
		}

		\Stripe\Stripe::setApiKey( $this->api_key );

		$session = \Stripe\Checkout\Session::create( [
			'payment_method_types' => [ 'card' ],
			'line_items'           => [ [
				'price'    => $plan_id, // Stripe Price ID
				'quantity' => 1,
			] ],
			'mode'                 => 'subscription',
			'success_url'          => admin_url( 'admin.php?page=ffp-dashboard&tab=subscription&status=success' ),
			'cancel_url'           => admin_url( 'admin.php?page=ffp-dashboard&tab=subscription&status=cancel' ),
			'metadata'             => [
				'user_id' => $user_id,
			],
		] );

		return $session;
	}

	public function handle_webhook( $payload, $signature ) {
		if ( ! class_exists( '\Stripe\Stripe' ) ) {
			return false;
		}

		try {
			$event = \Stripe\Webhook::constructEvent(
				$payload, $signature, $this->webhook_secret
			);
		} catch ( \Exception $e ) {
			return false;
		}

		switch ( $event->type ) {
			case 'checkout.session.completed':
				$session = $event->data->object;
				$user_id = (int) $session->metadata->user_id;
				$price_id = $session->line_items->data[0]->price->id ?? '';

				// Map price IDs to plan types
				$plan = 'pro';
				if ( strpos( $price_id, 'agency' ) !== false ) {
					$plan = 'agency';
				}

				update_user_meta( $user_id, 'ffp_user_plan', $plan );
				break;
		}

		return true;
	}

	public function get_subscription_status( $user_id ) {
		return get_user_meta( $user_id, 'ffp_stripe_subscription_status', true );
	}

	public function cancel_subscription( $user_id ) {
		// Call Stripe API to cancel
		return true;
	}
}
