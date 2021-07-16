<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class IntranetPais extends \App\Modelo
{
    protected $table = 'intranet_pais';
    protected $fillable = ['nombre'];
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
            'nombre'  => 'required|max:63',
        ];
        if ($campo === null) {
            return $reglas;
        }
        return isset($reglas[$campo]) ? $reglas[$campo] : '';
    }

    
    // un pais tiene muchos departamentos
    public function departamento()
    {
        return hasMany('App\IntranetDepartamento','id_pais','id');
    }
    public static function traerData($campos = null) {
        $campos = [
            'id',
            'nombre',
        ];

        //var_dump($obj); dd();
        try {
            return self::orderBy('nombre')
            ->get($campos)
            ->toArray();


          
            //code...
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['traza'=>$th->getTrace(), 'error'=> $th ],400);
            
        }
    }

}
