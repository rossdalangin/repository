<?php

namespace FreelanceFlowPro\Models;

/**
 * Sample Content Engine
 */
class SampleContent {

	public static function get_templates() {
		return [
			'freelance_contract' => [
				'title'       => 'Professional Freelance Service Agreement',
				'description' => 'A comprehensive legal contract covering scope, intellectual property, and payment terms.',
				'content'     => '<h1>Independent Contractor Agreement</h1>
								 <p>This Agreement is made as of {{effective_date}} between <strong>{{business_name}}</strong> ("Contractor") and <strong>{{client_name}}</strong> ("Client").</p>
								 <h2>1. Services</h2>
								 <p>The Contractor agrees to perform the following services: {{project_scope}}</p>
								 <h2>2. Compensation</h2>
								 <p>The Client shall pay the Contractor: {{payment_terms}}. Total Project Value: {{currency}}{{total_amount}}.</p>
								 <h2>3. Milestones</h2>
								 {{milestones}}
								 <h2>4. Intellectual Property</h2>
								 <p>Upon full payment, all work product produced under this agreement shall be the property of the Client.</p>
								 <h2>5. Termination</h2>
								 <p>Either party may terminate this agreement with {{termination_notice}} days written notice.</p>
								 <br><br>
								 <p>Signed: ____________________ (Contractor) &nbsp;&nbsp;&nbsp; Signed: ____________________ (Client)</p>',
				'fields'      => [
					'effective_date' => [ 'label' => 'Effective Date', 'sample' => date('Y-m-d'), 'note' => 'Format: YYYY-MM-DD (e.g. 2023-10-30)' ],
					'client_name'    => [ 'label' => 'Client Legal Name', 'sample' => 'Acme Global Inc.', 'note' => 'Full legal name of the entity paying for services.' ],
					'project_scope'  => [ 'label' => 'Project Scope', 'type' => 'textarea', 'sample' => 'Full-stack development of a React dashboard including API integration and deployment.', 'note' => 'Detailed description of the work deliverables.' ],
					'currency'       => [ 'label' => 'Currency Symbol', 'sample' => '$', 'note' => 'e.g. $, €, £' ],
					'total_amount'   => [ 'label' => 'Total Amount', 'sample' => '5,000', 'note' => 'Numeric value of the total project cost.' ],
					'payment_terms'  => [ 'label' => 'Payment Terms', 'type' => 'select', 'options' => [ '50_upfront' => '50% Upfront, 50% on Completion', 'net_15' => 'Net 15 Days', 'hourly' => 'Weekly Billing' ], 'sample' => '50_upfront' ],
					'milestones'     => [ 'label' => 'Project Milestones', 'type' => 'repeater', 'note' => 'Add key phases of the project.' ],
					'termination_notice' => [ 'label' => 'Notice Period (Days)', 'sample' => '30', 'note' => 'Number of days required for termination.' ],
				]
			],
			'va_proposal' => [
				'title'       => 'High-Converting VA Proposal',
				'description' => 'A structured proposal designed to showcase value and secure new virtual assistant clients.',
				'content'     => '<h1>Proposal for Administrative Support</h1>
								 <p>Prepared for: {{client_name}}</p>
								 <p><strong>Introduction:</strong> {{intro_text}}</p>
								 <h2>Proposed Services</h2>
								 {{services}}
								 <h2>Your Investment</h2>
								 <p>Monthly Retainer: {{currency}}{{retainer_amount}} for {{hours}} hours/month.</p>
								 <p><strong>Why choose us?</strong> {{value_prop}}</p>',
				'fields'      => [
					'client_name'     => [ 'label' => 'Prospective Client', 'sample' => 'Sarah Johnson, CEO', 'note' => 'Name and title of the decision maker.' ],
					'intro_text'      => [ 'label' => 'Cover Letter/Intro', 'type' => 'textarea', 'sample' => 'I am thrilled to submit this proposal to streamline your inbox and calendar management...', 'note' => 'Briefly state your understanding of their needs.' ],
					'services'        => [ 'label' => 'Service List', 'type' => 'repeater', 'note' => 'e.g. Email Management, Travel Booking, Research.' ],
					'currency'        => [ 'label' => 'Currency', 'sample' => 'USD', 'note' => 'Currency code or symbol.' ],
					'retainer_amount' => [ 'label' => 'Monthly Investment', 'sample' => '1,200', 'note' => 'Amount for the monthly retainer.' ],
					'hours'           => [ 'label' => 'Allocated Hours', 'sample' => '20', 'note' => 'Hours included in this tier.' ],
					'value_prop'      => [ 'label' => 'Value Proposition', 'type' => 'textarea', 'sample' => 'We save our clients an average of 15 hours a week, allowing them to focus on revenue growth.', 'note' => 'What makes you different?' ],
				]
			],
			'status_report' => [
				'title'       => 'Weekly Executive Status Report',
				'description' => 'A professional update report to keep clients informed and show continuous value.',
				'content'     => '<h1>Weekly Progress Report</h1>
								 <p><strong>Period:</strong> {{start_date}} to {{end_date}}</p>
								 <h2>Completed Tasks</h2>
								 {{tasks_completed}}
								 <h2>Work in Progress</h2>
								 {{tasks_pending}}
								 <h2>Key Insights</h2>
								 <p>{{insights}}</p>
								 <p>Next Meeting: {{next_meeting}}</p>',
				'fields'      => [
					'start_date'      => [ 'label' => 'Start Date', 'sample' => '2023-10-23', 'note' => 'Beginning of the reporting period.' ],
					'end_date'        => [ 'label' => 'End Date', 'sample' => '2023-10-29', 'note' => 'End of the reporting period.' ],
					'tasks_completed' => [ 'label' => 'Done This Week', 'type' => 'repeater', 'note' => 'List your wins for the client.' ],
					'tasks_pending'   => [ 'label' => 'Next Week Priority', 'type' => 'repeater', 'note' => 'Focus items for the coming period.' ],
					'insights'        => [ 'label' => 'Insights/Results', 'type' => 'textarea', 'sample' => 'Conversion rates increased by 5% following the landing page optimization.', 'note' => 'High-level value statement.' ],
					'next_meeting'    => [ 'label' => 'Next Check-in', 'sample' => 'Monday at 10 AM EST', 'note' => 'Confirmation of the next sync.' ],
				]
			]
		];
	}
}
