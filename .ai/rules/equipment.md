---
paths:
  - 'app/{Http/Controllers/Technician,Http/Requests/Technician,Services}/**/*.php,resources/views/technician/equipment/*.blade.php'
---

# Equipment

## Keep ICT specifications type-driven
ICT equipment uses the shared technician/admin form and config/ict_equipment.php schema. Asset IDs are generated server-side with AssetReferenceGenerator and must never be accepted from form input. Persist only type-allowed dynamic fields in assets.specifications; registration requires only name, make, and normalized unique serial number, except a broad Computer Equipment selection requires its subtype.
