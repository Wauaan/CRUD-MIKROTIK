<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mikrotik Resources</title>
</head>
<body>
    <h1>Mikrotik Resources</h1>
    <table border="1">
        <tbody>
            <tr>
                <th>CPU Load</th>
                <td>{{ $resources['cpu-load'] ?? 'N/A' }}%</td>
            </tr>
            <tr>
                <th>Free Memory</th>
                <td>{{ number_format(($resources['free-memory'] ?? 0) / 1024 / 1024, 2) }} MB</td>
            </tr>
            <tr>
                <th>Total Memory</th>
                <td>{{ number_format(($resources['total-memory'] ?? 0) / 1024 / 1024, 2) }} MB</td>
            </tr>
            <tr>
                <th>Uptime</th>
                <td>{{ $resources['uptime'] ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Version</th>
                <td>{{ $resources['version'] ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Architecture Name</th>
                <td>{{ $resources['architecture-name'] ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>CPU Frequency</th>
                <td>{{ $resources['cpu-frequency'] ?? 'N/A' }} MHz</td>
            </tr>
        </tbody>
    </table>

    <script>
setTimeout(function(){window.location.reload(1);}, 1000000);
    </script>
</body>
</html>
