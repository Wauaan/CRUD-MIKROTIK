@extends('Template.utama')

@section('title', 'Blokir Website')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4">Denylist</h1>
<!-- Tombol Tambah Website -->
<button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#tambahWebsiteModal">
    Tambah Website
</button>

<!-- Modal Tambah Website -->
<div class="modal fade" id="tambahWebsiteModal" tabindex="-1" role="dialog" aria-labelledby="tambahWebsiteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="{{ route('denylist.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahWebsiteModalLabel">Tambah Website</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="website">Website</label>
                        <input type="text" class="form-control" id="website" name="website" placeholder="contoh.com" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Tambah</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                </div>
            </div>
        </form>
    </div>
</div>

    @if(isset($denylist['data']) && is_array($denylist['data']) && count($denylist['data']) > 0)
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Website</th>
                    <th>Active</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
                @foreach($denylist['data'] as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item['id'] }}</td>
                        <td>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox"
                                role="switch"
                                onchange="toggleActive('{{ $item['id'] }}', this.checked)"
                                {{ $item['active'] ? 'checked' : '' }}>
                        </div>
                    </td>
                        <td>
                            <form action="{{ route('denylist.delete', ['id' => $item['id']]) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus {{ $item['id'] }}?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="alert alert-info">Tidak ada data denylist atau gagal mengambil data.</div>
    @endif
</div>
@endsection
@push('scripts')
<script>
function toggleActive(id, isActive) {
    fetch('/nextdns/denylist/toggle', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        body: JSON.stringify({ id: id, active: isActive })
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            alert('Gagal mengubah status: ' + (data.message || 'unknown error'));
        }
    });
}
</script>