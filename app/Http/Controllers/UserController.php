<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Campus;
use App\Models\OrgUnit;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $users = User::query()
            ->with(['campus:id,name', 'orgUnit:id,name', 'role:id,name'])
            ->select([
                'id',
                'name',
                'email',
                'position',
                'campus_id',
                'org_unit_id',
                'role_id',
                'is_active',
                'created_at',
            ])
            ->orderBy('name')
            ->orderBy('id')
            ->paginate(15);

        return view('users.index', ['users' => $users]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('users.create', $this->formReferenceData());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $attributes = $request->safe()->only([
            'first_name',
            'middle_name',
            'last_name',
            'email',
            'telephone',
            'position',
            'campus_id',
            'org_unit_id',
            'role_id',
            'password',
        ]);
        $attributes['name'] = $this->fullName($attributes);

        User::create($attributes);

        return redirect()
            ->route('users.index')
            ->with('status', 'User account created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user): View
    {
        return view('users.edit', [
            'user' => $user,
            ...$this->formReferenceData(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $attributes = $request->safe()->only([
            'first_name',
            'middle_name',
            'last_name',
            'email',
            'telephone',
            'position',
            'campus_id',
            'org_unit_id',
            'role_id',
        ]);
        $attributes['name'] = $this->fullName($attributes);

        $user->update($attributes);

        return redirect()
            ->route('users.index')
            ->with('status', 'User account updated successfully.');
    }

    /**
     * @return array{campuses: Collection<int, Campus>, orgUnits: Collection<int, OrgUnit>, roles: Collection<int, Role>}
     */
    private function formReferenceData(): array
    {
        return [
            'campuses' => Campus::query()->orderBy('name')->get(['id', 'name']),
            'orgUnits' => OrgUnit::query()->orderBy('name')->get(['id', 'name']),
            'roles' => Role::query()->orderBy('name')->get(['id', 'name']),
        ];
    }

    /**
     * @param  array{first_name: string, middle_name?: string|null, last_name: string}  $attributes
     */
    private function fullName(array $attributes): string
    {
        return collect([
            $attributes['first_name'],
            $attributes['middle_name'] ?? null,
            $attributes['last_name'],
        ])->filter()->implode(' ');
    }
}
