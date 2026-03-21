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
- **Templates:** Over 12+ industry-standard templates (Contracts, SOW, ROI, SOPs, Invoices, etc.).
- **Smart Fields:** Use the "Load Sample" buttons to instantly see high-converting text for every field.
- **Repeater Support:** Easily add multiple milestones or line items with a single click.

### 3. File Vault (Secure Repository)
**Purpose:** 100% secure storage for sensitive documents.
- **Visibility Rules:**
    - **Private:** Only you (the uploader) can see or download.
    - **Public:** Any user with a "Free" or higher account can access (useful for global tutorials/guides).
    - **Premium:** Only "Pro" or "Agency" tiers can access.
    - **Agency:** Restricted strictly to the Agency Owner and their specific team members.
- **Security:** All download links are signed, temporary, and cannot be hotlinked.

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

## ❓ FAQ & Troubleshooting
- **PDF not downloading?** The system includes a built-in fix for local XAMPP environments, but ensure `allow_url_fopen` is enabled on your live server.
- **Branding not showing?** Free tier users only show the business name. Upgrade to Pro/Agency to enable full Logo support.
- **Sub-user can't see team files?** Verify the file visibility in the Vault is set to "Agency".
