@extends('adminlte::page')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>States List</h1>
            <ol class="breadcrumb">
                <li><a href="/home"><i class="fa fa-dashboard"></i> Home </a></li>
                <li class="active"> States </li>
            </ol>
        </section>

        <section class="content">
            @include('includes.messages')
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-body">
                            <table id="example1" class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>State</th>
                                    <th>Country</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($states as $state)
                                    <tr>
                                        <td>{{ $state->getAttribute('id') }}</td>
                                        <td>{{ $state->getAttribute('state_name') }}</td>
                                        <td>{{ $state->country->country_name }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </section>
    </div>
    <div class="box-footer clearfix">
        <div class="d-flex justify-content-center">
            {{ $states->links() }}
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(function() {
            $('#example1').DataTable();

            @if(session('success'))
            toastr.success("{{ session('success') }}");
            @endif
        });
    </script>
@stop
