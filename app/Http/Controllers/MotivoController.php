<?php
// app/Http/Controllers/MotivoController.php

namespace App\Http\Controllers;

use App\Models\Motivo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MotivoController extends Controller
{
    public function index()
    {
        $motivos = Motivo::withCount('registros')
            ->orderBy('descripcion')
            ->paginate(15);
        
        return view('motivos.index', compact('motivos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'descripcion' => 'required|string|max:200|unique:motivos,descripcion',
            'observacion' => 'nullable|string'
        ]);

        Motivo::create($validated);

        return redirect()->route('motivos.index')
            ->with('success', 'Motivo creado exitosamente.');
    }

    public function update(Request $request, Motivo $motivo)
    {
        $validated = $request->validate([
            'descripcion' => [
                'required', 
                'string', 
                'max:200', 
                Rule::unique('motivos')->ignore($motivo->id)
            ],
            'observacion' => 'nullable|string'
        ]);

        $motivo->update($validated);

        return redirect()->route('motivos.index')
            ->with('success', 'Motivo actualizado exitosamente.');
    }

    public function destroy(Motivo $motivo)
    {
        if ($motivo->registros()->count() > 0) {
            return redirect()->route('motivos.index')
                ->with('error', 'No se puede eliminar el motivo porque tiene ' . 
                       $motivo->registros()->count() . ' registro(s) asociado(s).');
        }

        $motivo->delete();

        return redirect()->route('motivos.index')
            ->with('success', 'Motivo eliminado exitosamente.');
    }
}