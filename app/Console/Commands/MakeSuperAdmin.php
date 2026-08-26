<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MakeSuperAdmin extends Command
{
    protected $signature = 'app:make-superadmin {email : Email address of the user to promote}';

    protected $description = 'Promote an existing user to superadmin';

    public function handle(): int
    {
        $user = User::where('email', $this->argument('email'))->first();

        if (! $user) {
            $this->error('No user was found with that email address.');
            return self::FAILURE;
        }

        $user->forceFill([
            'role' => 'superadmin',
            'status' => 'active',
        ])->save();

        $this->info("{$user->email} is now a superadmin.");

        return self::SUCCESS;
    }
}
