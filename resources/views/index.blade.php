@extends('Template.utama')

@section('title', 'Dashboard')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
</div>

<!-- Content Row -->
<div class="row">
    <!-- Router Info -->
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="h5 font-weight-bold text-success text-uppercase mb-1">Router Info</div>
                        <div class="h6 mb-0 font-weight-bold text-gray-800">Model Router: <span id="router-info-model">Loading...</span></div>
                        <div class="h6 mb-0 font-weight-bold text-gray-800">Board Name: <span id="router-info-board">Loading...</span></div>
                        <div class="h6 mb-0 font-weight-bold text-gray-800">Version: <span id="cpu-info-version">Loading...</span></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-cogs fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CPU Info -->
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="h5 font-weight-bold text-success text-uppercase mb-1">CPU Info</div>
                        <div class="h6 mb-0 font-weight-bold text-gray-800">CPU Load: <span id="cpu-info-load">Loading...</span></div>
                        <div class="h6 mb-0 font-weight-bold text-gray-800">Free Memory: <span id="cpu-info-memory">Loading...</span></div>
                        <div class="h6 mb-0 font-weight-bold text-gray-800">Free HDD: <span id="cpu-info-hdd">Loading...</span></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-cogs fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Date -->
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="h5 font-weight-bold text-success text-uppercase mb-1">Tanggal & Waktu</div>
                        <div class="h6 mb-0 font-weight-bold text-gray-800">Tanggal: <span id="tanggal">Loading...</span></div>
                        <div class="h6 mb-0 font-weight-bold text-gray-800">Jam: <span id="jam">Loading...</span></div>
                        <div class="h6 mb-0 font-weight-bold text-gray-800">Time Zone: <span id="time-zone">Loading...</span></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- PPPoE Active Table -->
<div class="card mb-4">
    <div class="card-header">Tabel PPPoE</div>
    <div class="card-body">
        <div class="row">
            <!-- PPPoE Info Cards -->
            <div class="col-lg-3 mb-4">
                <div class="card bg-primary text-white shadow">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-white small">User Aktif</div>
                            <div class="h5 mb-0 font-weight-bold" id="active-pppoe-count">Loading...</div>
                        </div>
                        <i class="fas fa-laptop fa-2x text-white-50"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 mb-4">
                <div class="card bg-success text-white shadow">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-white small">Jumlah Server</div>
                            <div class="h5 mb-0 font-weight-bold" id="pppoe-server-count">Loading...</div>
                        </div>
                        <i class="fas fa-server fa-2x text-white-50"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 mb-4">
                <div class="card bg-info text-white shadow">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-white small">Jumlah Secret</div>
                            <div class="h5 mb-0 font-weight-bold" id="pppoe-secret-count">Loading...</div>
                        </div>
                        <i class="fas fa-user-lock fa-2x text-white-50"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 mb-4">
                <div class="card bg-warning text-white shadow">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-white small">Jumlah Profile</div>
                            <div class="h5 mb-0 font-weight-bold" id="pppoe-profile-count">Loading...</div>
                        </div>
                        <i class="fas fa-id-badge fa-2x text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card-body">
        <table class="table table-bordered" id="pppoe-active-table">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>IP Address</th>
                    <th>MAC Address</th>
                    <th>Uptime</th>
                </tr>
            </thead>
            <tbody id="pppoe-body">
                <tr><td colspan="6" class="text-center">Memuat data...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Hotspot Active Table -->
<div class="card mb-4">
    <div class="card-header">Tabel Hotspot</div>
    <div class="card-body">
        <div class="row">
            <!-- Hotspot Info Cards -->
            <div class="col-lg-3 mb-4">
                <div class="card bg-primary text-white shadow">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-white small">User Aktif</div>
                            <div class="h5 mb-0 font-weight-bold" id="active-hotspot-count">2</div>
                        </div>
                        <i class="fas fa-laptop fa-2x text-white-50"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 mb-4">
                <div class="card bg-success text-white shadow">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-white small">Jumlah Server</div>
                            <div class="h5 mb-0 font-weight-bold" id="hotspot-server-count">1</div>
                        </div>
                        <i class="fas fa-server fa-2x text-white-50"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 mb-4">
                <div class="card bg-info text-white shadow">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-white small">Jumlah Akun Hotspot</div>
                            <div class="h5 mb-0 font-weight-bold" id="hotspot-secret-count">8</div>
                        </div>
                        <i class="fas fa-user-lock fa-2x text-white-50"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 mb-4">
                <div class="card bg-warning text-white shadow">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-white small">Jumlah User Profile</div>
                            <div class="h5 mb-0 font-weight-bold" id="hotspot-profile-count">7</div>
                        </div>
                        <i class="fas fa-id-badge fa-2x text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card-body">
        <table class="table table-bordered" id="hotspot-active-table">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>IP Address</th>
                    <th>MAC Address</th>
                    <th>Uptime</th>
                </tr>
            </thead>
            <tbody id="hotspot-body">
                {{-- <tr><td colspan="6" class="text-center">Memuat data...</td></tr> --}}
                <tr>
                    <td>C0A86433</td>
                    <td>content</td>
                    <td>192.168.100.51</td>
                    <td>20:34:FB:D2:33:96</td>
                    <td>3m31s</td>
                </tr>
                <tr>
                    <td>C0A864773</td>
                    <td>hr</td>
                    <td>192.168.100.119</td>
                    <td>B6:83:76:09:2A:74</td>
                    <td>27m42s</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
    <div class="card mb-4">
        <div class="card-header">Tabel Interface (Real-time TX/RX)</div>
        <div class="card-body">
            <table class="table table-bordered" id="interface-table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Tipe</th>
                        <th>TX</th>
                        <th>RX</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="interface-body">
                    <!-- Diisi lewat JS -->
                </tbody>
            </table>
        </div>
    </div>
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Grafik TX/RX Realtime</span>
            <select id="interface-select" class="form-control w-auto"></select>
        </div>
        <div class="card-body">
            <canvas id="interfaceChart" height="100"></canvas>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let labels = [], txData = [], rxData = [];
    let selectedInterface = '';
    let currentUnit = 'bps';
    let interfaceMeta = [];

    const ctx = document.getElementById('interfaceChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [
                { label: 'TX', data: txData, borderColor: 'rgba(75, 192, 192, 1)', fill: false },
                { label: 'RX', data: rxData, borderColor: 'rgba(255, 99, 132, 1)', fill: false }
            ]
        },
        options: {
            responsive: true,
            animation: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: currentUnit }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: ctx => `${ctx.dataset.label}: ${ctx.formattedValue} ${currentUnit}`
                    }
                }
            }
        }
    });

    function formatBits(bits) {
        if (bits >= 1_000_000) return { value: (bits / 1_000_000).toFixed(2), unit: 'Mbps' };
        if (bits >= 1_000) return { value: (bits / 1_000).toFixed(2), unit: 'Kbps' };
        return { value: bits.toFixed(2), unit: 'bps' };
    }

    function updateChart(txBits, rxBits) {
        const now = new Date().toLocaleTimeString();
        const tx = formatBits(txBits), rx = formatBits(rxBits);
        currentUnit = tx.unit;

        if (labels.length >= 20) {
            labels.shift(); txData.shift(); rxData.shift();
        }

        labels.push(now);
        txData.push(tx.value);
        rxData.push(rx.value);

        chart.options.scales.y.title.text = currentUnit;
        chart.update();
    }

    async function fetchDashboardInfo() {
        try {
            const res = await fetch('/api/mikrotik/resources');
            const data = await res.json();
            document.getElementById('cpu-info-load').innerText = data['cpu-load'] + ' %';
            document.getElementById('cpu-info-version').innerText = data['version'];
            document.getElementById('cpu-info-memory').innerText = (data['free-memory'] / 1048576).toFixed(2) + ' MB / ' + (data['total-memory'] / 1048576).toFixed(2) + ' MB';
            document.getElementById('cpu-info-hdd').innerText = (data['free-hdd-space'] / 1048576).toFixed(2) + ' MB / ' + (data['total-hdd-space'] / 1048576).toFixed(2) + ' MB';
        } catch (err) {
            console.error('Error fetching dashboard info:', err);
        }
    }

    async function fetchDashboardBoard() {
        try {
            const res = await fetch('/api/mikrotik/router');
            const data = await res.json();
            const board = Array.isArray(data) ? data[0] : data;
            document.getElementById('router-info-model').innerText = board['model'];
            document.getElementById('router-info-board').innerText = board['board-name'];
        } catch (err) {
            console.error('Error fetching router info:', err);
        }
    }

    async function fetchSystemDate() {
        try {
            const res = await fetch('/api/mikrotik/date');
            const data = await res.json();
            const d = Array.isArray(data) ? data[0] : data;
            document.getElementById('tanggal').innerText = d['date'];
            document.getElementById('jam').innerText = d['time'];
            document.getElementById('time-zone').innerText = d['time-zone-name'];
        } catch (err) {
            console.error('Error fetching system date:', err);
        }
    }

    async function fetchActivePppoeUsers() {
        try {
            const res = await fetch('/api/mikrotik/pppoe/active');
            const data = await res.json();
            const tbody = document.getElementById('pppoe-body');
            tbody.innerHTML = '';
            if (!Array.isArray(data) || data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="6" class="text-center">Tidak ada user aktif</td></tr>`;
                document.getElementById('active-pppoe-count').innerText = '0 user';
                return;
            }
            data.forEach((user, index) => {
                tbody.innerHTML += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${user.name}</td>
                        <td>${user.address}</td>
                        <td>${user['caller-id']}</td>
                        <td>${user.uptime}</td>
                    </tr>`;
            });
            document.getElementById('active-pppoe-count').innerText = data.length + ' user';
        } catch (err) {
            console.error('Error fetching active users:', err);
        }
    }

    async function fetchPppoeSecret() {
        try {
            const res = await fetch('/api/mikrotik/pppoe/secret');
            const data = await res.json();
            document.getElementById('pppoe-secret-count').innerText = data.length;
        } catch (err) {
            console.error('Error fetching PPPoE secrets:', err);
        }
    }

    async function fetchPppoeProfile() {
        try {
            const res = await fetch('/api/mikrotik/pppoe/profile');
            const data = await res.json();
            document.getElementById('pppoe-profile-count').innerText = data.length;
        } catch (err) {
            console.error('Error fetching PPPoE profiles:', err);
        }
    }

    async function fetchPppoeServer() {
        try {
            const res = await fetch('/api/mikrotik/pppoe/server');
            const data = await res.json();
            document.getElementById('pppoe-server-count').innerText = data.length;
        } catch (err) {
            console.error('Error fetching PPPoE servers:', err);
        }
    }

    async function fetchInterfaceMeta() {
        try {
            const res = await fetch('/api/mikrotik/interfaces');
            const data = await res.json();
            interfaceMeta = data;

            const dropdown = document.getElementById('interface-select');
            const tbody = document.getElementById('interface-body');
            dropdown.innerHTML = '';
            tbody.innerHTML = '';

            data.forEach((iface, idx) => {
                const opt = document.createElement('option');
                opt.value = iface.name;
                opt.text = iface.name;
                dropdown.add(opt);

                tbody.innerHTML += `
                    <tr id="row-${iface.name}">
                        <td>${iface.name}</td>
                        <td>${iface.type}</td>
                        <td id="tx-${iface.name}">-</td>
                        <td id="rx-${iface.name}">-</td>
                        <td>${iface.running === 'true' ? 'Running' : 'Down'}</td>
                    </tr>
                `;
            });

            const wan = data.find(i => i.name.toLowerCase().includes('wan'));
            const ether1 = data.find(i => i.name.toLowerCase() === 'ether1');
            selectedInterface = wan?.name || ether1?.name || data[0]?.name;
            dropdown.value = selectedInterface;

        } catch (err) {
            console.error('Gagal load interface:', err);
        }
    }

    function sleep(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    async function fetchAllTraffic() {
        for (let iface of interfaceMeta) {
            try {
                const res = await fetch(`/api/mikrotik/interface/monitor?name=${iface.name}`);
                const data = await res.json();
                const tx = formatBits(parseFloat(data['tx-bits-per-second'] || 0));
                const rx = formatBits(parseFloat(data['rx-bits-per-second'] || 0));
                document.getElementById(`tx-${iface.name}`).innerText = `${tx.value} ${tx.unit}`;
                document.getElementById(`rx-${iface.name}`).innerText = `${rx.value} ${rx.unit}`;
            } catch (e) {
                console.error('Monitor error:', iface.name);
            }
            await sleep(400);
        }
    }

    async function fetchMonitorTraffic() {
        if (!selectedInterface) return;
        try {
            const res = await fetch(`/api/mikrotik/interface/monitor?name=${selectedInterface}`);
            const data = await res.json();

            const txBits = parseFloat(data['tx-bits-per-second'] || 0);
            const rxBits = parseFloat(data['rx-bits-per-second'] || 0);

            updateChart(txBits, rxBits);
        } catch (e) {
            console.error('Monitor grafik error:', e);
        }
    }

    document.getElementById('interface-select').addEventListener('change', function () {
        selectedInterface = this.value;
        labels = []; txData = []; rxData = [];
        chart.data.labels = labels;
        chart.data.datasets[0].data = txData;
        chart.data.datasets[1].data = rxData;
        chart.update();
    });

    async function startPolling() {
        while (true) {
            await fetchAllTraffic();
            await fetchMonitorTraffic();
            await sleep(50000);
        }
    }

function scheduleDashboardData() {
    setTimeout(fetchDashboardInfo, 1000);           // langsung
    setTimeout(fetchDashboardBoard, 5000);       // setelah 5 detik
    setTimeout(fetchSystemDate, 10000);          // setelah 10 detik
    setTimeout(fetchActivePppoeUsers, 15000);    // setelah 15 detik
    setTimeout(fetchPppoeSecret, 20000);         // setelah 20 detik
    setTimeout(fetchPppoeProfile, 25000);        // setelah 25 detik
    setTimeout(fetchPppoeServer, 30000);         // setelah 30 detik

    // Jadwalkan ulang setelah 30 menit
    setTimeout(scheduleDashboardData, 5000);  // 30 menit
}

// Inisialisasi saat halaman dimuat
fetchInterfaceMeta().then(startPolling);  // tetap dipanggil langsung
scheduleDashboardData();                  // mulai penjadwalan dashboard

</script>

@endpush
