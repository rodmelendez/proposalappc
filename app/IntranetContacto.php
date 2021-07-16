<?php

namespace App;

use 
    Illuminate\Database\Eloquent\Model;

class IntranetContacto extends \App\Modelo
{
    protected $table = 'intranet_contacto';
    protected $fillable = [     
       'id_cliente',
       'tipo',
       'pertenece',
       'descripcion',
       'observacion',
    ];
    protected $hidden = ['created_at','updated_at'];
    
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
            'tipo'=>'max:255',
            'pertenece'=>'max:255',
            'descripcion'=>'max:255',
            'observacion'=>'max:255',v
            
        ];
        if ($campo === null) {
            return $reglas;
        }
        return isset($reglas[$campo]) ? $reglas[$campo] : '';
    }

    public static function traerData() {
        $campos = [
            'id_cliente',
            'tipo',
            'pertenece',
            'descripcion',
            'observacion'
        ];
        self::orderBy('id_cliente');

        return self::orderBy('id_cliente')
            ->get($campos)
            ->toArray();
    }
    // un contacto pertence a un cliente
    public function cliente() {
        return $this->belongsTo('App\IntranetCliente', 'id_cliente', 'id');
    }
}
