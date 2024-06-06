@extends('Admin.layouts.app')

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
    </style>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row">
                <div class="col-12 order-5">
                    <form action="{{ route('admin.report.attendance_student.search') }}" method="post">
                        @csrf
                        <div class="row ">
                            <div class="row justify-content-end">
                                <div class="col-md-3">
                                    <input type="text" class="form-control" placeholder="YYYY-MM-DD to YYYY-MM-DD"
                                        name="range-date" id="flatpickr-range" />
                                </div>
                                <div class="col-md-3">
                                    <select class="form-select" name="classroom_id">
                                        <option selected disabled>pilih kelas</option>
                                        @foreach ($classrooms as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-1">
                                    <button data-bs-toggle="modal" data-bs-target="#modal-admin" type="submit"
                                        class="btn btn-label-success"><span
                                            class="d-none d-sm-inline-block">Search</span></button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row my-5">
                <div class="col-12 order-5">
                    <div class="card py-3 ">

                           @if ($report != null)
                           <div class="container">
                            <h1>Monthly Attendance Report for Selected Classes</h1>
                            <p>From: {{ $startDate }} To: {{ $endDate }}</p>
                            
                            @foreach ($classrooms as $classroom)
                                <h2>Class: {{ $classroom->name }}</h2>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Student Name</th>
                                            @foreach (Carbon\CarbonPeriod::create($startDate, $endDate) as $date)
                                                <th>{{ $date->format('d M') }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($report as $studentId => $studentReport)
                                            @if ($studentReport['class'] == $classroom->name)
                                                <tr>
                                                    <td>{{ $studentReport['name'] }}</td>
                                                    @foreach ($studentReport['attendance'] as $date => $summary)
                                                        <td>{{ $summary }}</td>
                                                    @endforeach
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            @endforeach
                        </div>
                            
                        @else
                            
                        @endif


                        <form action="{{ route('admin.export.attendance_report') }}" method="get">
                            @csrf
                            {{-- <input type="hidden" name="classroom_ids[]" value="{{ implode(',', $classrooms->pluck('id')->toArray()) }}">
                            <input type="hidden" name="start_date" value="{{ $startDate }}">
                            <input type="hidden" name="end_date" value="{{ $endDate }}"> --}}
                            <button type="submit" class="btn btn-success mt-2">Export to Excel</button>
                        </form>
                        

                        {{-- @if ($report != null)
                        <div class="container table-responsive">
                            <h1>Monthly Attendance Report for Class: {{ $classroom->name }}</h1>
                            <p>From: {{ $startDate }} To: {{ $endDate }}</p>

                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Student Name</th>
                                        @foreach (Carbon\CarbonPeriod::create($startDate, $endDate) as $date)
                                            <th>{{ $date->format('d M') }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($report as $studentId => $studentReport)
                                        <tr>
                                            <td>{{ $studentReport['name'] }}</td>
                                            @foreach ($studentReport['attendance'] as $date => $summary)
                                                <td>{{ $summary }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                            
                        @else
                            
                        @endif --}}

                        

                    </div>
                </div>

            </div>
        </div>

        <div class="content-backdrop fade"></div>
    </div>
@endsection

@section('js')
    {{-- <script>
        new DataTable('#table-content', {
            pagingType: 'simple_numbers'
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
    </script> --}}
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
