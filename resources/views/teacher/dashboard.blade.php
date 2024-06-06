@extends('teacher.layouts.app')

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
         

            <div class="row">
                <div class="col-12 mb-4">
                    <div class="card">
                        <div class="user-profile-header d-flex flex-column flex-sm-row text-sm-start text-center mb-4">
                            <div class="flex-shrink-0 mt-n2 mx-sm-0 mx-auto">
                                <img src="{{ asset('assets/img/avatars/14.png') }}" alt="user image"
                                    class="d-block h-auto ms-0 ms-sm-4 rounded user-profile-img" />
                            </div>
                            <div class="flex-grow-1 mt-3 mt-sm-5">
                                <div
                                    class="d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start mx-4 flex-md-row flex-column gap-4">
                                    <div class="user-profile-info">
                                        <h4>{{ Auth::user()->teacher->name }}</h4>
                                        <ul
                                            class="list-inline mb-0 d-flex align-items-center flex-wrap justify-content-sm-start justify-content-center gap-2">
                                            <li class="list-inline-item d-flex gap-1">
                                                <i class="ti ti-color-swatch"></i> {{ Auth::user()->teacher->position }}
                                            </li>
                                            <li class="list-inline-item d-flex gap-1">
                                                <i class="fa-regular fa-address-book" style="font-size: 18px"></i> {{ Auth::user()->teacher->nip }}
                                            </li>
                                            <li class="list-inline-item d-flex gap-1">
                                                <i class="fa-regular fa-address-book" style="font-size: 18px"></i> {{ Auth::user()->teacher->nuptk }}
                                            </li>
                                            <li class="list-inline-item d-flex gap-1">
                                                <i class="ti ti-calendar"></i> {{ \Carbon\Carbon::parse(Auth::user()->teacher->day_of_birth)->format('d M Y') }}
                                            </li>
                                        </ul>
                                    </div>
                                    <a href="javascript:void(0)" class="btn btn-primary">
                                        <i class="ti ti-check me-1"></i>Connected
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container mt-5">
                <div class="row">
                    <div class="col-12">
                        <ul class="nav nav-pills flex-column flex-sm-row mb-4">
                            @foreach($typeClasses as $index => $typeClass)
                                <li class="nav-item mx-1">
                                    <button class="nav-link {{ $index == 0 ? 'active' : '' }}" data-bs-toggle="tab"
                                        data-bs-target="#form-tabs-{{ $typeClass->id }}" role="tab" aria-selected="{{ $index == 0 ? 'true' : 'false' }}">
                                        {{ $typeClass->category }}
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
        
                <div class="row">
                    <div class="col-12">
                        <div class="tab-content">
                            @foreach($typeClasses as $index => $typeClass)
                                <div class="tab-pane fade {{ $index == 0 ? 'active show' : '' }}" id="form-tabs-{{ $typeClass->id }}" role="tabpanel">
                                    <div class="row g-4">
                                        @foreach($typeClass->classRooms as $classRoom)
                                            <div class="col-xl-4 col-lg-6 col-md-6">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="d-flex align-items-center mb-3">
                                                            <a href="javascript:;" class="d-flex align-items-center">
                                                                <div class="me-2 text-body h5 mb-0">
                                                                    {{ $classRoom->name }}
                                                                </div>
                                                            </a>
                                                            <div class="ms-auto">
                                                                <ul class="list-inline mb-0 d-flex align-items-center">
                                                                    <li class="list-inline-item">
                                                                        <div class="dropdown">
                                                                            <button type="button"
                                                                                class="btn dropdown-toggle hide-arrow p-0"
                                                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                                                <i class="ti ti-dots-vertical text-muted"></i>
                                                                            </button>
                                                                            <ul class="dropdown-menu dropdown-menu-end">
                                                                                <li>
                                                                                    <a class="dropdown-item"
                                                                                        href="javascript:void(0);">Rename Team</a>
                                                                                </li>
                                                                                <li>
                                                                                    <a class="dropdown-item"
                                                                                        href="javascript:void(0);">View Details</a>
                                                                                </li>
                                                                                <li>
                                                                                    <a class="dropdown-item"
                                                                                        href="javascript:void(0);">Add to
                                                                                        favorites</a>
                                                                                </li>
                                                                                <li>
                                                                                    <hr class="dropdown-divider" />
                                                                                </li>
                                                                                <li>
                                                                                    <a class="dropdown-item text-danger"
                                                                                        href="javascript:void(0);">Delete Team</a>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                        <div class="ms-auto">
                                                            <a href="javascript:;" class="me-2"><span style="font-size: 12px;"
                                                                    class="badge bg-label-primary">36 SISWA</span></a>
                                                            <a href="javascript:;" class="me-2"><span style="font-size: 12px;"
                                                                    class="badge bg-label-danger">MATEMATIKA</span></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
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
