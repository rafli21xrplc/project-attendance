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
@endsection

@section('content')
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            margin: 20px 0;
            border: 1px solid #a4a4a4;
            border-radius: 10px;
            background-color: white;
            overflow: hidden;
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
                <div class="col-12 order-5">
                    <form action="{{ route('admin.SIA.search') }}" method="get">
                        <div class="row justify-content-end">
                            <div class="col-md-2">
                                <a href="{{ route('admin.SIA.export') }}" class="btn btn-primary w-100">
                                    <span class="d-none d-sm-inline-block">Export</span>
                                </a>
                            </div>
                            <div class="col-md-3">
                                <select class="js-example-basic-multiple" name="states[]" multiple="multiple"
                                    style="width: 100%;">
                                    @foreach ($types as $typeClass)
                                        <optgroup label="{{ $typeClass->category }}">
                                            @foreach ($typeClass->classRooms as $classRoom)
                                                <option value="{{ $classRoom->id }}">{{ $classRoom->name }}</option>
                                            @endforeach
                                        </optgroup>
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
                    @if (!$report == null)
                        <div class="table-responsive text-nowrap custom-border">
                            @foreach ($report as $className => $students)
                                <h2>{{ $className }}</h2>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th rowspan="2">Nama Siswa</th>
                                            <th rowspan="2">L/P</th>
                                            @foreach ($months as $month)
                                                <th colspan="3">
                                                    {{ \Carbon\Carbon::parse($month)->translatedFormat('F Y') }}
                                                </th>
                                            @endforeach
                                            <th colspan="3">Jum. KTDHDRN</th>
                                            <th rowspan="2">Poin Tatib</th>
                                            <th rowspan="2">Keterangan</th>
                                        </tr>
                                        <tr>
                                            @foreach ($months as $month)
                                                <th>S</th>
                                                <th>I</th>
                                                <th>A</th>
                                            @endforeach
                                            <th>S</th>
                                            <th>I</th>
                                            <th>A</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($students as $index => $student)
                                            <tr>
                                                <td>{{ $student['name'] }}</td>
                                                <td>{{ $student['gender'] }}</td>
                                                @foreach ($months as $month)
                                                    <td>{{ $student['months'][$month]['sick'] }}</td>
                                                    <td>{{ $student['months'][$month]['permission'] }}</td>
                                                    <td>{{ $student['months'][$month]['alpha'] }}</td>
                                                @endforeach
                                                <td>{{ array_sum(array_column($student['months'], 'sick')) }}</td>
                                                <td>{{ array_sum(array_column($student['months'], 'permission')) }}</td>
                                                <td>{{ array_sum(array_column($student['months'], 'alpha')) }}</td>
                                                <td>{{ number_format($student['total_tatib_points'], 1) }}</td>
                                                <td>{{ $student['warning'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endforeach
                        </div>
                    @else
                        <div class="d-flex justify-content-center align-items-center my-5">
                            <img src="{{ asset('assets/content/empty.svg') }}" width="300" alt="No Data Available">
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="content-backdrop fade"></div>

@endsection

@section('js')
    <script>
        console.log('testing')
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
