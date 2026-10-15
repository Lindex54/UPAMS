---
paths:
  - 'app/{Services/GisFeatureService.php,Models/**/*.php},database/migrations/*.php,resources/js/gis/*.js'
---

# Gis

## Geolocated records must feed the GIS map
Any model given latitude/longitude columns (decimal 10,7, nullable, entered as a pair) must also be added to GisFeatureService: one entry in LAYERS (label + colour) and one collector returning the shared feature shape (id, layer, title, subtitle, badge, status, campus, latitude, longitude, url, details). The /gis-map page renders whatever that service returns; Leaflet is lazy-loaded from resources/js/gis/map.js, so never import leaflet in app.js directly.
