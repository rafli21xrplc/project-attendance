<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed customizer-hide" dir="ltr" data-theme="theme-default">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PRESENCE 8</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('assets/content/icon-load.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/tabler-iconsea04.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/fontawesome8a69.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/flag-icons80a8.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/core6cc1.css') }}"
        class="template-customizer-core-css">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/theme-defaultfc79.css') }}"
        class="template-customizer-theme-css">
    <link rel="stylesheet" href="{{ asset('assets/css/demof1ed.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/node-waves/node-wavesd178.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar7358.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/typeahead-js/typeaheadb5e1.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/%40form-validation/umd/styles/index.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/datatable.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/template-customizer.js') }}"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


    <style>
        * {
            font-size: 12px;
        }

        .loader-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .loader {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100px;
            height: 100px;
            border: 10px solid rgba(255, 255, 255, 0.1);
            border-top: 10px solid #ffffff;
            border-radius: 50%;
            animation: spin 1.5s linear infinite;
        }

        .logo {
            position: absolute;
            width: 120px;
            height: 120px;
            background: url('{{ asset('assets/content/icon-load.png') }}') no-repeat center center;
            background-size: contain;
            animation: pulse 2s infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.2);
            }

            100% {
                transform: scale(1);
            }
        }
    </style>
    <link rel="preload" href="{{ asset('assets/content/icon-load.png') }}" as="image">
</head>

<body>
    <div class="loader-overlay" id="loader">
        <div class="loader"></div>
        <div class="logo"></div>
    </div>

    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                <div class="app-brand demo mt-3">
                    <a href="#" class="app-brand-link">
                        <span class="demo">
                            <img src="{{ asset('assets/content/logo-name.png') }}" height="50" alt="Logo">
                        </span>
                    </a>
                    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
                        <i class="ti ti-x d-block d-xl-none ti-sm align-middle"></i>
                    </a>
                </div>

                <div class="menu-inner-shadow"></div>
                <ul class="menu-inner py-1">
                    <li class="menu-header small text-uppercase">
                        <span class="menu-header-text">Main Features</span>
                    </li>
                    <li class="menu-item">
                        <a href="{{ route('student.dashboard_student') }}" class="menu-link">
                            <i class="menu-icon tf-icons ti ti-smart-home"></i>
                            <div>Dashboard</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ route('student.schedule_student.index') }}" class="menu-link">
                            <div class="menu-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="icon icon-tabler icon-tabler-clipboard-data">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path
                                        d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2">
                                    </path>
                                    <path
                                        d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z">
                                    </path>
                                    <path d="M9 17v-4"></path>
                                    <path d="M12 17v-1"></path>
                                    <path d="M15 17v-2"></path>
                                    <path d="M12 17v-1"></path>
                                </svg>
                            </div>
                            <div>Pelajaran</div>
                        </a>
                    </li>

                    @if (isset($qrCode))
                        <li class="menu-item">
                            <a class="menu-link btn-qr-code">
                                <div class="menu-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="icon icon-tabler icon-tabler-clipboard-data">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path
                                            d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2">
                                        </path>
                                        <path
                                            d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z">
                                        </path>
                                        <path d="M9 17v-4"></path>
                                        <path d="M12 17v-1"></path>
                                        <path d="M15 17v-2"></path>
                                        <path d="M12 17v-1"></path>
                                    </svg>
                                </div>
                                <div>QR Code UJIAN</div>
                            </a>
                        </li>
                    @endif

                </ul>
            </aside>

            <div class="layout-page">
                <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
                    id="layout-navbar">
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                            <i class="ti ti-menu-2 ti-sm"></i>
                        </a>
                    </div>

                    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                        <div class="navbar-nav">
                            <div class="d-flex justify-content-center align-items-center">
                                <span style="font-size: 20px">Dashboard</span>
                            </div>
                        </div>

                        <ul class="navbar-nav flex-row align-items-center ms-auto">
                            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);"
                                    data-bs-toggle="dropdown">
                                    <div class="avatar avatar-online">
                                        <img src="{{ asset('assets/content/hello-robot.gif') }}" alt="User Avatar"
                                            class="h-auto rounded-circle">
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 me-3">
                                                    <div class="avatar avatar-online">
                                                        <img src="{{ asset('assets/content/hello-robot.gif') }}"
                                                            alt="User Avatar" class="h-auto rounded-circle" />
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <span
                                                        class="fw-medium d-block">{{ Auth::user()->student->name }}</span>
                                                    <small class="text-muted">student</small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="change-password-modal" data-bs-toggle="modal"
                                            data-bs-target="#change-password-modal">
                                            <i class="ti ti-settings me-2 ti-sm"></i>
                                            <span class="align-middle">change password</span>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('logout') }}"
                                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            <i class="ti ti-login me-2"></i>
                                            <span class="align-middle">{{ __('Logout') }}</span>
                                        </a>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                            class="d-none">
                                            @csrf
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>

                    <div class="navbar-search-wrapper search-input-wrapper d-none">
                        <input type="text" class="form-control search-input container-xxl border-0"
                            placeholder="Search..." aria-label="Search...">
                        <i class="ti ti-x ti-sm search-toggler cursor-pointer"></i>
                    </div>
                </nav>

                @include('validation.error_message')
                @include('validation.delete_modal')
                @include('validation.response_message')
                @include('sweetalert::alert')
                @yield('content')
            </div>
        </div>

        <div class="layout-overlay layout-menu-toggle"></div>
        <div class="drag-target"></div>
    </div>


    <div class="modal fade" id="change-password-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title mx-auto my-1" id="exampleModalLabel1">Change Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('change.password') }}" method="POST">
                    @csrf
                    <div class="modal-body row py-0">
                        <div class="mb-3">
                            <label class="form-label" for="current-password">Current Password</label>
                            <input type="password" class="form-control" id="current-password"
                                name="current_password" placeholder="Enter current password" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="new-password">New Password</label>
                            <input type="password" class="form-control" id="new-password" name="new_password"
                                placeholder="Enter new password" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="new-password-confirmation">Confirm New Password</label>
                            <input type="password" class="form-control" id="new-password-confirmation"
                                name="new_password_confirmation" placeholder="Confirm new password" required>
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

    <!-- Modal for QR Code Exam Login -->
    <div class="modal fade" id="qrCodeExamModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-simple">
            <div class="modal-content p-3 p-md-5">
                <div class="modal-body">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="text-center mb-4">
                        <h3 class="mb-3">Exam Login QR Code</h3>
                        <p class="mb-4">Using an app like Google Authenticator, Microsoft Authenticator, or any QR
                            code scanner, scan the QR code. It will contain your exam login details (username and
                            password).</p>
                        <div id="qrCodeContainer" class="mb-3">
                            {{ $qrCode ?? '' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('load', function() {
            const loader = document.getElementById('loader');
            loader.style.display = 'none';
        });
    </script>

    <script>
        $('.btn-qr-code').click(function() {

            $('#qrCodeExamModal').modal('show');
        });
    </script>

    <script src="{{ asset('assets/vendor/libs/jquery/jquery1e84.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/popper/popper0a73.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstraped84.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/node-waves/node-waves259f.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar6188.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/hammer/hammer2de0.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/typeahead-js/typeahead60e7.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/menu2dc9.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/%40form-validation/umd/bundle/popular.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/%40form-validation/umd/plugin-bootstrap5/index.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/%40form-validation/umd/plugin-auto-focus/index.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/js/mainf696.js?id=8bd0165c1c4340f4d4a66add0761ae8a') }}"></script>

    @yield('js')
</body>

</html>
