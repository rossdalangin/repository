<?php
declare(strict_types=1);

namespace FreelanceFlowPro\Core;

/**
 * Shortcode Handler
 */
class Shortcodes {

	public function __construct() {
		add_shortcode( 'ffp_pricing', [ $this, 'pricing_grid' ] );
		add_shortcode( 'ffp_file_list', [ $this, 'file_list' ] );
	}

	public function get_pricing_html( $agency_id = 0 ) {
		ob_start();
		$plugin = Plugin::instance();
		$user_id = get_current_user_id();
		$current_plan = $user_id ? (get_user_meta( $user_id, 'ffp_user_plan', true ) ?: 'free') : 'none';

		// Gateway detection
		$stripe_valid = false;
		$paypal_valid = false;

		if ( $agency_id > 0 ) {
			$stripe_valid = ! empty( get_user_meta( $agency_id, 'ffp_agency_stripe_key', true ) );
			$paypal_valid = ! empty( get_user_meta( $agency_id, 'ffp_agency_paypal_client_id', true ) );
		} else {
			$stripe_valid = ! empty( get_option( 'ffp_stripe_secret_key' ) );
			$paypal_valid = ! empty( get_option( 'ffp_paypal_client_id' ) );
		}

		$show_selector = ( $stripe_valid && $paypal_valid );
		$any_valid = ( $stripe_valid || $paypal_valid );
		?>
		<div class="ffp-pricing-grid-public">
			<style>
				.ffp-pricing-grid-public { display: flex; gap: 20px; text-align: center; }
				.ffp-price-card { border: 1px solid #ddd; padding: 20px; border-radius: 10px; flex: 1; transition: transform 0.2s; }
				.ffp-price-card:hover { transform: translateY(-5px); border-color: #4f46e5; }
				.ffp-price-card h4 { margin: 10px 0; color: #4f46e5; }
				.ffp-price-card .price { font-size: 24px; font-weight: bold; margin-bottom: 20px; }
				.ffp-price-card button, .ffp-price-card .btn { background: #4f46e5; color: #fff; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; text-decoration: none; }
			</style>
			<div class="ffp-price-card">
				<h4>Free</h4>
				<p class="price">$0 /mo</p>
				<form method="POST" action="<?php echo admin_url('admin-post.php'); ?>">
					<input type="hidden" name="action" value="ffp_external_upgrade">
					<input type="hidden" name="agency_id" value="<?php echo (int) $agency_id; ?>">
					<input type="hidden" name="plan_id" value="free">
					<button type="submit" <?php disabled($current_plan, 'free'); ?>><?php echo $user_id ? 'Current Plan' : 'Join Free'; ?></button>
				</form>
			</div>
			<div class="ffp-price-card">
				<h4>Pro</h4>
				<p class="price">$29 /mo</p>
				<?php if ( $any_valid ) : ?>
					<form method="POST" action="<?php echo admin_url('admin-post.php'); ?>">
						<input type="hidden" name="action" value="ffp_external_upgrade">
						<input type="hidden" name="agency_id" value="<?php echo (int) $agency_id; ?>">
						<input type="hidden" name="plan_id" value="price_pro">
						<?php if ( $show_selector ) : ?>
							<select name="gateway" style="margin-bottom: 10px;">
								<option value="stripe">Card (Stripe)</option>
								<option value="paypal">PayPal</option>
							</select>
						<?php else : ?>
							<input type="hidden" name="gateway" value="<?php echo $stripe_valid ? 'stripe' : 'paypal'; ?>">
						<?php endif; ?>
						<button type="submit" <?php disabled($current_plan, 'pro'); ?>><?php echo $current_plan === 'pro' ? 'Active' : 'Get Pro'; ?></button>
					</form>
				<?php else: ?>
					<p style="color:red; font-size:11px;">Payment unavailable.</p>
				<?php endif; ?>
			</div>
			<div class="ffp-price-card">
				<h4>Agency</h4>
				<p class="price">$99 /mo</p>
				<?php if ( $any_valid ) : ?>
					<form method="POST" action="<?php echo admin_url('admin-post.php'); ?>">
						<input type="hidden" name="action" value="ffp_external_upgrade">
						<input type="hidden" name="agency_id" value="<?php echo (int) $agency_id; ?>">
						<input type="hidden" name="plan_id" value="price_agency">
						<?php if ( $show_selector ) : ?>
							<select name="gateway" style="margin-bottom: 10px;">
								<option value="stripe">Card (Stripe)</option>
								<option value="paypal">PayPal</option>
							</select>
						<?php else : ?>
							<input type="hidden" name="gateway" value="<?php echo $stripe_valid ? 'stripe' : 'paypal'; ?>">
						<?php endif; ?>
						<button type="submit" <?php disabled($current_plan, 'agency'); ?>><?php echo $current_plan === 'agency' ? 'Active' : 'Get Agency'; ?></button>
					</form>
				<?php else: ?>
					<p style="color:red; font-size:11px;">Payment unavailable.</p>
				<?php endif; ?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	public function pricing_grid() {
		return $this->get_pricing_html();
	}

	public function file_list( $atts ) {
		$a = shortcode_atts( [
			'category' => '',
		], $atts );

		$user_id = get_current_user_id();
		$vault_service = Plugin::instance()->get( 'file_vault' );

		$query_args = $vault_service->get_access_query_args( $user_id );

		if ( ! empty( $a['category'] ) ) {
			$query_args['meta_query'][] = [
				'key'   => 'ffp_vault_category',
				'value' => $a['category']
			];
		}

		$files = get_posts( $query_args );
		$user_id = get_current_user_id();
		$plugin = Plugin::instance();
		$vault_service = $plugin->get( 'file_vault' );

		ob_start();
		echo '<ul class="ffp-file-list">';
		foreach ( $files as $file ) {
			$file_tier = get_post_meta( $file->ID, 'ffp_vault_visibility', true ) ?: 'all';
			$has_access = $plugin->check_plan_access( $file_tier );

			echo '<li>';
			echo '<strong>' . esc_html( $file->post_title ) . '</strong><br>';
			echo '<em>' . esc_html( $file->post_content ) . '</em><br>';

			if ( $has_access ) {
				$secure_url = $vault_service->get_secure_url( $file->ID );
				echo '<a href="' . esc_url( $secure_url ) . '">Download Now</a>';
			} else {
				echo '<span style="color:red;">Premium Access Only. <a href="' . admin_url('admin.php?page=ffp-dashboard&tab=subscription') . '">Subscribe to unlock.</a></span>';
			}
			echo '</li>';
		}
		echo '</ul>';
		return ob_get_clean();
	}
}
