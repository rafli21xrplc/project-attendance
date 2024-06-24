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
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Data Izin dan Sakit Siswa</h4>
                        </div>
                        @empty(!$permissions->count())
                            <div class="card-body">
                                <div class="table-responsive text-nowrap">
                                    <table class="table table-bordered" id="table-content">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>Nama Siswa</th>
                                                <th>Kelas</th>
                                                <th>Tanggal Izin/Sakit</th>
                                                <th>Keterangan</th>
                                                <th>File</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($permissions as $permission)
                                                <tr>
                                                    <td>{{ $permission->student->name }}</td>
                                                    <td>{{ $permission->student->classroom->typeClass->category }}
                                                        {{ $permission->student->classroom->name }}</td>
                                                    <td>{{ $permission->created_at->format('d M Y') }}</td>
                                                    <td class="text-wrap">{{ $permission->description }}</td>
                                                    <td>
                                                        @if ($permission->file)
                                                            <a href="{{ Storage::url($permission->file) }}"
                                                                target="_blank">Lihat File</a>
                                                        @else
                                                            Tidak ada file
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center">Tidak ada data izin atau sakit siswa
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                {{ $permissions->links() }}
                            </div>
                        @else
                            <div class="d-flex justify-content-center align-items-center my-5">
                                <img src="{{ asset('assets/content/empty.svg') }}" width="300" alt="No Data Available">
                            </div>
                        @endempty

                    </div>
                </div>
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
