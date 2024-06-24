<!DOCTYPE html>
<html>
<head>
    <title>Class Attendance Report</title>
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
        th, td {
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
    @foreach ($attendanceExport->sheets() as $sheet)
        <h2>{{ $sheet->title() }}</h2>
        @foreach ($sheet->collection() as $row)
            @if ($loop->first || $row[0] === '')
                @if (!$loop->first)
                    </table>
                @endif
                <table>
                <tr>
                    @foreach ($row as $cell)
                        <th>{{ $cell }}</th>
                    @endforeach
                </tr>
            @else
                <tr>
                    @foreach ($row as $cell)
                        <td>{{ $cell }}</td>
                    @endforeach
                </tr>
            @endif
        @endforeach
        </table>
        <div style="page-break-after: always;"></div>
    @endforeach
</body>
</html>
