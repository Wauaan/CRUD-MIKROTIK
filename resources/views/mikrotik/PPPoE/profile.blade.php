@extends('template.utama')
@section('title', 'PPPoE Profile')
@section('content')
<div class="container mt-4">
    <h3 class="mb-3">Daftar PPPoE Profiles</h3>
    <!-- Menampilkan alert jika ada session flash -->
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
    <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#addProfileModal">Tambah Profile</button>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Local Address</th>
                <th>Remote Address</th>
                <th>Rate Limit</th>
                <th>Only One</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody id="profileBody"></tbody>
    </table>
</div>

<!-- Modal Tambah Profile -->
<div class="modal fade" id="addProfileModal" tabindex="-1" role="dialog" aria-labelledby="addProfileLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="{{ route('pppoe-profiles.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah PPPoE Profile</h5>
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
                        <label>Local Address</label>
                        <input type="text" class="form-control" name="local-address">
                    </div>
                    <div class="form-group">
                        <label>Remote Address</label>
                        <input type="text" class="form-control" name="remote-address">
                    </div>
                    <div class="form-group">
                        <label>Rate Limit</label>
                        <input type="text" class="form-control" name="rate-limit">
                    </div>
                    <div class="form-group form-check">
                        <input type="hidden" name="only-one" value="false">
                        <input type="checkbox" class="form-check-input" name="only-one" id="onlyOneCheck">
                        <label class="form-check-label" for="onlyOneCheck">Only One</label>
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

<!-- Modal Edit Profile -->
<div class="modal fade" id="editProfileModal" tabindex="-1" role="dialog" aria-labelledby="editProfileLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="POST" id="editProfileForm">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit PPPoE Profile</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="editId">
                    <div class="form-group">
                        <label>Nama Profile</label>
                        <input type="text" class="form-control" name="name" id="editName" required>
                    </div>
                    <div class="form-group">
                        <label>Local Address</label>
                        <input type="text" class="form-control" name="local-address" id="editLocal">
                    </div>
                    <div class="form-group">
                        <label>Remote Address</label>
                        <input type="text" class="form-control" name="remote-address" id="editRemote">
                    </div>
                    <div class="form-group">
                        <label>Rate Limit</label>
                        <input type="text" class="form-control" name="rate-limit" id="editRate">
                    </div>
                    <div class="form-group form-check">
                        <input type="hidden" name="only-one" value="false">
                        <input type="checkbox" class="form-check-input" name="only-one" id="editOnlyOne">
                        <label class="form-check-label" for="editOnlyOne">Only One</label>
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
    let profilesCache = [];
    
    async function loadProfiles() {
        const response = await fetch('{{ url("/api/mikrotik/pppoe/profile") }}');
        const data = await response.json();
        profilesCache = data;
    
        const tbody = document.getElementById('profileBody');
        tbody.innerHTML = '';
    
        data.forEach((profile, index) => {
            const deleteForm = `
                <form action="/mikrotik/pppoe/profile/${profile['.id']}" method="POST" onsubmit="return confirm('Yakin ingin menghapus profile ini?')">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="DELETE">
                    <button class="btn btn-sm btn-danger">Hapus</button>
                </form>
            `;
    
            tbody.innerHTML += `
                <tr>
                    <td>${profile.name || '-'}</td>
                    <td>${profile['local-address'] || '-'}</td>
                    <td>${profile['remote-address'] || '-'}</td>
                    <td>${profile['rate-limit'] || '-'}</td>
                    <td>${profile['only-one'] === 'yes' ? 'Ya' : 'Tidak'}</td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick="openEditModal('${profile['.id']}')">Edit</button>
                        ${deleteForm}
                    </td>
                </tr>
            `;
        });
    }
    
    function openEditModal(id) {
        const profile = profilesCache.find(p => p['.id'] === id);
        if (!profile) return;
    
        document.getElementById('editId').value = profile['.id'];
        document.getElementById('editName').value = profile.name || '';
        document.getElementById('editLocal').value = profile['local-address'] || '';
        document.getElementById('editRemote').value = profile['remote-address'] || '';
        document.getElementById('editRate').value = profile['rate-limit'] || '';
        document.getElementById('editOnlyOne').checked = profile['only-one'] === 'yes';
        
        document.getElementById('editProfileForm').action = `/mikrotik/pppoe/profile/${encodeURIComponent(profile['.id'])}`;
        $('#editProfileModal').modal('show');
    }
    
    document.addEventListener('DOMContentLoaded', loadProfiles);
    </script>
    
@endpush
