@extends('Template.utama')

@section('title', 'Blokir Website')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4">NextDNS Denylist</h1>

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