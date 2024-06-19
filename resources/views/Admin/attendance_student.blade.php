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
        <div class="container-xl flex-grow-1 container-p-y">
            <div class="container">
                <div class="row justify-content-end">
                    <div class="col-md-6">
                        <form action="{{ route('admin.attendance_student.search') }}" method="POST" class="d-flex gap-3">
                            @csrf
                            <div class="flex-grow-1">
                                <input class="form-control" type="date" value="2021-06-18" id="html5-date-input"
                                    name="range-date" />
                            </div>
                            <div class="flex-grow-1">
                                <select class="form-select" name="classroom_id">
                                    <option value="12 RPL C" disabled selected>Pilih kelas</option>
                                    @foreach ($classroom as $item)
                                        <option value="{{ $item->id }}">{{ $item->typeClass->category }}
                                            {{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <button type="submit" class="btn btn-label-success">
                                    <span class="d-none d-sm-inline-block">Search</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="row my-4">
                @if ($schedules !== null)
                    <div class="col-12 order-5">
                        <div class="py-3">
                            <div class="container">
                                <div class="row">
                                    <div class="col-12">
                                        <ul class="nav nav-pills flex-column flex-sm-row mb-4">
                                            @foreach ($schedules as $index => $item)
                                                @if ($item)
                                                    <li class="nav-item mx-1">
                                                        <button class="nav-link {{ $index == 0 ? 'active' : '' }}"
                                                            data-bs-toggle="tab"
                                                            data-bs-target="#form-tabs-{{ $item->id }}" role="tab"
                                                            aria-selected="{{ $index == 0 ? 'true' : 'false' }}">
                                                            {{ $item->course->name }}
                                                        </button>
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 card">
                                        @forelse ($schedules as $index => $item)
                                            <div class="tab-content p-3">
                                                @if ($item)
                                                    <div class="tab-pane fade {{ $index == 0 ? 'active show' : '' }}"
                                                        id="form-tabs-{{ $item->id }}" role="tabpanel">
                                                        <div class="row g-4">
                                                            <form id="attendanceForm-{{ $item->id }}"
                                                                action="{{ route('admin.attendance_student.update', $item->id) }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('PUT')
                                                                <div class="d-flex justify-content-end mb-4">
                                                                    <button id="saveButton" type="submit"
                                                                        class="btn btn-success">
                                                                        <span class="d-none d-sm-inline-block">Simpan</span>
                                                                    </button>
                                                                </div>
                                                                <table class="table table-custom">
                                                                    <thead>
                                                                        <tr>
                                                                            <th class="text-center">STUDENT ID</th>
                                                                            <th>NAMA</th>
                                                                            <th class="text-center">PRESENSI</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach ($item->classroom->students as $student)
                                                                            <tr>
                                                                                <td class="text-center">
                                                                                    {{ $student->student_id }}</td>
                                                                                <td>{{ $student->name }}</td>
                                                                                <td>
                                                                                    <div
                                                                                        class="d-flex justify-content-center align-items-center gap-2">
                                                                                        @foreach (['present' => 'Hadir', 'alpha' => 'Alpha', 'permission' => 'Izin', 'sick' => 'Sakit'] as $value => $label)
                                                                                            <div
                                                                                                class="form-check form-check-inline">
                                                                                                <input
                                                                                                    name="attendance[{{ $student->id }}]"
                                                                                                    class="form-check-input"
                                                                                                    type="radio"
                                                                                                    value="{{ $value }}"
                                                                                                    id="{{ $value }}_{{ $student->id }}_{{ $item->id }}"
                                                                                                    @if (isset($attendanceBySchedule[$item->id][$student->id]['attendance_status']) &&
                                                                                                            in_array($value, $attendanceBySchedule[$item->id][$student->id]['attendance_status'])) checked @endif
                                                                                                    {{ $loop->first ? 'required' : '' }} />
                                                                                                <label
                                                                                                    class="form-check-label"
                                                                                                    for="{{ $value }}_{{ $student->id }}_{{ $item->id }}">{{ $label }}</label>
                                                                                            </div>
                                                                                        @endforeach
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>

                                                            </form>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="d-flex justify-content-center align-items-center my-5">
                                                        <img src="{{ asset('assets/content/empty.svg') }}" width="300"
                                                            alt="No Data Available">
                                                    </div>
                                                @endif
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
                @else
                    <div class="d-flex justify-content-center align-items-center my-5">
                        <img src="{{ asset('assets/content/empty.svg') }}" width="300" alt="No Data Available">
                    </div>
                @endif


                <div class="offcanvas offcanvas-end" id="add-new-record">
                    <div class="offcanvas-header border-bottom">
                        <h5 class="offcanvas-title" id="exampleModalLabel">New Record</h5>
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
                                    <span id="basicPost2" class="input-group-text"><i class="ti ti-briefcase"></i></span>
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
                                <div class="form-text">You can use letters, numbers & periods</div>
                            </div>
                            <div class="col-sm-12">
                                <label class="form-label" for="basicDate">Joining Date</label>
                                <div class="input-group input-group-merge">
                                    <span id="basicDate2" class="input-group-text"><i class="ti ti-calendar"></i></span>
                                    <input type="text" class="form-control dt-date" id="basicDate" name="basicDate"
                                        aria-describedby="basicDate2" placeholder="MM/DD/YYYY" aria-label="MM/DD/YYYY" />
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
                                <button type="submit" class="btn btn-danger data-submit me-sm-3 me-1">Submit</button>
                                <button type="reset" class="btn btn-outline-secondary"
                                    data-bs-dismiss="offcanvas">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>


            </div>
        </div>

        <div class="content-backdrop fade"></div>
    </div>
@endsection

@section('js')
    {{-- <script>
    document.getElementById('saveButton').addEventListener('click', function() {
        var form = document.getElementById('attendanceForm');
        var allChecked = true;
        var students = @json($attendance->schedule->classroom->students);

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
</script> --}}

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
