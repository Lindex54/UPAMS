<?php

namespace App\Models;

use Database\Factories\BeneficiaryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'reference', 'full_name_organization', 'category', 'contact_person', 'telephone', 'email',
    'nin', 'nin_hash', 'national_id_given_names', 'national_id_surname',
    'national_id_sex', 'nationality', 'photo_path',
    'district_id', 'county_id', 'sub_county_id', 'parish_id', 'village_id',
    'physical_address_landmark', 'current_property_allocation', 'agreement_reference',
    'billing_cycle', 'opening_balance', 'campus_id', 'record_status', 'responsible_unit',
    'record_owner', 'administrative_notes', 'created_by', 'updated_by',
])]
#[Hidden(['nin', 'nin_hash'])]
class Beneficiary extends Model
{
    /** @use HasFactory<BeneficiaryFactory> */
    use HasFactory;

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function county(): BelongsTo
    {
        return $this->belongsTo(County::class);
    }

    public function subCounty(): BelongsTo
    {
        return $this->belongsTo(SubCounty::class);
    }

    public function parish(): BelongsTo
    {
        return $this->belongsTo(Parish::class);
    }

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    protected function casts(): array
    {
        return [
            'nin' => 'encrypted',
            'opening_balance' => 'decimal:2',
        ];
    }
}
