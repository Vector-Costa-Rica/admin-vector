<!-- Edit Modal -->
<div class="modal fade" id="edit{{$report->id}}">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><b>Edit Report</b></h4>
            </div>
            <form class="form-horizontal" method="POST" action="{{ route('reports.update', $report->id) }}"
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
                                        {{ $report->project_id == $project->id ? 'selected' : '' }}>
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
                            <small class="text-muted">Current file: {{ $report->file }}</small>
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
<div class="modal fade" id="delete{{$report->id}}">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><b>Deleting...</b></h4>
            </div>
            <form class="form-horizontal" method="POST" action="{{ route('reports.destroy', $report->id) }}">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p>Are you sure you want to delete this report file: <b>{{ $report->file }}</b>?</p>
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
