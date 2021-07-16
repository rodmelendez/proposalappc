<?php
/**
 * Created by PhpStorm.
 * User: Alfredo Fleming
 * Date: 6/5/2019
 * Time: 5:11 PM
 */

namespace App\Http\Controllers;


use App\Departamento;
use App\Empresa;
use App\Subdepartamento;
use App\Sucursal;

class UbicacionController extends Controlador {

    protected $modelo = 'Ubicacion';


    public function cargarSubdepartamentosGet() {
        $sub_departamentos = Subdepartamento::orderBy('nombre')
            ->get(['id', 'nombre'])
            ->toArray();

        $this->especificarRespuesta('sub_departamentos', $sub_departamentos);
        return $this->retornar();
    }


    public function cargarSucursalesGet() {
        $empresas = Empresa::orderBy('nombre')
            ->get(['id', 'nombre'])
            ->toArray();

        $sucursales = Sucursal::orderBy('nombre')
            ->get(['id', 'nombre', 'id_empresa'])
            ->toArray();

        $departamentos = Departamento::orderBy('nombre')
            ->get(['id', 'nombre', 'id_sucursal'])
            ->toArray();

        $sub_departamentos = Subdepartamento::orderBy('nombre')
            ->get(['id', 'nombre', 'id_departamento'])
            ->toArray();

        $this->especificarRespuesta('empresas', $empresas);
        $this->especificarRespuesta('sucursales', $sucursales);
        $this->especificarRespuesta('departamentos', $departamentos);
        $this->especificarRespuesta('sub_departamentos', $sub_departamentos);
        return $this->retornar();
    }

}