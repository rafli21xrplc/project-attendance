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
        .notes-and-signatures {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-top: 20px;
            gap: 10px;
        }
        .notes {
            width: 30%;
        }
        .calculation {
            width: 30%;
        }
        .signatures {
            width: 40%;
            display: flex;
            justify-content: space-between;
        }
        .signatures div {
            text-align: left;
            font-size: 10px;
        }
        .notes p, .calculation p {
            margin: 0;
            padding: 0;
        }
        .notes table, .calculation table {
            width: 100%;
            border: none;
            margin-top: 5px;
        }
        .notes th, .notes td, .calculation th, .calculation td {
            border: none;
            padding: 0;
        }
        .calculation td:nth-child(2) {
            padding-left: 10px;
        }
    </style>
</head>
<body>
    @foreach ($attendanceExport->sheets() as $sheet)
        <h2>{{ $sheet->title() }}</h2>
        <table>
        @foreach ($sheet->collection() as $row)
            @if ($loop->first || $row[0] === '')
                @if (!$loop->first)
                    </table>
                    <table>
                @endif
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
    
    <div class="notes-and-signatures">
        <div class="notes">
            <table>
                <tr>
                    {{-- keterangan penulisan sakit --}}
                    <td></td>
                </tr>
                <tr>
                    {{-- keterangan jumlah ketidakhadiran --}}
                    <td></td>
                </tr>
                <tr>
                    {{-- tanda tangan waka kesiswaan --}}
                    <td></td>
                </tr>
                <tr>
                    {{-- tanda tangan petugas --}}
                    <td></td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
