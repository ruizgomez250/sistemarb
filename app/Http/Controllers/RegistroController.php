<?php
// app/Http/Controllers/RegistroController.php

namespace App\Http\Controllers;

use App\Models\CoopUniversitaria;
use App\Models\Copavic;
use App\Models\Registro;
use App\Models\Motivo;
use App\Models\PadronIluminado;
use App\Models\Profesion;
use App\Models\Socio;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RegistroController extends Controller
{
    public function index(Request $request)
    {
        $query = Registro::with(['motivo', 'profesion']);
        
        if ($request->filled('motivo_id')) {
            $query->where('motivo_id', $request->motivo_id);
        }
        
        if ($request->filled('profesion_id')) {
            $query->where('profesion_id', $request->profesion_id);
        }
        
        $registros = $query->orderBy('created_at', 'desc')->paginate(15);
        $motivos = Motivo::orderBy('descripcion')->get();
        $profesiones = Profesion::orderBy('descripcion')->get();
        $motivoId = $request->motivo_id;
        $profesionId = $request->profesion_id;
        
        return view('registros.index', compact('registros', 'motivos', 'profesiones', 'motivoId', 'profesionId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cedula' => 'required|string|max:20|unique:registros',
            'nombres_y_apellidos' => 'required|string|max:200',
            'telefono1' => 'required|string|max:20',
            'telefono2' => 'nullable|string|max:20',
            'telefono3' => 'nullable|string|max:20',
            'direccion' => 'nullable|string',
            'barrio' => 'required|string|max:100',
            'observacion_general' => 'nullable|string',
            'motivo_id' => 'required|exists:motivos,id',
            'fecha_nacimiento' => 'required|date',
            'afiliacion' => 'required|string|max:100',
            'profesion_id' => 'required|exists:profesiones,id',
            'local_interna' => 'required|string|max:100',
            'local_generales' => 'required|string|max:100'
        ]);

        Registro::create($validated);

        return redirect()->route('registros.index')
            ->with('success', 'Registro creado exitosamente.');
    }

    public function show(Registro $registro)
    {
        return response()->json([
            'id' => $registro->id,
            'cedula' => $registro->cedula,
            'nombres_y_apellidos' => $registro->nombres_y_apellidos,
            'telefono1' => $registro->telefono1,
            'telefono2' => $registro->telefono2,
            'telefono3' => $registro->telefono3,
            'direccion' => $registro->direccion,
            'barrio' => $registro->barrio,
            'observacion_general' => $registro->observacion_general,
            'fecha_nacimiento' => $registro->fecha_nacimiento->format('d/m/Y'),
            'afiliacion' => $registro->afiliacion,
            'local_interna' => $registro->local_interna,
            'local_generales' => $registro->local_generales,
            'motivo' => $registro->motivo->descripcion ?? 'N/A',
            'profesion' => $registro->profesion->descripcion ?? 'N/A'
        ]);
    }

    public function update(Request $request, Registro $registro)
    {
        $validated = $request->validate([
            'cedula' => 'required|string|max:20|unique:registros,cedula,' . $registro->id,
            'nombres_y_apellidos' => 'required|string|max:200',
            'telefono1' => 'required|string|max:20',
            'telefono2' => 'nullable|string|max:20',
            'telefono3' => 'nullable|string|max:20',
            'direccion' => 'nullable|string',
            'barrio' => 'required|string|max:100',
            'observacion_general' => 'nullable|string',
            'motivo_id' => 'required|exists:motivos,id',
            'fecha_nacimiento' => 'required|date',
            'afiliacion' => 'required|string|max:100',
            'profesion_id' => 'required|exists:profesiones,id',
            'local_interna' => 'required|string|max:100',
            'local_generales' => 'required|string|max:100'
        ]);

        $registro->update($validated);

        return redirect()->route('registros.index')
            ->with('success', 'Registro actualizado exitosamente.');
    }

    public function destroy(Registro $registro)
    {
        $registro->delete();
        return redirect()->route('registros.index')
            ->with('success', 'Registro eliminado exitosamente.');
    }
    /**
     * Buscar persona por cédula en múltiples tablas
     */
    public function buscarPersonaPorCedula($cedula)
    {
        $datos = [
            'encontrado' => false,
            'origen' => null,
            'telefonos' => [],
            'nombre' => null,
            'direccion' => null,
            'barrio' => null,
            'afiliacion' => null,
            'fecha_nacimiento' => null,
            'local_interna' => null,
            'local_generales' => null
        ];
        
        // 1. Buscar en SOCIOS
        $socio = Socio::where('cedula', $cedula)->first();
        if ($socio) {
            $datos['encontrado'] = true;
            $datos['origen'] = 'SOCIOS';
            $datos['nombre'] = $socio->nombre;
            $datos['direccion'] = $socio->direccion;
            $datos['barrio'] = $socio->barrio;
            $datos['afiliacion'] = 'Socio';
            
            // Recolectar teléfonos
            if ($socio->telefono) $datos['telefonos'][] = $socio->telefono;
            if ($socio->telefono1) $datos['telefonos'][] = $socio->telefono1;
            if ($socio->telefono2) $datos['telefonos'][] = $socio->telefono2;
        }
        
        // 2. Si no se encontró en SOCIOS, buscar en COOPERATIVA UNIVERSITARIA
        if (!$datos['encontrado']) {
            $coopUniversitaria = CoopUniversitaria::where('cedula', $cedula)->first();
            if ($coopUniversitaria) {
                $datos['encontrado'] = true;
                $datos['origen'] = 'COOPERATIVA UNIVERSITARIA';
                $datos['nombre'] = 'Socio N° ' . $coopUniversitaria->socio;
                $datos['barrio'] = $coopUniversitaria->ciudad;
                $datos['afiliacion'] = 'Cooperativa Universitaria';
                
                if ($coopUniversitaria->telefono) {
                    $datos['telefonos'][] = $coopUniversitaria->telefono;
                }
            }
        }
        
        // 3. Si no se encontró, buscar en COPAVIC
        if (!$datos['encontrado']) {
            $copavic = Copavic::where('cedula', $cedula)->first();
            if ($copavic) {
                $datos['encontrado'] = true;
                $datos['origen'] = 'COPAVIC';
                $datos['nombre'] = $copavic->nombre;
                $datos['direccion'] = $copavic->direccion;
                $datos['afiliacion'] = 'Copavic';
                
                if ($copavic->telefono) {
                    $datos['telefonos'][] = $copavic->telefono;
                }
            }
        }
        
        // 4. SIEMPRE buscar en PADRÓN ILUMINADO para obtener afiliación y otros datos
        $padron = PadronIluminado::where('cedula', $cedula)->first();
        if ($padron) {
            $datos['encontrado'] = true;
            $datos['afiliacion'] = $padron->partido ?? $datos['afiliacion'];
            $datos['local_interna'] = $padron->local ?? $datos['local_interna'];
            $datos['local_generales'] = $padron->localdesc ?? $datos['local_generales'];
            
            // Si no se encontró nombre antes, usar el del padrón
            if (!$datos['nombre']) {
                $datos['nombre'] = trim($padron->nombre . ' ' . $padron->apellido);
            }
            
            // Si no se encontró barrio antes, usar distrito del padrón
            if (!$datos['barrio']) {
                $datos['barrio'] = $padron->distrito;
            }
            
            // CONVERTIR FECHA DE d/m/Y a Y-m-d para el input date
            if ($padron->fecnac && !$datos['fecha_nacimiento']) {
                try {
                    // El formato en la BD es d/m/Y (ej: 20/07/1987)
                    $fecha = Carbon::createFromFormat('d/m/Y', $padron->fecnac);
                    $datos['fecha_nacimiento'] = $fecha->format('Y-m-d');
                } catch (\Exception $e) {
                    // Si falla, intentar con otros formatos comunes
                    try {
                        $fecha = Carbon::parse($padron->fecnac);
                        $datos['fecha_nacimiento'] = $fecha->format('Y-m-d');
                    } catch (\Exception $e) {
                        // Si no se puede convertir, dejar como está
                        $datos['fecha_nacimiento'] = null;
                    }
                }
            }
        }
        
        return response()->json($datos);
    }
}