<?php
/**
 * Created by PhpStorm.
 * User: Alfredo Fleming
 * Date: 6/5/2019
 * Time: 4:38 PM
 */

namespace App\Http\Controllers;


use App\Departamento;
use App\Empresa;
use App\Sucursal;

class SubdepartamentoController extends Controlador {

    protected $modelo = 'Subdepartamento';


    public function cargarDepartamentosGet() {
        $empresas = Empresa::orderBy('nombre')
            ->get(['id', 'nombre'])
            ->toArray();

        $sucursales = Sucursal::orderBy('nombre')
            ->get(['id', 'nombre', 'id_empresa'])
            ->toArray();

        $departamentos = Departamento::orderBy('nombre')
            ->get(['id', 'nombre', 'id_sucursal'])
            ->toArray();

        $this->especificarRespuesta('empresas', $empresas);
        $this->especificarRespuesta('sucursales', $sucursales);
        $this->especificarRespuesta('departamentos', $departamentos);
        return $this->retornar();
    }

}