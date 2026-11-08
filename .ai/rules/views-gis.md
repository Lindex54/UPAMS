---
paths:
  - 'app/Services/GisFeatureService.php,resources/views/gis/*.blade.php'
---

# Views Gis

## Campus map visibility boundary
Campus-scoped officers may see every geolocated campus marker for university-wide orientation, but asset/property features, operational counts, and record links must remain limited to their assigned campus. Enforce the property filter in GisFeatureService using the viewer passed to the service; do not rely only on the authenticated global scope.
