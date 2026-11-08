---
paths:
  - 'app/Http/Controllers/**/*.php'
---

# Controllers

## Quote the asset condition column
MariaDB treats `condition` as a reserved keyword. For grouped asset condition queries, pass the qualified column through Query Builder `select()` and `groupBy()` so Laravel quotes it; do not place an unquoted `condition` identifier inside `selectRaw()`.
