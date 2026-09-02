{{-- A selected image takes precedence over the stored photo or the default avatar. --}}
<div
    class="sm:col-span-2"
    x-data="{
        imageUrl: null,
        loadImage(event) {
            if (this.imageUrl) {
                URL.revokeObjectURL(this.imageUrl);
            }

            const image = event.target.files[0];
            this.imageUrl = image ? URL.createObjectURL(image) : null;
        },
    }"
>
    <label class="flex flex-col gap-2 text-sm font-semibold text-heading">
        <span>Person's Photo <span class="font-normal text-body-text">(optional)</span></span>
        <span class="grid gap-4 rounded-xl border border-dashed border-border bg-light-background p-4 sm:grid-cols-[7rem_1fr] sm:items-center">
            <span class="flex aspect-square items-center justify-center overflow-hidden rounded-lg border border-border bg-white text-body-text shadow-sm">
                <img x-cloak x-show="imageUrl" :src="imageUrl" class="size-full object-cover" alt="Selected beneficiary photo preview">
                @if ($beneficiaryPhotoUrl ?? null)
                    <img x-show="! imageUrl" src="{{ $beneficiaryPhotoUrl }}" class="size-full object-cover" alt="Stored beneficiary photo">
                @else
                    <span x-show="! imageUrl" class="flex size-full items-center justify-center bg-linear-to-br from-blue-50 to-slate-200" aria-label="Default beneficiary avatar">
                        <span class="flex size-20 items-center justify-center rounded-full bg-white text-busitema-blue shadow-sm ring-1 ring-busitema-blue/10">
                            <svg class="size-14" aria-hidden="true" viewBox="0 0 64 64" fill="currentColor">
                                <circle cx="32" cy="23" r="13" />
                                <path d="M10 59c1.4-14 10.2-22 22-22s20.6 8 22 22H10Z" />
                            </svg>
                        </span>
                    </span>
                @endif
            </span>
            <span class="flex min-w-0 flex-col gap-2">
                <input
                    class="min-h-11 w-full cursor-pointer rounded-lg border border-border bg-white px-3 py-2 text-sm font-normal text-body-text file:mr-3 file:cursor-pointer file:rounded-md file:border-0 file:bg-blue-50 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-busitema-blue"
                    type="file"
                    name="photo"
                    accept="image/jpeg,image/png,image/webp"
                    @change="loadImage($event)"
                >
                <span class="text-xs font-normal leading-5 text-body-text">JPEG, PNG, or WebP. Maximum file size: 2 MB.</span>
                @if ($beneficiaryRecord?->photo_path)
                    <span class="text-xs font-normal text-emerald-700">A photo is stored. Leave this field blank to keep it, or choose another image to replace it.</span>
                @endif
            </span>
        </span>
        @error('photo')<span class="text-xs font-normal text-red-600">{{ $message }}</span>@enderror
    </label>
</div>
