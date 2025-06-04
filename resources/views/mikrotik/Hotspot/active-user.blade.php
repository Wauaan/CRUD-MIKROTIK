@extends('template.utama')
@section('title', 'Hotspot Active Users')

@section('content')
<div class="container mt-4">
    <h3 class="mb-3">Pengguna Hotspot Aktif</h3>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>User</th>
                <th>Address</th>
                <th>MAC Address</th>
                <th>Uptime</th>
                <th>Login By</th>
            </tr>
        </thead>
        <tbody id="activeUserBody"></tbody>
    </table>
</div>
@endsection

@push('scripts')
<script>
    async function loadActiveUsers() {
        const response = await fetch('{{ url("/api/mikrotik/hotspot/active") }}');
        const data = await response.json();
        const tbody = document.getElementById('activeUserBody');
        tbody.innerHTML = '';

        data.forEach(active => {
            tbody.innerHTML += `
                <tr>
                    <td>${active.user || '-'}</td>
                    <td>${active.address || '-'}</td>
                    <td>${active['mac-address'] || '-'}</td>
                    <td>${active.uptime || '-'}</td>
                    <td>${active['login-by'] || '-'}</td>
                </tr>
            `;
        });
    }

    document.addEventListener('DOMContentLoaded', loadActiveUsers);
</script>
@endpush
