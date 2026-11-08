<?php

namespace App\Models\Concerns;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait BelongsToCampus
{
    protected static function bootBelongsToCampus(): void
    {
        static::saving(function (Model $model): void {
            $user = auth()->user();

            if (! $user instanceof User || ! $user->isCampusScoped()) {
                return;
            }

            if ($user->campus_id === null) {
                throw new AuthorizationException('A campus assignment is required to manage campus records.');
            }

            $model->setAttribute('campus_id', $user->campus_id);
        });

        static::addGlobalScope('campus', function (Builder $builder): void {
            $user = auth()->user();

            if (! $user instanceof User || ! $user->isCampusScoped()) {
                return;
            }

            if ($user->campus_id === null) {
                $builder->whereRaw('1 = 0');

                return;
            }

            $builder->where(
                $builder->getModel()->qualifyColumn('campus_id'),
                $user->campus_id,
            );
        });
    }
}
