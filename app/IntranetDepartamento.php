<?php

namespace App;


class IntranetDepartamento extends Modelo
{
    
    protected $table = 'intranet_departamento';
    protected $fillable = ['id_pais','nombre'];
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
            'id_pais'=>'integer',
            'nombre'  => 'required||max:63',
        ];
        if ($campo === null) {
            return $reglas;
        }
        return isset($reglas[$campo]) ? $reglas[$campo] : '';
    }

    public static function traerData() {
        $campos = [
            'intranet_departamento.id', 
            'intranet_departamento.id_pais',
            'intranet_pais.nombre as pais',
            'intranet_departamento.nombre'  
        ];
    
      
        return self::orderBy('intranet_departamento.nombre')
            ->leftJoin('intranet_pais','id_pais','=','intranet_pais.id')
            ->get($campos)
            ->toArray();
    }
    
    public function departamento()
    {
        return belongsTo('App\IntranetPais','id_pais','id');
    }

    public function municipio(Type $var = null)
    {
        return hasMany('App\IntranetMunicipio','id_municipio','id');
    }
}
