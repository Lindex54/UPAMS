<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name',
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
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Roles allowed to generate invoices and record or reverse payments.
     *
     * @var list<string>
     */
    public const FINANCE_ROLES = ['System Administrator', 'Finance Officer'];

    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class);
    }

    public function orgUnit(): BelongsTo
    {
        return $this->belongsTo(OrgUnit::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function responsibleComputerLabs(): HasMany
    {
        return $this->hasMany(ComputerLab::class, 'responsible_technician_id');
    }

    /**
     * Active System Administrators and Finance Officers may handle invoices and payments.
     */
    public function managesFinance(): bool
    {
        return $this->is_active && in_array($this->role?->name, self::FINANCE_ROLES, true);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }
}
