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
- **Strict Tier Visibility:**
    - **Private:** Visible ONLY to the uploader. All Free and Pro user uploads are private by default.
    - **Free Account Only (Admin/Agency Public):** Files intended specifically for Free users (direct or within an agency).
    - **Pro Account Only (Admin/Agency Premium):** Premium resources strictly for Pro subscribers.
    - **Agency Account Only:** High-level strategic documents for Agency owners.
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
- **PDF not downloading?** The system includes a built-in fix for local XAMPP environments, but ensure `allow_url_fopen` is enabled on your live server.
- **Branding not showing?** Free tier users only show the business name. Upgrade to Pro/Agency to enable full Logo support.
- **Sub-user can't see team files?** Verify the file visibility in the Vault is set to "Agency".
