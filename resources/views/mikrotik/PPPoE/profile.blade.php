@extends('Template.utama')

@section('title', 'PPPoE Profile')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">PPPoE Profile</h1>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    <!-- Tombol Tambah -->
    <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#addProfileModal">
        <i class="fas fa-plus"></i> Tambah Profile
    </button>

    <!-- Tabel PPPoE Profile -->
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered" id="profileTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Local Address</th>
                        <th>Remote Address</th>
                        <th>Rate Limit</th>
                        <th>Only One</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="profileBody">
                    <!-- Diisi oleh JS -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah Profile -->
    <div class="modal fade" id="addProfileModal" tabindex="-1" role="dialog" aria-labelledby="addProfileLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form method="POST" action="{{ route('pppoe-profiles.store') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah PPPoE Profile</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Form Input -->
                        <div class="form-group">
                            <label for="name">Nama Profile</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="local-address">Local Address</label>
                            <input type="text" class="form-control" name="local-address">
                        </div>
                        <div class="form-group">
                            <label for="remote-address">Remote Address</label>
                            <input type="text" class="form-control" name="remote-address">
                        </div>
                        <div class="form-group">
                            <label for="rate-limit">Rate Limit</label>
                            <input type="text" class="form-control" name="rate-limit">
                        </div>
                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" name="only-one" id="onlyOneCheckbox" value="true">
                            <label class="form-check-label" for="onlyOneCheckbox">Only One</label>
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
    async function loadProfiles() {
        const response = await fetch('{{ url("/api/mikrotik/pppoe/profile") }}');
        const data = await response.json();

        const tbody = document.getElementById('profileBody');
        tbody.innerHTML = '';

        data.forEach(profile => {
            tbody.innerHTML += `
                <tr>
                    <td>${profile.name || '-'}</td>
                    <td>${profile['local-address'] || '-'}</td>
                    <td>${profile['remote-address'] || '-'}</td>
                    <td>${profile['rate-limit'] || '-'}</td>
                    <td>${profile['only-one'] === 'true' ? 'Ya' : 'Tidak'}</td>
                    <td>
                        <button class="btn btn-sm btn-warning">Edit</button>
                        <button class="btn btn-sm btn-danger">Hapus</button>
                    </td>
                </tr>
            `;
        });
    }

    // Panggil saat halaman selesai dimuat
    document.addEventListener('DOMContentLoaded', loadProfiles);
</script>
@endpush
