@extends('Template.utama')

@section('title', 'Resource MikroTik')

@section('content')
<h1 class="h3 mb-4 text-gray-800">Resource MikroTik</h1>

<div class="row">
    <!-- CPU Load Chart -->
    <div class="col-md-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">CPU Load</h6>
            </div>
            <div class="card-body text-center" style="height: 460px;">
                <div style="max-width: 380px; margin: auto;">
                    <canvas id="cpuLoadChart"></canvas>
                </div>
                <div class="text-center mt-3">
                    <span id="cpu-load-text" class="font-weight-bold">Loading...</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Memory Chart -->
    <div class="col-md-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Memory Usage</h6>
            </div>
            <div class="card-body">
                <canvas id="memoryChart"></canvas>
                <div class="text-center mt-3">
                    <span id="memory-usage-text" class="font-weight-bold">Loading...</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Table Info -->
<div class="card shadow mb-4">
    <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Info Lainnya</h6></div>
    <div class="card-body">
        <table class="table table-bordered">
            <tr><th>Uptime</th><td id="uptime">Loading...</td></tr>
            <tr><th>Version</th><td id="version">Loading...</td></tr>
            <tr><th>Architecture</th><td id="arch">Loading...</td></tr>
            <tr><th>CPU Frequency</th><td id="cpu-freq">Loading...</td></tr>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let cpuChart, memoryChart;

    function createCharts() {
        const ctxCpu = document.getElementById('cpuLoadChart').getContext('2d');
        const ctxMem = document.getElementById('memoryChart').getContext('2d');

        cpuChart = new Chart(ctxCpu, {
            type: 'doughnut',
            data: {
                labels: ['Used (%)', 'Free (%)'],
                datasets: [{
                    data: [0, 100],
                    backgroundColor: ['#e74a3b', '#36b9cc']
                }]
            },
            options: {cutout: '70%'}
        });

        memoryChart = new Chart(ctxMem, {
            type: 'bar',
            data: {
                labels: ['Free Memory', 'Total Memory'],
                datasets: [{
                    label: 'Memory (MB)',
                    data: [0, 0],
                    backgroundColor: ['#1cc88a', '#f6c23e']
                }]
            },
            options: {
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    async function fetchResources() {
        try {
            const response = await fetch('/api/mikrotik/resources');
            const rawText = await response.text();
            const data = JSON.parse(rawText);

            // Update Teks
            document.getElementById('uptime').textContent = data['uptime'] ?? 'N/A';
            document.getElementById('version').textContent = data['version'] ?? 'N/A';
            document.getElementById('arch').textContent = data['architecture-name'] ?? 'N/A';
            document.getElementById('cpu-freq').textContent = data['cpu-frequency'] + ' MHz' ?? 'N/A';

            const cpuLoad = parseFloat(data['cpu-load'] ?? 0);
            const freeMem = data['free-memory'] ? (data['free-memory'] / 1024 / 1024).toFixed(2) : 0;
            const totalMem = data['total-memory'] ? (data['total-memory'] / 1024 / 1024).toFixed(2) : 0;

            // Update CPU Chart
            if (cpuChart) {
                cpuChart.data.datasets[0].data = [cpuLoad, 100 - cpuLoad];
                cpuChart.update();
            }
            document.getElementById('cpu-load-text').textContent = `${cpuLoad} %`;

            // Update Memory Chart
            if (memoryChart) {
                memoryChart.data.datasets[0].data = [freeMem, totalMem];
                memoryChart.update();
            }
            document.getElementById('memory-usage-text').textContent = `Free: ${freeMem} MB / Total: ${totalMem} MB`;

        } catch (err) {
            console.error('Error:', err.message);
        }
    }

    createCharts();
    setInterval(fetchResources, 1000);
    fetchResources();
</script>
@endpush
