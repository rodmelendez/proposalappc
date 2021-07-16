<?php
/**
 * Created by PhpStorm.
 * User: Alfredo
 * Date: 22/9/2017
 * Time: 11:24 AM
 */

namespace App;

use Carbon\Carbon;

class Persona extends Modelo {

    public $timestamps = true;

    protected $table = 'persona';

    /**
     * Los atributos que se pueden guardar
     *
     * @var array
     */
    protected $fillable = [
	    'id_usuario',
	    'primer_nombre',
	    'segundo_nombre',
	    'primer_apellido',
	    'segundo_apellido',
	    'dni',
	    'nacionalidad',
	    'sexo',
	    'direccion_domicilio',
	    'fecha_nacimiento',
	    'foto',
        'status'
    ];


    const SEXO_FEMENINO = 0;
    const SEXO_MASCULINO = 1;


    /**
     * Devuélve las reglas de validación para un campo específico o el arreglo de reglas por defecto.
     *
     * @param string $campo     Nombre del campo del que se quiere las reglas de validación.
     * @param int $ignorar_id    ID del elemento que se está editando, si es el caso.
     * @return array|string
     */
    public static function reglasValidacion($campo = null, $ignorar_id = 0) {
        $reglas = [
            'id'                    => 'integer',
            'id_usuario'            => 'integer',
            'primer_nombre'         => 'required|max:60',
            'segundo_nombre'        => 'max:60',
            'primer_apellido'       => 'required|max:60',
            'segundo_apellido'      => 'max:60',
            //'dni'                   => 'required|max:20|unique:persona,dni,' . $ignorar_id . ',id,fecha_eliminacion,NULL',
            'nacionalidad'          => 'max:31',
            'sexo'                  => 'required|in:0,1',
            'direccion_domicilio'   => 'max:255',
            'fecha_nacimiento'      => 'nullable|date_format:d/m/Y',
            'foto'                  => '',
            'foto_upload'           => 'image',
            'status'                => 'integer'
        ];
        if ($campo === null) {
            return $reglas;
        }
        return isset($reglas[$campo]) ? $reglas[$campo] : '';
    }

    /**
     * Devuélve los mensajes de validación para un campo específico o el arreglo de mensajes por defecto
     * Los mensajes se buscan en el archivo de correspondiente en lang
     *
     * @param $campo
     * @return array|string
     */
    public static function mensajesValidacion($campo = null) {
        $mensajes = [
            'dni' => [
                'unique' => 'dni_not_unique'
            ]
        ];
        if ($campo === null) {
            return $mensajes;
        }
        return isset($mensajes[$campo]) ? $mensajes[$campo] : '';
    }


    # RELACIONES

    public function estudiante() {
        return $this->hasOne('App\Estudiante', 'id_persona', 'id');
    }

    public function profesor() {
        return $this->hasOne('App\Profesor', 'id_persona', 'id');
    }

    public function contactos() {
        return $this->hasMany('App\Contacto', 'id_persona', 'id');
    }

    public function usuario() {
        return $this->belongsToMany('App\User', 'usuario_persona', 'id_persona', 'id_usuario');
    }


    # FILTROS

    public function scopeConDni($query, $val) {
        return $query->where('dni', '=', $val);
    }


    # ASIGNACIONES

    public function setFechaNacimientoAttribute($val) {
        if (!empty($val)) {
            $this->attributes['fecha_nacimiento'] = Funciones::formatoFechaSistema($val);
        }
    }


    # LECTURAS

    public function getDniAttribute() {
        return strtoupper($this->attributes['dni']);
    }

    public function getPrimerNombreAttribute() {
        return mb_convert_case(mb_strtolower($this->attributes['primer_nombre']), MB_CASE_TITLE, 'UTF-8');
    }

    public function getSegundoNombreAttribute() {
        return mb_convert_case(mb_strtolower($this->attributes['segundo_nombre']), MB_CASE_TITLE, 'UTF-8');
    }

    public function getPrimerApellidoAttribute() {
        return mb_convert_case(mb_strtolower($this->attributes['primer_apellido']), MB_CASE_TITLE, 'UTF-8');
    }

    public function getSegundoApellidoAttribute() {
        return mb_convert_case(mb_strtolower($this->attributes['segundo_apellido']), MB_CASE_TITLE, 'UTF-8');
    }

    /*public function getFechaNacimientoAttribute() {
        if (!empty($this->attributes['fecha_nacimiento'])) {
            return Carbon::createFromFormat('Y-m-d', $this->attributes['fecha_nacimiento'])->format('d/m/Y');
        }
        return null;
    }*/


    # METODOS


    /**
     * Retorna el id del usuario asociado a la persona, o falso si no tiene un usuario.
     *
     * @return bool
     */
    public function idUsuario() {
        $usuario = $this->usuario()->first(['id']);
        if ($usuario) {
            return $usuario->id;
        }

        return false;
    }


    /**
     * Retorna un texto con el primer y segundo nombre de la persona
     *
     * @param bool $incluir_segundo_nombre
     * @param bool $inicial_segundo_nombre
     * @return string
     */
    public function nombre($incluir_segundo_nombre = true, $inicial_segundo_nombre = false) {
        $nombres = [$this->primer_nombre];

        if ($incluir_segundo_nombre && !empty($this->segundo_nombre)) {
            $nombres[] = $inicial_segundo_nombre ? substr($this->segundo_nombre, 0, 1) : $this->segundo_nombre;
        }

        return implode(' ', $nombres);
    }


    /**
     * Retorna un texto con el primer y segundo apellido de la persona
     *
     * @param bool $incluir_segundo_apellido
     * @param bool $inicial_segundo_apellido
     * @return string
     */
    public function apellido($incluir_segundo_apellido = true, $inicial_segundo_apellido = false) {
        $apellidos = [$this->primer_apellido];

        if ($incluir_segundo_apellido && !empty($this->segundo_apellido)) {
            $apellidos[] = $inicial_segundo_apellido ? substr($this->segundo_apellido, 0, 1) : $this->segundo_apellido;
        }

        return implode(' ', $apellidos);
    }


    /**
     * Retorna un texto con los nombres de la persona
     *
     * @param bool $incluir_segundo_nombre
     * @param bool $incluir_segundo_apellido
     * @param bool $iniciales_segundos
     * @param bool $inicial_primer_apellido
     * @return string
     */
    public function nombres($incluir_segundo_nombre = false, $incluir_segundo_apellido = false, $iniciales_segundos = false, $inicial_primer_apellido = false) {
        $nombres = [$this->primer_nombre];

        if ($incluir_segundo_nombre && !empty($this->segundo_nombre)) {
            $nombres[] = $iniciales_segundos ? substr($this->segundo_nombre, 0, 1) : $this->segundo_nombre;
        }
        if (!empty($this->primer_apellido)) {
            $nombres[] = $inicial_primer_apellido ? substr($this->primer_apellido, 0, 1) : $this->primer_apellido;
        }
        if ($incluir_segundo_apellido && !empty($this->segundo_apellido)) {
            $nombres[] = $iniciales_segundos ? substr($this->segundo_apellido, 0, 1) : $this->segundo_apellido;
        }

        return implode(' ', $nombres);
    }


    public function guardarContactos($valores, $tipo) {
        $this->contactos()->deTipo($tipo)->delete();

        $items = [];
        foreach ($valores as $valor) {
            if (!empty($valor)) {
                $items[] = [
                    'id_persona' => $this->id,
                    'tipo' => $tipo,
                    'valor' => $valor
                ];
            }
        }

        $this->contactos()->insert($items);
    }


    public function guardarTelefonos($valores) {
        $this->guardarContactos($valores, Contacto::TIPO_TELEFONO);
    }


    public function guardarCorreos($valores) {
        $this->guardarContactos($valores, Contacto::TIPO_CORREO);
    }


    public function telefonos() {
        return $this->contactos()->deTipo(Contacto::TIPO_TELEFONO)->get(['valor'])->pluck('valor')->toArray();
    }


    public function correos() {
        return $this->contactos()->deTipo(Contacto::TIPO_CORREO)->get(['valor'])->pluck('valor')->toArray();
    }


    public function empleado() {
        return Empleado::where('id_persona', '=', $this->id)->first();
    }

}