<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IntranetDocumentoCategoria extends \App\Modelo
{
    protected $table = 'intranet_documento_categoria';
    protected $fillable = [     
        'nombre',
        'id_usuario',
        'id_documento_categoria'

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
            'nombre'=> 'max:255',
            'id_usuario'=>'integer',
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
            'id_usuario',
            'id_documento_categoria'
        ];
        $items = self
            ::whereNull('id_documento_categoria')
            ->orderBy('fecha_creacion');


        return $items
            ->selectRaw(implode(',', $campos))
            ->where('nombre','<>','otros')
            ->whereNull('fecha_eliminacion')
            ->get()
            ->toArray();
        
    }
    
    public static function listaDocumentosDe($id_documento_categoria) {
        $campos = [
            'intranet_documento.id',
            'intranet_documento.nombre',
            'intranet_documento.descripcion',
            'intranet_documento.tipo',
            'intranet_documento.opcional',
            'intranet_documento.proceso',
            'intranet_documento.id_documento_categoria'
        ];

        return self::join('intranet_documento', 'intranet_documento.id_documento_categoria', '=', 'intranet_documento_categoria.id')
            ->where([
            ['intranet_documento_categoria.id', '=', (int)$id_documento_categoria],
            ['intranet_documento.fecha_eliminacion','=',null],
            ['intranet_documento.nombre','<>','pdf_generico'],
            ['intranet_documento.nombre','<>','foto_generico']])
            ->orderBy('intranet_documento.nombre')
            ->selectRaw(implode(',', $campos))
            ->get()
            ->toArray();
    }


  
    public function documentosCarpeta() {
        return $this->hasMany('App\IntranetDocumento', 'id_documento_categoria', 'id');
    }
    public function carpetasHijos() {
        return $this->hasMany('App\IntranetDocumentoCategoria', 'id_documento_categoria', 'id');
    }
   
}
