<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AuditLog;
use App\Models\ComputerLab;
use App\Models\Document;
use App\Models\IctEquipmentAssignment;
use App\Models\IctInspection;
use App\Models\IctTransferRequest;
use App\Models\MaintenanceRequest;
use App\Services\TechnicianScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityController extends Controller
{
    public function __construct(private readonly TechnicianScope $scope) {}

    public function __invoke(Request $request, string $type, int $record): View
    {
        $model = $this->findRecord($request, $type, $record);
        $model->load(['createdBy.role', 'updatedBy.role']);

        return view('technician.activity.show', [
            'record' => $model,
            'recordType' => $this->labels()[$type],
            'logs' => AuditLog::query()->where('auditable_type', $model->getMorphClass())->where('auditable_id', $model->getKey())->latest('created_at')->get(),
        ]);
    }

    private function findRecord(Request $request, string $type, int $record): Model
    {
        $user = $request->user();
        $equipmentIds = $this->scope->equipment($user)->select('id');
        $labIds = $this->scope->labs($user)->select('id');

        $query = match ($type) {
            'equipment' => $this->scope->equipment($user),
            'labs' => $this->scope->labs($user),
            'faults' => $this->scope->faults($user),
            'assignments' => IctEquipmentAssignment::query()->whereIn('asset_id', $equipmentIds),
            'inspections' => IctInspection::query()->where(fn (Builder $query) => $query->whereIn('asset_id', $equipmentIds)->orWhereIn('computer_lab_id', $labIds)),
            'transfers' => IctTransferRequest::query()->whereIn('asset_id', $equipmentIds),
            'documents' => Document::query()->whereIn('asset_id', $equipmentIds),
            default => abort(404),
        };

        return $query->whereKey($record)->firstOrFail();
    }

    /** @return array<string, string> */
    private function labels(): array
    {
        return [
            'equipment' => class_basename(Asset::class),
            'labs' => class_basename(ComputerLab::class),
            'faults' => class_basename(MaintenanceRequest::class),
            'assignments' => class_basename(IctEquipmentAssignment::class),
            'inspections' => class_basename(IctInspection::class),
            'transfers' => class_basename(IctTransferRequest::class),
            'documents' => class_basename(Document::class),
        ];
    }
}
