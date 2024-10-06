@extends('admin.layouts.app')


@section('link')
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/app-logistics-dashboard.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/%40form-validation/umd/styles/index.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/animate-css/animate.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css') }}" />
    <link rel="stylesheet"
        href="{{ asset('assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/jquery-timepicker/jquery-timepicker.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/pickr/pickr-themes.css') }}" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
    <link href="https://raw.githack.com/ttskch/select2-bootstrap4-theme/master/dist/select2-bootstrap4.css"
        rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
@endsection

@section('content')
    <style>
        select {
            width: 100%;
            min-height: 100px;
            border-radius: 3px;
            border: 1px solid #444;
            padding: 10px;
            color: #444444;
            font-size: 14px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin: 20px 0;
            border: 1px solid #a4a4a4;
            border-radius: 10px;
            overflow: hidden;
            background-color: white;
        }

        th,
        td {
            border: 1px solid #ddd;
            text-align: center;
            padding: 8px;
        }

        th {
            background-color: #f4f4f4;
            font-weight: bold;
        }

        td {
            background-color: #f9f9f9;
        }

        tr:nth-child(even) td {
            background-color: #ffffff;
        }

        thead th {
            background-color: #f2f2f2;
        }

        tfoot td {
            font-weight: bold;
        }
    </style>

    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="container mt-4">
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="container-xxl flex-grow-1 container-p-y">
                            <div class="row mt-4">
                                <div class="col-12">
                                    <!-- Form untuk pencarian berdasarkan tanggal dan tipe kelas -->
                                    <form action="{{ route('admin.report.attendance_student.search') }}" method="get">
                                        <div class="row g-3 align-items-center">
                                            <!-- Kolom untuk input tanggal mulai dan tanggal akhir -->
                                            <div class="col-12 col-md-6 col-lg-4">
                                                <div class="input-group">
                                                    <input type="date" class="form-control" placeholder="Start Date"
                                                        name="start-date" required />
                                                    <span class="input-group-text">to</span>
                                                    <input type="date" class="form-control" placeholder="End Date"
                                                        name="end-date" required />
                                                </div>
                                            </div>

                                            <!-- Kolom untuk pilihan tipe kelas -->
                                            <div class="col-12 col-md-6 col-lg-4">
                                                <select class="select form-select" name="states[]" multiple
                                                    aria-label="Select Type Classes">
                                                    @foreach ($types as $item)
                                                        <option value="{{ $item->id }}">{{ $item->category }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <!-- Tombol submit -->
                                            <div class="col-12 col-lg-4">
                                                <button type="submit" class="btn btn-success w-100">
                                                    <span class="d-none d-sm-inline-block">Search</span>
                                                </button>
                                            </div>
                                        </div>
                                    </form>

                                    <!-- Form untuk ekspor laporan berdasarkan bulan -->
                                    <form action="{{ route('admin.export.attendance_report.month') }}" method="POST"
                                        class="mt-4">
                                        @csrf
                                        <div class="row g-3 align-items-center">
                                            <!-- Kolom untuk memilih bulan -->
                                            <div class="col-12 col-md-6 col-lg-4">
                                                <label for="month" class="form-label">Select Month</label>
                                                <input type="month" id="month" name="month" class="form-control"
                                                    required>
                                            </div>

                                            <!-- Kolom untuk memilih format ekspor -->
                                            <div class="col-12 col-md-6 col-lg-4">
                                                <label for="format" class="form-label">Export Format</label>
                                                <select name="format" id="format" class="select form-select" required>
                                                    <option value="pdf">PDF</option>
                                                    <option value="excel">Excel</option>
                                                    <option value="student_all">All Student</option>
                                                </select>
                                            </div>

                                            <!-- Tombol submit -->
                                            <div class="col-12 col-lg-4">
                                                <button type="submit" class="btn btn-primary w-100">
                                                    Export Report
                                                </button>
                                            </div>
                                        </div>
                                    </form>

                                    <!-- Form untuk update laporan kehadiran -->
                                    <form id="form-update" method="POST"
                                        action="{{ route('admin.update.attendance.student.report') }}">
                                        @csrf
                                        <div class="row g-3 align-items-center modal-body py-0" id="div-update">
                                            <!-- Pilihan Siswa -->
                                            <div class="col-12 col-md-6 mb-1">
                                                <label class="form-label">Student</label>
                                                <select class="form-control select" name="student_id" id="student-select"
                                                    required>
                                                    <option selected disabled>Pilih Siswa</option>
                                                    @foreach ($student as $item)
                                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <!-- Input tanggal pembayaran -->
                                            <div class="col-12 col-md-6 mb-1">
                                                <label class="form-label" for="payment-date">Tanggal Absensi</label>
                                                <input type="date" class="form-control" id="payment-date"
                                                    name="payment_date" required />
                                            </div>
                                        </div>

                                        <div class="table-responsive mb-1">
                                            <table class="table table-custom" id="table-content">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">STUDENT ID</th>
                                                        <th>NAMA</th>
                                                        <th class="text-center">PRESENSI</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="modal-footer">
                                            <button class="btn btn-primary">Save changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="row my-5">
                            <div class="col-12 order-5">
                                @if ($report != null)
                                    <div class="py-3">
                                        <div class="container">
                                            @foreach ($classroom as $class)
                                                <h2 class="mt-4">{{ $class->typeClass->category }} {{ $class->name }}
                                                </h2>
                                                <div class="table-responsive text-nowrap custom-border">
                                                    <table class="table table-bordered">
                                                        <thead class="thead-dark">
                                                            <tr>
                                                                <th rowspan="2">NAMA SISWA</th>
                                                                <th rowspan="2">L/P</th>
                                                                <th
                                                                    colspan="{{ Carbon\CarbonPeriod::create($startDate, $endDate)->count() }}">
                                                                    TANGGAL*</th>
                                                                <th colspan="3">JUM. KTDHDRN**</th>
                                                                <th rowspan="2">POIN TATIB</th>
                                                                <th rowspan="2">KET</th>
                                                            </tr>
                                                            <tr>
                                                                @foreach (Carbon\CarbonPeriod::create($startDate, $endDate) as $date)
                                                                    <th>{{ $date->format('d') }}</th>
                                                                @endforeach
                                                                <th>S</th>
                                                                <th>I</th>
                                                                <th>A</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @php $no = 1; @endphp
                                                            @foreach ($report as $studentId => $studentReport)
                                                                @if ($studentReport['class'] == $class->typeClass->category . ' ' . $class->name)
                                                                    <tr>
                                                                        <td>{{ $studentReport['name'] }}</td>
                                                                        <td>{{ $studentReport['gender'] }}</td>
                                                                        @foreach ($studentReport['attendance'] as $date => $attendance)
                                                                            <td class="editable"
                                                                                data-student-id="{{ $studentId }}"
                                                                                data-date="{{ $date }}"
                                                                                data-times="{{ json_encode($attendance['times']) }}">
                                                                                {{ $attendance['status'] }}
                                                                            </td>
                                                                        @endforeach
                                                                        <td>{{ number_format($studentReport['total_sakit'] * 0.1, 1) }}
                                                                        </td>
                                                                        <td>{{ number_format($studentReport['total_izin'] * 0.1, 1) }}
                                                                        </td>
                                                                        <td>{{ number_format($studentReport['total_alpha'] * 0.1, 1) }}
                                                                        </td>
                                                                        <td>{{ number_format($studentReport['total_points'] * 0.1, 1) }}
                                                                        </td>
                                                                        <td>{{ $studentReport['warning'] }}</td>
                                                                    </tr>
                                                                @endif
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <div class="d-flex justify-content-center align-items-center my-5">
                                        <img src="{{ asset('assets/content/empty.svg') }}" width="300"
                                            alt="No Data Available">
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="content-backdrop fade"></div>
                </div>

                <div class="modal fade" id="modal-student_attendance-update" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header mb-2 py-3" style="background: rgba(56, 42, 214, 0.9);">
                                <h5 class="modal-title mx-auto" style="color: rgb(246, 246, 246);"
                                    id="exampleModalLabel1">Student
                                    Payment</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <form id="form-update" method="POST"
                                action="{{ route('admin.update.attendance.student.report') }}">
                                @csrf
                                <div class="modal-body row py-0" id="div-update">
                                    <div class="mb-1">
                                        <label class="form-label">Student</label>
                                        <select class="form-select" name="student_id" id="student-select" required>
                                            <option selected disabled>Pilih Siswa</option>
                                            @foreach ($student as $item)
                                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-1">
                                        <label class="form-label" for="payment-date">Payment Date</label>
                                        <input type="date" class="form-control" id="payment-date" name="payment_date"
                                            required />
                                    </div>
                                </div>

                                <div>
                                    <table class="table table-custom" id="table-content">
                                        <thead>
                                            <tr>
                                                <th>NAMA</th>
                                                <th class="text-center">PRESENSI</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-label-danger"
                                        data-bs-dismiss="modal">Close</button>
                                    <button class="btn btn-primary">Save changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endsection

            @section('js')
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const studentSelect = document.getElementById('student-select');
                        const paymentDate = document.getElementById('payment-date');

                        // Add event listeners to both the student select and payment date input
                        studentSelect.addEventListener('change', fetchAttendanceData);
                        paymentDate.addEventListener('change', fetchAttendanceData);

                        function fetchAttendanceData() {
                            const studentId = studentSelect.value;
                            const date = paymentDate.value;

                            // Only trigger the request if both fields have values
                            if (studentId && date) {
                                axios.get('{{ route('admin.attendance.student.report') }}', {
                                        params: {
                                            student_id: studentId,
                                            payment_date: date
                                        }
                                    })
                                    .then(function(response) {
                                        // Ensure response contains data
                                        if (response.data && response.data.data) {
                                            // Clear the existing table content
                                            const tbody = document.querySelector('#table-content tbody');
                                            tbody.innerHTML = '';

                                            const data = response.data.data;

                                            if (data.length > 0) {
                                                data.forEach(function(attendance) {
                                                    const row = `
                <tr>
<td>${attendance.student?.name ? attendance.student.name : ''}</td>
                    <td>
                        <div class="col-md d-flex align-items-center flex-wrap gap-2 justify-content-center">
                            <input type="hidden" name="attendance[${attendance.id}]" value="present">

                            <div class="form-check form-check-success">
                                <input name="attendance[${attendance.id}]"
                                    class="form-check-input" type="radio"
                                    value="present"
                                    id="present_${attendance.id}"
                                    ${attendance.status === 'present' ? 'checked' : ''} />
                                <label class="form-check-label" for="present_${attendance.id}"> Hadir </label>
                            </div>

                            <div class="form-check form-check-danger">
                                <input name="attendance[${attendance.id}]"
                                    class="form-check-input" type="radio"
                                    value="alpha"
                                    id="alpha_${attendance.id}"
                                    ${attendance.status === 'alpha' ? 'checked' : ''} />
                                <label class="form-check-label" for="alpha_${attendance.id}"> Alpha </label>
                            </div>

                            <div class="form-check form-check-warning">
                                <input name="attendance[${attendance.id}]"
                                    class="form-check-input" type="radio"
                                    value="permission"
                                    id="izin_${attendance.id}"
                                    ${attendance.status === 'permission' ? 'checked' : ''} />
                                <label class="form-check-label" for="izin_${attendance.id}"> Izin </label>
                            </div>

                            <div class="form-check form-check-alpha">
                                <input name="attendance[${attendance.id}]"
                                    class="form-check-input" type="radio"
                                    value="sick"
                                    id="sakit_${attendance.id}"
                                    ${attendance.status === 'sick' ? 'checked' : ''} />
                                <label class="form-check-label" for="sakit_${attendance.id}"> Sakit </label>
                            </div>
                        </div>
                    </td>
                </tr>
            `;
                                                    tbody.insertAdjacentHTML('beforeend', row);
                                                });
                                            } else {
                                                // If no data, show a message in the table
                                                const row = `
            <tr>
                <td colspan="3" class="text-center">No attendance data available for this date.</td>
            </tr>
        `;
                                                tbody.insertAdjacentHTML('beforeend', row);
                                            }
                                        }

                                    })
                                    .catch(function(error) {
                                        console.error('Error fetching attendance data:', error);
                                    });
                            }
                        }
                    });
                </script>

                <script>
                    $(document).ready(function() {
                        $('.editable').on('click', function() {
                            var currentElement = $(this);
                            var originalContent = currentElement.text();
                            var studentId = currentElement.data('student-id');
                            var date = currentElement.data('date');

                            var input = $('<input>', {
                                type: 'text',
                                value: originalContent,
                                blur: function() {
                                    var newContent = input.val();
                                    currentElement.text(newContent);
                                    saveChanges(studentId, date, newContent);
                                },
                                keyup: function(e) {
                                    if (e.which === 13) {
                                        input.blur();
                                    }
                                }
                            }).appendTo(currentElement.empty()).focus();
                        });

                        function saveChanges(studentId, date, content) {
                            console.log('testing');
                            console.log(studentId);
                            console.log(date);
                            console.log(content);
                            axios.post("{{ route('admin.report.attendance_student.update') }}", {
                                    student_id: studentId,
                                    date: date,
                                    content: content,
                                    _token: '{{ csrf_token() }}'
                                })
                                .then(function(response) {
                                    console.log('Response:', response);
                                    console.log('Response:', response.data);
                                    if (response.data.success) {
                                        console.log('Response:', response);
                                        alert('Kehadiran berhasil diperbarui!');
                                    }
                                })
                                .catch(function(error) {
                                    console.log('Response:', error);
                                    console.error('Error updating attendance:', error);
                                });
                        }
                    });
                </script>

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
            @endsection
