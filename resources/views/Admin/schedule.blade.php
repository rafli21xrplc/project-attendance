@extends('Admin.layouts.app')

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
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="d-flex justify-content-between align-items-center flex-wrap py-2 px-4">
                <div>
                    <h3>Schedule</h3>
                </div>
                <div>
                    <button type="button" class="btn btn-label-primary me-2" style="color: blue">
                        <i class="ti ti-printer me-1"></i> <span class="d-none d-sm-inline-block">Export</span>
                    </button>
                    <button data-bs-toggle="modal" data-bs-target="#modal-course" type="button"
                        class="btn btn-label-success"><i class="ti ti-plus me-sm-1"></i> <span
                            class="d-none d-sm-inline-block">Add New Record</span></button>
                </div>
            </div>

            <div class="row">
                <div class="col-12 order-5">
                    <div class="card py-3">
                        <div class="card-datatable table-responsive py-3 px-4">
                            <table id="table-content" class="datatables-basic table display">
                                <thead>
                                    <tr class="text-center">
                                        <th>NO</th>
                                        <th>HARI</th>
                                        <th>KELAS</th>
                                        <th>JAM MULAI</th>
                                        <th>JAM SELESAI</th>
                                        <th>PELAJARAN</th>
                                        <th>PENGAJAR</th>
                                        <th>ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($schedule as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $item->day_of_week }}</td>
                                            <td>{{ $item->classroom->name }}</td>
                                            <td>{{ $item->StartTimeSchedules->time_number }}</td>
                                            <td>{{ $item->EndTimeSchedules->time_number }}</td>
                                            <td>{{ $item->course->name }}</td>
                                            <td>{{ $item->teacher->name }}</td>
                                            <td>
                                                <button data-id="{{ $item->id }}"
                                                    data-day_of_week="{{ $item->day_of_week }}"
                                                    data-classroom="{{ $item->classroom->id }}"
                                                    data-start_time_schedule_id="{{ $item->StartTimeSchedules->id }}"
                                                    data-end_time_schedule_id="{{ $item->EndTimeSchedules->id }}"
                                                    data-course="{{ $item->course->id }}"
                                                    data-teacher="{{ $item->teacher->id }}" type="button"
                                                    class="btn btn-label-warning btn-update"><i
                                                        class="fa-solid fa-pen"></i></button>
                                                <button data-id="{{ $item->id }}" type="button"
                                                    class="btn btn-label-danger btn-delete"><i
                                                        class="fa-solid fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="offcanvas offcanvas-end" id="add-new-record">
                        <div class="offcanvas-header border-bottom">
                            <h5 class="offcanvas-title" id="exampleModalLabel">
                                New Record
                            </h5>
                            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                                aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body flex-grow-1">
                            <form class="add-new-record pt-0 row g-2" id="form-add-new-record" onsubmit="return false">
                                <div class="col-sm-12">
                                    <label class="form-label" for="basicFullname">Full Name</label>
                                    <div class="input-group input-group-merge">
                                        <span id="basicFullname2" class="input-group-text"><i class="ti ti-user"></i></span>
                                        <input type="text" id="basicFullname" class="form-control dt-full-name"
                                            name="basicFullname" placeholder="John Doe" aria-label="John Doe"
                                            aria-describedby="basicFullname2" />
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <label class="form-label" for="basicPost">Post</label>
                                    <div class="input-group input-group-merge">
                                        <span id="basicPost2" class="input-group-text"><i
                                                class="ti ti-briefcase"></i></span>
                                        <input type="text" id="basicPost" name="basicPost" class="form-control dt-post"
                                            placeholder="Web Developer" aria-label="Web Developer"
                                            aria-describedby="basicPost2" />
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <label class="form-label" for="basicEmail">Email</label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="ti ti-mail"></i></span>
                                        <input type="text" id="basicEmail" name="basicEmail"
                                            class="form-control dt-email" placeholder="john.doe@example.com"
                                            aria-label="john.doe@example.com" />
                                    </div>
                                    <div class="form-text">
                                        You can use letters, numbers & periods
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <label class="form-label" for="basicDate">Joining Date</label>
                                    <div class="input-group input-group-merge">
                                        <span id="basicDate2" class="input-group-text"><i
                                                class="ti ti-calendar"></i></span>
                                        <input type="text" class="form-control dt-date" id="basicDate"
                                            name="basicDate" aria-describedby="basicDate2" placeholder="MM/DD/YYYY"
                                            aria-label="MM/DD/YYYY" />
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <label class="form-label" for="basicSalary">Salary</label>
                                    <div class="input-group input-group-merge">
                                        <span id="basicSalary2" class="input-group-text"><i
                                                class="ti ti-currency-dollar"></i></span>
                                        <input type="number" id="basicSalary" name="basicSalary"
                                            class="form-control dt-salary" placeholder="12000" aria-label="12000"
                                            aria-describedby="basicSalary2" />
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <button type="submit" class="btn btn-danger data-submit me-sm-3 me-1">
                                        Submit
                                    </button>
                                    <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="content-backdrop fade"></div>
    </div>

    {{-- modal --}}
    <div class="modal fade" id="modal-course" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title mx-auto my-1" id="exampleModalLabel1">JADWAL PELAJARAN</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.schedule.store') }}" method="POST">
                    @csrf
                    <div class="modal-body row py-0">
                        <div class="mb-1">
                            <label class="form-label" for="basic-default-name">Hari</label>
                            <select id="day_of_week" name="day_of_week" class="select2 form-select"
                                aria-label="Default select example">
                                <option selected disabled value="monday">Pilih Hari</option>
                                <option value="Monday">Senin</option>
                                <option value="Tuesday">Selasa</option>
                                <option value="Wednesday">Rabu</option>
                                <option value="Thursday">Kamis</option>
                                <option value="Friday">Jumat</option>
                                <option value="Saturday">Sabtu</option>
                                <option value="Sunday">Minggu</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6 mb-1">
                            <label class="form-label" for="start_time_schedule_id">Waktu Mulai</label>
                            <select id="start_time_schedule_id" name="start_time_schedule_id" class="select2 form-select"
                                aria-label="Default select example">
                                <option selected disabled>Pilih Waktu Mulai</option>
                                @foreach ($time_schedule as $item)
                                    <option value="{{ $item->id }}"
                                        {{ old('start_time_schedule_id') == $item->id ? 'selected' : '' }}>
                                        {{ Carbon\Carbon::parse($item->start_time_schedule)->format('H:i') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6 mb-1">
                            <label class="form-label" for="end_time_schedule_id">Waktu Akhir</label>
                            <select id="end_time_schedule_id" name="end_time_schedule_id" class="select2 form-select"
                                aria-label="Default select example">
                                <option selected disabled>Pilih Waktu Akhir</option>
                                @foreach ($time_schedule as $item)
                                    <option value="{{ $item->id }}"
                                        {{ old('end_time_schedule_id') == $item->id ? 'selected' : '' }}>
                                        {{ Carbon\Carbon::parse($item->end_time_schedule)->format('H:i') }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label" for="teacher_id">Pengajar</label>
                            <select id="teacher_id" name="teacher_id" class="select2 form-select"
                                aria-label="Default select example">
                                <option selected disabled>Pilih Pengajar</option>
                                @foreach ($teacher as $item)
                                    <option value="{{ $item->id }}"
                                        {{ old('teacher') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-4 mb-2">
                            <label class="form-label" for="classroom_id">Kelas</label>
                            <select id="classroom_id" name="classroom_id" class="select2 form-select"
                                aria-label="Default select example">
                                <option selected disabled>Pilih Kelas</option>
                                @foreach ($classroom as $item)
                                    <option value="{{ $item->id }}"
                                        {{ old('classroom') == $item->id ? 'selected' : '' }}>{{ $item->typeClass->category }} {{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-4 mb-2">
                            <label class="form-label" for="course_id">Pelajaran</label>
                            <select id="course_id" name="course_id" class="select2 form-select"
                                aria-label="Default select example">
                                <option selected disabled>Pilih Pelajaran</option>
                                @foreach ($course as $item)
                                    <option value="{{ $item->id }}"
                                        {{ old('course') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-danger" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- modal update --}}
    <div class="modal fade" id="modal-course-update" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header mb-2 py-3" style="background: rgba(56, 42, 214, 0.9);">
                    <h5 class="modal-title mx-auto" style="color: rgb(246, 246, 246);" id="exampleModalLabel1">PELAJARAN
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="form-update" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body row py-0" id="div-update">
                        <div class="mb-1">
                            <label class="form-label" for="day_of_week-update">Hari</label>
                            <select id="day_of_week-update" name="day_of_week" class="select2 form-select"
                                aria-label="Default select example">
                                <option selected disabled value="monday">Pilih Hari</option>
                                <option value="Monday">Senin</option>
                                <option value="Tuesday">Selasa</option>
                                <option value="Wednesday">Rabu</option>
                                <option value="Thursday">Kamis</option>
                                <option value="Friday">Jumat</option>
                                <option value="Saturday">Sabtu</option>
                                <option value="Sunday">Minggu</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="start_time_schedule_id-update">Waktu Mulai</label>
                            <select id="start_time_schedule_id-update" name="start_time_schedule_id"
                                class="select2 form-select" aria-label="Default select example">
                                <option selected disabled>Pilih Waktu Mulai</option>
                                @foreach ($time_schedule as $item)
                                    <option value="{{ $item->id }}"
                                        {{ old('start_time_schedule_id') == $item->id ? 'selected' : '' }}>
                                        {{ Carbon\Carbon::parse($item->start_time_schedule)->format('H:i') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="end_time_schedule_id-update">Waktu Akhir</label>
                            <select id="end_time_schedule_id-update" name="end_time_schedule_id"
                                class="select2 form-select" aria-label="Default select example">
                                <option selected disabled>Pilih Waktu Akhir</option>
                                @foreach ($time_schedule as $item)
                                    <option value="{{ $item->id }}"
                                        {{ old('end_time_schedule_id') == $item->id ? 'selected' : '' }}>
                                        {{ Carbon\Carbon::parse($item->end_time_schedule)->format('H:i') }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label" for="teacher_id-update">Pengajar</label>
                            <select id="teacher_id-update" name="teacher_id" class="select2 form-select"
                                aria-label="Default select example">
                                <option selected disabled>Pilih Pengajar</option>
                                @foreach ($teacher as $item)
                                    <option value="{{ $item->id }}"
                                        {{ old('teacher') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-4 mb-2">
                            <label class="form-label" for="classroom_id-update">Kelas</label>
                            <select id="classroom_id-update" name="classroom_id" class="select2 form-select"
                                aria-label="Default select example">
                                <option selected disabled>Pilih Kelas</option>
                                @foreach ($classroom as $item)
                                    <option value="{{ $item->id }}"
                                        {{ old('classroom') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-4 mb-2">
                            <label class="form-label" for="course_id-update">Pelajaran</label>
                            <select id="course_id-update" name="course_id" class="select2 form-select"
                                aria-label="Default select example">
                                <option selected disabled>Pilih Pelajaran</option>
                                @foreach ($course as $item)
                                    <option value="{{ $item->id }}"
                                        {{ old('course') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-danger" data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        new DataTable('#table-content', {
            pagingType: 'simple_numbers'
        });
    </script>
    <script>
        $('.btn-update').click(function() {
            var id = $(this).data('id');
            var actionUrl = `schedule/${id}`;
            $('#form-update').attr('action', actionUrl);

            var day_of_week = $(this).data('day_of_week');
            var classroom = $(this).data('classroom');
            var teacher = $(this).data('teacher');
            var course = $(this).data('course');
            var start_time_schedule_id = $(this).data('start_time_schedule_id');
            var end_time_schedule_id = $(this).data('end_time_schedule_id');

            var formUpdate = $('#modal-course-update #div-update');

            formUpdate.find('#start_time_schedule_id-update option[value="' + start_time_schedule_id + '"]').prop('selected', true);
            formUpdate.find('#end_time_schedule_id-update option[value="' + end_time_schedule_id + '"]').prop('selected', true);
            formUpdate.find('#day_of_week-update option[value="' + day_of_week + '"]').prop('selected', true);
            formUpdate.find('#course_id-update option[value="' + course + '"]').prop('selected', true);
            formUpdate.find('#teacher_id-update option[value="' + teacher + '"]').prop('selected', true);
            formUpdate.find('#classroom_id-update option[value="' + classroom + '"]').prop('selected', true);
            $('#modal-course-update').modal('show');
        });

        $('.btn-delete').click(function() {
            id = $(this).data('id')
            var actionUrl = `schedule/${id}`;
            $('#form-delete').attr('action', actionUrl);
            $('#modal-delete').modal('show')
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
