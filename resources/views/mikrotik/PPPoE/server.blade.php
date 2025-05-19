@extends('template.utama')
@section('title', 'PPPoE Server')
@section('content')
<div class="container mt-4">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @elseif(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <h3 class="mb-3">Daftar PPPoE Servers</h3>

    <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#addServerModal">Tambah Server</button>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Interface</th>
                <th>Disabled</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody id="serverBody"></tbody>
    </table>
</div>

<!-- Modal Tambah Server -->
<div class="modal fade" id="addServerModal" tabindex="-1" role="dialog" aria-labelledby="addServerLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="{{ route('pppoe-servers.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah PPPoE Server</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Server</label>
                        <input type="text" class="form-control" name="service-name" required>
                    </div>
                    <div class="form-group">
                        <label>Interface</label>
                        <select name="interface" class="form-control" required>
                            <option value="">Pilih Interface</option>
                            @foreach($interfaces as $interface)
                                <option value="{{ $interface['name'] }}">{{ $interface['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group form-check">
                        <input type="hidden" class="form-control" name="default-profile" value="default" readonly>
                        <input type="hidden" name="disabled" value="false">
                        <input type="checkbox" class="form-check-input" name="disabled" id="disabledCheck">
                        <label class="form-check-label" for="disabledCheck">Disabled</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Server -->
<div class="modal fade" id="editServerModal" tabindex="-1" role="dialog" aria-labelledby="editServerLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="POST" id="editServerForm">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit PPPoE Server</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="editServerId">
                    <div class="form-group">
                        <label>Nama Server</label>
                        <input type="text" class="form-control" name="service-name" id="editServerName" required>
                    </div>
                    <div class="form-group">
                        <label>Interface</label>
                        <select name="interface" class="form-control" id="editServerInterface" required>
                            @foreach($interfaces as $interface)
                                <option value="{{ $interface['name'] }}">{{ $interface['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group form-check">
                        <input type="hidden" name="disabled" value="false">
                        <input type="checkbox" class="form-check-input" name="disabled" id="editDisabledCheck">
                        <label class="form-check-label" for="editDisabledCheck">Disabled</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let serversCache = [];

    async function loadServers() {
        const response = await fetch('{{ url("/api/mikrotik/pppoe/server") }}');
        const data = await response.json();
        serversCache = data;

        const tbody = document.getElementById('serverBody');
        tbody.innerHTML = '';

        data.forEach((server, index) => {
            const deleteForm = `
                <form action="/mikrotik/pppoe/server/${server['.id']}" method="POST" onsubmit="return confirm('Yakin ingin menghapus server ini?')">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="DELETE">
                    <button class="btn btn-sm btn-danger">Hapus</button>
                </form>
            `;

            tbody.innerHTML += `
                <tr>
                    <td>${server['service-name'] || '-'}</td>
                    <td>${server.interface || '-'}</td>
                    <td>${server.disabled === 'true' ? 'Ya' : 'Tidak'}</td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick="openEditModal('${server['.id']}')">Edit</button>
                        ${deleteForm}
                    </td>
                </tr>
            `;
        });
    }

function openEditModal(id) {
    const server = serversCache.find(s => s['.id'] === id);
    if (!server) return;

    document.getElementById('editServerId').value = server['.id'];
    document.getElementById('editServerName').value = server['service-name'] || '';

    const interfaceSelect = document.getElementById('editServerInterface');
    [...interfaceSelect.options].forEach(option => {
        option.selected = option.value === server.interface;
    });

    document.getElementById('editDisabledCheck').checked = server.disabled === 'true';

    document.getElementById('editServerForm').action = `/mikrotik/pppoe/server/${encodeURIComponent(server['.id'])}`;
    $('#editServerModal').modal('show');
}


    document.addEventListener('DOMContentLoaded', loadServers);
</script>
@endpush
