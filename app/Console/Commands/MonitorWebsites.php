<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MonitorWebsites extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monitor:websites';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitor all client websites for availability';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $websites = \App\Models\Website::with('client')->get();

        foreach ($websites as $website) {
            /** @var \App\Models\Website $website */
            try {
                $response = \Illuminate\Support\Facades\Http::timeout(10)->get($website->url);
                $isUp = $response->successful();
            } catch (\Exception $e) {
                $isUp = false;
            }

            // If website is down and was previously up (or first check), notify client
            // Requirement: "If a website is unreachable ... an alert must be triggered"
            // We'll trigger if it's down. To avoid spam, we could check if($isUp === false && $website->is_up === true)
            // But let's follow requirement strictly: if unreachable -> alert.
            // But realistically, repeated alerts every 15 mins for same downtime might be annoying, but "Monitor... every 15 mins" implies checks.
            // Trigger notification if site is down
            // Modified logic: Alert EVERY time it is down, as requested.
            if (!$isUp) {
                $this->info("Sending WebsiteDown notification for {$website->url} to {$website->client->email}");
                try {
                    $website->client->notify(new \App\Notifications\WebsiteDown($website));
                    $this->info("Notification sent successfully.");
                } catch (\Exception $e) {
                    $this->error("Failed to send notification: " . $e->getMessage());
                }
            }

            $website->update([
                'is_up' => $isUp,
                'last_checked_at' => now(),
            ]);

            $this->info("Checked {$website->url}: " . ($isUp ? 'UP' : 'DOWN'));
        }
    }
}
