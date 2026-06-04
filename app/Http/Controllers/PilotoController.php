<?php

namespace App\Http\Controllers;

use App\Models\Bus;
use App\Models\HistorialEducativo;
use App\Models\Piloto;
use Illuminate\Http\Request;

class PilotoController extends Controller
{
    public function index()
    {
        $pilotos = Piloto::with(['bus.linea'])->orderBy('nombre')->get();
        return view('pilotos.index', compact('pilotos'));
    }

    public function create()
    {
        $buses = Bus::with('linea')->doesntHave('piloto')->orderBy('placa')->get();
        return view('pilotos.create', compact('buses'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'    => ['required', 'string', 'max:150'],
            'direccion' => ['required', 'string', 'max:255'],
            'telefono'  => ['required', 'string', 'max:20'],
            'email'     => ['required', 'email', 'max:150', 'unique:pilotos,email'],
            'bus_id'    => ['nullable', 'exists:buses,id'],
            'historial'             => ['nullable', 'array'],
            'historial.*.institucion'     => ['required_with:historial', 'string', 'max:150'],
            'historial.*.titulo'          => ['required_with:historial', 'string', 'max:150'],
            'historial.*.anio_graduacion' => ['required_with:historial', 'integer', 'min:1950', 'max:' . date('Y')],
        ]);

        $piloto = Piloto::create([
            'bus_id'    => $data['bus_id'] ?? null,
            'nombre'    => $data['nombre'],
            'direccion' => $data['direccion'],
            'telefono'  => $data['telefono'],
            'email'     => $data['email'],
        ]);

        foreach ($data['historial'] ?? [] as $h) {
            $piloto->historialEducativo()->create($h);
        }

        return redirect()->route('pilotos.index')->with('success', "Piloto '{$piloto->nombre}' registrado.");
    }

    public function show(Piloto $piloto)
    {
        $piloto->load(['bus.linea', 'historialEducativo']);
        return view('pilotos.show', compact('piloto'));
    }

    public function edit(Piloto $piloto)
    {
        $piloto->load('historialEducativo');
        $assignedBusIds = Piloto::whereNotNull('bus_id')
            ->where('id', '!=', $piloto->id)
            ->pluck('bus_id');
        $buses = Bus::with('linea')
            ->whereNotIn('id', $assignedBusIds)
            ->orderBy('placa')
            ->get();
        return view('pilotos.edit', compact('piloto', 'buses'));
    }

    public function update(Request $request, Piloto $piloto)
    {
        $data = $request->validate([
            'nombre'    => ['required', 'string', 'max:150'],
            'direccion' => ['required', 'string', 'max:255'],
            'telefono'  => ['required', 'string', 'max:20'],
            'email'     => ['required', 'email', 'max:150', 'unique:pilotos,email,' . $piloto->id],
            'bus_id'    => ['nullable', 'exists:buses,id'],
            'historial'             => ['nullable', 'array'],
            'historial.*.id'              => ['nullable', 'exists:historial_educativos,id'],
            'historial.*.institucion'     => ['required_with:historial', 'string', 'max:150'],
            'historial.*.titulo'          => ['required_with:historial', 'string', 'max:150'],
            'historial.*.anio_graduacion' => ['required_with:historial', 'integer', 'min:1950', 'max:' . date('Y')],
        ]);

        $piloto->update([
            'bus_id'    => $data['bus_id'] ?? null,
            'nombre'    => $data['nombre'],
            'direccion' => $data['direccion'],
            'telefono'  => $data['telefono'],
            'email'     => $data['email'],
        ]);

        // Sync historial: borrar los eliminados, crear/actualizar los presentes
        $idsNuevos = collect($data['historial'] ?? [])->pluck('id')->filter()->values();
        $piloto->historialEducativo()->whereNotIn('id', $idsNuevos)->delete();

        foreach ($data['historial'] ?? [] as $h) {
            if (! empty($h['id'])) {
                HistorialEducativo::where('id', $h['id'])->update([
                    'institucion'     => $h['institucion'],
                    'titulo'          => $h['titulo'],
                    'anio_graduacion' => $h['anio_graduacion'],
                ]);
            } else {
                $piloto->historialEducativo()->create($h);
            }
        }

        return redirect()->route('pilotos.index')->with('success', "Piloto '{$piloto->nombre}' actualizado.");
    }

    public function destroy(Piloto $piloto)
    {
        $nombre = $piloto->nombre;
        $piloto->historialEducativo()->delete();
        $piloto->delete();
        return redirect()->route('pilotos.index')->with('success', "Piloto '{$nombre}' eliminado.");
    }
}
