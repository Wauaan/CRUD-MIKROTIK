@extends('Template.utama')

@section('title', 'PPPoE Server')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">Daftar PPPoE Server</h1>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered" id="pppoe-server-table">
                <thead>
                    <tr>
                        <th>Interface</th>
                        <th>Service Name</th>
                        <th>Profile</th>
                        <th>Disabled</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($servers as $server)
                        <tr>
                            <td>{{ $server['interface'] ?? '-' }}</td>
                            <td>{{ $server['service-name'] ?? '-' }}</td>
                            <td>{{ $server['default-profile'] ?? '-' }}</td>
                            <td>{{ $server['disabled'] === 'true' ? 'Yes' : 'No' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
