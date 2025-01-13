<!-- Edit Modal -->
<div class="modal fade" id="edit{{$client_doc->id}}">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><b>Edit Client Doc</b></h4>
            </div>
            <form class="form-horizontal" method="POST" action="{{ route('client_docs.update', $client_doc->id) }}"
                  enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_project_id" class="col-sm-3 control-label">Project</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="project_id" name="project_id" required>
                                <option value="">Select Project</option>
                                @foreach($projects as $project)
                                    <option
                                        value="{{ $project->id }}"
                                        {{ $client_doc->project_id == $project->id ? 'selected' : '' }}>
                                        {{$project->id}} {{ $project->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="edit_file" class="col-sm-3 control-label">File</label>
                        <div class="col-sm-9">
                            <input type="file" class="form-control" id="edit_file" name="file">
                            <small class="text-muted">Current file: {{ $client_doc->file }}</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal">
                        <i class="fa fa-close"></i> Close
                    </button>
                    <button type="submit" class="btn btn-success btn-flat">
                        <i class="fa fa-check-square-o"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="delete{{$client_doc->id}}">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><b>Deleting...</b></h4>
            </div>
            <form class="form-horizontal" method="POST" action="{{ route('client_docs.destroy', $client_doc->id) }}">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p>Are you sure you want to delete this client_doc file: <b>{{ $client_doc->file }}</b>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal">
                        <i class="fa fa-close"></i> Close
                    </button>
                    <button type="submit" class="btn btn-danger btn-flat">
                        <i class="fa fa-trash"></i> Delete
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
