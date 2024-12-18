@extends('adminlte::page')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>Rates List</h1>
            <ol class="breadcrumb">
                <li><a href="/home"><i class="fa fa-dashboard"></i> Home </a></li>
                <li class="active"> Rates </li>
            </ol>
        </section>

        <section class="content">
            @include('includes.messages')
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <button type="button" class="btn btn-primary btn-sm btn-flat" data-toggle="modal" data-target="#addnewrate">
                                <i class="fa fa-plus"></i> New Rate
                            </button>
                        </div>
                        <div class="box-body">
                            <table id="example1" class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($rates as $rate)
                                    <tr>
                                        <td>{{ $rate->getAttribute('id') }}</td>
                                        <td>{{ $rate->getAttribute('name') }}</td>
                                        <td>
                                            <button type="button" class="btn btn-success btn-sm edit btn-flat" data-toggle="modal" data-target="#edit{{$rate->id}}">
                                                <i class='fa fa-edit'></i> Edit
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm delete btn-flat" data-toggle="modal" data-target="#delete{{$rate->id}}">
                                                <i class='fa fa-trash'></i> Delete
                                            </button>
                                        </td>
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

    <!-- Modals -->
    @include('admin.rates.modals.add')

    <!-- Edit/Delete Modals -->
    @foreach($rates as $rate)
        @include('admin.rates.modals.edit_delete')
    @endforeach
@endsection


<style>
    .btn-flat {
        margin-right: 5px;
    }
</style>


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
