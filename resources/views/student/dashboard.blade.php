@extends('student.layouts.app')

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
    </style>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row">
                <div class="col-12 mb-4">
                    <div class="card p-0 mb-4">
                        <div class="card-body d-flex flex-column flex-md-row justify-content-between p-0">
                            <div class="app-academy-md-25 d-flex align-items-start justify-content-start">
                                <img src="{{ asset('assets/content/ornament-left.png') }}" alt="pencil rocket"
                                    width="200" class="scaleX-n1-rtl" />
                            </div>
                            <div
                                class="app-academy-md-50 card-body d-flex align-items-md-center flex-column text-md-center">
                                <h3 id="greeting" class="card-title mb-4 lh-sm px-md-5 lh-lg"></h3>
                                <p class="mb-3">Find out how easy it is to make your classes engaging, more
                                    functional, and more efficient.</p>
                            </div>
                            <div class="app-academy-md-25 d-flex align-items-end justify-content-end">
                                <img src="{{ asset('assets/content/ornament-right.png') }}" alt="pencil rocket"
                                    width="200" class="scaleX-n1-rtl" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container">
                <div class="row">
                    <div class="col-12">
                            <div class="card-body">
                                <h2 class="mb-4">Today's Schedule</h2>
                                @if ($schedules->isEmpty())
                                    <p>No classes scheduled for today.</p>
                                @else
                                    <div class="row">
                                        @foreach ($schedules as $schedule)
                                            <div class="col-md-6">
                                                <div class="card mb-3 shadow-sm schedule-card">
                                                    <div class="card-body d-flex align-items-center">
                                                        <div class="text-center border-end schedule-date">
                                                            <h6 class="text-primary mb-1">{{ now()->format('D') }}</h6>
                                                            <h4 class="mb-0">{{ now()->format('d') }}</h4>
                                                        </div>
                                                        @if ($schedule->course != null && $schedule->classroom != null && $schedule->teacher != null)

                                                        <div class="flex-grow-1 schedule-details ms-3">
                                                            <h5 class="card-title mb-1">{{ $schedule->course->name }}</h5>
                                                            <p class="card-text text-muted">
                                                                {{ $schedule->classroom->typeClass->category }}
                                                                {{ $schedule->classroom->name }}
                                                            </p>
                                                        </div>
                                                        <div class="text-end schedule-time">
                                                            <h6 class="mb-1">
                                                                {{ \Carbon\Carbon::parse($schedule->StartTimeSchedules->start_time_schedule)->format('H:i') }}
                                                                -
                                                                {{ \Carbon\Carbon::parse($schedule->EndTimeSchedules->end_time_schedule)->format('H:i') }}
                                                            </h6>
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
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

        const studentName = "{{ Auth::user()->student->name }}";
        greetingElement.innerHTML = `
        ${greetingText}, ${studentName}!
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

    <script>
        new DataTable('#table-content', {
            pagingType: 'simple_numbers'
        });
    </script>
@endsection
