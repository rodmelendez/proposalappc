<?php
/**
 * Created by PhpStorm.
 * User: Alfredo Fleming
 * Date: 11/5/2019
 * Time: 8:39 AM
 */

namespace App;

use Illuminate\Support\Facades\Auth;

class GaleriaCredito extends Modelo {

    public $timestamps = true;

    protected $table = 'galeria_credito';

    /**
     * Los atributos que se pueden guardar
     *
     * @var array
     */
    protected $fillable = [
        'id_usuario',
        'id_galeria_credito_cliente',
        'nombre',
        'fecha',
        'monto',
        'id_moneda',
        'moneda_iso',
        'moneda_simbolo',
        'observaciones',
        'status',
    ];


    //definiciones de status
    const STATUS_PENDIENTE = 1;
    const STATUS_APROBADO = 2;
    const STATUS_RECHAZADO = 3;


    /**
     * Devuélve las reglas de validación para un campo específico o el arreglo de reglas por defecto
     *
     * @param string $campo     Nombre del campo del que se quiere las reglas de validación.
     * @param int $ignorar_id    ID del elemento que se está editando, si es el caso.
     * @return array|string
     */
    public static function reglasValidacion($campo = null, $ignorar_id = 0) {
        $reglas = [
            'id_usuario'                    => 'integer',
            'id_galeria_credito_cliente'    => 'integer',
            'nombre'                        => 'required|max:63',
            'fecha'                         => 'date_format:d/m/Y',
            'monto'                         => 'numeric',
            'observaciones'                 => 'max:700',
            'status'                        => 'integer',
        ];
        if ($campo === null) {
            return $reglas;
        }
        return isset($reglas[$campo]) ? $reglas[$campo] : '';
    }


    # RELACIONES

    public function cliente() {
        return $this->belongsTo('App\GaleriaCreditoCliente', 'id_galeria_credito_cliente', 'id');
    }

    public function fotos() {
        return $this->hasMany('App\GaleriaCreditoItem', 'id_galeria_credito', 'id');
    }


    # FILTROS


    # ASIGNACIONES

    public function setFechaAttribute($val) {
        if (!empty($val)) {
            $this->attributes['fecha'] = Funciones::formatoFechaSistema($val);
        }
    }


    # LECTURAS

    public function getNombreAttribute() {
        return ucfirst($this->attributes['nombre']);
    }


    # METODOS

    public static function traerData($id_usuario = null) {
        $campos = [
            'galeria_credito.id',
            'galeria_credito.id_galeria_credito_cliente',
            'galeria_credito.nombre',
            'galeria_credito.fecha',
            'galeria_credito.monto',
            'galeria_credito.id_moneda',
            'galeria_credito.moneda_iso',
            'galeria_credito.moneda_simbolo',
            'galeria_credito.fecha_creacion',
            'galeria_credito_cliente.nombre AS nombre_cliente',
            'galeria_credito_cliente.dni AS dni_cliente',
            'galeria_credito_cliente.negocio AS negocio_cliente',
            'galeria_credito_cliente.ruc AS ruc_cliente',
            'galeria_credito_cliente.direccion AS direccion_cliente',
            'galeria_credito_cliente.telefono AS telefono_cliente',
            'galeria_credito.observaciones AS observaciones',
            'usuario.nombre AS nombre_usuario',
            'galeria_credito.status AS status',
        ];

        if ($id_usuario === null) {
            $usuario = Auth::user();
        } else {
            $usuario = User::find((int)$id_usuario);
        }

        $items = self
            ::join('galeria_credito_cliente', 'galeria_credito.id_galeria_credito_cliente', '=', 'galeria_credito_cliente.id', 'left')
            ->join('usuario', 'galeria_credito.id_usuario', '=', 'usuario.id', 'left')
            ->orderBy('fecha_creacion');

        self::verificarPermiso($items, 'documentos-creditos', 'galeria_credito', $usuario);

        return $items->selectRaw(implode(',', $campos))
            ->get()
            ->toArray();
    }


    public static function guardarCliente($data_cliente) {
        if (!$data_cliente['id']) {
            return GaleriaCreditoCliente::create($data_cliente);
        }

        if (!($cliente = GaleriaCreditoCliente::find((int)$data_cliente['id']))) {
            return false;
        }

        foreach ($data_cliente as $prop => $valor) {
            $cliente->$prop = $valor;
        }

        $cliente->save();

        return $cliente;
    }


    public function guardarFotoItem($data_foto_item) {
        if (!$data_foto_item['id']) {
            $data_foto_item['id_galeria_credito'] = $this->id;
            return GaleriaCreditoItem::create($data_foto_item);
        }

        if (!($item = GaleriaCreditoItem::find((int)$data_foto_item['id']))) {
            return false;
        }

        foreach ($data_foto_item as $prop => $valor) {
            $item->$prop = $valor;
        }

        $item->save();

        return $item;
    }


    public function listaFotos($ordenar_por = null) {
        $items = $this->fotos();

        switch ($ordenar_por) {
            case 'categorias':
                $items = $items
                    ->orderBy('tipo');
                break;

            case 'nombre':
                $items = $items
                    ->orderBy('nombre');
                break;

            default:
                $items = $items
                    ->orderBy('indice');
        }

        return $items
            ->where('visible', '=', 1)
            ->orderBy('fecha_creacion')
            ->get();
    }


    public function cambiarStatus($status) {
        if (!in_array($status, [self::STATUS_PENDIENTE, self::STATUS_APROBADO, self::STATUS_RECHAZADO]) || $status == $this->status) {
            return;
        }

        $this->status = $status;
        $this->save();

        //TODO: crear log
    }

}