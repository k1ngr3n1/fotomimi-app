<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactNotification;

class TestContactEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-contact-email';

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
        $this->info('Testing contact email functionality...');

        $testData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '+1234567890',
            'message' => 'This is a test message to verify the contact form email functionality is working properly.'
        ];

        try {
            Mail::to(env('MAIL_TO_ADDRESS', 'vrhovec.renato@gmail.com'))
                ->send(new ContactNotification($testData));
            
            $this->info('✅ Test email sent successfully to: ' . env('MAIL_TO_ADDRESS', 'vrhovec.renato@gmail.com'));
            $this->info('Please check your email inbox.');
        } catch (\Exception $e) {
            $this->error('❌ Failed to send test email: ' . $e->getMessage());
            $this->error('Mail configuration:');
            $this->line('MAIL_MAILER: ' . env('MAIL_MAILER'));
            $this->line('MAIL_HOST: ' . env('MAIL_HOST'));
            $this->line('MAIL_PORT: ' . env('MAIL_PORT'));
            $this->line('MAIL_USERNAME: ' . env('MAIL_USERNAME'));
            $this->line('MAIL_FROM_ADDRESS: ' . env('MAIL_FROM_ADDRESS'));
            $this->line('MAIL_TO_ADDRESS: ' . env('MAIL_TO_ADDRESS'));
        }
    }
}
