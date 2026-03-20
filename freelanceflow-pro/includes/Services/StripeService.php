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

	public function create_checkout_session( $plan_id, $user_id, $meta = [] ) {
		if ( ! class_exists( '\Stripe\Stripe' ) ) {
			return false;
		}

		\Stripe\Stripe::setApiKey( $this->api_key );

		$session_args = [
			'payment_method_types' => [ 'card' ],
			'mode'                 => 'subscription',
			'success_url'          => admin_url( 'admin.php?page=ffp-dashboard&status=success' ),
			'cancel_url'           => home_url( '/pricing?status=cancel' ),
			'metadata'             => array_merge( [ 'user_id' => $user_id ], $meta ),
		];

		if ( $plan_id !== 'free' ) {
			$session_args['line_items'] = [ [
				'price'    => $plan_id,
				'quantity' => 1,
			] ];
		}

		return \Stripe\Checkout\Session::create( $session_args );
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
				$user_id = (int) ($session->metadata->user_id ?? 0);
				$is_guest = $session->metadata->is_guest ?? '0';
				$price_id = $session->metadata->plan_id ?? '';
				$agency_id = (int) ($session->metadata->agency_id ?? 0);

				// Create user for guests
				if ( $is_guest === '1' && $user_id === 0 ) {
					$email = $session->customer_details->email;
					$username = strstr($email, '@', true) . '_' . rand(100, 999);

					if ( ! email_exists($email) ) {
						$user_id = wp_create_user( $username, wp_generate_password(), $email );
					} else {
						$user_id = get_user_by('email', $email)->ID;
					}
				}

				if ( $user_id > 0 ) {
					$plan = 'pro';
					if ( strpos( $price_id, 'agency' ) !== false || $price_id === 'price_agency' ) {
						$plan = 'agency';
					} elseif ( $price_id === 'free' ) {
						$plan = 'free';
					}

					update_user_meta( $user_id, 'ffp_user_plan', $plan );

					if ( $agency_id > 0 ) {
						update_user_meta( $user_id, 'ffp_parent_agency', $agency_id );
					}
				}
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
