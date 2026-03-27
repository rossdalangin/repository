<?php
declare(strict_types=1);

namespace FreelanceFlowPro\Core;

/**
 * Service Container / Plugin Registry
 */
class Plugin {

	private static $instance = null;
	private $services = [];

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function set( string $id, $service ): void {
		$this->services[ $id ] = $service;
	}

	public function get( string $id ) {
		return $this->services[ $id ] ?? null;
	}

	public function get_plans(): array {
		$free_limit = (int) get_option( 'ffp_free_limit', 3 );
		return [
			'free'   => [
				'title'    => 'Free',
				'price'    => '0',
				'features' => [
					"$free_limit Documents / mo",
					"Basic Templates",
					"Standard Support"
				],
				'button'   => 'Join Free'
			],
			'pro'    => [
				'title'    => 'Pro',
				'price'    => '29',
				'features' => [
					"Unlimited Documents",
					"Custom Branding",
					"PDF & DOCX Export",
					"Priority Support"
				],
				'button'   => 'Get Pro',
				'price_id' => 'price_H5ggu9GWU123'
			],
			'agency' => [
				'title'    => 'Agency',
				'price'    => '99',
				'features' => [
					"Multi-user Access",
					"White-labeling",
					"Sovereign Payments",
					"24/7 VIP Support"
				],
				'button'   => 'Get Agency',
				'price_id' => 'price_Agency123'
			]
		];
	}

	/**
	 * Middleware-style function for access control
	 */
	public function check_plan_access( string $required_plan = 'pro' ): bool {
		$user_id = get_current_user_id();
		if ( ! $user_id ) {
			return false;
		}

		if ( user_can( $user_id, 'manage_options' ) ) {
			return true;
		}

		$user_plan = get_user_meta( $user_id, 'ffp_user_plan', true ) ?: 'free';

		// Agency has same access as Pro + extra management rights
		if ( $user_plan === 'agency' && in_array( $required_plan, [ 'free', 'pro' ] ) ) {
			return true;
		}

		$plans = [
			'free'   => 0,
			'pro'    => 1,
			'agency' => 2,
		];

		$user_level = isset( $plans[ $user_plan ] ) ? $plans[ $user_plan ] : 0;
		$required_level = isset( $plans[ $required_plan ] ) ? $plans[ $required_plan ] : 0;

		return $user_level >= $required_level;
	}
}
