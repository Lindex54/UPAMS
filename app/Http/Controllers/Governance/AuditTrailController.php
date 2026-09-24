<?php

namespace App\Http\Controllers\Governance;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Campus;
use App\Support\CsvExporter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditTrailController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.audit.index', ['logs' => $this->query($request)->paginate(20)->withQueryString(), 'campuses' => Campus::query()->orderBy('name')->get(), 'actions' => AuditLog::query()->distinct()->orderBy('action')->pluck('action')]);
    }

    public function show(AuditLog $auditLog): View
    {
        return view('admin.audit.show', compact('auditLog'));
    }

    public function export(Request $request): mixed
    {
        $rows = $this->query($request)->cursor()->map(fn (AuditLog $log): array => [$log->created_at->format('Y-m-d H:i:s'), $log->user_id, $log->user_name, $log->user_role, $log->campus?->name, $log->action, $log->auditable_type, $log->auditable_id, $log->description, json_encode($log->old_values), json_encode($log->new_values), $log->ip_address]);

        return CsvExporter::download('upams-audit-trail-'.today()->format('Y-m-d').'.csv', ['Created At', 'User ID', 'User Name', 'User Role', 'Campus', 'Action', 'Record Type', 'Record ID', 'Description', 'Previous Values', 'New Values', 'IP Address'], $rows);
    }

    private function query(Request $request): Builder
    {
        return AuditLog::query()->with('campus')->when($request->string('q')->toString(), fn (Builder $query, string $q): Builder => $query->where(fn (Builder $nested): Builder => $nested->where('description', 'like', "%{$q}%")->orWhere('user_name', 'like', "%{$q}%")))
            ->when($request->string('action')->toString(), fn (Builder $query, string $action): Builder => $query->where('action', $action))->when($request->integer('campus_id'), fn (Builder $query, int $id): Builder => $query->where('campus_id', $id))->latest('created_at');
    }
}
