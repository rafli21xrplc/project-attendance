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
            <div class="d-flex justify-content-between align-items-center flex-wrap py-2 px-4">
                <div>
                    <h3> PENJADWALAN</h3>
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
                                        <th class="text-center">NOMER</th>
                                        <th class="text-center">AWAL WAKTU</th>
                                        <th class="text-center">AKHIR WAKTU</th>
                                        <th class="text-center">ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($time as $index => $item)
                                        <tr class="text-center">
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $item->time_number }}</td>
                                            <td>{{ \Carbon\Carbon::parse($item->start_time_schedule)->format('H:i') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($item->end_time_schedule)->format('H:i') }}</td>
                                            <td>
                                                <button data-id="{{ $item->id }}"
                                                    data-time_number="{{ $item->time_number }}"
                                                    data-start_time_schedule="{{ $item->start_time_schedule }}"
                                                    data-end_time_schedule="{{ $item->end_time_schedule }}" type="button"
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
                    <h5 class="modal-title mx-auto my-1" id="exampleModalLabel1">KELAS</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.time_schedule.store') }}" method="POST">
                    @csrf
                    <div class="modal-body row py-0">
                        <div class="mb-3 row">
                            <label for="time_number" class="col-md-2 col-form-label">Number Schedule</label>
                            <div class="col-md-10">
                                <input class="form-control" type="number" name="time_number" id="time_number"
                                    placeholder="1" />
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="start_time_schedule" class="col-md-2 col-form-label">Awal Waktu Schedule</label>
                            <div class="col-md-10">
                                <input class="form-control" type="time" value="12:30:00" id="start_time_schedule"
                                    name="start_time_schedule" />
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="end_time_schedule" class="col-md-2 col-form-label">Akhir Waktu Schedule</label>
                            <div class="col-md-10">
                                <input class="form-control" type="time" value="12:30:00" id="end_time_schedule"
                                    name="end_time_schedule" />
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
    <div class="modal fade" id="modal-classroom-update" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header mb-2 py-3" style="background: rgba(56, 42, 214, 0.9);">
                    <h5 class="modal-title mx-auto" style="color: rgb(246, 246, 246);" id="exampleModalLabel1">KELAS</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="form-update" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body row py-0" id="div-update">
                        <div class="mb-1">
                            <label class="form-label" for="basic-default-time_number-update">Number Schedule</label>
                            <input type="number" class="form-control" id="basic-default-time_number-update"
                                name="time_number" placeholder="1" required value="{{ old('time_number') }}" />
                        </div>
                        <div class="mb-1">
                            <label class="form-label" for="basic-default-start_time_schedule-update">Awal Waktu
                                Schedule</label>
                            <input type="time" class="form-control" id="basic-default-start_time_schedule-update"
                                name="start_time_schedule" placeholder="1" required
                                value="{{ old('start_time_schedule') }}" />
                        </div>
                        <div class="mb-1">
                            <label class="form-label" for="basic-default-end_time_schedule-update">Akhir Waktu
                                Schedule</label>
                            <input type="time" class="form-control" id="basic-default-end_time_schedule-update"
                                name="end_time_schedule" placeholder="1" required
                                value="{{ old('end_time_schedule') }}" />
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
        $('.btn-update').click(function() {
            var id = $(this).data('id');
            var actionUrl = `time_schedule/${id}`;
            $('#form-update').attr('action', actionUrl);

            var time_number = $(this).data('time_number');
            var start_time_schedule = $(this).data('start_time_schedule');
            var end_time_schedule = $(this).data('end_time_schedule');

            var formUpdate = $('#modal-classroom-update #div-update');

            formUpdate.find('#basic-default-time_number-update').val(time_number);
            formUpdate.find('#basic-default-start_time_schedule-update').val(start_time_schedule);
            formUpdate.find('#basic-default-end_time_schedule-update').val(end_time_schedule);
            $('#modal-classroom-update').modal('show');
        });

        $('.btn-delete').click(function() {
            id = $(this).data('id')
            var actionUrl = `time_schedule/${id}`;
            $('#form-delete').attr('action', actionUrl);
            console.log(actionUrl);
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
