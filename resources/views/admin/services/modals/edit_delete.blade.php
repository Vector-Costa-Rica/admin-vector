<!-- Edit Modal -->
<div class="modal fade" id="edit{{$service->id}}">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editServiceModalLabel">Edit Service</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form action="{{ route('services.update', $service->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="product">Product</label>
                        <input type="text" class="form-control" id="product" name="product" value="{{ $service->product }}" required>
                    </div>
                    <div class="form-group">
                        <label for="code">Code</label>
                        <input type="text" class="form-control" id="code" name="code" value="{{ $service->code }}" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description">{{ $service->description }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="price">Price</label>
                        <div class="input-group col-sm-9">
                            <span class="input-group-text">$</span>
                            <input type="text" class="form-control" id="price" name="price" value="{{ $service->price }}" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="rate_id">Rate</label>
                        <select class="form-control" id="rate_id" name="rate_id" required>
                            <option value="">Select Rate</option>
                            @foreach($rates as $rate)
                                <option value="{{ $rate->id }}" {{ $service->rate_id == $rate->id ? 'selected' : '' }}>
                                    {{ $rate->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="delete{{$service->id}}">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><b>Deleting...</b></h4>
            </div>
            <form class="form-horizontal" method="POST" action="{{ route('services.destroy', $service->id) }}">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p>Are you sure you want to delete this service?</p>
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
