{{-- resources/views/motivos/index.blade.php --}}
@extends('adminlte::page')

@section('title', 'Motivos')

@section('content_header')
    <div class="row">
        <div class="col-6">
            <h1 class="m-0 custom-heading">Lista de Motivos</h1>
        </div>
        <div class="col-6">
            <button type="button" class="btn btn-primary" style="float: right;" data-toggle="modal"
                data-target="#modalCrearMotivo">
                <i class="fas fa-plus-circle"></i> Registrar Nuevo Motivo
            </button>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tableMotivos" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Descripción</th>
                                    <th>Observación</th>
                                    <th>Registros</th>
                                    <th>Fecha Creación</th>
                                    <th width="120">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($motivos as $motivo)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $motivo->descripcion }}</td>
                                        <td>{{ Str::limit($motivo->observacion, 50) ?? 'Sin observación' }}</td>
                                        <td>
                                            <span class="badge {{ $motivo->registros_count > 0 ? 'bg-info' : 'bg-secondary' }}">
                                                {{ $motivo->registros_count ?? 0 }}
                                            </span>
                                        </td>
                                        <td>{{ $motivo->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            {{-- Botón Editar con URL incluida --}}
                                            <button type="button" class="btn btn-sm btn-outline-warning btn-editar"
                                                data-id="{{ $motivo->id }}"
                                                data-url="{{ route('motivos.update', $motivo->id) }}"
                                                data-descripcion="{{ $motivo->descripcion }}"
                                                data-observacion="{{ $motivo->observacion }}"
                                                data-toggle="modal" data-target="#modalEditarMotivo">
                                                <i class="fas fa-edit"></i>
                                            </button>

                                            {{-- Botón Eliminar --}}
                                            <button type="button" class="btn btn-sm btn-outline-danger btn-eliminar"
                                                data-id="{{ $motivo->id }}"
                                                data-descripcion="{{ $motivo->descripcion }}">
                                                <i class="fas fa-trash"></i>
                                            </button>

                                            {{-- Formulario oculto para eliminar --}}
                                            <form id="delete-form-{{ $motivo->id }}"
                                                action="{{ route('motivos.destroy', $motivo->id) }}" method="POST"
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
                        {{ $motivos->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL CREAR MOTIVO --}}
    <div class="modal fade" id="modalCrearMotivo" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-plus-circle"></i> Nuevo Motivo
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form action="{{ route('motivos.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Descripción <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                </div>
                                <input type="text" name="descripcion" class="form-control"
                                    placeholder="Ej: Consulta médica" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Observación</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-comment"></i></span>
                                </div>
                                <textarea name="observacion" class="form-control" rows="4" 
                                    placeholder="Observaciones adicionales (opcional)"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL EDITAR MOTIVO --}}
    <div class="modal fade" id="modalEditarMotivo" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-edit"></i> Editar Motivo
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formEditarMotivo" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label>Descripción <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                </div>
                                <input type="text" name="descripcion" id="edit_descripcion" class="form-control" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Observación</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-comment"></i></span>
                                </div>
                                <textarea name="observacion" id="edit_observacion" class="form-control" rows="4"></textarea>
                            </div>
                        </div>
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                <i class="fas fa-times"></i> Cancelar
                            </button>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Actualizar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@push('js')
    <script>
        $(document).ready(function() {
            // Inicializar DataTable
            $('#tableMotivos').DataTable({
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                pageLength: 10,
                order: [[0, 'asc']]
            });

            // Modal Editar Motivo - Usando la URL del data-url
            $('.btn-editar').on('click', function() {
                var url = $(this).data('url');
                var descripcion = $(this).data('descripcion');
                var observacion = $(this).data('observacion');

                $('#formEditarMotivo').attr('action', url);
                $('#edit_descripcion').val(descripcion);
                $('#edit_observacion').val(observacion || '');

                // Debug: Verificar la URL en consola
                console.log('URL del formulario:', url);
            });

            // Eliminar Motivo con SweetAlert
            $('.btn-eliminar').on('click', function() {
                var id = $(this).data('id');
                var descripcion = $(this).data('descripcion');

                Swal.fire({
                    title: '¿Eliminar motivo?',
                    html: `Estás seguro de eliminar el motivo:<br><strong>${descripcion}</strong>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('delete-form-' + id).submit();
                    }
                });
            });

            // Mensajes de éxito/error
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: '{{ session('success') }}',
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: '{{ session('error') }}',
                    confirmButtonColor: '#d33'
                });
            @endif

            // Resetear formulario al cerrar modales
            $('#modalCrearMotivo').on('hidden.bs.modal', function() {
                $(this).find('form')[0].reset();
            });

            $('#modalEditarMotivo').on('hidden.bs.modal', function() {
                $('#formEditarMotivo')[0].reset();
                $('#formEditarMotivo').attr('action', '');
            });
        });
    </script>
@endpush