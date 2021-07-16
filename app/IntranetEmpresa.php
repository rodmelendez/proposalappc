<?php

namespace App;


class IntranetEmpresa extends \App\Modelo
{
    protected $table = 'intranet_empresa';
    protected $fillable = [
        'nombre',
        'razon',
        'ruc',
        'id_cliente'
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
            'nombre'=> 'max:63',
            'razon'=> 'max:63',
            'ruc'=> 'max:63|nullable',
            'id_cliente'=> 'integer',         
        ];
        if ($campo === null) {
            return $reglas;
        }
        return isset($reglas[$campo]) ? $reglas[$campo] : '';
    }
    public static function traerData() {
        $campos = [
            'intranet_empresa.nombre',
            'intranet_empresa.razon',
            'intranet_empresa.ruc',
            'intranet_empresa.id_cliente',
            'intranet_cliente.nombre as nombreCliente'
        ];

        return self::orderBy('intranet_empresa.nombre')
            ->leftJoin('intranet_cliente','intranet_cliente.id','=','id_cliente')
            ->get($campos)
            ->toArray();
    }
    //una empresa puede ser muchos clientes, si estas leyendo esto y no entiendes pues te explico 
    // 1 empresa puede pedir 10 trabajos distintos, y para cada trabajo distinto, tendre un codigo de cliente diferente
    //este codigo sera su ID, en caso de que no entiendas, chamo busca otro trabajo de backend :c 
    public function cliente() {
        return $this->belongsTo('App\IntranetCliente', 'id_cliente', 'id');
    }
    }
