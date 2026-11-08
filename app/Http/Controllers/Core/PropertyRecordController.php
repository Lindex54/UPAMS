<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Http\Requests\SavePropertyRecordRequest;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Campus;
use App\Models\OrgUnit;
use App\Models\User;
use App\Services\AssetReferenceGenerator;
use App\Services\PropertyModuleRegistry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\View\View;

class PropertyRecordController extends Controller
{
    public function __construct(
        private readonly PropertyModuleRegistry $registry,
        private readonly AssetReferenceGenerator $referenceGenerator,
    ) {}

    public function index(Request $request, string $module): View
    {
        $this->authorizeViewing($request->user());
        $query = $this->moduleQuery($module)->with(['category', 'campus', 'orgUnit', 'createdBy', 'updatedBy']);
        $summaryQuery = clone $query;

        $query->when($request->string('search')->toString(), function (Builder $query, string $search): void {
            $query->where(fn (Builder $nested) => $nested
                ->where('reference', 'like', "%{$search}%")
                ->orWhere('name', 'like', "%{$search}%")
                ->orWhere('serial_number', 'like', "%{$search}%"));
        });
        $query->when($request->integer('campus_id'), fn (Builder $query, int $campusId) => $query->where('campus_id', $campusId));
        $query->when($request->string('status')->toString(), fn (Builder $query, string $status) => $query->where('status', $status));

        $records = $query->latest()->orderByDesc('id')->paginate(15)->withQueryString();
        $records->through(fn (Asset $asset): array => $this->present($asset, $module));

        return view('asset-management.page', [
            'module' => $module,
            'page' => 'index',
            'liveRecords' => $records,
            'liveSummary' => [
                ['Total Records', (string) (clone $summaryQuery)->count()],
                ['Active', (string) (clone $summaryQuery)->where('status', 'Active')->count()],
                ['Attention Required', (string) (clone $summaryQuery)->where('status', 'Attention required')->count()],
                ['Mapped', (string) (clone $summaryQuery)->whereNotNull('latitude')->whereNotNull('longitude')->count()],
            ],
            ...$this->formOptions($request->user()),
        ]);
    }

    public function create(Request $request, string $module): View
    {
        $this->authorizeManaging($request->user());

        return view('asset-management.page', [
            'module' => $module,
            'page' => 'create',
            ...$this->formOptions($request->user()),
        ]);
    }

    public function store(SavePropertyRecordRequest $request, string $module): RedirectResponse
    {
        $asset = Asset::query()->create($this->attributes($request, $module));

        return redirect()->route("asset-management.{$module}.show", ['record' => $asset->reference])
            ->with('status', 'Property record created successfully.');
    }

    public function show(Request $request, string $module, string $record): View
    {
        $this->authorizeViewing($request->user());
        $asset = $this->findRecord($module, $record);

        return view('asset-management.page', [
            'module' => $module,
            'page' => 'show',
            'liveRecord' => $this->present($asset, $module),
            'canManagePropertyRecords' => $this->canManage($request->user()),
        ]);
    }

    public function edit(Request $request, string $module, string $record): View
    {
        $this->authorizeManaging($request->user());
        $asset = $this->findRecord($module, $record);

        return view('asset-management.page', [
            'module' => $module,
            'page' => 'edit',
            'liveRecord' => $this->present($asset, $module),
            ...$this->formOptions($request->user()),
        ]);
    }

    public function update(SavePropertyRecordRequest $request, string $module, string $record): RedirectResponse
    {
        $asset = $this->findRecord($module, $record);
        $asset->update($this->attributes($request, $module, $asset));

        return redirect()->route("asset-management.{$module}.show", ['record' => $asset->reference])
            ->with('status', 'Property record updated successfully.');
    }

    private function moduleQuery(string $module): Builder
    {
        abort_unless(in_array($module, $this->registry->modules(), true), 404);

        return $this->registry->constrain(Asset::query(), $module);
    }

    private function findRecord(string $module, string $record): Asset
    {
        return $this->moduleQuery($module)
            ->with(['category', 'campus', 'orgUnit', 'createdBy', 'updatedBy'])
            ->where('reference', $record)
            ->firstOrFail();
    }

    /** @return array<string, mixed> */
    private function attributes(SavePropertyRecordRequest $request, string $module, ?Asset $asset = null): array
    {
        $configuration = $this->registry->configuration($module);
        $validated = $request->validated();
        $specifications = Arr::only($validated, $configuration['fields']);

        foreach ($configuration['fields'] as $field) {
            if (($specifications[$field] ?? null) === '__other') {
                $specifications[$field] = $validated[$field.'_other'];
            }
        }

        $specifications['administrative_notes'] = $validated['administrative_notes'] ?? null;
        $categoryName = $configuration['category'] ?? $specifications['asset_category'];
        $category = AssetCategory::query()->firstOrCreate(['name' => $categoryName]);
        $user = $request->user();

        return [
            'reference' => $asset?->reference ?? $this->referenceGenerator->generatePropertyReference($configuration['prefix']),
            'name' => $specifications[$configuration['name']],
            'campus_id' => $user->isCampusScoped() ? $user->campus_id : $validated['campus_id'],
            'org_unit_id' => $user->isCampusScoped() ? $user->org_unit_id : ($validated['org_unit_id'] ?? null),
            'asset_category_id' => $category->id,
            'status' => ($validated['intent'] ?? 'save') === 'draft' ? 'Draft' : $validated['record_status'],
            'condition' => $specifications['condition'] ?? $specifications['infrastructure_condition'] ?? 'Good',
            'acquired_at' => $specifications['acquisition_date'] ?? null,
            'serial_number' => $specifications['serial_number'] ?? null,
            'building' => $specifications['physical_location'] ?? null,
            'custodian' => $specifications['custodian'] ?? $specifications['facility_manager'] ?? $specifications['property_manager'] ?? $specifications['property_custodian'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'specifications' => array_filter($specifications, fn (mixed $value): bool => filled($value)),
            'is_ict' => false,
            'operational_status' => 'Operational',
            'created_by' => $asset?->created_by ?? $user->id,
            'updated_by' => $user->id,
        ];
    }

    /** @return array<string, mixed> */
    private function present(Asset $asset, string $module): array
    {
        $configuration = $this->registry->configuration($module);
        $specifications = $asset->specifications ?? [];

        return [
            'reference' => $asset->reference,
            'name' => $asset->name,
            'campus' => $asset->campus?->name ?? 'Not assigned',
            'campusId' => $asset->campus_id,
            'orgUnitId' => $asset->org_unit_id,
            'latitude' => $asset->latitude,
            'longitude' => $asset->longitude,
            'primary' => $specifications[$configuration['primary']] ?? $asset->category?->name ?? 'Not recorded',
            'secondary' => $specifications[$configuration['secondary']] ?? $asset->building ?? 'Not recorded',
            'status' => $asset->status,
            'statusClass' => $this->statusClass($asset->status),
            'addedBy' => $asset->createdBy?->name ?? 'System',
            'dateAdded' => $asset->created_at?->format('d M Y') ?? 'Not recorded',
            'updatedBy' => $asset->updatedBy?->name ?? 'System',
            'dateUpdated' => $asset->updated_at?->format('d M Y') ?? 'Not recorded',
            'fieldValues' => $specifications,
        ];
    }

    /** @return array<string, mixed> */
    private function formOptions(User $user): array
    {
        return [
            'campuses' => Campus::query()->orderBy('name')->get(),
            'orgUnits' => $user->isCampusScoped()
                ? OrgUnit::query()->whereKey($user->org_unit_id)->get()
                : OrgUnit::query()->orderBy('name')->get(),
            'canManagePropertyRecords' => $this->canManage($user),
        ];
    }

    private function statusClass(string $status): string
    {
        return match ($status) {
            'Active' => 'bg-emerald-50 text-emerald-700',
            'Attention required' => 'bg-amber-50 text-amber-700',
            'Inactive', 'Archived' => 'bg-slate-100 text-slate-700',
            default => 'bg-blue-50 text-busitema-blue',
        };
    }

    private function authorizeViewing(?User $user): void
    {
        abort_unless($user?->is_active === true && in_array($user->role?->name, [
            'System Administrator',
            'University Management',
            'University Property / Estates Manager',
            'Campus Property Officer',
        ], true), 403);
    }

    private function authorizeManaging(?User $user): void
    {
        abort_unless($this->canManage($user), 403);
    }

    private function canManage(?User $user): bool
    {
        return $user?->is_active === true && in_array($user->role?->name, [
            'System Administrator',
            'University Property / Estates Manager',
            'Campus Property Officer',
        ], true);
    }
}
