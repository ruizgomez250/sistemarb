<?php
// app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use App\Models\Registro;
use App\Models\Motivo;
use App\Models\Profesion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    // app/Http/Controllers/DashboardController.php

    public function index()
    {
        // Estadísticas generales
        $totalRegistros = Registro::count();
        $totalMotivos = Motivo::count();
        $totalProfesiones = Profesion::count();

        // Obtener TODOS los motivos (para el filtro o selector)
        $motivos = Motivo::orderBy('descripcion')->get();

        // Obtener TODAS las profesiones (para el filtro o selector)
        $profesiones = Profesion::orderBy('descripcion')->get();

        // Registros del día actual
        $registrosHoy = Registro::whereDate('created_at', Carbon::today())->count();

        // Registros de la semana actual
        $registrosSemana = Registro::whereBetween('created_at', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ])->count();

        // Registros del mes actual
        $registrosMes = Registro::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // Registros del mes anterior
        $registrosMesAnterior = Registro::whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->count();

        // Calcular crecimiento porcentual
        $crecimiento = $registrosMesAnterior > 0
            ? round((($registrosMes - $registrosMesAnterior) / $registrosMesAnterior) * 100, 1)
            : 0;

        // Top 5 motivos más comunes
        $topMotivos = Motivo::withCount('registros')
            ->having('registros_count', '>', 0)
            ->orderBy('registros_count', 'desc')
            ->limit(5)
            ->get();

        // Top 5 profesiones más comunes
        $topProfesiones = Profesion::withCount('registros')
            ->having('registros_count', '>', 0)
            ->orderBy('registros_count', 'desc')
            ->limit(5)
            ->get();

        // Top 10 barrios con más registros
        $topBarrios = Registro::select('barrio', DB::raw('count(*) as total'))
            ->groupBy('barrio')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        // Registros por mes (últimos 12 meses)
        $registrosPorMes = Registro::select(
            DB::raw('YEAR(created_at) as año'),
            DB::raw('MONTH(created_at) as mes'),
            DB::raw('COUNT(*) as total')
        )
            ->where('created_at', '>=', Carbon::now()->subMonths(11)->startOfMonth())
            ->groupBy('año', 'mes')
            ->orderBy('año', 'asc')
            ->orderBy('mes', 'asc')
            ->get()
            ->map(function ($item) {
                $fecha = Carbon::createFromDate($item->año, $item->mes, 1);
                $item->nombre_mes = $fecha->locale('es')->isoFormat('MMMM YYYY');
                $item->mes_numero = $item->mes;
                return $item;
            });

        // Registros por día (últimos 7 días)
        $registrosPorDia = Registro::select(
            DB::raw('DATE(created_at) as fecha'),
            DB::raw('COUNT(*) as total')
        )
            ->where('created_at', '>=', Carbon::now()->subDays(6)->startOfDay())
            ->groupBy('fecha')
            ->orderBy('fecha', 'asc')
            ->get()
            ->map(function ($item) {
                $item->nombre_dia = Carbon::parse($item->fecha)->locale('es')->isoFormat('dddd D');
                return $item;
            });

        // Últimos 10 registros
        $ultimosRegistros = Registro::with(['motivo', 'profesion'])
            ->latest()
            ->limit(10)
            ->get();

        // Distribución por edades
        $edades = [
            '0-18 años' => Registro::whereRaw('TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 0 AND 18')->count(),
            '19-30 años' => Registro::whereRaw('TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 19 AND 30')->count(),
            '31-45 años' => Registro::whereRaw('TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 31 AND 45')->count(),
            '46-60 años' => Registro::whereRaw('TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 46 AND 60')->count(),
            '60+ años' => Registro::whereRaw('TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) > 60')->count(),
        ];

        // Registros por local
        $registrosPorLocalInterna = Registro::select('local_interna', DB::raw('count(*) as total'))
            ->groupBy('local_interna')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        $registrosPorLocalGenerales = Registro::select('local_generales', DB::raw('count(*) as total'))
            ->groupBy('local_generales')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'totalRegistros',
            'totalMotivos',
            'totalProfesiones',
            'registrosHoy',
            'registrosSemana',
            'registrosMes',
            'crecimiento',
            'topMotivos',
            'topProfesiones',
            'topBarrios',
            'registrosPorMes',
            'registrosPorDia',
            'ultimosRegistros',
            'edades',
            'registrosPorLocalInterna',
            'registrosPorLocalGenerales',
            'motivos',      // ← Agregar esta línea
            'profesiones'   // ← Agregar esta línea (opcional)
        ));
    }

    /**
     * API: Datos para gráficos
     */
    public function getChartData()
    {
        // Datos para gráfico de registros por mes
        $registrosPorMes = Registro::select(
            DB::raw('YEAR(created_at) as año'),
            DB::raw('MONTH(created_at) as mes'),
            DB::raw('COUNT(*) as total')
        )
            ->where('created_at', '>=', Carbon::now()->subMonths(11)->startOfMonth())
            ->groupBy('año', 'mes')
            ->orderBy('año', 'asc')
            ->orderBy('mes', 'asc')
            ->get();

        // Datos para gráfico de motivos
        $motivosData = Motivo::withCount('registros')
            ->having('registros_count', '>', 0)
            ->orderBy('registros_count', 'desc')
            ->limit(6)
            ->get()
            ->map(function ($item) {
                return [
                    'nombre' => $item->descripcion,
                    'total' => $item->registros_count
                ];
            });

        // Datos para gráfico de profesiones
        $profesionesData = Profesion::withCount('registros')
            ->having('registros_count', '>', 0)
            ->orderBy('registros_count', 'desc')
            ->limit(6)
            ->get()
            ->map(function ($item) {
                return [
                    'nombre' => $item->descripcion,
                    'total' => $item->registros_count
                ];
            });

        return response()->json([
            'registrosPorMes' => $registrosPorMes,
            'motivos' => $motivosData,
            'profesiones' => $profesionesData
        ]);
    }

    /**
     * Filtrar dashboard por fechas
     */
    public function filter(Request $request)
    {
        $request->validate([
            'fecha_desde' => 'nullable|date',
            'fecha_hasta' => 'nullable|date|after_or_equal:fecha_desde'
        ]);

        $query = Registro::query();

        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        $totalRegistros = $query->count();

        // Top motivos en el período
        $topMotivos = Motivo::withCount(['registros' => function ($q) use ($request) {
            if ($request->filled('fecha_desde')) {
                $q->whereDate('created_at', '>=', $request->fecha_desde);
            }
            if ($request->filled('fecha_hasta')) {
                $q->whereDate('created_at', '<=', $request->fecha_hasta);
            }
        }])
            ->having('registros_count', '>', 0)
            ->orderBy('registros_count', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'total' => $totalRegistros,
            'topMotivos' => $topMotivos
        ]);
    }
}
