@extends('adminlte::auth.auth-page', ['auth_type' => 'login'])

@section('adminlte_css_pre')
    <link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">
@stop

@section('auth_header', __('adminlte::adminlte.login_message'))

@section('auth_body')
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="text-center">
        <a href="{{ route('saml.login') }}" class="btn btn-primary btn-block btn-flat">
            <i class="fas fa-microsoft"></i> Login with Microsoft 365
        </a>
    </div>
@stop
