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

		// Set options for better compatibility
		$options = $dompdf->getOptions();
		$options->set( 'isRemoteEnabled', true );
		$options->set( 'defaultFont', 'Arial' );
		$dompdf->setOptions( $options );

		$dompdf->render();

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
		$placeholders['business_name'] = get_option( 'ffp_business_name', 'FreelanceFlow User' );
		$placeholders['business_logo'] = get_option( 'ffp_logo_url', '' );

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
