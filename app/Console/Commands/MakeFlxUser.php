<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class MakeFlxUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flx:make-user {--name= : The name of the user} {--email= : The email address of the user} {--password= : The password for the user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new user for the FLX platform';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Creating a new FLX User...');

        $name = $this->option('name') ?: $this->ask('Name');
        $email = $this->option('email') ?: $this->ask('Email address');

        $validator = Validator::make(['email' => $email], [
            'email' => 'required|email|unique:users,email',
        ]);

        if ($validator->fails()) {
            $this->error('Invalid email or email already exists:');
            foreach ($validator->errors()->all() as $error) {
                $this->line(" - $error");
            }
            return self::FAILURE;
        }

        $password = $this->option('password') ?: $this->secret('Password');
        
        while (empty($password) || strlen($password) < 8) {
            $this->error('Password must be at least 8 characters.');
            $password = $this->secret('Password');
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        $this->newLine();
        $this->info("✅ FLX User [{$user->name}] successfully created!");
        $this->line("   - Email: {$user->email}");
        $this->line("   - You can now log into the admin dashboard.");

        return self::SUCCESS;
    }
}
