<?php
/**
 * Plugin Name:       FreelanceFlow Pro
 * Plugin URI:        https://example.com/freelanceflow-pro
 * Description:       High-converting document automation + repository system for Virtual Assistants, Freelancers, and Agencies.
 * Version:           1.0.0
 * Author:            FreelanceFlow Team
 * Author URI:        https://example.com
 * License:           GPL-3.0-or-later
 * Text Domain:       freelanceflow-pro
 * Domain Path:       /languages
 * Requires PHP:      7.4
 * Requires at least: 5.8
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Core Constants
define( 'FFP_VERSION', '1.0.0' );
define( 'FFP_PATH', plugin_dir_path( __FILE__ ) );
define( 'FFP_URL', plugin_dir_url( __FILE__ ) );
define( 'FFP_INC', FFP_PATH . 'includes/' );
define( 'FFP_ASSETS', FFP_URL . 'assets/' );

// Load Composer Autoloader if available
if ( file_exists( FFP_PATH . 'vendor/autoload.php' ) ) {
	require_once FFP_PATH . 'vendor/autoload.php';
}

/**
 * PSR-4 Autoloader
 */
spl_autoload_register( function ( $class ) {
	$prefix = 'FreelanceFlowPro\\';
	$base_dir = FFP_INC;

	$len = strlen( $prefix );
	if ( strncmp( $prefix, $class, $len ) !== 0 ) {
		return;
	}

	$relative_class = substr( $class, $len );
	$file = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';

	if ( file_exists( $file ) ) {
		require $file;
	}
} );

/**
 * Initialize Plugin
 */
function ffp_initialize() {
	if ( ! class_exists( 'FreelanceFlowPro\Core\App' ) ) {
		return;
	}

	\FreelanceFlowPro\Core\App::instance()->run();
}

add_action( 'plugins_loaded', 'ffp_initialize' );

/**
 * Activation / Deactivation Hooks
 */
register_activation_hook( __FILE__, [ 'FreelanceFlowPro\Core\App', 'activate' ] );
register_deactivation_hook( __FILE__, [ 'FreelanceFlowPro\Core\App', 'deactivate' ] );
