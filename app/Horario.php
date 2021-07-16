<?php
/**
 * Created by PhpStorm.
 * User: Alfredo Fleming
 * Date: 17/7/2019
 * Time: 1:28 PM
 */

namespace App;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class Horario extends Modelo {

    public $timestamps = true;

    protected $table = 'horario';

    /**
     * Los atributos que se pueden guardar
     *
     * @var array
     */
    protected $fillable = [
        'id_usuario',
        'id_persona',
        'id_empleado',
        'inicio',
        'fin',
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
            'id_usuario'    => 'integer',
            'id_persona'    => 'required|integer',
            'id_empleado'   => 'integer',
            'inicio'        => '',
            'fin'           => '',
        ];
        if ($campo === null) {
            return $reglas;
        }
        return isset($reglas[$campo]) ? $reglas[$campo] : '';
    }


    # RELACIONES

    public function eventos() {
        return $this->hasMany('App\HorarioEvento', 'id_horario', 'id');
    }


    # FILTROS


    # ASIGNACIONES


    # LECTURAS

    public function getNombreAttribute() {
        return ucwords($this->attributes['nombre']);
    }


    # METODOS

    public static function traerData($campos = null) {
        $campos = [
            'id',
            'nombre',
            'abreviatura',
            'descripcion',
            'fecha_creacion'
        ];
        return self::orderBy('fecha_creacion')
            ->get($campos)
            ->toArray();
    }


    public function actualizarItem($inicio, $fin) {
        $this->inicio = $inicio;
        $this->fin = $fin;
        $this->save();
    }


    public function guardarEvento($tipo, $horas, $comentario, $archivo = null) {
        $inicio = Carbon::createFromFormat('Y-m-d H:m:s', $this->inicio);
        $fin = Carbon::createFromFormat('Y-m-d H:m:s', $this->fin);
        $dia_sem = $inicio->dayOfWeekIso;
        $duracion = $fin->diffInMinutes($inicio) / 60;
        $porcentaje = (float)$horas * 100 / $duracion;

        $evento = $this->eventos()->create([
            'dia' => $dia_sem,
            'tipo' => $tipo,
            'horas' => (float)$horas,
            'porcentaje' => $porcentaje,
            'comentario' => $comentario,
            'id_usuario' => Auth::id(),
        ]);

        if (!$evento) {
            return false;
        }

        if (!empty($archivo)) {
            $evento->archivos()->create([
                'archivo' => $archivo,
            ]);
        }

        return $evento;
    }


    public function listaEventos() {
        $items = $this
            ->eventos()
            ->with('archivos')
            ->get();

        $eventos = [];

        foreach ($items as $item) {
            $eventos[] = [
                'id' => $item->id,
                'comentario' => $item->comentario,
                'horas' => $item->horas,
                'tipo' => $item->tipo,
                'fecha_creacion' => $item->fecha_creacion,
                'archivos' => $item->archivos,
            ];
        }

        return $eventos;
    }

}