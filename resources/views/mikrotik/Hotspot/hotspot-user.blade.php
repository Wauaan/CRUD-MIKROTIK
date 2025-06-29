@extends('template.utama')
@section('title', 'Hotspot Users')

@section('content')
<div class="container mt-4">
    <h3 class="mb-3">Daftar Hotspot Users</h3>
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif
    <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#addHotspotUserModal">
        Tambah Hotspot User
    </button>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Username</th>
                <th>Profile</th>
                <th>Password</th>
                <th>Uptime</th>
                <th>Comment</th>
                <th>Disabled</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody id="hotspotUserBody"></tbody>
    </table>
</div>

<!-- Modal Tambah Hotspot User -->
<div class="modal fade" id="addHotspotUserModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form id="formAddHotspotUser" action="{{ route('hotspot.user.store') }}" method="POST">
        @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Hotspot User</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="text" class="form-control" name="password" required>
                    </div>
                    <div class="form-group">
                        <label>Profile</label>
                        <select class="form-control" name="profile" id="selectProfile" required>
                            <option value="">-- Pilih Profile --</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Comment</label>
                        <input type="text" class="form-control" name="comment">
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

<!-- Modal Edit Hotspot User -->
<div class="modal fade" id="editHotspotUserModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form method="POST" id="formEditHotspotUser">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Hotspot User</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="editUserId">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="name" class="form-control" id="editUserName" required>
                    </div>
                    <div class="form-group">
                        <label>Password (kosongkan jika tidak diubah)</label>
                        <input type="text" name="password" class="form-control" id="editUserPassword">
                    </div>
                    <div class="form-group">
                        <label>Profile</label>
                        <select class="form-control" name="profile" id="editUserProfileSelect" required>
                            <option value="">-- Pilih Profile --</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Comment</label>
                        <input type="text" name="comment" class="form-control" id="editUserComment">
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
let hotspotUsers = []
let hotspotUserProfiles = []

async function loadHotspotUsers() {
    const response = await fetch('{{ url("/api/mikrotik/hotspot/hotspot-user") }}');
    const data = await response.json();
    hotspotUsers = data;
    const tbody = document.getElementById('hotspotUserBody');
    tbody.innerHTML = '';

    data.forEach(user => {
        tbody.innerHTML += `
            <tr>
                <td>${user.name || '-'}</td>
                <td>${user.profile || '-'}</td>
                <td>${user.password || '-'}</td>
                <td>${user.uptime || '-'}</td>
                <td>${user.comment || '-'}</td>
                <td>${user.disabled === 'true' ? 'Ya' : 'Tidak'}</td>
                <td>
                    <button class="btn btn-sm btn-warning" onclick="showEditHotspotUser('${user['.id']}')">Edit</button>
                    <button class="btn btn-sm btn-danger" onclick="submitDeleteHotspotUser('${user['.id']}')">Hapus</button>
                </td>
            </tr>
        `;
    });
}

async function loadHotspotUserProfiles() {
    const response = await fetch('{{ url("/api/mikrotik/hotspot/user-profile") }}');
    hotspotUserProfiles = await response.json();

    const select = document.getElementById('selectProfile');
    if (select) {
        select.innerHTML = '<option value="">-- Pilih Profile --</option>';
        hotspotUserProfiles.forEach(profile => {
            const option = document.createElement('option');
            option.value = profile.name;
            option.textContent = profile.name;
            select.appendChild(option);
        });
    }
}
function submitDeleteHotspotUser(userId) {
    if (!confirm('Yakin ingin menghapus Hotspot User ini?')) {
        return;
    }

    const form = document.createElement('form');
    form.action = `/mikrotik/hotspot/hotspot-user/${userId}`;
    form.method = 'POST';

    form.innerHTML = `
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="_method" value="DELETE">
    `;

    document.body.appendChild(form);
    form.submit();
}

function showEditHotspotUser(id) {
    const user = hotspotUsers.find(u => u['.id'] === id);
    const form = document.getElementById('formEditHotspotUser');
    form.action = `/mikrotik/hotspot/hotspot-user/${encodeURIComponent(id)}`;

    document.getElementById('editUserId').value = id;
    document.getElementById('editUserName').value = user.name || '';
    document.getElementById('editUserPassword').value = '';
    document.getElementById('editUserComment').value = user.comment || '';

    const select = document.getElementById('editUserProfileSelect');
    select.innerHTML = '<option value="">-- Pilih Profile --</option>';
    hotspotUserProfiles.forEach(profile => {
        const option = document.createElement('option');
        option.value = profile.name;
        option.textContent = profile.name;
        if (profile.name === user.profile) {
            option.selected = true;
        }
        select.appendChild(option);
    });

    $('#editHotspotUserModal').modal('show');
}

document.addEventListener('DOMContentLoaded', () => {
    loadHotspotUsers();
    loadHotspotUserProfiles();
});
</script>
@endpush
