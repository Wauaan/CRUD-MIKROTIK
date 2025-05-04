<?php

namespace App\Services;

use RouterOS\Client;
use RouterOS\Query;

class MikrotikService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'host' => env('MIKROTIK_HOST'),
            'user' => env('MIKROTIK_USERNAME'),
            'pass' => env('MIKROTIK_PASSWORD'), 
            'port'=> 6150, 
        ]);
    }

    public function getInterfaces()
    {
        $query = new Query('/interface/print');
        return $this->client->query($query)->read();
    }
    
    public function getResources()
{
    $query = new Query('/system/resource/print');
    $resources = $this->client->query($query)->read();

    // Biasanya hanya 1 item, jadi kita ambil index pertama
    return $resources[0] ?? [];
}
}