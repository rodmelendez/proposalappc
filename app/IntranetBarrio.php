<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IntranetBarrio extends \App\Modelo
{
    
    protected $table = 'intranet_barrio';
    protected $fillable = ['id_municipio','nombre'];
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
            'id_municipio'=>'integer',
            'nombre'  => 'required||max:63',
        ];
        if ($campo === null) {
            return $reglas;
        }
        return isset($reglas[$campo]) ? $reglas[$campo] : '';
    }


    public static function traerData() {
        $campos = [
            'intranet_pais.id as id_pais',
            'intranet_departamento.id as id_departamento', 
            'intranet_barrio.id_municipio',
            'intranet_barrio.id',
            'intranet_pais.nombre as pais',
            'intranet_departamento.nombre as departamento',
            'intranet_municipio.nombre as municipio',
            'intranet_barrio.nombre' 
        ];
        

        return self::orderBy('intranet_barrio.nombre')
        ->leftJoin('intranet_municipio','intranet_barrio.id_municipio','=','intranet_municipio.id')
        ->leftJoin('intranet_departamento','intranet_municipio.id_departamento','=','intranet_departamento.id')
        ->leftJoin('intranet_pais','intranet_departamento.id_pais','=','intranet_pais.id')
        ->get($campos)
        ->toArray();
    }

    public static function traerBarrio($id) {
        $campos = [
            'intranet_pais.id as id_pais',
            'intranet_departamento.id as id_departamento', 
            'intranet_barrio.id_municipio',
            'intranet_barrio.id',
            'intranet_pais.nombre as pais',
            'intranet_departamento.nombre as departamento',
            'intranet_municipio.nombre as municipio',
            'intranet_barrio.nombre' 
        ];
        

        return self::orderBy('intranet_barrio.nombre')
        ->where('intranet_barrio.id','=',$id)
        ->leftJoin('intranet_municipio','intranet_barrio.id_municipio','=','intranet_municipio.id')
        ->leftJoin('intranet_departamento','intranet_municipio.id_departamento','=','intranet_departamento.id')
        ->leftJoin('intranet_pais','intranet_departamento.id_pais','=','intranet_pais.id')
        ->get($campos)->first();
    }
    //un guetto pertenece a un municipio
    public function barrio()
    {
        return belongsTo('App\IntranetMunicipio','id_municipio','id');
    }
    //un barrio tiene muchas dirreciones y si te metes por la que no es vas a llevar plomo el mio 
    public function direccion()
    {
        return hasMany('App\IntranetDireccion','id_barrio','id');
    }
}
