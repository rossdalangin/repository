# FreelanceFlow Pro - The Comprehensive Guide

## 🚀 Quick Start Guide

### 1. For Administrators
- **Setup:** Navigate to **FreelanceFlow -> Subscription & Payments** and enter your Stripe/PayPal keys.
- **Limits:** Set the global "Free User Document Limit" in the **Profile & Branding** tab.
- **Monitoring:** Track growth in the **Transaction Logs** tab.

### 2. For Agency Owners
- **Monetization:** Enter your own Stripe/PayPal keys in **Profile & Branding**. This routes team payments to you.
- **Team Building:** Use **User Management** to create sub-users and assign them to Free or Pro tiers.
- **Content:** Access and manage all team documents in the **File Vault**.

### 3. For Freelancers (Pro/Free)
- **Branding:** Set your business name and logo (Pro only) in **Profile & Branding**.
- **Automation:** Use the **Template Generator** to create professional PDFs/DOCX files.

---

## 🛠️ Feature Deep-Dive

### Document Template Generator
- **Professional Content:** Every template (Contract, SOW, ROI Report) comes pre-loaded with high-value content.
- **Smart Fields:** Use "Load Sample" to see how to fill out complex legal or business fields.
- **Export:** One-click download as high-quality PDF or editable DOCX.

### Secure File Vault
- **Encryption:** All download links are signed and temporary.
- **Visibility Tiers:**
  - **Private:** Visible only to the uploader.
  - **Public:** Accessible to all logged-in users (Free, Pro, Agency).
  - **Premium:** Restricted to Pro and Agency tiers.
  - **Agency Only:** Restricted to Agency owners and their sub-users.
- **Categories:** Files can be categorized (Legal, ID, Agency). The 'Agency' category automatically restricts access to Agency-level users.

### Multi-Gateway Payments
- **Choice:** If configured, customers can choose between Stripe (Card) or PayPal.
- **Tenant Isolation:** Payments made through an Agency's embed code or team are routed directly to that Agency's account.

---

## 📋 Comprehensive Template Directory
1.  **Professional Service Agreement:** Comprehensive legal contract.
2.  **VA Proposal:** High-converting sales document.
3.  **Itemized Invoice:** Professional billing with repeater support.
4.  **SOP Generator:** Standardize your operations.
5.  **ROI Impact Report:** Prove your value to clients.
6.  **Weekly/Monthly Status Reports:** Maintain high retention.
7.  **Scope of Work (SOW):** Eliminate scope creep.
8.  **Cold Outreach Generator:** Scale your client acquisition.

---

## ❓ Troubleshooting & FAQ
- **PDF not downloading?** Ensure you are running on a server with `allow_url_fopen` enabled and `Dompdf` dependencies installed via Composer.
- **Branding not showing?** Check that you have saved your settings in the 'Profile & Branding' tab. Free users only display the company name.
- **Gateway selection missing?** Both Stripe and PayPal credentials must be validly entered to show the choice UI.
