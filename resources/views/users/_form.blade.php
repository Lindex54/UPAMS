<div class="grid gap-6 sm:grid-cols-2">
    <div>
        <label class="mb-2 block text-sm font-semibold text-heading" for="first_name">First Name</label>
        <input
            class="block w-full rounded-lg border bg-white px-4 py-3 text-base text-heading outline-none transition placeholder:text-body-text/60 focus:border-busitema-blue focus:ring-3 focus:ring-busitema-blue/15 @error('first_name') border-red-400 @else border-border @enderror"
            id="first_name"
            name="first_name"
            type="text"
            value="{{ old('first_name', $user?->first_name ?? $user?->name) }}"
            autocomplete="given-name"
            required
            autofocus
            @error('first_name') aria-invalid="true" aria-describedby="first-name-error" @enderror
        >
        @error('first_name')
            <p class="mt-2 text-sm text-red-600" id="first-name-error">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-2 block text-sm font-semibold text-heading" for="middle_name">Middle Name <span class="font-normal text-body-text">(optional)</span></label>
        <input class="block w-full rounded-lg border bg-white px-4 py-3 text-base text-heading outline-none transition focus:border-busitema-blue focus:ring-3 focus:ring-busitema-blue/15 @error('middle_name') border-red-400 @else border-border @enderror" id="middle_name" name="middle_name" type="text" value="{{ old('middle_name', $user?->middle_name) }}" autocomplete="additional-name" @error('middle_name') aria-invalid="true" aria-describedby="middle-name-error" @enderror>
        @error('middle_name')
            <p class="mt-2 text-sm text-red-600" id="middle-name-error">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-2 block text-sm font-semibold text-heading" for="last_name">Last Name</label>
        <input class="block w-full rounded-lg border bg-white px-4 py-3 text-base text-heading outline-none transition focus:border-busitema-blue focus:ring-3 focus:ring-busitema-blue/15 @error('last_name') border-red-400 @else border-border @enderror" id="last_name" name="last_name" type="text" value="{{ old('last_name', $user?->last_name) }}" autocomplete="family-name" required @error('last_name') aria-invalid="true" aria-describedby="last-name-error" @enderror>
        @error('last_name')
            <p class="mt-2 text-sm text-red-600" id="last-name-error">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-2 block text-sm font-semibold text-heading" for="telephone">Telephone Number</label>
        <input class="block w-full rounded-lg border bg-white px-4 py-3 text-base text-heading outline-none transition placeholder:text-body-text/60 focus:border-busitema-blue focus:ring-3 focus:ring-busitema-blue/15 @error('telephone') border-red-400 @else border-border @enderror" id="telephone" name="telephone" type="tel" value="{{ old('telephone', $user?->telephone) }}" placeholder="+256 700 000000" autocomplete="tel" required @error('telephone') aria-invalid="true" aria-describedby="telephone-error" @enderror>
        @error('telephone')
            <p class="mt-2 text-sm text-red-600" id="telephone-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="sm:col-span-2">
        <label class="mb-2 block text-sm font-semibold text-heading" for="email">Email address</label>
        <input
            class="block w-full rounded-lg border bg-white px-4 py-3 text-base text-heading outline-none transition placeholder:text-body-text/60 focus:border-busitema-blue focus:ring-3 focus:ring-busitema-blue/15 @error('email') border-red-400 @else border-border @enderror"
            id="email"
            name="email"
            type="email"
            value="{{ old('email', $user?->email) }}"
            placeholder="name@busitema.ac.ug"
            autocomplete="email"
            inputmode="email"
            required
            @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
        >
        @error('email')
            <p class="mt-2 text-sm text-red-600" id="email-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="sm:col-span-2">
        <label class="mb-2 block text-sm font-semibold text-heading" for="position">Position / Job Title <span class="font-normal text-body-text">(optional)</span></label>
        <input class="block w-full rounded-lg border bg-white px-4 py-3 text-base text-heading outline-none transition focus:border-busitema-blue focus:ring-3 focus:ring-busitema-blue/15 @error('position') border-red-400 @else border-border @enderror" id="position" name="position" type="text" value="{{ old('position', $user?->position) }}" autocomplete="organization-title" @error('position') aria-invalid="true" aria-describedby="position-error" @enderror>
        @error('position')
            <p class="mt-2 text-sm text-red-600" id="position-error">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-2 block text-sm font-semibold text-heading" for="campus_id">Campus</label>
        <select class="block w-full rounded-lg border bg-white px-4 py-3 text-base text-heading outline-none transition focus:border-busitema-blue focus:ring-3 focus:ring-busitema-blue/15 @error('campus_id') border-red-400 @else border-border @enderror" id="campus_id" name="campus_id" required @error('campus_id') aria-invalid="true" aria-describedby="campus-error" @enderror>
            <option value="">Select campus</option>
            @foreach ($campuses as $campus)
                <option value="{{ $campus->id }}" @selected((string) old('campus_id', $user?->campus_id) === (string) $campus->id)>{{ $campus->name }}</option>
            @endforeach
        </select>
        @error('campus_id')
            <p class="mt-2 text-sm text-red-600" id="campus-error">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-2 block text-sm font-semibold text-heading" for="org_unit_id">Organizational Unit</label>
        <select class="block w-full rounded-lg border bg-white px-4 py-3 text-base text-heading outline-none transition focus:border-busitema-blue focus:ring-3 focus:ring-busitema-blue/15 @error('org_unit_id') border-red-400 @else border-border @enderror" id="org_unit_id" name="org_unit_id" required @error('org_unit_id') aria-invalid="true" aria-describedby="org-unit-error" @enderror>
            <option value="">Select organizational unit</option>
            @foreach ($orgUnits as $orgUnit)
                <option value="{{ $orgUnit->id }}" @selected((string) old('org_unit_id', $user?->org_unit_id) === (string) $orgUnit->id)>{{ $orgUnit->name }}</option>
            @endforeach
        </select>
        @error('org_unit_id')
            <p class="mt-2 text-sm text-red-600" id="org-unit-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="sm:col-span-2">
        <label class="mb-2 block text-sm font-semibold text-heading" for="role_id">Role</label>
        <select class="block w-full rounded-lg border bg-white px-4 py-3 text-base text-heading outline-none transition focus:border-busitema-blue focus:ring-3 focus:ring-busitema-blue/15 @error('role_id') border-red-400 @else border-border @enderror" id="role_id" name="role_id" required @error('role_id') aria-invalid="true" aria-describedby="role-error" @enderror>
            <option value="">Select role</option>
            @foreach ($roles as $role)
                <option value="{{ $role->id }}" @selected((string) old('role_id', $user?->role_id) === (string) $role->id)>{{ $role->name }}</option>
            @endforeach
        </select>
        @error('role_id')
            <p class="mt-2 text-sm text-red-600" id="role-error">{{ $message }}</p>
        @enderror
    </div>

    @if ($includePassword)
        <div>
            <label class="mb-2 block text-sm font-semibold text-heading" for="password">Initial password</label>
            <input
                class="block w-full rounded-lg border bg-white px-4 py-3 text-base text-heading outline-none transition focus:border-busitema-blue focus:ring-3 focus:ring-busitema-blue/15 @error('password') border-red-400 @else border-border @enderror"
                id="password"
                name="password"
                type="password"
                autocomplete="new-password"
                required
                @error('password') aria-invalid="true" aria-describedby="password-error" @enderror
            >
            @error('password')
                <p class="mt-2 text-sm text-red-600" id="password-error">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="mb-2 block text-sm font-semibold text-heading" for="password_confirmation">Confirm initial password</label>
            <input
                class="block w-full rounded-lg border border-border bg-white px-4 py-3 text-base text-heading outline-none transition focus:border-busitema-blue focus:ring-3 focus:ring-busitema-blue/15"
                id="password_confirmation"
                name="password_confirmation"
                type="password"
                autocomplete="new-password"
                required
            >
        </div>

        <p class="text-xs leading-5 text-body-text sm:col-span-2">Use at least 8 characters with uppercase and lowercase letters, a number, and a symbol. The password is securely hashed before storage.</p>
    @endif
</div>
