<?php
declare(strict_types=1);

namespace FreelanceFlowPro\Core;

use FreelanceFlowPro\Admin\Settings;
use FreelanceFlowPro\Services\DocumentService;
use FreelanceFlowPro\Services\FileVaultService;
use FreelanceFlowPro\Services\StripeService;
use FreelanceFlowPro\Services\PayPalService;

/**
 * Main Initializer (Singleton)
 */
class App {

	private static $instance = null;

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function run() {
		$this->init_services();

		$vault = Plugin::instance()->get( 'file_vault' );
		add_action( 'init', [ $vault, 'handle_file_request' ] );

		if ( is_admin() ) {
			$this->init_admin();
		} else {
			$this->init_public();
		}

		$this->register_api();
	}

	private function init_services() {
		$plugin = Plugin::instance();

		// Register core services
		$plugin->set( 'template_engine', new TemplateEngine() );
		$plugin->set( 'document_service', new DocumentService() );
		$plugin->set( 'file_vault', new FileVaultService() );

		// Register payment services with configured keys
		$stripe_key = get_option( 'ffp_stripe_secret_key' );
		$stripe_webhook = get_option( 'ffp_stripe_webhook_secret' );
		$plugin->set( 'stripe', new StripeService( $stripe_key, $stripe_webhook ) );

		$paypal_id = get_option( 'ffp_paypal_client_id' );
		$plugin->set( 'paypal', new PayPalService( $paypal_id ) );
	}

	private function init_admin() {
		new Settings();
	}

	private function init_public() {
		// Public hooks/frontend logic
	}

	private function register_api() {
		add_action( 'rest_api_init', function () {
			$controller = new \FreelanceFlowPro\API\V1\TemplateController();
			$controller->register_routes();

			// Register Stripe Webhook Route
			register_rest_route( 'ffp/v1', '/webhooks/stripe', [
				'methods'             => 'POST',
				'callback'            => [ $this, 'handle_stripe_webhook' ],
				'permission_callback' => '__return_true',
			] );
		} );
	}

	public function handle_stripe_webhook( $request ) {
		$payload   = $request->get_body();
		$signature = $request->get_header( 'stripe-signature' );
		$stripe    = Plugin::instance()->get( 'stripe' );

		if ( $stripe && $stripe->handle_webhook( $payload, $signature ) ) {
			return new \WP_REST_Response( [ 'status' => 'success' ], 200 );
		}

		return new \WP_REST_Response( [ 'status' => 'failed' ], 400 );
	}

	public static function activate() {
		// Migration or setup tasks
		if ( ! get_option( 'ffp_version' ) ) {
			update_option( 'ffp_version', FFP_VERSION );
		}
	}

	public static function deactivate() {
		// Cleanup tasks
	}
}
