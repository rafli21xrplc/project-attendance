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
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.2/css/dataTables.dataTables.css">
    <style>
        .schedule-card {
            border-radius: 10px;
        }

        .schedule-date {
            width: 80px;
            margin-right: 20px;
        }

        .schedule-details h5 {
            font-weight: bold;
        }

        .schedule-time h6 {
            font-weight: normal;
        }

        .border-end {
            border-right: 1px solid #e0e0e0 !important;
        }

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
        @media (max-width: 768px) {
            #hide-on-mobile {
                display: none;
            }
        }
    </style>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row">
                <div class="col-12 mb-4">
                    <div class="card p-0 mb-4">
                        <div class="card-body d-flex flex-column flex-md-row justify-content-between p-0 ">
                            <div class="app-academy-md-25 d-flex align-items-end justify-content-end hide-on-mobile" id="hide-on-mobile">
                                <img src="{{ asset('assets/content/ornament-left.png') }}" alt="pencil rocket" width="200" class="scaleX-n1-rtl" id="hide-on-mobile" />
                            </div>
                            <div class="app-academy-md-50 card-body d-flex align-items-md-center flex-column text-md-center">
                                <h3 id="greeting" class="card-title mb-4 lh-sm px-md-5 lh-lg"></h3>
                                <p class="mb-3">Find out how easy it is to make your classes engaging, more functional, and more efficient.</p>
                            </div>
                            <div class="app-academy-md-25 d-flex align-items-end justify-content-end hide-on-mobile" id="hide-on-mobile">
                                <img src="{{ asset('assets/content/ornament-right.png') }}" alt="pencil rocket" width="200" class="scaleX-n1-rtl" id="hide-on-mobile" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <h4 class="py-2 mb-4">
                    Schedule hari ini
                </h4>
                <div class="row ">
                    @forelse ($schedules as $schedule)
                        <div class="col-md-6">
                            <div class="card mb-3 shadow-sm schedule-card">
                                <div class="card-body d-flex align-items-center">
                                    <div class="text-center border-end schedule-date">
                                        <h6 class="text-primary mb-1">{{ now()->format('D') }}</h6>
                                        <h4 class="mb-0">{{ now()->format('d') }}</h4>
                                    </div>
                                    <div class="flex-grow-1 schedule-details ms-3">
                                        <h5 class="card-title mb-1">{{ $schedule->course->name }}</h5>
                                        <p class="card-text text-muted">
                                            {{ $schedule->classroom->typeClass->category }}
                                            {{ $schedule->classroom->name }}</p>
                                    </div>
                                    <div class="text-end schedule-time">
                                        <h6 class="mb-1">
                                            {{ \Carbon\Carbon::parse($schedule->StartTimeSchedules->start_time_schedule)->format('H:i') }}
                                            -
                                            {{ \Carbon\Carbon::parse($schedule->EndTimeSchedules->end_time_schedule)->format('H:i') }}
                                        </h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="d-flex justify-content-center align-items-center my-5 flex-column">
                                <img src="{{ asset('assets/content/empty.svg') }}" width="300" alt="No Data Available">
                                <small>No schedules for today.</small>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="row">
                <h4 class="py-2 mb-4">
                    Top rank point kedisiplinan
                </h4>
                <div class="card-datatable table-responsive">
                    <div class="my-4">
                        <div class="table-responsive text-nowrap custom-border">
                            <table class="table table-custom" id="table-content">
                                <thead>
                                    <tr>
                                        <th class="text-center">Rank</th>
                                        <th class="text-center">Student ID</th>
                                        <th>Name</th>
                                        <th class="text-center">Class</th>
                                        <th class="text-center">Total Violation Points</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($violations as $index => $student)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td class="text-center">{{ $student['id'] }}</td>
                                            <td>{{ $student['name'] }}</td>
                                            <td class="text-center">{{ $student['class_name'] }}</td>
                                            <td class="text-center">{{ $student['total_tatib_points'] }}</td>
                                        </tr>
                                    @empty
                                        <div class="col-12">
                                            <div class="d-flex justify-content-center align-items-center my-5 flex-column">
                                                <img src="{{ asset('assets/content/empty.svg') }}" width="300"
                                                    alt="No Data Available">
                                                <small>No studeFnts found.</small>
                                            </div>
                                        </div>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container">
                <h4 class="py-2 mb-4">
                    Kelas
                </h4>
                <div class="row">
                    <div class="col-12">
                        <ul class="nav nav-pills flex-column flex-sm-row mb-4 justify-content-center">
                            @foreach ($typeClasses as $index => $typeClass)
                                <li class="nav-item mx-1">
                                    <button class="nav-link {{ $index == 0 ? 'active' : '' }}" data-bs-toggle="tab"
                                        data-bs-target="#form-tabs-{{ $typeClass->id }}" role="tab"
                                        aria-selected="{{ $index == 0 ? 'true' : 'false' }}">
                                        {{ $typeClass->category }}
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="tab-content p-0">
                        @forelse ($typeClasses as $index => $typeClass)
                            <div class="tab-pane fade {{ $index == 0 ? 'active show' : '' }}"
                                id="form-tabs-{{ $typeClass->id }}" role="tabpanel">
                                <div class="row g-4">
                                    @foreach ($typeClass->classRooms as $classRoom)
                                        <div class="col-xl-4 col-lg-6 col-md-6">
                                            <div class="card shadow-sm">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center mb-3">
                                                        <div class="me-2">
                                                            <h5 class="card-title mb-0">{{ $classRoom->name }}</h5>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <span
                                                            class="badge bg-primary m-1">{{ $classRoom->students->count() }}
                                                            SISWA</span>
                                                        @foreach ($classRoom->schedules as $schedule)
                                                            <span
                                                                class="badge bg-info m-1">{{ $schedule->course->name }}</span>
                                                        @endforeach
                                                    </div>
                                                    <div class="text-center">
                                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                                            id="detail-{{ $classRoom->id }}"
                                                            onclick="showStudentsModal(`{{ $classRoom->id }}`, `{{ $classRoom->name }}`)"
                                                            data-students='@json($classRoom->students)'>
                                                            Detail
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @empty
                            <div class="col-12">
                                <div class="d-flex justify-content-center align-items-center my-5 flex-column">
                                    <img src="{{ asset('assets/content/empty.svg') }}" width="300"
                                        alt="No Data Available">
                                    <small>classroom empty.</small>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>


        </div>
    </div>


    <div class="content-backdrop fade"></div>
    </div>

    <div class="modal fade" id="studentsModal" tabindex="-1" aria-labelledby="studentsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-simple modal-enable-otp modal-dialog-centered">
            <div class="modal-content p-3 p-md-5">
                <div class="modal-body">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="text-center">
                        <h3 class="mb-2" id="studentsModalLabel">Students</h3>
                        <p>List of students in the class</p>
                    </div>
                    <div id="studentsContent">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        function showStudentsModal(classRoomId, classRoomName) {
            var button = document.querySelector('#detail-' + classRoomId);

            var students = JSON.parse(button.getAttribute('data-students'));

            document.getElementById('studentsModalLabel').innerText = 'Students in ' + classRoomName;

            var studentsContent = document.querySelector('#studentsContent');

            studentsContent.innerHTML = '';

            if (students.length === 0) {
                studentsContent.innerHTML = '<p>No students in this class.</p>';
            } else {
                var container = document.createElement('div');
                container.className = 'd-flex flex-wrap ';

                students.forEach(function(student) {
                    var studentItem = document.createElement('div');
                    studentItem.className = 'p-2';
                    studentItem.innerHTML = `
                <div class="border rounded p-2 d-inline-block">
                    <h6 class="mb-0">${student.name}</h6>
                </div>
            `;
                    container.appendChild(studentItem);
                });
                studentsContent.appendChild(container);
            }

            var studentsModal = new bootstrap.Modal(document.getElementById('studentsModal'));
            studentsModal.show();
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const greetingElement = document.getElementById('greeting');
            const now = new Date();
            const hours = now.getHours();
            let greetingText = '';

            if (hours < 12) {
                greetingText = 'Good Morning';
            } else if (hours < 18) {
                greetingText = 'Good Afternoon';
            } else {
                greetingText = 'Good Evening';
            }

            const teacherName = "{{ Auth::user()->teacher->name }}";
            greetingElement.innerHTML = `
            ${greetingText}, ${teacherName}!
        `;
        });
    </script>
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/%40form-validation/umd/bundle/popular.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/%40form-validation/umd/plugin-bootstrap5/index.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/%40form-validation/umd/plugin-auto-focus/index.min.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.0.2/js/dataTables.js"></script>
@endsection
