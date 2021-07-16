<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IntranetPreguntaPresolicitud extends \App\Modelo
{
    protected $table = 'intranet_pregunta_presolicitud';
    protected $fillable = [
        'id_presolicitud',
        'id_usuario',
        'pregunta',
        'estatus',
        'fecha'
    ];


     /**
     * Devuélve las reglas de validación para un campo específico o el arreglo de reglas por defecto
     *
     * @param string $campo     Nombre del campo del que se quiere las reglas de validación.
     * @param int $ignorar_id    ID del elemento que se está editando, si es el caso.
     * @return array|string
     */
    public static function reglasValidacion($campo = null, $ignorar_id = 0) {
        $reglas = [
            'id_presolicitud'=>'integer',
            'id_usuario'=>'integer',
            'pregunta'=>'max:255',
            'fecha'=>'date'

        ];
        if ($campo === null) {
            return $reglas;
        }
        return isset($reglas[$campo]) ? $reglas[$campo] : '';
    }


    public static function traerData() {

        $campos = [
            'id',
            'id_presolicitud',
            'id_usuario',
            'pregunta',
            'estatus',
            'fecha'
        ];

        return self::orderBy('fecha')
        ->get($campos)
        ->toArray();
    }

    public function presolicitud() {
        return $this->belongsTo('App\IntranetPresolicitud', 'id_presolicitud', 'id');
    }
    public function respuestas(){
        return $this->hasMany('App\IntranetRespuestaPresolicitud', 'id_pregunta', 'id');
    }
    public function usuario() {
        return $this->belongsTo('App\User', 'id_usuario', 'id');
    }
}
