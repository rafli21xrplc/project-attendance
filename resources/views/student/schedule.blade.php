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
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">

            <div class="app-academy">
                <div class="card p-0 mb-4">
                    <div class="card-body d-flex flex-column flex-md-row justify-content-between p-0 pt-4">
                        <div class="app-academy-md-25 card-body py-0">
                            <img src="{{ asset('assets/img/illustrations/bulb-light.png') }}"
                                class="img-fluid app-academy-img-height scaleX-n1-rtl" alt="Bulb in hand"
                                data-app-light-img="illustrations/bulb-light.png"
                                data-app-dark-img="illustrations/bulb-dark.png" height="90" />
                        </div>
                        <div class="app-academy-md-50 card-body d-flex align-items-md-center flex-column text-md-center">
                            <h3 class="card-title mb-4 lh-sm px-md-5 lh-lg">
                                Education, talents, and career opportunities.
                                <span class="text-primary fw-medium text-nowrap">All in one place</span>.
                            </h3>
                            <p class="mb-3">
                                Grow your skill with the most reliable online courses
                                and certifications in marketing, information technology,
                                programming, and data science.
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
                            <h5 class="mb-1">My Classroom</h5>
                            <p class="text-muted mb-0">
                                Total 6 course you have purchased
                            </p>
                        </div>
                        <div class="d-flex flex-column flex-md-row text-nowrap justify-content-end">
                            <button class="btn btn-label-warning d-flex align-items-center" href=""
                                data-bs-toggle="modal" data-bs-target="#modal-permission" type="button">
                                <span class="me-2">Izin</span><i class="ti ti-chevron-right scaleX-n1-rtl ti-sm"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row gy-4 mb-4">
                            @forelse ($schedule as $item)
                                <div class="col-sm-6 col-lg-4">
                                    <div class="card p-2 h-100 shadow-none border">
                                        <div class="rounded-2 text-center mb-3">
                                            <a href="course-details.html"><img class="img-fluid"
                                                    src="{{ asset('assets/content/classroom_bg.png') }}"
                                                    alt="tutor image 1" /></a>
                                        </div>
                                        <div class="card-body p-3 pt-2">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <span class="badge bg-label-primary">{{ $item->classroom->name }}</span>
                                                <h6 class="d-flex align-items-center justify-content-center gap-1 mb-0">
                                                    <span
                                                        class="text-muted badge bg-label-info">{{ $item->classroom->students->count() }}
                                                        Siswa</span>
                                                </h6>
                                            </div>
                                            <a href="course-details.html" class="h5">{{ $item->course->name }}</a>
                                            <p class="mt-2">
                                                Introductory course for Angular and framework
                                                basics in web development.
                                            </p>
                                            <p class="d-flex align-items-center">
                                                <i class="ti ti-clock me-2 mt-n1"></i>30 minutes
                                            </p>
                                            <div class="d-flex flex-column flex-md-row text-nowrap justify-content-end">
                                                <a class="btn btn-label-primary d-flex align-items-center"
                                                    href="{{ route('teacher.attendance', ['classroomid' => $item->classroom->id, 'scheduleId' => $item->id]) }}">
                                                    <span class="me-2">Absensi</span><i
                                                        class="ti ti-chevron-right scaleX-n1-rtl ti-sm"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
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
                <div class="modal-header mb-2 py-3" style="background: rgba(56, 42, 214, 0.9);">
                    <h5 class="modal-title mx-auto" style="color: rgb(246, 246, 246);" id="exampleModalLabel1">PERIZINAN
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('student.schedule_student.permission') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body row py-0" id="div-update">
                        <div class="col-12 col-md-12 my-4">
                            <div class="col-12" style="display: none;">
                                <div class="card mb-4">
                                    <div class="card-body">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div  class="dropzone needsclick"
                                    id="dropzone-basic">
                                    <div class="dz-message needsclick">
                                        Upload foto surat izin
                                    </div>
                                    <div class="fallback">
                                        <input name="file" type="file" />
                                    </div>
                                </div>
                            </div>
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
    Dropzone.autoDiscover = false;

var myDropzone = new Dropzone("#dropzone-basic", {
    url: "{{ route('student.schedule_student.permission') }}",
    paramName: "file", // The name that will be used to transfer the file
    maxFilesize: 2, // MB
    addRemoveLinks: true,
    autoProcessQueue: false, // Prevent auto processing of files

    init: function() {
        var myDropzone = this;

        // When the form is submitted
        $("form").on("submit", function(e) {
            e.preventDefault();
            e.stopPropagation();

            if (myDropzone.getQueuedFiles().length > 0) {
                // Process the queued files
                myDropzone.processQueue();
            } else {
                // No files to process, so submit the form immediately
                this.submit();
            }
        });

        // On success, submit the form
        myDropzone.on("success", function(file, response) {
            if (myDropzone.getQueuedFiles().length === 0 && myDropzone.getUploadingFiles().length === 0) {
                $("form")[0].submit();
            }
        });
    }
});

</script>
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
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.0.2/js/dataTables.js"></script>

    <script>
        new DataTable('#table-content', {
            pagingType: 'simple_numbers'
        });
    </script>
@endsection
