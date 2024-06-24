<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            padding: 20px;
            margin: 20px auto;
            max-width: 1200px;
        }
        h2, h3, p {
            margin: 10px 0;
            text-align: center;
        }
        .table-container {
            margin-bottom: 60px; /* Increased margin to ensure tables are not too close */
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 20px; /* Added margin to ensure tables are not too close */
        }
        th, td {
            border: 1px solid black; /* Only table data has borders */
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        .table-responsive {
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Kelas: {{ $typeClass->category }}</h2>
        <p>Tanggal Rekap: {{ $startDate }} - {{ $endDate }}</p>

        @foreach ($classrooms as $classroom)
            <div class="table-container">
                <h3>{{ $classroom->name }}</h3>
                <p>Wali Kelas: {{ $classroom->teacher->name ?? 'N/A' }}</p>

                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th rowspan="2">NAMA SISWA</th>
                                <th rowspan="2">L/P</th>
                                <th colspan="{{ $dateRange->count() }}">TANGGAL*</th>
                                <th colspan="3">JUM. KTDHDRN**</th>
                                <th rowspan="2">POIN TATIB</th>
                                <th rowspan="2">KET</th>
                            </tr>
                            <tr>
                                @foreach ($dateRange as $date)
                                    <th>{{ $date->format('d') }}</th>
                                @endforeach
                                <th>S</th>
                                <th>I</th>
                                <th>A</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($classroom->students as $index => $student)
                                <tr>
                                    <td>{{ $student->name }}</td>
                                    <td>{{ $student->gender }}</td>
                                    @foreach ($dateRange as $date)
                                        <td>{{ $attendanceSummary[$student->id]['summary'][$date->format('Y-m-d')] ?? '' }}</td>
                                    @endforeach
                                    <td>{{ number_format($attendanceSummary[$student->id]['total_sakit'] * 0.1, 1) }}</td>
                                    <td>{{ number_format($attendanceSummary[$student->id]['total_izin'] * 0.1, 1) }}</td>
                                    <td>{{ number_format($attendanceSummary[$student->id]['total_alpha'] * 0.1, 1) }}</td>
                                    <td>{{ number_format($attendanceSummary[$student->id]['total_points'], 1) }}</td>
                                    <td>{{ $attendanceSummary[$student->id]['warning'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    </div>
</body>
</html>
