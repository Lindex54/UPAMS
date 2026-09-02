<?php

namespace App\Services;

use App\Models\Approval;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ApprovalWorkflowService
{
    public function decide(Approval $approval, string $decision, string $comments, User $reviewer): Approval
    {
        return DB::transaction(function () use ($approval, $decision, $comments, $reviewer): Approval {
            $approval = Approval::query()->lockForUpdate()->findOrFail($approval->id);
            if ($approval->status !== 'Pending') {
                throw ValidationException::withMessages(['decision' => 'Only pending requests can receive a decision.']);
            }

            $approval->update(['status' => $decision, 'reviewer_id' => $reviewer->id, 'decision_at' => now(), 'decision_comments' => $comments, 'updated_by' => $reviewer->id]);

            return $approval->refresh();
        });
    }
}
