<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MikrotikController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('index');
});

// Ini untuk slash di search bar website
//api
Route::get('/mikrotik/resources', [MikrotikController::class, 'view_resources'])->name('mikrotik.resorces');
Route::get('/mikrotik/interfaces', [MikrotikController::class, 'view_interfaces'])->name('mikrotik.interfaces');
Route::get('/mikrotik/interface/monitor', [MikrotikController::class, 'monitorInterface']);

Route::prefix('mikrotik/pppoe')->group(function () {
    Route::get('/server', [MikrotikController::class, 'view_server'])->name('PPPoE.Server');
    Route::get('/secret', [MikrotikController::class, 'view_secret'])->name('PPPoE.Secret');
    Route::get('/profile',[MikrotikController::class, 'view_profile'])->name('PPPoE.Profile');
});
Route::post('/mikrotik/pppoe/profile/store', [MikrotikController::class, 'store'])->name('pppoe-profiles.store');
Route::delete('/mikrotik/pppoe/profile/{id}', [MikrotikController::class, 'destroy']);
Route::put('/mikrotik/pppoe/profile/{index}', [MikrotikController::class, 'update'])->name('pppoe-profiles.update');





