<?php

namespace App\Http\Controllers;

use App\Services\GisFeatureService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GisMapController extends Controller
{
    public function __construct(private readonly GisFeatureService $gis) {}

    /**
     * Show every geolocated record on one interactive map.
     */
    public function __invoke(Request $request): View
    {
        $features = $this->gis->features($request->user());

        return view('gis.index', [
            'features' => $features,
            'layers' => $this->gis->layers($features),
        ]);
    }
}
