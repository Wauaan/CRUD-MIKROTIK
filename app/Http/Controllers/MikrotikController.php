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
        return view('mikrotik.resources');
    }

    // API JSON - Interfaces
    public function api_Interfaces()
    {
        $interfaces = $this->mikrotikService->getInterfaces();
        return response()->json($interfaces);
    }

    public function view_interfaces()
    {
        return view('mikrotik.interfaces');
    }

    // Monitoring Traffic
    public function monitorInterface(Request $request)
    {
        $interfaceName = $request->input('name');

        if (!$interfaceName) {
            return response()->json(['error' => 'Interface name is required'], 400);
        }

        $result = $this->mikrotikService->monitorInterface($interfaceName);
        return response()->json($result);
    }

    // PPPoE - Server
    public function server()
    {
        $servers = $this->mikrotikService->getPPPoEServers();
        return response()->json($servers);
    }

    public function view_server()
    {
        return view('mikrotik.PPPoE.server');
    }

    // PPPoE - Secret
    public function secret()
    {
        $secrets = $this->mikrotikService->getSecrets();
        return response()->json($secrets);
    }

    public function view_secret()
    {
        return view('mikrotik.PPPoE.secret');
    }

    // PPPoE - Profile
    public function profile()
    {
        $profiles = $this->mikrotikService->getProfiles();
        return response()->json($profiles);
    }

    public function view_profile()
    {
        return view('mikrotik.PPPoE.profile');
    }

    // Tambah PPPoE Profile
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'local-address' => 'nullable|ip',
            'remote-address' => 'nullable|ip',
            'rate-limit' => 'nullable|string',
        ]);

        try {
            $data = [
                'name' => $request->input('name'),
                'rate-limit' => $request->input('rate-limit'),
                'only-one' => $request->boolean('only-one') ? 'yes' : 'no',
            ];

            if ($request->filled('local-address')) {
                $data['local-address'] = $request->input('local-address');
            }

            if ($request->filled('remote-address')) {
                $data['remote-address'] = $request->input('remote-address');
            }

            $this->mikrotikService->addPppoeProfile($data);

            return redirect()->back()->with('success', 'Berhasil menambahkan profile.');
        } catch (\Throwable $e) {
            \Log::error('Gagal tambah PPPoE Profile', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    // Update PPPoE Profile
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'local-address' => 'nullable|ip',
            'remote-address' => 'nullable|ip',
            'rate-limit' => 'nullable|string',
        ]);
    
        try {
            $data = [
                '.id' => $id,
                'name' => $request->input('name'),
                'rate-limit' => $request->input('rate-limit') ?? '',
                'only-one' => $request->boolean('only-one') ? 'yes' : 'no',
            ];
    
            // Kirim kosong kalau tidak diisi (agar bisa dihapus di Mikrotik)
            $data['local-address'] = $request->input('local-address', '');
            $data['remote-address'] = $request->input('remote-address', '');
    
            $this->mikrotikService->updatePppoeProfile($data);
    
            return redirect()->back()->with('success', 'Profile berhasil diupdate');
        } catch (\Throwable $e) {
            \Log::error('Gagal update profile', ['msg' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }
    

    // Hapus PPPoE Profile
    public function destroy($id)
    {
        try {
            $this->mikrotikService->deletePppoeProfile($id);
            return redirect()->back()->with('success', 'Profile berhasil dihapus');
        } catch (\Throwable $e) {
            \Log::error('Gagal menghapus profile', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('error', 'Gagal menghapus profile: ' . $e->getMessage());
        }
    }
}
