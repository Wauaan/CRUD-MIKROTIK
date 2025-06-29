<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\MikrotikController;
use App\Http\Controllers\NextDnsController;
use App\Http\Controllers\Auth\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
// TODO: Landing page (bisa diganti ke halaman login jika user belum auth)
Route::get("/", function () {
    return view("welcome");
});

// TODO: Dashboard Mikrotik (halaman utama setelah login)
Route::get("/", [MikrotikController::class, "dashboard"])
    ->middleware(["auth", "verified"])
    ->name("dashboard");

// TODO: Profile Management (edit, update, hapus profil)
Route::middleware("auth")->group(function () {
    // TODO: Edit Profile
    Route::get("/profile", [ProfileController::class, "edit"])->name("profile.edit");

    // TODO: Update Profile
    Route::patch("/profile", [ProfileController::class, "update"])->name("profile.update");

    // TODO: Delete Profile
    Route::delete("/profile", [ProfileController::class, "destroy"])->name("profile.destroy");

    // TODO: Dashboard ulang (redundant, bisa dievaluasi)
    Route::get("/", [MikrotikController::class, "dashboard"]);

    // TODO: Mikrotik Resource dan Interface Monitoring
    Route::get("/mikrotik/resources", [MikrotikController::class, "view_resources"])->name("mikrotik.resorces"); // TODO: Typo pada "resources"
    Route::get("/mikrotik/interfaces", [MikrotikController::class, "view_interfaces"])->name("mikrotik.interfaces");
    Route::get("/mikrotik/interface/monitor", [MikrotikController::class, "monitorInterface"]);

    // TODO: PPPoE Profile CRUD
    Route::post("/mikrotik/pppoe/profile/store", [MikrotikController::class, "storeProfile"])->name("pppoe-profiles.store");
    Route::delete("/mikrotik/pppoe/profile/{id}", [MikrotikController::class, "destroyProfile"]);
    Route::put("/mikrotik/pppoe/profile/{index}", [MikrotikController::class, "updateProfile"])->name("pppoe-profiles.update");
    Route::post("/mikrotik/pppoe/profile/{id}/disable", [MikrotikController::class, "disableProfile"])->name("pppoe-profiles.disable");

    // TODO: PPPoE Server, Secret, Profile Views dan CRUD
    Route::prefix("mikrotik/pppoe")->group(function () {
        // TODO: View PPPoE Server
        Route::get("/server", [MikrotikController::class, "view_server"])->name("PPPoE.Server");

        // TODO: View PPPoE Secret
        Route::get("/secret", [MikrotikController::class, "view_secret"])->name("PPPoE.Secret");

        // TODO: View PPPoE Profile
        Route::get("/profile", [MikrotikController::class, "view_profile"])->name("PPPoE.Profile");

        // TODO: CRUD PPPoE Server
        Route::post("/server/store", [MikrotikController::class, "storeServer"])->name("pppoe-servers.store");
        Route::put("/server/{id}", [MikrotikController::class, "updateServer"])->name("pppoe-servers.update");
        Route::delete("/server/{id}", [MikrotikController::class, "destroyServer"])->name("pppoe-servers.delete");

        // TODO: CRUD PPPoE Secret
        Route::post("/secret/store", [MikrotikController::class, "storeSecret"])->name("pppoe-secrets.store");
        Route::put("/secret/{id}", [MikrotikController::class, "updateSecret"])->name("pppoe-secrets.update");
        Route::delete("/secret/{id}", [MikrotikController::class, "destroySecret"])->name("pppoe-secrets.delete");
    });

    // TODO: NextDNS Denylist Management
    Route::get("/nextdns/denylist", [NextDnsController::class, "showDenylist"])->name("denylist");
    Route::delete("/denylist/{id}", [NextDnsController::class, "deleteDenylist"])->name("denylist.delete");
    Route::post("/nextdns/denylist/toggle", [NextDnsController::class, "toggleActive"]);
    Route::post("/nextdns/denylist/add", [NextDnsController::class, "store"])->name("denylist.store");

    // TODO: Hotspot Server Profile, User, dan Active User Management
    Route::prefix("mikrotik/hotspot")->group(function () {
        // TODO: View Server Profile
        Route::get("/server-profile", [MikrotikController::class, "view_server_hs"])->name("hotspot.Server.profile");

        // TODO: View Hotspot User
        Route::get("/hotspot-user", [MikrotikController::class, "view_hotspot_user_hs"])->name("hotspot.user");

        // TODO: View User Profile
        Route::get("/user-profile", [MikrotikController::class, "view_userprofile_hs"])->name("hotspot.user.Profile");

        // TODO: View Active User
        Route::get("/active", [MikrotikController::class, "view_active_user_hs"])->name("active.hotspot");

        // TODO: CRUD Server Profile
        Route::post("/server-profile/store", [MikrotikController::class, "storeHotspotServerProfile"])->name("hotspot.server-profile.store");
        Route::delete("/server-profile/{id}", [MikrotikController::class, "deleteServerProfile"])->name("hotspot.server-profile.delete");
        Route::put("/server-profile/{id}", [MikrotikController::class, "updateServerProfile"])->name("hotspot.server-profile.update");

        // TODO: CRUD Hotspot User Profile
        Route::post('/user-profile/store', [MikrotikController::class, 'storeHotspotUserProfile'])->name('hotspot.user-profile.store');
        Route::put('/user-profile/{id}', [MikrotikController::class, 'updateHotspotUserProfile'])->name('hotspot.user-profile.update');
        Route::delete('/user-profile/{id}', [MikrotikController::class, 'deleteHotspotUserProfile'])->name('hotspot.user-profile.delete');

        // TODO: CRUD Hotspot User
        Route::post('/hotspot-user/store', [MikrotikController::class, 'storeHotspotUser'])->name('hotspot.user.store');
        Route::put('/hotspot-user/{id}', [MikrotikController::class, 'updateHotspotUser'])->name('hotspot.user.update');
        Route::delete('/hotspot-user/{id}', [MikrotikController::class, 'destroyHotspotUser'])->name('hotspot.user.destroy');
    });
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
});

// TODO: Login & Logout Routes
Route::get("/login", [AuthenticatedSessionController::class, "create"])->middleware("guest")->name("login");
Route::post("/login", [AuthenticatedSessionController::class, "store"])->middleware("guest");
Route::post("/logout", [AuthenticatedSessionController::class, "destroy"])->middleware("auth")->name("logout");
