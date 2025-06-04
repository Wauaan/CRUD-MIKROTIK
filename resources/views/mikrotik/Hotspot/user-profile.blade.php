@extends('template.utama')
@section('title', 'Hotspot User Profiles')

@section('content')
<div class="container mt-4">
    <h3 class="mb-3">Daftar Hotspot User Profiles</h3>

    <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#addUserProfileModal">
        Tambah User Profile
    </button>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Address Pool</th>
                <th>Shared Users</th>
                <th>Idle Timeout</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody id="userProfileBody"></tbody>
    </table>
</div>

<!-- Modal Tambah User Profile -->
<div class="modal fade" id="addUserProfileModal" tabindex="-1" role="dialog" aria-labelledby="addUserProfileLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="formAddUserProfile">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah User Profile</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Profile</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Address Pool</label>
                        <input type="text" class="form-control" name="address-pool">
                    </div>
                    <div class="form-group">
                        <label>Shared Users</label>
                        <input type="number" class="form-control" name="shared-users">
                    </div>
                    <div class="form-group">
                        <label>Idle Timeout</label>
                        <input type="text" class="form-control" name="idle-timeout" placeholder="misal: 5m" />
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
    async function loadUserProfiles() {
        const response = await fetch('{{ url("/api/mikrotik/hotspot/user-profile") }}');
        const data = await response.json();
        const tbody = document.getElementById('userProfileBody');
        tbody.innerHTML = '';

        data.forEach(profile => {
            tbody.innerHTML += `
                <tr>
                    <td>${profile.name || '-'}</td>
                    <td>${profile['address-pool'] || '-'}</td>
                    <td>${profile['shared-users'] || '-'}</td>
                    <td>${profile['idle-timeout'] || '-'}</td>
                    <td>
                        <button class="btn btn-sm btn-warning" disabled>Edit</button>
                        <button class="btn btn-sm btn-danger" disabled>Hapus</button>
                    </td>
                </tr>
            `;
        });
    }

    document.getElementById('formAddUserProfile').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        const response = await fetch('{{ url("/mikrotik/hotspot/user-profile") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: formData
        });

        if (response.ok) {
            $('#addUserProfileModal').modal('hide');
            this.reset();
            loadUserProfiles();
        } else {
            alert('Gagal menambahkan user profile.');
        }
    });

    document.addEventListener('DOMContentLoaded', loadUserProfiles);
</script>
@endpush
