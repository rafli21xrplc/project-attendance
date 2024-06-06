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
                <div>
                    <button type="button" class="btn btn-label-primary me-2" style="color: blue">
                        <i class="ti ti-printer me-1"></i> <span class="d-none d-sm-inline-block">Export</span>
                    </button>
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
                                        <th>EMAIL</th>
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
                                            <td>{{ $item->user->email }}</td>
                                            <td>{{ $item->gender }}</td>
                                            <td>{{ \Carbon\Carbon::parse($item->day_of_birth)->formatLocalized('%d %B %Y') }}
                                            </td>
                                            <td>{{ $item->telp }}</td>
                                            <td>
                                                <button data-name="{{ $item->name }}" data-gender="{{ $item->gender }}"
                                                    data-telp="{{ $item->telp }}" data-email="{{ $item->user->email }}"
                                                    data-classroom="{{ $item->classroom->name }}"
                                                    data-day_of_birth="{{ \Carbon\Carbon::parse($item->day_of_birth)->formatLocalized('%d %B %Y') }}"
                                                    class="btn btn-label-primary btn-view"><i
                                                        class="fa-solid fa-eye"></i></button>
                                                <button data-id="{{ $item->id }}" data-name="{{ $item->name }}"
                                                    data-gender="{{ $item->gender }}" data-telp="{{ $item->telp }}"
                                                    data-classroom_id="{{ $item->classroom_id }}"
                                                    data-day_of_birth="{{ $item->day_of_birth }}"
                                                    data-email="{{ $item->user->email }}" type="button"
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
                        <div class="col-12 col-md-4">
                            <label class="form-label" for="classroom_id">Kelas</label>
                            <select id="classroom_id" name="classroom_id" class="select2 form-select"
                                aria-label="Default select example">
                                <option selected disabled>Kelas</option>
                                @foreach ($class_room as $item)
                                    <option value="{{ $item->id }}"
                                        {{ old('classroom') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
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

    {{-- modal view --}}
    <div class="modal fade" id="modal-student-view" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header mb-2 py-3" style="background: rgba(56, 42, 214, 0.9);">
                    <h5 class="modal-title mx-auto" style="color: rgb(246, 246, 246);" id="exampleModalLabel1">BIODATA
                        SISWA</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body row py-0" id="div-view">
                    <div class="col-12 mb-1">
                        <label class="form-label" for="basic-default-email">Email</label>
                        <input type="email" class="form-control" id="basic-default-email" name="email-view"disabled
                            placeholder="your email" required value="{{ old('email') }}" />
                    </div>
                    <div class="col-12 col-md-6 mb-1">
                        <label class="form-label" for="basic-default-name">Name</label>
                        <input type="text" class="form-control" id="basic-default-name" name="name-view"disabled
                            placeholder="your name" required value="{{ old('name') }}" />
                    </div>
                    <div class="col-12 col-md-6 mb-1">
                        <label class="form-label" for="basic-default-status">Kelas</label>
                        <input type="text" class="form-control" id="basic-default-classroom"
                            name="classroom-view"disabled placeholder="PNS" required value="{{ old('classroom') }}" />
                    </div>
                    <div class="col-12 col-md-4 mb-1">
                        <label class="form-label" for="basic-default-status">Agama</label>
                        <input type="text" class="form-control" id="basic-default-agama" name="religion-view"disabled
                            placeholder="PNS" required value="{{ old('agama') }}" />
                    </div>
                    <div class="col-12 col-md-4 mb-1">
                        <label for="form-label-Telefon" class="form-label">Telefon</label>
                        <input class="form-control" type="number" id="form-label-Telefon" name="telp-view"disabled
                            placeholder="00392002911" value="{{ old('telp') }}" />
                    </div>
                    <div class="col-12 col-md-4 mb-1">
                        <label class="form-label" for="basic-default-status">Gender</label>
                        <input type="text" class="form-control" id="basic-default-gender" name="gender-view"disabled
                            placeholder="PNS" required value="{{ old('gender') }}" />
                    </div>
                    <div class="col-12 col-md-6 mb-1">
                        <label class="form-label" for="basic-default-born-at">Tempat Lahir</label>
                        <input type="text" class="form-control" id="basic-default-born-at"
                            name="born_at-view"disabled placeholder="Malang" required value="{{ old('born_at') }}" />
                    </div>
                    <div class="col-12 col-md-6 mb-1">
                        <label class="form-label" for="basic-default-day-of-birth">TANGGAL LAHIR</label>
                        <input type="text" class="form-control" id="basic-default-day-of-birth"
                            name="day_of_birth-view"disabled placeholder="Malang" required
                            value="{{ old('day_of_birth') }}" />
                    </div>
                    <div class="">
                        <label class="form-label" for="basic-default-bio">Alamat</label>
                        <textarea class="form-control" id="basic-default-bio" name="address-view" rows="3" required disabled></textarea>
                    </div>
                </div>
                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-danger" data-bs-dismiss="modal">Close</button>
                </div>
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
                            <label class="form-label" for="basic-default-email-update">Email</label>
                            <input type="email" class="form-control" id="basic-default-email-update" name="email"
                                placeholder="your email" required value="{{ old('email') }}" />
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
                        <div class="col-12 col-md-6 mb-2">
                            <label class="form-label" for="classroom_id-update">Kelas</label>
                            <select id="classroom_id-update" name="classroom_id" class="select2 form-select"
                                aria-label="Default select example">
                                <option selected disabled>Kelas</option>
                                @foreach ($class_room as $item)
                                    <option value="{{ $item->id }}"
                                        {{ old('classroom') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
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
        new DataTable('#table-content', {
            pagingType: 'simple_numbers'
        });
    </script>
    <script>
        $('.btn-view').click(function() {
            var name = $(this).data('name');
            var email = $(this).data('email');
            var gender = $(this).data('gender');
            var classroom = $(this).data('classroom');
            var day_of_birth = $(this).data('day_of_birth');
            var telp = $(this).data('telp');

            var formUpdate = $('#modal-student-view #div-view');

            formUpdate.find('input[name="email-view"]').val(email);
            formUpdate.find('input[name="name-view"]').val(name);
            formUpdate.find('input[name="gender-view"]').val(gender);
            formUpdate.find('input[name="classroom-view"]').val(classroom);
            formUpdate.find('input[name="day_of_birth-view"]').val(day_of_birth);
            formUpdate.find('input[name="telp-view"]').val(telp);

            $('#modal-student-view').modal('show');
        });

        $('.btn-update').click(function() {
            var id = $(this).data('id');
            var actionUrl = `student/${id}`;
            $('#form-update').attr('action', actionUrl);

            var email = $(this).data('email');
            var name = $(this).data('name');
            var gender = $(this).data('gender');
            var day_of_birth = $(this).data('day_of_birth');
            var telp = $(this).data('telp');
            var classroom_id = $(this).data('classroom_id');

            var formUpdate = $('#modal-student-update #div-update');

            formUpdate.find('#basic-default-email-update').val(email);
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
