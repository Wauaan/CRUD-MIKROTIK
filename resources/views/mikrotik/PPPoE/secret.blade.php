@extends('template.utama')
@section('title', 'PPPoE Secret')
@section('content')
<div class="container mt-4">
    <h3 class="mb-3">Daftar PPPoE Secrets</h3>

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

    <!-- Tombol Tambah -->
    <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#addSecretModal">Tambah Secret</button>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Password</th>
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

<!-- Modal Tambah Secret -->
<div class="modal fade" id="addSecretModal" tabindex="-1" role="dialog" aria-labelledby="addSecretLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="{{ route('pppoe-secrets.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah PPPoE Secret</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Profile</label>
                        <select name="profile" id="addProfileSelect" class="form-control" required>
                            <option value="" disabled selected>Loading profiles...</option>
                        </select>
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

<!-- Modal Edit Secret -->
<div class="modal fade" id="editSecretModal" tabindex="-1" role="dialog" aria-labelledby="editSecretLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="POST" id="editSecretForm" action="">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit PPPoE Secret</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="editId">
                    <div class="form-group">
                        <label>Nama</label>
                        <input type="text" name="name" class="form-control" id="editName" required>
                    </div>
                    <div class="form-group">
                        <label>Password (biarkan kosong jika tidak diubah)</label>
                        <input type="password" name="password" class="form-control" id="editPassword">
                    </div>
                    <div class="form-group">
                        <label>Profile</label>
                        <select name="profile" id="editProfileSelect" class="form-control" required>
                            <option value="" disabled selected>Loading profiles...</option>
                        </select>
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
let secretsCache = [];
let profilesCache = [];

async function fetchProfiles() {
    try {
        const response = await fetch('{{ url("/api/mikrotik/pppoe/profile") }}');
        if (!response.ok) throw new Error('Failed to fetch profiles');
        const data = await response.json();
        profilesCache = data;
        populateProfileSelects();
    } catch (error) {
        console.error('Error fetching profiles:', error);
        // Fallback: clear selects or display error option
        const selects = [document.getElementById('addProfileSelect'), document.getElementById('editProfileSelect')];
        selects.forEach(select => {
            select.innerHTML = '<option value="" disabled selected>Error loading profiles</option>';
        });
    }
}

function populateProfileSelects() {
    // helper to generate option elements
    const optionsHtml = profilesCache.length === 0 ? 
        '<option value="" disabled selected>Tidak ada profile</option>' :
        profilesCache.map(profile => `<option value="${profile.name}">${profile.name}</option>`).join('');
    const addSelect = document.getElementById('addProfileSelect');
    const editSelect = document.getElementById('editProfileSelect');

    addSelect.innerHTML = optionsHtml;
    editSelect.innerHTML = optionsHtml;
}

async function loadSecrets() {
    const response = await fetch('{{ url("/api/mikrotik/pppoe/secret") }}');
    const data = await response.json();
    secretsCache = data;

    const tbody = document.getElementById('secretBody');
    tbody.innerHTML = '';

    data.forEach(secret => {
        const deleteForm = `
            <form action="/mikrotik/pppoe/secret/${secret['.id']}" method="POST" onsubmit="return confirm('Yakin ingin menghapus secret ini?')">
                @csrf
                @method('DELETE')
                <button class="btn btn-sm btn-danger">Hapus</button>
            </form>
        `;
        tbody.innerHTML += `
            <tr>
                <td>${secret.name || '-'}</td>
                <td>${secret.password || '-'}</td>
                <td>${secret.service || '-'}</td>
                <td>${secret.profile || '-'}</td>
                <td>${secret['local-address'] || '-'}</td>
                <td>${secret['remote-address'] || '-'}</td>
                <td>${secret.disabled === 'true' ? 'Ya' : 'Tidak'}</td>
                <td>
                    <button class="btn btn-sm btn-warning" onclick="openEditModal('${secret['.id']}', '${secret.name}', '${secret.profile}')">Edit</button>
                    ${deleteForm}
                </td>
            </tr>
        `;
    });
}

function openEditModal(id, name, profile) {
    // Set action form
    document.getElementById('editSecretForm').action = `/mikrotik/pppoe/secret/${encodeURIComponent(id)}`;
    document.getElementById('editId').value = id;

    // Set input values
    document.getElementById('editName').value = name;
    document.getElementById('editPassword').value = ''; // Do not fill password for security
    document.getElementById('editProfileSelect').value = profile || '';

    // Show modal
    $('#editSecretModal').modal('show');
}

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('secretBody').innerHTML = `
        <tr><td colspan="7" class="text-center">Data belum dimuat.</td></tr>
    `;
    fetchProfiles().then(() => {
        loadSecrets();
    });
});
</script>
@endpush

