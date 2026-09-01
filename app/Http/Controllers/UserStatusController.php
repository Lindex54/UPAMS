<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserStatusRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class UserStatusController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(UpdateUserStatusRequest $request, User $user): RedirectResponse
    {
        $isActive = $request->boolean('is_active');

        if ($request->user()->is($user) && ! $isActive) {
            return back()->withErrors([
                'account' => 'You cannot deactivate your own account.',
            ]);
        }

        $user->is_active = $isActive;

        if (! $isActive) {
            $user->setRememberToken(Str::random(60));
        }

        $user->save();

        return back()->with(
            'status',
            $isActive ? 'User account activated.' : 'User account deactivated.',
        );
    }
}
