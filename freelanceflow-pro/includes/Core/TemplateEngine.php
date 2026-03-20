<?php
declare(strict_types=1);

namespace FreelanceFlowPro\Core;

/**
 * Dynamic Template Engine
 */
class TemplateEngine {

	public function get_template_fields( string $template_id ): array {
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

	public function render_field( array $field ): string {
		$output = '<div class="ffp-field-group">';
		$output .= sprintf( '<label>%s</label>', esc_html( $field['label'] ) );

		switch ( $field['type'] ) {
			case 'textarea':
				$output .= sprintf( '<textarea id="%s" class="ffp-field"></textarea>', esc_attr( $field['id'] ) );
				break;
			case 'repeater':
				$output .= $this->render_repeater( $field );
				break;
			case 'select':
				$output .= sprintf( '<select id="%s" class="ffp-field">', esc_attr( $field['id'] ) );
				if ( isset( $field['options'] ) ) {
					foreach ( $field['options'] as $val => $lbl ) {
						$output .= sprintf( '<option value="%s">%s</option>', esc_attr( $val ), esc_html( $lbl ) );
					}
				}
				$output .= '</select>';
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

	private function render_repeater( array $field ): string {
		$html = sprintf( '<div id="%s" class="ffp-repeater ffp-field" data-id="%s">', esc_attr( $field['id'] ), esc_attr( $field['id'] ) );
		$html .= '<div class="ffp-repeater-rows"></div>';
		$html .= sprintf( '<button type="button" class="button ffp-add-row" data-repeater="%s">Add Row</button>', esc_attr( $field['id'] ) );
		$html .= '</div>';
		return $html;
	}

	public function parse_template_content( string $content, array $data ): string {
		foreach ( $data as $key => $value ) {
			if ( is_array( $value ) ) {
				// Handle repeater parsing (basic list generation)
				$list = '<ul>';
				foreach ( $value as $item ) {
					$list .= '<li>' . esc_html( is_array( $item ) ? implode( ' - ', $item ) : $item ) . '</li>';
				}
				$list .= '</ul>';
				$content = str_replace( '{{' . $key . '}}', $list, $content );
			} else {
				$content = str_replace( '{{' . $key . '}}', $value, $content );
			}
		}
		return $content;
	}
}
