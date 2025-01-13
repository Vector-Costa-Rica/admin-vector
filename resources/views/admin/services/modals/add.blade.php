<!-- Add Modal -->
<div class="modal fade" id="addnewservice">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><b>Add New Service</b></h4>
            </div>
            <form class="form-horizontal" method="POST" action="{{ route('services.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="product" class="col-sm-3 control-label">Product</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="product" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="code" class="col-sm-3 control-label">Code</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="code" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="description" class="col-sm-3 control-label">Description</label>
                        <div class="col-sm-9">
                            <textarea type="text" class="form-control" name="description" required></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="price" class="col-sm-3 control-label">Price</label>
                        <div class="input-group col-sm-9">
                            <span class="input-group-text">$</span>
                            <input type="text" class="form-control" id="price" name="price" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="rate" class="col-sm-3 control-label">Rate</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="rate" name="rate" required>
                                <option value="" selected>- Select -</option>
                                @foreach($rates as $rate)
                                    <option value="{{$rate->id}}">{{$rate->name}}</option>
                                @endforeach
                            </select>
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

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#price').inputmask({
                alias: 'numeric',
                groupSeparator: ',',
                autoGroup: true,
                digits: 2,
                digitsOptional: true,
                prefix: '',
                rightAlign: false,
                allowMinus: false,
                integerDigits: 12,
                removeMaskOnSubmit: true,
                greedy: false,
                clearIncomplete: true
            });
        });
    </script>
@endpush
