<!-- Edit Modal -->
<div class="modal fade" id="edit{{$client->id}}" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><b>Edit Client</b></h4>
            </div>
            <form class="form-horizontal" method="POST" action="{{ route('clients.update', $client->id) }}"
                  enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ $client->name }}">
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit_country_id">Country</label>
                                <select class="form-control" id="edit_country_id" name="country_id">
                                    <option value="">Select Country</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->id }}" {{ $client->city->state->country->id == $country->id ? 'selected' : '' }}>{{ $country->country_name }}</option>

                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit_state_id">State</label>
                                <select class="form-control" id="edit_state_id" name="state_id" >
                                    <option value="">Select State</option>
                                    <option value="{{ $client->city->state->id }}" selected>{{ $client->city->state->state_name }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit_id_city">City</label>
                                <select class="form-control" id="edit_id_city" name="id_city" >
                                    <option value="">Select City</option>
                                    <option value="{{ $client->city->id }}" selected>{{ $client->city->city }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="address">Address</label>
                                <input type="text" class="form-control" id="address" name="address" value="{{ $client->address }}" >
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="zip">ZIP Code</label>
                                <input type="number" class="form-control" id="zip" name="zip" value="{{ $client->zip }}" >
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input type="text" class="form-control" id="phone" name="phone" value="{{ $client->phone }}" >
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="mobile">Mobile</label>
                                <input type="text" class="form-control" id="mobile" name="mobile" value="{{ $client->mobile }}" >
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ $client->email }}" >
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="web">Website</label>
                                <input type="text" class="form-control" id="web" name="web" value="{{ $client->web }}" >
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="language_id">Language</label>
                                <select class="form-control" id="language_id" name="language_id" >
                                    <option value="">Select Language</option>
                                    @foreach($languages as $language)
                                        <option value="{{ $language->id }}" {{ $client->language_id == $language->id ? 'selected' : '' }}>{{ $language->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="picture">Picture</label>
                        <div class="col-sm-9">
                            <input type="file" class="form-control-file" id="picture" name="picture">
                            <small class="text-muted">Current file: {{$client->picture}}</small>
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
<script>

    /*class EditLocationSelectors {
        constructor() {
            this.countrySelect = document.getElementById('edit_country_id');
            this.stateSelect = document.getElementById('edit_state_id');
            this.citySelect = document.getElementById('edit_id_city');

            this.initializeSelectors();
        }

        initializeSelectors() {
            if (this.countrySelect && this.stateSelect && this.citySelect) {
                this.stateSelect.disabled = true;
                this.citySelect.disabled = true;

                // Cuando cambia el país, cargar sus estados
                this.countrySelect.addEventListener('change', () => {
                    const selectedCountryId = this.countrySelect.value;
                    if (selectedCountryId) {
                        this.loadStates(selectedCountryId);
                    } else {
                        this.clearSelect(this.stateSelect);
                        this.clearSelect(this.citySelect);
                    }
                });

                // Cuando cambia el estado, cargar sus ciudades
                this.stateSelect.addEventListener('change', () => {
                    const selectedStateId = this.stateSelect.value;
                    if (selectedStateId) {
                        this.loadCities(selectedStateId);
                    } else {
                        this.clearSelect(this.citySelect);
                    }
                });
            }
        }

        clearSelect(selectElement) {
            selectElement.innerHTML = '<option value="">Select option</option>';
            selectElement.disabled = true;
        }

        async loadStates(countryId) {
            try {
                const response = await fetch(`/states/by-country/${countryId}`);
                const states = await response.json();

                this.clearSelect(this.stateSelect);
                this.clearSelect(this.citySelect);

                this.stateSelect.innerHTML = '<option value="">Select State</option>';
                this.stateSelect.disabled = false;

                states.forEach(state => {
                    const option = document.createElement('option');
                    option.value = state.id;
                    option.textContent = state.name;
                    this.stateSelect.appendChild(option);
                });
            } catch (error) {
                console.error('Error loading states:', error);
            }
        }

        async loadCities(stateId) {
            try {
                const response = await fetch(`/cities/by-state/${stateId}`);
                const cities = await response.json();

                this.clearSelect(this.citySelect);

                this.citySelect.innerHTML = '<option value="">Select City</option>';
                this.citySelect.disabled = false;

                cities.forEach(city => {
                    const option = document.createElement('option');
                    option.value = city.id;
                    option.textContent = city.name;
                    this.citySelect.appendChild(option);
                });
            } catch (error) {
                console.error('Error loading cities:', error);
            }
        }
    }*/

    //const editLocationSelectors = new EditLocationSelectors();




    /*class EditLocationSelectors {
        constructor() {
            console.log('EditLocationSelectors initialized');
            this.countrySelect = document.getElementById('edit_country_id');
            this.stateSelect = document.getElementById('edit_state_id');
            this.citySelect = document.getElementById('edit_id_city');

            if (this.countrySelect && this.stateSelect && this.citySelect) {
                this.initializeSelectors();
            }
        }

        async initializeSelectors() {
            // Obtener los valores iniciales
            const initialCountryId = this.citySelect.getAttribute('data-initial-country');
            const initialStateId = this.citySelect.getAttribute('data-initial-state');
            const initialCityId = this.citySelect.getAttribute('data-initial-city');

            console.log('Initial values:', { initialCountryId, initialStateId, initialCityId });

            if (initialCountryId && initialStateId && initialCityId) {
                // Cargar los estados del país inicial
                await this.loadStates(initialCountryId, initialStateId);
                // Cargar las ciudades del estado inicial
                await this.loadCities(initialStateId, initialCityId);
            }

            // Agregar event listeners para cambios
            this.countrySelect.addEventListener('change', () => this.handleCountryChange());
            this.stateSelect.addEventListener('change', () => this.handleStateChange());
        }

        async loadStates(countryId, selectedStateId = null) {
            try {
                const response = await fetch(`/states/by-country/${countryId}`);
                const states = await response.json();

                this.resetSelect(this.stateSelect, 'Select State');
                this.stateSelect.disabled = false;

                states.forEach(state => {
                    const option = new Option(state.state_name, state.id);
                    if (selectedStateId && state.id == selectedStateId) {
                        option.selected = true;
                    }
                    this.stateSelect.appendChild(option);
                });
            } catch (error) {
                console.error('Error loading states:', error);
                this.stateSelect.disabled = true;
            }
        }

        async loadCities(stateId, selectedCityId = null) {
            try {
                const response = await fetch(`/cities/by-state/${stateId}`);
                const cities = await response.json();

                this.resetSelect(this.citySelect, 'Select City');
                this.citySelect.disabled = false;

                cities.forEach(city => {
                    const option = new Option(city.city, city.id);
                    if (selectedCityId && city.id == selectedCityId) {
                        option.selected = true;
                    }
                    this.citySelect.appendChild(option);
                });
            } catch (error) {
                console.error('Error loading cities:', error);
                this.citySelect.disabled = true;
            }
        }

        async handleCountryChange() {
            const countryId = this.countrySelect.value;
            if (countryId) {
                // Al cambiar el país, cargar estados sin seleccionar ninguno
                await this.loadStates(countryId);
                // Resetear ciudades
                this.resetSelect(this.citySelect, 'Select City');
            } else {
                // Si no hay país seleccionado, deshabilitar estados y ciudades
                this.resetSelect(this.stateSelect, 'Select State');
                this.resetSelect(this.citySelect, 'Select City');
            }
        }

        async handleStateChange() {
            const stateId = this.stateSelect.value;
            if (stateId) {
                // Al cambiar el estado, cargar ciudades sin seleccionar ninguna
                await this.loadCities(stateId);
            } else {
                // Si no hay estado seleccionado, resetear ciudades
                this.resetSelect(this.citySelect, 'Select City');
            }
        }

        resetSelect(selectElement, defaultText) {
            selectElement.innerHTML = '';
            selectElement.appendChild(new Option(defaultText, ''));
            selectElement.disabled = true;
        }
    }

    new EditLocationSelectors();*/
</script>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="delete{{$client->id}}">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><b>Deleting...</b></h4>
            </div>
            <form class="form-horizontal" method="POST" action="{{ route('clients.destroy', $client->id) }}">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p>Are you sure you want to delete this client file: <b>{{ $client->file }}</b>?</p>
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
