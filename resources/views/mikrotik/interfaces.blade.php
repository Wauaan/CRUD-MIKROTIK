@extends('Template.utama')

@section('title', 'Monitoring Interface Mikrotik')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">Monitoring Interface Mikrotik (Real-time)</h1>

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

            // Pilih interface default: WAN > ether1 > pertama
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
            await sleep(400); // delay antar interface
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
            await sleep(5000); // polling interval
        }
    }

    fetchInterfaceMeta().then(startPolling);
</script>
@endpush
