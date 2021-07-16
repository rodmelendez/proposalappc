<?php
/**
 * Created by PhpStorm.
 * User: Alfredo Fleming
 * Date: 3/5/2019
 * Time: 8:59 AM
 */

namespace App\Http\Controllers;


use App\Empresa;

class SucursalController extends Controlador {

    protected $modelo = 'Sucursal';


    public function cargarEmpresasGet() {
        $empresas = Empresa::orderBy('nombre')
            ->get(['id', 'nombre', 'logo'])
            ->toArray();

        $this->especificarRespuesta('empresas', $empresas);
        return $this->retornar();
    }

}