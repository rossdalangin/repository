<?php

namespace FreelanceFlowPro\Services;

/**
 * One-Click Document Generator
 */
class DocumentService {

	public function export_pdf( $html, $filename = 'document.pdf' ) {
		// Ensure Dompdf is loaded via composer autoloader
		if ( ! class_exists( '\Dompdf\Dompdf' ) ) {
			return false;
		}

		$dompdf = new \Dompdf\Dompdf();
		$dompdf->loadHtml( $html );
		$dompdf->setPaper( 'A4', 'portrait' );

		// Set options for better compatibility (Crucial for XAMPP/Local)
		$options = $dompdf->getOptions();
		$options->set( 'isRemoteEnabled', true );
		$options->set( 'defaultFont', 'DejaVu Sans' );
		$options->set( 'chroot', FFP_PATH );
		$options->set( 'tempDir', sys_get_temp_dir() );

		// XAMPP SSL Fix for remote images
		$context = stream_context_create([
			'ssl' => [
				'verify_peer' => false,
				'verify_peer_name' => false,
				'allow_self_signed' => true
			]
		]);
		$dompdf->setHttpContext($context);

		$dompdf->setOptions( $options );

		try {
			$dompdf->render();
		} catch (\Exception $e) {
			if (defined('WP_DEBUG') && WP_DEBUG) {
				error_log('Dompdf Render Error: ' . $e->getMessage());
			}
		}

		// Clear any previous output buffers to prevent corruption
		if ( ob_get_length() ) {
			ob_end_clean();
		}

		// Output the generated PDF to Browser
		$dompdf->stream( $filename, [ 'Attachment' => 1 ] );
		exit;
	}

	public function export_docx( $content, $filename = 'document.docx' ) {
		// Basic DOCX generation logic (often requires PHPWord, but we'll provide a placeholder or basic header)
		header( "Content-type: application/vnd.ms-word" );
		header( "Content-Disposition: attachment;Filename=" . $filename );
		echo "<html>";
		echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">";
		echo "<body>";
		echo $content;
		echo "</body>";
		echo "</html>";
		exit;
	}

	public function parse_template( $template_body, $placeholders ) {
		$plugin = \FreelanceFlowPro\Core\Plugin::instance();
		$engine = $plugin->get( 'template_engine' );

		// Inject Branding Data
		$user_id = get_current_user_id();
		$user_plan = get_user_meta( $user_id, 'ffp_user_plan', true ) ?: 'free';
		$is_admin = current_user_can( 'manage_options' );

		$placeholders['business_name'] = get_option( 'ffp_business_name', 'FreelanceFlow User' );

		// Hide logo for free users unless admin
		if ( $user_plan === 'free' && ! $is_admin ) {
			$placeholders['business_logo'] = '';
		} else {
			$placeholders['business_logo'] = get_option( 'ffp_logo_url', '' );
			$meta_logo = get_user_meta( $user_id, 'ffp_logo_url', true );
			if ( $meta_logo ) $placeholders['business_logo'] = $meta_logo;
		}

		// Specific branding for Agency/Pro if set in meta (optional override)
		$meta_name = get_user_meta( $user_id, 'ffp_business_name', true );
		if ( $meta_name ) $placeholders['business_name'] = $meta_name;

		$parsed = $engine->parse_template_content( $template_body, $placeholders );

		/**
		 * Hook: ffp_before_generate
		 */
		do_action( 'ffp_before_generate', $parsed, $placeholders );

		/**
		 * Filter: ffp_template_data
		 */
		return apply_filters( 'ffp_template_data', $parsed, $placeholders );
	}
}
