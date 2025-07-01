<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('interface_traffic_logs', function (Blueprint $table) {
            $table->id();
            $table->string('interface_name');
            $table->bigInteger('tx_bps'); // bit per second
            $table->bigInteger('rx_bps');
            $table->timestamp('recorded_at');
        });
    }

    public function down(): void {
        Schema::dropIfExists('interface_traffic_logs');
    }
};
