<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUserCommand extends Command
{
    protected $signature = 'user:create-admin
        {email? : Email for the admin user}
        {--name= : Name for the admin user}
        {--password= : Password for the admin user}
        {--force : Update the user if the email already exists}';

    protected $description = 'Create or update an admin user';

    public function handle(): int
    {
        $email = $this->argument('email') ?: $this->ask('Admin email');
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Invalid email address.');

            return self::FAILURE;
        }

        $name = $this->option('name') ?: $this->ask('Admin name', 'Admin');

        $password = $this->option('password');
        if (! $password) {
            $password = $this->secret('Admin password (min 12 chars, upper/lower/digit)');
            $confirm = $this->secret('Confirm password');
            if ($password !== $confirm) {
                $this->error('Passwords do not match.');

                return self::FAILURE;
            }
        }

        if (! $this->isStrongPassword($password)) {
            $this->error('Password must be at least 12 characters and include upper, lower, and digits.');

            return self::FAILURE;
        }

        $existing = User::where('email', $email)->first();
        if ($existing && ! $this->option('force')) {
            $this->error('User already exists. Use --force to update.');

            return self::FAILURE;
        }

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'is_admin' => true,
            ]
        );

        $this->info('Admin user saved.');

        return self::SUCCESS;
    }

    private function isStrongPassword(string $password): bool
    {
        if (strlen($password) < 12) {
            return false;
        }

        return preg_match('/[a-z]/', $password)
            && preg_match('/[A-Z]/', $password)
            && preg_match('/\d/', $password);
    }
}
