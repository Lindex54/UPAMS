<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\ComputerLab;
use App\Models\MaintenanceRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class TechnicianScope
{
    public function isAdministrator(User $user): bool
    {
        return $user->role?->name === 'System Administrator';
    }

    /** @return Builder<Asset> */
    public function equipment(User $user): Builder
    {
        return $this->apply(Asset::query()->where('is_ict', true), $user);
    }

    /** @return Builder<ComputerLab> */
    public function labs(User $user): Builder
    {
        return $this->apply(ComputerLab::query(), $user);
    }

    /** @return Builder<MaintenanceRequest> */
    public function faults(User $user): Builder
    {
        return $this->apply(
            MaintenanceRequest::query()->whereHas('asset', fn (Builder $query) => $query->where('is_ict', true)),
            $user,
        );
    }

    /** @template TModel of \Illuminate\Database\Eloquent\Model
     * @param  Builder<TModel>  $query
     * @return Builder<TModel>
     */
    private function apply(Builder $query, User $user): Builder
    {
        if ($this->isAdministrator($user)) {
            return $query;
        }

        $query->where('campus_id', $user->campus_id);

        if ($user->org_unit_id !== null) {
            $query->where('org_unit_id', $user->org_unit_id);
        }

        return $query;
    }
}
