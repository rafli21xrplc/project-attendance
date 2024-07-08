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
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet"
        href="{{ asset('assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/jquery-timepicker/jquery-timepicker.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/pickr/pickr-themes.css') }}" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
    <link href="https://raw.githack.com/ttskch/select2-bootstrap4-theme/master/dist/select2-bootstrap4.css"
        rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.4/css/jquery.dataTables.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/css/bootstrap-select.min.css"
        rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/js/bootstrap-select.min.js"></script>

    <style>
        #select2Multiple {
            width: 100%;
            min-height: 100px;
            border-radius: 3px;
            border: 1px solid #444;
            padding: 10px;
            color: #444444;
            font-size: 14px;
        }

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

        .select2-container {
            z-index: 99999;
        }
    </style>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="d-flex justify-content-between align-items-center flex-wrap py-2 px-4">
                <div>
                    <h3>Pembayaran Siswa</h3>
                </div>
                <div>
                    <button data-bs-toggle="modal" data-bs-target="#modal-installments" type="button"
                        class="btn btn-label-success"><i class="ti ti-plus me-sm-1"></i> <span
                            class="d-none d-sm-inline-block">Add New Record</span></button>
                </div>
            </div>

            <div class="row">
                <div class="col-12 order-5">
                    <div class="card py-3">
                        <div class="card-datatable table-responsive py-3 px-4">
                            <table id="table-content" class="table-content datatables-basic table display">
                                <thead>
                                    <tr class="text-center">
                                        <th>NO</th>
                                        <th>PEMBAYARAN</th>
                                        <th>SISWA</th>
                                        <th>JUMLAH</th>
                                        <th>TANGGAL PEMBAYARAN</th>
                                        <th>ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($installments as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $item->studentPayment->payment->name }}</td>
                                            <td>{{ $item->studentPayment->student->name }}</td>
                                            <td>{{ $item->amount }}</td>
                                            <td>{{ \Carbon\Carbon::parse($item->payment_date)->formatLocalized('%d %B %Y') ?? '-' }}
                                            </td>
                                            <td>
                                                <button data-id="{{ $item->id }}"
                                                    data-student_payment_id="{{ $item->student_payment_id }}"
                                                    data-amount="{{ $item->amount }}"
                                                    data-payment_date="{{ $item->payment_date }}" type="button"
                                                    class="btn btn-label-warning btn-update">
                                                    <i class="fa-solid fa-pen"></i>
                                                </button>
                                                <button data-id="{{ $item->id }}" type="button"
                                                    class="btn btn-label-danger btn-delete">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
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

    <div class="modal fade" id="modal-installments-update" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title mx-auto my-1" id="exampleModalLabel1">Update Installment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="form-update" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body row py-0" id="div-update">
                        <div class="col-md-6 mb-4">
                            <label for="student_payment_id-update" class="form-label">Tagihan Siswa</label>
                            <select id="student_payment_id-update" class="selectpicker form-control" name="student_payment_id" required data-live-search="true">
                                <option selected disabled>PILIH SISWA</option>
                                @foreach ($tagihan as $item)
                                    <option value="{{ $item->id }}">{{ $item->student_name }} -
                                        {{ $item->payment_name }} -
                                        {{ number_format($item->payment_amount, 0, ',', '.') }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label" for="basic-default-amount-update">Nominal</label>
                            <input type="number" class="form-control" id="basic-default-amount-update" name="amount"
                                placeholder="Nominal.." required value="{{ old('amount') }}" />
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label" for="basic-default-payment-date-update">Payment Date</label>
                            <input type="date" class="form-control" id="basic-default-payment-date-update"
                                name="payment_date" required value="{{ old('payment_date') }}" />
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

    <div class="modal fade" id="modal-installments" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title mx-auto my-1" id="exampleModalLabel1">Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.installment.store') }}" method="POST">
                    @csrf
                    <div class="modal-body row py-0">
                        <div class="col-md-6 mb-4">
                            <label for="student_payment_id" class="form-label">Tagihan Siswa</label>
                            <select id="student_payment_id" class="selectpicker form-control" data-live-search="true"
                                name="student_payment_id">
                                <option selected disabled>PILIH SISWA</option>
                                @foreach ($tagihan as $item)
                                    <option value="{{ $item->id }}">{{ $item->student_name }} -
                                        {{ $item->payment_name }} -
                                        {{ number_format($item->payment_amount, 0, ',', '.') }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label" for="basic-default-amount">nominal</label>
                            <input type="number" class="form-control" id="basic-default-amount" name="amount"
                                placeholder="Nominal.. " required value="{{ old('amount') }}" />
                        </div>

                        <div id="hidden-inputs"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-danger" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            $('#table-content').DataTable();
        });
        $(document).ready(function() {
            $('.selectpicker').selectpicker();
        });
    </script>

    <script>
        $('.btn-update').click(function() {
            var id = $(this).data('id');
            var actionUrl = `installment/${id}`;
            $('#form-update').attr('action', actionUrl);

            var studentPaymentId = $(this).data('student_payment_id');
            var amount = $(this).data('amount');
            var paymentDate = $(this).data('payment_date');

            var formUpdate = $('#modal-installments-update #div-update');

            console.log(studentPaymentId);

            formUpdate.find('#student_payment_id-update option[value="' + studentPaymentId + '"]').prop('selected', true);
            formUpdate.find('#basic-default-amount-update').val(amount);
            formUpdate.find('#basic-default-payment-date-update').val(paymentDate);

            $('#modal-installments-update').modal('show');
        });


        $('.btn-delete').click(function() {
            id = $(this).data('id')
            var actionUrl = `installment/${id}`;
            $('#form-delete').attr('action', actionUrl);
            $('#modal-delete').modal('show')
        });
    </script>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
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
    <script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.js"></script>
@endsection
