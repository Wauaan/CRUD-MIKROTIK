@extends('template.utama')
@section('title', 'Hotspot Server Profiles')

@section('content')
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="container mt-4">
    <h3 class="mb-3">Daftar Hotspot Server Profiles</h3>

    <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#addServerProfileModal">
        Tambah Server Profile
    </button>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Hotspot Address</th>
                <th>DNS Name</th>
                <th>Rate-Limit</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody id="serverProfileBody"></tbody>
    </table>
</div>

<!-- Modal Tambah Server Profile -->
<div class="modal fade" id="addServerProfileModal" tabindex="-1" role="dialog" aria-labelledby="addServerProfileLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="{{ route('hotspot.server-profile.store') }}" method="POST" id="formAddServerProfile">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Server Profile</h5>
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
                        <label>Hotspot Address</label>
                        <input type="text" class="form-control" name="hotspot_address">
                    </div>
                    <div class="form-group">
                        <label>DNS Name</label>
                        <input type="text" class="form-control" name="dns_name">
                    </div>
                    <div class="form-group">
                        <label>Rate Limit</label>
                        <input type="text" class="form-control" name="rate_limit" placeholder="contoh: 2M/2M">
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

<!-- Modal Edit Server Profile -->
<div class="modal fade" id="editServerProfileModal" tabindex="-1" role="dialog" aria-labelledby="editServerProfileLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="POST" id="editServerProfileForm" action="">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Server Profile</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="editProfileId">

                    <div class="form-group">
                        <label>Nama Profile</label>
                        <input type="text" name="name" class="form-control" id="editProfileName" required>
                    </div>

                    <div class="form-group">
                        <label>Hotspot Address</label>
                        <input type="text" name="hotspot_address" class="form-control" id="editHotspotAddress">
                    </div>

                    <div class="form-group">
                        <label>DNS Name</label>
                        <input type="text" name="dns_name" class="form-control" id="editDnsName">
                    </div>

                    <div class="form-group">
                        <label>Rate Limit</label>
                        <input type="text" name="rate_limit" class="form-control" id="editRateLimit" placeholder="contoh: 2M/2M">
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
    async function loadServerProfiles() {
        const response = await fetch('{{ url("/api/mikrotik/hotspot/server-profile") }}');
        const data = await response.json();
        serverProfiles = data;  // simpan data

        const tbody = document.getElementById('serverProfileBody');
        tbody.innerHTML = '';

        data.forEach(profile => {
            tbody.innerHTML += `
                <tr>
                    <td>${profile.name || '-'}</td>
                    <td>${profile['hotspot-address'] || '-'}</td>
                    <td>${profile['dns-name'] || '-'}</td>
                    <td>${profile['rate-limit'] || '-'}</td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick="showEditModal('${profile['.id']}')">Edit</button>
                        <button class="btn btn-sm btn-danger" onclick="submitDeleteServerProfile('${profile['.id']}')">Hapus</button>
                    </td>
                </tr>
            `;
        });
    }

    function submitDeleteServerProfile(profileId) {
        if (!confirm('Yakin ingin menghapus Server Profile ini?')) {
            return;
        }

        const form = document.createElement('form');
        form.action = `/mikrotik/hotspot/server-profile/${profileId}`;
        form.method = 'POST';

        form.innerHTML = `
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="_method" value="DELETE">
        `;

        document.body.appendChild(form);
        form.submit();
    }

function showEditModal(id) {
    const profile = serverProfiles.find(p => p['.id'] === id);
    const form = document.getElementById('editServerProfileForm');
    form.action = `/mikrotik/hotspot/server-profile/${encodeURIComponent(id)}`;

    document.getElementById('editProfileId').value = id;
    document.getElementById('editProfileName').value = profile.name || '';
    document.getElementById('editHotspotAddress').value = profile['hotspot-address'] || '';
    document.getElementById('editDnsName').value = profile['dns-name'] || '';
    document.getElementById('editRateLimit').value = profile['rate-limit'] || '';

    $('#editServerProfileModal').modal('show');
}


    document.addEventListener('DOMContentLoaded', loadServerProfiles);
</script>
@endpush

