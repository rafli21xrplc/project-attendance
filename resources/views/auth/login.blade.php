@extends('auth.layouts.app')

@section('content')
    <style>
        #btn-login {
            background-color: rgb(202, 0, 0);
            color: whitesmoke;
        }

        #btn-login:hover {
            background-color: rgb(212, 0, 0)
        }

        #btn-login:active {
            background-color: rgb(208, 0, 0)
        }
    </style>

    <div class="authentication-inner row">
        <div class="d-none d-lg-flex col-lg-7 p-0">
            <div class="auth-cover-bg auth-cover-bg-color d-flex justify-content-center align-items-center">
                <img width="500" src="{{ asset('assets/content/icon.png') }}" alt="LOGO" srcset="">
            </div>
        </div>

        <div class="d-flex col-12 col-lg-5 align-items-center p-sm-5 p-4">
            <div class="w-px-400 mx-auto">
                <h3 class=" mb-1">Welcome to Presence8!</h3>
                <p class="mb-4">Please sign-in to your account</p>

                <form id="formAuthentication" class="mb-3" action="{{ route('login') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username"
                            placeholder="Enter your username" autofocus>
                    </div>
                    <div class="mb-3 form-password-toggle">
                        <div class="input-group input-group-merge">
                            <input type="password" id="password" class="form-control" name="password"
                                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                aria-describedby="password" />
                            <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                        </div>
                    </div>
                    <button id="btn-login" type="submit" class="btn  d-grid w-100">
                        Sign in
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
