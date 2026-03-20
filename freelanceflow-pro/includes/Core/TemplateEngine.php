<?php

namespace FreelanceFlowPro\Core;

/**
 * Dynamic Template Engine
 */
class TemplateEngine {

	public function get_template_fields( $template_id ) {
		// Mocked JSON schema retrieval
		$templates = [
			'contract' => [
				[
					'id'          => 'client_name',
					'label'       => 'Client Name',
					'type'        => 'text',
					'sample_note' => 'Full legal name of the client',
					'sample'      => 'Acme Corp'
				],
				[
					'id'          => 'project_scope',
					'label'       => 'Project Scope',
					'type'        => 'textarea',
					'sample_note' => 'Detailed description of work',
					'sample'      => 'Web development and SEO optimization for 6 months.'
				]
			]
		];

		return isset( $templates[ $template_id ] ) ? $templates[ $template_id ] : [];
	}

	public function render_field( $field ) {
		$output = '<div class="ffp-field-group">';
		$output .= sprintf( '<label>%s</label>', esc_html( $field['label'] ) );

		switch ( $field['type'] ) {
			case 'textarea':
				$output .= sprintf( '<textarea id="%s" class="ffp-field"></textarea>', esc_attr( $field['id'] ) );
				break;
			default:
				$output .= sprintf( '<input type="text" id="%s" class="ffp-field" />', esc_attr( $field['id'] ) );
		}

		if ( ! empty( $field['sample_note'] ) ) {
			$output .= sprintf( '<p class="description">%s</p>', esc_html( $field['sample_note'] ) );
		}

		if ( ! empty( $field['sample'] ) ) {
			$output .= sprintf( '<button type="button" class="ffp-load-sample" data-field="%s" data-sample="%s">Load Sample</button>',
				esc_attr( $field['id'] ),
				esc_attr( $field['sample'] )
			);
		}

		$output .= '</div>';
		return $output;
	}

	public function parse_template_content( $content, $data ) {
		foreach ( $data as $key => $value ) {
			$content = str_replace( '{{' . $key . '}}', $value, $content );
		}
		return $content;
	}
}
