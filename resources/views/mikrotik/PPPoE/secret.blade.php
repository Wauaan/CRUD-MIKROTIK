@extends('template.utama')
@section('title', 'PPPoE Secret')
@section('content')
<div class="container mt-4">
    <h3 class="mb-3">Daftar PPPoE Secrets</h3>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Service</th>
                <th>Profile</th>
                <th>Local Address</th>
                <th>Remote Address</th>
                <th>Disabled</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody id="secretBody"></tbody>
    </table>
</div>
@endsection

@push('scripts')
<script>
let secretsCache = [];

async function loadSecrets() {
    const response = await fetch('{{ url("/api/mikrotik/pppoe/secret") }}');
    const data = await response.json();
    secretsCache = data;

    const tbody = document.getElementById('secretBody');
    tbody.innerHTML = '';

    data.forEach(secret => {
        tbody.innerHTML += `
            <tr>
                <td>${secret.name || '-'}</td>
                <td>${secret.service || '-'}</td>
                <td>${secret.profile || '-'}</td>
                <td>${secret['local-address'] || '-'}</td>
                <td>${secret['remote-address'] || '-'}</td>
                <td>${secret.disabled === 'true' ? 'Ya' : 'Tidak'}</td>
                <td>
                    <div class="d-flex gap-1">
                        <form action="/pppoe-secrets/${secret['.id']}" method="POST" onsubmit="return confirm('Yakin ingin menghapus secret ini?')">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button class="btn btn-sm btn-danger">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
        `;
    });
}

document.addEventListener('DOMContentLoaded', loadSecrets);
</script>
@endpush
