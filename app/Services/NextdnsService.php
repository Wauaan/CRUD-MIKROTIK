<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class NextDnsService
{
    protected $apiKey;
    protected $profileId;
    protected $baseUrl;
    
    public function __construct()
    {
        $this->apiKey = config('services.nextdns.api_key');    // simpan di config/services.php
        $this->profileId = config('services.nextdns.profile_id');
        $this->baseUrl = "https://api.nextdns.io";
    }

    public function getDenylist()
    {
        $url = "https://api.nextdns.io/profiles/{$this->profileId}/denylist";

        $response = Http::withHeaders([
            'x-api-key' => $this->apiKey,
        ])->get($url);

        if ($response->successful()) {
            return $response->json(); // array hasil denylist
        }
        return null;
    }
    
    public function deleteFromDenylist($domain)
    {
        $response = Http::withHeaders([
            'x-api-key' => $this->apiKey,
        ])->delete("https://api.nextdns.io/profiles/{$this->profileId}/denylist/{$domain}");

        return $response->successful();
    }
        public function updateDenylistActiveStatus($domainId, $newStatus)
    {
        $denylist = $this->getDenylist();

        if (!isset($denylist['data'])) {
            return ['success' => false, 'message' => 'Failed to get denylist.'];
        }

        // Update domain status
        foreach ($denylist['data'] as &$item) {
            if ($item['id'] === $domainId) {
                $item['active'] = (bool) $newStatus;
            }
        }

        // Kirim ulang semua data
        $response = Http::withHeaders([
            'x-api-key' => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->put("{$this->baseUrl}/profiles/{$this->profileId}/denylist", $denylist['data']);

        if ($response->successful()) {
            return ['success' => true];
        }

        return ['success' => false, 'message' => $response->json()];
    }
}
