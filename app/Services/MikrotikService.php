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
public function monitorInterface($interface)
{
    $query = new Query('/interface/monitor-traffic');
    $query->equal('interface', $interface);
    $query->equal('once', '');

    $result = $this->client->query($query)->read();

    return $result[0] ?? [];
}

//Service PPPoE
public function getPPPoEServers()
{
    $query = new Query('/interface/pppoe-server/server/print');
    return $this->client->query($query)->read();
}

public function getSecrets()
{
    $query = new Query('/ppp/secret/print');
    return $this->client->query($query)->read();
}

public function getProfiles()
{
    $query = new Query('/ppp/profile/print');
    return $this->client->query($query)->read();
}

public function addPppoeProfile(array $data)
{
    $query = new Query('/ppp/profile/add');

    foreach ($data as $key => $value) {
        $query->equal($key, $value);
    }

    return $this->client->query($query)->read();
}

}