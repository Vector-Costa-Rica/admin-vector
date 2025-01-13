@extends('adminlte::page')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>Tech Docs List</h1>
            <ol class="breadcrumb">
                <li><a href="/home"><i class="fa fa-dashboard"></i> Home </a></li>
                <li class="active"> Tech Docs </li>
            </ol>
        </section>

        <section class="content">
            @include('includes.messages')
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <button type="button" class="btn btn-primary btn-sm btn-flat" data-toggle="modal" data-target="#addnewtech_doc">
                                <i class="fa fa-plus"></i> New Tech Doc
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
                                @foreach($tech_docs as $tech_doc)
                                    <tr>
                                        <td>{{ $tech_doc->getAttribute('id') }}</td>
                                        <td>{{ $tech_doc->getAttribute('file') }}</td>
                                        <td>{{ $tech_doc->getAttribute('project_id') }}</td>
                                        <td>{{ $tech_doc->project->name }}</td>
                                        <td>
                                            <a href="{{ route('tech_docs.download', $tech_doc) }}" class="btn btn-primary btn-sm btn-flat">
                                                <i class="fas fa-download"></i> Download
                                            </a>
                                            <button type="button" class="btn btn-success btn-sm edit btn-flat" data-toggle="modal" data-target="#edit{{$tech_doc->id}}">
                                                <i class='fa fa-edit'></i> Edit
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm delete btn-flat" data-toggle="modal" data-target="#delete{{$tech_doc->id}}">
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
    @include('admin.tech_docs.modals.add')

    <!-- Edit/Delete Modals -->
    @foreach($tech_docs as $tech_doc)
        @include('admin.tech_docs.modals.edit_delete')
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
