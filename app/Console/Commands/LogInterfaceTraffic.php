<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Services\MikrotikService;

class LogInterfaceTraffic extends Command
{
    protected $signature = 'log:interface-traffic';
    protected $description = 'Log MikroTik interface TX/RX ke database';

    protected $mikrotik;

    public function __construct(MikrotikService $mikrotik)
    {
        parent::__construct();
        $this->mikrotik = $mikrotik;
    }

    public function handle()
    {
        try {
            $interfaces = $this->mikrotik->getInterfaces();

            foreach ($interfaces as $iface) {
                $monitor = $this->mikrotik->monitorInterface($iface['name']);

                DB::table('interface_traffic_logs')->insert([
                    'interface_name' => $iface['name'],
                    'tx_bps' => (int)($monitor['tx-bits-per-second'] ?? 0),
                    'rx_bps' => (int)($monitor['rx-bits-per-second'] ?? 0),
                    'recorded_at' => now(),
                ]);

                usleep(200000); // delay 200ms agar tidak overload MikroTik
            }

            $this->info('Interface traffic berhasil dicatat.');
        } catch (\Exception $e) {
            $this->error('Gagal ambil data interface: ' . $e->getMessage());
        }
    }
}
