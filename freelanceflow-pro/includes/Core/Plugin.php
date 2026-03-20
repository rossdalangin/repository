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
