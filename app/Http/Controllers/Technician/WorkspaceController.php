<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Models\Campus;
use App\Models\Document;
use App\Models\IctEquipmentAssignment;
use App\Models\IctInspection;
use App\Models\IctTransferRequest;
use App\Services\TechnicianScope;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class WorkspaceController extends Controller
{
    public function __construct(private readonly TechnicianScope $scope) {}

    public function assignments(Request $request): View
    {
        $equipment = $this->scope->equipment($request->user())->with(['computerLab', 'assignments' => fn ($query) => $query->with(['asset', 'createdBy.role', 'updatedBy.role'])->latest('assigned_at')])->orderBy('reference')->get();

        return $this->page('assignments', ['equipment' => $equipment, 'labs' => $this->scope->labs($request->user())->orderBy('name')->get()]);
    }

    public function storeAssignment(Request $request): RedirectResponse
    {
        $data = $request->validate(['asset_id' => ['required', 'exists:assets,id'], 'computer_lab_id' => ['nullable', 'exists:computer_labs,id'], 'custodian' => ['nullable', 'string', 'max:255'], 'notes' => ['nullable', 'string', 'max:2000']]);
        $asset = $this->scope->equipment($request->user())->whereKey($data['asset_id'])->firstOrFail();
        $lab = empty($data['computer_lab_id']) ? null : $this->scope->labs($request->user())->whereKey($data['computer_lab_id'])->firstOrFail();
        IctEquipmentAssignment::query()->where('asset_id', $asset->id)->whereNull('returned_at')->get()->each->update(['returned_at' => now(), 'updated_by' => $request->user()->id]);
        IctEquipmentAssignment::query()->create([...$data, 'assigned_by' => $request->user()->id, 'assigned_at' => now(), 'created_by' => $request->user()->id, 'updated_by' => $request->user()->id]);
        $asset->update(['computer_lab_id' => $lab?->id, 'custodian' => $data['custodian'] ?? null, 'building' => $lab?->building ?? $asset->building, 'room' => $lab?->room ?? $asset->room, 'operational_status' => $lab === null && empty($data['custodian']) ? 'Unassigned' : $asset->operational_status, 'updated_by' => $request->user()->id]);

        return back()->with('status', 'Equipment assignment updated.');
    }

    public function maintenance(Request $request): View
    {
        $faults = $this->scope->faults($request->user())->with(['asset', 'assignedTechnician', 'createdBy.role', 'updatedBy.role'])->whereNot('status', 'Resolved')->latest('reported_at')->get();

        return $this->page('maintenance', compact('faults'));
    }

    public function inspections(Request $request): View
    {
        $assetIds = $this->scope->equipment($request->user())->pluck('id');
        $inspections = IctInspection::query()->with(['asset', 'computerLab', 'technician', 'createdBy.role', 'updatedBy.role'])->whereIn('asset_id', $assetIds)->latest('inspected_at')->get();

        return $this->page('inspections', ['inspections' => $inspections, 'equipment' => $this->scope->equipment($request->user())->orderBy('reference')->get()]);
    }

    public function storeInspection(Request $request): RedirectResponse
    {
        $data = $request->validate(['asset_id' => ['required', 'exists:assets,id'], 'condition' => ['required', Rule::in(['Good', 'Fair', 'Poor', 'Critical'])], 'operational_status' => ['required', Rule::in(['Operational', 'Faulty', 'Under Maintenance', 'Unassigned', 'Retired'])], 'findings' => ['required', 'string', 'max:5000'], 'next_due_at' => ['nullable', 'date', 'after_or_equal:today']]);
        $asset = $this->scope->equipment($request->user())->whereKey($data['asset_id'])->firstOrFail();
        IctInspection::query()->create([...$data, 'computer_lab_id' => $asset->computer_lab_id, 'technician_id' => $request->user()->id, 'inspected_at' => now(), 'created_by' => $request->user()->id, 'updated_by' => $request->user()->id]);
        $asset->update(['condition' => $data['condition'], 'operational_status' => $data['operational_status'], 'updated_by' => $request->user()->id]);

        return back()->with('status', 'Inspection recorded.');
    }

    public function transfers(Request $request): View
    {
        $assetIds = $this->scope->equipment($request->user())->pluck('id');
        $transfers = IctTransferRequest::query()->with(['asset', 'fromCampus', 'toCampus', 'createdBy.role', 'updatedBy.role'])->whereIn('asset_id', $assetIds)->latest('requested_at')->get();

        return $this->page('transfers', ['transfers' => $transfers, 'equipment' => $this->scope->equipment($request->user())->orderBy('reference')->get(), 'campuses' => Campus::query()->orderBy('name')->get()]);
    }

    public function storeTransfer(Request $request): RedirectResponse
    {
        $data = $request->validate(['asset_id' => ['required', 'exists:assets,id'], 'to_campus_id' => ['required', 'exists:campuses,id'], 'reason' => ['required', 'string', 'max:3000']]);
        $asset = $this->scope->equipment($request->user())->whereKey($data['asset_id'])->firstOrFail();
        abort_if($asset->campus_id === (int) $data['to_campus_id'], 422, 'Destination campus must be different.');
        IctTransferRequest::query()->create([...$data, 'reference' => 'TRF-'.now()->format('ymd').'-'.Str::upper(Str::random(5)), 'from_campus_id' => $asset->campus_id, 'requested_by' => $request->user()->id, 'requested_at' => now(), 'created_by' => $request->user()->id, 'updated_by' => $request->user()->id]);

        return back()->with('status', 'Transfer request submitted.');
    }

    public function updateTransfer(Request $request, IctTransferRequest $transfer): RedirectResponse
    {
        abort_unless($this->scope->isAdministrator($request->user()), 403);
        $this->scope->equipment($request->user())->whereKey($transfer->asset_id)->firstOrFail();
        $data = $request->validate(['status' => ['required', Rule::in(['Requested', 'Approved', 'Rejected', 'Completed'])]]);
        $transfer->update([...$data, 'updated_by' => $request->user()->id]);

        return back()->with('status', 'Transfer status updated.');
    }

    public function documents(Request $request): View
    {
        $assetIds = $this->scope->equipment($request->user())->pluck('id');
        $documents = Document::query()->with(['asset', 'createdBy.role', 'updatedBy.role'])->whereIn('asset_id', $assetIds)->latest()->get();

        return $this->page('documents', ['documents' => $documents, 'equipment' => $this->scope->equipment($request->user())->orderBy('reference')->get()]);
    }

    public function storeDocument(Request $request): RedirectResponse
    {
        $data = $request->validate(['asset_id' => ['required', 'exists:assets,id'], 'title' => ['required', 'string', 'max:255'], 'document_type' => ['required', 'string', 'max:100'], 'expires_at' => ['nullable', 'date'], 'document' => ['required', 'file', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png', 'max:10240']]);
        $asset = $this->scope->equipment($request->user())->whereKey($data['asset_id'])->firstOrFail();
        $path = $request->file('document')->store('ict-documents');
        Document::query()->create(['reference' => 'ICT-DOC-'.now()->format('ymd').'-'.Str::upper(Str::random(5)), 'title' => $data['title'], 'document_type' => $data['document_type'], 'related_reference' => $asset->reference, 'file_path' => $path, 'asset_id' => $asset->id, 'campus_id' => $asset->campus_id, 'org_unit_id' => $asset->org_unit_id, 'status' => 'Current', 'expires_at' => $data['expires_at'] ?? null, 'created_by' => $request->user()->id, 'updated_by' => $request->user()->id]);

        return back()->with('status', 'Technical document uploaded.');
    }

    public function downloadDocument(Request $request, Document $document): StreamedResponse
    {
        $this->scope->equipment($request->user())->whereKey($document->asset_id)->firstOrFail();
        abort_unless($document->file_path && Storage::exists($document->file_path), 404);

        return Storage::download($document->file_path, basename($document->file_path));
    }

    public function reports(Request $request): View
    {
        return $this->page('reports', ['equipment' => $this->scope->equipment($request->user())->with(['type', 'campus', 'computerLab', 'createdBy.role', 'updatedBy.role'])->orderBy('reference')->get()]);
    }

    public function exportReport(Request $request): StreamedResponse
    {
        $equipment = $this->scope->equipment($request->user())->with(['type', 'campus', 'computerLab', 'createdBy.role', 'updatedBy.role'])->orderBy('reference')->get();

        return response()->streamDownload(function () use ($equipment): void {
            $stream = fopen('php://output', 'w');
            fputcsv($stream, ['Asset ID', 'Serial Number', 'Type', 'Campus', 'Lab', 'Condition', 'Operational Status', 'Warranty Expiry', 'Added By User ID', 'Added By Name', 'Added By Role', 'Added At', 'Last Updated By User ID', 'Last Updated By Name', 'Last Updated By Role', 'Last Updated At']);
            foreach ($equipment as $asset) {
                fputcsv($stream, [$asset->reference, $asset->serial_number, $asset->type?->name, $asset->campus?->name, $asset->computerLab?->name, $asset->condition, $asset->operational_status, $asset->warranty_expires_at?->toDateString(), $asset->created_by, $asset->createdBy?->name, $asset->createdBy?->role?->name, $asset->created_at?->toDateTimeString(), $asset->updated_by, $asset->updatedBy?->name, $asset->updatedBy?->role?->name, $asset->updated_at?->toDateTimeString()]);
            }
            fclose($stream);
        }, 'ict-equipment-report-'.today()->format('Y-m-d').'.csv');
    }

    /** @param array<string, mixed> $data */
    private function page(string $page, array $data): View
    {
        return view('technician.workspace', [...$data, 'page' => $page]);
    }
}
