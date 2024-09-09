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
                    <h3>Pembayaran</h3>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button data-bs-toggle="modal" data-bs-target="#add-modal-payment" type="button"
                        class="btn btn-label-success"><i class="ti ti-plus me-sm-1"></i> <span
                            class="d-none d-sm-inline-block">Type Pembayaran</span></button>
                    <button data-bs-toggle="modal" data-bs-target="#modal-payment" type="button"
                        class="btn btn-label-success"><i class="ti ti-credit-card me-sm-1"></i> <span
                            class="d-none d-sm-inline-block">Record Type Pembayaran</span></button>
                    <a href="{{ route('admin.export.payment.rekapitulasi') }}" class="btn btn-label-primary me-2"
                        style="color: blue">
                        <i class="fa-solid fa-file-export me-1"></i> <span class="d-none d-sm-inline-block">Export
                            Rekapitulasi</span>
                    </a>
                    <form id="importForm" action="{{ route('admin.payment.import') }}" method="POST"
                        enctype="multipart/form-data" class="d-flex align-items-center mb-2 mb-md-0">
                        @csrf
                        <input type="file" name="file" id="fileInput" class="form-control d-none" required>
                        <button type="submit" class="btn btn-label-primary me-2" id="importButton" style="color: blue">
                            <i class="ti ti-printer me-1"></i> <span class="d-none d-sm-inline-block">Import</span>
                        </button>
                    </form>
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
                                    <tr class="text-center">
                                        <th>NO</th>
                                        <th>NAMA</th>
                                        <th>NOMINAL</th>
                                        <th>TYPE</th>
                                        <th>MULAI TENGGAT</th>
                                        <th>AKHIR TENGGAT</th>
                                        <th>ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($payment as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $item->name }}</td>
                                            <td>{{ number_format($item->amount, 0, ',', '.') }}</td>
                                            <td>{{ $item->category }}</td>
                                            <td>{{ \Carbon\Carbon::parse($item->start_date)->formatLocalized('%d %B %Y') ?? '-' }}
                                            <td>{{ \Carbon\Carbon::parse($item->end_date)->formatLocalized('%d %B %Y') ?? '-' }}
                                            <td>
                                                <a class="btn btn-label-success"
                                                    href="{{ route('admin.export.payment', $item->id) }}"
                                                    onclick="event.preventDefault(); document.getElementById('export-form-{{ $item->id }}').submit();">
                                                    <i class="fa-solid fa-file-export"></i>
                                                </a>
                                                <form id="export-form-{{ $item->id }}"
                                                    action="{{ route('admin.export.payment', $item->id) }}" method="GET"
                                                    class="d-none">
                                                    @csrf
                                                </form>
                                                <button data-id="{{ $item->id }}" data-name="{{ $item->name }}"
                                                    data-category="{{ $item->category }}"
                                                    data-start_date="{{ $item->start_date }}"
                                                    data-end_date="{{ $item->end_date }}"
                                                    data-category="{{ $item->category }}"
                                                    data-tenggat="{{ $item->tenggat }}" data-amount="{{ $item->amount }}"
                                                    type="button" class="btn btn-label-warning btn-update"><i
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
                    <h5 class="modal-title mx-auto my-1" id="exampleModalLabel1">Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.payment.store') }}" method="POST">
                    @csrf
                    <div class="modal-body row py-0">
                        <div class="mb-1">
                            <label class="form-label" for="basic-default-name">name</label>
                            <input type="text" class="form-control" id="basic-default-name" name="name"
                                placeholder="nama.. " required value="{{ old('name') }}" />
                        </div>
                        <div class="mb-1">
                            <label class="form-label" for="basic-default-amount">nominal</label>
                            <input type="number" class="form-control" id="basic-default-amount" name="amount"
                                placeholder="Nominal.. " required value="{{ old('amount') }}" />
                        </div>
                        <div class="mb-1">
                            <label for="category" class="form-label">Kategori</label>
                            <select class="form-select " name="category" id="category">
                                <option value="utama">Utama</option>
                                <option value="tambahan">Tambahan</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="start_date" class="form-label">Bulan</label>
                            <input class="form-control" type="date" value="2024-06-18" id="start_date"
                                name="start_date" />
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="end_date" class="form-label">Bulan</label>
                            <input class="form-control" type="date" value="2024-06-18" id="end_date"
                                name="end_date" />
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
                            <label class="form-label" for="basic-default-name-update">nama</label>
                            <input type="text" class="form-control" id="basic-default-name-update" name="name"
                                placeholder="nama.. " required value="{{ old('name') }}" />
                        </div>
                        <div class="mb-1">
                            <label class="form-label" for="basic-default-amount-update">nominal</label>
                            <input type="text" class="form-control" id="basic-default-amount-update" name="amount"
                                placeholder="nominal.. " required value="{{ old('amount') }}" />
                        </div>
                        <div class="mb-1">
                            <label class="form-label" for="basic-default-start_date-update">Mulai Tenggat</label>
                            <input class="form-control" type="date" value="2021-06-18"
                                id="basic-default-start_date-update" name="start_date"
                                value="{{ old('start_date') }}" />
                        </div>
                        <div class="mb-1">
                            <label class="form-label" for="basic-default-end_date-update">Akhir Tenggat</label>
                            <input class="form-control" type="date" value="2021-06-18"
                                id="basic-default-end_date-update" name="end_date" value="{{ old('end_date') }}" />
                        </div>
                        <div class="mb-1">
                            <label for="category" class="form-label">Kategori</label>
                            <select class="form-select " name="category" id="category-update">
                                <option value="utama">Utama</option>
                                <option value="tambahan">Tambahan</option>
                            </select>
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

    {{-- modal payment --}}
    <div class="modal fade" id="add-modal-payment" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title mx-auto my-1" id="exampleModalLabel1">Tipe Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.type_payment.store') }}" method="POST">
                    @csrf
                    <div class="modal-body row py-0">
                        <div class="mb-1">
                            <label class="form-label" for="basic-default-name">name</label>
                            <input type="text" class="form-control" id="basic-default-name" name="name"
                                placeholder="nama.. " required value="{{ old('name') }}" />
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

    {{-- modal payment --}}
    <div class="modal fade" id="modal-payment" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title mx-auto my-1" id="exampleModalLabel1">Tipe Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="col-12 col-md-12 mb-1">
                    <div class="py-3">
                        <div class="card-datatable table-responsive py-3 px-4">
                            <table id="table-modal"
                                class="table-content table-content-modal datatables-basic table display">
                                <thead>
                                    <tr class="text-center">
                                        <th>NO</th>
                                        <th>NAMA</th>
                                    </tr>
                                </thead>
                                <tbody id="student-table-body">
                                    @foreach ($typePayment as $index => $item)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td class="text-center">{{ $item->name }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
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

        new DataTable('#table-content', {
            pagingType: 'simple_numbers'
        });
    </script>
    <script>
        $('.btn-update').click(function() {
            var id = $(this).data('id');
            var actionUrl = `payment/${id}`;
            $('#form-update').attr('action', actionUrl);

            var name = $(this).data('name');
            var amount = $(this).data('amount');
            var installment = $(this).data('installment');
            var category = $(this).data('category');
            var start_date = $(this).data('start_date');
            var end_date = $(this).data('end_date');

            var formUpdate = $('#modal-classroom-update #div-update');

            formUpdate.find('#basic-default-name-update').val(name);
            formUpdate.find('#basic-default-amount-update').val(amount);
            formUpdate.find('#basic-default-installment-update').val(installment);
            formUpdate.find('#basic-default-start_date-update').val(start_date);
            formUpdate.find('#basic-default-end_date-update').val(end_date);
            formUpdate.find('#category-update option[value="' + category + '"]').prop('selected', true);
            $('#modal-classroom-update').modal('show');
        });

        $('.btn-delete').click(function() {
            id = $(this).data('id')
            var actionUrl = `payment/${id}`;
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
