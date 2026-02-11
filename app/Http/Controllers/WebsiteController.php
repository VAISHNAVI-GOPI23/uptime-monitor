<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Website;
use Illuminate\Http\Request;

class WebsiteController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'url' => 'required|url',
        ]);

        $client = Client::findOrFail($request->client_id);

        $website = $client->websites()->create([
            'url' => $request->url,
            'is_up' => false, // Default status
        ]);

        // Immediate check
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(5)->get($website->url);
            $isUp = $response->successful();
        } catch (\Exception $e) {
            $isUp = false;
        }

        $website->update([
            'is_up' => $isUp,
            'last_checked_at' => now(),
        ]);

        return response()->json($website, 201);
    }

    public function destroy(Website $website)
    {
        $website->delete();
        return response()->json(null, 204);
    }
}
