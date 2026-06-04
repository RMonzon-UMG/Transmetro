<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Acceso extends Model {
    protected $table = 'accesos';
    protected $fillable = ['estacion_id', 'descripcion'];
    public function estacion() { return $this->belongsTo(Estacion::class); }
    public function guardia() { return $this->hasOne(Guardia::class); }
    public function guardias() { return $this->hasMany(Guardia::class); }
}