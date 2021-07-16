<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IntranetEvento extends Modelo
{
    protected $table = 'intranet_evento';

    protected $fillable = [
        'id_cliente',
        'id_usuario',
        'latitud',
        'longitud',
        'evento',
        'descripcion',
        'fecha_registro',
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
            'id_cliente'=> 'integer',
            'id_usuario' => 'integer',
            //'fecha_registro' =>'required|string',
            'latitud' => 'string',
            'longitud' => 'string',
            'evento' => 'string',
            'estado' => 'max:30',
            'descripcion' => 'string',
        ];
        if ($campo === null) {
            return $reglas;
        }
        return isset($reglas[$campo]) ? $reglas[$campo] : '';
    }

    /*************/
    // un evento pertenece a un cliente
    public function cliente() {
        return $this->belongsTo('App\IntranetCliente', 'id_cliente', 'id');
    }

    //Un evento pertenece a un usuario
    public function usuario() {
        return $this->belongsTo('App\User', 'id_usuario', 'id');
    }



}
