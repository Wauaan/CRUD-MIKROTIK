@extends('Template.utama')

@section('title', 'User PPPoE Aktif')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">User PPPoE yang Aktif</h1>

    @if(is_array($activeUsers) && count($activeUsers) > 0)
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>IP Address</th>
                        <th>MAC Address</th>
                        <th>Uptime</th>
                        <th>Interface</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($activeUsers as $index => $user)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $user['name'] ?? '-' }}</td>
                            <td>{{ $user['address'] ?? '-' }}</td>
                            <td>{{ $user['caller-id'] ?? '-' }}</td>
                            <td>{{ $user['uptime'] ?? '-' }}</td>
                            <td>{{ $user['interface'] ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="alert alert-info">
            Tidak ada user PPPoE yang aktif saat ini atau gagal koneksi ke MikroTik.
        </div>
    @endif
</div>
@endsection
