<?php

namespace App\Services;

use App\Models\Beneficiary;
use App\Models\BeneficiaryDocument;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

/**
 * Stores beneficiary company documents on the private disk in two phases:
 * stage() writes files before the database transaction, attach() records them inside it,
 * and purge()/discard() remove files only once the outcome of the transaction is known.
 */
class BeneficiaryDocumentService
{
    private const DIRECTORY = 'beneficiaries/documents';

    private const DISK = 'local';

    /**
     * Write uploaded files to private storage and describe them for attach().
     *
     * @param  array<string, UploadedFile|null>  $standardFiles
     * @param  array<int, array{name?: string|null, file?: UploadedFile|null}>  $otherDocuments
     * @return list<array{document_type: string, title: string, file_path: string, original_name: string, mime_type: ?string, size: int}>
     */
    public function stage(array $standardFiles, array $otherDocuments): array
    {
        $staged = [];

        try {
            foreach (BeneficiaryDocument::STANDARD_TYPES as $type => $title) {
                if (($standardFiles[$type] ?? null) instanceof UploadedFile) {
                    $staged[] = $this->storeFile($standardFiles[$type], $type, $title);
                }
            }

            foreach ($otherDocuments as $document) {
                if (($document['file'] ?? null) instanceof UploadedFile && filled($document['name'] ?? null)) {
                    $staged[] = $this->storeFile($document['file'], BeneficiaryDocument::TYPE_OTHER, trim($document['name']));
                }
            }
        } catch (RuntimeException $exception) {
            $this->discard($staged);

            throw $exception;
        }

        return $staged;
    }

    /**
     * Record staged files and requested removals; call inside the beneficiary transaction.
     *
     * @param  list<array<string, mixed>>  $staged
     * @param  array<int, int|string>  $removeIds
     * @return list<string> Paths of files that are no longer referenced and can be purged after commit.
     */
    public function attach(Beneficiary $beneficiary, array $staged, array $removeIds, ?User $actor): array
    {
        $replacedTypes = collect($staged)
            ->pluck('document_type')
            ->filter(fn (string $type): bool => $type !== BeneficiaryDocument::TYPE_OTHER)
            ->all();

        $obsolete = $beneficiary->documents()
            ->getQuery()
            ->where(function ($query) use ($replacedTypes, $removeIds): void {
                $query->whereIn('document_type', $replacedTypes)
                    ->orWhereIn('id', array_map('intval', $removeIds));
            })
            ->get();

        $obsolete->each->delete();

        foreach ($staged as $document) {
            $beneficiary->documents()->create($document + ['uploaded_by' => $actor?->id]);
        }

        return $obsolete->pluck('file_path')->all();
    }

    /**
     * Remove staged files when the transaction that would have referenced them fails.
     *
     * @param  list<array<string, mixed>>  $staged
     */
    public function discard(array $staged): void
    {
        $this->purge(array_column($staged, 'file_path'));
    }

    /**
     * @param  list<string>  $paths
     */
    public function purge(array $paths): void
    {
        if ($paths !== []) {
            Storage::disk(self::DISK)->delete($paths);
        }
    }

    /**
     * @return array{document_type: string, title: string, file_path: string, original_name: string, mime_type: ?string, size: int}
     */
    private function storeFile(UploadedFile $file, string $type, string $title): array
    {
        $path = $file->store(self::DIRECTORY, self::DISK);

        if ($path === false) {
            throw new RuntimeException("The {$title} file could not be stored.");
        }

        return [
            'document_type' => $type,
            'title' => $title,
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size' => (int) $file->getSize(),
        ];
    }
}
