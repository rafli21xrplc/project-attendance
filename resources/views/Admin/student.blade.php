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
                    <h3>Biodata Siswa</h3>
                </div>
                <div style="display: flex; align-items: center;">
                    <form id="importForm" action="{{ route('admin.student.import') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="form-group d-none">
                            <input type="file" name="file" id="fileInput" class="form-control" required>
                        </div>
                        <button type="button" class="btn btn-label-primary me-2" id="importButton" style="color: blue">
                            <i class="ti ti-printer me-1"></i> <span class="d-none d-sm-inline-block">Import</span>
                        </button>
                    </form>
                    <button data-bs-toggle="modal" data-bs-target="#modal-student" type="button"
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
                                        <th>NAMA</th>
                                        <th>GENDER</th>
                                        <th>TANGGAL LAHIR</th>
                                        <th>TELP</th>
                                        <th>ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($student as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $item->name }}</td>
                                            <td>{{ $item->gender }}</td>
                                            <td>{{ \Carbon\Carbon::parse($item->day_of_birth)->formatLocalized('%d %B %Y') }}
                                            </td>
                                            <td>{{ $item->telp }}</td>
                                            <td>
                                                <button data-id="{{ $item->id }}" data-name="{{ $item->name }}"
                                                    data-gender="{{ $item->gender }}" data-telp="{{ $item->telp }}" data-username="{{ $item->user->username }}"
                                                    data-classroom_id="{{ $item->classroom_id }}"
                                                    data-day_of_birth="{{ $item->day_of_birth }}" type="button"
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
                </div>

            </div>
        </div>

        <div class="content-backdrop fade"></div>
    </div>

    {{-- modal --}}
    <div class="modal fade" id="modal-student" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title mx-auto my-1" id="exampleModalLabel1">BIODATA SISWA</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.student.store') }}" method="POST">
                    @csrf
                    <div class="modal-body row py-0">
                        <div class="col-12 col-md-6 mb-1">
                            <label class="form-label" for="basic-default-email">email</label>
                            <input type="email" class="form-control" id="basic-default-email" name="email"
                                placeholder="email@gmail.com" required value="{{ old('email') }}" />
                        </div>
                        <div class="col-12 col-md-6 mb-1">
                            <label class="form-label" for="basic-default-password">password</label>
                            <input type="password" class="form-control" id="basic-default-password" name="password"
                                placeholder="password" required value="{{ old('password') }}" />
                        </div>
                        <div class="col-12 col-md-6 mb-1">
                            <label class="form-label" for="basic-default-name">Name</label>
                            <input type="text" class="form-control" id="basic-default-name" name="name"
                                placeholder="your name" required value="{{ old('name') }}" />
                        </div>
                        <div class="col-12 col-md-6 mb-1">
                            <label for="html5-date-input" class="form-label">Tanggal Lahir</label>
                            <input class="form-control" type="date" value="2021-06-18" id="html5-date-input"
                                name="day_of_birth" value="{{ old('day_of_birth') }}" />
                        </div>
                        <div class="col-12 col-md-4 mb-2">
                            <label class="form-label" for="classroom_id">Kelas</label>
                            <select id="classroom_id" name="classroom_id" class="select2 form-select"
                                aria-label="Default select example">
                                <option selected disabled>Kelas</option>
                                @foreach ($class_room as $item)
                                    <option value="{{ $item->id }}"
                                        {{ old('classroom') == $item->id ? 'selected' : '' }}>{{ $item->typeClass->category }} {{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-4 mb-2">
                            <label for="form-label-Telefon" class="form-label">Telefon</label>
                            <input class="form-control" type="number" id="form-label-Telefon" name="telp"
                                placeholder="00392002911" value="{{ old('telp') }}" />
                        </div>
                        <div class="col-12 col-md-4 mb-2">
                            <label class="form-label">Gender</label>
                            <div class="form-check">
                                <input type="radio" id="basic-default-radio-laki-laki" name="gender" value="L"
                                    class="form-check-input" required {{ old('gender') == 'L' ? 'checked' : '' }} />
                                <label class="form-check-label" for="basic-default-radio-laki-laki">laki-laki</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" id="basic-default-radio-perempuan" name="gender" value="P"
                                    class="form-check-input" required {{ old('gender') == 'P' ? 'checked' : '' }} />
                                <label class="form-check-label" for="basic-default-radio-perempuan">perempuan</label>
                            </div>
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
                            <input type="text" class="form-control" id="basic-default-username-update" name="username"
                                placeholder="your username" required value="{{ old('username') }}" />
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
                                        {{ old('classroom') == $item->id ? 'selected' : '' }}>{{ $item->typeClass->category }} {{ $item->name }}</option>
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
        new DataTable('#table-content', {
            pagingType: 'simple_numbers'
        });
    </script>
    <script>

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
