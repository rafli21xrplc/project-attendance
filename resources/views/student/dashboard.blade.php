@extends('student.layouts.app')

@section('link')
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/app-logistics-dashboard.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/%40form-validation/umd/styles/index.min.css') }}" />
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.2/css/dataTables.dataTables.css">
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="py-2 mb-4">
                Dashboard
            </h4>

            <div class="row">
                <div class="col-sm-6 col-lg-3 mb-4">
                    <div class="card card-border-shadow-primary">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2 pb-1">
                                <div class="avatar me-2">
                                    <span class="avatar-initial rounded bg-label-primary"><i
                                            class="ti ti-truck ti-md"></i></span>
                                </div>
                                <h4 class="ms-1 mb-0">42</h4>
                            </div>
                            <p class="mb-1">On route vehicles</p>
                            <p class="mb-0">
                                <span class="fw-medium me-1">+18.2%</span>
                                <small class="text-muted">than last week</small>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3 mb-4">
                    <div class="card card-border-shadow-warning">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2 pb-1">
                                <div class="avatar me-2">
                                    <span class="avatar-initial rounded bg-label-warning"><i
                                            class="ti ti-alert-triangle ti-md"></i></span>
                                </div>
                                <h4 class="ms-1 mb-0">8</h4>
                            </div>
                            <p class="mb-1">Vehicles with errors</p>
                            <p class="mb-0">
                                <span class="fw-medium me-1">-8.7%</span>
                                <small class="text-muted">than last week</small>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3 mb-4">
                    <div class="card card-border-shadow-danger">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2 pb-1">
                                <div class="avatar me-2">
                                    <span class="avatar-initial rounded bg-label-danger"><i
                                            class="ti ti-git-fork ti-md"></i></span>
                                </div>
                                <h4 class="ms-1 mb-0">27</h4>
                            </div>
                            <p class="mb-1">Deviated from route</p>
                            <p class="mb-0">
                                <span class="fw-medium me-1">+4.3%</span>
                                <small class="text-muted">than last week</small>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3 mb-4">
                    <div class="card card-border-shadow-info">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2 pb-1">
                                <div class="avatar me-2">
                                    <span class="avatar-initial rounded bg-label-info"><i
                                            class="ti ti-clock ti-md"></i></span>
                                </div>
                                <h4 class="ms-1 mb-0">13</h4>
                            </div>
                            <p class="mb-1">Late vehicles</p>
                            <p class="mb-0">
                                <span class="fw-medium me-1">-2.5%</span>
                                <small class="text-muted">than last week</small>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 order-5">
                    <div class="card py-3">
                        <div class="card-datatable table-responsive py-3 px-4">
                            <table id="table-content" class="datatables-basic table display">
                                <thead>
                                    <tr>
                                        <th>id</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Date</th>
                                        <th>Salary</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>John Doe</td>
                                        <td>john.doe@example.com</td>
                                        <td>2024-03-12</td>
                                        <td>$5000</td>
                                        <td>Active</td>
                                        <td>
                                            <button type="button" class="btn btn-label-success">Success</button>
                                            <button type="button" class="btn btn-label-danger">Danger</button>
                                            <button type="button" class="btn btn-label-warning">Warning</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>Jane Smith</td>
                                        <td>jane.smith@example.com</td>
                                        <td>2024-03-12</td>
                                        <td>$6000</td>
                                        <td>Inactive</td>
                                        <td>
                                            <button type="button" class="btn btn-label-success">Success</button>
                                            <button type="button" class="btn btn-label-danger">Danger</button>
                                            <button type="button" class="btn btn-label-warning">Warning</button>
                                        </td>
                                    </tr>
                                    <!-- Add more rows as needed -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Modal to add new record -->
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
                                    <button type="submit" class="btn btn-primary data-submit me-sm-3 me-1">
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
                    {{-- <div class="offcanvas offcanvas-end" id="add-new-record">
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
                                    <button type="submit" class="btn btn-primary data-submit me-sm-3 me-1">
                                        Submit
                                    </button>
                                    <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div> --}}
                </div>

            </div>
        </div>

        <div class="content-backdrop fade"></div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/%40form-validation/umd/bundle/popular.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/%40form-validation/umd/plugin-bootstrap5/index.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/%40form-validation/umd/plugin-auto-focus/index.min.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.0.2/js/dataTables.js"></script>

    <script>
        new DataTable('#table-content', {
            pagingType: 'simple_numbers'
        });
    </script>
@endsection
