<!-- Add Modal -->
<div class="modal fade" id="addnewproposal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><b>Add New Proposal</b></h4>
            </div>
            <form class="form-horizontal" method="POST" action="{{ route('proposals.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="project_id" class="col-sm-3 control-label">Project</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="project_id" name="project_id" required>
                                <option value="" selected>- Select -</option>
                                @foreach($projects as $project)
                                    <option value="{{$project->id}}">{{$project->id}} {{$project->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="file" class="col-sm-3 control-label">File</label>
                        <div class="col-sm-9">
                            <input type="file" class="form-control" id="file" name="file" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal">
                        <i class="fa fa-close"></i> Close
                    </button>
                    <button type="submit" class="btn btn-primary btn-flat">
                        <i class="fa fa-save"></i> Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
