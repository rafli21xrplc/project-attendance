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
    <h2>LAPORAN GURU YANG TIDAK MELAKUKAN ABSENSI SISWA di APLIKASI ASIK 8</h2>
    <p>Tanggal Rekap: {{ date('d M Y', strtotime($start_date)) }} - {{ date('d M Y', strtotime($end_date)) }}</p>

    <table>
        <thead>
            <tr>
                <th>NO</th>
                <th>HARI</th>
                <th>WAKTU DETEKSI SYSTEM</th>
                <th>GURU</th>
                <th>KELAS</th>
                <th>PELAJARAN</th>
                <th>MULAI PELAJARAN</th>
                <th>AKHIR PELAJARAN</th>

            </tr>
        </thead>
        <tbody>
            @foreach ($teacher_late as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ [
                        'Monday' => 'Senin',
                        'Tuesday' => 'Selasa',
                        'Wednesday' => 'Rabu',
                        'Thursday' => 'Kamis',
                        'Friday' => 'Jumat',
                        'Saturday' => 'Sabtu',
                        'Sunday' => 'Minggu',
                    ][$item->day] ?? $item->day }}
                    </td>
                    <td>{{ \Carbon\Carbon::parse($item->created_at)->locale('id')->formatLocalized('%d %B %Y %H:%M') }}
                    </td>
                    <td>{{ $item->teacher_name }}</td>
                    <td>{{ $item->type_class_category }} {{ $item->class_name }}</td>
                    <td>{{ $item->course_name }}</td>
                    <td>{{ $item->start_time }}</td>
                    <td>{{ $item->end_time }}</td>


                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
