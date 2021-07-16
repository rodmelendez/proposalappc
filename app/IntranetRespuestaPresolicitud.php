<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IntranetRespuestaPresolicitud extends Modelo
{
    protected $table = 'intranet_respuesta_presolicitud';
    protected $fillable = [
        'id_pregunta',
        'id_usuario',
        'respuesta',
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
            'id_pregunta'=>'integer',  
            'id_usuario'=>'integer',
            'fecha'=>'date',
            'respuesta'=>'max:255'
         
        ];
        if ($campo === null) {
            return $reglas;
        }
        return isset($reglas[$campo]) ? $reglas[$campo] : '';
    }
    public static function traerData() {

        $campos = [
            'id',
            'id_pregunta',
            'id_usuario',
            'respuesta',
            'fecha'
        ];

        return self::orderBy('fecha')
        ->get($campos)
        ->toArray();
    }

 
    public function pregunta(){
        return $this->belongsTo('App\IntranetPreguntaPresolicitud', 'id_pregunta', 'id');
    }
    public function usuario() {
        return $this->belongsTo('App\User', 'id_usuario', 'id');
    }
}
