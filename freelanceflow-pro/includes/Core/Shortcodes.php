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
		$plans  = $plugin->get_plans();
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
				.ffp-pricing-grid-public { display: flex; gap: 20px; text-align: center; font-family: sans-serif; }
				.ffp-price-card { border: 1px solid #ddd; padding: 30px 20px; border-radius: 12px; flex: 1; transition: all 0.3s; background: #fff; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
				.ffp-price-card:hover { transform: translateY(-5px); border-color: #4f46e5; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }
				.ffp-price-card h4 { margin: 0; color: #1e293b; font-size: 20px; }
				.ffp-price-card .price { font-size: 32px; font-weight: 800; margin: 15px 0; color: #4f46e5; }
				.ffp-price-card .price span { font-size: 14px; color: #64748b; font-weight: 400; }
				.ffp-price-card ul { list-style: none; padding: 0; margin: 20px 0; text-align: left; font-size: 14px; color: #475569; }
				.ffp-price-card ul li { margin-bottom: 10px; display: flex; align-items: center; }
				.ffp-price-card ul li::before { content: "✓"; color: #10b981; font-weight: bold; margin-right: 10px; }
				.ffp-price-card button, .ffp-price-card .btn { width: 100%; background: #4f46e5; color: #fff; border: none; padding: 12px; border-radius: 8px; cursor: pointer; font-weight: 600; transition: background 0.2s; }
				.ffp-price-card button:hover { background: #4338ca; }
				.ffp-price-card button:disabled { background: #cbd5e1; cursor: not-allowed; }
			</style>

			<?php foreach ( $plans as $key => $plan ) :
				if ( $key === 'agency' && $agency_id !== 0 && ! user_can( $agency_id, 'manage_options' ) ) continue;
				?>
				<div class="ffp-price-card <?php echo ($key === $current_plan) ? 'active' : ''; ?>">
					<h4><?php echo esc_html( $plan['title'] ); ?></h4>
					<p class="price">$<?php echo esc_html( $plan['price'] ); ?><span>/mo</span></p>
					<ul>
						<?php foreach ( $plan['features'] as $feature ) : ?>
							<li><?php echo esc_html( $feature ); ?></li>
						<?php endforeach; ?>
					</ul>

					<form method="POST" action="<?php echo admin_url('admin-post.php'); ?>">
						<input type="hidden" name="action" value="ffp_external_upgrade">
						<input type="hidden" name="agency_id" value="<?php echo (int) $agency_id; ?>">
						<input type="hidden" name="plan_id" value="<?php echo esc_attr( $key ); ?>">

						<?php if ( $key === 'free' ) : ?>
							<?php if ( $user_id ) : ?>
								<button type="button" disabled>Current Plan</button>
							<?php else : ?>
								<input type="hidden" name="is_registration" value="1">
								<button type="submit">Join Free & Register</button>
							<?php endif; ?>
						<?php else : ?>
							<?php if ( $any_valid ) : ?>
								<?php if ( $show_selector ) : ?>
									<select name="gateway" style="margin-bottom: 10px; width: 100%; padding: 8px; border-radius: 6px;">
										<option value="stripe">Pay with Card</option>
										<option value="paypal">Pay with PayPal</option>
									</select>
								<?php else : ?>
									<input type="hidden" name="gateway" value="<?php echo $stripe_valid ? 'stripe' : 'paypal'; ?>">
								<?php endif; ?>
								<button type="submit" <?php disabled($current_plan, $key); ?>>
									<?php echo ($key === $current_plan) ? 'Active Subscription' : $plan['button']; ?>
								</button>
							<?php else : ?>
								<p style="color:#ef4444; font-size:12px; margin-top:10px;">Payment setup incomplete.</p>
							<?php endif; ?>
						<?php endif; ?>
					</form>
				</div>
			<?php endforeach; ?>
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
