<?php
/**
 * Created by PhpStorm.
 * User: Alfredo Fleming
 * Date: 11/5/2019
 * Time: 8:45 AM
 */

namespace App\Http\Controllers;


use App\GaleriaCredito;
use App\GaleriaCreditoItem;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;

class GaleriaCreditoController extends Controlador {

    protected $modelo = 'GaleriaCredito';
    
    
    public function tipos() {
        return [
            1 => [
                'id' => 1,
                'nombre' => 'Garantía',
                'clase' => 'icono-garantia',
                'icono' => 'icono-garantia-c.png',
                'icono_bw' => 'icono-garantia.png',
            ],
            2 => [
                'id' => 2,
                'nombre' => 'Negocio',
                'clase' => 'icono-negocio',
                'icono' => 'icono-negocio-c.png',
                'icono_bw' => 'icono-negocio.png',
            ],
            3 => [
                'id' => 3,
                'nombre' => 'Ubicación',
                'clase' => 'icono-ubicacion',
                'icono' => 'icono-ubicacion-c.png',
                'icono_bw' => 'icono-ubicacion.png',
            ],
            4 => [
                'id' => 4,
                'nombre' => 'Inventario',
                'clase' => 'icono-inventario',
                'icono' => 'icono-inventario-c.png',
                'icono_bw' => 'icono-inventario.png',
            ],
            5 => [
                'id' => 5,
                'nombre' => 'Documento',
                'clase' => 'icono-documento',
                'icono' => 'icono-documento-c.png',
                'icono_bw' => 'icono-documento.png',
            ],
        ];
    }


    public function antesDeGuardar() {
        if (!Auth::user()) {
            $this->retornarError('La sesión ha finalizado.');
            return false;
        }

        $this->antesDeGuardarDefecto();

        if (!($nombre_cliente = Input::get('nombre_cliente'))) {
            $this->retornarError('Por favor ingrese el nombre del cliente');
            return false;
        }

        //se guarda el cliente
        {
            $telefonos = Input::get('telefono_cliente');
            if (is_array($telefonos)) $telefonos = implode(',', $telefonos);

            $data_cliente = [
                'id'        => Input::get('id_cliente', 0),
                'nombre'    => Input::get('nombre_cliente'),
                'dni'       => Input::get('dni_cliente'),
                'tipo'      => Input::get('tipo_cliente'),
                'negocio'   => Input::get('negocio_cliente'),
                'ruc'       => Input::get('ruc_cliente'),
                'direccion' => Input::get('direccion_cliente'),
                'telefono'  => $telefonos,
            ];

            $cliente = GaleriaCredito::guardarCliente($data_cliente);

            if (!$cliente) {
                $this->retornarError('Error al intentar registrar el cliente.');
                return false;
            }

            Input::merge([
                'id_galeria_credito_cliente' => $cliente->id
            ]);
        }

        //datos de la moneda
        {
            $id_moneda = (int)Input::get('monto_id_moneda');
            $moneda_iso = Input::get('monto_moneda_iso');
            $moneda_simbolo = Input::get('monto_moneda_simbolo');

            Input::merge([
                'id_moneda' => $id_moneda ?: null,
                'moneda_iso' => $moneda_iso ?: null,
                'moneda_simbolo' => $moneda_simbolo ?: null,
            ]);
        }

        return true;
    }


    public function despuesDeGuardar($item) {
        //se guardan las fotos
        $ids = Input::get('galeria_item_id');
        $nombres = Input::get('galeria_item_titulo');
        $indices = Input::get('galeria_item_indice');
        $fotos = Input::get('galeria_item_foto');
        $tipos = Input::get('galeria_item_tipo');
        $camaras = Input::get('galeria_item_camara');
        $visibles = Input::get('galeria_item_visible');

        if (is_array($ids) && count($ids)) {
            foreach ($ids as $key => $id_galeria_credito_item) {
                $atributos = ImagenController::atributos($fotos[$key]);

                $data_foto_item = [
                    'id' => $id_galeria_credito_item,
                    'nombre' => $nombres[$key],
                    'indice' => $indices[$key],
                    'foto' => $fotos[$key],
                    'tipo' => $tipos[$key],
                    'visible' => $visibles[$key],
                    'ancho' => $atributos['ancho'],
                    'alto' => $atributos['alto'],
                    'kb' => $atributos['kbs'],
                    'camara' => $camaras[$key],
                ];

                $item->guardarFotoItem($data_foto_item);
            }
        }
    }


    /**
     * @param \App\GaleriaCredito $item
     */
    public function itemDataAdicional($item) {
        //cliente
        {
            $cliente = $item->cliente;
            $this->especificarRespuesta('cliente', $cliente);
        }

        //foto items
        {
            $fotos = $item->fotos()
                ->orderBy('indice')
                ->orderBy('fecha_creacion')
                ->get();

            $this->especificarRespuesta('fotos', $fotos);
        }
    }


    public function subirImagenPost() {
        $nombre_foto = ImagenController::subirImagenParaItem(null, 'foto');

        if (empty($nombre_foto)) {
            return $this->retornarError('Error. No se pudo guardar la imagen.');
        }

        $atributos = ImagenController::atributos($nombre_foto);

        $this->especificarRespuesta('nombre', $nombre_foto);
        $this->especificarRespuesta('atributos', $atributos);

        return $this->retornar();
    }


    public function rotarPost() {
        $nombre_foto = Input::get('nombre_foto');
        $resultado = null;

        if (!empty($nombre_foto)) {
            $resultado = ImagenController::rotar($nombre_foto);
        }

        if (!$resultado) {
            return $this->retornarError();
        }

        $this->especificarRespuesta('nombre', $nombre_foto);
        $this->especificarRespuesta('img_tipo', $resultado);
        return $this->retornar();
    }


    public function cambiarVisibilidadPost() {
        $id = (int)Input::get('id');

        if (!$id) {
            return $this->retornar();
        }

        $visible = (bool)Input::get('visible');

        if (!($item = GaleriaCreditoItem::find($id))) {
            return $this->retornarError(self::ERROR_NO_ENCONTRADO);
        }

        $item->visible = $visible;
        $item->save();

        return $this->retornar();
    }


    /**
     * @return array
     * @throws \Throwable
     */
    public function cargarImpresionGet() {
        $id = (int)Input::get('id');
        $layout = (int)Input::get('layout', 9);
        $orientacion = Input::get('orientacion');
        $ordenar_por = Input::get('ordenar_por', null);

        if (!($galeria_credito = GaleriaCredito::find($id))) {
            return $this->retornarError(self::ERROR_NO_ENCONTRADO);
        }

        $items = $galeria_credito->listaFotos($ordenar_por);

        //temp \/
        /*$items_foo = [];
        for ($i = 0; $i < 10; $i++) {
            foreach ($items as $item) {
                $items_foo[] = $item;
            }
        }
        $items = $items_foo;*/
        //temp /\

        $cliente = $galeria_credito->cliente;

        $empresa = EmpresaController::activa();

        $tipos = self::tipos();

        $usuario = User::find((int)$galeria_credito->id_usuario);
        $usuario->persona = $usuario ? $usuario->traerPersona() : null;

        $data = [
            'logo' => $empresa ? $empresa->logo : '',
            'num' => $galeria_credito->id,
            'nombre' => $galeria_credito->nombre,
            'monto' => number_format($galeria_credito->monto, 2),
            'nombre_cliente' => $cliente->nombre ?? '',
            'dni_cliente' => $cliente->dni ?? '',
            'direccion_cliente' => $cliente->direccion ?? '',
            'tipo_cliente' => $cliente->tipo ?? '',
            'negocio_cliente' => $cliente->negocio ?? '',
            'ruc_cliente' => $cliente->ruc ?? '',
            'telefono_cliente' => $cliente->telefono ?? '',
            'items' => $items,
            'tipos' => $tipos,
            'items_por_pagina' => $layout,
            'orientacion' => $orientacion,
            'fecha' => !empty($galeria_credito->fecha) ? Carbon::createFromFormat('Y-m-d', $galeria_credito->fecha)->format('d/m/Y') : '',
            'usuario' => $usuario,
            'moneda' => [
                'id' => $galeria_credito->id_moneda,
                'moneda_iso' => $galeria_credito->moneda_iso,
                'simbolo' => $galeria_credito->moneda_simbolo,
            ],
            'empresa_telefono' => $empresa->telefono ?? '',
            'empresa_website' => $empresa->website ?? '',
            'observaciones' => $galeria_credito->observaciones,
        ];

        if (Request::wantsJson()) {
            $html = view('layouts.pdf.galeria_credito')->with($data)->render();
            $this->especificarRespuesta('html', $html);
            $this->especificarRespuesta('id', $id);
            return $this->retornar();
        }

        return PdfController::generar('galeria_credito', $data);
    }


    public function cambiarStatusPost() {
        $id_galeria_credito = (int)Input::get('id_galeria_credito');
        $status = (int)Input::get('status');

        if (!($galeria_credito = GaleriaCredito::find($id_galeria_credito))) {
            return $this->retornarError(self::ERROR_NO_ENCONTRADO);
        }

        $galeria_credito->cambiarStatus($status);

        return $this->retornar();
    }

}