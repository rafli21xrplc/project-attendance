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
                    <h3>KBM Periode</h3>
                </div>
                <div>
                    <button type="button" class="btn btn-label-primary me-2" style="color: blue">
                        <i class="ti ti-printer me-1"></i> <span class="d-none d-sm-inline-block">Export</span>
                    </button>
                    <button data-bs-toggle="modal" data-bs-target="#modal-classroom" type="button"
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
                                    <tr>
                                        <th class="text-center">NO</th>
                                        <th class="text-center">NAMA</th>
                                        <th class="text-center">RENTANG TANGGAL</th>
                                        <th class="text-center">ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($kbm_period as $index => $item)
                                        <tr class="text-center">
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $item->name }}</td>
                                            <td>
                                                {{ Carbon\Carbon::parse($item->start_date)->format('d F Y') }} - {{ Carbon\Carbon::parse($item->end_date)->format('d F Y') }}
                                            </td>

                                            <td>
                                                <button data-id="{{ $item->id }}" data-name="{{ $item->name }}"
                                                    data-start_date="{{ $item->start_date }}"
                                                    data-end_date="{{ $item->end_date }}" type="button"
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
    <div class="modal fade" id="modal-classroom" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title mx-auto my-1" id="exampleModalLabel1">KBM</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.kbm_period.store') }}" method="POST">
                    @csrf
                    <div class="modal-body row py-0">
                        <div class="mb-1">
                            <label class="form-label" for="basic-default-name">Nama</label>
                            <input type="text" class="form-control" id="basic-default-name" name="name"
                                placeholder="Kelas.. " required value="{{ old('name') }}" />
                        </div>
                        <div class="mb-1">
                            <label for="start_date" class="form-label">Waktu Awal Periode</label>
                            <input class="form-control" type="date" id="start_date" name="start_date" />
                        </div>
                        <div class="mb-1">
                            <label for="end_date" class="form-label">Waktu Akhir Periode</label>
                            <input class="form-control" type="date" id="end_date" name="end_date" />
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
    <div class="modal fade" id="modal-period-update" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header mb-2 py-3" style="background: rgba(56, 42, 214, 0.9);">
                    <h5 class="modal-title mx-auto" style="color: rgb(246, 246, 246);" id="exampleModalLabel1">KBM</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="form-update" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body row py-0" id="div-update">
                        <div class="mb-1">
                            <label class="form-label" for="basic-default-name-update">Nama</label>
                            <input type="text" class="form-control" id="basic-default-name-update" name="name"
                                placeholder="name.. " required value="{{ old('name') }}" />
                        </div>
                        <div class="mb-1">
                            <label for="start_date" class="form-label">Waktu Awal Periode</label>
                            <input class="form-control" type="date" id="start_date-update" name="start_date" />
                        </div>
                        <div class="mb-1">
                            <label for="end_date" class="form-label">Waktu Akhir Periode</label>
                            <input class="form-control" type="date" id="end_date-update" name="end_date" />
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
            var actionUrl = `kbm_period/${id}`;
            $('#form-update').attr('action', actionUrl);

            var name = $(this).data('name');
            var start_date = $(this).data('start_date');
            var end_date = $(this).data('end_date');

            var formUpdate = $('#modal-period-update #div-update');

            formUpdate.find('#basic-default-name-update').val(name);
            formUpdate.find('#start_date-update').val(start_date);
            formUpdate.find('#end_date-update').val(end_date);

            $('#modal-period-update').modal('show');
        });

        $('.btn-delete').click(function() {
            id = $(this).data('id')
            var actionUrl = `kbm_period/${id}`;
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
