<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MonitorWebsitesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_it_detects_down_website_and_sends_notification()
    {
        \Illuminate\Support\Facades\Notification::fake();
        \Illuminate\Support\Facades\Http::fake([
            '*' => \Illuminate\Support\Facades\Http::response(null, 500),
        ]);

        $client = \App\Models\Client::factory()->create();
        $website = \App\Models\Website::create([
            'client_id' => $client->id,
            'url' => 'http://example.com',
            'is_up' => true,
        ]);

        $this->artisan('monitor:websites');

        $website->refresh();
        $this->assertFalse($website->is_up);
        $this->assertNotNull($website->last_checked_at);

        \Illuminate\Support\Facades\Notification::assertSentTo(
            $client,
            \App\Notifications\WebsiteDown::class
        );
    }

    public function test_it_updates_status_to_up()
    {
        \Illuminate\Support\Facades\Http::fake([
            '*' => \Illuminate\Support\Facades\Http::response(null, 200),
        ]);

        $client = \App\Models\Client::factory()->create();
        $website = \App\Models\Website::create([
            'client_id' => $client->id,
            'url' => 'http://example.com',
            'is_up' => false,
        ]);

        $this->artisan('monitor:websites');

        $website->refresh();
        $this->assertTrue($website->is_up);
    }
}
