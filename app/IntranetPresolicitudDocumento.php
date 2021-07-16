<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IntranetPresolicitudDocumento extends \App\Modelo
{
    protected $table = 'intranet_presolicitud_documento';
    protected $fillable = [
       'id_presolicitud',
       'id_documento', 
       'id_carpeta',
       'id_subcarpeta',
       'tipo',//1 imagenes, 2 pdf;
       'fecha',//fecha de registro
       'fecha_vencimiento',//fecha de vencimiento del documento
       'fecha_entrega',
       'nombre',
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
            'id_presolicitud'=>'integer',
            'id_documento'=>'integer',
            'id_carpeta'=>'integer',
                
            'tipo'=>'integer',//1 imagenes, 2 pdf;
            'fecha'=>'max:63',//fecha de registro
            'fecha_vencimiento'=>'max:63',//fecha de vencimiento del documento
            'fecha_entrega'=>'max:63',
            'nombre'=>'max:255',
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
            'id_presolicitud',
            'id_documento',
            'id_carpeta',
            'id_subcarpeta',
            'tipo',//1 imagenes, 2 pdf;
            'fecha',//fecha de registro
            'fecha_vencimiento',//fecha de vencimiento del documento
            'fecha_entrega',
            'nombre',
            'id_usuario',
            'generico'
        ];

        return self::orderBy('nombre')
            ->get($campos)
            ->toArray();
    }
   
    public function metadata(){
        return $this->hasMany('App\IntranetPresolicitudDocumentoDetalle','id_presolicitud_documento','id');
    }
}
