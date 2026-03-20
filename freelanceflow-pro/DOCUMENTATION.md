# FreelanceFlow Pro - Documentation

## 1. Introduction
FreelanceFlow Pro is an enterprise-grade document automation and secure repository system for Virtual Assistants, Freelancers, and Agencies. It streamlines client onboarding and reporting by generating professional PDF/DOCX documents in seconds.

## 2. Installation
1. Upload the `freelanceflow-pro` folder to your `/wp-content/plugins/` directory.
2. Ensure you run `composer install` inside the plugin directory to fetch dependencies (Dompdf, Stripe).
3. Activate the plugin via the WordPress 'Plugins' menu.

## 3. Initial Configuration
Navigate to **FreelanceFlow** in your WordPress Admin sidebar.

### 3.1 Profile & Branding
Set your business name and logo URL. These will be injected into generated documents for professional branding.

### 3.2 Subscription & Payments
- **Stripe:** Enter your Stripe Secret Key and Webhook Secret to enable Pro/Agency subscriptions.
- **PayPal:** Enter your PayPal Client ID.
- Access the pricing grid to see available features for each tier.

## 4. Using the Template Generator
1. Go to the **Template Generator** tab.
2. Select a template from the sidebar (e.g., Freelance Contract, VA Proposal).
3. Fill in the dynamic fields.
4. Use the **"Load Sample"** buttons to quickly see example data.
5. Click **"Generate PDF"** or **"Generate DOCX"** to download the finalized document.

## 5. File Vault
- Upload legal documents, identity proofs, or portfolio pieces.
- Files are protected via signed URLs, ensuring only authorized users can access them.

## 6. Developer Extensibility
### Hooks
- `ffp_before_generate`: Runs before document generation.
- `ffp_template_data`: Filter to modify placeholder data globally.

### REST API
Endpoint: `/wp-json/ffp/v1/templates` (Requires `manage_options` capability).
