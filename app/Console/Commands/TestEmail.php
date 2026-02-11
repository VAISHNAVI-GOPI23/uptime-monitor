<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmail extends Command
{
    protected $signature = 'test:email {to}';
    protected $description = 'Send a test email to verify configuration';

    public function handle()
    {
        $to = $this->argument('to');
        $this->info("Sending test email to {$to}...");

        try {
            Mail::raw('This is a test email from Uptime Monitor.', function ($message) use ($to) {
                $message->to($to)
                    ->subject('Test Email');
            });

            $this->info('Test email sent successfully.');
        } catch (\Exception $e) {
            $this->error('Failed to send email: ' . $e->getMessage());
        }
    }
}
