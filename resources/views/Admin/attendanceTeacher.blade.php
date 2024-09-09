@extends('admin.layouts.app')


@section('link')
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/app-logistics-dashboard.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/%40form-validation/umd/styles/index.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/animate-css/animate.css') }}">
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.4/css/jquery.dataTables.css">
    
     <style>
        #table-content {
            border-collapse: collapse;
            width: 100%;
        }

        #table-content th,
        #table-content td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: center;
        }

        #table-content th {
            background-color: #ffffff;
        }

        #table-content tbody tr:nth-child(odd) {
            background-color: #ffffff;
        }

        #table-content tbody tr:hover {
            background-color: #e9e9e9;
        }

        #info-table {
            width: 100%;
            margin-bottom: 20px;
        }

        #info-table th,
        #info-table td {
            padding: 8px;
            text-align: left;
            border-bottom: 0px solid #ddd;
        }

        #info-table th {
            background-color: #f2f2f2;
        }

        #info-table td:first-child {
            font-weight: bold;
        }

        #info-table td:last-child {
            font-style: italic;
        }
    </style>

@endsection

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row my-4">
                <div class="col-12">
                    <form action="{{ route('admin.report.attendance_teacher_late.search') }}" method="get">
                        <div class="row g-3 align-items-center">
                            <div class="col-12 col-md-3">
                                <div class="input-group">
                                    <input type="date" class="form-control" placeholder="Start Date" name="start_date" />
                                    <span class="input-group-text">-</span>
                                    <input type="date" class="form-control" placeholder="End Date" name="end_date" />
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                                <button type="submit" class="btn btn-success w-100">
                                    <span class="d-none d-sm-inline-block">Search</span>
                                </button>
                            </div>
                        </div>
                    </form>

                    <form action="{{ route('admin.export.attendance_report_teacher.date') }}" method="POST" class="mt-4">
                        @csrf
                        <div class="row g-3 align-items-center">
                            <!-- Month Picker -->
                            <div class="col-12 col-md-4">
                                <div class="input-group">
                                    <input type="date" class="form-control" placeholder="Start Date" name="start_date" />
                                    <span class="input-group-text">-</span>
                                    <input type="date" class="form-control" placeholder="End Date" name="end_date" />
                                </div>
                            </div>

                            <!-- Format Selector -->
                            <div class="col-12 col-md-4">
                                <label for="format" class="form-label">Export Format</label>
                                <select name="format" id="format" class="form-select" required>
                                    <option value="pdf">PDF</option>
                                    <option value="excel">Excel</option>
                                </select>
                            </div>

                            <!-- Export Button -->
                            <div class="col-12 col-md-4">
                                <button type="submit" class="btn btn-primary w-100">
                                    Export Report
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row">
                <div class="col-12 order-5">
                    <div class="card py-3">
                        <div class="card-datatable table-responsive py-3 px-4">
                            <table id="table-content" class="datatables-basic table display">
                                <thead>
                                    <tr>
                                        <th>NO</th>                           <th>HARI</th>
                                         <th>WAKTU DETEKSI SYSTEM</th>
                                        <th>GURU</th>
                                        <th>KELAS</th>
                                        <th>PELAJARAN</th>
                                        <th>MULAI PELAJARAN</th>
                                        <th>AKHIR PELAJARAN</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach ($teacher as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ [
                                            'Monday' => 'Senin',
                                            'Tuesday' => 'Selasa',
                                            'Wednesday' => 'Rabu',
                                            'Thursday' => 'Kamis',
                                            'Friday' => 'Jumat',
                                            'Saturday' => 'Sabtu',
                                            'Sunday' => 'Minggu'
                                        ][$item->day] ?? $item->day }}</td> 
                                        <td>{{ \Carbon\Carbon::parse($item->created_at)->locale('id')->formatLocalized('%d %B %Y %H:%M') }}</td>
                                        <td>{{ $item->teacher_name }}</td>
                                        <td>{{ $item->type_class_category }} {{ $item->class_name }}</td>
                                        <td>{{ $item->course_name }}</td>
                                        <td>{{ $item->start_time }}</td>
                                        <td>{{ $item->end_time }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="content-backdrop fade"></div>
    </div>
@endsection

@section('js')
     <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.js"></script>

    <!-- Inisialisasi DataTable -->
    <script>
        $(document).ready(function() {
            $('#table-content').DataTable();
        });
    </script>
    <script>
        $('.btn-update').click(function() {
            var id = $(this).data('id');
            var name = $(this).data('name');
            var email = $(this).data('email');
            var actionUrl = `user_admin/${id}`;
            $('#form-update').attr('action', actionUrl);

            var formUpdate = $('#modal-admin-update #div-update');
            formUpdate.find('#basic-default-name-update').val(name);
            formUpdate.find('#basic-default-email-update').val(email);

            $('#modal-admin-update').modal('show');
        });

        $('.btn-delete').click(function() {
            id = $(this).data('id')
            var actionUrl = `user_admin/${id}`;
            console.log(actionUrl);
            console.log(id);
            $('#form-delete').attr('action', actionUrl);
            $('#modal-delete').modal('show')
        });
    </script>
    <script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/%40form-validation/umd/bundle/popular.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/%40form-validation/umd/plugin-bootstrap5/index.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/%40form-validation/umd/plugin-auto-focus/index.min.js') }}"></script>
    <script src="{{ asset('assets/js/ui-modals.js') }}"></script>
@endsection