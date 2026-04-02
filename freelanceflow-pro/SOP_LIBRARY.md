# FreelanceFlow Pro: Standard Operating Procedures (SOP) Library

This document provides step-by-step SOPs for every user role within the system to ensure maximum operational efficiency.

---

## 🛠️ SOP 1: FOR ADMINISTRATORS (Global Setup)
**Objective:** Configure the SaaS platform for high conversion and secure global payments.

1.  **Dependency Check:** Run `composer install` to verify Dompdf and Stripe libraries are active.
2.  **Revenue Configuration:**
    *   Navigate to `Subscription & Payments`.
    *   Enter the **Global Stripe Secret Key** and **Webhook Secret**.
    *   Map your Price IDs (Price_Pro, Price_Agency) from the Stripe Dashboard to the `Master Plan Configuration` table.
3.  **Tier Limits:**
    *   Go to `Profile & Branding`.
    *   Set the `Free User Document Limit` (Recommended: 3).
4.  **Template Oversight:** Use the API `GET /templates` endpoint periodically to verify template schema integrity.

---

## 🏢 SOP 2: FOR AGENCY OWNERS (Team Management)
**Objective:** Standardize team output and capture direct revenue.

1.  **Identity Branding:**
    *   Upload your Agency Logo in `Profile & Branding`.
    *   Toggle `White-Labeling` to ensure your team sees your brand, not FreelanceFlow.
2.  **Sovereign Payment Setup:**
    *   Enter your specific Stripe Secret Key in the Agency settings.
    *   *Why?* This ensures your team’s payments bypass the plugin admin and go directly to you.
3.  **Onboarding Team Members:**
    *   Navigate to `User Management`.
    *   Add sub-users via email and assign them their initial tier (Free/Pro).
4.  **Quality Control:**
    *   Weekly, check the `File Vault` > `All Files`.
    *   Audit recently generated contracts from your team to ensure compliance with Agency standards.

---

## 💼 SOP 3: FOR FREELANCERS / VAs (Daily Operations)
**Objective:** Rapid document generation and secure client onboarding.

1.  **Daily Identity Sync:**
    *   Ensure your Business Name is current in `Profile & Branding`.
2.  **Document Lifecycle:**
    *   **Phase 1 (Discovery):** Generate a `Discovery Questionnaire`. Use "Load Sample" for qualifying questions.
    *   **Phase 2 (Closing):** Generate an `Independent Contractor Agreement`.
    *   **Phase 3 (Billing):** Use the `Invoice` template with repeater fields for itemized services.
3.  **Secure Handoff:**
    *   Upload signed contracts or client ID photos to the `File Vault`.
    *   Generate a `Secure Download URL` and share it with the client via Slack/Email.
4.  **Archival:** Check `My Generated Docs` monthly to clean up old drafts and maintain an organized project history.
