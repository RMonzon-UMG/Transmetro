<?php

namespace App\Http\Controllers;

use App\Models\Alerta;
use App\Models\Bus;
use App\Models\Estacion;
use App\Models\Linea;
use App\Models\Piloto;
use App\Models\RegistroEspera;

class DashboardController extends Controller
{
    public function index()
    {
        $operador = auth()->user();

        $alertaQuery = Alerta::where('atendida', false);
        if ($operador->esOperador() && $operador->estacion_id) {
            $alertaQuery->where('estacion_id', $operador->estacion_id);
        }

        $stats = [
            'lineas'    => Linea::count(),
            'estaciones'=> Estacion::count(),
            'buses'     => Bus::count(),
            'pilotos'   => Piloto::count(),
            'alertas'   => (clone $alertaQuery)->count(),
        ];

        // Alertas pendientes (últimas 10)
        $alertas = (clone $alertaQuery)->with('estacion')
            ->orderByDesc('fecha_hora')
            ->limit(10)
            ->get();

        // Registros de espera (últimos 10)
        $esperas = RegistroEspera::with(['bus', 'estacion'])
            ->orderByDesc('fecha_hora')
            ->limit(10)
            ->get();

        // Estaciones con alta ocupación (>= 75 %)
        $estacionesCriticas = Estacion::whereRaw('ocupacion_actual / capacidad_maxima >= 0.75')
            ->orderByRaw('ocupacion_actual / capacidad_maxima DESC')
            ->limit(5)
            ->get();

        return view('dashboard', compact('stats', 'alertas', 'esperas', 'estacionesCriticas', 'operador'));
    }
}
