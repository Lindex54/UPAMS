---
paths:
  - 'app/{Http/Controllers/Finance,Http/Requests,Policies,Services,Models}/**/*{Invoice,Billing}*.php'
  - 'app/{Http/Controllers/Finance,Http/Requests,Policies,Services,Models}/**/*{Payment,Billing}*.php'
---

# Requests Policies Services Models

## Invoice role checks and post-issue locking
Only System Administrator and Finance Officer (InvoicePolicy::MANAGING_ROLES) may create, edit, cancel or archive invoices. Drafts are fully editable; once issued only Invoice::EDITABLE_AFTER_ISSUE fields may change (enforced by `prohibited` rules in StoreInvoiceRequest and again in BillingService). Cancel only when no Recorded payments exist; archive only fully paid invoices so open balances never leave arrears. No payments against Drafts. Route all state changes through BillingService (row-locked transactions).

## Payment role checks and two-person reversal
Only finance roles (User::FINANCE_ROLES / User::managesFinance(): System Administrator, Finance Officer) may record or reverse payments; PaymentPolicy create/reverse. The user who recorded a payment (created_by) can never reverse it — enforced in PaymentPolicy::reverse and again in BillingService::reversePayment. Tests that reverse payments must use a different user from the recorder.
