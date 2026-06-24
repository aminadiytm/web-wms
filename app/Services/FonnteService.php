<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    public function sendMessage(string $target, string $message): bool
    {
        $response = Http::withHeaders([
                'Authorization' => config('services.fonnte.token'),
            ])
            ->asForm()
            ->post(config('services.fonnte.base_url'), [
                'target' => $target,
                'message' => $message,
                'countryCode' => '62',
            ]);
    
        Log::info('Fonnte response', [
            'target' => $target,
            'status' => $response->status(),
            'body' => $response->json(),
        ]);
    
        if (!$response->successful()) {
            return false;
        }
    
        return (bool) data_get($response->json(), 'status');
    }
    
}