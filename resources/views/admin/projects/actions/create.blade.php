@extends('adminlte::page')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>Create New Project</h1>
            <ol class="breadcrumb">
                <li><a href="/home"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="{{ route('projects.index') }}">Projects</a></li>
                <li class="active">Create</li>
            </ol>
        </section>

        <section class="content">
            @include('includes.messages')

            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Project Details</h3>
                </div>

                <form role="form" method="POST" action="{{ route('projects.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="box-body">
                        <div class="row">
                            <!-- Name -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Project Name</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                           value="{{ old('name') }}" required placeholder="Enter project name">
                                </div>
                            </div>

                            <!-- Client -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="client_id">Client</label>
                                    <select class="form-control" id="client_id" name="client_id" required>
                                        <option value="">Select Client</option>
                                        @foreach($clients as $client)
                                            <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                                {{ $client->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Start Date -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="start_date">Start Date</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date"
                                           value="{{ old('start_date') }}" required>
                                </div>
                            </div>

                            <!-- End Date -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="end_date">End Date</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date"
                                           value="{{ old('end_date') }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Pricing -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="pricing">Pricing</label>
                                    <input type="number" class="form-control" id="pricing" name="pricing"
                                           value="{{ old('pricing') }}" step="0.01" required>
                                </div>
                            </div>

                            <!-- Repository URL -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="repo">Repository URL</label>
                                    <input type="url" class="form-control" id="repo" name="repo"
                                           value="{{ old('repo') }}" placeholder="https://github.com/...">
                                </div>
                            </div>

                            <!-- Project URL -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="url">Project URL</label>
                                    <input type="url" class="form-control" id="url" name="url"
                                           value="{{ old('url') }}" placeholder="https://...">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Server -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="server">Server</label>
                                    <input type="text" class="form-control" id="server" name="server"
                                           value="{{ old('server') }}" placeholder="Enter server details">
                                </div>
                            </div>

                            <!-- State -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="state">Project State</label>
                                    <select class="form-control" id="state" name="state" required>
                                        <option value="">Select State</option>
                                        @foreach($project_states as $state)
                                            <option value="{{ $state->id }}" {{ old('state') == $state->id ? 'selected' : '' }}>
                                                {{ $state->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select class="form-control" id="status" name="status" required>
                                        <option value="">Select Status</option>
                                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        <option value="on_hold" {{ old('status') == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                                        <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Condition -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="condition">Project Condition</label>
                                    <textarea class="form-control" id="condition" name="condition"
                                              rows="3" placeholder="Enter project condition">{{ old('condition') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="assets">Project Assets</label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="assets" name="assets[]" multiple>
                                        <label class="custom-file-label" for="assets">Choose files</label>
                                    </div>
                                </div>
                                <small class="text-muted">You can select multiple files</small>
                                <div id="selectedFiles" class="mt-3">
                                    <ul class="list-group file-list"></ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="box-footer">
                        <a href="{{ route('projects.index') }}" class="btn btn-danger">Cancel</a>
                        <button type="submit" class="btn btn-primary pull-right">Create Project</button>
                    </div>
                </form>
            </div>
        </section>
    </div>
@endsection


    <style>
        .form-group {
            margin-bottom: 15px;
        }
        .box-footer {
            padding: 20px;
            text-align: center;
        }
        .btn {
            margin-right: 5px;
        }

        #selectedFiles ul li {
            margin-bottom: 5px;
            padding: 5px;
        }

        #selectedFiles ul li:last-child {
            border-bottom: none;
        }

        .custom-file-label::after {
            content: "Browse";
        }





        .file-list {
            max-height: 300px;
            overflow-y: auto;
            border-radius: 4px;
        }

        .file-list-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 15px;
            margin-bottom: 5px;
            background-color: var(--dark); /* Usando variable de AdminLTE para modo oscuro */
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 4px;
        }

        .file-info {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--light);
        }

        .file-name {
            margin: 0;
            font-size: 14px;
            color: var(--light);
        }

        .file-size {
            color: #6c757d;
            font-size: 12px;
        }

        .remove-file {
            color: #dc3545;
            cursor: pointer;
            padding: 5px;
            border-radius: 3px;
            transition: all 0.2s;
        }

        .remove-file:hover {
            background-color: rgba(220, 53, 69, 0.2);
            color: #ff6b6b;
        }

        /* Estilos para el input file */
        .custom-file-label {
            background-color: var(--dark);
            border-color: rgba(255, 255, 255, 0.1);
            color: var(--light);
        }

        .custom-file-label::after {
            background-color: #3f6791;
            color: var(--light);
        }

        /* Iconos de archivo */
        .file-icon {
            color: #3f6791;
            margin-right: 10px;
        }
    </style>


@section('js')
    <script>
        $(document).ready(function() {
            const fileInput = document.getElementById('assets');
            const fileList = document.querySelector('.file-list');
            let files = new DataTransfer(); // Para mantener la lista de archivos actual

            fileInput.addEventListener('change', function() {
                // Agregar los nuevos archivos al DataTransfer
                Array.from(this.files).forEach(file => {
                    files.items.add(file);
                });

                // Actualizar el input con todos los archivos
                updateFileList();
            });

            function updateFileList() {
                // Limpiar la lista visual
                fileList.innerHTML = '';

                // Crear elementos para cada archivo
                Array.from(files.files).forEach((file, index) => {
                    const li = document.createElement('li');
                    li.className = 'file-list-item';

                    const fileSize = (file.size / 1024).toFixed(2);
                    const fileExtension = file.name.split('.').pop().toLowerCase();

                    // Elegir el icono según la extensión
                    let icon = 'fa-file';
                    if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExtension)) {
                        icon = 'fa-file-image';
                    } else if (['pdf'].includes(fileExtension)) {
                        icon = 'fa-file-pdf';
                    } else if (['doc', 'docx'].includes(fileExtension)) {
                        icon = 'fa-file-word';
                    } else if (['xls', 'xlsx'].includes(fileExtension)) {
                        icon = 'fa-file-excel';
                    }

                    li.innerHTML = `
                    <div class="file-info">
                        <i class="fas ${icon}"></i>
                        <div>
                            <p class="file-name">${file.name}</p>
                            <span class="file-size">${fileSize} KB</span>
                        </div>
                    </div>
                    <i class="fas fa-times remove-file" data-index="${index}"></i>
                `;

                    fileList.appendChild(li);
                });

                // Actualizar el input file con la lista actual de archivos
                fileInput.files = files.files;
            }

            // Delegación de eventos para el botón de eliminar
            $(fileList).on('click', '.remove-file', function() {
                const index = $(this).data('index');
                const newFiles = new DataTransfer();

                // Recrear la lista de archivos excluyendo el eliminado
                Array.from(files.files).forEach((file, i) => {
                    if (i !== index) {
                        newFiles.items.add(file);
                    }
                });

                // Actualizar la lista de archivos
                files = newFiles;
                updateFileList();
            });

            // Validación del formulario
            $('form').submit(function(e) {
                let startDate = new Date($('#start_date').val());
                let endDate = new Date($('#end_date').val());

                if (endDate < startDate) {
                    e.preventDefault();
                    alert('End date cannot be earlier than start date');
                    return false;
                }
            });
        });
    </script>
@endsection
