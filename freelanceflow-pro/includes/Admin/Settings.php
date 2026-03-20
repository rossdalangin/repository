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
		add_action( 'admin_init', [ $this, 'handle_admin_actions' ] );
		add_action( 'admin_init', [ $this, 'handle_create_subuser' ] );
		add_action( 'admin_post_ffp_external_upgrade', [ $this, 'handle_external_upgrade' ] );
		add_action( 'admin_post_nopriv_ffp_external_upgrade', [ $this, 'handle_external_upgrade' ] );

		// AJAX Handlers
		add_action( 'wp_ajax_ffp_get_template_fields', [ $this, 'ajax_get_template_fields' ] );
		add_action( 'wp_ajax_ffp_generate_document', [ $this, 'ajax_generate_document' ] );
		add_action( 'wp_ajax_ffp_save_to_vault', [ $this, 'ajax_save_to_vault' ] );
		add_action( 'wp_ajax_ffp_update_file_meta', [ $this, 'ajax_update_file_meta' ] );
		add_action( 'wp_ajax_ffp_update_file_visibility', [ $this, 'ajax_update_file_visibility' ] );
	}

	public function register_settings() {
		register_setting( 'ffp_payment_settings', 'ffp_stripe_secret_key' );
		register_setting( 'ffp_payment_settings', 'ffp_stripe_webhook_secret' );
		register_setting( 'ffp_payment_settings', 'ffp_paypal_client_id' );
		register_setting( 'ffp_payment_settings', 'ffp_paypal_client_secret' );
		register_setting( 'ffp_payment_settings', 'ffp_free_limit' );
	}

	public function handle_profile_save() {
		if ( ! isset( $_POST['ffp_profile_save_nonce'] ) || ! wp_verify_nonce( $_POST['ffp_profile_save_nonce'], 'ffp_profile_save' ) ) {
			return;
		}

		if ( ! is_user_logged_in() ) {
			return;
		}

		$user_id_current = get_current_user_id();
		$user_plan = get_user_meta( $user_id_current, 'ffp_user_plan', true ) ?: 'free';

		// Multi-tenant branding: Save to user meta
		update_user_meta( $user_id_current, 'ffp_business_name', sanitize_text_field( $_POST['ffp_business_name'] ) );
		update_user_meta( $user_id_current, 'ffp_logo_url', esc_url_raw( $_POST['ffp_logo_url'] ) );

		// Admin-only global limit
		if ( current_user_can( 'manage_options' ) ) {
			update_option( 'ffp_free_limit', (int) $_POST['ffp_free_limit'] );
		}

		// Agency specific payment keys
		if ( $user_plan === 'agency' ) {
			update_user_meta( $user_id_current, 'ffp_agency_stripe_key', sanitize_text_field( $_POST['ffp_agency_stripe_key'] ) );
			update_user_meta( $user_id_current, 'ffp_agency_stripe_secret', sanitize_text_field( $_POST['ffp_agency_stripe_secret'] ) );
			update_user_meta( $user_id_current, 'ffp_agency_paypal_client_id', sanitize_text_field( $_POST['ffp_agency_paypal_client_id'] ) );
			update_user_meta( $user_id_current, 'ffp_agency_paypal_client_secret', sanitize_text_field( $_POST['ffp_agency_paypal_client_secret'] ) );
		}

		add_settings_error( 'ffp_messages', 'ffp_message', 'Profile and Settings saved successfully.', 'updated' );
	}

	public function handle_admin_actions() {
		if ( ! isset( $_GET['ffp_action'] ) ) {
			return;
		}

		check_admin_referer( 'ffp_admin_action', 'ffp_nonce' );

		$user_id_current = get_current_user_id();
		$current_plan = get_user_meta( $user_id_current, 'ffp_user_plan', true ) ?: 'free';
		$is_admin = current_user_can( 'manage_options' );
		$is_agency = ( $current_plan === 'agency' );

		if ( ! $is_admin && ! $is_agency ) {
			return;
		}

		if ( isset( $_GET['user_id'] ) ) {
			$user_id = absint( $_GET['user_id'] );
			$action  = sanitize_text_field( $_GET['ffp_action'] );

			// Multi-tenancy check for Agency
			if ( $is_agency && ! $is_admin ) {
				$parent_agency = (int) get_user_meta( $user_id, 'ffp_parent_agency', true );
				if ( $parent_agency !== $user_id_current ) {
					wp_die( 'Forbidden: You can only manage your own sub-users.' );
				}
			}

			if ( $action === 'remove_access' ) {
				update_user_meta( $user_id, 'ffp_user_plan', 'free' );
				add_settings_error( 'ffp_messages', 'ffp_msg', 'User access removed.', 'updated' );
			} elseif ( $action === 'set_pro' ) {
				update_user_meta( $user_id, 'ffp_user_plan', 'pro' );
				add_settings_error( 'ffp_messages', 'ffp_msg', 'User plan set to Pro.', 'updated' );
			} elseif ( $action === 'set_agency' && $is_admin ) {
				update_user_meta( $user_id, 'ffp_user_plan', 'agency' );
				add_settings_error( 'ffp_messages', 'ffp_msg', 'User plan set to Agency.', 'updated' );
			} elseif ( $action === 'delete_file' && isset( $_GET['file_id'] ) ) {
				$file_id = absint( $_GET['file_id'] );

				// IDOR check: Verify owner
				$attachment = get_post( $file_id );
				$is_owner = (int) $attachment->post_author === $user_id_current;
				$parent_of_author = (int) get_user_meta( (int)$attachment->post_author, 'ffp_parent_agency', true );
				$is_parent = ( $is_agency && $parent_of_author === $user_id_current );

				if ( $is_admin || $is_owner || $is_parent ) {
					wp_delete_attachment( $file_id, true );
					add_settings_error( 'ffp_messages', 'ffp_msg', 'File deleted successfully.', 'updated' );
				} else {
					wp_die( 'Forbidden: You do not have permission to delete this file.' );
				}
			}
		}
	}

	public function handle_create_subuser() {
		if ( ! isset( $_POST['ffp_subuser_nonce'] ) || ! wp_verify_nonce( $_POST['ffp_subuser_nonce'], 'ffp_create_subuser' ) ) {
			return;
		}

		$user_id_current = get_current_user_id();
		$current_plan = get_user_meta( $user_id_current, 'ffp_user_plan', true ) ?: 'free';

		if ( $current_plan !== 'agency' && ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$email = sanitize_email( $_POST['ffp_subuser_email'] );
		$username = sanitize_user( $_POST['ffp_subuser_username'] );
		$plan = sanitize_text_field( $_POST['ffp_subuser_plan'] );

		// Security: Agencies cannot create other Agencies or Admins
		if ( ! current_user_can( 'manage_options' ) && $plan === 'agency' ) {
			$plan = 'pro';
		}

		if ( ! email_exists( $email ) && ! username_exists( $username ) ) {
			$password = wp_generate_password();
			$subuser_id = wp_create_user( $username, $password, $email );

			if ( ! is_wp_error( $subuser_id ) ) {
				update_user_meta( $subuser_id, 'ffp_user_plan', $plan );
				update_user_meta( $subuser_id, 'ffp_parent_agency', $user_id_current );
				add_settings_error( 'ffp_messages', 'ffp_msg', 'Sub-user created successfully.', 'updated' );
			}
		} else {
			add_settings_error( 'ffp_messages', 'ffp_msg', 'User already exists.', 'error' );
		}
	}

	public function handle_external_upgrade() {
		$agency_id = isset( $_POST['agency_id'] ) ? absint( $_POST['agency_id'] ) : 0;
		$plan_id   = sanitize_text_field( $_POST['plan_id'] );
		$gateway   = sanitize_text_field( $_POST['gateway'] ?? 'stripe' );
		$user_id   = get_current_user_id();

		// Handle Free Plan immediately for logged in users
		if ( $user_id && $plan_id === 'free' ) {
			update_user_meta( $user_id, 'ffp_user_plan', 'free' );
			wp_redirect( admin_url('admin.php?page=ffp-dashboard') );
			exit;
		}

		$plugin = \FreelanceFlowPro\Core\Plugin::instance();

		// Determine which keys to use
		$stripe_key = '';
		if ( $agency_id > 0 ) {
			$stripe_key = get_user_meta( $agency_id, 'ffp_agency_stripe_key', true );
		}

		if ( empty($stripe_key) ) {
			$stripe_key = get_option( 'ffp_stripe_secret_key' );
		}

		if ( $gateway === 'stripe' ) {
			$stripe = new \FreelanceFlowPro\Services\StripeService( $stripe_key );

			$meta = [ 'plan_id' => $plan_id, 'agency_id' => $agency_id ];
			if ( $user_id ) $meta['user_id'] = $user_id;
			else $meta['is_guest'] = '1';

			$session = $stripe->create_checkout_session( $plan_id, $user_id ?: 0, $meta );
			if ( $session && isset( $session->url ) ) {
				wp_redirect( $session->url );
				exit;
			}
		} else {
			// PayPal Logic
			$paypal_id = $agency_id > 0 ? get_user_meta($agency_id, 'ffp_agency_paypal_client_id', true) : get_option('ffp_paypal_client_id');
			$paypal = new \FreelanceFlowPro\Services\PayPalService( $paypal_id );
			$order = $paypal->create_checkout_session( $plan_id, $user_id ?: 0 );
			if ( isset($order['approve_url']) ) {
				wp_redirect( $order['approve_url'] );
				exit;
			}
		}

		wp_die( 'Payment initialization failed.' );
	}

	public function handle_subscription_upgrade() {
		if ( ! isset( $_POST['ffp_upgrade_nonce'] ) || ! wp_verify_nonce( $_POST['ffp_upgrade_nonce'], 'ffp_upgrade' ) ) {
			return;
		}

		$plan_id = sanitize_text_field( $_POST['ffp_plan_id'] );
		$user_id = get_current_user_id();
		$plugin  = \FreelanceFlowPro\Core\Plugin::instance();

		// Check if user has a parent agency and if agency is active
		$parent_agency_id = (int) get_user_meta( $user_id, 'ffp_parent_agency', true );
		$stripe_key = '';
		if ( $parent_agency_id > 0 ) {
			$parent_plan = get_user_meta( $parent_agency_id, 'ffp_user_plan', true );
			if ( $parent_plan === 'agency' ) {
				$stripe_key = get_user_meta( $parent_agency_id, 'ffp_agency_stripe_key', true );
			}
		}

		if ( empty($stripe_key) ) {
			$stripe_key = get_option( 'ffp_stripe_secret_key' );
		}

		if ( empty($stripe_key) ) {
			wp_die( 'Stripe service not configured.' );
		}

		$stripe = new \FreelanceFlowPro\Services\StripeService( $stripe_key );

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
			'read', // Allow any logged in user to see the menu
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

		wp_enqueue_media();
		wp_enqueue_style( 'ffp-admin-css', FFP_ASSETS . 'css/admin.css', [], FFP_VERSION );
		wp_enqueue_script( 'ffp-admin-js', FFP_ASSETS . 'js/admin.js', [ 'jquery' ], FFP_VERSION, true );

		wp_localize_script( 'ffp-admin-js', 'ffpData', [
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'ffp_nonce' )
		] );
	}

	public function render_dashboard() {
		settings_errors( 'ffp_messages' );
		$user_id_current = get_current_user_id();
		$user_plan = get_user_meta( $user_id_current, 'ffp_user_plan', true ) ?: 'free';
		$is_admin = current_user_can( 'manage_options' );
		$is_agency = ( $user_plan === 'agency' );

		$tabs = [
			'profile'      => 'Profile & Branding',
			'generator'    => 'Template Generator',
			'vault'        => 'File Vault',
			'subscription' => 'Subscription & Payments',
		];

		if ( $is_admin || $is_agency ) {
			$tabs['users'] = 'User Management';
		}

		if ( $is_admin ) {
			$tabs['payments'] = 'Transaction Logs';
		}

		$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'profile';
		$user_id_current = get_current_user_id();
		$user_plan = get_user_meta( $user_id_current, 'ffp_user_plan', true ) ?: 'free';
		$is_admin = current_user_can( 'manage_options' );
		?>
		<div class="wrap ffp-admin-wrap">
			<?php if ( $is_admin || $is_agency ) : ?>
			<div class="ffp-card" style="border-left: 5px solid #4f46e5; margin-bottom: 30px;">
				<h3>🚀 Developer Reference: Embeds</h3>
				<?php if ( $is_admin ) : ?>
					<p><strong>Shortcodes:</strong> (WordPress Only)</p>
					<code>[ffp_pricing]</code><br>
					<code>[ffp_file_list category="legal"]</code><br><br>
				<?php endif; ?>

				<p><strong>HTML Embed Codes:</strong> (For any website)</p>
				<p>Pricing Grid:</p>
				<textarea readonly style="width:100%; height:60px; font-family:monospace; background:#f8fafc; font-size:11px;"><?php echo esc_textarea( \FreelanceFlowPro\Core\Plugin::instance()->get('shortcodes')->get_pricing_html( $user_id_current ) ); ?></textarea>
			</div>
			<?php endif; ?>

			<div class="ffp-header" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 30px; background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
				<div class="ffp-branding">
					<h1 style="margin:0; background: linear-gradient(90deg, #4f46e5, #06b6d4); -webkit-background-clip: text; -webkit-text-fill-color: transparent; font-weight: 800; font-size: 28px;">FreelanceFlow <span style="font-weight: 300;">Pro</span></h1>
					<p style="margin:0; color: #718096; font-size: 13px;">Enterprise-grade Document Automation</p>
				</div>
				<div class="ffp-status-badge" style="background: #eef2ff; color: #4f46e5; padding: 6px 15px; border-radius: 20px; font-weight: 700; font-size: 11px; border: 1px solid #c7d2fe;">
					SYSTEM OPERATIONAL
				</div>
			</div>

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
		$is_admin = current_user_can( 'manage_options' );
		$user_plan = get_user_meta( get_current_user_id(), 'ffp_user_plan', true ) ?: 'free';
		$is_agency = ( $user_plan === 'agency' );

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
			case 'users':
				if ( $is_admin || $is_agency ) {
					$this->render_users_tab();
				} else {
					echo '<div class="notice notice-error"><p>Unauthorized access.</p></div>';
				}
				break;
			case 'payments':
				if ( $is_admin ) {
					$this->render_payments_tab();
				} else {
					echo '<div class="notice notice-error"><p>Unauthorized access.</p></div>';
				}
				break;
			default:
				$this->render_profile_tab();
				break;
		}
	}

	private function render_profile_tab() {
		$user_id_current = get_current_user_id();
		$user_plan = get_user_meta( $user_id_current, 'ffp_user_plan', true ) ?: 'free';
		$is_admin = current_user_can( 'manage_options' );
		?>
		<div class="ffp-card">
			<h3>User Profile & Branding</h3>
			<div class="ffp-help-text">
				Welcome to your Profile Command Center. Here you can configure your professional identity which is automatically injected into every document you generate.
				<strong>Pro and Agency</strong> users can upload a custom logo for premium branding.
			</div>
			<form method="post" action="">
				<?php wp_nonce_field( 'ffp_profile_save', 'ffp_profile_save_nonce' ); ?>
				<table class="form-table">
					<tr>
						<th>Business Name</th>
						<td><input type="text" name="ffp_business_name" value="<?php echo esc_attr( get_user_meta( $user_id_current, 'ffp_business_name', true ) ?: get_option('ffp_business_name') ); ?>" class="regular-text" /></td>
					</tr>
					<tr>
						<th>Logo URL</th>
						<td><input type="text" name="ffp_logo_url" value="<?php echo esc_attr( get_user_meta( $user_id_current, 'ffp_logo_url', true ) ?: get_option('ffp_logo_url') ); ?>" class="regular-text" /></td>
					</tr>
					<?php if ( $user_plan === 'agency' ) : ?>
					<tr>
						<th>Stripe Secret Key (Agency)</th>
						<td><input type="password" name="ffp_agency_stripe_key" value="<?php echo esc_attr( get_user_meta( $user_id_current, 'ffp_agency_stripe_key', true ) ); ?>" class="regular-text" /></td>
					</tr>
					<tr>
						<th>Stripe Webhook Secret (Agency)</th>
						<td><input type="password" name="ffp_agency_stripe_secret" value="<?php echo esc_attr( get_user_meta( $user_id_current, 'ffp_agency_stripe_secret', true ) ); ?>" class="regular-text" /></td>
					</tr>
					<tr>
						<th>PayPal Client ID (Agency)</th>
						<td><input type="text" name="ffp_agency_paypal_client_id" value="<?php echo esc_attr( get_user_meta( $user_id_current, 'ffp_agency_paypal_client_id', true ) ); ?>" class="regular-text" /></td>
					</tr>
					<tr>
						<th>PayPal Client Secret (Agency)</th>
						<td><input type="password" name="ffp_agency_paypal_client_secret" value="<?php echo esc_attr( get_user_meta( $user_id_current, 'ffp_agency_paypal_client_secret', true ) ); ?>" class="regular-text" /></td>
					</tr>
					<?php endif; ?>
				</table>
				<?php if ( $is_admin ) : ?>
					<table class="form-table">
						<tr>
							<th scope="row">Free User Document Limit</th>
							<td><input type="number" name="ffp_free_limit" value="<?php echo esc_attr( get_option( 'ffp_free_limit', 3 ) ); ?>" class="small-text"></td>
						</tr>
					</table>
				<?php endif; ?>
				<input type="submit" class="button button-primary" value="Save Settings" />
			</form>
		</div>
		<?php
	}

	private function render_users_tab() {
		$users = get_users();
		?>
		<div class="ffp-card">
			<h3>Create New Sub-User</h3>
			<div class="ffp-help-text">
				<strong>Agency Feature:</strong> Build your team by creating sub-users. You can assign them to Free or Pro tiers.
				All documents created by your sub-users will be visible in your Agency management dashboard.
			</div>
			<form method="POST" action="">
				<?php wp_nonce_field( 'ffp_create_subuser', 'ffp_subuser_nonce' ); ?>
				<table class="form-table">
					<tr>
						<th>Username</th>
						<td><input type="text" name="ffp_subuser_username" required class="regular-text"></td>
					</tr>
					<tr>
						<th>Email</th>
						<td><input type="email" name="ffp_subuser_email" required class="regular-text"></td>
					</tr>
					<tr>
						<th>Plan</th>
						<td>
							<select name="ffp_subuser_plan">
								<option value="free">Free</option>
								<option value="pro">Pro</option>
								<?php if ( current_user_can( 'manage_options' ) ) : ?>
									<option value="agency">Agency</option>
								<?php endif; ?>
							</select>
						</td>
					</tr>
				</table>
				<input type="submit" class="button button-primary" value="Create Sub-User">
			</form>
		</div>

		<div class="ffp-card">
			<h3>Manage Existing Users</h3>
			<table class="wp-list-table widefat fixed striped">
				<thead>
					<tr>
						<th>User</th>
						<th>Plan</th>
						<th>Access Rights & Capabilities</th>
						<th>Docs/mo</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$user_id_current = get_current_user_id();
					$is_admin = current_user_can( 'manage_options' );

					foreach ( $users as $user ) :
						$plan = get_user_meta( $user->ID, 'ffp_user_plan', true ) ?: 'free';
						$count = get_user_meta( $user->ID, 'ffp_doc_count_' . date('Ym'), true ) ?: 0;

						// Agency filtering
						if ( ! $is_admin ) {
							$parent = (int) get_user_meta( $user->ID, 'ffp_parent_agency', true );
							if ( $parent !== $user_id_current ) continue;
						}

						$rights = '';
						switch($plan) {
							case 'free': $rights = 'Can generate up to 3 docs/mo. Access to basic templates.'; break;
							case 'pro': $rights = 'Unlimited documents. Access to all templates & PDF/DOCX.'; break;
							case 'agency': $rights = 'Manage all files/users created by them. Manage sub-users.'; break;
						}
						?>
						<tr>
							<td><?php echo esc_html( $user->display_name ); ?> (<?php echo esc_html( $user->user_email ); ?>)</td>
							<td><strong><?php echo strtoupper( $plan ); ?></strong></td>
							<td><small><?php echo esc_html( $rights ); ?></small></td>
							<td><?php echo $count; ?></td>
							<td>
								<?php
								$base_url = "?page=ffp-dashboard&tab=users&user_id={$user->ID}";
								$pro_url = wp_nonce_url( $base_url . "&ffp_action=set_pro", 'ffp_admin_action', 'ffp_nonce' );
								$free_url = wp_nonce_url( $base_url . "&ffp_action=remove_access", 'ffp_admin_action', 'ffp_nonce' );
								?>
								<a href="<?php echo esc_url( $pro_url ); ?>" class="button button-small">Set Pro</a>
								<?php if ( $is_admin ) :
									$agency_url = wp_nonce_url( $base_url . "&ffp_action=set_agency", 'ffp_admin_action', 'ffp_nonce' );
									?>
									<a href="<?php echo esc_url( $agency_url ); ?>" class="button button-small">Set Agency</a>
								<?php endif; ?>
								<a href="<?php echo esc_url( $free_url ); ?>" class="button button-small">Set Free</a>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php
	}

	private function render_payments_tab() {
		$users = get_users([ 'meta_key' => 'ffp_user_plan', 'meta_compare' => '!=', 'meta_value' => 'free' ]);
		?>
		<div class="ffp-card">
			<h3>Active Premium Subscriptions</h3>
			<div class="ffp-help-text">
				<strong>Admin Overview:</strong> Track all active paid subscriptions across your platform. This log provides a real-time snapshot of your SaaS revenue and user tiers.
			</div>
			<table class="wp-list-table widefat fixed striped">
				<thead>
					<tr>
						<th>Activation Date</th>
						<th>Subscriber</th>
						<th>Plan Tier</th>
						<th>Status</th>
						<th>Gateway</th>
					</tr>
				</thead>
				<tbody>
					<?php if ( empty($users) ) : ?>
						<tr><td colspan="5">No active premium subscriptions found.</td></tr>
					<?php else :
						foreach ( $users as $user ) :
							$plan = get_user_meta( $user->ID, 'ffp_user_plan', true );
							?>
							<tr>
								<td><?php echo date('Y-m-d H:i'); ?></td>
								<td><?php echo esc_html( $user->display_name ); ?></td>
								<td><strong><?php echo strtoupper($plan); ?></strong></td>
								<td><span style="color:green;">ACTIVE</span></td>
								<td>Stripe (Checkout)</td>
							</tr>
						<?php endforeach;
					endif; ?>
				</tbody>
			</table>
		</div>
		<?php
	}

	private function render_generator_tab() {
		$templates = SampleContent::get_templates();
		?>
		<div class="ffp-card">
			<h3>Document Template Generator</h3>
			<div class="ffp-help-text">
				Generate professional business documents in seconds. Select a template from the sidebar, fill in the dynamic fields, and click generate.
				<strong>Tip:</strong> Use the "Load Sample" buttons to see high-converting examples for each field.
			</div>
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
		$user_id_current = get_current_user_id();
		$current_plan = get_user_meta( $user_id_current, 'ffp_user_plan', true ) ?: 'free';
		?>
		<div class="ffp-card">
			<h3>File Vault (Secure Repository)</h3>
			<div class="ffp-help-text">
				Securely store and manage your legal and identity documents. Files are protected via signed URLs to prevent unauthorized access.
				<strong>Visibility:</strong> Private (You only), Public (All users), Premium (Pro tiers), or Agency (Your team).
			</div>
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
							$user_id_current = get_current_user_id();
							$vault_service = \FreelanceFlowPro\Core\Plugin::instance()->get( 'file_vault' );
							$query_args = $vault_service->get_access_query_args( $user_id_current );
							$attachments = get_posts( $query_args );

							if ( empty( $attachments ) ) : ?>
								<tr><td colspan="4">No documents found in vault.</td></tr>
							<?php else :
								$vault_service = \FreelanceFlowPro\Core\Plugin::instance()->get( 'file_vault' );
								foreach ( $attachments as $attachment ) :
									$fid = $attachment->ID;
									$secure_url = $vault_service->get_secure_url( $fid );
									?>
									<tr>
										<td><?php echo esc_html( get_the_title( $fid ) ); ?></td>
										<td>
											<select class="ffp-change-cat" data-id="<?php echo $fid; ?>">
												<option value="">Uncategorized</option>
												<option value="legal" <?php selected(get_post_meta($fid, 'ffp_vault_category', true), 'legal'); ?>>Legal</option>
												<option value="id" <?php selected(get_post_meta($fid, 'ffp_vault_category', true), 'id'); ?>>Identity</option>
												<option value="agency" <?php selected(get_post_meta($fid, 'ffp_vault_category', true), 'agency'); ?>>Agency</option>
											</select>
											<select class="ffp-change-visibility" data-id="<?php echo $fid; ?>">
												<option value="admin" <?php selected(get_post_meta($fid, 'ffp_vault_visibility', true), 'admin'); ?>>Private (Me Only)</option>
												<option value="all" <?php selected(get_post_meta($fid, 'ffp_vault_visibility', true), 'all'); ?>>Public (Free+)</option>
												<option value="pro" <?php selected(get_post_meta($fid, 'ffp_vault_visibility', true), 'pro'); ?>>Premium (Pro+)</option>
												<option value="agency" <?php selected(get_post_meta($fid, 'ffp_vault_visibility', true), 'agency'); ?>>Agency Only</option>
											</select>
										</td>
										<td>
											<a href="<?php echo esc_url( $secure_url ); ?>" class="button button-small">Download</a>
											<?php if ( current_user_can( 'manage_options' ) || $current_plan === 'agency' ) :
												$del_url = wp_nonce_url( "?page=ffp-dashboard&tab=vault&ffp_action=delete_file&user_id=$user_id_current&file_id=$fid", 'ffp_admin_action', 'ffp_nonce' );
												?>
												<a href="<?php echo esc_url( $del_url ); ?>" class="button button-small" onclick="return confirm('Delete this file?');">Delete</a>
											<?php endif; ?>
										</td>
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
			<div class="ffp-help-text">
				Choose the plan that fits your business scale. Upgrade to unlock unlimited documents, custom branding, and team management tools.
				Payments are processed securely via Stripe or PayPal.
			</div>
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
					<form method="POST" action="">
						<?php wp_nonce_field( 'ffp_upgrade', 'ffp_upgrade_nonce' ); ?>
						<input type="hidden" name="ffp_plan_id" value="price_Agency123">
						<button type="submit" class="button-primary" <?php disabled($current_plan, 'agency'); ?>>Upgrade to Agency</button>
					</form>
				</div>
			</div>

			<hr style="margin: 40px 0;">

			<?php if ( current_user_can( 'manage_options' ) ) : ?>
			<hr style="margin: 40px 0;">
			<h3>Global Payment Gateway Configuration</h3>
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
					<tr>
						<th scope="row">PayPal Client Secret</th>
						<td><input type="password" name="ffp_paypal_client_secret" value="<?php echo esc_attr( get_option( 'ffp_paypal_client_secret' ) ); ?>" class="regular-text"></td>
					</tr>
				</table>
				<?php submit_button( 'Save Global Gateway Settings' ); ?>
			</form>
			<?php endif; ?>
		</div>
		<?php
	}

	public function ajax_get_template_fields() {
		check_ajax_referer( 'ffp_nonce', 'nonce' );

		if ( ! is_user_logged_in() ) {
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

		if ( ! is_user_logged_in() ) {
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
		$free_limit   = (int) get_option( 'ffp_free_limit', 3 );

		if ( ! current_user_can( 'manage_options' ) && $current_plan === 'free' && $doc_count >= $free_limit ) {
			wp_die( sprintf( 'Free plan limit reached (%d documents/mo). Please upgrade to Pro.', $free_limit ) );
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

	public function ajax_update_file_meta() {
		check_ajax_referer( 'ffp_nonce', 'nonce' );
		if ( ! is_user_logged_in() ) wp_send_json_error();

		$file_id = absint( $_POST['file_id'] );

		// IDOR check
		$attachment = get_post($file_id);
		if ( ! current_user_can('manage_options') && (int)$attachment->post_author !== get_current_user_id() ) {
			wp_send_json_error(['message' => 'Forbidden']);
		}

		$cat = sanitize_text_field( $_POST['category'] );

		update_post_meta( $file_id, 'ffp_vault_category', $cat );
		wp_send_json_success();
	}

	public function ajax_update_file_visibility() {
		check_ajax_referer( 'ffp_nonce', 'nonce' );
		if ( ! is_user_logged_in() ) wp_send_json_error();

		$file_id = absint( $_POST['file_id'] );

		// IDOR check
		$attachment = get_post($file_id);
		if ( ! current_user_can('manage_options') && (int)$attachment->post_author !== get_current_user_id() ) {
			wp_send_json_error(['message' => 'Forbidden']);
		}

		$visibility = sanitize_text_field( $_POST['visibility'] );

		update_post_meta( $file_id, 'ffp_vault_visibility', $visibility );
		wp_send_json_success();
	}

	public function ajax_save_to_vault() {
		check_ajax_referer( 'ffp_nonce', 'nonce' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( [ 'message' => 'Forbidden' ] );
		}

		$attachment_id = absint( $_POST['attachment_id'] );
		$user_id = get_current_user_id();

		// Security: Check if the user is the one who actually uploaded this file (WP default behavior for new uploads)
		// Or if user has admin rights.
		$attachment = get_post( $attachment_id );
		if ( ! current_user_can('manage_options') && (int)$attachment->post_author !== $user_id ) {
			wp_send_json_error( [ 'message' => 'You cannot add this file to your vault.' ] );
		}

		// Update author and add helper meta for query filtering
		$is_admin = current_user_can('manage_options') ? '1' : '0';
		$parent_id = (int) get_user_meta($user_id, 'ffp_parent_agency', true);

		wp_update_post( [ 'ID' => $attachment_id, 'post_author' => $user_id ] );
		update_post_meta( $attachment_id, '_ffp_is_admin_file', $is_admin );
		update_post_meta( $attachment_id, '_ffp_author_parent', $parent_id );

		wp_send_json_success( [ 'message' => 'File added to vault.' ] );
	}
}
