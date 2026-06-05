<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Estacion extends Model {
    protected $table = 'estaciones';
    protected $fillable = ['municipalidad_id', 'nombre', 'capacidad_maxima', 'ocupacion_actual'];
    public function municipalidad() { return $this->belongsTo(Municipalidad::class); }
    public function lineas() {
        return $this->belongsToMany(Linea::class, 'linea_estacion')->withPivot('orden', 'distancia');
    }
    public function accesos() { return $this->hasMany(Acceso::class); }
    public function parqueos() { return $this->hasMany(Parqueo::class); }
    public function alertas() { return $this->hasMany(Alerta::class); }
    public function registroEsperas() { return $this->hasMany(RegistroEspera::class); }
    public function operadores() { return $this->hasMany(Operador::class); }
    public function porcentajeOcupacion(): float {
        if (empty($this->capacidad_maxima)) return 0;
        return round(($this->ocupacion_actual / $this->capacidad_maxima) * 100, 1);
    }
    public function generarAlertaSiNecesario(): void {
        if ($this->capacidad_maxima > 0 && $this->ocupacion_actual >= ($this->capacidad_maxima * 0.5)) {
            $this->alertas()->create(['tipo' => 'ocupacion', 'fecha_hora' => now(), 'atendida' => false]);
        }
    }
}