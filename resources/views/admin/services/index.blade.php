@extends('adminlte::page')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>Services List</h1>
            <ol class="breadcrumb">
                <li><a href="/home"><i class="fa fa-dashboard"></i> Home </a></li>
                <li class="active"> Services </li>
            </ol>
        </section>

        <section class="content">
            @include('includes.messages')
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <button type="button" class="btn btn-primary btn-sm btn-flat" data-toggle="modal" data-target="#addnewservice">
                                <i class="fa fa-plus"></i> New Service
                            </button>
                        </div>
                        <div class="box-body">
                            <table id="example1" class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Product</th>
                                    <th>Code</th>
                                    <th>Description</th>
                                    <th>Price</th>
                                    <th>Rate</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($services as $service)
                                    <tr>
                                        <td>{{ $service->getAttribute('id') }}</td>
                                        <td>{{ $service->getAttribute('product') }}</td>
                                        <td>{{ $service->getAttribute('code') }}</td>
                                        <td>{{ $service->getAttribute('description') }}</td>
                                        <td>{{ $service->getAttribute('price') }}</td>
                                        <td>{{ $service->rate->name }}</td>
                                        <td>
                                            <button type="button" class="btn btn-success btn-sm edit btn-flat" data-toggle="modal" data-target="#edit{{$service->id}}">
                                                <i class='fa fa-edit'></i> Edit
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm delete btn-flat" data-toggle="modal" data-target="#delete{{$service->id}}">
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
    @include('admin.services.modals.add')

    <!-- Edit/Delete Modals -->
    @foreach($services as $service)
        @include('admin.services.modals.edit_delete')
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
