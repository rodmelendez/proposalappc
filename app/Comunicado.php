<?php
/**
 * Created by PhpStorm.
 * User: Alfredo Fleming
 * Date: 8/6/2019
 * Time: 11:49 AM
 */

namespace App;

use Illuminate\Support\Facades\Auth;

class Comunicado extends Modelo {

    public $timestamps = true;

    protected $table = 'comunicado';

    /**
     * Los atributos que se pueden guardar
     *
     * @var array
     */
    protected $fillable = [
        'id_usuario',
        'id_comunicado_padre',
        'titulo',
        'contenido',
        'data',
        'tipo',
        'fecha_activacion',
        'fecha_vencimiento',
        'delimitar_fechas',
        'publico',
        'permite_respuestas',
        'moderado',
    ];

    public $booleanos = [
        'delimitar_fechas',
        'publico',
        'permite_respuestas',
        'moderado',
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
            'id_comunicado_padre'   => 'integer',
            'titulo'                => 'max:255',
            'contenido'             => 'required',
            'imagen'                => 'max:31',
            'tipo'                  => 'integer',
            'fecha_activacion'      => 'nullable|date_format:d/m/Y',
            'fecha_vencimiento'     => 'nullable|date_format:d/m/Y',
            'delimitar_fechas'      => 'boolean',
            'publico'               => 'boolean',
            'permite_respuestas'    => 'boolean',
            'moderado'              => 'boolean',
        ];
        if ($campo === null) {
            return $reglas;
        }
        return isset($reglas[$campo]) ? $reglas[$campo] : '';
    }


    # RELACIONES

    public function usuario() {
        return $this->belongsTo('App\User', 'id_usuario', 'id');
    }

    public function destinatarios() {
        return $this->hasMany('App\ComunicadoDestinatario', 'id_comunicado', 'id');
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
            'id_usuario',
            'id_comunicado_padre',
            'titulo',
            'contenido',
            'data',
            'imagen',
            'fecha_activacion',
            'fecha_vencimiento',
            'delimitar_fechas',
            'publico',
            'permite_respuestas',
            'moderado',
            'fecha_creacion'
        ];

        $items = self::orderBy('fecha_creacion', 'DESC');

        self::verificarPermiso($items, 'comunicados');

        return $items
            ->selectRaw(implode(',', $campos))
            ->get()
            ->toArray();
    }


    public function dataPersona() {
        if (!($usuario = User::find($this->id_usuario))) {
            $persona = null;
        }
        else {
            $persona = $usuario->traerPersona();
        }

        return [
            'usuario' => !$usuario ? '' : $usuario->nombre,
            'nombre' => !$persona ? '' : $persona->nombres(),
            'dni' => !$persona ? '' : $persona->dni,
            'foto' => !$persona ? '' : $persona->foto,
        ];
    }


    public static function paraUsuario($usuario = null, $max = 5) {
        if ($usuario === null) {
            $usuario = Auth::user();
        }

        $id_empresas = [];

        if ($usuario) {
            if ($persona = $usuario->traerPersona()) {
                if ($empleado = Empleado::where('id_persona', '=', $persona->id)->first()) {
                    $id_empresas = $empleado
                        ->empresas()
                        ->get()
                        ->pluck('id')
                        ->toArray();
                }
            }
        }

        $fecha_actual = date('Y-m-d H:i:s');

        $items = Comunicado
            ::join('comunicado_destinatario', 'comunicado_destinatario.id_comunicado', '=', 'comunicado.id', 'left')
            ->where('fecha_activacion', '<=', $fecha_actual)
            ->where(function ($q) use ($fecha_actual) {
                $q->whereNull('fecha_vencimiento');
                $q->orWhere('fecha_vencimiento', '>', $fecha_actual);
            })
            ->orderBy('fecha_activacion', 'DESC')
            ->take(50)
            ->selectRaw('
                comunicado.*,
                comunicado_destinatario.tipo AS tipo_destinatario,
                comunicado_destinatario.valor AS valor_destinatario,
                comunicado_destinatario.valor_str AS valor_str_destinatario
            ')
            ->get();

        $id_comunicados = [];
        $comunicados = [];
        $n = 0;

        foreach ($items as $key => $item) {
            if (!in_array($item->id, $id_comunicados)) {

                //se filtran por empresa
                switch ($item->tipo_destinatario) {
                    case ComunicadoDestinatario::TIPO_EMPRESA:
                        if (!in_array($item->valor_destinatario, $id_empresas)) {
                            continue 2;
                        }
                        break;
                }

                $comunicados[] = $item;
                $id_comunicados[] = $item->id;
                $n++;
                if ($n == $max) break;
            }
        }

        return $comunicados;
    }


    public function actualizarDestinatarios($destinatarios) {
        $ids = [];

        foreach ($destinatarios as $destinatario) {
            $valor = isset($destinatario['valor']) ? ($destinatario['valor'] ?: null) : null;
            $valor_str = isset($destinatario['valor_str']) ? ($destinatario['valor_str'] ?: null) : null;

            $item = ComunicadoDestinatario
                ::where('id_comunicado', '=', (int)$this->id)
                ->where('tipo', '=', (int)$destinatario['tipo'])
                ->where('valor', '=', $valor)
                ->where('valor_str', '=', $valor_str)
                ->first();

            if ($item) {
                $item->valor = $valor;
                $item->valor_str = $valor_str;
                $item->save();

                $ids[] = $item->id;
            }
            else {
                $item = ComunicadoDestinatario::create([
                    'id_comunicado' => (int)$this->id,
                    'tipo' => (int)$destinatario['tipo'],
                    'valor' => $valor,
                    'valor_str' => $valor_str,
                ]);

                if ($item) $ids[] = $item->id;
            }
        }

        if (count($ids)) {
            ComunicadoDestinatario
                ::where('id_comunicado', '=', (int)$this->id)
                ->whereNotIn('id', $ids)
                ->forceDelete();
        }
        else {
            ComunicadoDestinatario
                ::where('id_comunicado', '=', (int)$this->id)
                ->where('id', '>', 0)
                ->forceDelete();
        }
    }

}