---
paths:
  - 'app/Http/{Middleware,Requests,Controllers}/**/*.php,routes/web.php,resources/views/{layouts,components}/**/*.blade.php'
---

# Layoutscomponents

## Administration is System Administrator only
User-account and campus administration routes must be protected by EnsureSystemAdministrator. Campus Property Officers must always receive the campus sidebar and must never see the Administration navigation group; hiding links is not a substitute for route authorization.
