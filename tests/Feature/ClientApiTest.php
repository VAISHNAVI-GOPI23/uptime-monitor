<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ClientApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_it_returns_clients_with_websites()
    {
        $client = \App\Models\Client::factory()->create();
        $client->websites()->create([
            'url' => 'http://example.com',
            'is_up' => true,
        ]);

        $response = $this->get('/api/clients');

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'email',
                    'websites' => [
                        '*' => [
                            'id',
                            'url',
                            'is_up',
                        ],
                    ],
                ],
            ]);
    }
}
