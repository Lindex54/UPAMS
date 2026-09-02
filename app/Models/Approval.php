<?php

namespace App\Models;

use Database\Factories\ApprovalFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['reference', 'campus_id', 'approval_type', 'subject_reference', 'title', 'request_details', 'status', 'reviewer_id', 'decision_at', 'decision_comments', 'created_by', 'updated_by'])]
class Approval extends Model
{
    /** @use HasFactory<ApprovalFactory> */
    use HasFactory;

    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    protected function casts(): array
    {
        return ['decision_at' => 'datetime'];
    }
}
