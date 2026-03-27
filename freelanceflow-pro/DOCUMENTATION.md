# FreelanceFlow Pro: The Definitive SaaS Master Guide

## 🌟 Introduction
FreelanceFlow Pro is an enterprise-grade document automation and secure multi-tenant repository designed for the high-performance freelance economy. This guide covers every feature, role, and configuration option available.

---

## 🚀 Dashboard Navigation & Features

### 1. Profile & Branding
**Purpose:** Define your professional identity.
- **Business Name:** Used across all documents.
- **Logo URL:** Upload your logo here (Paid Tiers/Admins only). It will appear in the top header of all generated PDFs/DOCX files.
- **Agency Payment Keys:** Agency owners can enter their own Stripe/PayPal credentials here to receive direct payments from their specific sub-users and external embeds.

### 2. Document Template Generator
**Purpose:** Create professional business docs in seconds.
- **Templates:** Over 12+ high-value templates including Service Agreements, VA Proposals, Invoices, SOPs, and ROI Impact Reports.
- **Smart Fields:** Use the "Load Sample" buttons to instantly inject industry-standard content and legal phrasing.
- **Repeater Support:** Dynamic rows for project milestones, itemized billing, or task lists.

### 3. File Vault (Secure Repository)
**Purpose:** Enterprise-grade security for sensitive documents.
- **Sub-Tabs Configuration:**
    - **All Files:** The primary repository for your legal and identity documents.
    - **My Generated Docs:** An automated archival system. Every PDF or DOCX you generate is instantly saved here as a private file for future reference.
- **Strict Tier Visibility:**
    - **Private (Uploader Only):** Visible ONLY to you. This is the default for all generated documents.
    - **Visible to All Tiers:** Publicly accessible resources for everyone in your organization.
    - **Visible to Pro & Agency:** Premium resources for paid subscribers.
    - **Visible to Agency Only:** High-level strategic documents for Agency owners.
- **Access Control:** The system automatically segregates global Admin resources from tenant-specific Agency resources. Referred users only see what their Agency provides.
- **Security:** Every download uses a signed, one-time-use token (`ffp_token`).

### 4. User Management (Agency/Admin Only)
**Purpose:** Build and manage your freelance team.
- **Create Sub-Users:** Agency owners can add Free or Pro team members.
- **Control:** Manually set tiers, remove access, or delete team documents.
- **Isolation:** Agencies only see their own sub-users; Administrators see everyone.

### 5. Subscription & Payments
**Purpose:** Manage your own plan or the system-wide gateway.
- **Gateway Choice:** If both Stripe and PayPal are configured, users see a choice during checkout.
- **Pricing Grid:** Easily upgrade from Free to Pro or Agency.

---

## 📋 Comprehensive Template Library
1.  **Independent Contractor Agreement:** Legal safety for freelancers.
2.  **VA Proposal:** Convert leads with structured value.
3.  **Service Invoice:** Itemized billing with repeater support.
4.  **SOP Generator:** Standardize your internal processes.
5.  **ROI Impact Report:** Mathematically prove your value to clients.
6.  **Scope of Work (SOW):** Eliminate scope creep.
7.  **Monthly Performance Review:** Maintain long-term retention.
8.  **Cold Outreach Generator:** Scale your client acquisition.
9.  **Retainer Agreement:** Secure ongoing monthly revenue.
10. **Case Study Generator:** Turn past wins into new sales.

---

## ⚙️ Setup & Technical Instructions

### Administrator Setup
1.  Install the plugin and run `composer install`.
2.  Go to `Subscription & Payments` and enter global Stripe/PayPal keys.
3.  In `Profile & Branding`, set the `Free User Document Limit`.

### Agency Setup
1.  Navigate to `Profile & Branding`.
2.  Enter your unique Stripe/PayPal keys to ensure payments from your team route to you.
3.  Use `User Management` to invite your team.

---

## ✅ New User Onboarding Checklist
To get the most out of FreelanceFlow Pro, complete these steps in order:

1.  **Brand Your Instance:** Head to `Profile & Branding`. Enter your business name and upload a high-resolution logo (minimum 200px width recommended).
2.  **Configure Payments:** Even if you aren't selling access, setting up your Stripe/PayPal keys ensures you're ready for our upcoming client-billing features.
3.  **Generate Your First Doc:** Select the 'VA Proposal' or 'Contract' template. Use the `Load Sample` buttons to see what high-converting documents look like.
4.  **Seed Your Vault:** Upload your current portfolio or standard service agreement to the File Vault. Set it to 'Private' to test the signed URL system.
- **Review Realistic Samples:** Navigate to the `/samples/` directory in the plugin folder. Review the pre-filled DOCX and CSV files to understand the gold standard for freelancer documentation.

---

## 💎 Tier Mastery: Maximizing Your Subscription

### Free Tier: The "Starter" Strategy
- **Capabilities:** 3 Documents/mo, Basic Templates.
- **Mastery:** Use the Free tier to qualifying leads. Generate a "Discovery Call Questionnaire" or "ROI Impact Report" to prove your value before committing to a full project.

### Pro Tier: The "Scaling Solo" Strategy
- **Capabilities:** Unlimited Docs, Custom Branding, Full Template Library.
- **Mastery:** Enable your logo in `Profile & Branding`. Every proposal and invoice you send now acts as a brand ambassador. Use the "Case Study Generator" monthly to build a massive portfolio of results.

### Agency Tier: The "SaaS Infrastructure" Strategy
- **Capabilities:** Sub-user management, White-labeling, Sovereign Payments.
- **Mastery:** This is where you stop being a freelancer and start being an infrastructure owner.
    1. **Centralize:** Require all your contractors to use *your* dash for proposals.
    2. **Sovereign Payments:** Input your Stripe keys. When your team's clients pay an invoice generated from *your* platform, the money hits *your* account first.
    3. **Visibility:** Use the "All Files" vault view to audit every piece of ID and contract your team handles, ensuring 100% compliance.

---

## 🔑 Subscription Capability Deep-Dive

### PRO TIER: THE BRANDED POWERHOUSE
Upgrade to Pro to transform from a "freelancer" into a "brand."
- **Full Template Library:** Access all 16+ templates including SOWs, SOPs, and Content Calendars.
- **Custom Branding:** Upload your logo in `Profile & Branding`. Our engine automatically scales it to a professional 40px height and aligns it in your documents.
- **PDF & DOCX Fidelity:** Generate high-resolution PDFs for legal contracts and editable DOCX files for collaborative drafts.
- **Automated Archival:** Never lose a document. Every generated file is privately saved in your Vault.

### AGENCY TIER: THE SAAS INFRASTRUCTURE
This is the elite tier for those building a workforce.
- **Multi-Tenant Team Management:** Create sub-accounts for your freelancers. They get their own private dashboards, but *you* maintain oversight.
- **Sovereign Payments (The Money-Maker):**
    1. Navigate to `Profile & Branding`.
    2. Enter *your* Stripe Secret Key.
    3. Now, whenever your sub-users or external pricing embeds are used, the funds go directly to your account. You own the financial stack.
- **White-Labeling:** Replace our branding with yours. Your team sees your logo, your business name, and uses your standardized legal templates.
- **Auditable Repository:** View all files handled by your team in the "All Files" vault tab. Ensure every freelancer is using the correct, signed contracts.

---

## 📢 Marketing 101 for Agency Owners
If you've upgraded to the Agency tier, you're now a SaaS owner. Here’s how to market your new "infrastructure" to your team:

- **The Value Prop:** "Join my team and get access to enterprise-grade tools. You'll spend less time on paperwork and more time on client work."
- **Standardization:** Use the shared vault to provide your team with standardized SOPs and contract templates. This ensures a consistent experience for *all* your clients.
- **Onboarding:** Use the `VIDEO_SCRIPTS.md` to record a personalized "Welcome to the Team" tour for your new hires.

---

## 🛠️ API & Developer Reference
FreelanceFlow Pro exposes a REST API for advanced integrations (AI, CRM, etc).

- **Namespace:** `/wp-json/ffp/v1/`
- **Endpoints:**
    - `GET /templates`: List all active document templates.
    - `POST /webhooks/stripe`: The target for your Stripe dashboard webhooks.

---

## ❓ FAQ & Troubleshooting
- **PDF not downloading?**
    - **XAMPP/Local:** The system includes a built-in fix for local SSL issues.
    - **Shared Hosting:** Ensure `allow_url_fopen` is enabled in your `php.ini`.
    - **Corrupted Files:** If PDFs appear blank, ensure no other plugin is outputting content (echo/print) before the generation starts. FreelanceFlow Pro handles output buffer cleaning automatically, but strict host configurations may interfere.
- **PDF Logo Issues:** We use absolute path resolution for logos. If your logo isn't showing, ensure the image is stored within the standard `wp-content` directory.
- **DOCX Formatting:** The DOCX export uses a high-compatibility HTML-header method. For complex layouts, we recommend generating a PDF for the best visual fidelity.
- **Branding not showing?** Free tier users only show the business name. Upgrade to Pro/Agency to enable full Logo support.
- **Sub-user can't see team files?** Verify the file visibility in the Vault is set to "Visible to All Tiers" or "Visible to Pro & Agency" depending on their plan.
