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
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="app-academy">
                <div class="card p-0 mb-4">
                    <div class="card-body d-flex flex-column flex-md-row justify-content-between p-0 pt-4">
                        <div class="app-academy-md-25 card-body py-0">
                            <img src="{{ asset('assets/img/illustrations/bulb-light.png') }}"
                                class="img-fluid app-academy-img-height scaleX-n1-rtl" alt="Bulb in hand" height="90" />
                        </div>
                        <div class="app-academy-md-50 card-body d-flex align-items-md-center flex-column text-md-center">
                            <h3 class="card-title mb-4 lh-sm px-md-5 lh-lg">
                                Welcome to Your Teaching Dashboard
                                <span class="text-primary fw-medium text-nowrap">All your classes</span> in one place.
                            </h3>
                            <p class="mb-3">
                                Manage your classes, track student progress, and stay updated
                                with the latest teaching resources and tools.
                            </p>
                        </div>
                        <div class="app-academy-md-25 d-flex align-items-end justify-content-end">
                            <img src="{{ asset('assets/img/illustrations/pencil-rocket.png') }}" alt="pencil rocket"
                                height="188" class="scaleX-n1-rtl" />
                        </div>
                    </div>
                </div>

                <div class="row my-2">
                    <div class="col-12">
                        <form action="{{ route('admin.report.attendance_student.search') }}" method="get">
                            <div class="row justify-content-end align-items-center g-3">
                                <div class="col-12 col-md-4">
                                    <div class="input-group">
                                        <input type="date" class="form-control" placeholder="Tanggal" name="date" />
                                    </div>
                                </div>
                                <div class="col-12 col-md-2">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <span class="d-none d-sm-inline-block">Export</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header d-flex flex-wrap justify-content-between gap-3">
                        <div class="card-title mb-0 me-1">
                            <h5 class="mb-1">My Classes</h5>
                            <p class="text-muted mb-0">
                                Overview of your current classes
                            </p>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row gy-4 mb-4">
                            @forelse ($schedule as $item)
                                <div class="col-sm-6 col-lg-4">
                                    <div class="card p-2 h-100 shadow-none border">
                                        <div class="rounded-2 text-center mb-3">
                                            <a href="#"><img class="img-fluid"
                                                    src="{{ asset('assets/content/classroom_bg.png') }}"
                                                    alt="classroom image" /></a>
                                        </div>
                                        <div class="card-body p-3 pt-2">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <span class="badge bg-label-primary">{{ $item->course->name }}</span>
                                                <h6 class="d-flex align-items-center justify-content-center gap-1 mb-0">
                                                    <span
                                                        class="text-muted badge bg-label-info">{{ $item->classroom->students->count() }}
                                                        Students</span>
                                                </h6>
                                            </div>
                                            <h5 class="mb-2">{{ $item->classroom->typeClass->category }}
                                                {{ $item->classroom->name }}</h5>
                                            <p class="d-flex align-items-center mb-2">
                                                <i class="ti ti-clock me-2 mt-n1"></i>
                                                {{ \Carbon\Carbon::parse($item->StartTimeSchedules->start_time_schedule)->format('H:i') }} -
                                                {{ \Carbon\Carbon::parse($item->EndTimeSchedules->end_time_schedule)->format('H:i') }}
                                            </p>
                                            <p class="mb-0">
                                                {{ $item->description ?? 'Class details and learning objectives.' }}
                                            </p>
                                            <div
                                                class="d-flex flex-column flex-md-row text-nowrap justify-content-end mt-3">
                                                <a class="btn btn-label-warning d-flex align-items-center"
                                                    href="{{ route('teacher.attendance.history', ['classroomid' => $item->classroom->id, 'scheduleId' => $item->id]) }}">
                                                    <span class="me-2">Update Attendance</span><i
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

    <script>
        new DataTable('#table-content', {
            pagingType: 'simple_numbers'
        });
    </script>
@endsection
