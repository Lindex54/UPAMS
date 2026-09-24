<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Http\Requests\Technician\SaveComputerLabRequest;
use App\Models\Campus;
use App\Models\ComputerLab;
use App\Models\OrgUnit;
use App\Models\User;
use App\Services\TechnicianScope;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ComputerLabController extends Controller
{
    public function __construct(private readonly TechnicianScope $scope) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'campus_id' => ['nullable', 'integer', 'exists:campuses,id'],
            'technician_id' => ['nullable', 'integer', 'exists:users,id'],
            'created_by' => ['nullable', 'integer', 'exists:users,id'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
        ]);
        $labs = $this->scope->labs($request->user())->with(['campus', 'responsibleTechnician', 'createdBy.role', 'updatedBy.role'])
            ->withCount(['equipment', 'equipment as operational_count' => fn ($query) => $query->where('operational_status', 'Operational'), 'equipment as faulty_count' => fn ($query) => $query->where('operational_status', 'Faulty')])
            ->when($filters['name'] ?? null, fn ($query, $name) => $query->where('name', 'like', "%{$name}%"))
            ->when($filters['campus_id'] ?? null, fn ($query, $campusId) => $query->where('campus_id', $campusId))
            ->when($filters['technician_id'] ?? null, fn ($query, $technicianId) => $query->where('responsible_technician_id', $technicianId))
            ->when($filters['created_by'] ?? null, fn ($query, $creatorId) => $query->where('created_by', $creatorId))
            ->when($filters['date_from'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '<=', $date))
            ->orderBy('name')->paginate(12)->withQueryString();

        return view('technician.labs.index', [
            'labs' => $labs,
            ...$this->filterOptions($request),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        return view('technician.labs.form', $this->formData($request));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SaveComputerLabRequest $request): RedirectResponse
    {
        $data = $this->scopedData($request, $request->validated());
        $lab = ComputerLab::query()->create([...$data, 'created_by' => $request->user()->id, 'updated_by' => $request->user()->id]);

        return redirect()->route('technician.labs.show', $lab)->with('status', 'Computer lab registered.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, ComputerLab $lab): View
    {
        $lab = $this->findScoped($request, $lab);
        $lab->load(['campus', 'orgUnit', 'responsibleTechnician', 'createdBy.role', 'updatedBy.role', 'equipment.type']);

        return view('technician.labs.show', compact('lab'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, ComputerLab $lab): View
    {
        $lab = $this->findScoped($request, $lab);

        return view('technician.labs.form', [...$this->formData($request), 'lab' => $lab]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SaveComputerLabRequest $request, ComputerLab $lab): RedirectResponse
    {
        $lab = $this->findScoped($request, $lab);
        $lab->update([...$this->scopedData($request, $request->validated()), 'updated_by' => $request->user()->id]);

        return redirect()->route('technician.labs.show', $lab)->with('status', 'Computer lab updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    private function findScoped(Request $request, ComputerLab $lab): ComputerLab
    {
        return $this->scope->labs($request->user())->whereKey($lab)->firstOrFail();
    }

    /** @return array<string, mixed> */
    private function formData(Request $request): array
    {
        $user = $request->user();

        return [
            'campuses' => $this->scope->isAdministrator($user) ? Campus::query()->orderBy('name')->get() : Campus::query()->whereKey($user->campus_id)->get(),
            'orgUnits' => $this->scope->isAdministrator($user) ? OrgUnit::query()->orderBy('name')->get() : OrgUnit::query()->whereKey($user->org_unit_id)->get(),
            'technicians' => User::query()->whereHas('role', fn ($query) => $query->where('name', 'IT Technician'))->when(! $this->scope->isAdministrator($user), fn ($query) => $query->where('campus_id', $user->campus_id))->orderBy('name')->get(),
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
            $data['responsible_technician_id'] = $request->user()->id;
        }

        return $data;
    }

    /** @return array<string, mixed> */
    private function filterOptions(Request $request): array
    {
        $user = $request->user();

        return [
            'filterCampuses' => $this->scope->isAdministrator($user) ? Campus::query()->orderBy('name')->get() : Campus::query()->whereKey($user->campus_id)->get(),
            'filterTechnicians' => User::query()->with('role')->whereHas('role', fn ($query) => $query->whereIn('name', ['IT Technician', 'System Administrator']))->when(! $this->scope->isAdministrator($user), fn ($query) => $query->where('campus_id', $user->campus_id))->orderBy('name')->get(),
        ];
    }
}
