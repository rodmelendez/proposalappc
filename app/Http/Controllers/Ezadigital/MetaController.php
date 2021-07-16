<?php
/**
 * Created by PhpStorm.
 * User: Alfredo Fleming
 * Date: 04/09/2020
 * Time: 3:23 PM
 */

namespace App\Http\Controllers\Ezadigital;


use App\Empleado;
use App\Ezadigital\SimicroCredito;
use App\Funciones;
use App\Http\Controllers\ImagenController;
use App\Http\Controllers\ImportarController;
use App\Persona;
use App\User;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class MetaController extends \App\Http\Controllers\Controlador {

    protected $modelo = 'Meta';

    public $subdirectorio = 'Ezadigital';




}