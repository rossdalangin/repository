# FreelanceFlow Pro - The Ultimate Guide

## 1. Introduction
High-converting document automation and secure repository for Virtual Assistants, Freelancers, and Agencies.

## 2. User Level Guide & Permissions

### **Free Tier (The Solopreneur Starter)**
- **Dashboard Access:** Basic document generator.
- **Limits:** 3 documents per month.
- **Templates:** access to "Simple Contract".
- **Instructions:** Navigate to 'Template Generator', select your template, fill fields, and download PDF.

### **Pro Tier (The Growth Engine)**
- **Dashboard Access:** Full document generator + Personal File Vault.
- **Limits:** Unlimited documents.
- **Features:** Custom Branding (Your Logo/Name on docs), PDF/DOCX export, access to all templates (NDA, Proposals, etc.).
- **Instructions:** Set your branding in 'Profile & Branding', then use the generator for any professional need.

### **Agency Tier (The Team Command Center)**
- **Dashboard Access:** Full Suite + User Management.
- **Limits:** Unlimited documents for owner and team.
- **Rights:**
  - Manage all files created by their sub-users.
  - **Create Sub-Users:** Can add up to [X] users to their team (Free or Pro tiers).
  - **Tier Management:** Can upgrade/downgrade their sub-users between Free and Pro.
  - **Agency Payments:** Can configure their own Stripe keys to receive payments directly from their team or HTML embeds.
- **Instructions:** Use the 'User Management' tab to invite team members and set their access level. Configure your own Stripe keys in 'Profile & Branding' to enable direct monetization.

### **Administrator (The System Owner)**
- **Dashboard Access:** Global Master Access.
- **Rights:**
  - Full Gateway Config (Stripe/PayPal).
  - **Manage Subscriptions:** Can manually promote users to Pro or Agency status.
  - Manage ALL users across all agencies.
  - View global Transaction Logs.
  - Global File Vault management (Categories, Visibility).
- **Instructions:** Use 'Subscription & Payments' to set keys, and 'User Management' for system-wide oversight.

## 3. Shortcode Reference
- `[ffp_pricing]`: Embed the pricing table anywhere.
- `[ffp_file_list category="legal"]`: List secure files by category.

## 4. Developer API
Endpoint: `/wp-json/ffp/v1/templates`
Webhook: `/wp-json/ffp/v1/webhooks/stripe`
