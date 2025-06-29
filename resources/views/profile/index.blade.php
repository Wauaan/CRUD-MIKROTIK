@extends('template.utama')
@section('title', 'Manajemen User')

@section('content')
<div class="container mt-4">
    <h3 class="mb-3">Daftar User</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>Nama Atau Email Sudah Digunakan</li>
            @endforeach
        </ul>
    </div>
@endif
@if ($errors->any())
    <script>
        $(document).ready(function () {
            $('#editUserModal').modal('show');
        });
    </script>
@endif
    <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#addUserModal">
        Tambah User
    </button>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Email</th>
                <th>Password</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->password }}</td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick="showEditModal({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}')">Edit</button>
                        <button class="btn btn-sm btn-danger" onclick="submitDeleteUser('{{ $user->id }}')">Hapus</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">Belum ada user.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Modal Tambah User -->
<div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="POST" action="{{ route('users.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah User</h5>
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
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required minlength="5">
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

<!-- Modal Edit User -->
<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form method="POST" id="editUserForm">
        @csrf
        @method('PUT')
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title">Edit User</h5>
            <button type="button" class="close" data-dismiss="modal">
                <span>&times;</span>
            </button>
            </div>
            <div class="modal-body">
            <input type="hidden" id="editUserId">
            <div class="form-group">
                <label>Nama</label>
                <input type="text" name="name" class="form-control" id="editUserName" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" id="editUserEmail" required>
            </div>
            <div class="form-group">
                <label>Password (Kosongkan jika tidak diubah)</label>
                <input type="password" name="password" class="form-control" id="editUserPassword">
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
function showEditModal(id, name, email) {
    $('#editUserId').val(id);
    $('#editUserName').val(name);
    $('#editUserEmail').val(email);
    $('#editUserPassword').val('');

    const form = document.getElementById('editUserForm');
    form.action = `/users/${id}`;

    $('#editUserModal').modal('show');
}

function submitDeleteUser(userId) {
    if (!confirm("Yakin ingin menghapus user ini?")) return;

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/users/${userId}`;
    form.innerHTML = `
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="_method" value="DELETE">
    `;
    document.body.appendChild(form);
    form.submit();
}

</script>
@endpush
