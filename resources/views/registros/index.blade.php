{{-- resources/views/registros/index.blade.php --}}
@extends('adminlte::page')

@section('title', 'Registros')

@section('content_header')
    <div class="row mb-2">
        <div class="col-md-4">
            <label for="motivo_id" class="form-label fw-bold">
                Filtrar por Motivo
            </label>
            <x-adminlte-select2 name="motivo_id" id="motivo_id" onchange="filtrarPorMotivo()" disable-faster-look>
                <option value="">Todos los motivos</option>
                @foreach ($motivos as $motivo)
                    <option value="{{ $motivo->id }}" @if ($motivoId == $motivo->id) selected @endif>
                        {{ $motivo->descripcion }}
                    </option>
                @endforeach
            </x-adminlte-select2>
        </div>

        <div class="col-md-4">
            <label for="profesion_id" class="form-label fw-bold">
                Filtrar por Profesión
            </label>
            <x-adminlte-select2 name="profesion_id" id="profesion_id" onchange="filtrarPorProfesion()" disable-faster-look>
                <option value="">Todas las profesiones</option>
                @foreach ($profesiones as $profesion)
                    <option value="{{ $profesion->id }}" @if ($profesionId == $profesion->id) selected @endif>
                        {{ $profesion->descripcion }}
                    </option>
                @endforeach
            </x-adminlte-select2>
        </div>

        <div class="col-md-4 text-right">
            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalCrearRegistro">
                <i class="fas fa-plus-circle"></i> Nuevo Registro
            </button>
            <button class="btn btn-danger" onclick="generarReportePDF()">
                <i class="fas fa-file-pdf"></i> Reporte PDF
            </button>
        </div>
    </div>
@stop

@section('content')
    {{-- TABLA DE REGISTROS --}}
    <div class="card">
        <div class="card-header bg-primary">
            <strong><i class="fas fa-database"></i> Listado de Registros</strong>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="registros-table" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Cédula</th>
                            <th>Nombres y Apellidos</th>
                            <th>Teléfonos</th>
                            <th>Barrio</th>
                            <th>Motivo Contacto</th>
                            <th>Profesión</th>
                            <th>Local Interna</th>
                            <th>Fecha Nac.</th>
                            <th width="100">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($registros as $registro)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $registro->cedula }}</td>
                                <td>{{ $registro->nombres_y_apellidos }}</td>
                                <td>
                                    {{ $registro->telefono1 }}
                                    @if($registro->telefono2) / {{ $registro->telefono2 }} @endif
                                    @if($registro->telefono3) / {{ $registro->telefono3 }} @endif
                                </td>
                                <td>{{ $registro->barrio }}</td>
                                <td>
                                    <span class="badge bg-info">
                                        {{ $registro->motivo->descripcion ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>{{ $registro->profesion->descripcion ?? 'N/A' }}</td>
                                <td>{{ $registro->local_interna }}</td>
                                <td>{{ \Carbon\Carbon::parse($registro->fecha_nacimiento)->format('d/m/Y') }}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-info btn-ver"
                                        data-id="{{ $registro->id }}"
                                        data-toggle="modal" data-target="#modalVerRegistro">
                                        <i class="fas fa-eye"></i>
                                    </button>

                                    <button type="button" class="btn btn-sm btn-outline-warning btn-editar"
                                        data-id="{{ $registro->id }}"
                                        data-url="{{ route('registros.update', $registro->id) }}"
                                        data-toggle="modal" data-target="#modalEditarRegistro">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <button type="button" class="btn btn-sm btn-outline-danger btn-eliminar"
                                        data-id="{{ $registro->id }}"
                                        data-nombre="{{ $registro->nombres_y_apellidos }}">
                                        <i class="fas fa-trash"></i>
                                    </button>

                                    <form id="delete-form-{{ $registro->id }}"
                                        action="{{ route('registros.destroy', $registro->id) }}" method="POST"
                                        style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            <div class="mt-3">
                {{ $registros->links() }}
            </div>
        </div>
    </div>

    {{-- MODAL CREAR REGISTRO --}}
    <div class="modal fade" id="modalCrearRegistro" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-plus-circle"></i> Nuevo Registro
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form action="{{ route('registros.store') }}" method="POST" id="formCrearRegistro">
                    @csrf
                    <div class="modal-body">
                        <div class="card mb-3">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="fas fa-user"></i> Datos Personales</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="cedula">Cédula <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                                </div>
                                                <input type="text" name="cedula" id="cedula" class="form-control" 
                                                    placeholder="Ej: 12345678" required>
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-primary" id="btnBuscarPersona">
                                                        <i class="fas fa-search"></i> Buscar
                                                    </button>
                                                </div>
                                            </div>
                                            <small class="text-muted">Presione Enter, Tab o click en Buscar</small>
                                            <div id="busquedaInfo" class="mt-1"></div>
                                        </div>
                                    </div>

                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="nombres_y_apellidos">Nombres y Apellidos <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                                </div>
                                                <input type="text" name="nombres_y_apellidos" id="nombres_y_apellidos" 
                                                    class="form-control" placeholder="Nombre completo" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="fecha_nacimiento">Fecha Nacimiento <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                                </div>
                                                <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" 
                                                    class="form-control" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="profesion_id">Profesión <span class="text-danger">*</span></label>
                                            <select name="profesion_id" id="profesion_id_form" class="form-control select2-profesion" 
                                                style="width: 100%;" required>
                                                @foreach ($profesiones as $profesion)
                                                    <option value="{{ $profesion->id }}">{{ $profesion->descripcion }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="telefono1">Teléfono Principal <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                                </div>
                                                <input type="text" name="telefono1" id="telefono1" class="form-control" 
                                                    placeholder="0981xxxxxx" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="telefono2">Teléfono Secundario</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                                </div>
                                                <input type="text" name="telefono2" id="telefono2" class="form-control" 
                                                    placeholder="0982xxxxxx">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="telefono3">Teléfono Adicional</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                                </div>
                                                <input type="text" name="telefono3" id="telefono3" class="form-control" 
                                                    placeholder="0983xxxxxx">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="afiliacion">Afiliación <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-hospital"></i></span>
                                                </div>
                                                <input type="text" name="afiliacion" id="afiliacion" class="form-control" 
                                                    placeholder="Seguro Social/IPS" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-header bg-secondary text-white">
                                <h6 class="mb-0"><i class="fas fa-map-marker-alt"></i> Ubicación</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="direccion">Dirección</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-location-dot"></i></span>
                                                </div>
                                                <input type="text" name="direccion" id="direccion" class="form-control" 
                                                    placeholder="Calle/Av. y número">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="barrio">Barrio/Compañía <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-building"></i></span>
                                                </div>
                                                <input type="text" name="barrio" id="barrio" class="form-control" 
                                                    placeholder="Nombre del barrio" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="motivo_id">Motivo Del Contacto<span class="text-danger">*</span></label>
                                            <select name="motivo_id" id="motivo_id_form" class="form-control select2-motivo" 
                                                style="width: 100%;" required>
                                                <option value="">Seleccione...</option>
                                                @foreach ($motivos as $motivo)
                                                    <option value="{{ $motivo->id }}">{{ $motivo->descripcion }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-header bg-dark text-white">
                                <h6 class="mb-0"><i class="fas fa-building"></i> Locales</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="local_interna">Local Interna <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-door-open"></i></span>
                                                </div>
                                                <input type="text" name="local_interna" id="local_interna" class="form-control" 
                                                    placeholder="Ej: Consultorio 1" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="local_generales">Locales Generales <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-building"></i></span>
                                                </div>
                                                <input type="text" name="local_generales" id="local_generales" class="form-control" 
                                                    placeholder="Ej: Área Médica" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="observacion_general">Observación General</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-comment"></i></span>
                                                </div>
                                                <textarea name="observacion_general" id="observacion_general" class="form-control" rows="3" 
                                                    placeholder="Observaciones adicionales (opcional)"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Guardar Registro
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL VER REGISTRO --}}
    <div class="modal fade" id="modalVerRegistro" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-info-circle"></i> Detalle del Registro
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="verRegistroContent">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Cargando...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .select2-container--default .select2-selection--single {
            height: 38px;
            border-radius: 4px;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }
        .info-origen {
            font-size: 12px;
            padding: 5px 10px;
            border-radius: 4px;
            background-color: #e9ecef;
            color: #495057;
        }
        .info-origen i {
            margin-right: 5px;
        }
    </style>
@stop

@section('js')
<script>
    const BASE_URL = '{{ url('/') }}';
    let tablaRegistros = null;

    $(document).ready(function() {
        inicializarSelects();
        inicializarTablaRegistros();
        inicializarBusquedaPersona();
        
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ session('error') }}',
                confirmButtonColor: '#d33'
            });
        @endif
    });

    function inicializarSelects() {
        $('.select2-profesion').select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: 'Seleccione una profesión',
            dropdownParent: $('#modalCrearRegistro')
        });

        $('.select2-motivo').select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: 'Seleccione un motivo',
            dropdownParent: $('#modalCrearRegistro')
        });

        $('#motivo_id, #profesion_id').select2({
            theme: 'bootstrap4',
            width: '100%'
        });
    }

    function inicializarTablaRegistros() {
        tablaRegistros = $('#registros-table').DataTable({
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
            },
            pageLength: 10,
            order: [[0, 'asc']]
        });
    }

    function inicializarBusquedaPersona() {
        const buscarPorCedula = function() {
            let cedula = $('#cedula').val().trim();
            
            if (cedula.length < 6) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Cédula muy corta',
                    text: 'Ingrese al menos 6 dígitos para buscar',
                    timer: 2000,
                    showConfirmButton: false
                });
                return;
            }
            
            $('#busquedaInfo').html(`
                <div class="info-origen">
                    <i class="fas fa-spinner fa-spin"></i> Buscando en SOCIOS, COOPERATIVA, COPAVIC y PADRÓN...
                </div>
            `);
            
            $.ajax({
                url: BASE_URL + '/buscar-persona/' + cedula,
                method: 'GET',
                success: function(response) {
                    if (response.encontrado) {
                        let origenHtml = `
                            <div class="info-origen mt-2">
                                <i class="fas fa-check-circle text-success"></i> 
                                Datos encontrados en: <strong>${response.origen || 'PADRÓN ILUMINADO'}</strong>
                            </div>
                        `;
                        $('#busquedaInfo').html(origenHtml);
                        
                        if (response.nombre) $('#nombres_y_apellidos').val(response.nombre);
                        if (response.telefonos && response.telefonos.length > 0) {
                            $('#telefono1').val(response.telefonos[0] || '');
                            $('#telefono2').val(response.telefonos[1] || '');
                            $('#telefono3').val(response.telefonos[2] || '');
                        }
                        if (response.direccion) $('#direccion').val(response.direccion);
                        if (response.barrio) $('#barrio').val(response.barrio);
                        if (response.afiliacion) $('#afiliacion').val(response.afiliacion);
                        if (response.fecha_nacimiento) $('#fecha_nacimiento').val(response.fecha_nacimiento);
                        if (response.local_interna) $('#local_interna').val(response.local_interna);
                        if (response.local_generales) $('#local_generales').val(response.local_generales);
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Persona encontrada',
                            text: `Datos cargados desde ${response.origen || 'PADRÓN ILUMINADO'}`,
                            timer: 2000,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
                        });
                    } else {
                        $('#busquedaInfo').html(`
                            <div class="info-origen mt-2 bg-warning text-dark">
                                <i class="fas fa-exclamation-triangle"></i> 
                                No se encontraron datos. Complete manualmente.
                            </div>
                        `);
                    }
                },
                error: function() {
                    $('#busquedaInfo').html(`
                        <div class="info-origen mt-2 bg-danger text-white">
                            <i class="fas fa-exclamation-circle"></i> Error al buscar.
                        </div>
                    `);
                }
            });
        };
        
        $('#cedula').on('blur', buscarPorCedula);
        $('#cedula').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                buscarPorCedula();
            }
        });
        $('#btnBuscarPersona').on('click', buscarPorCedula);
    }

    function filtrarPorMotivo() {
        let motivoId = $('#motivo_id').val();
        let profesionId = $('#profesion_id').val();
        window.location.href = BASE_URL + "/registros?motivo_id=" + (motivoId || '') + "&profesion_id=" + (profesionId || '');
    }

    function filtrarPorProfesion() {
        let motivoId = $('#motivo_id').val();
        let profesionId = $('#profesion_id').val();
        window.location.href = BASE_URL + "/registros?motivo_id=" + (motivoId || '') + "&profesion_id=" + (profesionId || '');
    }

    $('.btn-ver').on('click', function() {
        var id = $(this).data('id');
        $.get(BASE_URL + "/registros/" + id, function(response) {
            var html = `
                <div class="row">
                    <div class="col-md-6">
                        <div class="card"><div class="card-header bg-info text-white"><h6>Datos Personales</h6></div>
                        <div class="card-body">
                            <p><strong>Cédula:</strong> ${response.cedula}</p>
                            <p><strong>Nombres:</strong> ${response.nombres_y_apellidos}</p>
                            <p><strong>Fecha Nac.:</strong> ${response.fecha_nacimiento}</p>
                            <p><strong>Afiliación:</strong> ${response.afiliacion}</p>
                            <p><strong>Profesión:</strong> ${response.profesion || 'N/A'}</p>
                        </div></div>
                    </div>
                    <div class="col-md-6">
                        <div class="card"><div class="card-header bg-success text-white"><h6>Contacto</h6></div>
                        <div class="card-body">
                            <p><strong>Teléfono 1:</strong> ${response.telefono1}</p>
                            ${response.telefono2 ? `<p><strong>Teléfono 2:</strong> ${response.telefono2}</p>` : ''}
                            ${response.telefono3 ? `<p><strong>Teléfono 3:</strong> ${response.telefono3}</p>` : ''}
                        </div></div>
                        <div class="card mt-2"><div class="card-header bg-secondary text-white"><h6>Ubicación</h6></div>
                        <div class="card-body">
                            <p><strong>Dirección:</strong> ${response.direccion || 'No especificada'}</p>
                            <p><strong>Barrio:</strong> ${response.barrio}</p>
                            <p><strong>Motivo:</strong> ${response.motivo || 'N/A'}</p>
                        </div></div>
                    </div>
                    <div class="col-md-12">
                        <div class="card"><div class="card-header bg-dark text-white"><h6>Locales</h6></div>
                        <div class="card-body">
                            <p><strong>Local Interna:</strong> ${response.local_interna}</p>
                            <p><strong>Locales Generales:</strong> ${response.local_generales}</p>
                            <p><strong>Observación:</strong> ${response.observacion_general || 'Ninguna'}</p>
                        </div></div>
                    </div>
                </div>
            `;
            $('#verRegistroContent').html(html);
        });
    });

    $('.btn-eliminar').on('click', function() {
        var id = $(this).data('id');
        var nombre = $(this).data('nombre');
        
        Swal.fire({
            title: '¿Eliminar registro?',
            html: `¿Eliminar registro de:<br><strong>${nombre}</strong>?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    });

    function generarReportePDF() {
        window.open(BASE_URL + "/registros/export/pdf", '_blank');
    }

    $('#modalCrearRegistro').on('hidden.bs.modal', function() {
        $('#formCrearRegistro')[0].reset();
        $('.select2-profesion').val(null).trigger('change');
        $('.select2-motivo').val(null).trigger('change');
        $('#busquedaInfo').html('');
    });
</script>
@stop