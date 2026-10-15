<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCampusRequest;
use App\Models\Campus;
use App\Models\District;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CampusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        Gate::authorize('viewAny', Campus::class);

        $campuses = Campus::query()
            ->with('district:id,name')
            ->withCount(['users', 'assets'])
            ->orderByRaw('status = ? desc', [Campus::STATUS_ACTIVE])
            ->orderBy('name')
            ->get();

        return view('campuses.index', ['campuses' => $campuses]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        Gate::authorize('create', Campus::class);

        return view('campuses.create', ['districts' => $this->districts()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCampusRequest $request): RedirectResponse
    {
        $campus = Campus::query()->create($request->validated());

        return redirect()->route('campuses.show', $campus)->with('status', "{$campus->name} has been added.");
    }

    /**
     * Display the specified resource.
     */
    public function show(Campus $campus): View
    {
        Gate::authorize('view', $campus);

        return view('campuses.show', ['campus' => $campus->load('district:id,name')->loadCount(['users', 'assets', 'computerLabs'])]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Campus $campus): View
    {
        Gate::authorize('update', $campus);

        return view('campuses.edit', ['campus' => $campus, 'districts' => $this->districts()]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreCampusRequest $request, Campus $campus): RedirectResponse
    {
        $campus->update($request->validated());

        return redirect()->route('campuses.show', $campus)->with('status', "{$campus->name} has been updated.");
    }

    /**
     * @return Collection<int, District>
     */
    private function districts(): Collection
    {
        return District::query()->active()->orderBy('name')->get(['id', 'name']);
    }
}
