<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateSuperadmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:make-superadmin {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make a user superadmin by email address';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("User with email {$email} not found.");
            
            if ($this->confirm('Would you like to create a new superadmin user?')) {
                $name = $this->ask('Enter the name for the superadmin');
                $password = $this->secret('Enter password for the superadmin');
                
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make($password),
                    'approved' => true,
                    'is_superadmin' => true,
                    'email_verified_at' => now(),
                ]);
                
                $this->info("Superadmin user created successfully!");
                $this->line("Email: {$email}");
                $this->line("Name: {$name}");
            }
            
            return;
        }
        
        $user->update([
            'is_superadmin' => true,
            'approved' => true,
        ]);
        
        $this->info("User {$user->name} ({$email}) is now a superadmin!");
    }
}