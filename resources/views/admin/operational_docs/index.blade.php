@extends('adminlte::page')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>Operational Docs List</h1>
            <ol class="breadcrumb">
                <li><a href="/home"><i class="fa fa-dashboard"></i> Home </a></li>
                <li class="active"> Operational Docs </li>
            </ol>
        </section>

        <section class="content">
            @include('includes.messages')
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <button type="button" class="btn btn-primary btn-sm btn-flat" data-toggle="modal" data-target="#addnewoperational_doc">
                                <i class="fa fa-plus"></i> New Operational Doc
                            </button>
                        </div>
                        <div class="box-body">
                            <table id="example1" class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>File</th>
                                    <th>Project ID</th>
                                    <th>Project Name</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($operational_docs as $operational_doc)
                                    <tr>
                                        <td>{{ $operational_doc->getAttribute('id') }}</td>
                                        <td>{{ $operational_doc->getAttribute('file') }}</td>
                                        <td>{{ $operational_doc->getAttribute('project_id') }}</td>
                                        <td>{{ $operational_doc->project->name }}</td>
                                        <td>
                                            <a href="{{ route('operational_docs.download', $operational_doc) }}" class="btn btn-primary btn-sm btn-flat">
                                                <i class="fas fa-download"></i> Download
                                            </a>
                                            <button type="button" class="btn btn-success btn-sm edit btn-flat" data-toggle="modal" data-target="#edit{{$operational_doc->id}}">
                                                <i class='fa fa-edit'></i> Edit
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm delete btn-flat" data-toggle="modal" data-target="#delete{{$operational_doc->id}}">
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
    @include('admin.operational_docs.modals.add')

    <!-- Edit/Delete Modals -->
    @foreach($operational_docs as $operational_doc)
        @include('admin.operational_docs.modals.edit_delete')
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
