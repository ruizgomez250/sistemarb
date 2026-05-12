<?php
// app/Http/Controllers/ProfesionController.php

namespace App\Http\Controllers;

use App\Models\Profesion;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfesionController extends Controller
{
    /**
     * Mostrar lista de profesiones
     */
    public function index()
    {
        $profesiones = Profesion::withCount('registros')
            ->orderBy('descripcion')
            ->paginate(15);
        
        return view('profesiones.index', compact('profesiones'));
    }

    /**
     * Guardar nueva profesión
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'descripcion' => 'required|string|max:150|unique:profesiones,descripcion',
            'observacion' => 'nullable|string'
        ]);

        Profesion::create($validated);

        return redirect()->route('profesiones.index')
            ->with('success', 'Profesión creada exitosamente.');
    }

    /**
     * Mostrar una profesión específica (API)
     */
    public function show(Profesion $profesion)
    {
        return response()->json([
            'id' => $profesion->id,
            'descripcion' => $profesion->descripcion,
            'observacion' => $profesion->observacion,
            'created_at' => $profesion->created_at->format('d/m/Y H:i:s'),
            'updated_at' => $profesion->updated_at->format('d/m/Y H:i:s'),
            'registros_count' => $profesion->registros()->count()
        ]);
    }

    /**
     * Actualizar profesión
     */
    public function update(Request $request, Profesion $profesion)
    {
        $validated = $request->validate([
            'descripcion' => [
                'required', 
                'string', 
                'max:150', 
                Rule::unique('profesiones')->ignore($profesion->id)
            ],
            'observacion' => 'nullable|string'
        ]);

        $profesion->update($validated);

        return redirect()->route('profesiones.index')
            ->with('success', 'Profesión actualizada exitosamente.');
    }

    /**
     * Eliminar profesión
     */
    public function destroy(Profesion $profesion)
    {
        // Verificar si tiene registros asociados
        if ($profesion->registros()->count() > 0) {
            return redirect()->route('profesiones.index')
                ->with('error', 'No se puede eliminar la profesión porque tiene ' . 
                       $profesion->registros()->count() . ' registro(s) asociado(s).');
        }

        $profesion->delete();

        return redirect()->route('profesiones.index')
            ->with('success', 'Profesión eliminada exitosamente.');
    }

    /**
     * API: Obtener todas las profesiones para select
     */
    public function getProfesionesApi()
    {
        return response()->json(Profesion::orderBy('descripcion')->get());
    }
}