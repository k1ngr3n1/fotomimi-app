<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactNotification;

class TestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test contact email functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing contact email...');
        
        $testData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '+1234567890',
            'message' => 'This is a test message from the contact form.'
        ];

        try {
            Mail::to(env('MAIL_TO_ADDRESS', 'fotomimi@gmail.com'))
                ->send(new ContactNotification($testData));
            
            $this->info('✅ Email sent successfully!');
            $this->info('Check your inbox at: ' . env('MAIL_TO_ADDRESS', 'fotomimi@gmail.com'));
        } catch (\Exception $e) {
            $this->error('❌ Failed to send email: ' . $e->getMessage());
            $this->info('Make sure you have configured your MAIL_USERNAME and MAIL_PASSWORD in .env file');
        }
    }
}
