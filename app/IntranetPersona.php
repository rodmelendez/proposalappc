<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IntranetPersona extends \App\Modelo
{
    protected $table = 'intranet_persona';
    protected $fillable = [
        'primer_nombre'
        ,'segundo_nombre'
        ,'primer_apellido'
        ,'segundo_apellido'
        ,'pasaporte'
        ,'dni'
        ,'ruc'
        ,'genero'
        ,'fecha_nacimiento'
        ,'id_cliente'
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
            'primer_nombre'  => 'max:63',
            'segundo_nombre' => 'max:63',
            'primer_apellido' => 'max:63',
            'segundo_apellido'=> 'max:63',
            'pasaporte'   => 'max:63',
            'dni'         => 'max:63|nullable',
            'ruc'         => 'max:63|nullable',
            'genero'          => 'max:63',
            'fecha_nacimiento'=>'max:30',
            'id_cliente' => 'integer',
         
        ];
        if ($campo === null) {
            return $reglas;
        }
        return isset($reglas[$campo]) ? $reglas[$campo] : '';
    }
    
    public static function traerData() {
        $campos = [
            'intranet_persona.id',
            'intranet_persona.primer_nombre' ,
            'intranet_persona.segundo_nombre',
            'intranet_persona.primer_apellido',
            'intranet_persona.segundo_apellido',
            'intranet_persona.pasaporte'  ,
            'intranet_persona.dni'        ,
            'intranet_persona.ruc'        ,
            'intranet_persona.genero'         ,
            'intranet_persona.fecha_nacimiento',
            'intranet_persona.id_cliente',
            'intranet_cliente.nombre as nombreCliente'
        ];

        return self::orderBy('intranet_persona.primer_nombre')
            ->leftJoin('intranet_cliente','intranet_cliente.id','=','intranet_persona.id_cliente')
            ->get($campos)
            ->toArray();
    }
    public function cliente() {
        return $this->belongsTo('App\IntranetCliente', 'id_cliente', 'id');
    }
    


}
