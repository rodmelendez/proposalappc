<?php
/**
 * Created by PhpStorm.
 * User: Alfredo Fleming
 * Date: 14/6/2019
 * Time: 6:11 PM
 */

namespace App;

class DocumentoCategoria extends Modelo {

    public $timestamps = true;

    protected $table = 'documento_categoria';

    /**
     * Los atributos que se pueden guardar
     *
     * @var array
     */
    protected $fillable = [
        'id_usuario',
        'id_documento_categoria',
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
            'id_usuario'                => 'integer',
            'id_documento_categoria'    => 'nullable|integer',
            'nombre'                    => 'max:63',
        ];
        if ($campo === null) {
            return $reglas;
        }
        return isset($reglas[$campo]) ? $reglas[$campo] : '';
    }


    # RELACIONES

    public function documentos() {
        return $this->hasMany('App\Documento', 'id_documento_categoria', 'id');
    }


    # FILTROS


    # ASIGNACIONES
    
    public function setIdDocumentoCategoriaAttribute($val) {
        if (!empty($val)) {
            $this->attributes['id_documento_categoria'] = (int)$val;
        }
    }


    # LECTURAS


    # METODOS

    public static function traerData($campos = null) {
        $campos = [
            'id',
            'id_documento_categoria',
            'nombre',
            'fecha_creacion'
        ];

        $items = self
            ::whereNull('id_documento_categoria')
            ->orderBy('nombre');

        self::verificarPermiso($items, 'categorias-documentos');

        return $items
            ->selectRaw(implode(',', $campos))
            ->get()
            ->toArray();

        /*return [
            [
                'id' => 1,
                'nombre' => 'item 1',
                'items' => [
                    [
                        'id' => 3,
                        'nombre' => 'item 1.1',
                        'items' => []
                    ]
                ],
            ],
            [
                'id' => 2,
                'nombre' => 'item 2',
            ]
        ];*/
    }


    public function listaDocumentos() {
        $items = $this
            ->documentos()
            ->get();

        $lista = [];

        foreach ($items as $item) {
            $lista[] = [
                'id' => $item->id,
                'nombre' => $item->nombre,
                'archivo' => $item->archivo,
            ];
        }

        return $lista;
    }


    public static function listaDocumentosDe($id_documento_categoria) {
        $campos = [
            'documento.id',
            'documento.nombre',
            'documento.archivo',
        ];

        return self::join('documento', 'documento.id_documento_categoria', '=', 'documento_categoria.id')
            ->where('documento_categoria.id', '=', (int)$id_documento_categoria)
            ->orderBy('documento.nombre')
            ->selectRaw(implode(',', $campos))
            ->get()
            ->toArray();
    }

}