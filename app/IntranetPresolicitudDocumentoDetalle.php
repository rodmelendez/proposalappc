<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IntranetPresolicitudDocumentoDetalle extends \App\Modelo
{

    protected $table = 'intrenet_presolicitud_documento_detalle';
    protected $fillable = [
        'id_presolicitud_documento',
        'observaciones',
        'ancho',
        'alto',
        'latitud',
        'longitud',
        'kb',
        'fecha_captura',
        'nombre_original',
        'camara',
        'id_usuario'
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
            'id_presolicitud_documento'=>'integer',
            'observaciones'=>'max:255',
            'ancho'=>'integer',
            'alto'=>'integer',
            'latitud'=>'max:255',
            'longitud'=>'max:255',
            'kb'=>'integer',
            'fecha_captura'=>'max:63',
            'nombre_original'=>'max:63',
            'camara'=>'max:63',
            'id_usuario'=>'integer'
         
        ];
        if ($campo === null) {
            return $reglas;
        }
        return isset($reglas[$campo]) ? $reglas[$campo] : '';
    }
    
    public static function traerData() {
        $campos = [
            'id',
            'id_presolicitud_documento',
            'observaciones',
            'ancho',
            'alto',
            'latitud',
            'longitud',
            'kb',
            'fecha_captura',
            'nombre_original',
            'camara',
            'id_usuario'
        ];

        return self::orderBy('nombre')
            ->get($campos)
            ->toArray();
    }

}
