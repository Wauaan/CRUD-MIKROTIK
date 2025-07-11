<?php

namespace App\Http\Controllers;

use App\Services\MikrotikService;
use Illuminate\Http\Request;

class MikrotikController extends Controller
{
    protected $mikrotikService;

    public function __construct(MikrotikService $mikrotikService)
    {
        $this->mikrotikService = $mikrotikService;
    }
    // API JSON - Resource
    public function api_Resources()
    {
        $resources_api = $this->mikrotikService->getResources();
        return response()->json($resources_api);
    }

    public function view_resources()
    {
        return view("mikrotik.resources");
    }

    // API JSON - Interfaces
    public function api_Interfaces()
    {
        $interfaces = $this->mikrotikService->getInterfaces();
        return response()->json($interfaces);
    }
    public function api_date()
    {
        $date = $this->mikrotikService->getDate();
        return response()->json($date);
    }
    public function api_router()
    {
        $date = $this->mikrotikService->getRouter();
        return response()->json($date);
    }
    public function Api_Address_Pool()
{
    $pools = $this->mikrotikService->getAddressPools();
    return response()->json($pools);
}
    public function view_interfaces()
    {
        return view("mikrotik.interfaces");
    }

    // Monitoring Traffic
    public function monitorInterface(Request $request)
    {
        $interfaceName = $request->input("name");

        if (!$interfaceName) {
            return response()->json(
                ["error" => "Interface name is required"],
                400
            );
        }

        $result = $this->mikrotikService->monitorInterface($interfaceName);
        return response()->json($result);
    }
    //PPPoE Active User
    public function activePppoeUsers()
    {
        $activeUsers = $this->mikrotikService->getActivePppoeUsers();
        return response()->json($activeUsers);
    }

    // Menampilkan semua PPPoE Server
    public function server()
    {
        $servers = $this->mikrotikService->getPPPoEServers();
        return response()->json($servers);
    }

    // Tampilan server PPPoE
    public function view_server()
    {
        // Mengambil data interface dari Mikrotik
        $interfaces = $this->mikrotikService->getInterfaces();
        // Mengirim data interface ke tampilan
        return view("mikrotik.PPPoE.server", compact("interfaces"));
    }

    // Tambah PPPoE Server
    public function storeServer(Request $request)
    {
        try {
            // Menyusun data untuk dikirim ke Mikrotik
            $data = [
                "service-name" => $request->input("service-name"),
                "interface" => $request->input("interface"),
                "default-profile" => $request->input(
                    "default-profile",
                    "default"
                ),
                "disabled" => $request->boolean("disabled") ? "yes" : "no",
            ];

            // Validasi jika interface kosong
            if (empty($data["interface"])) {
                return redirect()
                    ->route("PPPoE.Server")
                    ->with("error", "Interface harus diisi.");
            }

            // Kirim data ke Mikrotik
            $this->mikrotikService->addPppoeServer($data);

            // Flash message untuk sukses
            return redirect()
                ->route("PPPoE.Server")
                ->with("success", "Berhasil menambahkan PPPoE Server.");
        } catch (\Throwable $e) {
            // Log error untuk debugging
            \Log::error("Gagal menambahkan PPPoE Server", [
                "message" => $e->getMessage(),
            ]);

            // Flash message untuk error
            return redirect()
                ->route("PPPoE.Server")
                ->with(
                    "error",
                    "Gagal menambahkan PPPoE Server: " . $e->getMessage()
                );
        }
    }

    // Update PPPoE Server
    public function updateServer(Request $request, $id)
    {
        try {
            // Menyusun data untuk update
            $request->validate([
                "service-name" => "required|string",
                "interface" => "required|string",
                "default-profile" => "nullable|string",
                "disabled" => "nullable", // tidak pakai 'boolean' agar tidak error saat checkbox tidak dicentang
            ]);
            $data = [
                ".id" => $id,
                "service-name" => $request->input("service-name"),
                "interface" => $request->input("interface"),
                "default-profile" => $request->input(
                    "default-profile",
                    "default"
                ),
                "disabled" => $request->boolean("disabled") ? "yes" : "no",
            ];
            // Update server di Mikrotik
            $this->mikrotikService->updatePppoeServer($data);
            // Flash message untuk sukses
            return redirect()
                ->route("PPPoE.Server")
                ->with("success", "Server berhasil diupdate");
        } catch (\Throwable $e) {
            // Flash message untuk error
            return redirect()
                ->route("PPPoE.Server")
                ->with("error", "Gagal update Server: " . $e->getMessage());
        }
    }

    // Hapus PPPoE Server
    public function destroyServer($id)
    {
        try {
            // Hapus server di Mikrotik
            $this->mikrotikService->deletePppoeServer($id);

            // Flash message untuk sukses
            return redirect()
                ->route("PPPoE.Server")
                ->with("success", "Server berhasil dihapus");
        } catch (\Throwable $e) {
            // Flash message untuk error
            return redirect()
                ->route("PPPoE.Server")
                ->with("error", "Gagal menghapus Server: " . $e->getMessage());
        }
    }
    // PPPoE - Secret
    public function secret()
    {
        $secrets = $this->mikrotikService->getSecrets();
        return response()->json($secrets);
    }

    public function view_secret()
    {
        return view("mikrotik.PPPoE.secret");
    }

    // PPPoE - Profile
    public function profile()
    {
        $profiles = $this->mikrotikService->getProfiles();
        return response()->json($profiles);
    }

    public function view_profile()
    {
        return view("mikrotik.PPPoE.profile");
    }
    // Tambah PPPoE Profile
    public function storeProfile(Request $request)
    {
        $request->validate([
            "name" => "required|string",
            "local-address" => "nullable|ip",
            "remote-address" => "nullable|ip",
            "rate-limit" => "nullable|string",
        ]);

        try {
            $data = [
                "name" => $request->input("name"),
                "rate-limit" => $request->input("rate-limit"),
                "only-one" => $request->boolean("only-one") ? "yes" : "no",
            ];

            if ($request->filled("local-address")) {
                $data["local-address"] = $request->input("local-address");
            }

            if ($request->filled("remote-address")) {
                $data["remote-address"] = $request->input("remote-address");
            }

            $this->mikrotikService->addPppoeProfile($data);

            return redirect()
                ->route("PPPoE.Profile")
                ->with("success", "Profile berhasil ditambahkan.");
        } catch (\Throwable $e) {
            \Log::error("Gagal tambah PPPoE Profile", [
                "message" => $e->getMessage(),
                "trace" => $e->getTraceAsString(),
            ]);
            return redirect()
                ->route("PPPoE.Profile")
                ->with("error", "Gagal: " . $e->getMessage());
        }
    }

    // Update PPPoE Profile
    public function updateProfile(Request $request, $id)
    {
        $request->validate([
            "name" => "required|string",
            "local-address" => "nullable|ip",
            "remote-address" => "nullable|ip",
            "rate-limit" => "nullable|string",
        ]);

        try {
            $data = [
                ".id" => $id,
                "name" => $request->input("name"),
                "rate-limit" => $request->input("rate-limit") ?? "",
                "only-one" => $request->boolean("only-one") ? "yes" : "no",
            ];

            // Kirim kosong kalau tidak diisi (agar bisa dihapus di Mikrotik)
            $data["local-address"] = $request->input("local-address", "");
            $data["remote-address"] = $request->input("remote-address", "");

            $this->mikrotikService->updatePppoeProfile($data);

            return redirect()
                ->route("PPPoE.Profile")
                ->with("success", "Profile berhasil diupdate");
        } catch (\Throwable $e) {
            \Log::error("Gagal update profile", ["msg" => $e->getMessage()]);
            return redirect()
                ->route("PPPoE.Profile")
                ->with("error", "Gagal update: " . $e->getMessage());
        }
    }

    // Hapus PPPoE Profile
    public function destroyProfile($id)
    {
        try {
            $this->mikrotikService->deletePppoeProfile($id);
            return redirect()
                ->route("PPPoE.Profile")
                ->with("success", "Profile berhasil dihapus");
        } catch (\Throwable $e) {
            \Log::error("Gagal menghapus profile", [
                "message" => $e->getMessage(),
                "trace" => $e->getTraceAsString(),
            ]);
            return redirect()
                ->route("PPPoE.Profile")
                ->with("error", "Gagal menghapus profile: " . $e->getMessage());
        }
    }

    //Add PPPoE Secret
    public function storeSecret(Request $request)
    {
        $request->validate([
            "name" => "required|string",
            "password" => "required|string",
            "profile" => "required|string",
        ]);

        try {
            $data = [
                "name" => $request->input("name"),
                "password" => $request->input("password"),
                "service" => "pppoe",
                "profile" => $request->input("profile"),
            ];

            $this->mikrotikService->addPppoeSecret($data);

            return redirect()
                ->route("PPPoE.Secret")
                ->with("success", "Secret berhasil ditambahkan");
        } catch (\Throwable $e) {
            return redirect()
                ->route("PPPoE.Secret")
                ->with(
                    "error",
                    "Gagal menambahkan secret: " . $e->getMessage()
                );
        }
    }
    //Edit PPPoE Secret
    public function editSecret($id)
    {
        try {
            $secrets = $this->mikrotikService->getSecrets();
            $secret = collect($secrets)->firstWhere(".id", $id);

            if (!$secret) {
                return redirect()
                    ->route("PPPoE.Secret")
                    ->with("error", "Secret tidak ditemukan");
            }

            $profiles = $this->mikrotikService->getProfiles();

            return view(
                "mikrotik.PPPoE.secret_edit",
                compact("secret", "profiles")
            );
        } catch (\Throwable $e) {
            return redirect()
                ->route("PPPoE.Secret")
                ->with("error", "Gagal membuka form edit: " . $e->getMessage());
        }
    }
    //Update PPPoE Secret
    public function updateSecret(Request $request, $id)
    {
        $request->validate([
            "name" => "required|string",
            "profile" => "required|string",
        ]);

        try {
            $data = [
                ".id" => $id,
                "name" => $request->input("name"),
                "password" => $request->input("password"),
                "profile" => $request->input("profile"),
            ];

            $this->mikrotikService->updatePppoeSecret($data);

            return redirect()
                ->route("PPPoE.Secret")
                ->with("success", "Secret berhasil diupdate");
        } catch (\Throwable $e) {
            return redirect()
                ->route("PPPoE.Secret")
                ->with("error", "Gagal update secret: " . $e->getMessage());
        }
    }
    //Hapus PPPoE Secret
    public function destroySecret($id)
    {
        try {
            $this->mikrotikService->deletePppoeSecret($id);
            return redirect()
                ->route("PPPoE.Secret")
                ->with("success", "Secret berhasil dihapus");
        } catch (\Throwable $e) {
            return redirect()
                ->route("PPPoE.Secret")
                ->with("error", "Gagal menghapus secret: " . $e->getMessage());
        }
    }
    public function dashboard()
    {
        return view("index");
    }

    //Get Hotspot
    public function Api_server_profile_hotspot()
    {
        $servers = $this->mikrotikService->getHotspotProfiles();
        return response()->json($servers);
    }

    public function Api_hotspot_user()
    {
        $servers = $this->mikrotikService->getHotspotUser();
        return response()->json($servers);
    }

    public function Api_User_profile_hotspot()
    {
        $servers = $this->mikrotikService->getHotspotUserProfiles();
        return response()->json($servers);
    }
    public function Api_Active_hotspot()
    {
        $servers = $this->mikrotikService->getHotspotActive();
        return response()->json($servers);
    }

    //view hotspot
    public function view_server_hs()
    {
        return view("mikrotik.hotspot.server-profile");
    }

    public function view_hotspot_user_hs()
    {
        return view("mikrotik.hotspot.hotspot-user");
    }

    public function view_userprofile_hs()
    {
        return view("mikrotik.hotspot.user-profile");
    }

    public function view_active_user_hs()
    {
        return view("mikrotik.hotspot.active-user");
    }

    //CRUD Hotspot
    public function storeHotspotServerProfile(Request $request)
    {
        $request->validate([
            "name" => "required|string",
            "hotspot_address" => "nullable|string",
            "dns_name" => "nullable|string",
            "rate_limit" => "nullable|string",
        ]);

        try {
            $data = [
                "name" => $request->input("name"),
                "hotspot-address" => $request->input("hotspot_address"),
                "dns-name" => $request->input("dns_name"),
                "rate-limit" => $request->input("rate_limit"),
            ];

            $this->mikrotikService->addHotspotServerProfile($data);

            return redirect()
                ->route("hotspot.Server.profile")
                ->with("success", "Server profile berhasil ditambahkan");
        } catch (\Throwable $e) {
            return redirect()
                ->route("hotspot.Server.profile")
                ->with(
                    "error",
                    "Gagal menambahkan server profile: " . $e->getMessage()
                );
        }
    }

    public function deleteServerProfile($id)
    {
        try {
            $result = $this->mikrotikService->deleteHotspotServerProfile($id);
            return redirect()
                ->route("hotspot.Server.profile")
                ->with("success", "Server profile berhasil di Hapus");
        } catch (\Throwable $e) {
            return redirect()
                ->route("hotspot.Server.profile")
                ->with(
                    "error",
                    "Gagal Menghapus server profile: " . $e->getMessage()
                );
        }
    }

    public function updateServerProfile(Request $request, $id)
    {
        $request->validate([
            "name" => "required|string",
            "hotspot_address" => "nullable|string",
            "dns_name" => "nullable|string",
            "rate_limit" => "nullable|string",
        ]);

        try {
            $data = [
                ".id" => $id,
                "name" => $request->input("name"),
                "hotspot-address" => $request->input("hotspot_address"),
                "dns-name" => $request->input("dns_name"),
                "rate-limit" => $request->input("rate_limit"),
            ];

            $this->mikrotikService->updateHotspotServerProfile($data);

            return redirect()
                ->route("hotspot.Server.profile")
                ->with("success", "Server Profile berhasil diupdate");
        } catch (\Throwable $e) {
            return redirect()
                ->route("hotspot.Server.profile")
                ->with(
                    "error",
                    "Gagal update Server Profile: " . $e->getMessage()
                );
        }
    }

    public function storeHotspotUserProfile(Request $request)
{
    $request->validate([
        'name' => 'required|string',
        'address-pool' => 'required|string|not_in:-- Pilih Address Pool --',
        'shared-users' => 'nullable|numeric',
        'idle-timeout' => 'nullable|string',
    ]);

    try {
        $data = [
            'name' => $request->input('name'),
            'address-pool' => $request->input('address-pool'),
            'shared-users' => $request->input('shared-users'),
            'idle-timeout' => $request->input('idle-timeout'),
        ];

        $this->mikrotikService->addHotspotUserProfile($data);

        return redirect()
            ->route('hotspot.user.Profile')
            ->with('success', 'User Profile berhasil ditambahkan');
    } catch (\Throwable $e) {
        return redirect()
            ->route('hotspot.user.Profile')
            ->with('error', 'Gagal menambahkan User Profile: ' . $e->getMessage());
    }
}
public function updateHotspotUserProfile(Request $request, $id)
{
    $request->validate([
        'name' => 'required|string',
        'address-pool' => 'required|string',
        'shared-users' => 'nullable|integer',
        'idle-timeout' => 'nullable|string',
    ]);

    try {
        $data = [
            '.id' => $id,
            'name' => $request->input('name'),
            'address-pool' => $request->input('address-pool'),
            'shared-users' => $request->input('shared-users'),
            'idle-timeout' => $request->input('idle-timeout'),
        ];

        $this->mikrotikService->updateHotspotUserProfile($data);

        return redirect()->route('hotspot.user.Profile')->with('success', 'User Profile berhasil diupdate');
    } catch (\Throwable $e) {
        return redirect()->route('hotspot.user.Profile')->with('error', 'Gagal update User Profile: ' . $e->getMessage());
    }
}
public function deleteHotspotUserProfile($id)
{
    try {
        $this->mikrotikService->deleteHotspotUserProfile($id);
        return redirect()->back()->with('success', 'User Profile berhasil dihapus.');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Gagal menghapus User Profile: ' . $e->getMessage());
    }
}

public function storeHotspotUser(Request $request)
{
    $request->validate([
        'name' => 'required|string',
        'password' => 'required|string',
        'profile' => 'nullable|string',
        'comment' => 'nullable|string',
    ]);

    try {
        $this->mikrotikService->addHotspotUser([
            'name' => $request->name,
            'password' => $request->password,
            'profile' => $request->profile,
            'comment' => $request->comment,
        ]);

        return redirect()->back()->with('success', 'Hotspot User berhasil ditambahkan.');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Gagal menambahkan user: ' . $e->getMessage());
    }
}
public function updateHotspotUser(Request $request, $id, MikrotikService $mikrotik)
{
    $request->validate([
        'name' => 'required|string',
        'profile' => 'required|string',
    ]);

    try {
        $data = [
            '.id' => $id,
            'name' => $request->name,
            'profile' => $request->profile,
            'comment' => $request->comment,
        ];

        // hanya update password jika diisi
        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        $mikrotik->updateHotspotUser($data);
        return redirect()->back()->with('success', 'Hotspot user berhasil diperbarui.');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Gagal memperbarui user: ' . $e->getMessage());
    }
}

public function destroyHotspotUser($id)
{
    try {
    $this->mikrotikService->deleteHotspotUser($id); // tidak perlu if
    return redirect()->back()->with('success', 'Hotspot User berhasil dihapus.');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }

}

}
