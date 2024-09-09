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
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.4/css/jquery.dataTables.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/css/bootstrap-select.min.css"
        rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/js/bootstrap-select.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
    <link href="https://raw.githack.com/ttskch/select2-bootstrap4-theme/master/dist/select2-bootstrap4.css"
        rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

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
                    <h3>Tanggungan Pembayaran</h3>
                </div>
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

                    <form action="{{ route('admin.export.payment_bbpp.excel') }}" method="POST">
                        @csrf
                        <div class="row g-3 align-items-center">
                            <div class="col-12 col-md-6">
                                <input type="month" id="month" name="month" class="form-control" required>
                            </div>

                            <div class="col-12 col-md-6">
                                <button type="submit" class="btn btn-primary w-100">
                                    Export Report
                                </button>
                            </div>
                        </div>
                    </form>

                    <button data-bs-toggle="modal" data-bs-target="#modal-classroom" type="button"
                        class="btn btn-label-success d-block"><i class="ti ti-plus me-sm-1"></i> <span
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
                                        <th>LUNAS</th>
                                        <th>ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($studentPayment as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $item->payment_name }}</td>
                                            <td>{{ $item->student_name }}</td>
                                            <td>
                                                @if ($item->is_paid)
                                                    <span style="color: green;">LUNAS</span>
                                                @else
                                                    <span style="color: red;">BELUM LUNAS</span>
                                                @endif
                                            </td>
                                            <td>
                                                <button data-id="{{ $item->id }}"
                                                    data-student_id="{{ $item->student_id }}"
                                                    data-payment_id="{{ $item->payment_id }}"
                                                    data-student_name="{{ $item->student_name }}"
                                                    data-payment_name="{{ $item->payment_name }}"
                                                    data-tenggat="{{ $item->payment_date }}"
                                                    data-payment_amount="{{ $item->payment_amount }}"
                                                    data-installments="{{ json_encode($item->installments) }}"
                                                    data-is_paid="{{ $item->is_paid }}" type="button"
                                                    class="btn btn-label-success btn-pay">
                                                    <i class="fa-solid fa-dollar-sign"></i>
                                                </button>
                                                <button data-id="{{ $item->id }}"
                                                    data-student_id="{{ $item->student_id }}"
                                                    data-payment_id="{{ $item->payment_id }}"
                                                    data-student_name="{{ $item->student_name }}"
                                                    data-payment_name="{{ $item->payment_name }}"
                                                    data-payment_date="{{ $item->payment_date }}"
                                                    data-payment_amount="{{ $item->payment_amount }}"
                                                    data-installments="{{ json_encode($item->installments) }}"
                                                    data-is_paid="{{ $item->is_paid }}" type="button"
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

    <div class="modal fade" id="modal-classroom" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title mx-auto my-1" id="exampleModalLabel1">Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.student_payment.store') }}" method="POST">
                    @csrf
                    <div class="modal-body row py-0">
                        <div class="col-md-6 mb-4">
                            <label class="form-label" for="payment_id">Pembayaran</label>
                            <select id="payment_id" name="payment_id" class="selectpicker form-control"
                                data-live-search="true" aria-label="Default select example">
                                <option selected disabled>PILIH PEMBAYARAN SISWA</option>
                                @foreach ($payment as $item)
                                    <option value="{{ $item->id }}"
                                        {{ old('payment') == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label for="classroom_id" class="form-label">Kelas</label>
                            <select id="classroom_id" class="selectpicker form-control" name="classroom_id"
                                data-live-search="true">
                                <option selected disabled>PILIH KELAS</option>
                                @foreach ($type as $typeClass)
                                    <optgroup label="{{ $typeClass->category }}">
                                        @foreach ($typeClass->classRooms as $classRoom)
                                            <option value="{{ $classRoom->id }}">{{ $classRoom->name }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-12 mb-1">
                            <div class="py-3">
                                <div class="card-datatable table-responsive py-3 px-4">
                                    <table id="table-modal"
                                        class="table-content table-content-modal datatables-basic table display">
                                        <thead>
                                            <tr class="text-center">
                                                <th><input type="checkbox" id="select-all"></th>
                                                <th>KODE</th>
                                                <th>NAMA</th>
                                            </tr>
                                        </thead>
                                        <tbody id="student-table-body">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
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

    <div class="modal fade" id="modal-student_payment-update" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header mb-2 py-3" style="background: rgba(56, 42, 214, 0.9);">
                    <h5 class="modal-title mx-auto" style="color: rgb(246, 246, 246);" id="exampleModalLabel1">Student
                        Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="form-update" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body row py-0" id="div-update">
                        <div class="mb-1">
                            <label class="form-label" for="student-dropdown">Student</label>
                            <select class="form-select" id="student-dropdown" name="student_id" required>
                                <option selected disabled>Pilih Siswa</option>
                                @foreach ($student as $item)
                                    <option value="{{ $item->id }}"
                                        {{ old('student_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-1">
                            <label class="form-label" for="payment-dropdown">Payment</label>
                            <select class="form-select" id="payment-dropdown" name="payment_id" required>
                                <option selected disabled>Pilih Pembayaran</option>
                                @foreach ($payment as $item)
                                    <option value="{{ $item->id }}"
                                        {{ old('payment_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-1">
                            <label class="form-label" for="payment-date">Payment Date</label>
                            <input type="date" class="form-control" id="payment-date" name="payment_date" required
                                value="{{ old('payment_date') }}" />
                        </div>
                        <div class="mb-1 form-check">
                            <input type="checkbox" class="form-check-input" id="is-paid" name="is_paid">
                            <label class="form-check-label" for="is-paid">Is Paid</label>
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


    <div class="modal fade" id="modal-payment-pay" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title mx-auto my-1" id="exampleModalLabel1">Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body row py-0">
                    <div class="col-md-6 mb-3 border-end">
                        <div class="info-section">
                            <label for="student-name" class="form-label">Nama Siswa</label>
                            <input type="text" id="student-name" class="form-control" readonly>
                        </div>
                        <div class="info-section">
                            <label for="payment-name" class="form-label">Nama Pembayaran</label>
                            <input type="text" id="payment-name" class="form-control" readonly>
                        </div>
                        <div class="info-section">
                            <label for="payment-amount" class="form-label">Jumlah Pembayaran</label>
                            <input type="text" id="payment-amount" class="form-control" readonly>
                        </div>
                        <div class="info-section">
                            <label for="payment-date" class="form-label">Tanggal Pembayaran</label>
                            <input type="text" id="payment-date" class="form-control" readonly>
                        </div>
                        <div class="info-section my-2">
                            <span id="payment-status" class="badge"></span>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3 d-flex flex-column gap-3">
                        <div class="border-bottom pb-3">
                            <label for="installments" class="form-label">Installments</label>
                            <ul id="installment-list" class="timeline ms-1 mb-0 list-group">
                                <!-- Installments will be populated here -->
                            </ul>
                        </div>
                        <div class="border-top pt-3">
                            <div class="row mb-3">
                                <div class="col-12 col-md-6 mb-2">
                                    <label for="installment-amount" class="form-label">Tambah Installment</label>
                                    <input type="number" class="form-control" id="installment-amount" min="1"
                                        placeholder="Masukkan nominal installment">
                                </div>
                                <div class="col-12 col-md-6 mb-2">
                                    <label for="type_payment_id" class="form-label">Pembayaran</label>
                                    <select id="type_payment_id" name="type_payment_id" class="form-select"
                                        aria-label="Default select example">
                                        <option selected disabled>Pilih Type</option>
                                        @foreach ($typePayment as $item)
                                            <option id="{{ $item->name }}" value="{{ $item->id }}">
                                                {{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 mb-2" id="file-upload-div" style="display: none;">
                                    <label for="description" class="form-label">Bukti Pembayaran</label>
                                    <textarea class="form-control" name="" id="description" rows="2" style="resize: none"></textarea>
                                </div>
                            </div>
                            <div>
                                <button type="button" id="add-installment-btn" class="btn btn-primary">Tambah
                                    Installment</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-danger" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(function() {
            $('.select').each(function() {
                $(this).select2({
                    theme: 'bootstrap4',
                    width: 'style',
                    placeholder: $(this).attr('placeholder'),
                    allowClear: Boolean($(this).data('allow-clear')),
                });
            });
        });
    </script>
    <script>
        document.getElementById('type_payment_id').addEventListener('change', function() {
            var fileUploadDiv = document.getElementById('file-upload-div');
            var selectedOption = this.options[this.selectedIndex];
            var selectedOptionId = selectedOption.id;
            if (selectedOptionId === 'TRANSFER') {
                fileUploadDiv.style.display = 'block';
            } else {
                fileUploadDiv.style.display = 'none';
            }
        });
    </script>
    <script>
        $(document).ready(function() {
            $('.btn-pay').click(function() {
                var id = $(this).data('id');
                var studentName = $(this).data('student_name');
                var paymentName = $(this).data('payment_name');
                var paymentAmount = $(this).data('payment_amount');
                var paymentDate = $(this).data('tenggat');
                var installments = $(this).data('installments');
                var isPaid = $(this).data('is_paid');

                $('#student-name').val(studentName);
                $('#payment-name').val(paymentName);
                $('#payment-amount').val(new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR'
                }).format(paymentAmount));
                $('#payment-date').val(new Date(paymentDate).toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric'
                }));

                if (isPaid) {
                    $('#payment-status').removeClass('bg-danger').addClass('bg-success').text('LUNAS');
                } else {
                    $('#payment-status').removeClass('bg-success').addClass('bg-danger').text(
                        'BELUM LUNAS');
                }

                var installmentList = $('#installment-list');
                installmentList.empty(); // Clear existing items

                if (installments.length > 0) {
                    $.each(installments, function(index, installment) {
                        var formattedAmount = new Intl.NumberFormat('id-ID', {
                            style: 'currency',
                            currency: 'IDR'
                        }).format(installment.amount);
                        var formattedDate = new Date(installment.payment_date).toLocaleDateString(
                            'id-ID', {
                                day: '2-digit',
                                month: 'long',
                                year: 'numeric'
                            });
                        var listItem = `<li class="timeline-item timeline-item-transparent ps-4 list-group-item">
                <span class="timeline-point timeline-point-info"></span>
                <div class="timeline-event">
                    <div class="timeline-header">
                        <h6 class="mb-0">Amount: ${formattedAmount}</h6>
                        <small class="text-muted">Payment Date: ${formattedDate}</small>
                    </div>
                </div>
            </li>`;
                        installmentList.append(listItem);
                    });
                } else {
                    installmentList.append(
                        '<li class="timeline-item timeline-item-transparent ps-4 list-group-item">No installments</li>'
                    );
                }

                $('#add-installment-btn').data('student-payment-id', id);
                $('#modal-payment-pay').modal('show');
            });

            $('#add-installment-btn').click(function() {
                var studentPaymentId = $(this).data('student-payment-id');
                var amount = $('#installment-amount').val();
                var type_payment_id = $('#type_payment_id').val();
                var description = $('#description').val();

                if (amount && studentPaymentId) {
                    var formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');
                    formData.append('student_payment_id', studentPaymentId);
                    formData.append('amount', amount);
                    formData.append('type_payment_id', type_payment_id);
                    formData.append('description', description);

                    axios.post('{{ route('admin.installments.api') }}', formData, {
                            headers: {
                                'Content-Type': 'multipart/form-data'
                            }
                        })
                        .then(function(response) {

                            var formattedAmount = new Intl.NumberFormat('id-ID', {
                                style: 'currency',
                                currency: 'IDR'
                            }).format(amount);
                            var formattedDate = new Date().toLocaleDateString('id-ID', {
                                day: '2-digit',
                                month: 'long',
                                year: 'numeric'
                            });
                            var listItem = `<li class="timeline-item timeline-item-transparent ps-4 list-group-item">
                        <span class="timeline-point timeline-point-info"></span>
                        <div class="timeline-event">
                            <div class="timeline-header">
                                <h6 class="mb-0">Amount: ${formattedAmount}</h6>
                                <small class="text-muted">Payment Date: ${formattedDate}</small>
                            </div>
                        </div>
                    </li>`;

                            $('#installment-list').append(listItem);

                            // Remove "No installments" message if it exists
                            $('#installment-list .no-installments').remove();

                            // Update totalAmount
                            var totalAmount = 0;
                            $('#installment-list li').each(function() {
                                var amountText = $(this).find('.mb-0').text().replace(
                                    'Amount: ', '').replace(/\D/g, '');
                                totalAmount += parseInt(amountText, 10);
                            });

                            // Update status if total amount paid matches the payment amount
                            var paymentAmount = parseInt($('#payment-amount').val().replace(/\D/g, ''),
                                10);
                            if (totalAmount >= paymentAmount) {
                                $('#payment-status').removeClass('bg-danger').addClass('bg-success')
                                    .text('LUNAS');

                                // Update status in table
                                $('button[data-id="' + studentPaymentId + '"]').closest('tr').find(
                                    '.status-cell').html(
                                    '<span class="badge bg-success">LUNAS</span>');
                            }

                            $('#installment-amount').val('');
                            $('#file').val(''); // Clear the file input after submission
                        })
                        .catch(function(error) {
                            console.log(error);

                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Gagal menambahkan installment',
                                showConfirmButton: false,
                                timer: 1500
                            });
                        });
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Mohon masukkan jumlah yang valid',
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
            });
        });



        $(document).ready(function() {
            $('.btn-pay').click(function() {
                var id = $(this).data('id');
                var studentName = $(this).data('student_name');
                var paymentName = $(this).data('payment_name');
                var paymentAmount = $(this).data('payment_amount');
                var paymentDate = $(this).data('tenggat');
                var installments = $(this).data('installments');

                $('#student-name').val(studentName);
                $('#payment-name').val(paymentName);
                $('#payment-amount').val(new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR'
                }).format(paymentAmount));
                $('#payment-date').val(new Date(paymentDate).toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric'
                }));

                var installmentList = $('#installment-list');
                installmentList.empty(); // Clear existing items

                if (installments.length > 0) {
                    $.each(installments, function(index, installment) {
                        var formattedAmount = new Intl.NumberFormat('id-ID', {
                            style: 'currency',
                            currency: 'IDR'
                        }).format(installment.amount);
                        var formattedDate = new Date(installment.payment_date).toLocaleDateString(
                            'id-ID', {
                                day: '2-digit',
                                month: 'long',
                                year: 'numeric'
                            });
                        var listItem = `
                    <li class="timeline-item timeline-item-transparent ps-4 list-group-item">
                        <span class="timeline-point timeline-point-info"></span>
                        <div class="timeline-event">
                            <div class="timeline-header">
                                <h6 class="mb-0">nominal: ${formattedAmount}</h6>
                                <small class="text-muted">${formattedDate}</small>
                            </div>
                        </div>
                    </li>`;
                        installmentList.append(listItem);
                    });
                } else {
                    installmentList.append(
                        '<li class="timeline-item timeline-item-transparent ps-4 list-group-item">No installments</li>'
                    );
                }

                $('#modal-payment-pay').modal('show');
            });
        });

        $('.btn-update').click(function() {
            var id = $(this).data('id');
            var actionUrl = `student_payment/${id}`;
            $('#form-update').attr('action', actionUrl);

            var studentId = $(this).data('student_id');
            var paymentId = $(this).data('payment_id');
            var paymentDate = $(this).data('payment_date');
            var isPaid = $(this).data('is_paid');

            var formUpdate = $('#modal-student_payment-update #div-update');

            formUpdate.find('#student-dropdown').val(studentId);
            formUpdate.find('#payment-dropdown').val(paymentId);
            formUpdate.find('#payment-date').val(paymentDate);
            formUpdate.find('#is-paid').prop('checked', isPaid);

            $('#modal-student_payment-update').modal('show');
        });

        $('.btn-delete').click(function() {
            id = $(this).data('id')
            var actionUrl = `student_payment/${id}`;
            $('#form-delete').attr('action', actionUrl);
            $('#modal-delete').modal('show')
        });
    </script>

    <script>
        $(document).ready(function() {
            $('.selectpicker').selectpicker();
        });
        $(document).ready(function() {
            $('#select2Multiple').select2({
                theme: 'bootstrap4',
                width: 'style',
                placeholder: $(this).attr('placeholder'),
                allowClear: Boolean($(this).data('allow-clear')),
                dropdownParent: $('#modal-classroom')
            });
        });
        $(document).ready(function() {
            $('#table-content').DataTable();
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $('.select2').select2();

            const table = $('#table-modal').DataTable({
                columnDefs: [{
                    orderable: false,
                    targets: 0
                }]
            });

            $('#classroom_id').on('change', function() {
                const classroomId = $(this).val();
                axios.get(`{{ route('admin.studentPayment.get') }}?classroom_id=${classroomId}`)
                    .then(response => {
                        const students = response.data;
                        const studentTableBody = document.getElementById('student-table-body');
                        studentTableBody.innerHTML = '';
                        students.forEach(student => {
                            const row = `
                            <tr>
                                <td><input type="checkbox" class="student-checkbox" value="${student.id}"></td>
                                <td>${student.student_id}</td>
                                <td>${student.name}</td>
                            </tr>
                        `;
                            studentTableBody.insertAdjacentHTML('beforeend', row);
                        });

                        attachEventListeners();
                    })
                    .catch(error => {
                        console.error('There was an error fetching the students!', error);
                    });
            });

            function updateSelectedStudentIds() {
                const hiddenInputsContainer = document.getElementById('hidden-inputs');
                hiddenInputsContainer.innerHTML = '';
                document.querySelectorAll('.student-checkbox:checked').forEach(function(checkbox) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'selected_student_ids[]';
                    input.value = checkbox.value;
                    hiddenInputsContainer.appendChild(input);
                });
            }

            document.getElementById('select-all').addEventListener('change', function() {
                const isChecked = this.checked;
                document.querySelectorAll('.student-checkbox').forEach(function(checkbox) {
                    checkbox.checked = isChecked;
                });
                updateSelectedStudentIds();
            });

            function attachEventListeners() {
                document.querySelectorAll('.student-checkbox').forEach(function(checkbox) {
                    checkbox.addEventListener('change', function() {
                        updateSelectedStudentIds();
                    });
                });
            }

            attachEventListeners();
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
