<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mikrotik Interfaces</title>
</head>
<body>
    <h1>Mikrotik Interfaces</h1>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Jenis</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($interfaces as $interface)
                <tr>
                    <td>{{ $interface['.id'] }}</td>
                    <td>{{ $interface['name'] }}</td>
                    <td>{{ $interface['type'] }}</td>
                    <td>{{ $interface['running'] ? 'Running' : 'Stopped' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>