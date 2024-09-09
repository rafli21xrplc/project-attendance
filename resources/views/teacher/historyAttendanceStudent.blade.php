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
                <div class="d-flex justify-content-end mb-4">
                    <button id="saveButton" type="button" class="btn btn-label-success"
                        onclick="document.getElementById('attendanceForm').submit();">
                        <span class="d-none d-sm-inline-block">Save</span>
                    </button>
                </div>
                <div class="card">
                    <div class="card-datatable table-responsive px-4">
                        <div class="my-4">
                            <div class="d-flex flex-wrap justify-content-between gap-3 my-3">
                                <div class="card-title mb-0">
                                    <h5 class="mb-1">{{ $classroom->name }}</h5>
                                    <p class="text-muted mb-0">Attendance for {{ $schedule->course->name }}</p>
                                </div>
                            </div>
                            <div class="table-responsive text-nowrap custom-border">
                                <form id="attendanceForm"
                                    action="{{ route('teacher.attendance.update.history', $schedule->id) }}"
                                    method="post">
                                    @csrf
                                    @method('POST')
                                    <table class="table table-custom" id="table-content">
                                        <thead>
                                            <tr>
                                                <th class="text-center">Student ID</th>
                                                <th>Name</th>
                                                <th class="text-center">Attendance</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($student as $index => $item)
                                                @php
                                                    $attendance = $attendanceData[$item->id] ?? null;
                                                    $status = $attendance ? $attendance->status : '';
                                                    $fileUrl =
                                                        $attendance &&
                                                        $attendance->permission &&
                                                        ($status == 'permission' || $status == 'sick')
                                                            ? Storage::url($attendance->permission->file)
                                                            : null;
                                                @endphp
                                                <tr>
                                                    <td class="text-center">{{ $item->student_id }}</td>
                                                    <td>{{ $item->name }}</td>
                                                    <td>
                                                        <div
                                                            class="col-md d-flex align-items-center flex-wrap gap-2 justify-content-center">
                                                            <div class="form-check form-check-success">
                                                                <input name="attendance[{{ $item->id }}]"
                                                                    class="form-check-input" type="radio"
                                                                    value="present" id="hadir_{{ $item->id }}"
                                                                    {{ $status == 'present' ? 'checked' : '' }}
                                                                    required />
                                                                <label class="form-check-label"
                                                                    for="hadir_{{ $item->id }}">Present</label>
                                                            </div>
                                                            <div class="form-check form-check-danger">
                                                                <input name="attendance[{{ $item->id }}]"
                                                                    class="form-check-input" type="radio"
                                                                    value="alpha" id="alpha_{{ $item->id }}"
                                                                    {{ $status == 'alpha' ? 'checked' : '' }} />
                                                                <label class="form-check-label"
                                                                    for="alpha_{{ $item->id }}">Alpha</label>
                                                            </div>
                                                            <div class="form-check form-check-warning">
                                                                <input name="attendance[{{ $item->id }}]"
                                                                    class="form-check-input" type="radio"
                                                                    value="permission" id="izin_{{ $item->id }}"
                                                                    {{ $status == 'permission' ? 'checked' : '' }} />
                                                                <label class="form-check-label"
                                                                    for="izin_{{ $item->id }}">Permission</label>
                                                            </div>
                                                            <div class="form-check form-check-warning">
                                                                <input name="attendance[{{ $item->id }}]"
                                                                    class="form-check-input" type="radio"
                                                                    value="sick" id="sakit_{{ $item->id }}"
                                                                    {{ $status == 'sick' ? 'checked' : '' }} />
                                                                <label class="form-check-label"
                                                                    for="sakit_{{ $item->id }}">Sick</label>
                                                            </div>
                                                            <div class="d-flex align-items-center mx-2">
                                                                @if ($status == 'permission' && $fileUrl)
                                                                    <a href="{{ $fileUrl }}" target="_blank"
                                                                        class="ml-2"><i
                                                                            class="fa fa-eye fs-5"></i></a>
                                                                @endif
                                                                @if ($status == 'sick' && $fileUrl)
                                                                    <a href="{{ asset('public/' .  $fileUrl) }}" target="_blank"
                                                                        class="ml-2"><i
                                                                            class="fa fa-file-medical-alt fs-5"></i></a>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center">No students found</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content-backdrop fade"></div>
</div>

@endsection

@section('js')
    <script>
        document.getElementById('saveButton').addEventListener('click', function() {
            var form = document.getElementById('attendanceForm');
            var allChecked = true;
            var students = @json($student);

            students.forEach(function(student) {
                var radios = form.querySelectorAll('input[name="attendance[' + student.id + ']"]:checked');
                if (radios.length === 0) {
                    allChecked = false;
                }
            });

            if (!allChecked) {
                alert('Please select attendance status for all students.');
            } else {
                form.submit();
            }
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
