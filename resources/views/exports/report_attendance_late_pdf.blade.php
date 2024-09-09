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
    <h2>SUPERVISI KS PRESENSI SISWA PIESA</h2>
    <p>Tanggal Rekap: {{ date('d M Y', strtotime($day)) }}</p>

    <table>
        <thead>
            <tr>
                <th>NO</th>
                <th>KELAS</th>
                <th>MATA PELAJARAN</th>
                <th>JAM MULAI</th>
                <th>JAM SELESAI</th>
                <th>GURU</th>
            </tr>
        </thead>
        <tbody>

            @forelse  ($teacher_late as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->schedule->classroom->name }}</td>
                    <td>{{ $item->schedule->course->name }}</td>
                    <td>{{ $item->schedule->StartTimeSchedules->start_time_schedule }}</td>
                    <td>{{ $item->schedule->EndTimeSchedules->end_time_schedule }}</td>
                    <td>{{ $item->schedule->teacher->name }}</td>
                </tr>
            @empty
                DATA KOSONG
            @endforelse
        </tbody>
    </table>
</body>

</html>
