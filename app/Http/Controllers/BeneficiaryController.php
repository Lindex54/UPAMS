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
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use RuntimeException;
use Throwable;

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
        $attributes = $request->validated();
        unset($attributes['photo']);

        $storedPhotoPath = $this->storePhoto($request->file('photo'));

        if ($storedPhotoPath !== null) {
            $attributes['photo_path'] = $storedPhotoPath;
        }

        $attributes['created_by'] = $request->user()?->id;
        $attributes['updated_by'] = $request->user()?->id;

        try {
            $beneficiary = DB::transaction(function () use ($attributes): Beneficiary {
                $beneficiary = Beneficiary::create($attributes);
                $beneficiary->update([
                    'reference' => 'BEN-'.str_pad((string) $beneficiary->id, 6, '0', STR_PAD_LEFT),
                ]);

                return $beneficiary;
            });
        } catch (Throwable $exception) {
            // Avoid leaving an orphaned private file when the database transaction fails.
            if ($storedPhotoPath !== null) {
                Storage::disk('local')->delete($storedPhotoPath);
            }

            throw $exception;
        }

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
            'beneficiaryPhotoUrl' => $this->photoUrl($beneficiary),
            ...$this->formReferenceData($beneficiary),
        ]);
    }

    public function update(UpdateBeneficiaryRequest $request, Beneficiary $beneficiary): RedirectResponse
    {
        $attributes = $request->validated();
        unset($attributes['photo']);

        if (blank($attributes['nin'] ?? null)) {
            unset($attributes['nin'], $attributes['nin_hash']);
        }

        $previousPhotoPath = $beneficiary->photo_path;
        $storedPhotoPath = $this->storePhoto($request->file('photo'));

        if ($storedPhotoPath !== null) {
            $attributes['photo_path'] = $storedPhotoPath;
        }

        $attributes['updated_by'] = $request->user()?->id;

        try {
            DB::transaction(fn (): bool => $beneficiary->update($attributes));
        } catch (Throwable $exception) {
            // Preserve the current photo and remove only the uncommitted replacement.
            if ($storedPhotoPath !== null) {
                Storage::disk('local')->delete($storedPhotoPath);
            }

            throw $exception;
        }

        if ($storedPhotoPath !== null && $previousPhotoPath !== null) {
            Storage::disk('local')->delete($previousPhotoPath);
        }

        return redirect()
            ->route('operations.beneficiaries.edit', $beneficiary->reference)
            ->with('status', 'Beneficiary profile updated successfully.');
    }

    /**
     * Store an uploaded beneficiary photo on the private local disk.
     */
    private function storePhoto(?UploadedFile $photo): ?string
    {
        if ($photo === null) {
            return null;
        }

        $storedPhotoPath = $photo->store('beneficiaries/photos', 'local');

        if ($storedPhotoPath === false) {
            throw new RuntimeException('The beneficiary photo could not be stored.');
        }

        return $storedPhotoPath;
    }

    /**
     * Generate a short-lived URL without making the stored personal photo public.
     */
    private function photoUrl(?Beneficiary $beneficiary): ?string
    {
        $photoPath = $beneficiary?->photo_path;

        if ($photoPath === null || ! Storage::disk('local')->exists($photoPath)) {
            return null;
        }

        return Storage::disk('local')->temporaryUrl($photoPath, now()->addMinutes(15));
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
