<?php

namespace App\Http\Controllers;

use App\Models\Alerta;
use App\Models\Bus;
use App\Models\Estacion;
use App\Models\RegistroEspera;
use Illuminate\Http\Request;

class AlertaController extends Controller
{
    public function index()
    {
        $operador = auth()->user();

        $query = Alerta::with(['estacion', 'operadorAtencion'])->orderByDesc('fecha_hora');

        // Operador solo ve alertas de su estación
        if ($operador->esOperador() && $operador->estacion_id) {
            $query->where('estacion_id', $operador->estacion_id);
        }

        $alertas  = $query->paginate(20);
        $buses    = Bus::with('linea')->orderBy('placa')->get();
        $estaciones = Estacion::orderBy('nombre')->get();

        return view('alertas.index', compact('alertas', 'buses', 'estaciones', 'operador'));
    }

    public function atender(Alerta $alerta)
    {
        $alerta->update([
            'atendida'     => true,
            'atendida_por' => auth()->user()->id,
        ]);

        return back()->with('success', "Alerta #{$alerta->id} marcada como atendida.");
    }

    public function registrarEspera(Request $request)
    {
        $data = $request->validate([
            'bus_id'        => ['required', 'exists:buses,id'],
            'estacion_id'   => ['required', 'exists:estaciones,id'],
            'minutos_espera'=> ['required', 'integer', 'min:1', 'max:120'],
        ]);

        RegistroEspera::create(array_merge($data, ['fecha_hora' => now()]));

        return back()->with('success', 'Registro de espera guardado correctamente.');
    }
}
