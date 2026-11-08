<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use InvalidArgumentException;

class PropertyModuleRegistry
{
    /** @var array<string, array<string, mixed>> */
    private const MODULES = [
        'assets' => [
            'prefix' => 'AST', 'category' => null, 'name' => 'asset_name',
            'required' => ['asset_name', 'asset_category', 'acquisition_date', 'condition', 'physical_location'],
            'fields' => ['asset_name', 'asset_category', 'asset_tag_code', 'serial_number', 'acquisition_date', 'acquisition_cost', 'condition', 'assigned_unit', 'physical_location', 'custodian'],
            'primary' => 'asset_category', 'secondary' => 'physical_location',
        ],
        'land' => [
            'prefix' => 'LND', 'category' => 'Land & Other Property', 'name' => 'parcel_name',
            'required' => ['parcel_name', 'tenure_type', 'area_acres', 'boundary_status'],
            'fields' => ['parcel_name', 'plot_block_number', 'tenure_type', 'area_acres', 'current_use', 'survey_reference', 'title_instrument_number', 'boundary_status', 'managing_unit'],
            'primary' => 'tenure_type', 'secondary' => 'current_use',
        ],
        'buildings' => [
            'prefix' => 'BLD', 'category' => 'Buildings & Spaces', 'name' => 'building_name',
            'required' => ['building_name', 'building_type', 'condition'],
            'fields' => ['building_name', 'building_code', 'building_type', 'number_of_floors', 'gross_floor_area_m2', 'year_completed', 'occupancy_status', 'condition', 'primary_function', 'facility_manager'],
            'primary' => 'building_type', 'secondary' => 'occupancy_status',
        ],
        'commercial-property' => [
            'prefix' => 'COM', 'category' => 'Commercial Property', 'name' => 'property_unit_name',
            'required' => ['property_unit_name', 'property_type', 'occupancy_status'],
            'fields' => ['property_unit_name', 'unit_reference', 'property_type', 'floor_area_m2', 'occupancy_status', 'monthly_rent', 'current_tenant', 'agreement_reference', 'agreement_expiry', 'property_manager'],
            'primary' => 'property_type', 'secondary' => 'occupancy_status',
        ],
        'agricultural-property' => [
            'prefix' => 'AGR', 'category' => 'Agricultural Property', 'name' => 'property_name',
            'required' => ['property_name', 'property_type', 'current_use', 'infrastructure_condition'],
            'fields' => ['property_name', 'plot_facility_reference', 'property_type', 'area_capacity', 'primary_enterprise', 'current_use', 'irrigation_water_source', 'infrastructure_condition', 'managing_faculty_unit', 'property_custodian'],
            'primary' => 'property_type', 'secondary' => 'current_use',
        ],
    ];

    /** @return list<string> */
    public function modules(): array
    {
        return array_keys(self::MODULES);
    }

    /** @return array<string, mixed> */
    public function configuration(string $module): array
    {
        return self::MODULES[$module] ?? throw new InvalidArgumentException("Unsupported property module [{$module}].");
    }

    public function constrain(Builder $query, string $module): Builder
    {
        $category = $this->configuration($module)['category'];

        return $query->when($category, fn (Builder $query) => $query->whereHas('category', fn (Builder $query) => $query->where('name', $category)));
    }
}
