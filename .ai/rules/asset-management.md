---
paths:
  - 'app/{Http/Controllers/Core,Http/Requests,Services}/**/*Property*.php,resources/views/asset-management/**/*.blade.php'
---

# Asset Management

## Property modules share the asset register
Asset Registry, Land, Buildings & Spaces, Commercial Property, and Agricultural Property persist through Asset records. Module-specific fields belong in assets.specifications; fixed modules are separated by AssetCategory. Keep all reads/writes on the Asset model so BelongsToCampus enforces campus isolation, and never trust a submitted campus for campus-scoped users.
