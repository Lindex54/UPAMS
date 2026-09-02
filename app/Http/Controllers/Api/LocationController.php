<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LocationOptionResource;
use App\Models\County;
use App\Models\District;
use App\Models\Parish;
use App\Models\SubCounty;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Serve database-backed options for the cascading Uganda location selector.
 *
 * These requests do not contact the external dataset. Each endpoint returns only active
 * children belonging to the parent selected in the preceding dropdown.
 */
class LocationController extends Controller
{
    /** Return the active counties or constituencies stored under a district. */
    public function counties(District $district): AnonymousResourceCollection
    {
        abort_unless($district->is_active, 404);

        return LocationOptionResource::collection(
            County::query()->active()->whereBelongsTo($district)->orderBy('name')->get(['id', 'name', 'code'])
        );
    }

    /** Return the active sub-counties or divisions stored under a county. */
    public function subCounties(County $county): AnonymousResourceCollection
    {
        abort_unless($county->is_active, 404);

        return LocationOptionResource::collection(
            SubCounty::query()->active()->whereBelongsTo($county)->orderBy('name')->get(['id', 'name', 'code'])
        );
    }

    /** Return the active parishes or wards stored under a sub-county. */
    public function parishes(SubCounty $subCounty): AnonymousResourceCollection
    {
        abort_unless($subCounty->is_active, 404);

        return LocationOptionResource::collection(
            Parish::query()->active()->whereBelongsTo($subCounty)->orderBy('name')->get(['id', 'name', 'code'])
        );
    }

    /** Return the active villages or cells stored under a parish. */
    public function villages(Parish $parish): AnonymousResourceCollection
    {
        abort_unless($parish->is_active, 404);

        return LocationOptionResource::collection(
            $parish->villages()->active()->orderBy('name')->get(['id', 'name', 'code'])
        );
    }
}
