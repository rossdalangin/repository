<?php

namespace FreelanceFlowPro\Models;

/**
 * Sample Content Engine
 */
class SampleContent {

	public static function get_templates() {
		return [
			'freelance_contract' => [
				'title'       => 'Freelance Contract',
				'description' => 'Standard freelance contract for service providers.',
				'content'     => '<h1>Freelance Service Agreement</h1>
								 <p>This agreement is between {{client_name}} and the Freelancer.</p>
								 <p><strong>Project Scope:</strong> {{project_scope}}</p>
								 <p><strong>Payment Terms:</strong> {{payment_terms}}</p>',
				'fields'      => [
					'client_name'   => [ 'label' => 'Client Name', 'sample' => 'Stark Industries', 'note' => 'Legal Entity Name' ],
					'project_scope' => [ 'label' => 'Project Scope', 'sample' => 'Integration of Arc Reactor with WordPress.', 'note' => 'Brief scope description' ],
					'payment_terms' => [ 'label' => 'Payment Terms', 'sample' => '50% Upfront, 50% on completion.', 'note' => 'Terms of payment' ],
				]
			],
			'va_proposal' => [
				'title'       => 'VA Proposal',
				'description' => 'Proposal template for Virtual Assistant services.',
				'content'     => '<h1>Virtual Assistant Proposal</h1>
								 <p>Prepared for: {{client_name}}</p>
								 <p><strong>Estimated Hours:</strong> {{hours}} per month</p>
								 <p><strong>Services:</strong> {{services}}</p>',
				'fields'      => [
					'client_name' => [ 'label' => 'Client Name', 'sample' => 'Wayne Enterprises', 'note' => 'Client contact' ],
					'hours'       => [ 'label' => 'Estimated Hours', 'sample' => '40', 'note' => 'Project duration' ],
					'services'    => [ 'label' => 'Services Provided', 'sample' => 'Calendar management, Email handling, Travel booking.', 'note' => 'List of services' ],
				]
			],
			'status_report' => [
				'title'       => 'Weekly Status Report',
				'description' => 'Weekly report for client projects.',
				'content'     => '<h1>Weekly Status Report</h1>
								 <p><strong>Reporting Period:</strong> {{report_date}}</p>
								 <p><strong>Tasks Completed:</strong> {{tasks_done}}</p>
								 <p><strong>Next Steps:</strong> {{next_steps}}</p>',
				'fields'      => [
					'report_date' => [ 'label' => 'Reporting Date', 'sample' => 'October 27, 2023', 'note' => 'Week of report' ],
					'tasks_done'  => [ 'label' => 'Completed Tasks', 'sample' => 'Implemented new payment gateway, Fixed 5 bugs.', 'note' => 'Work completed' ],
					'next_steps'  => [ 'label' => 'Upcoming Tasks', 'sample' => 'Finalize documentation, Start QA phase.', 'note' => 'Planned work' ],
				]
			]
		];
	}
}
