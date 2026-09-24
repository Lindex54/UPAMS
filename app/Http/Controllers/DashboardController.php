<?php

namespace App\Http\Controllers;

use App\Models\Agreement;
use App\Models\Approval;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\AssetType;
use App\Models\AuditLog;
use App\Models\Campus;
use App\Models\Document;
use App\Models\MaintenanceRequest;
use App\Models\Notification;
use App\Models\OrgUnit;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $filters = $request->validate([
            'campus_id' => ['nullable', 'integer', 'exists:campuses,id'],
            'asset_type_id' => ['nullable', 'integer', 'exists:asset_types,id'],
            'status' => ['nullable', Rule::in(['active', 'maintenance', 'poor_critical', 'retired'])],
            'org_unit_id' => ['nullable', 'integer', 'exists:org_units,id'],
            'date_range' => ['nullable', Rule::in(['current_financial_year', 'last_30_days', 'last_quarter', 'last_12_months'])],
        ]);
        $filters['date_range'] ??= 'current_financial_year';
        $dateStart = $this->dateStart($filters['date_range']);

        $assets = $this->assetQuery($filters, $dateStart);
        $totalAssets = (clone $assets)->count();
        $underMaintenance = (clone $assets)->whereIn('status', ['Maintenance', 'Under Maintenance'])->count();
        $poorCritical = (clone $assets)->whereIn('condition', ['Poor', 'Critical'])->count();

        $users = $this->commonFilters(User::query(), $filters, $dateStart, supportsOrgUnit: true);
        $totalUsers = (clone $users)->count();
        $activeUsers = (clone $users)->where('is_active', true)->count();
        $newUsers = (clone $users)->where('is_active', true)->where('created_at', '>=', now()->subDays(30))->count();
        $inactiveUsers = (clone $users)->where('is_active', false)->count();

        $agreements = $this->commonFilters(Agreement::query(), $filters, $dateStart, supportsOrgUnit: true);
        $activeAgreements = (clone $agreements)->where('status', 'Active')->count();

        $approvals = $this->commonFilters(Approval::query(), $filters, $dateStart);
        $pendingApprovals = (clone $approvals)->where('status', 'Pending')->count();

        $documents = $this->commonFilters(Document::query(), $filters, $dateStart, supportsOrgUnit: true);
        $expiringDocuments = (clone $documents)->whereBetween('expires_at', [today(), today()->addDays(90)])->whereNotIn('status', ['Expired', 'Archived', 'Superseded'])->count();

        $maintenance = $this->commonFilters(MaintenanceRequest::query(), $filters, $dateStart, supportsOrgUnit: true);
        $highPriorityMaintenance = (clone $maintenance)->whereIn('priority', ['High', 'Emergency'])->where('status', '!=', 'Closed')->count();

        $notifications = $this->commonFilters(Notification::query(), $filters, $dateStart);
        $failedNotifications = (clone $notifications)->where('status', 'Failed')->count();

        $attentionItems = $this->attentionItems($assets, $agreements, $documents, $maintenance, $approvals);
        $attentionTotal = collect($attentionItems)->sum('count');

        return view('dashboard', [
            'filters' => $filters,
            'filterOptions' => [
                'campuses' => Campus::query()->orderBy('name')->get(['id', 'name']),
                'assetTypes' => AssetType::query()->orderBy('name')->get(['id', 'name']),
                'orgUnits' => OrgUnit::query()->orderBy('name')->get(['id', 'name']),
            ],
            'metrics' => [
                'totalAssets' => $totalAssets,
                'totalUsers' => $totalUsers,
                'activeUsers' => $activeUsers,
                'underMaintenance' => $underMaintenance,
                'highPriorityMaintenance' => $highPriorityMaintenance,
                'poorCritical' => $poorCritical,
                'poorCriticalPercentage' => $this->percentage($poorCritical, $totalAssets),
                'activeAgreements' => $activeAgreements,
                'pendingApprovals' => $pendingApprovals,
                'expiringDocuments' => $expiringDocuments,
                'outstandingAlerts' => $attentionTotal + $failedNotifications,
                'criticalAlerts' => $poorCritical + $highPriorityMaintenance + $failedNotifications,
            ],
            'categoryChart' => $this->categoryChart($filters, $dateStart, $totalAssets),
            'campusChart' => $this->campusChart($filters, $dateStart),
            'trendChart' => $this->trendChart($assets),
            'conditionChart' => $this->conditionChart($assets, $totalAssets),
            'attentionItems' => collect($attentionItems),
            'attentionTotal' => $attentionTotal,
            'userChart' => [
                ['label' => 'Active', 'count' => max(0, $activeUsers - $newUsers), 'color' => 'bg-emerald-500', 'text' => 'text-emerald-700'],
                ['label' => 'New', 'count' => $newUsers, 'color' => 'bg-busitema-gold', 'text' => 'text-amber-700'],
                ['label' => 'Inactive', 'count' => $inactiveUsers, 'color' => 'bg-slate-300', 'text' => 'text-slate-600'],
            ],
            'health' => [
                'database' => 'Connected',
                'queuedJobs' => (int) DB::table('jobs')->count(),
                'failedJobs' => (int) DB::table('failed_jobs')->count(),
                'lastActivity' => AuditLog::query()->max('created_at'),
            ],
            'recentActivity' => $this->recentActivity($filters, $dateStart),
            'updatedAt' => now(),
        ]);
    }

    /** @param array<string, mixed> $filters */
    private function assetQuery(array $filters, CarbonImmutable $dateStart, ?Builder $query = null): Builder
    {
        return $this->commonFilters($query ?? Asset::query(), $filters, $dateStart, supportsOrgUnit: true)
            ->when($filters['asset_type_id'] ?? null, fn (Builder $query, int $id): Builder => $query->where('asset_type_id', $id))
            ->when($filters['status'] ?? null, function (Builder $query, string $status): void {
                match ($status) {
                    'active' => $query->whereIn('status', ['Active', 'In Use']),
                    'maintenance' => $query->whereIn('status', ['Maintenance', 'Under Maintenance']),
                    'poor_critical' => $query->whereIn('condition', ['Poor', 'Critical']),
                    'retired' => $query->whereIn('status', ['Retired', 'Disposed']),
                };
            });
    }

    /** @param array<string, mixed> $filters */
    private function commonFilters(Builder $query, array $filters, CarbonImmutable $dateStart, bool $supportsOrgUnit = false): Builder
    {
        return $query
            ->when($filters['campus_id'] ?? null, fn (Builder $builder, int $id): Builder => $builder->where('campus_id', $id))
            ->when($supportsOrgUnit ? ($filters['org_unit_id'] ?? null) : null, fn (Builder $builder, int $id): Builder => $builder->where('org_unit_id', $id))
            ->where('created_at', '>=', $dateStart);
    }

    private function dateStart(string $range): CarbonImmutable
    {
        $today = CarbonImmutable::today();

        return match ($range) {
            'last_30_days' => $today->subDays(29),
            'last_quarter' => $today->subMonths(3),
            'last_12_months' => $today->subMonths(12),
            default => $today->month >= 7 ? $today->startOfYear()->addMonths(6) : $today->subYear()->startOfYear()->addMonths(6),
        };
    }

    /** @param array<string, mixed> $filters */
    private function categoryChart(array $filters, CarbonImmutable $dateStart, int $totalAssets): Collection
    {
        $colors = ['#1e73be', '#5599e8', '#f9d028', '#10b981', '#f97316', '#8b5cf6', '#ec4899'];

        return AssetCategory::query()
            ->withCount(['assets' => fn (Builder $query): Builder => $this->assetQuery($filters, $dateStart, $query)])
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(function (AssetCategory $category, int $index) use ($totalAssets, $colors): array {
                return ['name' => $category->name, 'count' => $category->assets_count, 'percentage' => $this->percentage($category->assets_count, $totalAssets), 'color' => $colors[$index % count($colors)]];
            })->filter(fn (array $category): bool => $category['count'] > 0)->values();
    }

    /** @param array<string, mixed> $filters */
    private function campusChart(array $filters, CarbonImmutable $dateStart): Collection
    {
        return Campus::query()
            ->withCount(['assets' => function (Builder $query) use ($filters, $dateStart): Builder {
                $assetFilters = $filters;
                unset($assetFilters['campus_id']);

                return $this->assetQuery($assetFilters, $dateStart, $query);
            }])
            ->when($filters['campus_id'] ?? null, fn (Builder $query, int $id): Builder => $query->whereKey($id))
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (Campus $campus): array => ['name' => str($campus->name)->before(' Campus')->toString(), 'count' => $campus->assets_count]);
    }

    private function trendChart(Builder $assets): Collection
    {
        $months = collect(range(11, 0))->map(fn (int $monthsAgo): CarbonImmutable => CarbonImmutable::today()->subMonths($monthsAgo)->startOfMonth());
        $dateExpression = $assets->getModel()->getConnection()->getDriverName() === 'sqlite'
            ? "strftime('%Y-%m', created_at)"
            : "date_format(created_at, '%Y-%m')";
        $counts = (clone $assets)->selectRaw("{$dateExpression} as period, count(*) as total")->groupBy('period')->pluck('total', 'period');
        $maximum = max(1, (int) $counts->max());

        return $months->map(function (CarbonImmutable $month, int $index) use ($counts, $maximum): array {
            $count = (int) $counts->get($month->format('Y-m'), 0);

            return [
                'label' => $month->format('M'),
                'count' => $count,
                'x' => round(42 + ($index * (662 / 11)), 2),
                'y' => round(176 - (($count / $maximum) * 137), 2),
            ];
        });
    }

    private function conditionChart(Builder $assets, int $totalAssets): Collection
    {
        return collect([
            ['label' => 'Good / Operational', 'conditions' => ['Excellent', 'Good'], 'color' => 'bg-emerald-500'],
            ['label' => 'Fair condition', 'conditions' => ['Fair'], 'color' => 'bg-busitema-blue'],
            ['label' => 'Under maintenance', 'conditions' => [], 'color' => 'bg-busitema-gold'],
            ['label' => 'Poor / Critical', 'conditions' => ['Poor', 'Critical'], 'color' => 'bg-red-500'],
        ])->map(function (array $item) use ($assets, $totalAssets): array {
            $count = $item['conditions'] === []
                ? (clone $assets)->whereIn('status', ['Maintenance', 'Under Maintenance'])->count()
                : (clone $assets)->whereIn('condition', $item['conditions'])->whereNotIn('status', ['Maintenance', 'Under Maintenance'])->count();

            return [...$item, 'count' => $count, 'percentage' => $this->percentage($count, $totalAssets)];
        });
    }

    /** @return list<array{title: string, detail: string, count: int, classes: string}> */
    private function attentionItems(Builder $assets, Builder $agreements, Builder $documents, Builder $maintenance, Builder $approvals): array
    {
        $expiringAgreements = (clone $agreements)->where('status', 'Active')->whereBetween('expires_at', [today(), today()->addDays(30)])->count();
        $expiringDocuments = (clone $documents)->whereBetween('expires_at', [today(), today()->addDays(60)])->whereNotIn('status', ['Expired', 'Archived', 'Superseded'])->count();
        $vehiclesDue = (clone $assets)->whereNotNull('next_service_at')->whereDate('next_service_at', '<=', today()->addDays(30))->count();
        $calibrationsDue = (clone $assets)->whereNotNull('calibration_due_at')->whereDate('calibration_due_at', '<=', today()->addDays(30))->count();
        $highPriority = (clone $maintenance)->whereIn('priority', ['High', 'Emergency'])->where('status', '!=', 'Closed')->count();
        $pendingApprovals = (clone $approvals)->where('status', 'Pending')->count();

        return [
            ['title' => 'Expiring agreements', 'detail' => 'Renewals due within 30 days', 'count' => $expiringAgreements, 'classes' => 'bg-red-50 text-red-700'],
            ['title' => 'Documents & licences', 'detail' => 'Records expiring within 60 days', 'count' => $expiringDocuments, 'classes' => 'bg-orange-50 text-orange-700'],
            ['title' => 'Vehicles due for service', 'detail' => 'Service dates reached within 30 days', 'count' => $vehiclesDue, 'classes' => 'bg-amber-50 text-amber-700'],
            ['title' => 'Laboratory calibration', 'detail' => 'Calibration dates reached within 30 days', 'count' => $calibrationsDue, 'classes' => 'bg-violet-50 text-violet-700'],
            ['title' => 'Maintenance requests', 'detail' => 'High-priority requests are open', 'count' => $highPriority, 'classes' => 'bg-blue-50 text-busitema-blue'],
            ['title' => 'Approvals', 'detail' => 'Submissions await administrator review', 'count' => $pendingApprovals, 'classes' => 'bg-emerald-50 text-emerald-700'],
        ];
    }

    /** @param array<string, mixed> $filters */
    private function recentActivity(array $filters, CarbonImmutable $dateStart): Collection
    {
        return $this->commonFilters(AuditLog::query(), $filters, $dateStart)
            ->latest('created_at')
            ->limit(8)
            ->get();
    }

    private function percentage(int $count, int $total): int
    {
        return $total === 0 ? 0 : (int) round(($count / $total) * 100);
    }
}
