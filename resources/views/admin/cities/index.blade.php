@extends('adminlte::page')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>Cities List</h1>
            <ol class="breadcrumb">
                <li><a href="/home"><i class="fa fa-dashboard"></i> Home </a></li>
                <li class="active"> Cities </li>
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
                                    <th>City</th>
                                    <th>State</th>
                                    <th>Country</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($cities as $city)
                                    <tr>
                                        <td>{{ $city->getAttribute('id') }}</td>
                                        <td>{{ $city->getAttribute('city') }}</td>
                                        <td>{{ $city->state->state_name }}</td>
                                        <td>{{ $city->state->country->country_name }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="box-footer clearfix">
                            <div class="pagination-wrapper">
                                {{ $cities->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('js')
    <script>
        $(function() {
            $('#example1').DataTable({
                "paging": false,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false
            });

            @if(session('success'))
            toastr.success("{{ session('success') }}");
            @endif
        });
    </script>
@stop
