<?php

namespace App\Http\Controllers\Campus;

use App\Http\Controllers\Controller;
use App\Models\Agreement;
use App\Models\Asset;
use App\Models\AuditLog;
use App\Models\Beneficiary;
use App\Models\Document;
use App\Models\MaintenanceRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    private const CLOSED_MAINTENANCE_STATUSES = ['Closed', 'Resolved'];

    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $campus = $user?->campus;

        abort_if($campus === null, 403, 'A campus assignment is required to access the campus dashboard.');

        $assets = Asset::query()->where('campus_id', $campus->id);
        $agreements = Agreement::query()->where('campus_id', $campus->id);
        $documents = Document::query()->where('campus_id', $campus->id);
        $maintenanceRequests = MaintenanceRequest::query()->where('campus_id', $campus->id);

        $totalAssets = (clone $assets)->count();
        $underMaintenance = (clone $assets)
            ->where(function (Builder $query): void {
                $query->whereIn('status', ['Maintenance', 'Under Maintenance'])
                    ->orWhere('operational_status', 'Under Maintenance');
            })
            ->count();
        $poorCritical = (clone $assets)->whereIn('condition', ['Poor', 'Critical'])->count();
        $openMaintenance = (clone $maintenanceRequests)->whereNotIn('status', self::CLOSED_MAINTENANCE_STATUSES)->count();
        $unassignedMaintenance = (clone $maintenanceRequests)
            ->whereNotIn('status', self::CLOSED_MAINTENANCE_STATUSES)
            ->whereNull('assigned_technician_id')
            ->count();

        return view('campus.dashboard', [
            'campus' => $campus,
            'metrics' => [
                'totalAssets' => $totalAssets,
                'assetCategories' => (clone $assets)->whereNotNull('asset_category_id')->distinct()->count('asset_category_id'),
                'activeAssets' => (clone $assets)->whereIn('status', ['Active', 'In Use'])->count(),
                'ictEquipment' => (clone $assets)->where('is_ict', true)->count(),
                'currentDocuments' => (clone $documents)->where('status', 'Current')->count(),
                'underMaintenance' => $underMaintenance,
                'expiringAgreements' => $this->expiringAgreements($agreements, 1, 90),
                'openMaintenance' => $openMaintenance,
                'unassignedMaintenance' => $unassignedMaintenance,
                'poorCritical' => $poorCritical,
            ],
            'recentActivity' => AuditLog::query()
                ->where('campus_id', $campus->id)
                ->latest('created_at')
                ->limit(6)
                ->get(),
            'maintenanceStatuses' => $this->maintenanceStatuses($maintenanceRequests, $openMaintenance),
            'agreementExpiries' => [
                ['label' => '0–30 days', 'count' => $this->expiringAgreements($agreements, 0, 30), 'classes' => 'text-red-600'],
                ['label' => '31–60 days', 'count' => $this->expiringAgreements($agreements, 31, 60), 'classes' => 'text-orange-600'],
                ['label' => '61–90 days', 'count' => $this->expiringAgreements($agreements, 61, 90), 'classes' => 'text-amber-600'],
            ],
            'conditionChart' => $this->conditionChart($assets, $totalAssets),
            'registryCounts' => [
                ['label' => 'Assets', 'count' => $totalAssets],
                ['label' => 'Agreements', 'count' => (clone $agreements)->count()],
                ['label' => 'Beneficiaries', 'count' => Beneficiary::query()->where('campus_id', $campus->id)->count()],
                ['label' => 'Documents', 'count' => (clone $documents)->count()],
            ],
            'upcomingDocuments' => (clone $documents)
                ->whereBetween('expires_at', [today(), today()->addDays(90)])
                ->whereNotIn('status', ['Expired', 'Archived', 'Superseded'])
                ->orderBy('expires_at')
                ->limit(6)
                ->get(),
        ]);
    }

    private function expiringAgreements(Builder $agreements, int $fromDay, int $toDay): int
    {
        return (clone $agreements)
            ->whereNotIn('status', ['Terminated', 'Archived'])
            ->whereBetween('expires_at', [today()->addDays($fromDay), today()->addDays($toDay)])
            ->count();
    }

    private function maintenanceStatuses(Builder $maintenanceRequests, int $openMaintenance): Collection
    {
        $colors = ['bg-slate-400', 'bg-busitema-blue', 'bg-busitema-gold', 'bg-orange-500', 'bg-violet-500'];

        return (clone $maintenanceRequests)
            ->whereNotIn('status', self::CLOSED_MAINTENANCE_STATUSES)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->orderByDesc('total')
            ->get()
            ->map(fn (MaintenanceRequest $request, int $index): array => [
                'status' => $request->status,
                'count' => (int) $request->getAttribute('total'),
                'percentage' => $this->percentage((int) $request->getAttribute('total'), $openMaintenance),
                'color' => $colors[$index % count($colors)],
            ]);
    }

    private function conditionChart(Builder $assets, int $totalAssets): Collection
    {
        $conditionColumn = $assets->getModel()->qualifyColumn('condition');
        $counts = (clone $assets)
            ->select($conditionColumn)
            ->selectRaw('count(*) as total')
            ->groupBy($conditionColumn)
            ->pluck('total', 'condition');

        return collect([
            ['label' => 'Good', 'conditions' => ['Excellent', 'Good'], 'color' => 'bg-emerald-500'],
            ['label' => 'Fair', 'conditions' => ['Fair'], 'color' => 'bg-busitema-gold'],
            ['label' => 'Poor', 'conditions' => ['Poor'], 'color' => 'bg-orange-500'],
            ['label' => 'Critical', 'conditions' => ['Critical'], 'color' => 'bg-red-500'],
        ])->map(function (array $item) use ($counts, $totalAssets): array {
            $count = collect($item['conditions'])->sum(fn (string $condition): int => (int) $counts->get($condition, 0));

            return [...$item, 'count' => $count, 'percentage' => $this->percentage($count, $totalAssets)];
        });
    }

    private function percentage(int $count, int $total): int
    {
        return $total === 0 ? 0 : (int) round(($count / $total) * 100);
    }
}
