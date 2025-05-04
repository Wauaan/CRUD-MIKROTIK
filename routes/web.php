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
Route::get('/mikrotik/interfaces', [MikrotikController::class, 'interfaces']);
Route::get('/mikrotik/resources', [MikrotikController::class, 'resources']);
//api
Route::get('/mikrotik/resourcesa', [MikrotikController::class, 'resources_api']);
Route::get('/api/mikrotik/resources', [MikrotikController::class, 'apiResources']);

