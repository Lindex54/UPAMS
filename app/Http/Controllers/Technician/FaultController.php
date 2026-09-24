<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Http\Requests\Technician\SaveFaultRequest;
use App\Models\AssetType;
use App\Models\Campus;
use App\Models\MaintenanceRequest;
use App\Models\User;
use App\Services\TechnicianScope;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FaultController extends Controller
{
    public function __construct(private readonly TechnicianScope $scope) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'asset_id' => ['nullable', 'string', 'max:100'],
            'serial_number' => ['nullable', 'string', 'max:150'],
            'campus_id' => ['nullable', 'integer', 'exists:campuses,id'],
            'computer_lab_id' => ['nullable', 'integer', 'exists:computer_labs,id'],
            'asset_type_id' => ['nullable', 'integer', 'exists:asset_types,id'],
            'condition' => ['nullable', Rule::in(['Good', 'Fair', 'Poor', 'Critical'])],
            'status' => ['nullable', Rule::in(['Reported', 'Diagnosis', 'Awaiting Repair', 'Repair In Progress', 'Testing', 'Resolved'])],
            'technician_id' => ['nullable', 'integer', 'exists:users,id'],
            'created_by' => ['nullable', 'integer', 'exists:users,id'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
        ]);
        $query = $this->scope->faults($request->user())->with(['asset.type', 'asset.computerLab', 'assignedTechnician', 'createdBy.role', 'updatedBy.role']);
        $query->when($filters['asset_id'] ?? null, fn ($query, $value) => $query->whereHas('asset', fn ($asset) => $asset->where('reference', 'like', "%{$value}%")));
        $query->when($filters['serial_number'] ?? null, fn ($query, $value) => $query->whereHas('asset', fn ($asset) => $asset->where('serial_number', 'like', "%{$value}%")));
        $query->when($filters['campus_id'] ?? null, fn ($query, $value) => $query->where('campus_id', $value));
        $query->when($filters['computer_lab_id'] ?? null, fn ($query, $value) => $query->whereHas('asset', fn ($asset) => $asset->where('computer_lab_id', $value)));
        $query->when($filters['asset_type_id'] ?? null, fn ($query, $value) => $query->whereHas('asset', fn ($asset) => $asset->where('asset_type_id', $value)));
        $query->when($filters['condition'] ?? null, fn ($query, $value) => $query->whereHas('asset', fn ($asset) => $asset->where('condition', $value)));
        $query->when($filters['status'] ?? null, fn ($query, $value) => $query->where('status', $value));
        $query->when($filters['technician_id'] ?? null, fn ($query, $value) => $query->where('assigned_technician_id', $value));
        $query->when($filters['created_by'] ?? null, fn ($query, $value) => $query->where('created_by', $value));
        $query->when($filters['date_from'] ?? null, fn ($query, $value) => $query->whereDate('created_at', '>=', $value));
        $query->when($filters['date_to'] ?? null, fn ($query, $value) => $query->whereDate('created_at', '<=', $value));

        return view('technician.faults.index', [
            'faults' => $query->latest('reported_at')->orderByDesc('id')->paginate(15)->withQueryString(),
            ...$this->filterOptions($request),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        return view('technician.faults.form', $this->formData($request));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SaveFaultRequest $request): RedirectResponse
    {
        $asset = $this->scope->equipment($request->user())->whereKey($request->integer('asset_id'))->firstOrFail();
        $data = $this->workflowDates($request->validated());
        $fault = MaintenanceRequest::query()->create([...$data, 'reference' => 'ICT-'.now()->format('ymd').'-'.Str::upper(Str::random(5)), 'campus_id' => $asset->campus_id, 'org_unit_id' => $asset->org_unit_id, 'reported_at' => now(), 'created_by' => $request->user()->id, 'updated_by' => $request->user()->id]);
        $this->syncAssetStatus($fault);

        return redirect()->route('technician.faults.show', $fault)->with('status', 'Fault recorded.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, MaintenanceRequest $fault): View
    {
        $fault = $this->findScoped($request, $fault);
        $fault->load(['asset.type', 'asset.computerLab', 'assignedTechnician', 'createdBy.role', 'updatedBy.role']);

        return view('technician.faults.show', compact('fault'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, MaintenanceRequest $fault): View
    {
        $fault = $this->findScoped($request, $fault);

        return view('technician.faults.form', [...$this->formData($request), 'fault' => $fault]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SaveFaultRequest $request, MaintenanceRequest $fault): RedirectResponse
    {
        $fault = $this->findScoped($request, $fault);
        $this->scope->equipment($request->user())->whereKey($request->integer('asset_id'))->firstOrFail();
        $fault->update([...$this->workflowDates($request->validated(), $fault), 'updated_by' => $request->user()->id]);
        $this->syncAssetStatus($fault);

        return redirect()->route('technician.faults.show', $fault)->with('status', 'Fault workflow updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    private function findScoped(Request $request, MaintenanceRequest $fault): MaintenanceRequest
    {
        return $this->scope->faults($request->user())->whereKey($fault)->firstOrFail();
    }

    /** @return array<string, mixed> */
    private function formData(Request $request): array
    {
        $user = $request->user();

        return ['equipment' => $this->scope->equipment($user)->orderBy('reference')->get(), 'technicians' => User::query()->whereHas('role', fn ($query) => $query->where('name', 'IT Technician'))->when(! $this->scope->isAdministrator($user), fn ($query) => $query->where('campus_id', $user->campus_id))->orderBy('name')->get()];
    }

    /** @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function workflowDates(array $data, ?MaintenanceRequest $fault = null): array
    {
        $rank = array_flip(['Reported', 'Diagnosis', 'Awaiting Repair', 'Repair In Progress', 'Testing', 'Resolved']);
        $current = $rank[$data['status']];
        foreach (['diagnosed_at' => 1, 'repair_started_at' => 3, 'testing_started_at' => 4, 'resolved_at' => 5] as $field => $requiredRank) {
            if ($current >= $requiredRank && $fault?->{$field} === null) {
                $data[$field] = now();
            }
        }

        return $data;
    }

    private function syncAssetStatus(MaintenanceRequest $fault): void
    {
        if ($fault->asset === null) {
            return;
        }

        $hasOtherActiveFault = $fault->asset->maintenanceRequests()->where('id', '!=', $fault->id)->whereNot('status', 'Resolved')->exists();
        $status = match (true) {
            $fault->status === 'Resolved' && ! $hasOtherActiveFault => 'Operational',
            $fault->status === 'Reported' => 'Faulty',
            default => 'Under Maintenance',
        };

        $fault->asset->update(['operational_status' => $status]);
    }

    /** @return array<string, mixed> */
    private function filterOptions(Request $request): array
    {
        $user = $request->user();

        return [
            'filterCampuses' => $this->scope->isAdministrator($user) ? Campus::query()->orderBy('name')->get() : Campus::query()->whereKey($user->campus_id)->get(),
            'filterLabs' => $this->scope->labs($user)->orderBy('name')->get(),
            'filterTypes' => AssetType::query()->whereHas('category', fn ($query) => $query->where('name', 'ICT Equipment'))->orderBy('name')->get(),
            'filterTechnicians' => User::query()->with('role')->whereHas('role', fn ($query) => $query->whereIn('name', ['IT Technician', 'System Administrator']))->when(! $this->scope->isAdministrator($user), fn ($query) => $query->where('campus_id', $user->campus_id))->orderBy('name')->get(),
        ];
    }
}
