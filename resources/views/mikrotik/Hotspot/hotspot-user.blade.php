@extends('template.utama')
@section('title', 'Hotspot Users')

@section('content')
<div class="container mt-4">
    <h3 class="mb-3">Daftar Hotspot Users</h3>

    <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#addHotspotUserModal">
        Tambah Hotspot User
    </button>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Username</th>
                <th>Profile</th>
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
<div class="modal fade" id="addHotspotUserModal" tabindex="-1" role="dialog" aria-labelledby="addHotspotUserLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="formAddHotspotUser">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Hotspot User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                        <span aria-hidden="true">&times;</span>
                    </button>
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
                        <input type="text" class="form-control" name="profile">
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
@endsection

@push('scripts')
<script>
    async function loadHotspotUsers() {
        const response = await fetch('{{ url("/api/mikrotik/hotspot/hotspot-user") }}');
        const data = await response.json();
        const tbody = document.getElementById('hotspotUserBody');
        tbody.innerHTML = '';

        data.forEach(user => {
            tbody.innerHTML += `
                <tr>
                    <td>${user.name || '-'}</td>
                    <td>${user.profile || '-'}</td>
                    <td>${user.uptime || '-'}</td>
                    <td>${user.comment || '-'}</td>
                    <td>${user.disabled === 'true' ? 'Ya' : 'Tidak'}</td>
                    <td>
                        <button class="btn btn-sm btn-warning" disabled>Edit</button>
                        <button class="btn btn-sm btn-danger" disabled>Hapus</button>
                    </td>
                </tr>
            `;
        });
    }

    document.getElementById('formAddHotspotUser').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        const response = await fetch('{{ url("/mikrotik/hotspot/hotspot-user") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: formData
        });

        if (response.ok) {
            $('#addHotspotUserModal').modal('hide');
            this.reset();
            loadHotspotUsers();
        } else {
            alert('Gagal menambahkan user Hotspot.');
        }
    });

    document.addEventListener('DOMContentLoaded', loadHotspotUsers);
</script>
@endpush
