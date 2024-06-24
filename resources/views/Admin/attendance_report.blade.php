@extends('Admin.layouts.app')

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
            <div class="row mt-4">
                <div class="col-12 order-5 ">
                    <form action="{{ route('admin.report.attendance_student.search') }}" method="get">
                        <div class="row justify-content-end align-items-center">
                            <div class="col-md-2">
                                <a href="{{ route('admin.export.attendance_report.pdf') }}" class="btn btn-warning w-100">
                                    <span class="d-none d-sm-inline-block">Export Pdf</span>
                                </a>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('admin.export.attendance_report.excel') }}" class="btn btn-primary w-100">
                                    <span class="d-none d-sm-inline-block">Export Excel</span>
                                </a>
                            </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control" placeholder="YYYY-MM-DD to YYYY-MM-DD"
                                    name="range-date" id="flatpickr-range" />
                            </div>
                            <div class="col-md-3">
                                <select class="js-example-basic-multiple" name="states[]" multiple="multiple"
                                    style="width: 100%;">
                                    @foreach ($classrooms as $item)
                                        <option value="{{ $item->id }}">{{ $item->typeClass->category }}
                                            {{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-success w-100">
                                    <span class="d-none d-sm-inline-block">Search</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row my-5">
                <div class="col-12 order-5">
                    @if ($report != null)
                        <div class="py-3">
                            <div class="container">
                                @foreach ($classroom as $class)
                                    <h2 class="mt-4">{{ $class->typeClass->category }} {{ $class->name }}</h2>
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
                                                                <td class="editable" data-student-id="{{ $studentId }}"
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
                                                            <td>{{ number_format($studentReport['total_points'], 1) }}</td>
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
                            <img src="{{ asset('assets/content/empty.svg') }}" width="300" alt="No Data Available">
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="content-backdrop fade"></div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            $('.editable').on('click', function() {
                var currentElement = $(this);
                var originalContent = currentElement.text();
                var studentId = currentElement.data('student-id');
                var date = currentElement.data('date');
                // var attendances = currentElement.data('times');

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
                console.log(studentId);
                console.log(date);
                console.log(content);
                axios.post("{{ route('admin.report.attendance_student.update') }}", {
                        student_id: studentId,
                        date: date,
                        content: content,
                        // attendances: attendances,
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
            $('select').each(function() {
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
