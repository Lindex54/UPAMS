---
paths:
  - 'app/{Http,Models,Services}/**/*Beneficiary*.php'
---

# Http Models Services

## Beneficiary company documents
Company fields (company_name, registration_number, tin, tax_returns, director_names, owner_names, local_council_permit) and all document uploads are optional. Files live in beneficiary_documents on the private `local` disk via BeneficiaryDocumentService (stage before the DB transaction, attach inside it, purge/discard after). One current file per BeneficiaryDocument::STANDARD_TYPES entry (re-upload replaces); `other` documents must carry a user-entered title. Downloads only through the scoped operations.beneficiaries.documents.show route.
