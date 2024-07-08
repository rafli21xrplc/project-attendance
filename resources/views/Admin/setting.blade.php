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

            <div class="row">
                <div class="col-12">
                    <div class="card mb-4">
                        <h5 class="card-header">Setting</h5>
                        <div class="card-body">
                            <form action="{{ route('admin.setting.update') }}" method="post">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="first-holiday" class="form-label">Libur mingguan pertama</label>
                                        <select class="form-select" id="first-holiday" name="first-holiday">
                                            <option disabled {{ !isset($setting['first-holiday']) ? 'selected' : '' }}>Pilih Hari
                                            </option>
                                            <option value="none" {{ ($setting['first-holiday'] ?? '') == 'none' ? 'selected' : '' }}>
                                                TIDAK ADA</option>
                                            <option value="Monday" {{ ($setting['first-holiday'] ?? '') == 'Monday' ? 'selected' : '' }}>
                                                Senin</option>
                                            <option value="Tuesday" {{ ($setting['first-holiday'] ?? '') == 'Tuesday' ? 'selected' : '' }}>
                                                Selasa</option>
                                            <option value="Wednesday" {{ ($setting['first-holiday'] ?? '') == 'Wednesday' ? 'selected' : '' }}>
                                                Rabu</option>
                                            <option value="Thursday" {{ ($setting['first-holiday'] ?? '') == 'Thursday' ? 'selected' : '' }}>
                                                Kamis</option>
                                            <option value="Friday" {{ ($setting['first-holiday'] ?? '') == 'Friday' ? 'selected' : '' }}>
                                                Jumat</option>
                                            <option value="Saturday" {{ ($setting['first-holiday'] ?? '') == 'Saturday' ? 'selected' : '' }}>
                                                Sabtu</option>
                                            <option value="Sunday" {{ ($setting['first-holiday'] ?? '') == 'Sunday' ? 'selected' : '' }}>
                                                Minggu</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="second-holiday" class="form-label">Libur mingguan kedua</label>
                                        <select class="form-select" id="second-holiday" name="second-holiday">
                                            <option disabled {{ !isset($setting['second-holiday']) ? 'selected' : '' }}>Pilih Hari
                                            </option>
                                            <option value="none" {{ ($setting['second-holiday'] ?? '') == 'none' ? 'selected' : '' }}>
                                                TIDAK ADA</option>
                                            <option value="Monday" {{ ($setting['second-holiday'] ?? '') == 'Monday' ? 'selected' : '' }}>
                                                Senin</option>
                                            <option value="Tuesday" {{ ($setting['second-holiday'] ?? '') == 'Tuesday' ? 'selected' : '' }}>
                                                Selasa</option>
                                            <option value="Wednesday" {{ ($setting['second-holiday'] ?? '') == 'Wednesday' ? 'selected' : '' }}>
                                                Rabu</option>
                                            <option value="Thursday" {{ ($setting['second-holiday'] ?? '') == 'Thursday' ? 'selected' : '' }}>
                                                Kamis</option>
                                            <option value="Friday" {{ ($setting['second-holiday'] ?? '') == 'Friday' ? 'selected' : '' }}>
                                                Jumat</option>
                                            <option value="Saturday" {{ ($setting['second-holiday'] ?? '') == 'Saturday' ? 'selected' : '' }}>
                                                Sabtu</option>
                                            <option value="Sunday" {{ ($setting['second-holiday'] ?? '') == 'Sunday' ? 'selected' : '' }}>
                                                Minggu</option>
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-12 mb-3">
                                        <label for="examp_date" class="form-label">Tanggal Pengambilan Kartu Ujian</label>
                                        <input class="form-control" type="date" value="{{ old('examp_date', '2021-06-18') }}" id="examp_date" name="examp_date" />
                                    </div>
                                    <div class="col-12 text-end">
                                        <button type="submit" class="btn btn-primary">Save changes</button>
                                    </div>
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
