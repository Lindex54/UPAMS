---
paths:
  - 'app/{Console,Services}/**/*UgandaLocation*.php'
---

# Console Services

## Use verified EC Uganda location hierarchy
Production Uganda administrative locations come from the pinned machine-readable extraction of the Electoral Commission's Verified Administrative Units, July 2022 register. Keep the source URL/version/hash in config/uganda_locations.php; never seed generated or placeholder location names.
