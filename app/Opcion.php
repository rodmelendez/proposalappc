<?php
/**
 * Created by PhpStorm.
 * User: Alfredo
 * Date: 08-Feb-18
 * Time: 3:03 PM
 */

namespace App;

use Illuminate\Support\Facades\Auth;

class Opcion extends Modelo {

    public $timestamps = true;

    protected $table = 'opcion';

    /**
     * Los atributos que se pueden guardar
     *
     * @var array
     */
    protected $fillable = [
        'id_usuario',
        'nombre',
        'valor',
        'tipo_dato'
    ];

    const TIPO_TEXTO = 0;
    const TIPO_NUMERO = 1;


    /**
     * Devuélve los mensajes de validación para un campo específico o el arreglo de mensajes por defecto
     * Los mensajes se buscan en el archivo de correspondiente en lang
     *
     * @param string $campo     Nombre del campo del que se quiere las reglas de validación.
     * @param int $ignorar_id    ID del elemento que se está editando, si es el caso.
     * @return array|string
     */
    public static function reglasValidacion($campo = null, $ignorar_id = 0) {
        $reglas = [
            'id_usuario'    => 'integer',
            'nombre'        => 'required|max:60',
            'valor'         => 'max:30',
            'tipo_dato'     => 'integer'
        ];
        if ($campo === null) {
            return $reglas;
        }
        return isset($reglas[$campo]) ? $reglas[$campo] : '';
    }


    # RELACIONES


    # FILTROS

    public function scopeNombre($query, $val) {
        return $query->where('nombre', '=', $val);
    }


    # ASIGNACIONES


    # LECTURAS


    # METODOS

    /**
     * Guarda el valor de una opción
     *
     * @param $nombre_opcion
     * @param $valor
     * @param bool $para_usuario
     */
    public static function guardar($nombre_opcion, $valor, $para_usuario = false) {
        $id_usuario = Auth::user()->id;

        $opcion = self::nombre($nombre_opcion);

        if ($para_usuario) {
            $opcion = $opcion->where('id_usuario', '=', (int)$id_usuario);
        }

        $opcion = $opcion->first();

        if (empty($valor)) $valor = '';

        if ($opcion) {
            $opcion->valor = $valor;
            $opcion->id_usuario = $id_usuario;
            $opcion->save();
        }
        else {
            self::create([
                'nombre' => $nombre_opcion,
                'valor' => $valor,
                'id_usuario' => $id_usuario
            ]);
        }
    }


    /**
     * Guarda los valores de un arreglo, asumiendo las claves como propiedades
     *
     * @param $arreglo
     * @param bool $para_usuario
     */
    public static function guardarArreglo($arreglo, $para_usuario = false) {
        if (is_array($arreglo) && count($arreglo)) {
            foreach ($arreglo as $propiedad => $valor) {
                self::guardar($propiedad, $valor, $para_usuario);
            }
        }
    }


    /**
     * Retorna el valor de una opción
     *
     * @param $nombre_opcion
     * @param null $defecto
     * @param null $id_usuario
     * @return float|null
     */
    public static function valor($nombre_opcion, $defecto = null, $id_usuario = null) {
        if (!empty($nombre_opcion)) {
            $opcion = self::nombre($nombre_opcion);

            if (!empty($id_usuario)) {
                $opcion = $opcion->where('id_usuario', '=', (int)$id_usuario);
            }

            $opcion = $opcion->first();

            if ($opcion) {
                if ($opcion->tipo_dato == self::TIPO_NUMERO) {
                    return floatval($opcion->valor);
                }
                return $opcion->valor;
            }
        }
        return $defecto;
    }


    /**
     * Retorna un arreglo con las opciones y valores
     *
     * @param null $id_usuario
     * @return array
     */
    public static function valores($id_usuario = null) {
        if (!empty($id_usuario)) {
            return self
                ::where('id_usuario', '=', (int)$id_usuario)
                ->get()
                ->pluck('valor', 'nombre')
                ->toArray();
        }

        return self
            ::get()
            ->pluck('valor', 'nombre')
            ->toArray();
    }


    public static function verificarPropiedadesBooleanos($attrs_a_verificar, $attrs) {
        foreach ($attrs_a_verificar as $attr) {
            if (!in_array($attr, $attrs)) {
                self::guardar($attr, '0');
            }
        }
    }

}