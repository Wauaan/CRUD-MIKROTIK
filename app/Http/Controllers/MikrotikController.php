<?php

namespace App\Http\Controllers;

use App\Services\MikrotikService; // Pastikan untuk mengimpor service
use Illuminate\Http\Request;

class MikrotikController extends Controller
{
    protected $mikrotikService;

    public function __construct(MikrotikService $mikrotikService)
    {
        $this->mikrotikService = $mikrotikService; // Inisialisasi service
    }

// Jika menggunakan API JSON
// Controller Resources
public function api_Resources()
{
    $resources_api = $this->mikrotikService->getResources();
    return response()->json($resources_api);
}

    public function view_resources()
{
    return view('mikrotik.resources');
}

//Controller Interfaces
public function api_Interfaces()
{
    $interfaces = $this->mikrotikService->getInterfaces();
    return response()->json($interfaces);
}
public function view_interfaces()
{
    return view('mikrotik.interfaces');
}

//Controller Monitoring-Traffic
public function monitorInterface(Request $request)
{
    $interfaceName = $request->input('name');

    if (!$interfaceName) {
        return response()->json(['error' => 'Interface name is required'], 400);
    }

    $result = $this->mikrotikService->monitorInterface($interfaceName);

    return response()->json($result);
}

//Controller PPPoE
public function server()
{
    $servers = $this->mikrotikService->getPPPoEServers();
    return response()->json($servers);
}
public function view_server()
{
    return view('mikrotik.PPPoE.server');
}

public function secret()
{
    $secrets = $this->mikrotikService->getSecrets();
    return response()->json($secrets);
}
public function view_secret()
{
    return view('mikrotik.PPPoE.secret');
}

public function profile()
{
    $profiles = $this->mikrotikService->getProfiles();
    return response()->json($profiles);
}
public function view_profile()
{
    return view('mikrotik.PPPoE.profile');
}

public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string',
        'local-address' => 'required|ip',
        'remote-address' => 'required|ip',
        'rate-limit' => 'nullable|string',
    ]);

    try {
        $response = $this->mikrotikService->addPppoeProfile($request->only([
            'name',
            'local-address',
            'remote-address',
            'rate-limit',
        ]));

        return redirect()->back()->with('success', 'Berhasil: ');
    } catch (\Throwable $e) {
        \Log::error('Gagal tambah PPPoE Profile', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
        return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
    }
}

}