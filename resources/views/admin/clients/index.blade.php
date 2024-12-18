@extends('adminlte::page')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>Clients List</h1>
            <ol class="breadcrumb">
                <li><a href="/home"><i class="fa fa-dashboard"></i> Home </a></li>
                <li class="active"> Clients </li>
            </ol>
        </section>

        <section class="content">
            @include('includes.messages')
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <button type="button" class="btn btn-primary btn-sm btn-flat" data-toggle="modal" data-target="#addnewclient">
                                <i class="fa fa-plus"></i> New Client
                            </button>
                        </div>
                        <div class="box-body">
                            <table id="example1" class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Picture</th>
                                    <th>Name</th>
                                    <th>Location</th>
                                    <th>Phone</th>
                                    <th>Mobile</th>
                                    <th>Email</th>
                                    <th>Web</th>
                                    <th>Language</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($clients as $client)
                                    <tr>
                                        <td>{{ $client->getAttribute('id') }}</td>
                                        <td>{{ $client->getAttribute('picture') }}</td>
                                        <td>{{ $client->getAttribute('name') }}</td>
                                        <td>{{ $client->getAttribute('zip') }} {{ $client->getAttribute('address') }},
                                            {{$client->city->city}},
                                            {{$client->city->state->state_name}},
                                            {{$client->city->state->country->iso}}</td>
                                        <td>{{$client->getAttribute('phone')}}</td>
                                        <td>{{$client->getAttribute('mobile')}}</td>
                                        <td>{{$client->getAttribute('email')}}</td>
                                        <td>{{$client->getAttribute('web')}}</td>
                                        <td>{{$client->language->name}}</td>
                                        <td>
                                            <button type="button" class="btn btn-success btn-sm edit btn-flat" data-toggle="modal" data-target="#edit{{$client->id}}">
                                                <i class='fa fa-edit'></i> Edit
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm delete btn-flat" data-toggle="modal" data-target="#delete{{$client->id}}">
                                                <i class='fa fa-trash'></i> Delete
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="box-footer clearfix">
                            <div class="pagination-wrapper">
                                {{ $clients->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Modals -->
    @include('admin.clients.modals.add')

    <!-- Edit/Delete Modals -->
    @foreach($clients as $client)
        @include('admin.clients.modals.edit_delete')
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



        class LocationSelectors {
            constructor(prefix = '') {
                console.log('LocationSelectors initialized');
                this.countrySelect = document.getElementById(prefix + 'country_id');
                this.stateSelect = document.getElementById(prefix + 'state_id');
                this.citySelect = document.getElementById(prefix + 'id_city');

                this.initializeSelectors();
            }

            initializeSelectors() {
                if (this.countrySelect) {
                    console.log('Setting up event listeners');
                    this.stateSelect.disabled = true;
                    this.citySelect.disabled = true;

                    this.countrySelect.addEventListener('change', () => this.handleCountryChange());
                    this.stateSelect.addEventListener('change', () => this.handleStateChange());
                }
            }

            async handleCountryChange() {
                const countryId = this.countrySelect.value;
                console.log('Country changed:', countryId);

                this.resetSelect(this.stateSelect, 'Select State');
                this.resetSelect(this.citySelect, 'Select City');

                if (countryId) {
                    try {
                        const response = await fetch(`/states/by-country/${countryId}`);
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        const states = await response.json();
                        console.log('States received:', states); // Para depuración

                        this.stateSelect.disabled = false;
                        states.forEach(state => {
                            // Log para depuración
                            console.log('Adding state:', state);
                            const option = new Option(state.state_name, state.id || '');
                            this.stateSelect.appendChild(option);
                        });
                    } catch (error) {
                        console.error('Error loading states:', error);
                        this.stateSelect.disabled = true;
                    }
                }
            }

            async handleStateChange() {
                const stateId = this.stateSelect.value;
                console.log('State changed, ID:', stateId); // Para depuración

                this.resetSelect(this.citySelect, 'Select City');

                if (stateId) {
                    try {
                        const url = `/cities/by-state/${stateId}`;
                        console.log('Fetching cities from:', url);

                        const response = await fetch(url);
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        const cities = await response.json();
                        console.log('Cities received:', cities);

                        this.citySelect.disabled = false;
                        cities.forEach(city => {
                            const option = new Option(city.city, city.id);
                            this.citySelect.appendChild(option);
                        });
                    } catch (error) {
                        console.error('Error loading cities:', error);
                        this.citySelect.disabled = true;
                    }
                }
            }

            resetSelect(selectElement, defaultText) {
                selectElement.innerHTML = '';
                selectElement.appendChild(new Option(defaultText, ''));
                selectElement.disabled = true;
            }
        }

        class EditLocationSelectors {
            constructor() {
                this.countrySelect = document.getElementById('edit_country_id');
                this.stateSelect = document.getElementById('edit_state_id');
                this.citySelect = document.getElementById('edit_id_city');

                if (this.countrySelect && this.stateSelect && this.citySelect) {
                    // Cargar los datos iniciales basados en el city_id que ya tiene el cliente
                    this.loadInitialData();
                    // Configurar los event listeners para cuando se quiera cambiar los valores
                    this.setupChangeEvents();
                }
            }

            async loadInitialData() {
                // Obtener el city_id del input hidden o data attribute
                const cityId = document.getElementById('client_city_id').value;

                try {
                    const response = await fetch(`/cities/get/${cityId}`);
                    const data = await response.json();

                    // Establecer los valores iniciales
                    this.countrySelect.value = data.country.id;

                    // Agregar el state actual al select de states
                    this.stateSelect.innerHTML = `                <option value="">Select State</option>
                <option value="${data.state.id}" selected>${data.state.name}</option>
            `;
                    this.stateSelect.disabled = false;

                    // Agregar la city actual al select de cities
                    this.citySelect.innerHTML = `                <option value="">Select City</option>
                <option value="${data.city.id}" selected>${data.city.name}</option>
            `;
                    this.citySelect.disabled = false;

                } catch (error) {
                    console.error('Error loading location data:', error);
                }
            }

            setupChangeEvents() {
                // Si el usuario cambia el país, usamos la lógica existente del add
                this.countrySelect.addEventListener('change', () => {
                    const selectedCountryId = this.countrySelect.value;
                    if (selectedCountryId) {
                        this.loadStates(selectedCountryId);
                    } else {
                        this.clearSelect(this.stateSelect);
                        this.clearSelect(this.citySelect);
                    }
                });

                // Si el usuario cambia el estado, usamos la lógica existente del add
                this.stateSelect.addEventListener('change', () => {
                    const selectedStateId = this.stateSelect.value;
                    if (selectedStateId) {
                        this.loadCities(selectedStateId);
                    } else {
                        this.clearSelect(this.citySelect);
                    }
                });
            }

            // Estos métodos son los mismos que en el add
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
        }

        new LocationSelectors('');
        new LocationSelectors('edit_');
        //new EditLocationSelectors();
    </script>
@stop
