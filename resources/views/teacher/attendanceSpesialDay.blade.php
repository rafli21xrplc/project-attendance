@extends('teacher.layouts.app')

@section('link')
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/app-logistics-dashboard.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/%40form-validation/umd/styles/index.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/animate-css/animate.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css') }}" />
    <link rel="stylesheet"
        href="{{ asset('assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/jquery-timepicker/jquery-timepicker.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/pickr/pickr-themes.css') }}" />

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
            background-color: #f0f0f0;
        }

        #table-content tbody tr:nth-child(odd) {
            background-color: #f9f9f9;
        }

        #table-content tbody tr:hover {
            background-color: #e9e9e9;
        }

        .flatpickr-calendar {
            background: white;
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

        .table-custom {
            border: 1px solid #ddd;
            border-radius: 10px;
            overflow: hidden;
        }

        .table-custom thead th {
            border: none;
            background-color: #f2f2f2;
        }

        .table-custom thead tr {
            border: 1px solid #ddd;
        }

        .table-custom tbody tr,
        .table-custom tbody td {
            border: none;
        }

        .table-custom th,
        .table-custom td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        .table-custom th {
            text-align: left;
        }

        .form-check-label {
            font-size: 14px;
        }

        .custom-border {
            border: 1px solid #ddd;
            border-radius: 10px;
        }

        .table-custom tbody tr:nth-child(odd) {
            background-color: #f9f9f9;
        }

        .table-custom tbody tr:nth-child(even) {
            background-color: #ffffff;
        }
    </style>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row">
                <div class="col-12 order-5">
                    @if (!$isDayDone && $isDay)
                        <div class="d-flex justify-content-end mb-4">
                            <button id="saveButton" type="button" class="btn btn-label-success">
                                <span class="d-none d-sm-inline-block">Simpan</span>
                            </button>
                        </div>
                    @endif
                    @if ($isDay)
                        @isset($classroom)
                            <div class="card">
                                <div class="card-datatable table-responsive px-4">
                                    <div class="my-4">
                                        <div class="my-4 d-flex flex-wrap justify-content-between gap-3">
                                            <div class="card-title mb-0">
                                                <h5 class="mb-1">{{ $classroom->name }}</h5>
                                                <small class="text-danger">System absensi hari khusus gunanya untuk memudakan
                                                    guru wali kelas mendata kehadiran siswa pada event tertentu.</small>
                                            </div>
                                        </div>
                                        <div class="table-responsive text-nowrap custom-border">
                                            @if ($isDayDone)
                                                <div class="d-flex justify-content-center align-items-center my-5 flex-column">
                                                    <img src="{{ asset('assets/content/empty.svg') }}" width="300"
                                                        alt="Attendance Already Taken">
                                                    <small class="text-success">Absensi sudah dilakukan.</small>
                                                </div>
                                            @else
                                                @if ($classroom->students->isNotEmpty())
                                                    <form id="attendanceForm"
                                                        action="{{ route('teacher.attendance_spesial_day.store', $classroom->id) }}"
                                                        method="post">
                                                        @csrf
                                                        @method('POST')
                                                        <table class="table table-custom" id="table-content">
                                                            <thead>
                                                                <tr>
                                                                    <th class="text-center">STUDENT ID</th>
                                                                    <th>NAME</th>
                                                                    <th class="text-center">ATTENDANCE</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($classroom->students as $index => $student)
                                                                    <tr>
                                                                        <td class="text-center">{{ $index + 1 }}</td>
                                                                        <td>{{ $student->name }}</td>
                                                                        <td>
                                                                            <div
                                                                                class="col-md d-flex align-items-center flex-wrap gap-2 justify-content-center">
                                                                                <input type="hidden"
                                                                                    name="attendance[{{ $student->id }}]"
                                                                                    value="present">
                                                                                <div class="form-check form-check-danger">
                                                                                    <input
                                                                                        name="attendance[{{ $student->id }}]"
                                                                                        class="form-check-input" type="radio"
                                                                                        value="alpha"
                                                                                        id="alpha_{{ $student->id }}" />
                                                                                    <label class="form-check-label"
                                                                                        for="alpha_{{ $student->id }}">Alpha</label>
                                                                                </div>
                                                                                <div class="form-check form-check-warning">
                                                                                    <input
                                                                                        name="attendance[{{ $student->id }}]"
                                                                                        class="form-check-input" type="radio"
                                                                                        value="permission"
                                                                                        id="izin_{{ $student->id }}" />
                                                                                    <label class="form-check-label"
                                                                                        for="izin_{{ $student->id }}">Permission</label>
                                                                                </div>
                                                                                <div class="form-check form-check-alpha">
                                                                                    <input
                                                                                        name="attendance[{{ $student->id }}]"
                                                                                        class="form-check-input" type="radio"
                                                                                        value="sick"
                                                                                        id="sakit_{{ $student->id }}" />
                                                                                    <label class="form-check-label"
                                                                                        for="sakit_{{ $student->id }}">Sick</label>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </form>
                                                @else
                                                    <div class="d-flex justify-content-center align-items-center my-5">
                                                        <img src="{{ asset('assets/content/empty.svg') }}" width="300"
                                                            alt="No Data Available">
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="d-flex justify-content-center align-items-center my-5 flex-column">
                                <img src="{{ asset('assets/content/empty.svg') }}" width="300" alt="No Data Available">
                            </div>
                        @endisset
                    @else
                        <div class="d-flex justify-content-center align-items-center my-5 flex-column">
                            <img src="{{ asset('assets/content/empty.svg') }}" width="300" alt="No Data Available">
                            <small class="text-danger">Hari ini tidak ada jadwal kehadiran khusus</small>
                        </div>
                    @endif

                </div>
            </div>
        </div>
        <div class="content-backdrop fade"></div>
    </div>
@endsection

@section('js')
    <script>
        document.getElementById('saveButton').addEventListener('click', function() {
            document.getElementById('attendanceForm').submit();
        });
    </script>
    <script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/%40form-validation/umd/bundle/popular.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/%40form-validation/umd/plugin-bootstrap5/index.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/%40form-validation/umd/plugin-auto-focus/index.min.js') }}"></script>
    <script src="{{ asset('assets/js/ui-modals.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/pickr/pickr.js') }}"></script>
    <script src="{{ asset('assets/js/forms-pickers.js') }}"></script>
@endsection
