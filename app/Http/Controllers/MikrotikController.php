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

    // Metode untuk mengambil dan menampilkan data interfaces
    public function interfaces()
    {
        $interfaces = $this->mikrotikService->getInterfaces(); // Mengambil data interfaces
        return view('mikrotik.interfaces', compact('interfaces')); // Mengirim data ke view
    }

    public function resources()
    {
        $resources = $this->mikrotikService->getResources();
        return view('mikrotik.resources', compact('resources'));
    }
// Jika menggunakan API JSON
public function apiResources()
{
    $resources_api = $this->mikrotikService->getResources();
    return response()->json($resources_api);
}
    public function resources_api()
{
    return view('mikrotik.resources_api');
}
}