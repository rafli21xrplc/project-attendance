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
                    <h3>Type Kelas</h3>
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
                                        <th class="text-center">ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($type as $index => $item)
                                        <tr class="text-center">
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $item->category }}</td>
                                            <td>
                                                <button data-id="{{ $item->id }}" data-category="{{ $item->category }}"  type="button"
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
                                        <input type="text" id="basicPost" name="basicPost"
                                            class="form-control dt-post" placeholder="Web Developer"
                                            aria-label="Web Developer" aria-describedby="basicPost2" />
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
    <div class="modal fade" id="modal-classroom" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title mx-auto my-1" id="exampleModalLabel1">KELAS</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.type_class.store') }}" method="POST">
                    @csrf
                    <div class="modal-body row py-0">
                        <div class="mb-1">
                            <label class="form-label" for="basic-default-category">Kagetori</label>
                            <input type="text" class="form-control" id="basic-default-category" name="category"
                                placeholder="Kelas.. " required value="{{ old('category') }}" />
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
                            <label class="form-label" for="basic-default-category-update">Kategori</label>
                            <input type="text" class="form-control" id="basic-default-category-update" name="category"
                                placeholder="Kelas.. " required value="{{ old('category') }}" />
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
            var actionUrl = `type_class/${id}`;
            $('#form-update').attr('action', actionUrl);
            
            var name = $(this).data('category');

            var formUpdate = $('#modal-category-update #div-update');

            formUpdate.find('#basic-default-name-update').val(name);
            $('#modal-category-update').modal('show');
        });

        $('.btn-delete').click(function() {
            id = $(this).data('id')
            var actionUrl = `type_class/${id}`;
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
