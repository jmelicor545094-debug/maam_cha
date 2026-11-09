<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateDeploymentAdmin extends Command
{
    protected $signature = 'medcare:create-admin';

    protected $description = 'Create or update the deployment administrator from environment variables';

    public function handle(): int
    {
        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');

        if (!$email && !$password) {
            return self::SUCCESS;
        }

        if (!$email || !$password || strlen($password) < 12) {
            $this->error('Set ADMIN_EMAIL and an ADMIN_PASSWORD of at least 12 characters.');
            return self::FAILURE;
        }

        User::updateOrCreate(
            ['email' => $email],
            ['name' => 'Medcare Administrator', 'password' => Hash::make($password), 'role' => 'admin'],
        );

        $this->info("Deployment administrator is ready: {$email}");
        return self::SUCCESS;
    }
}
