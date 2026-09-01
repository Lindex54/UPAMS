---
paths:
  - 'app/{Http,Models}/**/*Beneficiary*.php'
---

# Http Models

## Beneficiaries are independent of user accounts
Beneficiary email is optional and beneficiary records must not require or create a system User/login. Uganda location selections must be stored as District→County→Sub-county→Parish→Village foreign keys and validation must verify each child belongs to the selected parent.
