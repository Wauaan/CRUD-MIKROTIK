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
            "host" => "id-12.tunnel.id",
            "user" => "laravelapi",
            "pass" => "Laravel123",
            "port" => 6150,
        ]);
    }

    public function getInterfaces()
    {
        // Menjalankan query untuk mendapatkan daftar interface dari Mikrotik
        $query = new Query("/interface/print");
        $interfaces = $this->client->query($query)->read();

        // Filter interface untuk mengabaikan yang berhubungan dengan PPPoE kecuali 'bridge-pppoe'
        $filteredInterfaces = array_filter($interfaces, function ($interface) {
            // Saring interface yang mengandung 'pppoe' atau 'tunnelid-laravelapi', kecuali 'bridge-pppoe'
            return !(
                strpos(strtolower($interface["name"]), "<pppoe") !== false &&
                strpos(strtolower($interface["name"]), "bridge-pppoe") === false
            ) &&
                strpos(
                    strtolower($interface["name"]),
                    "tunnelid-laravelapi"
                ) === false;
        });

        // Mengembalikan daftar interface yang sudah difilter
        return array_values($filteredInterfaces); // array_values untuk memastikan indeksnya berurutan
    }
    public function getDate()
    {
        $query = new Query("/system/clock/print");
        return $this->client->query($query)->read();
    }
    public function getRouter()
    {
        $query = new Query("/system/routerboard/print");
        return $this->client->query($query)->read();
    }

    public function getResources()
    {
        $query = new Query("/system/resource/print");
        $resources = $this->client->query($query)->read();

        // Biasanya hanya 1 item, jadi kita ambil index pertama
        return $resources[0] ?? [];
    }
    public function monitorInterface($interface)
    {
        $query = new Query("/interface/monitor-traffic");
        $query->equal("interface", $interface);
        $query->equal("once", "");

        $result = $this->client->query($query)->read();

        return $result[0] ?? [];
    }

    //Service PPPoE
    public function getPPPoEServers()
    {
        $query = new Query("/interface/pppoe-server/server/print");
        return $this->client->query($query)->read();
    }

    public function getSecrets()
    {
        $query = new Query("/ppp/secret/print");
        return $this->client->query($query)->read();
    }

    public function getProfiles()
    {
        $query = new Query("/ppp/profile/print");
        return $this->client->query($query)->read();
    }

    public function addPppoeProfile(array $data)
    {
        $query = new Query("/ppp/profile/add");

        foreach ($data as $key => $value) {
            $query->equal($key, $value);
        }

        return $this->client->query($query)->read();
    }

    public function deletePppoeProfile(string $name)
    {
        $query = new Query("/ppp/profile/remove");
        $query->equal("numbers", $name); // gunakan 'name' bukan '.id'

        return $this->client->query($query)->read();
    }

    public function updatePppoeProfile(array $data)
    {
        $query = new \RouterOS\Query("/ppp/profile/set");

        $query->equal(".id", $data[".id"]);

        // Hanya tambahkan field jika nilainya tidak null
        foreach ($data as $key => $value) {
            if ($key !== ".id" && $value !== null) {
                $query->equal($key, $value);
            }
        }

        return $this->client->query($query)->read();
    }
    //New
    // Add PPPoE Server
    public function addPppoeServer(array $data)
    {
        try {
            // Menyusun query untuk menambahkan server PPPoE
            $query = new Query("/interface/pppoe-server/server/add");

            foreach ($data as $key => $value) {
                $query->equal($key, $value);
            }

            // Kirim query ke Mikrotik
            $response = $this->client->query($query)->read();

            if (empty($response)) {
                throw new Exception(
                    "Mikrotik tidak mengembalikan respons yang valid."
                );
            }

            return $response;
        } catch (Exception $e) {
            // Tangani error
            \Log::error("Gagal menghubungi Mikrotik", [
                "message" => $e->getMessage(),
                "data" => $data,
            ]);
            throw new Exception(
                "Terjadi kesalahan saat menambahkan PPPoE Server: " .
                    $e->getMessage()
            );
        }
    }

    // Update PPPoE Server
    public function updatePppoeServer(array $data)
    {
        $query = new Query("/interface/pppoe-server/server/set");
        $query->equal(".id", $data[".id"]);

        foreach ($data as $key => $value) {
            if ($value !== null) {
                $query->equal($key, $value);
            }
        }

        return $this->client->query($query)->read();
    }

    // Delete PPPoE Server
    public function deletePppoeServer(string $id)
    {
        $query = new Query("/interface/pppoe-server/server/remove");
        $query->equal("numbers", $id);

        return $this->client->query($query)->read();
    }

    // Add PPPoE Secret
    public function addPppoeSecret(array $data)
    {
        $query = new Query("/ppp/secret/add");

        foreach ($data as $key => $value) {
            $query->equal($key, $value);
        }

        return $this->client->query($query)->read();
    }

    // Update PPPoE Secret
    public function updatePppoeSecret(array $data)
    {
        $query = new Query("/ppp/secret/set");
        $query->equal(".id", $data[".id"]);

        foreach ($data as $key => $value) {
            if ($value !== null) {
                $query->equal($key, $value);
            }
        }

        return $this->client->query($query)->read();
    }

    // Delete PPPoE Secret
    public function deletePppoeSecret(string $id)
    {
        $query = new Query("/ppp/secret/remove");
        $query->equal("numbers", $id);

        return $this->client->query($query)->read();
    }
    //Get User PPPoE
    public function getActivePppoeUsers()
    {
        try {
            return $this->client->query("/ppp/active/print")->read();
        } catch (\Exception $e) {
            \Log::error(
                "Gagal mengambil data PPPoE Aktif: " . $e->getMessage()
            );
            return [];
        }
    }

    public function getHotspotProfiles()
    {
        return $this->client->query("/ip/hotspot/profile/print")->read();
    }

    public function getHotspotUser()
    {
        return $this->client->query("/ip/hotspot/user/print")->read();
    }

    public function getHotspotUserProfiles()
    {
        return $this->client->query("/ip/hotspot/user/profile/print")->read();
    }
    public function getHotspotActive()
    {
        return $this->client->query("/ip/hotspot/active/print")->read();
    }

    public function addHotspotServerProfile(array $data)
    {
        $query = new \RouterOS\Query("/ip/hotspot/profile/add");

        foreach ($data as $key => $value) {
            if ($value !== null && $value !== "") {
                $query->equal($key, $value);
            }
        }

        return $this->client->query($query)->read();
    }

    public function deleteHotspotServerProfile(string $id)
    {
        $query = new Query("/ip/hotspot/profile/remove");
        $query->equal(".id", $id);

        return $this->client->query($query)->read();
    }

    public function updateHotspotServerProfile(array $data)
    {
        $query = new Query("/ip/hotspot/profile/set");
        $query->equal(".id", $data[".id"]);

        foreach ($data as $key => $value) {
            if ($key !== ".id" && $value !== null) {
                $query->equal($key, $value);
            }
        }

        return $this->client->query($query)->read();
    }
}
