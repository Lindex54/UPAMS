<?php

namespace App\Models;

use Database\Factories\BeneficiaryDocumentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['beneficiary_id', 'document_type', 'title', 'file_path', 'original_name', 'mime_type', 'size', 'uploaded_by'])]
class BeneficiaryDocument extends Model
{
    /**
     * Standard company documents, keyed by the form input name; each beneficiary keeps one current file per type.
     *
     * @var array<string, string>
     */
    public const STANDARD_TYPES = [
        'certificate_of_incorporation' => 'Certificate of Incorporation',
        'form_18' => 'Form 18',
        'form_20' => 'Form 20',
        'form_1' => 'Form 1',
        'annual_returns' => 'Annual Returns',
    ];

    public const TYPE_OTHER = 'other';

    /** @use HasFactory<BeneficiaryDocumentFactory> */
    use HasFactory;

    public function beneficiary(): BelongsTo
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function isStandard(): bool
    {
        return array_key_exists($this->document_type, self::STANDARD_TYPES);
    }

    public function readableSize(): string
    {
        return $this->size >= 1048576
            ? number_format($this->size / 1048576, 1).' MB'
            : number_format(max(1, (int) round($this->size / 1024))).' KB';
    }

    protected function casts(): array
    {
        return ['size' => 'integer'];
    }
}
