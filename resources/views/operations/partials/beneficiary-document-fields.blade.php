@php
    $storedDocuments = $beneficiaryRecord?->documents ?? collect();
    $standardDocuments = $storedDocuments->keyBy('document_type');
    $otherStoredDocuments = $storedDocuments->where('document_type', \App\Models\BeneficiaryDocument::TYPE_OTHER);
    $fileInput = 'min-h-10 w-full cursor-pointer rounded-lg border border-border bg-white px-3 py-1.5 text-sm font-normal text-body-text file:mr-3 file:cursor-pointer file:rounded-md file:border-0 file:bg-blue-50 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-busitema-blue';
    $otherDocumentRows = collect(old('other_documents', []))->map(fn (array $row, int|string $index): array => ['key' => "old-{$index}", 'name' => $row['name'] ?? ''])->values();
    $otherDocumentErrors = collect($errors->getMessages())->filter(fn (array $messages, string $key): bool => str_starts_with($key, 'other_documents.'))->map(fn (array $messages): string => $messages[0]);
@endphp

<section class="rounded-xl border border-border bg-white p-6 shadow-sm sm:p-8">
    <div class="flex flex-col gap-1 border-b border-border pb-5 sm:flex-row sm:items-end sm:justify-between">
        <div><p class="text-xs font-semibold tracking-[0.12em] text-busitema-blue uppercase">Supporting documents</p><h2 class="mt-1.5 text-xl font-semibold text-heading">Company documents</h2></div>
        <p class="text-xs text-body-text">PDF, Word, JPG or PNG · up to 10 MB each</p>
    </div>

    <ul class="mt-6 divide-y divide-border rounded-xl border border-border">
        @foreach (\App\Models\BeneficiaryDocument::STANDARD_TYPES as $type => $title)
            @php($storedDocument = $standardDocuments->get($type))
            <li class="grid gap-3 p-4 sm:grid-cols-[minmax(0,14rem)_1fr] sm:items-center">
                <div class="min-w-0">
                    <p class="flex items-center gap-2 text-sm font-semibold text-heading">
                        <svg class="size-4 shrink-0 {{ $storedDocument ? 'text-emerald-600' : 'text-body-text' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">@if ($storedDocument)<path d="m5 12 4 4L19 6" />@else<path d="M7 3h8l4 4v14H7V3Zm8 0v5h4" />@endif</svg>
                        {{ $title }} <span class="font-normal text-body-text">(optional)</span>
                    </p>
                    @if ($storedDocument)
                        <p class="mt-1 truncate pl-6 text-xs text-body-text">
                            <a class="font-semibold text-busitema-blue hover:underline" href="{{ route('operations.beneficiaries.documents.show', [$beneficiaryRecord, $storedDocument]) }}">{{ $storedDocument->original_name }}</a>
                            · {{ $storedDocument->readableSize() }} · {{ $storedDocument->created_at->format('d M Y') }}
                        </p>
                    @endif
                </div>
                <div class="flex flex-col gap-2">
                    <label class="sr-only" for="document-{{ $type }}">{{ $storedDocument ? 'Replace' : 'Upload' }} {{ $title }}</label>
                    <input class="{{ $fileInput }}" id="document-{{ $type }}" type="file" name="documents[{{ $type }}]" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                    @if ($storedDocument)
                        <label class="inline-flex items-center gap-2 text-xs font-normal text-body-text"><input class="accent-red-600" type="checkbox" name="remove_documents[]" value="{{ $storedDocument->id }}" @checked(in_array((string) $storedDocument->id, old('remove_documents', []), true))> Remove the stored file</label>
                    @endif
                    @error("documents.{$type}")<span class="text-xs font-normal text-red-600">{{ $message }}</span>@enderror
                </div>
            </li>
        @endforeach
    </ul>

    <div class="mt-8" x-data="{
        rows: @js($otherDocumentRows),
        errors: @js($otherDocumentErrors),
        add() {
            this.rows.push({ key: `new-${Date.now()}-${this.rows.length}`, name: '' });
            this.$nextTick(() => this.$refs.otherDocuments.querySelector('li:last-child input[type=text]')?.focus());
        },
        remove(index) {
            this.rows.splice(index, 1);
        },
    }">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h3 class="text-sm font-semibold text-heading">Other documents</h3>
                <p class="mt-0.5 text-xs text-body-text">Add any other form or certificate and give it a name.</p>
            </div>
            <button class="inline-flex min-h-10 items-center gap-2 rounded-lg border border-busitema-blue px-4 text-sm font-semibold text-busitema-blue transition hover:bg-blue-50" type="button" x-on:click="add()">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="M12 5v14M5 12h14" /></svg>
                Add document
            </button>
        </div>

        @if ($otherStoredDocuments->isNotEmpty())
            <ul class="mt-4 divide-y divide-border rounded-xl border border-border">
                @foreach ($otherStoredDocuments as $storedDocument)
                    <li class="flex flex-col gap-2 p-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-heading">{{ $storedDocument->title }}</p>
                            <p class="mt-0.5 truncate text-xs text-body-text"><a class="font-semibold text-busitema-blue hover:underline" href="{{ route('operations.beneficiaries.documents.show', [$beneficiaryRecord, $storedDocument]) }}">{{ $storedDocument->original_name }}</a> · {{ $storedDocument->readableSize() }} · {{ $storedDocument->created_at->format('d M Y') }}</p>
                        </div>
                        <label class="inline-flex shrink-0 items-center gap-2 text-xs text-body-text"><input class="accent-red-600" type="checkbox" name="remove_documents[]" value="{{ $storedDocument->id }}" @checked(in_array((string) $storedDocument->id, old('remove_documents', []), true))> Remove</label>
                    </li>
                @endforeach
            </ul>
        @endif

        <ul class="mt-4 flex flex-col gap-3" x-ref="otherDocuments">
            <template x-for="(row, index) in rows" :key="row.key">
                <li class="grid gap-3 rounded-xl border border-dashed border-border bg-light-background p-4 sm:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_auto] sm:items-start">
                    <label class="flex flex-col gap-1.5 text-xs font-semibold text-heading">
                        Document name
                        <input class="min-h-10 rounded-lg border border-border bg-white px-3 text-sm font-normal text-heading outline-none transition placeholder:text-body-text/60 focus:border-busitema-blue focus:ring-2 focus:ring-busitema-blue/15" type="text" maxlength="150" placeholder="e.g. Trading Licence" :name="`other_documents[${index}][name]`" x-model="row.name">
                        <span class="text-xs font-normal text-red-600" x-show="errors[`other_documents.${index}.name`]" x-text="errors[`other_documents.${index}.name`]"></span>
                    </label>
                    <label class="flex flex-col gap-1.5 text-xs font-semibold text-heading">
                        File
                        <input class="{{ $fileInput }}" type="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" :name="`other_documents[${index}][file]`">
                        <span class="text-xs font-normal text-red-600" x-show="errors[`other_documents.${index}.file`]" x-text="errors[`other_documents.${index}.file`]"></span>
                    </label>
                    <button class="inline-flex min-h-10 items-center justify-center rounded-lg border border-border px-3 text-xs font-semibold text-red-700 transition hover:border-red-300 hover:bg-red-50 sm:mt-5" type="button" x-on:click="remove(index)" :aria-label="`Remove ${row.name || 'this document'}`">Remove</button>
                </li>
            </template>
        </ul>
        <p class="mt-3 rounded-xl border border-dashed border-border px-4 py-5 text-center text-xs text-body-text" x-show="rows.length === 0 && {{ $otherStoredDocuments->isEmpty() ? 'true' : 'false' }}">No other documents added.</p>
    </div>
</section>
