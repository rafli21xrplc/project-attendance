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

            <div class="row mb-4">
                <div class="col-12 order-5">
                    <div class="card">
                        <div class="card-header">
                            <h4>Manage Student Promotions</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-3">
                                    <a href="{{ route('admin.promoted_student') }}" class="btn btn-primary">Promote Students</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 order-5">
                    <div class="col-12 order-5">
                        <form action="{{ route('admin.setting.update') }}" method="post">
                            @csrf
                            @method('PUT')
                            <div class="card mb-4">
                                <h5 class="card-header">Setting</h5>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="start-date" class="form-label">Tanggal Awal Masa KBM</label>
                                            <input type="date" class="form-control" id="start-date" name="start-date"
                                                value="{{ $setting['start-date'] ?? '' }}" />
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="end-date" class="form-label">Tanggal Akhir Masa KBM</label>
                                            <input type="date" class="form-control" id="end-date" name="end-date"
                                                value="{{ $setting['end-date'] ?? '' }}" />
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="first-holiday" class="form-label">Libur mingguan pertama</label>
                                            <select class="form-select" id="first-holiday" name="first-holiday">
                                                <option disabled {{ !isset($setting['first-holiday']) ? 'selected' : '' }}>
                                                    Pilih Hari</option>
                                                <option value="none"
                                                    {{ ($setting['first-holiday'] ?? '') == 'none' ? 'selected' : '' }}>
                                                    TIDAK ADA</option>
                                                <option value="Monday"
                                                    {{ ($setting['first-holiday'] ?? '') == 'Monday' ? 'selected' : '' }}>
                                                    Senin</option>
                                                <option value="Tuesday"
                                                    {{ ($setting['first-holiday'] ?? '') == 'Tuesday' ? 'selected' : '' }}>
                                                    Selasa</option>
                                                <option value="Wednesday"
                                                    {{ ($setting['first-holiday'] ?? '') == 'Wednesday' ? 'selected' : '' }}>
                                                    Rabu</option>
                                                <option value="Thursday"
                                                    {{ ($setting['first-holiday'] ?? '') == 'Thursday' ? 'selected' : '' }}>
                                                    Kamis</option>
                                                <option value="Friday"
                                                    {{ ($setting['first-holiday'] ?? '') == 'Friday' ? 'selected' : '' }}>
                                                    Jumat</option>
                                                <option value="Saturday"
                                                    {{ ($setting['first-holiday'] ?? '') == 'Saturday' ? 'selected' : '' }}>
                                                    Sabtu</option>
                                                <option value="Sunday"
                                                    {{ ($setting['first-holiday'] ?? '') == 'Sunday' ? 'selected' : '' }}>
                                                    Minggu</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="second-holiday" class="form-label">Libur mingguan kedua</label>
                                            <select class="form-select" id="second-holiday" name="second-holiday">
                                                <option disabled
                                                    {{ !isset($setting['second-holiday']) ? 'selected' : '' }}>Pilih Hari
                                                </option>
                                                <option value="none"
                                                    {{ ($setting['second-holiday'] ?? '') == 'none' ? 'selected' : '' }}>
                                                    TIDAK ADA</option>
                                                <option value="Monday"
                                                    {{ ($setting['second-holiday'] ?? '') == 'Monday' ? 'selected' : '' }}>
                                                    Senin</option>
                                                <option value="Tuesday"
                                                    {{ ($setting['second-holiday'] ?? '') == 'Tuesday' ? 'selected' : '' }}>
                                                    Selasa</option>
                                                <option value="Wednesday"
                                                    {{ ($setting['second-holiday'] ?? '') == 'Wednesday' ? 'selected' : '' }}>
                                                    Rabu</option>
                                                <option value="Thursday"
                                                    {{ ($setting['second-holiday'] ?? '') == 'Thursday' ? 'selected' : '' }}>
                                                    Kamis</option>
                                                <option value="Friday"
                                                    {{ ($setting['second-holiday'] ?? '') == 'Friday' ? 'selected' : '' }}>
                                                    Jumat</option>
                                                <option value="Saturday"
                                                    {{ ($setting['second-holiday'] ?? '') == 'Saturday' ? 'selected' : '' }}>
                                                    Sabtu</option>
                                                <option value="Sunday"
                                                    {{ ($setting['second-holiday'] ?? '') == 'Sunday' ? 'selected' : '' }}>
                                                    Minggu</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary">Save changes</button>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>

            <div class="content-backdrop fade"></div>
        </div>

        {{-- modal --}}
        <div class="modal fade" id="modal-teacher" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title mx-auto my-1" id="exampleModalLabel1">BIODATA GURU</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.teacher.store') }}" method="POST">
                        @csrf
                        <div class="modal-body row py-0">
                            <div class="col-12 col-md-6 mb-1">
                                <label class="form-label" for="basic-default-NIP">NIP</label>
                                <input type="text" class="form-control" id="basic-default-NIP" name="nip"
                                    placeholder="0029100292101" required value="{{ old('nip') }}" />
                            </div>
                            <div class="col-12 col-md-6 mb-1">
                                <label class="form-label" for="basic-default-NUPTK">NUPTK</label>
                                <input type="text" class="form-control" id="basic-default-NUPTK" name="nuptk"
                                    placeholder="2022911119921" required value="{{ old('nuptk') }}" />
                            </div>
                            <div class="col-12 col-md-6 mb-1">
                                <label class="form-label" for="basic-default-email">email</label>
                                <input type="email" class="form-control" id="basic-default-email" name="email"
                                    placeholder="email@email.com" required value="{{ old('email') }}" />
                            </div>
                            <div class="col-12 col-md-6 mb-1">
                                <label class="form-label" for="basic-default-password">password</label>
                                <input type="password" class="form-control" id="basic-default-password" name="password"
                                    placeholder="password" required value="{{ old('password') }}" />
                            </div>
                            <div class="col-4 col-md-4 mb-1">
                                <label class="form-label" for="basic-default-name">Name</label>
                                <input type="text" class="form-control" id="basic-default-name" name="name"
                                    placeholder="your name" required value="{{ old('name') }}" />
                            </div>
                            {{-- <div class="col-12 col-md-4">
                            <label class="form-label" for="religion_id">Agama</label>
                            <select id="religion_id" name="religion_id" class="select2 form-select"
                                aria-label="Default select example">
                                <option selected disabled>Agama</option>
                                @foreach ($religi as $item)
                                    <option value="{{ $item->id }}"
                                        {{ old('religi') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div> --}}

                            <div class="col-4 col-md-4 mb-2">
                                <label for="form-label-Telefon" class="form-label">Telefon</label>
                                <input class="form-control" type="number" id="form-label-Telefon" name="telp"
                                    placeholder="00392002911" value="{{ old('telp') }}" />
                            </div>
                            <div class="col-4 col-md-4 mb-2">
                                <label class="form-label">Gender</label>
                                <div class="form-check">
                                    <input type="radio" id="basic-default-radio-laki-laki" name="gender"
                                        value="L" class="form-check-input" required
                                        {{ old('gender') == 'L' ? 'checked' : '' }} />
                                    <label class="form-check-label" for="basic-default-radio-laki-laki">laki-laki</label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" id="basic-default-radio-perempuan" name="gender"
                                        value="P" class="form-check-input" required
                                        {{ old('gender') == 'P' ? 'checked' : '' }} />
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
        <div class="modal fade" id="modal-teacher-view" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header mb-2 py-3" style="background: rgba(56, 42, 214, 0.9);">
                        <h5 class="modal-title mx-auto" style="color: rgb(246, 246, 246);" id="exampleModalLabel1">
                            BIODATA
                            GURU</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body row py-0" id="div-view">
                        <div class="col-12 col-md-6 mb-1">
                            <label class="form-label" for="basic-default-NIP">NIP</label>
                            <input type="text" class="form-control" id="basic-default-NIP" name="nip-view" disabled
                                placeholder="0029100292101" required value="{{ old('nip') }}" />
                        </div>
                        <div class="col-12 col-md-6 mb-1">
                            <label class="form-label" for="basic-default-NUPTK">NUPTK</label>
                            <input type="text" class="form-control" id="basic-default-NUPTK"
                                name="nuptk-view"disabled placeholder="2022911119921" required
                                value="{{ old('nuptk') }}" />
                        </div>
                        <div class="mb-1">
                            <label class="form-label" for="basic-default-email">Email</label>
                            <input type="text" class="form-control" id="basic-default-email" name="email-view"
                                disabled placeholder="your email" required value="{{ old('email') }}" />
                        </div>
                        <div class="col-4 col-md-4 mb-1">
                            <label class="form-label" for="basic-default-name">Name</label>
                            <input type="text" class="form-control" id="basic-default-name" name="name-view"disabled
                                placeholder="your name" required value="{{ old('name') }}" />
                        </div>
                        <div class="col-4 col-md-4 mb-1">
                            <label for="form-label-Telefon" class="form-label">Telefon</label>
                            <input class="form-control" type="number" id="form-label-Telefon" name="telp-view"disabled
                                placeholder="00392002911" value="{{ old('telp') }}" />
                        </div>
                        <div class="col-4 col-md-4 mb-1">
                            <label class="form-label" for="basic-default-status">Gender</label>
                            <input type="text" class="form-control" id="basic-default-gender"
                                name="gender-view"disabled placeholder="PNS" required value="{{ old('gender') }}" />
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
        <div class="modal fade" id="modal-teacher-update" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header mb-2 py-3" style="background: rgba(56, 42, 214, 0.9);">
                        <h5 class="modal-title mx-auto" style="color: rgb(246, 246, 246);" id="exampleModalLabel1">
                            BIODATA
                            GURU</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="form-update" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body row py-0" id="div-update">
                            <div class="col-12 col-md-6 mb-2">
                                <label class="form-label" for="basic-default-email-update">Email</label>
                                <input type="email" class="form-control" id="basic-default-email-update"
                                    name="email" placeholder="your email" required value="{{ old('email') }}" />
                            </div>
                            <div class="col-12 col-md-6 mb-2">
                                <label class="form-label" for="basic-default-password-update">Password Baru</label>
                                <input type="password" class="form-control" id="basic-default-password-update"
                                    name="password" placeholder="" value="{{ old('password') }}" />
                                <small class="text-warning text-muted">jika tidak ingin memperbarui password maka
                                    kosongkan</small>
                            </div>
                            <div class="col-12 col-md-6 mb-1">
                                <label class="form-label" for="basic-default-NIP-update">NIP</label>
                                <input type="text" class="form-control" id="basic-default-NIP-update" name="nip"
                                    placeholder="0029100292101" required value="{{ old('nip') }}" />
                            </div>
                            <div class="col-12 col-md-6 mb-1">
                                <label class="form-label" for="basic-default-NUPTK-update">NUPTK</label>
                                <input type="text" class="form-control" id="basic-default-NUPTK-update"
                                    name="nuptk" placeholder="2022911119921" required value="{{ old('nuptk') }}" />
                            </div>
                            <div class="col-4 col-md-4 mb-1">
                                <label class="form-label" for="basic-default-name-update">Name</label>
                                <input type="text" class="form-control" id="basic-default-name-update" name="name"
                                    placeholder="your name" required value="{{ old('name') }}" />
                            </div>
                            <div class="col-4 col-md-4 mb-1">
                                <label for="form-label-Telefon-update" class="form-label">Telefon</label>
                                <input class="form-control" type="number" id="form-label-Telefon-update" name="telp"
                                    placeholder="00392002911" value="{{ old('telp') }}" />
                            </div>
                            <div class="col-4 col-md-4 mb-2">
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
                var nip = $(this).data('nip');
                var email = $(this).data('email');
                var nuptk = $(this).data('nuptk');
                var name = $(this).data('name');
                var gender = $(this).data('gender');
                var telp = $(this).data('telp');

                var formUpdate = $('#modal-teacher-view #div-view');

                formUpdate.find('input[name="nip-view"]').val(nip);
                formUpdate.find('input[name="email-view"]').val(email);
                formUpdate.find('input[name="nuptk-view"]').val(nuptk);
                formUpdate.find('input[name="name-view"]').val(name);
                formUpdate.find('input[name="gender-view"]').val(gender);
                formUpdate.find('input[name="telp-view"]').val(telp);

                $('#modal-teacher-view').modal('show');
            });

            $('.btn-update').click(function() {
                var id = $(this).data('id');
                var actionUrl = `teacher/${id}`;
                $('#form-update').attr('action', actionUrl);

                var email = $(this).data('email');
                var nip = $(this).data('nip');
                var nuptk = $(this).data('nuptk');
                var name = $(this).data('name');
                var gender = $(this).data('gender');
                var telp = $(this).data('telp');

                var formUpdate = $('#modal-teacher-update #div-update');

                console.log(gender);

                formUpdate.find('#basic-default-email-update').val(email);
                formUpdate.find('#basic-default-NIP-update').val(nip);
                formUpdate.find('#basic-default-NUPTK-update').val(nuptk);
                formUpdate.find('#basic-default-name-update').val(name);
                formUpdate.find('#form-label-Telefon-update').val(telp);
                if (gender === 'L') {
                    $('#basic-default-radio-laki-laki-update').prop('checked', true);
                } else if (gender === 'P') {
                    $('#basic-default-radio-perempuan-update').prop('checked', true);
                }

                $('#modal-teacher-update').modal('show');
            });

            $('.btn-delete').click(function() {
                id = $(this).data('id')
                var actionUrl = `teacher/${id}`;
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
