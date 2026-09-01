<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBeneficiaryRequest;
use App\Http\Requests\UpdateBeneficiaryRequest;
use App\Models\Beneficiary;
use App\Models\Campus;
use App\Models\County;
use App\Models\District;
use App\Models\Parish;
use App\Models\SubCounty;
use App\Models\Village;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BeneficiaryController extends Controller
{
    public function create(): View
    {
        return view('operations.page', [
            'module' => 'beneficiaries',
            'page' => 'create',
            'beneficiary' => null,
            ...$this->formReferenceData(),
        ]);
    }

    public function store(StoreBeneficiaryRequest $request): RedirectResponse
    {
        $beneficiary = DB::transaction(function () use ($request): Beneficiary {
            $attributes = $request->validated();
            $attributes['created_by'] = $request->user()?->id;
            $attributes['updated_by'] = $request->user()?->id;

            $beneficiary = Beneficiary::create($attributes);
            $beneficiary->update([
                'reference' => 'BEN-'.str_pad((string) $beneficiary->id, 6, '0', STR_PAD_LEFT),
            ]);

            return $beneficiary;
        });

        return redirect()
            ->route('operations.beneficiaries.edit', $beneficiary->reference)
            ->with('status', 'Beneficiary profile created successfully.');
    }

    public function edit(string $record): View
    {
        $beneficiary = Beneficiary::query()
            ->with(['campus:id,name', 'creator:id,name', 'updater:id,name'])
            ->where('reference', $record)
            ->when(ctype_digit($record), fn ($query) => $query->orWhereKey((int) $record))
            ->first();

        return view('operations.page', [
            'module' => 'beneficiaries',
            'page' => 'edit',
            'beneficiary' => $beneficiary,
            ...$this->formReferenceData($beneficiary),
        ]);
    }

    public function update(UpdateBeneficiaryRequest $request, Beneficiary $beneficiary): RedirectResponse
    {
        $attributes = $request->validated();
        $attributes['updated_by'] = $request->user()?->id;
        $beneficiary->update($attributes);

        return redirect()
            ->route('operations.beneficiaries.edit', $beneficiary->reference)
            ->with('status', 'Beneficiary profile updated successfully.');
    }

    /**
     * @return array{
     *     campuses: Collection<int, Campus>,
     *     districts: Collection<int, District>,
     *     counties: Collection<int, County>,
     *     subCounties: Collection<int, SubCounty>,
     *     parishes: Collection<int, Parish>,
     *     villages: Collection<int, Village>
     * }
     */
    private function formReferenceData(?Beneficiary $beneficiary = null): array
    {
        $districtId = old('district_id', $beneficiary?->district_id);
        $countyId = old('county_id', $beneficiary?->county_id);
        $subCountyId = old('sub_county_id', $beneficiary?->sub_county_id);
        $parishId = old('parish_id', $beneficiary?->parish_id);

        return [
            'campuses' => Campus::query()->orderBy('name')->get(['id', 'name']),
            'districts' => District::query()->active()->orderBy('name')->get(['id', 'name', 'code']),
            'counties' => County::query()->active()->where('district_id', $districtId)->orderBy('name')->get(['id', 'name', 'code']),
            'subCounties' => SubCounty::query()->active()->where('county_id', $countyId)->orderBy('name')->get(['id', 'name', 'code']),
            'parishes' => Parish::query()->active()->where('sub_county_id', $subCountyId)->orderBy('name')->get(['id', 'name', 'code']),
            'villages' => Village::query()->active()->where('parish_id', $parishId)->orderBy('name')->get(['id', 'name', 'code']),
        ];
    }
}
