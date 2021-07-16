<?php
namespace App;

class UsuarioTouchId extends Modelo {

    public $timestamps = true;

    protected $table = 'usuario_touchid';

    /**
     * Los atributos que se pueden guardar
     *
     * @var array
     */
    protected $fillable = [
        'id_usuario',
        'id_empleado',
        'imei',
        'serial_dispositivo',
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
            'id_empleado'           => 'required|integer',
            'imei'                  => 'max:63',
            'serial_dispositivo'    => 'max:63',
        ];
        if ($campo === null) {
            return $reglas;
        }
        return isset($reglas[$campo]) ? $reglas[$campo] : '';
    }


    # RELACIONES


    # FILTROS


    # ASIGNACIONES


    # LECTURAS


    # METODOS

    public static function traerData($desde = 1, $cantidad_pagina = 0, $busqueda = null) {
        $campos = [
            'usuario_touchid.id',
            'persona.primer_nombre',
            'persona.segundo_nombre',
            'persona.primer_apellido',
            'persona.segundo_apellido',
            'persona.dni',
            'usuario_touchid.imei',
            'usuario_touchid.serial_dispositivo',
            'usuario_touchid.fecha_creacion',
        ];

        return self
            /*::join('usuario', 'usuario_touchid.id_usuario', '=', 'usuario.id', 'left')
            ->join('usuario_persona', 'usuario_persona.id_usuario', '=', 'usuario.id', 'left')
            ->join('persona', 'usuario_persona.id_persona', '=', 'persona.id', 'left')*/
            ::join('empleado', 'usuario_touchid.id_empleado', '=', 'empleado.id')
            ->join('persona', 'empleado.id_persona', '=', 'persona.id')
            ->orderBy('usuario_touchid.fecha_creacion')
            ->selectRaw(implode(',', $campos))
            ->get()
            ->toArray();
    }

}