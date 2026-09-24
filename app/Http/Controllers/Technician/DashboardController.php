<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Services\TechnicianScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private readonly TechnicianScope $scope) {}

    public function __invoke(Request $request): View
    {
        $equipment = $this->scope->equipment($request->user());
        $faults = $this->scope->faults($request->user());

        $metrics = [
            'total' => (clone $equipment)->count(),
            'operational' => (clone $equipment)->where('operational_status', 'Operational')->count(),
            'faulty' => (clone $equipment)->where('operational_status', 'Faulty')->count(),
            'maintenance' => (clone $equipment)->where('operational_status', 'Under Maintenance')->count(),
            'unassigned' => (clone $equipment)->where(fn (Builder $query) => $query->where('operational_status', 'Unassigned')->orWhere(fn (Builder $location) => $location->whereNull('computer_lab_id')->whereNull('custodian')))->count(),
            'warranties' => (clone $equipment)->whereBetween('warranty_expires_at', [today(), today()->addDays(60)])->count(),
        ];

        return view('technician.dashboard', [
            'metrics' => $metrics,
            'equipmentByType' => (clone $equipment)->join('asset_types', 'assets.asset_type_id', '=', 'asset_types.id')->selectRaw('asset_types.name as label, count(*) as total')->groupBy('asset_types.id', 'asset_types.name')->orderByDesc('total')->limit(6)->get(),
            'equipmentByStatus' => (clone $equipment)->selectRaw('operational_status as label, count(*) as total')->groupBy('operational_status')->get(),
            'recentFaults' => (clone $faults)->with(['asset', 'assignedTechnician'])->latest('reported_at')->limit(5)->get(),
            'attentionEquipment' => (clone $equipment)->with(['type', 'computerLab'])->where(fn (Builder $query) => $query->whereIn('condition', ['Poor', 'Critical'])->orWhereIn('operational_status', ['Faulty', 'Under Maintenance']))->limit(5)->get(),
            'recentRepairs' => (clone $faults)->with('asset')->where('status', 'Resolved')->latest('resolved_at')->limit(5)->get(),
            'warrantyExpiries' => (clone $equipment)->with('type')->whereBetween('warranty_expires_at', [today(), today()->addDays(90)])->orderBy('warranty_expires_at')->limit(5)->get(),
        ]);
    }
}
