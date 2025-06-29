<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MikrotikController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// TODO: Get authenticated user info
Route::middleware("auth:sanctum")->get("/user", function (Request $request) {
    return $request->user();
});

// TODO: API Mikrotik Data
Route::get("/mikrotik/resources", [MikrotikController::class, "api_Resources"]);
Route::get("/mikrotik/interfaces", [MikrotikController::class, "api_Interfaces"]);
Route::get("/mikrotik/date", [MikrotikController::class, "api_date"]);
Route::get("/mikrotik/router", [MikrotikController::class, "api_router"]);
Route::get("/mikrotik/interface/monitor", [MikrotikController::class, "monitorInterface"]);
Route::get('/mikrotik/address-pool', [MikrotikController::class, 'Api_Address_Pool']);



// TODO: API PPPoE
Route::prefix("mikrotik/pppoe")->group(function () {
    Route::get("/server", [MikrotikController::class, "server"]);
    Route::get("/secret", [MikrotikController::class, "secret"]);
    Route::get("/profile", [MikrotikController::class, "profile"]);
    Route::get("/active", [MikrotikController::class, "activePppoeUsers"]);
});

// TODO: API Hotspot
Route::prefix("mikrotik/hotspot")->group(function () {
    Route::get("/server-profile", [MikrotikController::class, "Api_server_profile_hotspot"]);
    Route::get("/hotspot-user", [MikrotikController::class, "Api_hotspot_user"]);
    Route::get("/user-profile", [MikrotikController::class, "Api_User_profile_hotspot"]);
    Route::get("/active", [MikrotikController::class, "Api_active_hotspot"]);
});
