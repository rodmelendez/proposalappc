<?php
/**
 * Created by PhpStorm.
 * User: Alfredo Fleming
 * Date: 8/6/2019
 * Time: 11:54 AM
 */

namespace App\Http\Controllers;


use App\Comunicado;
use App\ComunicadoDestinatario;
use App\Empleado;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;

class ComunicadoController extends Controlador {

    protected $modelo = 'Comunicado';


    public function antesDeGuardar() {
        $this->antesDeGuardarDefecto();

        $contenido = Input::get('contenido');

        if (empty($contenido)) {
            $this->retornarError('Por favor especifique el contenido del comunicado.');
            return false;
        }

        //fecha activación
        {
            $fecha_activacion = Input::get('fecha_activacion');
            if (empty($fecha_activacion)) {
                Input::merge([
                    'fecha_activacion' => date('Y-m-d H:i:s')
                ]);
            }
        }

        Input::merge([
            'data' => Input::get('contenido_json'),
        ]);

        return true;
    }


    /**
     * @param \App\Comunicado $item
     */
    public function despuesDeGuardar($item) {
        ImagenController::subirImagenParaItem($item, 'imagen');

        //destinatarios
        {
            $destinatarios = [];

            $id_empresa = Input::get('id_empresa');
            $empresas = [];

            if (!empty($id_empresa)) {
                if (is_array($id_empresa)) {
                    $empresas = array_map('intval', $id_empresa);
                }
                else {
                    $empresas = [(int)$id_empresa];
                }
            }
            elseif ($usuario = Auth::user()) {
                //se buscan las empresas asociadas al empleado
                if ($persona = $usuario = $usuario->traerPersona()) {
                    if ($empleado = Empleado::where('id_persona', '=', $persona->id)->first()) {
                        $empresas = $empleado->empresas;
                    }
                }
            }

            if (count($empresas)) {
                foreach ($empresas as $empresa) {
                    $destinatarios[] = [
                        'tipo' => ComunicadoDestinatario::TIPO_EMPRESA,
                        'valor' => is_int($empresa) ? $empresa : $empresa->id,
                    ];
                }
            }

            $item->actualizarDestinatarios($destinatarios);
        }
    }


    /**
     * @param \App\Comunicado $item
     */
    public function itemDataAdicional($item) {
        $empresas = $item
            ->destinatarios()
            ->tipoEmpresa()
            ->get()
            ->pluck('valor')
            ->toArray();

        $this->especificarRespuesta('empresas', $empresas);
    }


    public function cargarGet() {
        $items = Comunicado::paraUsuario();

        $listado = [];

        foreach ($items as $item) {
            $persona = $item->dataPersona();

            $listado[] = [
                'id' => $item->id,
                'fecha' => $item->fecha_activacion ?: $item->fecha_creacion,
                'titulo' => $item->titulo,
                'contenido' => $item->contenido,//nl2br($item->contenido),
                'imagen' => ImagenController::urlImagen($item->imagen, 'l', false),
                'usuario' => [
                    'nombre' => $persona['nombre'] ?: $persona['usuario'],
                    'foto' => PersonaController::urlFoto($persona['foto']),
                ]
            ];
        }

        return $listado;
    }

}