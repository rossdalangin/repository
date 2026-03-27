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
					'effective_date' => [ 'label' => 'Effective Date', 'sample' => date('Y-m-d'), 'note' => 'Format: YYYY-MM-DD' ],
					'client_name'    => [ 'label' => 'Client Legal Name', 'sample' => 'Global Media Dynamics LLC', 'note' => 'Full legal name of the entity paying for services.' ],
					'project_scope'  => [ 'label' => 'Project Scope', 'type' => 'textarea', 'sample' => 'Development of a multi-tenant SaaS dashboard using React, Node.js, and PostgreSQL. Includes API integration with Stripe and AWS S3, responsive frontend implementation, and full CI/CD deployment pipeline setup.', 'note' => 'Detailed description of the work deliverables.' ],
					'currency'       => [ 'label' => 'Currency Symbol', 'sample' => 'USD ', 'note' => 'e.g. $, €, £' ],
					'total_amount'   => [ 'label' => 'Total Amount', 'sample' => '12,500.00', 'note' => 'Numeric value of the total project cost.' ],
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
					'client_name'     => [ 'label' => 'Prospective Client', 'sample' => 'Alexandra Hayes, CEO of Hayes Global', 'note' => 'Name and title of the decision maker.' ],
					'intro_text'      => [ 'label' => 'Cover Letter/Intro', 'type' => 'textarea', 'sample' => 'After reviewing your recent expansion into the European market, I am thrilled to submit this proposal to streamline your operations. My goal is to reclaim 15 hours of your week by managing high-level administrative bottlenecks.', 'note' => 'Briefly state your understanding of their needs.' ],
					'services'        => [ 'label' => 'Service List', 'type' => 'repeater', 'note' => 'e.g. Executive Inbox Management, Complex Travel Coordination, Vendor Liaison.' ],
					'currency'        => [ 'label' => 'Currency', 'sample' => '$', 'note' => 'Currency code or symbol.' ],
					'retainer_amount' => [ 'label' => 'Monthly Investment', 'sample' => '2,500.00', 'note' => 'Amount for the monthly retainer.' ],
					'hours'           => [ 'label' => 'Allocated Hours', 'sample' => '40', 'note' => 'Hours included in this tier.' ],
					'value_prop'      => [ 'label' => 'Value Proposition', 'type' => 'textarea', 'sample' => 'We specialize in executive support for 7-figure agency owners, focusing on radical efficiency and proactive problem solving rather than just reactive task management.', 'note' => 'What makes you different?' ],
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
					'start_date'      => [ 'label' => 'Start Date', 'sample' => date('Y-m-d', strtotime('last Monday')), 'note' => 'Beginning of the reporting period.' ],
					'end_date'        => [ 'label' => 'End Date', 'sample' => date('Y-m-d', strtotime('last Sunday')), 'note' => 'End of the reporting period.' ],
					'tasks_completed' => [ 'label' => 'Done This Week', 'type' => 'repeater', 'note' => 'List your wins for the client.' ],
					'tasks_pending'   => [ 'label' => 'Next Week Priority', 'type' => 'repeater', 'note' => 'Focus items for the coming period.' ],
					'insights'        => [ 'label' => 'Insights/Results', 'type' => 'textarea', 'sample' => 'The implementation of the automated lead-nurture sequence resulted in a 12% increase in discovery call bookings this week. Total time saved on manual outreach: 5.5 hours.', 'note' => 'High-level value statement.' ],
					'next_meeting'    => [ 'label' => 'Next Check-in', 'sample' => 'Wednesday at 2 PM PST via Zoom', 'note' => 'Confirmation of the next sync.' ],
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
					'invoice_number' => [ 'label' => 'Invoice #', 'sample' => 'INV-' . date('Y') . '-442' ],
					'client_name'    => [ 'label' => 'Client Details', 'type' => 'textarea', 'sample' => "Sterling Cooper Advertising\nAttn: Accounts Payable\n1271 Avenue of the Americas\nNew York, NY 10020" ],
					'invoice_date'   => [ 'label' => 'Invoice Date', 'sample' => date('Y-m-d') ],
					'due_date'       => [ 'label' => 'Due Date', 'sample' => date('Y-m-d', strtotime('+14 days')) ],
					'line_items'     => [ 'label' => 'Line Items', 'type' => 'repeater', 'note' => 'Format: Description - Price' ],
					'currency'       => [ 'label' => 'Currency', 'sample' => '$' ],
					'total_due'      => [ 'label' => 'Total Amount', 'sample' => '3,450.00' ],
					'payment_method' => [ 'label' => 'How to Pay', 'type' => 'textarea', 'sample' => 'Please settle via Stripe Transfer or Wire to: routing #021000021 acct #992288331. Net 14 terms apply.', 'note' => 'Specific payment instructions.' ],
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
					'recommendations' => [ 'label' => 'Future Strategy', 'type' => 'textarea', 'sample' => 'Based on this month\'s high engagement on visual content, we recommend shifting 15% of the outreach budget from LinkedIn to Instagram Reels. Additionally, we should initiate the Q4 Referral Campaign by the 15th to capitalize on holiday demand.', 'note' => 'Future strategic advice.' ],
				]
			],
			'va_discovery' => [
				'title'       => 'VA Discovery Call Questionnaire',
				'description' => 'Strategic questions to qualify leads and uncover client pain points during the first call.',
				'content'     => $branding_header . '<h1>Discovery Call Summary: {{client_name}}</h1>
								 <h2>Current Bottlenecks</h2>
								 <p>{{pain_points}}</p>
								 <h2>Goals & Objectives</h2>
								 <p>{{goals}}</p>
								 <h2>Preferred Workflow</h2>
								 <p>{{workflow_notes}}</p>
								 <h2>Proposed Support Tier</h2>
								 <p>Based on our discussion, I recommend the <strong>{{recommended_tier}}</strong> plan.</p>',
				'fields'      => [
					'client_name'      => [ 'label' => 'Prospect Name', 'sample' => 'Jordan Smith, Founder of TechStart' ],
					'pain_points'     => [ 'label' => 'Key Pain Points', 'type' => 'textarea', 'sample' => 'Client is spending 12 hours a week on manual email sorting and appointment scheduling. Missing roughly 20% of inbound leads due to slow response times.' ],
					'goals'           => [ 'label' => 'Client Goals', 'type' => 'textarea', 'sample' => 'To achieve "Inbox Zero" daily, automate the booking process via Calendly, and free up 10 hours for high-level business development.' ],
					'workflow_notes'  => [ 'label' => 'Workflow Tools', 'type' => 'textarea', 'sample' => 'Primary communication via Slack. Tasks managed in Asana. Email via G-Suite.' ],
					'recommended_tier' => [ 'label' => 'Recommended Tier', 'sample' => 'Standard 20-Hour Retainer' ],
				]
			],
			'content_calendar' => [
				'title'       => 'Social Media Content Calendar',
				'description' => 'Plan and present your content strategy for the month in a clean, structured format.',
				'content'     => $branding_header . '<h1>Content Strategy: {{month_year}}</h1>
								 <h2>Campaign Objective</h2>
								 <p>{{objective}}</p>
								 <table style="width:100%; border-collapse: collapse;">
									<thead><tr style="background:#f1f5f9;"><th style="padding:10px; border:1px solid #ddd;">Week</th><th style="padding:10px; border:1px solid #ddd;">Themes & Topics</th></tr></thead>
									<tbody>{{weekly_plan}}</tbody>
								 </table>
								 <h2>Key Visual Direction</h2>
								 <p>{{visual_notes}}</p>',
				'fields'      => [
					'month_year'   => [ 'label' => 'Month/Year', 'sample' => date('F Y') ],
					'objective'    => [ 'label' => 'Campaign Goal', 'type' => 'textarea', 'sample' => 'To drive 500 new registrations for the November "Freelance Freedom" webinar and increase brand authority in the VA niche.' ],
					'weekly_plan'  => [ 'label' => 'Weekly Schedule', 'type' => 'repeater', 'note' => 'Format: Week # - Content Focus' ],
					'visual_notes' => [ 'label' => 'Creative Direction', 'type' => 'textarea', 'sample' => 'High-contrast minimalist graphics, 3 carousel posts highlighting user results, and 2 "Behind the Scenes" Reels.' ],
				]
			],
			'client_onboarding' => [
				'title'       => 'Freelancer Onboarding Checklist',
				'description' => 'Professional checklist to ensure a smooth transition for new clients and set expectations.',
				'content'     => $branding_header . '<h1>Onboarding Checklist: {{project_title}}</h1>
								 <p>Welcome! To get started efficiently, please ensure we have access to the following:</p>
								 {{checklist_items}}
								 <h2>Communication Guidelines</h2>
								 <p>{{comm_rules}}</p>
								 <h2>Next Milestone</h2>
								 <p>{{first_task}}</p>',
				'fields'      => [
					'project_title'   => [ 'label' => 'Project Name', 'sample' => 'Q4 Digital Marketing Overhaul' ],
					'checklist_items' => [ 'label' => 'Requirements', 'type' => 'repeater', 'note' => 'e.g. WordPress Admin Access, Branding Assets (SVG Logo).' ],
					'comm_rules'      => [ 'label' => 'Communication Plan', 'type' => 'textarea', 'sample' => 'All formal approvals via Email. Quick queries via Slack (9 AM - 5 PM EST). Weekly sync every Tuesday at 10 AM.' ],
					'first_task'      => [ 'label' => 'First Action Item', 'sample' => 'Audit of current Facebook Ad account performance.' ],
				]
			],
			'brand_guidelines' => [
				'title'       => 'Brand Identity Brief',
				'description' => 'Summarize a client\'s brand elements for designers or content creators.',
				'content'     => $branding_header . '<h1>Brand Identity Brief: {{brand_name}}</h1>
								 <h2>Brand Voice</h2>
								 <p>{{voice}}</p>
								 <h2>Color Palette</h2>
								 {{colors}}
								 <h2>Typography</h2>
								 {{fonts}}
								 <h2>Target Audience</h2>
								 <p>{{audience}}</p>',
				'fields'      => [
					'brand_name' => [ 'label' => 'Brand/Company', 'sample' => 'Lumina Wellness' ],
					'voice'      => [ 'label' => 'Brand Voice', 'type' => 'textarea', 'sample' => 'Empathetic, authoritative, and minimalist. We avoid jargon and focus on clarity and peace.' ],
					'colors'     => [ 'label' => 'Primary Colors', 'type' => 'repeater', 'note' => 'List HEX codes or names.' ],
					'fonts'      => [ 'label' => 'Brand Fonts', 'type' => 'repeater', 'note' => 'e.g. Montserrat (Headings), Open Sans (Body).' ],
					'audience'   => [ 'label' => 'Ideal Customer', 'type' => 'textarea', 'sample' => 'Female entrepreneurs aged 30-45 looking for holistic stress management solutions.' ],
				]
			]
		];
	}
}
