<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // specific user
        $client = \App\Models\Client::factory()->create([
            'name' => 'Vaishnavi G',
            'email' => 'vaishnavi.g@gmail.com',
        ]);

        // Add some dummy websites for this user
        $client->websites()->createMany([
            ['url' => 'https://google.com', 'is_up' => true],
            ['url' => 'https://example.com', 'is_up' => true],
        ]);
    }
}
