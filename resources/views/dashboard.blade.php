{{-- resources/views/dashboard.blade.php --}}
@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1><i class="fas fa-chart-line"></i> Dashboard</h1>
        </div>
    </div>
@stop

@section('content')
    {{-- Tarjetas de estadísticas --}}
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ number_format($totalRegistros) }}</h3>
                    <p>Total Registros</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="{{ route('registros.index') }}" class="small-box-footer">
                    Ver más <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ number_format($registrosHoy) }}</h3>
                    <p>Registros Hoy</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="small-box-footer">
                    {{ now()->format('d/m/Y') }}
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ number_format($registrosSemana) }}</h3>
                    <p>Registros Esta Semana</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-week"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ number_format($registrosMes) }}</h3>
                    <p>Registros Este Mes</p>
                    @if($crecimiento != 0)
                        <small class="text-white">
                            @if($crecimiento > 0)
                                <i class="fas fa-arrow-up"></i> +{{ $crecimiento }}%
                            @else
                                <i class="fas fa-arrow-down"></i> {{ $crecimiento }}%
                            @endif
                            vs mes anterior
                        </small>
                    @endif
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Gráficos --}}
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary">
                    <h5 class="card-title"><i class="fas fa-chart-line"></i> Registros por Mes</h5>
                </div>
                <div class="card-body">
                    <canvas id="chartRegistrosMes" height="300"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info">
                    <h5 class="card-title"><i class="fas fa-chart-pie"></i> Distribución por Edades</h5>
                </div>
                <div class="card-body">
                    <canvas id="chartEdades" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success">
                    <h5 class="card-title"><i class="fas fa-trophy"></i> Top 5 Motivos</h5>
                </div>
                <div class="card-body">
                    <canvas id="chartMotivos" height="250"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-warning">
                    <h5 class="card-title"><i class="fas fa-briefcase"></i> Top 5 Profesiones</h5>
                </div>
                <div class="card-body">
                    <canvas id="chartProfesiones" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Tablas --}}
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-secondary">
                    <h5 class="card-title"><i class="fas fa-map-marker-alt"></i> Top 10 Barrios</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Barrio</th>
                                    <th class="text-right">Registros</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topBarrios as $barrio)
                                <tr>
                                    <td>{{ $barrio->barrio }}</td>
                                    <td class="text-right">
                                        <span class="badge bg-primary">{{ number_format($barrio->total) }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-dark">
                    <h5 class="card-title"><i class="fas fa-clock"></i> Últimos Registros</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Cédula</th>
                                    <th>Nombre</th>
                                    <th>Motivo</th>
                                    <th class="text-right">Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ultimosRegistros as $registro)
                                <tr>
                                    <td>{{ $registro->cedula }}</td>
                                    <td>{{ Str::limit($registro->nombres_y_apellidos, 25) }}</td>
                                    <td>{{ $registro->motivo->descripcion ?? 'N/A' }}</td>
                                    <td class="text-right">{{ $registro->created_at->format('d/m/Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .small-box {
            border-radius: 10px;
            transition: all 0.3s;
        }
        .small-box:hover {
            transform: translateY(-5px);
        }
    </style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Gráfico de registros por mes
    const ctxMes = document.getElementById('chartRegistrosMes').getContext('2d');
    new Chart(ctxMes, {
        type: 'line',
        data: {
            labels: {!! json_encode($registrosPorMes->pluck('nombre_mes')) !!},
            datasets: [{
                label: 'Registros',
                data: {!! json_encode($registrosPorMes->pluck('total')) !!},
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                borderWidth: 2,
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { position: 'top' }
            }
        }
    });

    // Gráfico de edades
    const ctxEdades = document.getElementById('chartEdades').getContext('2d');
    new Chart(ctxEdades, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode(array_keys($edades)) !!},
            datasets: [{
                data: {!! json_encode(array_values($edades)) !!},
                backgroundColor: [
                    'rgb(255, 99, 132)',
                    'rgb(54, 162, 235)',
                    'rgb(255, 206, 86)',
                    'rgb(75, 192, 192)',
                    'rgb(153, 102, 255)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    // Gráfico de motivos
    const ctxMotivos = document.getElementById('chartMotivos').getContext('2d');
    new Chart(ctxMotivos, {
        type: 'bar',
        data: {
            labels: {!! json_encode($topMotivos->pluck('descripcion')) !!},
            datasets: [{
                label: 'Cantidad de registros',
                data: {!! json_encode($topMotivos->pluck('registros_count')) !!},
                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                borderColor: 'rgb(54, 162, 235)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });

    // Gráfico de profesiones
    const ctxProfesiones = document.getElementById('chartProfesiones').getContext('2d');
    new Chart(ctxProfesiones, {
        type: 'bar',
        data: {
            labels: {!! json_encode($topProfesiones->pluck('descripcion')) !!},
            datasets: [{
                label: 'Cantidad de registros',
                data: {!! json_encode($topProfesiones->pluck('registros_count')) !!},
                backgroundColor: 'rgba(255, 159, 64, 0.7)',
                borderColor: 'rgb(255, 159, 64)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });
</script>
@stop