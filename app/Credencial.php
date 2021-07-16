<?php
/**
 * Created by PhpStorm.
 * User: Alfredo Fleming
 * Date: 30/4/2019
 * Time: 10:27 AM
 */

namespace App;

class Credencial extends Modelo {

    public $timestamps = true;

    protected $table = 'credencial';

    private $key = 'Jamit!';

    /**
     * Los atributos que se pueden guardar
     *
     * @var array
     */
    protected $fillable = [
        'id_usuario',
        'nombre',
        'usuario',
        'contrasena',
        'puerto',
        'dominio',
        'protocolo',
        'categoria',
        'id_credencial_categoria',
        'correo',
        'telefono',
        'url',
        'tipo',
    ];

    protected $hidden = [
        'contrasena',
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
            'nombre'                    => 'max:63',
            'usuario'                   => 'max:63',
            'contrasena'                => 'max:63',
            'puerto'                    => 'max:31',
            'dominio'                   => 'max:63',
            'protocolo'                 => 'max:63',
            'categoria'                 => 'max:63',
            'id_credencial_categoria'   => 'integer',
            'correo'                    => 'max:63',
            'telefono'                  => 'max:63',
            'url'                       => 'max:127',
            'tipo'                      => 'integer',
        ];
        if ($campo === null) {
            return $reglas;
        }
        return isset($reglas[$campo]) ? $reglas[$campo] : '';
    }


    # RELACIONES


    # FILTROS


    # ASIGNACIONES

    /*public function setContrasena($val) {
        $this->attributes['contrasena'] = $this->encriptar($val);
    }*/


    # LECTURAS

    public function getNombreAttribute() {
        return ucfirst($this->attributes['nombre']);
    }

    /*public function getContrasenaAttribute() {
        $decrypted = $this->desencriptar($this->attributes($val))

        return $decrypted;
    }*/


    # METODOS

    public static function traerData() {
        $campos = [
            'credencial.id',
            'credencial.nombre',
            'credencial.usuario',
            'credencial.contrasena',
            'credencial.puerto',
            'credencial.dominio',
            'credencial.protocolo',
            'credencial.imagen',
            'credencial_categoria.nombre AS categoria',
            'credencial.id_credencial_categoria',
            'credencial.correo',
            'credencial.telefono',
            'credencial.url',
            'credencial.tipo',
            'credencial.fecha_creacion'
        ];

        $items = self
            ::join('credencial_categoria', 'credencial.id_credencial_categoria', '=', 'credencial_categoria.id', 'left')
            ->orderBy('credencial.fecha_actualizacion', 'DESC');

        self::verificarPermiso($items, 'credenciales', 'credencial');

        return $items->selectRaw(implode(',', $campos))
            ->get()
            ->toArray();
    }


    public function encriptar($val) {
        $encrypted = openssl_encrypt($val,'AES-128-ECB', $this->key);
        return $encrypted;
    }


    public function desencriptar($val = null) {
        if ($val === null) $val = $this->contrasena;
        $decrypted = openssl_decrypt($val,'AES-128-ECB', $this->key);
        return $decrypted;
    }

}