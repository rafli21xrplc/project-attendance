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
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/app-academy.css') }}" />
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.2/css/dataTables.dataTables.css">
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
            b
    </style>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">

            <div class="app-academy">
                <div class="card p-0 mb-4">
                    <div class="card-body d-flex flex-column flex-md-row justify-content-between p-0 pt-4">
                        <div class="app-academy-md-25 card-body py-0">
                            <img src="{{ asset('assets/img/illustrations/bulb-light.png') }}"
                                class="img-fluid app-academy-img-height scaleX-n1-rtl" alt="Bulb in hand"
                                data-app-light-img="illustrations/bulb-light.png"
                                data-app-dark-img="illustrations/bulb-dark.png" height="90" />
                        </div>
                        <div class="app-academy-md-50 card-body d-flex align-items-md-center flex-column text-md-center">
                            <h3 class="card-title mb-4 lh-sm px-md-5 lh-lg">
                                Education, talents, and career opportunities.
                                <span class="text-primary fw-medium text-nowrap">All in one place</span>.
                            </h3>
                            <p class="mb-3">
                                Grow your skill with the most reliable online courses
                                and certifications in marketing, information technology,
                                programming, and data science.
                            </p>
                        </div>
                        <div class="app-academy-md-25 d-flex align-items-end justify-content-end">
                            <img src="{{ asset('assets/img/illustrations/pencil-rocket.png') }}" alt="pencil rocket"
                                height="188" class="scaleX-n1-rtl" />
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header d-flex flex-wrap justify-content-between gap-3">
                        <div class="card-title mb-0 me-1">
                            <h5 class="mb-1">My Classroom</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row gy-4 mb-4">
                            @forelse ($schedule as $item)
                                <div class="col-sm-6 col-lg-4">
                                    <div class="card p-2 h-100 shadow-none border">
                                        <div class="rounded-2 text-center mb-3">
                                            <a href="course-details.html"><img class="img-fluid"
                                                    src="{{ asset('assets/content/classroom_bg.png') }}"
                                                    alt="tutor image 1" /></a>
                                        </div>
                                        <div class="card-body p-3 pt-2">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <span class="badge bg-label-primary">{{ $item->course->name }}</span>
                                                <h6 class="d-flex align-items-center justify-content-center gap-1 mb-0">
                                                    <span
                                                        class="text-muted badge bg-label-info">{{ $item->classroom->students->count() }}
                                                        Siswa</span>
                                                </h6>
                                            </div>
                                            <a href="course-details.html" class="h5">{{ $item->classroom->typeClass->category }} {{ $item->classroom->name }}</a>
                                            <div class="d-flex flex-column flex-md-row text-nowrap justify-content-end">
                                                <a class="btn btn-label-primary d-flex align-items-center"
                                                    href="{{ route('teacher.attendance', ['classroomid' => $item->classroom->id, 'scheduleId' => $item->id]) }}">
                                                    <span class="me-2">Absensi</span><i
                                                        class="ti ti-chevron-right scaleX-n1-rtl ti-sm"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="d-flex justify-content-center align-items-center my-5">
                                    <img src="{{ asset('assets/content/empty.svg') }}" width="300"
                                        alt="No Data Available">
                                </div>
                            @endforelse
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
    <script src="{{ asset('assets/js/app-academy-course.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/%40form-validation/umd/bundle/popular.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/%40form-validation/umd/plugin-bootstrap5/index.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/%40form-validation/umd/plugin-auto-focus/index.min.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.0.2/js/dataTables.js"></script>
@endsection
