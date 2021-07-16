<?php
/**
 * Created by PhpStorm.
 * User: Alfredo Fleming
 * Date: 29/4/2019
 * Time: 12:17 PM
 */

namespace App;


use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class Empleado extends Modelo {

    public $timestamps = true;

    protected $table = 'empleado';

    const STATUS_INHABILITADO = 2;

    /**
     * Los atributos que se pueden guardar
     *
     * @var array
     */
    protected $fillable = [
        'id_usuario',
        'id_persona',
        'num_control',
        'fecha_ingreso',
        'fecha_ingreso_inss',
        'descripcion',
        'grado',
        'tipo_cargo',
        'salario_actual',
        'id_empresa',
        'id_sucursal',
        'id_departamento',
        'id_sub_departamento',
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
            'id_persona'            => 'required|integer',
            'num_control'           => 'max:63',
            'fecha_ingreso'         => 'nullable|date_format:d/m/Y',
            'fecha_ingreso_inss'    => 'nullable|date_format:d/m/Y',
            'descripcion'           => 'max:255',
            'grado'                 => 'max:63',
            'tipo_cargo'            => 'max:63',
            'salario_actual'        => 'nullable|numeric',
            'id_empresa'            => 'integer',
            'id_sucursal'           => 'integer',
            'id_departamento'       => 'integer',
            'id_sub_departamento'   => 'integer',
        ];
        if ($campo === null) {
            return $reglas;
        }
        return isset($reglas[$campo]) ? $reglas[$campo] : '';
    }


    # RELACIONES

    public function empresas() {
        return $this->belongsToMany('App\Empresa', 'empleado_empresa', 'id_empleado', 'id_empresa');
    }

    public function persona() {
        return $this->hasOne('App\Persona', 'id', 'id_persona');
    }
    
    public function horario() {
        return $this->hasMany('App\Horario', 'id_empleado', 'id');
    }

    public function departamento() {
        return $this->belongsTo('App\Departamento', 'id_departamento', 'id');
    }


    # FILTROS


    # ASIGNACIONES


    # LECTURAS


    # METODOS

    public static function traerData($campos = null) {
        $campos = [
            'empleado.id',
            'empleado.id_persona',
            'empleado.num_control',
            'empleado.fecha_ingreso',
            'empleado.fecha_ingreso_inss',
            'empleado.descripcion',
            'empleado.grado',
            'empleado.tipo_cargo',
            'empleado.salario_actual',
            'empleado.id_empresa',
            'empleado.id_sucursal',
            'empleado.id_departamento',
            'empleado.id_sub_departamento',
            'persona.primer_nombre',
            'persona.segundo_nombre',
            'persona.primer_apellido',
            'persona.segundo_apellido',
            'persona.dni',
            'persona.foto',
        ];

        return self
            ::join('persona', 'empleado.id_persona', '=', 'persona.id')
            ->orderBy('persona.primer_nombre')
            ->orderBy('empleado.fecha_creacion')
            ->get($campos)
            ->toArray();
    }


    public function empresa() {
        $this->empresas()->first();
    }
    
    
    public function eventos($fecha_inicio = null, $fecha_fin = null) {
        $items = $this->horario();
        
        if (!empty($fecha_inicio)) {
            $items = $items->where('inicio', '>=', $fecha_inicio . ' 00:00:00');
        }
        
        if (!empty($fecha_fin)) {
            $items = $items->where('fin', '<=', $fecha_fin . ' 23:59:59');
        }

        return $items->get();
    }


    public function asignarHorario($fecha, $hora_inicio, $hora_fin) {
        $inicio = Funciones::formatoFechaSistema($fecha) . ' ' . Funciones::formatoHoraSistema($hora_inicio);
        $fin = Funciones::formatoFechaSistema($fecha) . ' ' . Funciones::formatoHoraSistema($hora_fin);

        $this->horario()->create([
            'id_persona' => (int)$this->id_persona,
            'id_empleado' => (int)$this->id,
            'inicio' => $inicio,
            'fin' => $fin,
            'id_usuario' => Auth::id(),
        ]);
    }


    public function asignarHorarioDesdePlantilla($id_horario_plantilla, $fecha_inicio, $fecha_fin) {
        if (!($horario_plantilla = HorarioPlantilla::find($id_horario_plantilla))) {
            return false;
        }

        $dias = $horario_plantilla->dias;

        $usuario = Auth::user();
        $id_usuario = $usuario ? $usuario->id : null;

        if (!$dias) {
            return true;
        }

        $inicio = Carbon::createFromFormat('Y-m-d', Funciones::formatoFechaSistema($fecha_inicio));
        $fin = Carbon::createFromFormat('Y-m-d', Funciones::formatoFechaSistema($fecha_fin));

        $fecha = $inicio->copy();

        $dia_fecha = $fecha->dayOfWeekIso;

        if ($dia_fecha > Carbon::MONDAY) {
            $fecha->subDays($dia_fecha - 1);
        }

        while ($fecha->lte($fin)) {
            foreach ($dias as $dia) {
                $fecha_semana = $fecha->copy();

                if ($dia->dia > Carbon::MONDAY) {
                    $fecha_semana->addDays($dia->dia - 1);
                }

                if ($fecha_semana->gte($inicio) && $fecha_semana->lte($fin)) {
                    $this->horario()->create([
                        'id_persona' => (int)$this->id_persona,
                        'id_empleado' => (int)$this->id,
                        'inicio' => $fecha_semana->format('Y-m-d') . ' ' . $dia->hora_inicio,
                        'fin' => $fecha_semana->format('Y-m-d') . ' ' . $dia->hora_fin,
                        'id_usuario' => $id_usuario,
                    ]);
                }
            }

            $fecha->addWeek();
        }

        return true;
    }


    public function quitarHorario($fecha_inicio = null, $fecha_fin = null) {
        $items = $this->horario();

        if (!empty($fecha_inicio)) {
            $items = $items->where('inicio', '>=', Funciones::formatoFechaSistema($fecha_inicio) . ' 00:00:00');
        }

        if (!empty($fecha_fin)) {
            $items = $items->where('fin', '<=', Funciones::formatoFechaSistema($fecha_fin) . ' 23:59:59');
        }

        $items->delete();
    }


    public static function deUsuario($usuario = null) {
        if ($usuario === null) $usuario = Auth::user();

        if (!$usuario) {
            return null;
        }

        if (!($persona = $usuario->traerPersona())) {
            return null;
        }

        $empleado = Empleado::where('id_persona', '=', (int)$persona->id)->first();

        return $empleado ? $empleado : null;
    }

}