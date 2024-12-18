@extends('adminlte::page')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>Projects List</h1>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/home"><i class="fa fa-dashboard"></i> Home </a></li>
                <li class="breadcrumb-item active" aria-current="page"> Projects </li>
            </ol>
        </section>

        <section class="content">
            @include('includes.messages')
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <a href="{{ route('projects.create') }}" class="btn btn-primary btn-sm btn-flat">
                                <i class="fa fa-plus"></i> New Project
                            </a>
                        </div>
                        <div class="box-body">
                            <table id="example1" class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Client</th>
                                    <th>Budget</th>
                                    <th>Repository</th>
                                    <th>URL</th>
                                    <th>Server</th>
                                    <th>State</th>
                                    <th>Status</th>
                                    <th>Condition</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($projects as $project)
                                    <tr>
                                        <td>{{ $project->getAttribute('id') }}</td>
                                        <td>{{ $project->getAttribute('name') }}</td>
                                        <td>{{ $project->getAttribute('start_date') }}</td>
                                        <td>{{ $project->getAttribute('end_date') }}</td>
                                        <td>{{ $project->clients->name }}</td>
                                        <td>${{ $project->getAttribute('budget')}}</td>
                                        <td>{{ $project->getAttribute('repo')}}</td>
                                        <td>{{ $project->getAttribute('url')}}</td>
                                        <td>{{ $project->getAttribute('server')}}</td>
                                        <td>{{ $project->project_state->name }}</td>
                                        <td>{{ $project->getAttribute('status')}}</td>
                                        <td>{{ $project->getAttribute('condition')}}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-info btn-sm view btn-flat" data-toggle="modal" data-target="#edit{{$project->id}}">
                                                    <i class='fa fa-eye'></i> View
                                                </button>
                                                <button type="button" class="btn btn-success btn-sm edit btn-flat" data-toggle="modal" data-target="#edit{{$project->id}}">
                                                    <i class='fa fa-edit'></i> Edit
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm delete btn-flat" data-toggle="modal" data-target="#delete{{$project->id}}">
                                                    <i class='fa fa-trash'></i> Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="box-footer clearfix">
                            <div class="pagination-wrapper">
                                {{ $projects->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection


<style>
    .btn-flat {
        margin-right: 5px;
    }
</style>
