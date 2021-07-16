<?php
/**
 * Created by PhpStorm.
 * User: Alfredo Fleming
 * Date: 30/4/2019
 * Time: 3:47 PM
 */

namespace App\Http\Controllers;


class InventarioController extends Controlador {

    protected $modelo = 'Inventario';


    public function despuesDeGuardar($item) {
        ImagenController::subirImagenParaItem($item, 'foto');
    }
}