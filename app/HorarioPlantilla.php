<?php
/**
 * Created by PhpStorm.
 * User: Alfredo Fleming
 * Date: 13/7/2019
 * Time: 2:45 PM
 */

namespace App;

use Illuminate\Support\Facades\Auth;

class HorarioPlantilla extends Modelo {

    public $timestamps = true;

    protected $table = 'horario_plantilla';

    /**
     * Los atributos que se pueden guardar
     *
     * @var array
     */
    protected $fillable = [
        'id_usuario',
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
            'id_usuario'    => 'integer',
            'nombre'        => 'required|max:60'
        ];
        if ($campo === null) {
            return $reglas;
        }
        return isset($reglas[$campo]) ? $reglas[$campo] : '';
    }


    # RELACIONES

    public function dias() {
        return $this->hasMany('App\HorarioPlantillaDia', 'id_horario_plantilla', 'id');
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
            'fecha_creacion'
        ];

        $items = self::orderBy('nombre');

        self::verificarPermiso($items, 'horarios-plantillas');

        return $items
            ->selectRaw(implode(',', $campos))
            ->get()
            ->toArray();
    }


    public function guardarHorario($items) {
        $existentes = $this->dias;
        $ids_guardadas = [];

        $usuario = Auth::user();

        foreach ($items as $item) {
            $existente = false;

            foreach ($existentes as $existente) {
                if (empty($item->dia) || empty($item->hora_inicio) || empty($item->hora_fin)) {
                    continue;
                }

                if ($existente->dia == $item->dia
                    && $existente->hora_inicio == $item->hora_inicio
                    && $existente->hora_fin == $item->hora_fin) {

                    $ids_guardadas[] = $existente->id;
                    $existente = true;
                    break;
                }
            }

            if (!$existente) {
                $nuevo = HorarioPlantillaDia::create([
                    'id_usuario' => $usuario ? $usuario->id : null,
                    'id_horario_plantilla' => (int)$this->id,
                    'dia' => (int)$item->dia,
                    'hora_inicio' => $item->hora_inicio,
                    'hora_fin' => $item->hora_fin,
                ]);

                if ($nuevo) {
                    $ids_guardadas[] = $nuevo->id;
                }
            }
        }

        if (count($ids_guardadas)) {
            $this
                ->dias()
                ->whereNotIn('id', $ids_guardadas)
                ->delete();
        }
        else {
            $this
                ->dias()
                ->delete();
        }
    }

}