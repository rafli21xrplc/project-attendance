@extends('student.layouts.app')

@section('link')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.2/css/dataTables.dataTables.css">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/app-logistics-dashboard.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/%40form-validation/umd/styles/index.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/app-academy.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/dropzone/dropzone.css') }}" />
    <style>
        .file-upload {
            display: flex;
            flex-direction: column;
            align-items: center;
            cursor: pointer;
            border: 2px dashed #ccc;
            padding: 20px;
            border-radius: 10px;
        }

        .file-upload:hover {
            background-color: #f8f9fa;
        }

        .upload-icon {
            color: #b6b6b6;
        }

        .upload-text {
            color: #6c757d;
        }

        .preview-container {
            margin-top: 10px;
        }

        .preview-container img {
            max-width: 100px;
        }
    </style>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="app-academy">
                <div class="card p-0 mb-4">
                    <div class="card-body d-flex flex-column flex-md-row justify-content-between p-0 pt-4">
                        <div class="app-academy-md-25 card-body py-0">
                            <img src="{{ asset('assets/img/illustrations/bulb-light.png') }}"
                                class="img-fluid app-academy-img-height scaleX-n1-rtl" alt="Bulb in hand" height="90" />
                        </div>
                        <div class="app-academy-md-50 card-body d-flex align-items-md-center flex-column text-md-center">
                            <h3 class="card-title mb-4 lh-sm px-md-5 lh-lg">
                                Welcome to your learning dashboard.
                                <span class="text-primary fw-medium text-nowrap">All your courses</span> in one place.
                            </h3>
                            <p class="mb-3">
                                Access your classes, assignments, and more.
                                Stay updated and make the most of your learning experience.
                            </p>
                        </div>
                        <div class="app-academy-md-25 d-flex align-items-end justify-content-end">
                            <img src="{{ asset('assets/img/illustrations/pencil-rocket.png') }}" alt="pencil rocket"
                                height="188" class="scaleX-n1-rtl" />
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header d-flex flex-wrap justify-content-between gap-3">
                        <div class="card-title mb-0 me-1">
                            <h5 class="mb-1">My Classes</h5>
                            <p class="text-muted mb-0">
                                Overview of your current courses
                            </p>
                        </div>
                        <div class="d-flex flex-column flex-md-row text-nowrap justify-content-end">
                            <button class="btn btn-label-warning d-flex align-items-center" href=""
                                data-bs-toggle="modal" data-bs-target="#modal-permission" type="button">
                                <span class="me-2">Request Permission</span><i
                                    class="ti ti-chevron-right scaleX-n1-rtl ti-sm"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row gy-4 mb-4">
                            @forelse ($schedule as $item)
                                <div class="col-sm-6 col-lg-4">
                                    <div class="card p-2 h-100 shadow-none border">
                                        <div class="rounded-2 text-center mb-3">
                                            <a href="#"><img class="img-fluid"
                                                    src="{{ asset('assets/content/classroom_bg.png') }}"
                                                    alt="classroom image" /></a>
                                        </div>
                                        <div class="card-body p-3 pt-2">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <span class="badge bg-label-primary">{{ $item->classroom->typeClass->category }} {{ $item->classroom->name }}</span>
                                                <h6 class="d-flex align-items-center justify-content-center gap-1 mb-0">
                                                    <span
                                                        class="text-muted badge bg-label-info">{{ $item->classroom->students->count() }}
                                                        Students</span>
                                                </h6>
                                            </div>
                                            <h5 class="mb-2">{{ $item->course->name }}</h5>
                                            <p class="d-flex align-items-center mb-2">
                                                <i class="ti ti-clock me-2 mt-n1"></i>
                                                {{ \Carbon\Carbon::parse($item->start_time)->format('H:i') }} -
                                                {{ \Carbon\Carbon::parse($item->end_time)->format('H:i') }}
                                            </p>
                                            <p class="mb-0">
                                                {{ $item->description ?? 'Course details and learning objectives.' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <p class="text-muted">No classes scheduled for today.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-backdrop fade"></div>
    </div>

    <div class="modal fade" id="modal-permission" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title mx-auto" id="exampleModalLabel1">PERIZINAN</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('student.schedule_student.permission') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body row py-0" id="div-update">
                        <div class="col-12 col-md-12 my-4 text-center">
                            <label for="fileInput" class="file-upload d-flex flex-column align-items-center">
                                <div class="upload-icon mb-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48"
                                        fill="currentColor" class="bi bi-cloud-upload" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd"
                                            d="M8 0a5.53 5.53 0 0 1 4.481 2.136 3.5 3.5 0 1 1 .867 6.746A5.489 5.489 0 0 1 8 13a5.478 5.478 0 0 1-4.688-2.514A3.5 3.5 0 0 1 8 3.5a5.478 5.478 0 0 1-4.688 2.514A5.53 5.53 0 0 1 8 0zm0 1.5a4 4 0 0 0-3.292 6.36A3.478 3.478 0 0 1 8 10.5a3.478 3.478 0 0 1 3.292-2.64A4 4 0 0 0 8 1.5z" />
                                        <path
                                            d="M7.5 5.5v4.792l1.146-1.147a.5.5 0 0 1 .708.708l-2 2a.5.5 0 0 1-.708 0l-2-2a.5.5 0 1 1 .708-.708L7.5 10.293V5.5a.5.5 0 0 1 1 0z" />
                                    </svg>
                                </div>
                                <div class="upload-text">Drag & Drop or Click to Upload</div>
                                <input type="file" class="form-control-file d-none" id="fileInput" name="file"
                                    accept=".png, .jpg, .jpeg" onchange="previewFile()">
                                <div class="preview-container mt-3" id="previewContainer">
                                    <img id="previewImage" src="#" alt="File Preview" class="img-fluid"
                                        style="display: none; max-width: 100px;">
                                </div>
                            </label>
                            <p id="fileName" class="mt-2"></p>
                        </div>
                        <div class="col-12 col-md-12 mb-2">
                            <label class="form-label" for="keterangan">Keterangan</label>
                            <textarea class="form-control" id="keterangan" rows="3" name="description"></textarea>
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
@endsection

@section('js')
    <script>
        function previewFile() {
            const file = document.getElementById('fileInput').files[0];
            const previewImage = document.getElementById('previewImage');
            const fileName = document.getElementById('fileName');
            const previewContainer = document.getElementById('previewContainer');

            const reader = new FileReader();

            reader.onloadend = function() {
                previewImage.src = reader.result;
                previewImage.style.display = 'block';
                fileName.textContent = file.name;
            }

            if (file) {
                reader.readAsDataURL(file);
                previewContainer.style.display = 'block';
            } else {
                previewImage.style.display = 'none';
                fileName.textContent = '';
                previewContainer.style.display = 'none';
            }
        }
    </script>

    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="{{ asset('assets/js/app-academy-course.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/%40form-validation/umd/bundle/popular.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/%40form-validation/umd/plugin-bootstrap5/index.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/%40form-validation/umd/plugin-auto-focus/index.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/node-waves/node-waves.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/hammer/hammer.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/i18n/i18n.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/dropzone/dropzone.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>
    <script src="{{ asset('assets/js/forms-file-upload.js') }}"></script>
    <script src="https://cdn.datatables.net/2.0.2/js/dataTables.js"></script>

    <script>
        new DataTable('#table-content', {
            pagingType: 'simple_numbers'
        });
    </script>
@endsection
