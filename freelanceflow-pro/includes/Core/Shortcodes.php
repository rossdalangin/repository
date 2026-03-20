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

	public function pricing_grid() {
		ob_start();
		// We can reuse the logic from Settings or a dedicated template
		$plugin = Plugin::instance();
		$user_id = get_current_user_id();
		$current_plan = $user_id ? (get_user_meta( $user_id, 'ffp_user_plan', true ) ?: 'free') : 'free';
		?>
		<div class="ffp-pricing-grid-public">
			<!-- Pricing table HTML (simplified version for public) -->
			<style>
				.ffp-pricing-grid-public { display: flex; gap: 20px; text-align: center; }
				.ffp-price-card { border: 1px solid #ddd; padding: 20px; border-radius: 10px; flex: 1; }
			</style>
			<div class="ffp-price-card">
				<h4>Free</h4>
				<p>$0</p>
				<button disabled>Get Started</button>
			</div>
			<div class="ffp-price-card">
				<h4>Pro</h4>
				<p>$29</p>
				<a href="<?php echo admin_url('admin.php?page=ffp-dashboard&tab=subscription'); ?>" class="button">Upgrade</a>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	public function file_list( $atts ) {
		$a = shortcode_atts( [
			'category' => '',
			'tier'     => 'all'
		], $atts );

		$query_args = [
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => -1,
		];

		if ( ! empty( $a['category'] ) ) {
			// In a real system, we'd use a taxonomy. Here we'll use meta.
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
