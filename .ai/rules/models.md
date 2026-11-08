---
paths:
  - 'app/Models/**'
---

# Models

## Campus-scoped operational records
System Administrator and University Management have university-wide data access. University Property / Estates Manager, Campus Property Officer, Finance Officer, and IT Technician must only query and save operational records for their assigned campus. Models with a campus_id column should use App\Models\Concerns\BelongsToCampus; do not bypass its global scope in HTTP workflows.
