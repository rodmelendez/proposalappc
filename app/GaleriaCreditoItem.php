<?php
/**
 * Created by PhpStorm.
 * User: Alfredo Fleming
 * Date: 13/5/2019
 * Time: 4:40 PM
 */

namespace App;

class GaleriaCreditoItem extends Modelo {

    public $timestamps = true;

    protected $table = 'galeria_credito_item';

    /**
     * Los atributos que se pueden guardar
     *
     * @var array
     */
    protected $fillable = [
        'id_usuario',
        'id_galeria_credito',
        'nombre',
        'tipo',
        'foto',
        'fecha',
        'visible',
        'observaciones',
        'ancho',
        'alto',
        'latitud',
        'longitud',
        'kb',
        'iso',
        'apertura',
        'fecha_captura',
        'nombre_original',
        'camara',
        'indice',
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
            'id_usuario'            => 'integer',
            'id_galeria_credito'    => 'integer',
            'nombre'                => 'max:63',
            'tipo'                  => 'integer',
            'foto'                  => 'max:31',
            'fecha'                 => 'nullable|date_format:d/m/Y',
            'visible'               => 'boolean',
            'observaciones'         => 'max:255',
            'ancho'                 => 'integer',
            'alto'                  => 'integer',
            'latitud'               => 'max:31',
            'longitud'              => 'max:31',
            'kb'                    => 'integer',
            'iso'                   => 'max:31',
            'apertura'              => 'max:31',
            'fecha_captura'         => 'nullable|date_format:d/m/Y',
            'nombre_original'       => 'max:63',
            'camara'                => 'max:31',
            'indice'                => 'integer',
        ];
        if ($campo === null) {
            return $reglas;
        }
        return isset($reglas[$campo]) ? $reglas[$campo] : '';
    }

    public static function tipos() {
        //GaleriaItem.vue (tipos)
        return [
            1 => 'Garantía',
            2 => 'Negocio',
            3 => 'Ubicación',
            4 => 'Inventario',
            5 => 'Documento',
        ];
    }


    # RELACIONES


    # FILTROS


    # ASIGNACIONES

    public function setLatitudAttribute($val) {
        if (!empty($val)) {
            $this->attributes['latitud'] = str_replace(',', '.', $val);
        }
    }

    public function setLongitudAttribute($val) {
        if (!empty($val)) {
            $this->attributes['longitud'] = str_replace(',', '.', $val);
        }
    }


    # LECTURAS

    public function getNombreAttribute() {
        return ucfirst($this->attributes['nombre']);
    }


    # METODOS

    public static function traerData($campos = null) {
        $campos = [
            'id',
            'id_galeria_credito',
            'nombre',
            'tipo',
            'foto',
            'fecha',
            'visible',
            'observaciones',
            'ancho',
            'alto',
            'latitud',
            'longitud',
            'kb',
            'iso',
            'apertura',
            'fecha_captura',
            'nombre_original',
            'camara',
            'indice',
            'fecha_creacion'
        ];
        return self::orderBy('fecha_creacion')
            ->get($campos)
            ->toArray();
    }


    public static function items($id_galeria_credito) {
        return self
            ::where('id_galeria_credito', '=', (int)$id_galeria_credito)
            ->orderBy('indice')
            ->get();
    }

}