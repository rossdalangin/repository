<?php

namespace FreelanceFlowPro\Core;

use FreelanceFlowPro\Admin\Settings;
use FreelanceFlowPro\Services\DocumentService;
use FreelanceFlowPro\Services\FileVaultService;

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
		} );
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
