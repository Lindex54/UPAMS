<?php

namespace App\Http\Controllers\Governance;

use App\Http\Controllers\Controller;
use App\Http\Requests\DecideApprovalRequest;
use App\Http\Requests\StoreApprovalRequest;
use App\Models\Approval;
use App\Models\Campus;
use App\Services\ApprovalWorkflowService;
use App\Support\CsvExporter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApprovalController extends Controller
{
    public function __construct(private readonly ApprovalWorkflowService $workflow) {}

    public function index(Request $request): View
    {
        $approvals = Approval::query()->with(['campus', 'reviewer', 'creator', 'updater'])
            ->when($request->string('q')->toString(), fn (Builder $query, string $q): Builder => $query->where(fn (Builder $nested): Builder => $nested->where('reference', 'like', "%{$q}%")->orWhere('title', 'like', "%{$q}%")->orWhere('subject_reference', 'like', "%{$q}%")))
            ->when($request->string('status')->toString(), fn (Builder $query, string $status): Builder => $query->where('status', $status))
            ->when($request->string('approval_type')->toString(), fn (Builder $query, string $type): Builder => $query->where('approval_type', $type))->latest();

        return view('admin.approvals.index', ['approvals' => $approvals->paginate(15)->withQueryString(), 'pendingCount' => Approval::query()->where('status', 'Pending')->count()]);
    }

    public function create(): View
    {
        return view('admin.approvals.form', ['campuses' => Campus::query()->orderBy('name')->get()]);
    }

    public function store(StoreApprovalRequest $request): RedirectResponse
    {
        $approval = Approval::query()->create($request->validated() + ['reference' => 'APR-'.str()->upper(str()->random(8)), 'created_by' => $request->user()->id, 'updated_by' => $request->user()->id]);

        return redirect()->route('approvals.show', $approval)->with('success', 'Approval request submitted.');
    }

    public function show(Approval $approval): View
    {
        return view('admin.approvals.show', ['approval' => $approval->load(['campus', 'reviewer', 'creator', 'updater'])]);
    }

    public function decide(DecideApprovalRequest $request, Approval $approval): RedirectResponse
    {
        $this->workflow->decide($approval, $request->validated('decision'), $request->validated('comments'), $request->user());

        return back()->with('success', 'Approval decision recorded.');
    }

    public function export(): mixed
    {
        $rows = Approval::query()->with(['campus', 'reviewer', 'creator'])->latest()->cursor()->map(fn (Approval $approval): array => [$approval->reference, $approval->approval_type, $approval->subject_reference, $approval->title, $approval->campus?->name, $approval->status, $approval->reviewer?->name, $approval->decision_at?->format('Y-m-d H:i'), $approval->creator?->name, $approval->created_at->format('Y-m-d H:i')]);

        return CsvExporter::download('upams-approvals-'.today()->format('Y-m-d').'.csv', ['Reference', 'Type', 'Subject', 'Title', 'Campus', 'Status', 'Reviewer', 'Decision At', 'Created By', 'Created At'], $rows);
    }
}
