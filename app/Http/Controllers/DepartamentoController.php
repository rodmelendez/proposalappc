<?php
/**
 * Created by PhpStorm.
 * User: Alfredo Fleming
 * Date: 6/5/2019
 * Time: 4:14 PM
 */

namespace App\Http\Controllers;


use App\Empresa;
use App\Sucursal;

class DepartamentoController extends Controlador {

    protected $modelo = 'Departamento';


    public function cargarSucursalesGet() {
        $empresas = Empresa::orderBy('nombre')
            ->get(['id', 'nombre'])
            ->toArray();

        $sucursales = Sucursal::orderBy('nombre')
            ->get(['id', 'nombre', 'id_empresa'])
            ->toArray();

        $this->especificarRespuesta('empresas', $empresas);
        $this->especificarRespuesta('sucursales', $sucursales);
        return $this->retornar();
    }


    public function itemDataAdicional($item) {
        $id_empresa = null;

        if (!empty($item->id_sucursal)) {
            if ($sucursal = Sucursal::find((int)$item->id_sucursal)) {
                $id_empresa = $sucursal->id_empresa;
            }
        }

        $this->especificarRespuesta('id_empresa', $id_empresa);
    }


}