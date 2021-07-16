<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IntranetPresolicitudProducto extends Modelo
{
    protected $table = 'intranet_presolicitud_producto';
    protected $fillable = [
       'id_usuario',
       'nombre',
   
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
            'id_usuario'=>'integer',
            'nombre'=>'max:255',
            
        ];
        if ($campo === null) {
            return $reglas;
        }
        return isset($reglas[$campo]) ? $reglas[$campo] : '';
    }
    
    public static function traerData() {
        $campos = [
            'id',
            'nombre',
          
        ];

        return self::orderBy('nombre')
            ->get($campos)
            ->toArray();
    }
   
}
