<?php

namespace FreelanceFlowPro\Admin;

use FreelanceFlowPro\Models\SampleContent;

/**
 * Admin Settings & Dashboard UI
 */
class Settings {

	public function __construct() {
		add_action( 'admin_menu', [ $this, 'add_menu_pages' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
		add_action( 'admin_init', [ $this, 'register_settings' ] );
		add_action( 'admin_init', [ $this, 'handle_profile_save' ] );
		add_action( 'admin_init', [ $this, 'handle_subscription_upgrade' ] );

		// AJAX Handlers
		add_action( 'wp_ajax_ffp_get_template_fields', [ $this, 'ajax_get_template_fields' ] );
		add_action( 'wp_ajax_ffp_generate_document', [ $this, 'ajax_generate_document' ] );
		add_action( 'wp_ajax_ffp_save_to_vault', [ $this, 'ajax_save_to_vault' ] );
	}

	public function register_settings() {
		register_setting( 'ffp_payment_settings', 'ffp_stripe_secret_key' );
		register_setting( 'ffp_payment_settings', 'ffp_stripe_webhook_secret' );
		register_setting( 'ffp_payment_settings', 'ffp_paypal_client_id' );
	}

	public function handle_profile_save() {
		if ( ! isset( $_POST['ffp_profile_save_nonce'] ) || ! wp_verify_nonce( $_POST['ffp_profile_save_nonce'], 'ffp_profile_save' ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		update_option( 'ffp_business_name', sanitize_text_field( $_POST['ffp_business_name'] ) );
		update_option( 'ffp_logo_url', esc_url_raw( $_POST['ffp_logo_url'] ) );

		add_settings_error( 'ffp_messages', 'ffp_message', 'Branding saved successfully.', 'updated' );
	}

	public function handle_subscription_upgrade() {
		if ( ! isset( $_POST['ffp_upgrade_nonce'] ) || ! wp_verify_nonce( $_POST['ffp_upgrade_nonce'], 'ffp_upgrade' ) ) {
			return;
		}

		$plan_id = sanitize_text_field( $_POST['ffp_plan_id'] );
		$user_id = get_current_user_id();
		$plugin  = \FreelanceFlowPro\Core\Plugin::instance();
		$stripe  = $plugin->get( 'stripe' );

		if ( ! $stripe ) {
			wp_die( 'Stripe service not configured.' );
		}

		$session = $stripe->create_checkout_session( $plan_id, $user_id );
		if ( $session && isset( $session->url ) ) {
			wp_redirect( $session->url );
			exit;
		}

		wp_die( 'Failed to initiate Stripe session.' );
	}

	public function add_menu_pages() {
		add_menu_page(
			'FreelanceFlow Pro',
			'FreelanceFlow',
			'manage_options',
			'ffp-dashboard',
			[ $this, 'render_dashboard' ],
			'dashicons-media-document',
			30
		);
	}

	public function enqueue_assets( $hook ) {
		if ( 'toplevel_page_ffp-dashboard' !== $hook ) {
			return;
		}

		wp_enqueue_style( 'ffp-admin-css', FFP_ASSETS . 'css/admin.css', [], FFP_VERSION );
		wp_enqueue_script( 'ffp-admin-js', FFP_ASSETS . 'js/admin.js', [ 'jquery' ], FFP_VERSION, true );

		wp_localize_script( 'ffp-admin-js', 'ffpData', [
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'ffp_nonce' )
		] );
	}

	public function render_dashboard() {
		settings_errors( 'ffp_messages' );
		$tabs = [
			'profile'      => 'Profile & Branding',
			'generator'    => 'Template Generator',
			'vault'        => 'File Vault',
			'subscription' => 'Subscription & Payments',
		];

		$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'profile';
		?>
		<div class="wrap ffp-admin-wrap">
			<h1>FreelanceFlow Pro Dashboard</h1>

			<h2 class="nav-tab-wrapper">
				<?php foreach ( $tabs as $id => $label ) : ?>
					<a href="?page=ffp-dashboard&tab=<?php echo $id; ?>" class="nav-tab <?php echo $active_tab === $id ? 'nav-tab-active' : ''; ?>">
						<?php echo esc_html( $label ); ?>
					</a>
				<?php endforeach; ?>
			</h2>

			<div class="ffp-tab-content">
				<?php $this->render_tab_content( $active_tab ); ?>
			</div>
		</div>
		<?php
	}

	private function render_tab_content( $tab ) {
		switch ( $tab ) {
			case 'generator':
				$this->render_generator_tab();
				break;
			case 'subscription':
				$this->render_subscription_tab();
				break;
			case 'vault':
				$this->render_vault_tab();
				break;
			default:
				$this->render_profile_tab();
				break;
		}
	}

	private function render_profile_tab() {
		?>
		<div class="ffp-card">
			<h3>User Profile & Branding</h3>
			<form method="post" action="">
				<?php wp_nonce_field( 'ffp_profile_save', 'ffp_profile_save_nonce' ); ?>
				<table class="form-table">
					<tr>
						<th>Business Name</th>
						<td><input type="text" name="ffp_business_name" value="<?php echo esc_attr( get_option( 'ffp_business_name' ) ); ?>" class="regular-text" /></td>
					</tr>
					<tr>
						<th>Logo URL</th>
						<td><input type="text" name="ffp_logo_url" value="<?php echo esc_attr( get_option( 'ffp_logo_url' ) ); ?>" class="regular-text" /></td>
					</tr>
				</table>
				<input type="submit" class="button button-primary" value="Save Branding" />
			</form>
		</div>
		<?php
	}

	private function render_generator_tab() {
		$templates = SampleContent::get_templates();
		?>
		<div class="ffp-card">
			<h3>Document Template Generator</h3>
			<div class="ffp-generator-layout">
				<div class="ffp-sidebar">
					<h4>Select Template</h4>
					<ul>
						<?php foreach ( $templates as $id => $tpl ) : ?>
							<li><button class="ffp-tpl-select" data-id="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $tpl['title'] ); ?></button></li>
						<?php endforeach; ?>
					</ul>
				</div>
				<div class="ffp-main-form" id="ffp-generator-form">
					<p>Please select a template to begin.</p>
				</div>
			</div>
		</div>
		<?php
	}

	private function render_vault_tab() {
		?>
		<div class="ffp-card">
			<h3>File Vault (Secure Repository)</h3>
			<p>Securely store and manage your legal, identity, and portfolio documents.</p>
			<button class="button ffp-upload-file">Upload New Document</button>
			<table class="wp-list-table widefat fixed striped">
				<thead>
					<tr>
						<th>File Name</th>
						<th>Category</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
							<?php
							$user_id = get_current_user_id();
							$vault_ids = get_user_meta( $user_id, 'ffp_vault_files', true ) ?: [];
							if ( empty( $vault_ids ) ) : ?>
								<tr><td colspan="3">No documents found in vault.</td></tr>
							<?php else :
								$vault_service = \FreelanceFlowPro\Core\Plugin::instance()->get( 'file_vault' );
								foreach ( $vault_ids as $fid ) :
									$secure_url = $vault_service->get_secure_url( $fid );
									?>
									<tr>
										<td><?php echo esc_html( get_the_title( $fid ) ); ?></td>
										<td>Uncategorized</td>
										<td><a href="<?php echo esc_url( $secure_url ); ?>" class="button button-small">Download Securely</a></td>
									</tr>
								<?php endforeach;
							endif; ?>
				</tbody>
			</table>
		</div>
		<?php
	}

	private function render_subscription_tab() {
		$user_id = get_current_user_id();
		$current_plan = get_user_meta( $user_id, 'ffp_user_plan', true ) ?: 'free';
		?>
		<div class="ffp-card">
			<h3>Subscription Management</h3>
			<p>Your current plan: <strong><?php echo esc_html( strtoupper( $current_plan ) ); ?></strong></p>

			<div class="ffp-pricing-grid">
				<div class="ffp-price-card <?php echo $current_plan === 'free' ? 'active' : ''; ?>">
					<h4>Free</h4>
					<p>$0 /mo</p>
					<ul>
						<li>3 Documents / mo</li>
						<li>Basic Templates</li>
					</ul>
					<button disabled>Current Plan</button>
				</div>
				<div class="ffp-price-card <?php echo $current_plan === 'pro' ? 'active' : ''; ?>">
					<h4>Pro</h4>
					<p>$29 /mo</p>
					<ul>
						<li>Unlimited Documents</li>
						<li>Custom Branding</li>
						<li>PDF & DOCX Export</li>
					</ul>
					<form method="POST" action="">
						<?php wp_nonce_field( 'ffp_upgrade', 'ffp_upgrade_nonce' ); ?>
						<input type="hidden" name="ffp_plan_id" value="price_H5ggu9GWU123"> <!-- Example Stripe Price ID -->
						<button type="submit" class="button-primary" <?php disabled($current_plan, 'pro'); ?>>Upgrade to Pro</button>
					</form>
				</div>
				<div class="ffp-price-card <?php echo $current_plan === 'agency' ? 'active' : ''; ?>">
					<h4>Agency</h4>
					<p>$99 /mo</p>
					<ul>
						<li>Multi-user Access</li>
						<li>White-labeling</li>
						<li>Priority Support</li>
					</ul>
					<button class="button-primary">Upgrade to Agency</button>
				</div>
			</div>

			<hr style="margin: 40px 0;">

			<h3>Payment Gateway Configuration</h3>
			<form method="post" action="options.php">
				<?php settings_fields( 'ffp_payment_settings' ); ?>
				<?php do_settings_sections( 'ffp-dashboard-payments' ); ?>
				<table class="form-table">
					<tr>
						<th scope="row">Stripe Secret Key</th>
						<td><input type="password" name="ffp_stripe_secret_key" value="<?php echo esc_attr( get_option( 'ffp_stripe_secret_key' ) ); ?>" class="regular-text"></td>
					</tr>
					<tr>
						<th scope="row">Stripe Webhook Secret</th>
						<td><input type="password" name="ffp_stripe_webhook_secret" value="<?php echo esc_attr( get_option( 'ffp_stripe_webhook_secret' ) ); ?>" class="regular-text"></td>
					</tr>
					<tr>
						<th scope="row">PayPal Client ID</th>
						<td><input type="text" name="ffp_paypal_client_id" value="<?php echo esc_attr( get_option( 'ffp_paypal_client_id' ) ); ?>" class="regular-text"></td>
					</tr>
				</table>
				<?php submit_button( 'Save Gateway Settings' ); ?>
			</form>
		</div>
		<?php
	}

	public function ajax_get_template_fields() {
		check_ajax_referer( 'ffp_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'message' => 'Forbidden' ], 403 );
		}

		$template_id = sanitize_text_field( $_POST['template_id'] );
		$templates = SampleContent::get_templates();

		if ( ! isset( $templates[ $template_id ] ) ) {
			wp_send_json_error( [ 'message' => 'Template not found' ] );
		}

		$template = $templates[ $template_id ];
		$engine = \FreelanceFlowPro\Core\Plugin::instance()->get( 'template_engine' );

		$html = sprintf( '<h4>%s</h4>', esc_html( $template['title'] ) );
		$html .= sprintf( '<p>%s</p>', esc_html( $template['description'] ) );
		$html .= sprintf( '<input type="hidden" id="ffp-active-tpl" value="%s">', esc_attr( $template_id ) );

		foreach ( $template['fields'] as $id => $field_data ) {
			$field = array_merge( [ 'id' => $id, 'type' => 'text' ], $field_data );
			if ( isset( $field['note'] ) ) {
				$field['sample_note'] = $field['note'];
			}
			$html .= $engine->render_field( $field );
		}

		$html .= '<div class="ffp-actions" style="margin-top: 20px;">';
		$html .= '<button class="button button-primary ffp-generate-doc" data-format="pdf">Generate PDF</button> ';
		$html .= '<button class="button ffp-generate-doc" data-format="docx">Generate DOCX</button>';
		$html .= '</div>';

		wp_send_json_success( [ 'html' => $html ] );
	}

	public function ajax_generate_document() {
		$nonce = isset( $_REQUEST['nonce'] ) ? $_REQUEST['nonce'] : '';
		if ( ! wp_verify_nonce( $nonce, 'ffp_nonce' ) ) {
			wp_die( 'Security check failed' );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Forbidden' );
		}

		$template_id = sanitize_text_field( $_REQUEST['template_id'] );
		$format      = sanitize_text_field( $_REQUEST['format'] );
		$payload     = json_decode( stripslashes( $_REQUEST['payload'] ), true );

		$templates = SampleContent::get_templates();
		if ( ! isset( $templates[ $template_id ] ) ) {
			wp_die( 'Template not found' );
		}

		$template = $templates[ $template_id ];
		$plugin   = \FreelanceFlowPro\Core\Plugin::instance();

		// Enforce plan limits
		$user_id = get_current_user_id();
		$current_plan = get_user_meta( $user_id, 'ffp_user_plan', true ) ?: 'free';
		$doc_count    = (int) get_user_meta( $user_id, 'ffp_doc_count_' . date('Ym'), true );

		if ( $current_plan === 'free' && $doc_count >= 3 ) {
			wp_die( 'Free plan limit reached (3 documents/mo). Please upgrade to Pro.' );
		}

		$doc_service = $plugin->get( 'document_service' );
		$parsed_content = $doc_service->parse_template( $template['content'], $payload );

		// Increment count
		update_user_meta( $user_id, 'ffp_doc_count_' . date('Ym'), $doc_count + 1 );

		if ( $format === 'pdf' ) {
			$doc_service->export_pdf( $parsed_content, $template_id . '.pdf' );
		} else {
			$doc_service->export_docx( $parsed_content, $template_id . '.docx' );
		}
		exit;
	}

	public function ajax_save_to_vault() {
		check_ajax_referer( 'ffp_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'message' => 'Forbidden' ] );
		}

		$attachment_id = absint( $_POST['attachment_id'] );
		$user_id = get_current_user_id();

		// Store in user meta (simulated repository)
		$vault = get_user_meta( $user_id, 'ffp_vault_files', true ) ?: [];
		$vault[] = $attachment_id;
		update_user_meta( $user_id, 'ffp_vault_files', array_unique( $vault ) );

		wp_send_json_success( [ 'message' => 'File added to vault.' ] );
	}
}
