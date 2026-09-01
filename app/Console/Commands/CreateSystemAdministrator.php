<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

#[Signature('upams:create-system-administrator')]
#[Description('Create the initial UPAMS System Administrator account')]
class CreateSystemAdministrator extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (User::query()->exists()) {
            $this->error('An UPAMS user account already exists. The initial administrator was not created.');

            return self::FAILURE;
        }

        $name = trim((string) $this->ask('Full name'));
        $email = Str::lower(trim((string) $this->ask('Email address')));
        $password = (string) $this->secret('Initial password (minimum 8 characters with uppercase, lowercase, numbers, and symbols)');
        $passwordConfirmation = (string) $this->secret('Confirm initial password');

        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $passwordConfirmation,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)],
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)->letters()->mixedCase()->numbers()->symbols(),
            ],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $message) {
                $this->error($message);
            }

            return self::FAILURE;
        }

        if (! $this->confirm("Create the initial System Administrator account for {$email}?", true)) {
            $this->warn('Account creation cancelled.');

            return self::FAILURE;
        }

        User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ]);

        $this->info('The initial UPAMS System Administrator account was created successfully.');

        return self::SUCCESS;
    }
}
