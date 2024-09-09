<!DOCTYPE html>
<html>

<head>
    <title>Permission Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
            font-size: 10px;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <h1>Permission Report</h1>
    <p>From: {{ request('start_date') }} To: {{ request('end_date') }}</p>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Student ID</th>
                <th>Description</th>
                <th>File</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($permissions as $permission)
                <tr>
                    <td>{{ $permission->id }}</td>
                    <td>{{ $permission->student_id }}</td>
                    <td>{{ $permission->description }}</td>
                    <td>
                        @if ($permission->file)
                            <a href="{{ asset('storage/' . $permission->file) }}" target="_blank">View File</a>
                        @else
                            No File
                        @endif
                    </td>
                    <td>{{ $permission->created_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
