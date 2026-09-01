<?php

return [
    'version' => 'Uganda Electoral Commission verified administrative units, July 2022',
    'official_source_url' => 'https://www.ec.or.ug/admin-units',
    'dataset_url' => 'https://raw.githubusercontent.com/kakandemanwell/uganda/32fa40b78320b2fcb8eca1c22b55df37bf9f13c2/dist/uganda-locations-full.csv',
    'sha256' => '6a406d5a6069da1f677c6a6591bf097bc2d1d58ebb97d08d29495128237cfb31',
    'verify_tls' => env('UGANDA_LOCATIONS_VERIFY_TLS', true),
];
