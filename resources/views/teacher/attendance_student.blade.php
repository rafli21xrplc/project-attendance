@extends('teacher.layouts.app')

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
         #saveButton {
        background-color: #28a745; /* Stronger green color */
        border-color: #28a745; /* Match border color to background */
        color: #fff; /* White text color for better contrast */
    }

    #saveButton:hover {
        background-color: #218838; /* Slightly darker green on hover */
        border-color: #1e7e34; /* Darker border on hover */
    }
    </style>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row">
                <div class="col-12 order-5">
                    @isset($classroom)
                        <div class="card">
                            <div class="card-datatable table-responsive px-4">
                                <div class="my-4">
                                    <div class="my-4 d-flex flex-wrap justify-content-between gap-3">
                                        <div class="card-title mb-0">
                                            <h5 class="mb-1">{{ $classroom->name }}</h5>
                                            <p class="text-muted mb-0">Total 6 course you have purchased</p>
                                        </div>
                                    </div>
                                    <div class="table-responsive text-nowrap custom-border">
                                            <form id="attendanceForm"
                                                action="{{ route('teacher.attendance.update.history', $schedule->id) }}" method="post">
                                                @csrf
                                                @method('POST')
                                                <table class="table table-custom" id="table-content">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">STUDENT ID</th>
                                                            <th>NAMA</th>
                                                            <th class="text-center">PRESENSI</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($attendance as $index => $item)
                                                            <tr>
                                                                <td class="text-center">{{ $index + 1 }}</td>
                                                                <td>{{ $item->student_name }}</td>
                                                                <td>
                                                                    <div
                                                                        class="col-md d-flex align-items-center flex-wrap gap-2 justify-content-center">
                                                                        <input type="hidden"
                                                                            name="attendance[{{ $item->id }}]" 
                                                                            value="present">
                                                                        <div class="form-check form-check-success">
                                                                            <input name="attendance[{{ $item->id }}]"
                                                                                class="form-check-input" type="radio" {{ $item->status == 'present' ? 'checked' : '' }}
                                                                                value="present" 
                                                                                id="present_{{ $item->id }}" />
                                                                            <label class="form-check-label"
                                                                                for="present_{{ $item->id }}"> Hadir </label>
                                                                        </div>
                                                                        <div class="form-check form-check-danger">
                                                                            <input name="attendance[{{ $item->id }}]"
                                                                                class="form-check-input" type="radio" {{ $item->status == 'alpha' ? 'checked' : '' }}
                                                                                value="alpha" 
                                                                                id="alpha_{{ $item->id }}" />
                                                                            <label class="form-check-label"
                                                                                for="alpha_{{ $item->id }}"> Alpha </label>
                                                                        </div>
                                                                        <div class="form-check form-check-warning">
                                                                            <input name="attendance[{{ $item->id }}]"
                                                                                class="form-check-input" type="radio" {{ $item->status == 'permission' ? 'checked' : '' }}
                                                                                value="permission"
                                                                                id="izin_{{ $item->id }}" />
                                                                            <label class="form-check-label"
                                                                                for="izin_{{ $item->id }}"> Izin </label>
                                                                        </div>
                                                                        <div class="form-check form-check-alpha">
                                                                            <input name="attendance[{{ $item->id }}]"
                                                                                class="form-check-input" type="radio" {{ $item->status == 'sick' ? 'checked' : '' }}
                                                                                value="sick"
                                                                                id="sakit_{{ $item->id }}" />
                                                                            <label class="form-check-label"
                                                                                for="sakit_{{ $item->id }}"> Sakit </label>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="d-flex justify-content-center align-items-center my-5">
                            <img src="{{ asset('assets/content/empty.svg') }}" width="300" alt="No Data Available">
                        </div>
                    @endisset
                    <div class="d-flex justify-content-end mt-4">
                        <button id="saveButton" type="button" class="btn btn-label-success">
                           Simpan
                        </button>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
        const saveButton = document.getElementById('saveButton');
        const classroomElement = @json(isset($classroom));
        
        if (classroomElement && document.querySelectorAll('#attendanceForm tbody tr').length > 0) {
            saveButton.classList.remove('d-none');
        } else {
            saveButton.classList.add('d-none');
        }
    });

    document.getElementById('saveButton').addEventListener('click', function() {
        document.getElementById('attendanceForm').submit();
    });
    </script>
   <script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/%40form-validation/umd/bundle/popular.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/%40form-validation/umd/plugin-bootstrap5/index.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/%40form-validation/umd/plugin-auto-focus/index.min.js') }}"></script>
    <script src="{{ asset('assets/js/mainf696.js?id=8bd0165c1c4340f4d4a66add0761ae8a') }}"></script>
    <script src="{{ asset('assets/js/ui-modals.js') }}"></script>
@endsection