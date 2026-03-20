<?php

namespace FreelanceFlowPro\API\V1;

use WP_REST_Controller;
use WP_REST_Response;
use FreelanceFlowPro\Models\SampleContent;

/**
 * REST API Controller for Templates
 */
class TemplateController extends WP_REST_Controller {

	protected $namespace = 'ffp/v1';
	protected $rest_base = 'templates';

	public function register_routes() {
		register_rest_route( $this->namespace, '/' . $this->rest_base, [
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_items' ],
				'permission_callback' => [ $this, 'get_items_permissions_check' ],
			],
		] );
	}

	public function get_items_permissions_check( $request ) {
		return current_user_can( 'manage_options' );
	}

	public function get_items( $request ) {
		$templates = SampleContent::get_templates();
		return new WP_REST_Response( $templates, 200 );
	}
}
