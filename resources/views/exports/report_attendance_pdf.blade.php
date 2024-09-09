<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <h2>Class Attendance Report</h2>
    <p>Kelas: {{ $classroom->typeClass->category }} {{ $classroom->name }}</p>
    <p>Wali Kelas: {{ $classroom->teacher->name ?? 'N/A' }}</p>
    <p>Tanggal Rekap: {{ date('d M Y', strtotime($startDate)) }} - {{ date('d M Y', strtotime($endDate)) }}</p>

    <table>
        <thead>
            <tr>
                <th>NO</th>
                <th>NISN</th>
                <th>NAMA SISWA</th>
                <th>L/P</th>
                @foreach ($dateRange as $date)
                    <th>{{ $date->format('d') }}</th>
                @endforeach
                <th>S</th>
                <th>I</th>
                <th>A</th>
                <th>POIN TATIB</th>
                <th>KET</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($classroom->students as $index => $student)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $student->student_id }}</td>
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
</body>
</html>
