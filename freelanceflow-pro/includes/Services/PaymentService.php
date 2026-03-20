<?php

namespace FreelanceFlowPro\Services;

/**
 * Payment Service Interface (Abstraction)
 */
interface PaymentService {

	public function create_checkout_session( $plan_id, $user_id );

	public function handle_webhook( $payload, $signature );

	public function get_subscription_status( $user_id );

	public function cancel_subscription( $user_id );
}
