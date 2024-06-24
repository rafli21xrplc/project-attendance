@extends('auth.layouts.app')

@section('content')
<div class="container text-center">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-12 col-md-8">
            <div>
                <img src="{{ asset('assets/content/error.png') }}" alt="" width="200" srcset="">
            </div>
            <div>
                <h4 style="color: rgb(144, 144, 144)">error 500 status code</h4>
            </div>
        </div>
    </div>
</div>
@endsection