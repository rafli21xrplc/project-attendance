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

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.4/css/jquery.dataTables.css">

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
            background-color: #ffffff;
        }

        #table-content tbody tr:nth-child(odd) {
            background-color: #ffffff;
        }

        #table-content tbody tr:hover {
            background-color: #e9e9e9;
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
            <div class="container py-2 px-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <h3>Biodata Siswa</h3>
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('admin.promoted_student') }}" class="btn btn-primary mb-2 mb-md-0">Promote All
                            Students</a>
                        <form id="importForm" action="{{ route('admin.student.import') }}" method="POST"
                            enctype="multipart/form-data" class="d-flex align-items-center mb-2 mb-md-0">
                            @csrf
                            <input type="file" name="file" id="fileInput" class="form-control d-none" required>
                            <button type="button" class="btn btn-label-primary me-2" id="importButton" style="color: blue">
                                <i class="ti ti-printer me-1"></i> <span class="d-none d-sm-inline-block">Import</span>
                            </button>
                        </form>
                        <button data-bs-toggle="modal" data-bs-target="#modal-cari" type="button"
                            class="btn btn-label-warning mb-2 mb-md-0">
                            <i class="ti ti-plus me-sm-1"></i> <span class="d-none d-sm-inline-block">Cari</span>
                        </button>
                        <button data-bs-toggle="modal" data-bs-target="#modal-student" type="button"
                            class="btn btn-label-success mb-2 mb-md-0">
                            <i class="ti ti-plus me-sm-1"></i> <span class="d-none d-sm-inline-block">Add New Record</span>
                        </button>
                    </div>
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
                                        <th>NAMA</th>
                                        <th>GENDER</th>
                                        <th>STATUS</th>
                                        <th>ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($student as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->student_name }}</td>
                                        <td>{{ $item->gender }}</td>
                                        <td>
                                            @if ($item->graduated)
                                                <span class="badge bg-success">Lulus</span>
                                            @else
                                                <span class="badge bg-primary">Siswa Aktif</span>
                                            @endif
                                        </td>

                                        <td>
                                            <button data-id="{{ $item->student_id }}"
                                                data-type_class_id="{{ $item->type_class_id }}"
                                                data-classroom_id="{{ $item->classroom_id }}"
                                                type="button"
                                                class="btn btn-label-primary btn-class"><i
                                                    class="fa-solid fa-circle-exclamation"></i></button>
                                            <button data-id="{{ $item->student_id }}"
                                                data-name="{{ $item->student_name }}"
                                                data-gender="{{ $item->gender }}" data-telp="{{ $item->telp }}"
                                                data-username="{{ $item->username }}"
                                                data-classroom_id="{{ $item->classroom_id }}"
                                                data-day_of_birth="{{ $item->day_of_birth }}" type="button"
                                                class="btn btn-label-warning btn-update"><i
                                                    class="fa-solid fa-pen"></i></button>
                                            <button data-id="{{ $item->student_id }}" type="button"
                                                class="btn btn-label-danger btn-delete"><i
                                                    class="fa-solid fa-trash"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="content-backdrop fade"></div>
    </div>


    <div class="modal fade" id="modal-class-student" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title mx-auto my-1" id="exampleModalLabel1">NAIK KELAS</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.promoted_student.update') }}" method="POST">
                    @csrf
                    <div class="modal-body row py-0" id="div-update">
                        <input type="hidden" id="classroom_id" name="classroom_id">
                        <input type="hidden" id="student_id" name="student_id">
                        @foreach ($type_class as $item)
                            <div class="col-md mb-md-0 mb-3">
                                <div class="form-check custom-option custom-option-icon checked">
                                    <label class="form-check-label custom-option-content" for="{{ $item->id }}">
                                        <span class="custom-option-body">
                                            <i class="ti ti-crown" style="font-size: 30px;"></i> <!-- Example icon -->
                                            <span class="custom-option-title">{{ $item->category }}</span>
                                        </span>
                                        <input name="type_class_id" class="form-check-input type-class-radio"
                                            type="radio" value="{{ $item->id }}" id="{{ $item->id }}">
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-danger" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

        {{-- modal --}}
        <div class="modal fade" id="modal-cari" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title mx-auto my-1" id="exampleModalLabel1">Pencarian</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.student.search') }}" method="GET">
                        <div class="modal-body row py-0">
                            <div class="col-12 col-md-12 mb-2">
                                <label class="form-label" for="classroom_id">Kelas</label>
                                <select id="classroom_id" name="classroom_id" class="select2 form-select"
                                    aria-label="Default select example">
                                    <option selected disabled>Kelas</option>
                                    @foreach ($class_room as $item)
                                        <option value="{{ $item->id }}"
                                            {{ old('classroom') == $item->id ? 'selected' : '' }}>
                                            {{ $item->typeClass->category }} {{ $item->name }}</option>
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
    <div class="modal fade" id="modal-student-update" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header mb-2 py-3" style="background: rgba(56, 42, 214, 0.9);">
                    <h5 class="modal-title mx-auto" style="color: rgb(246, 246, 246);" id="exampleModalLabel1">BIODATA
                        SISWA</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="form-update" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body row py-0" id="div-update">
                        <div class="col-12 col-md-6 mb-2">
                            <label class="form-label" for="basic-default-username-update">username</label>
                            <input type="text" class="form-control" id="basic-default-username-update"
                                name="username" placeholder="your username" required value="{{ old('username') }}" />
                        </div>
                        <div class="col-12 col-md-6 mb-2">
                            <label class="form-label" for="basic-default-password-update">Password Baru</label>
                            <input type="password" class="form-control" id="basic-default-password-update"
                                name="password" placeholder="" value="{{ old('password') }}" />
                            <small class="text-warning text-muted">jika tidak ingin memperbarui password maka
                                kosongkan</small>
                        </div>
                        <div class="col-12 col-md-6 mb-2">
                            <label class="form-label" for="basic-default-name-update">Name</label>
                            <input type="text" class="form-control" id="basic-default-name-update" name="name"
                                placeholder="your name" required value="{{ old('name') }}" />
                        </div>
                        <div class="col-12 col-md-6 mb-2">
                            <label for="html5-date-input-update" class="form-label">Tanggal Lahir</label>
                            <input class="form-control" type="date" value="2021-06-18" id="html5-date-input-update"
                                name="day_of_birth" value="{{ old('day_of_birth') }}" />
                        </div>
                        <div class="col-12 col-md-4 mb-2">
                            <label class="form-label" for="classroom_id-update">Kelas</label>
                            <select id="classroom_id-update" name="classroom_id" class="select2 form-select"
                                aria-label="Default select example">
                                <option selected disabled>Kelas</option>
                                @foreach ($class_room as $item)
                                    <option value="{{ $item->id }}"
                                        {{ old('classroom') == $item->id ? 'selected' : '' }}>
                                        {{ $item->typeClass->category }} {{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-4 mb-2">
                            <label for="form-label-Telefon-update" class="form-label">Telefon</label>
                            <input class="form-control" type="number" id="form-label-Telefon-update" name="telp"
                                placeholder="00392002911" value="{{ old('telp') }}" />
                        </div>
                        <div class="col-12 col-md-4 mb-2">
                            <label class="form-label">Gender</label>
                            <div class="form-check">
                                <input type="radio" id="basic-default-radio-laki-laki-update" name="gender"
                                    value="L" class="form-check-input" required
                                    {{ old('gender') == 'L' ? 'checked' : '' }} />
                                <label class="form-check-label"
                                    for="basic-default-radio-laki-laki-update">laki-laki</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" id="basic-default-radio-perempuan-update" name="gender"
                                    value="P" class="form-check-input" required
                                    {{ old('gender') == 'P' ? 'checked' : '' }} />
                                <label class="form-check-label"
                                    for="basic-default-radio-perempuan-update">perempuan</label>
                            </div>
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.js"></script>

    <!-- Inisialisasi DataTable -->
    <script>
        $(document).ready(function() {
            $('#table-content').DataTable();
        });
    </script>
    <script>
        document.getElementById('importButton').addEventListener('click', function() {
            document.getElementById('fileInput').click();
        });

        document.getElementById('fileInput').addEventListener('change', function() {
            if (this.files.length > 0) {
                document.getElementById('importForm').submit();
            }
        });
    </script>
    <script>
        $('.btn-class').click(function() {
            var id = $(this).data('id');
            var typeClassId = $(this).data('type_class_id');
            var classroomId = $(this).data('classroom_id');

            $('#modal-class-student').modal('show');
            var formUpdate = $('#modal-class-student #div-update');

            // Uncheck all radio buttons first
            $('.type-class-radio').prop('checked', false);

            // Check the radio button that matches the typeClassId
            formUpdate.find('#classroom_id').val(classroomId);
            formUpdate.find('#student_id').val(id);
            $('#modal-class-student').find(`input[type=radio][value=${typeClassId}]`).prop('checked', true);
        });


        $('.btn-update').click(function() {
            var id = $(this).data('id');
            var actionUrl = `student/${id}`;
            $('#form-update').attr('action', actionUrl);

            var username = $(this).data('username');
            var name = $(this).data('name');
            var gender = $(this).data('gender');
            var day_of_birth = $(this).data('day_of_birth');
            var telp = $(this).data('telp');
            var classroom_id = $(this).data('classroom_id');

            var formUpdate = $('#modal-student-update #div-update');

            formUpdate.find('#basic-default-username-update').val(username);
            formUpdate.find('#basic-default-name-update').val(name);
            formUpdate.find('#html5-date-input-update').val(day_of_birth);
            formUpdate.find('#form-label-Telefon-update').val(telp);
            formUpdate.find('#classroom_id-update option[value="' + classroom_id + '"]').prop('selected', true);
            if (gender === 'L') {
                $('#basic-default-radio-laki-laki-update').prop('checked', true);
            } else if (gender === 'P') {
                $('#basic-default-radio-perempuan-update').prop('checked', true);
            }

            $('#modal-student-update').modal('show');
        });

        $('.btn-delete').click(function() {
            id = $(this).data('id')
            var actionUrl = `student/${id}`;
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
