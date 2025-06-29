@extends('template.utama')
@section('title', 'Hotspot User Profiles')

@section('content')
<div class="container mt-4">
    <h3 class="mb-3">Daftar Hotspot User Profiles</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

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

<!-- Modal Tambah -->
<div class="modal fade" id="addUserProfileModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form action="{{ route('hotspot.user-profile.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah User Profile</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Profile</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Address Pool</label>
                        <select class="form-control" name="address-pool" required>
                            <option value="">-- Pilih Address Pool --</option>
                        </select>
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

<!-- Modal Edit -->
<div class="modal fade" id="editUserProfileModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <form method="POST" id="editUserProfileForm">
      @csrf
      @method('PUT')
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit User Profile</h5>
          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="editProfileId" name="id">
          <div class="form-group">
            <label>Nama Profile</label>
            <input type="text" name="name" class="form-control" id="editProfileName" required>
          </div>
          <div class="form-group">
            <label>Address Pool</label>
            <select class="form-control" name="address-pool" id="editAddressPool" required>
              <option value="">-- Pilih Address Pool --</option>
            </select>
          </div>
          <div class="form-group">
            <label>Shared Users</label>
            <input type="number" name="shared-users" class="form-control" id="editSharedUsers">
          </div>
          <div class="form-group">
            <label>Idle Timeout</label>
            <input type="text" name="idle-timeout" class="form-control" id="editIdleTimeout">
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
let userProfiles = [];
let pools = [];

async function loadUserProfiles() {
    const response = await fetch('{{ url("/api/mikrotik/hotspot/user-profile") }}');
    userProfiles = await response.json();
    const tbody = document.getElementById('userProfileBody');
    tbody.innerHTML = '';

    userProfiles.forEach(profile => {
        tbody.innerHTML += `
            <tr>
                <td>${profile.name || '-'}</td>
                <td>${profile['address-pool'] || '-'}</td>
                <td>${profile['shared-users'] || '-'}</td>
                <td>${profile['idle-timeout'] || '-'}</td>
                <td>
                    <button class="btn btn-sm btn-warning" onclick="showEditModal('${profile['.id']}')">Edit</button>
                    <button class="btn btn-sm btn-danger" onclick="submitDeleteUserProfile('${profile['.id']}')">Hapus</button>
                </td>
            </tr>
        `;
    });
}

async function loadAddressPools() {
    const response = await fetch('{{ url("/api/mikrotik/address-pool") }}');
    pools = await response.json();

    // Untuk modal tambah
    const addSelect = document.querySelector('select[name="address-pool"]');
    if (addSelect) {
        addSelect.innerHTML = `<option value="" disabled selected>-- Pilih Address Pool --</option>`;
        pools
            .filter(pool => pool.name.startsWith('pool-HS'))
            .forEach(pool => {
                const option = document.createElement('option');
                option.value = pool.name;
                option.textContent = `${pool.name} (${pool.ranges})`;
                addSelect.appendChild(option);
            });
    }
}

function showEditModal(id) {
    const profile = userProfiles.find(p => p['.id'] === id);
    const form = document.getElementById('editUserProfileForm');
    form.action = `/mikrotik/hotspot/user-profile/${encodeURIComponent(id)}`;

    document.getElementById('editProfileId').value = id;
    document.getElementById('editProfileName').value = profile.name || '';
    document.getElementById('editSharedUsers').value = profile['shared-users'] || '';
    document.getElementById('editIdleTimeout').value = profile['idle-timeout'] || '';

    // Address Pool Select
    const select = document.getElementById('editAddressPool');
    select.innerHTML = `<option value="" disabled>-- Pilih Address Pool --</option>`;
    pools
        .filter(p => p.name.startsWith('pool-HS'))
        .forEach(pool => {
            const option = document.createElement('option');
            option.value = pool.name;
            option.textContent = `${pool.name} (${pool.ranges})`;
            if (pool.name === profile['address-pool']) {
                option.selected = true;
            }
            select.appendChild(option);
        });

    $('#editUserProfileModal').modal('show');
}
function submitDeleteUserProfile(profileId) {
    if (!confirm('Yakin ingin menghapus User Profile ini?')) {
        return;
    }

    const form = document.createElement('form');
    form.action = `/mikrotik/hotspot/user-profile/${profileId}`;
    form.method = 'POST';

    form.innerHTML = `
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="_method" value="DELETE">
    `;

    document.body.appendChild(form);
    form.submit();
}

document.addEventListener('DOMContentLoaded', () => {
    loadUserProfiles();
    loadAddressPools();
});
</script>
@endpush
