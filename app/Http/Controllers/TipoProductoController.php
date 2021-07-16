<?php
/**
 * Created by PhpStorm.
 * User: Alfredo Fleming
 * Date: 4/5/2019
 * Time: 5:26 PM
 */

namespace App\Http\Controllers;


use App\Atributo;
use Illuminate\Support\Facades\Input;

class TipoProductoController extends Controlador {

    protected $modelo = 'TipoProducto';


    /**
     * @param \App\TipoProducto $item
     */
    public function despuesDeGuardar($item) {
        $atributos = Input::get('atributos');

        if (!is_array($atributos)) $atributos = [];

        $item->atributos()->sync($atributos);
    }


    /**
     * @param \App\TipoProducto $item
     * @return array
     */
    public function itemDataAdicional($item) {
        $atributos = $item->atributos()
            ->get()
            ->pluck('id')
            ->toArray();

        $this->especificarRespuesta('atributos', $atributos);
        return $this->retornar();
    }


    public function cargarAtributosGet() {
        $atributos = Atributo::orderBy('nombre')
            ->get(['id', 'nombre', 'tipo'])
            ->toArray();

        $this->especificarRespuesta('atributos', $atributos);
        return $this->retornar();
    }

}