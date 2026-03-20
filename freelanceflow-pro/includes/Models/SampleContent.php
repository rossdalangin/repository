<?php

namespace FreelanceFlowPro\Models;

/**
 * Sample Content Engine
 */
class SampleContent {

	public static function get_templates() {
		$branding_header = '<div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #eee; padding-bottom: 20px; margin-bottom: 30px;">
								<div>{{business_logo}}</div>
								<div style="text-align: right; font-weight: bold; font-size: 18px;">{{business_name}}</div>
							</div>';

		return [
			'freelance_contract' => [
				'title'       => 'Professional Freelance Service Agreement',
				'description' => 'A comprehensive legal contract covering scope, intellectual property, and payment terms.',
				'content'     => $branding_header . '<h1>Independent Contractor Agreement</h1>
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
				'content'     => $branding_header . '<h1>Proposal for Administrative Support</h1>
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
				'content'     => $branding_header . '<h1>Weekly Progress Report</h1>
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
			],
			'invoice' => [
				'title'       => 'Professional Service Invoice',
				'description' => 'Generate clean, itemized invoices for your clients.',
				'content'     => $branding_header . '<h1>Invoice #{{invoice_number}}</h1>
								 <p><strong>Bill To:</strong> {{client_name}}</p>
								 <p><strong>Date:</strong> {{invoice_date}} | <strong>Due:</strong> {{due_date}}</p>
								 <table style="width:100%; border-collapse: collapse; margin: 20px 0;">
									<thead><tr style="background:#f8fafc;"><th style="text-align:left; padding:10px;">Description</th><th style="text-align:right; padding:10px;">Amount</th></tr></thead>
									<tbody>{{line_items}}</tbody>
								 </table>
								 <h3 style="text-align:right;">Total Due: {{currency}}{{total_due}}</h3>
								 <p><strong>Payment Instructions:</strong> {{payment_method}}</p>',
				'fields'      => [
					'invoice_number' => [ 'label' => 'Invoice #', 'sample' => 'INV-2023-001' ],
					'client_name'    => [ 'label' => 'Client Details', 'type' => 'textarea', 'sample' => "John Doe\n123 Street Ave\nNY, 10001" ],
					'invoice_date'   => [ 'label' => 'Invoice Date', 'sample' => date('Y-m-d') ],
					'due_date'       => [ 'label' => 'Due Date', 'sample' => date('Y-m-d', strtotime('+15 days')) ],
					'line_items'     => [ 'label' => 'Line Items', 'type' => 'repeater', 'note' => 'Format: Description - Price' ],
					'currency'       => [ 'label' => 'Currency', 'sample' => '$' ],
					'total_due'      => [ 'label' => 'Total Amount', 'sample' => '2,500.00' ],
					'payment_method' => [ 'label' => 'How to Pay', 'type' => 'textarea', 'sample' => 'Zelle: email@example.com or Wire Transfer.' ],
				]
			],
			'outreach' => [
				'title'       => 'Cold Outreach Message Generator',
				'description' => 'Create personalized outreach messages for LinkedIn or Email.',
				'content'     => '<h3>Subject: {{subject}}</h3>
								 <p>Hi {{contact_name}},</p>
								 <p>{{opening_hook}}</p>
								 <p>I noticed that {{company_name}} is {{pain_point_context}}. I help businesses like yours by {{your_solution}}.</p>
								 <p>Would you be open to a 10-minute chat next Tuesday?</p>
								 <p>Best,<br>{{business_name}}</p>',
				'fields'      => [
					'subject'            => [ 'label' => 'Subject Line', 'sample' => 'Streamlining {{company_name}} administrative workflow' ],
					'contact_name'       => [ 'label' => 'Contact Name', 'sample' => 'Michael' ],
					'opening_hook'       => [ 'label' => 'Opening Hook', 'sample' => 'I’ve been following your recent expansion into the SaaS market and was impressed by your latest feature release.' ],
					'company_name'       => [ 'label' => 'Target Company', 'sample' => 'TechSolutions' ],
					'pain_point_context' => [ 'label' => 'Pain Point', 'sample' => 'currently managing a high volume of client support tickets manually' ],
					'your_solution'      => [ 'label' => 'Your Solution', 'sample' => 'implementing automated Zendesk workflows and reducing response times by 40%' ],
				]
			],
			'sop' => [
				'title'       => 'Standard Operating Procedure (SOP)',
				'description' => 'Document your workflows for consistent delivery and team onboarding.',
				'content'     => $branding_header . '<h1>SOP: {{process_name}}</h1>
								 <p><strong>Objective:</strong> {{objective}}</p>
								 <h2>Prerequisites</h2>
								 {{tools_needed}}
								 <h2>Step-by-Step Instructions</h2>
								 {{steps}}
								 <h2>Verification</h2>
								 <p>{{verification_method}}</p>',
				'fields'      => [
					'process_name'        => [ 'label' => 'Process Title', 'sample' => 'Monthly Client Invoicing' ],
					'objective'           => [ 'label' => 'Objective', 'sample' => 'To ensure all clients are billed accurately and on time every month.' ],
					'tools_needed'        => [ 'label' => 'Tools Needed', 'type' => 'repeater', 'note' => 'List apps or logins required.' ],
					'steps'               => [ 'label' => 'Action Steps', 'type' => 'repeater', 'note' => 'Detailed sequential instructions.' ],
					'verification_method' => [ 'label' => 'Quality Check', 'sample' => 'Check the "Sent" folder and verify QuickBooks status is "Paid".' ],
				]
			],
			'case_study' => [
				'title'       => 'Client Success Case Study',
				'description' => 'A powerful sales tool showing how you solved a specific client problem.',
				'content'     => $branding_header . '<h1>Case Study: {{client_title}}</h1>
								 <h2>The Challenge</h2>
								 <p>{{challenge}}</p>
								 <h2>The Solution</h2>
								 <p>{{solution}}</p>
								 <h2>The Results</h2>
								 {{results_list}}
								 <p><strong>Quote:</strong> "{{client_quote}}"</p>',
				'fields'      => [
					'client_title' => [ 'label' => 'Client/Project Name', 'sample' => 'ScaleUp Agency Workflow Transformation' ],
					'challenge'    => [ 'label' => 'The Problem', 'type' => 'textarea', 'sample' => 'The client was losing 15 hours a week due to manual data entry across 3 different platforms.' ],
					'solution'     => [ 'label' => 'Your Approach', 'type' => 'textarea', 'sample' => 'We implemented a custom Zapier automation and a centralized Airtable database.' ],
					'results_list' => [ 'label' => 'Key Outcomes', 'type' => 'repeater', 'note' => 'e.g. 20% increase in profit, 40 hours saved.' ],
					'client_quote' => [ 'label' => 'Testimonial', 'type' => 'textarea', 'sample' => 'This changed our entire business operations. Highly recommended!' ],
				]
			],
			'sow' => [
				'title'       => 'Scope of Work (SOW)',
				'description' => 'Define exact deliverables and boundaries to prevent scope creep.',
				'content'     => $branding_header . '<h1>Scope of Work: {{project_name}}</h1>
								 <h2>Deliverables</h2>
								 {{deliverables}}
								 <h2>Timeline</h2>
								 <p>{{timeline}}</p>
								 <h2>Out of Scope</h2>
								 <p>{{exclusions}}</p>',
				'fields'      => [
					'project_name' => [ 'label' => 'Project Name', 'sample' => 'Website Redesign 2024' ],
					'deliverables' => [ 'label' => 'Deliverables', 'type' => 'repeater', 'note' => 'List specific items to be delivered.' ],
					'timeline'     => [ 'label' => 'Project Timeline', 'sample' => '8-week engagement starting Nov 1st.' ],
					'exclusions'   => [ 'label' => 'Exclusions', 'type' => 'textarea', 'sample' => 'Copywriting and custom photography are not included.' ],
				]
			],
			'roi_report' => [
				'title'       => 'ROI Impact Report',
				'description' => 'Show the financial impact of your work to justify premium rates.',
				'content'     => $branding_header . '<h1>ROI Analysis Report</h1>
								 <p><strong>Total Investment:</strong> {{currency}}{{investment}}</p>
								 <p><strong>Financial Gain/Savings:</strong> {{currency}}{{gains}}</p>
								 <h2 style="color:green;">Estimated ROI: {{roi_percentage}}%</h2>
								 <h3>Detailed Breakdown</h3>
								 {{breakdown}}',
				'fields'      => [
					'investment'     => [ 'label' => 'Client Investment', 'sample' => '2,000' ],
					'gains'          => [ 'label' => 'Value Created', 'sample' => '10,000' ],
					'roi_percentage' => [ 'label' => 'ROI %', 'sample' => '400' ],
					'breakdown'      => [ 'label' => 'Analysis', 'type' => 'repeater', 'note' => 'List where the savings/gains came from.' ],
					'currency'       => [ 'label' => 'Currency', 'sample' => '$' ],
				]
			],
			'retainer' => [
				'title'       => 'Retainer Agreement Addendum',
				'description' => 'A supplemental agreement for ongoing monthly support.',
				'content'     => $branding_header . '<h1>Retainer Agreement</h1>
								 <p>This addendum confirms that {{client_name}} has engaged {{business_name}} for ongoing services.</p>
								 <h2>Monthly Terms</h2>
								 <p><strong>Retainer Fee:</strong> {{currency}}{{monthly_fee}} per month.</p>
								 <p><strong>Included Hours:</strong> {{hours}} hours.</p>
								 <h2>Automatic Renewal</h2>
								 <p>This agreement shall renew every 30 days unless cancelled with {{cancel_notice}} days notice.</p>',
				'fields'      => [
					'client_name'   => [ 'label' => 'Client Name', 'sample' => 'Acme Corp' ],
					'monthly_fee'   => [ 'label' => 'Monthly Fee', 'sample' => '1,500' ],
					'hours'         => [ 'label' => 'Monthly Hours', 'sample' => '20' ],
					'cancel_notice' => [ 'label' => 'Notice Days', 'sample' => '15' ],
					'currency'      => [ 'label' => 'Currency', 'sample' => '$' ],
				]
			],
			'monthly_performance' => [
				'title'       => 'Monthly Performance Review',
				'description' => 'A high-level overview of monthly achievements and growth metrics.',
				'content'     => $branding_header . '<h1>Monthly Performance Review</h1>
								 <h2>Monthly KPI Summary</h2>
								 {{kpis}}
								 <h2>Key Projects Completed</h2>
								 {{projects}}
								 <h2>Strategic Recommendations</h2>
								 <p>{{recommendations}}</p>',
				'fields'      => [
					'kpis'            => [ 'label' => 'Key Metrics', 'type' => 'repeater', 'note' => 'e.g. Website Traffic: +15%' ],
					'projects'        => [ 'label' => 'Completed Work', 'type' => 'repeater' ],
					'recommendations' => [ 'label' => 'Future Strategy', 'type' => 'textarea', 'sample' => 'We suggest increasing LinkedIn ad spend by 10% next month.' ],
				]
			]
		];
	}
}
