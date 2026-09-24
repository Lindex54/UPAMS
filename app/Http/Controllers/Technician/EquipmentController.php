<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Http\Requests\Technician\EquipmentFilterRequest;
use App\Http\Requests\Technician\SaveEquipmentRequest;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\AssetType;
use App\Models\Campus;
use App\Models\OrgUnit;
use App\Models\User;
use App\Services\AssetReferenceGenerator;
use App\Services\IctEquipmentSpecificationService;
use App\Services\TechnicianScope;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EquipmentController extends Controller
{
    public function __construct(
        private readonly TechnicianScope $scope,
        private readonly AssetReferenceGenerator $referenceGenerator,
        private readonly IctEquipmentSpecificationService $specifications,
    ) {}

    public function index(EquipmentFilterRequest $request): View
    {
        $filters = $request->validated();
        $query = $this->scope->equipment($request->user())->with(['type', 'campus', 'computerLab', 'createdBy.role', 'updatedBy.role']);
        $query->when($filters['search'] ?? null, fn ($query, $search) => $query->where(fn ($nested) => $nested->where('reference', 'like', "%{$search}%")->orWhere('serial_number', 'like', "%{$search}%")->orWhere('name', 'like', "%{$search}%")));
        $query->when($filters['asset_id'] ?? null, fn ($query, $assetId) => $query->where('reference', 'like', "%{$assetId}%"));
        $query->when($filters['serial_number'] ?? null, fn ($query, $serialNumber) => $query->where('serial_number', 'like', "%{$serialNumber}%"));
        $query->when($filters['campus_id'] ?? null, fn ($query, $campusId) => $query->where('campus_id', $campusId));
        $query->when($filters['computer_lab_id'] ?? null, fn ($query, $labId) => $query->where('computer_lab_id', $labId));
        $query->when($filters['asset_type_id'] ?? null, fn ($query, $typeId) => $query->where('asset_type_id', $typeId));
        $query->when($filters['condition'] ?? null, fn ($query, $condition) => $query->where('condition', $condition));
        $query->when($filters['status'] ?? null, fn ($query, $status) => $query->where('operational_status', $status));
        $query->when($filters['created_by'] ?? null, fn ($query, $creatorId) => $query->where('created_by', $creatorId));
        $query->when($filters['date_from'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '>=', $date));
        $query->when($filters['date_to'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '<=', $date));

        return view('technician.equipment.index', [
            'equipment' => $query->latest()->orderByDesc('id')->paginate(15)->withQueryString(),
            ...$this->filterOptions($request),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        return view('technician.equipment.form', $this->formData($request));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SaveEquipmentRequest $request): RedirectResponse
    {
        $data = $this->scopedData($request, $request->validated());
        $type = $this->ictType($data['asset_type_id'] ?? null);
        $data['specifications'] = $this->specifications->sanitize($type, $data['specifications'] ?? []);
        $asset = Asset::query()->create([
            ...$data,
            'reference' => $this->referenceGenerator->generateIctReference(),
            'asset_category_id' => $type?->asset_category_id ?? $this->ictCategory()->id,
            'is_ict' => true,
            'status' => 'Active',
            'condition' => $data['condition'] ?? 'Good',
            'operational_status' => $data['operational_status'] ?? 'Operational',
            'created_by' => $request->user()->id,
            'updated_by' => $request->user()->id,
        ]);

        return redirect()->route('technician.equipment.show', $asset)->with('status', 'ICT equipment registered.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Asset $asset): View
    {
        $asset = $this->findScoped($request, $asset);
        $asset->load(['type', 'category', 'campus', 'orgUnit', 'computerLab', 'createdBy.role', 'updatedBy.role', 'maintenanceRequests.assignedTechnician', 'assignments.createdBy.role', 'assignments.updatedBy.role', 'inspections.technician', 'transfers.toCampus', 'documents']);

        return view('technician.equipment.show', [
            'asset' => $asset,
            'technicalSpecifications' => $this->specifications->displayValues($asset),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Asset $asset): View
    {
        $asset = $this->findScoped($request, $asset);

        return view('technician.equipment.form', [...$this->formData($request, $asset), 'asset' => $asset]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SaveEquipmentRequest $request, Asset $asset): RedirectResponse
    {
        $asset = $this->findScoped($request, $asset);
        $data = $this->scopedData($request, $request->validated());
        $type = $this->ictType($data['asset_type_id'] ?? null);
        $data['specifications'] = $this->specifications->sanitize($type, $data['specifications'] ?? []);
        foreach (['condition', 'operational_status'] as $defaultedAttribute) {
            if (! filled($data[$defaultedAttribute] ?? null)) {
                unset($data[$defaultedAttribute]);
            }
        }
        $asset->update([...$data, 'asset_category_id' => $type?->asset_category_id ?? $this->ictCategory()->id, 'is_ict' => true, 'updated_by' => $request->user()->id]);

        return redirect()->route('technician.equipment.show', $asset)->with('status', 'ICT equipment updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    private function findScoped(Request $request, Asset $asset): Asset
    {
        return $this->scope->equipment($request->user())->whereKey($asset)->firstOrFail();
    }

    /** @return array<string, mixed> */
    private function formData(Request $request, ?Asset $asset = null): array
    {
        $user = $request->user();
        $campuses = $this->scope->isAdministrator($user) ? Campus::query()->orderBy('name')->get() : Campus::query()->whereKey($user->campus_id)->get();
        $orgUnits = $this->scope->isAdministrator($user) ? OrgUnit::query()->orderBy('name')->get() : OrgUnit::query()->whereKey($user->org_unit_id)->get();

        $types = AssetType::query()->with('category')->whereHas('category', fn ($query) => $query->where('name', 'ICT Equipment'))->orderBy('name')->get();

        return [
            'types' => $types,
            'typeSpecificationSchemas' => $this->specifications->schemasForTypes($types),
            'specificationValues' => $asset ? $this->specifications->valuesFor($asset) : [],
            'campuses' => $campuses,
            'orgUnits' => $orgUnits,
            'labs' => $this->scope->labs($user)->orderBy('name')->get(),
        ];
    }

    /** @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function scopedData(Request $request, array $data): array
    {
        if (! $this->scope->isAdministrator($request->user())) {
            $data['campus_id'] = $request->user()->campus_id;
            $data['org_unit_id'] = $request->user()->org_unit_id;
        }

        if (! empty($data['computer_lab_id'])) {
            $this->scope->labs($request->user())->whereKey($data['computer_lab_id'])->firstOrFail();
        }

        return $data;
    }

    private function ictType(?int $typeId): ?AssetType
    {
        if ($typeId === null) {
            return null;
        }

        return AssetType::query()->whereKey($typeId)->whereHas('category', fn ($query) => $query->where('name', 'ICT Equipment'))->firstOrFail();
    }

    private function ictCategory(): AssetCategory
    {
        return AssetCategory::query()->where('name', 'ICT Equipment')->firstOrFail();
    }

    /** @return array<string, mixed> */
    private function filterOptions(Request $request): array
    {
        $user = $request->user();

        return [
            'filterCampuses' => $this->scope->isAdministrator($user) ? Campus::query()->orderBy('name')->get() : Campus::query()->whereKey($user->campus_id)->get(),
            'filterLabs' => $this->scope->labs($user)->orderBy('name')->get(),
            'filterTypes' => AssetType::query()->whereHas('category', fn ($query) => $query->where('name', 'ICT Equipment'))->orderBy('name')->get(),
            'filterUsers' => User::query()->with('role')->whereHas('role', fn ($query) => $query->whereIn('name', ['IT Technician', 'System Administrator']))->when(! $this->scope->isAdministrator($user), fn ($query) => $query->where('campus_id', $user->campus_id))->orderBy('name')->get(),
        ];
    }
}
