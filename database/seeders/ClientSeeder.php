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
            'name' => 'Vaishnavi Gopi',
            'email' => 'vaishnavigopi13@gmail.com',
        ]);
    }
}
