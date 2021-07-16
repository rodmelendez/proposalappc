<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IntranetMunicipio extends Modelo
{
    protected $table = 'intranet_municipio';
    protected $fillable = ['id_departamento','nombre'];
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
            'id_departamento'=>'integer',
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
            'intranet_municipio.id_departamento',
            'intranet_municipio.id', 
            'intranet_pais.nombre as pais',
             'intranet_departamento.nombre as departamento',
            'intranet_municipio.nombre'  
        ];
    
      
        return self::orderBy('intranet_municipio.nombre')
            ->leftJoin('intranet_departamento','id_departamento','=','intranet_departamento.id')
            ->leftJoin('intranet_pais','intranet_departamento.id_pais','=','intranet_pais.id')
            ->get($campos)
            ->toArray();
    }
    //un municipio pertenece a u departamento
    public function municipio()
    {
        return belongsTo('App\IntranetDepartamento','id_departamento','id');
    }
    //un municipio tiene muchos guettos 
    public function barrio()
    {
        return hasMany('App\IntranetBarrio','id_barrio','id');
    }
}
