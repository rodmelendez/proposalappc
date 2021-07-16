<?php
/**
 * Created by PhpStorm.
 * User: Alfredo Fleming
 * Date: 15/5/2019
 * Time: 10:45 AM
 */

namespace App\Http\Controllers;


use App\Departamento;
use App\Empleado;
use App\Empresa;
use App\Funciones;
use App\Persona;
use App\Subdepartamento;
use App\Sucursal;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;

class EmpleadoController extends Controlador {

    protected $modelo = 'Empleado';


    /**
     * @return array
     * @throws \Throwable
     */
    public function cargarCarnetGet() {
        $id_usuario = (int)Input::get('id');
        $tipo_carnet = Input::get('tipo_carnet', '2');
        $id_empresa = (int)Input::get('id_empresa', 0);
        $fecha_vencimiento = Input::get('fecha_vencimiento');
        $direccion = Input::get('direccion');
        $telefono = Input::get('telefono');
        $website = Input::get('website');

        //se busca el usuario editado
        if (!($usuario = User::find($id_usuario))) {
            return $this->retornarError(self::ERROR_NO_ENCONTRADO);
        }

        //se busca la persona asociada al usuario
        if (!($persona = $usuario->traerPersona())) {
            return $this->retornarError(self::ERROR_NO_ENCONTRADO);
        }

        //se busca el empleado asociado a la persona
        if (!($empleado = Empleado::where('id_persona', '=', $persona->id)->first())) {
            return $this->retornarError(self::ERROR_NO_ENCONTRADO);
        }

        //se busca la empresa
        if (empty($id_empresa)) {
            $empresa = EmpresaController::activa($empleado->id_empresa);
        }
        else {
            $empresa = Empresa::find($id_empresa);
        }

        //fecha de vencimiento
        if (empty($fecha_vencimiento)) {
            $fecha_vencimiento = '31/12/' . date('Y');
        }

        //dirección
        if (emptY($direccion)) {
            $direccion = $empresa ? $empresa->ubicacion : '';
        }

        //teléfono
        if (empty($telefono)) {
            $telefono = $empresa ? $empresa->telefono : '';
        }

        //website
        if (empty($website)) {
            $website = $empresa ? $empresa->website : '';
        }

        $barra = new CodigoBarraController;

        $data = [
            'logo' => $empresa ? $empresa->logo : '',
            'url_logo' => $empresa ? asset('uploads/img/' . $empresa->logo) : '',
            'grado_abreviatura' => '',
            'cargo' => $empleado->tipo_cargo,
            'nombres' => $persona->nombre(),
            'apellidos' => $persona->apellido(),
            'nombre' => $persona->nombres(true, true, true),
            'dni' => $persona->dni,
            'num_carnet' => $empleado->num_control,
            'codigo_barra' => !empty($empleado->num_control) ? $barra->imagen($empleado->num_control, false) : '',
            'foto' => $persona->foto,
            'url_foto' => PersonaController::urlFoto($persona->foto),
            'direccion' => $direccion,
            'telefono' => $telefono,
            'website' => $website,
            'valido_hasta' => $fecha_vencimiento,
        ];


        if (Request::wantsJson()) {
            $empresas = $empleado->empresas()->get(['empresa.id', 'empresa.nombre'])->toArray();
            foreach ($empresas as $key => $empresa) {
                unset($empresas[$key]['pivot']);
            }

            $html = view('layouts.pdf.carnet_tipo_' . $tipo_carnet)->with($data)->render();

            $this->especificarRespuesta('id', $usuario->id);
            $this->especificarRespuesta('id_empleado', $empleado->id);
            $this->especificarRespuesta('id_persona', $persona->id);
            $this->especificarRespuesta('empresas', $empresas);
            $this->especificarRespuesta('html', $html);
            return $this->retornar();
        }

        return PdfController::generar('carnet_tipo_' . $tipo_carnet, $data);
    }


    public static function codigoQr($empleado, $persona = null, $formato = 'svg') {
        if ($persona === null) {
            $persona = $empleado->persona;
        }

        if (!$empleado || !$persona) {
            return '';
        }

        $empresa = Empresa::find((int)$empleado->id_empresa);

        $telefonos = $persona->telefonos();
        $correos = $persona->correos();

        $params = [
            'tipo' => 1,
            'nombre' => $persona->nombre(),
            'apellido' => $persona->apellido(),
            'telefono' => reset($telefonos),
            'correo' => reset($correos),
            'empresa' => $empresa ? $empresa->nombre : '',
            'cargo' => '',
            'direccion' => $persona->direccion_domicilio,
        ];

        return CodigoQrController::generar($params, $formato);
    }


    public function importarEmpleadosPost() {
        $precargar = (bool)Input::get('precargar');
        $vaciar = (bool)Input::get('vaciar');

        if (!Input::hasFile('archivo_upload')) {
            return $this->retornarError(__('importar.archivo_no_valido'));
        }

        $validator = Validator::make(Input::all(), ['archivo_upload' => 'mimes:csv,txt,xlsx,xls,application/octet-stream|max:2048']);
        $archivo = Input::file('archivo_upload');

        if ($archivo->isValid() && ($validator->passes() || in_array($archivo->getClientOriginalExtension(), ['xls','xlsx']))) {
            //columnas
            $cabecera = [
                'num'                   => 'Nº',
                'primer_nombre'         => 'Primer nombre',
                'segundo_nombre'        => 'Segundo nombre',
                'primer_apellido'       => 'Primer apellido',
                'segundo_apellido'      => 'Segundo apellido',
                'dni'                   => 'DNI',
                'nacionalidad'          => 'Nacionalidad',
                'fecha_nacimiento'      => 'Fecha de nacimiento',
                'sexo'                  => 'Sexo',
                'direccion'             => 'Dirección de domicilio',
                'telefono1'             => 'Teléfono personal',
                'telefono2'             => 'Teléfono de empresa',
                'correo1'               => 'Correo personal',
                'correo2'               => 'Correo de empresa',
                'empresa'               => 'Empresa',
                'sucursal'              => 'Sucursal',
                'departamento'          => 'Departamento',
                'subdepartamento'       => 'Sub-departamento',
                'num_control'           => 'Num. de control / Carné',
                'fecha_ingreso'         => 'Fecha de ingreso',
                'fecha_ingreso_inss'    => 'Fecha de ingreso INSS',
                'descripcion'           => 'Descripción',
                'grado'                 => 'Grado académico',
                'tipo_cargo'            => 'Tipo de cargo',
            ];

           

            //validaciones
            $importar->validaciones_columnas = [
                'primer_nombre' => 'required',
                'primer_apellido' => 'required',
                'dni' => 'required',
                'empresa' => 'required',
            ];

            //empresas
            $empresas = Empresa::get()->pluck('nombre', 'id')->toArray();

            //verificación
            $importar->proceso_verificar = function(&$item, $item_anterior, $items_guardar) use ($empresas) {
                return self::verificarEmpleadoImportar($item, $items_guardar, $empresas);
            };

            //al guardar
            $importar->proceso_guardar = function($item) {
                return self::guardarEmpleadoImportar($item);
            };

            //formato para las columnas al previsualizar
            $importar->formatos_columnas = [
                'fecha_nacimiento' => function($valor) {
                    return self::formatoFechaHtml($valor);
                },
                'fecha_ingreso' => function($valor) {
                    return self::formatoFechaHtml($valor);
                },
                'fecha_ingreso_inss' => function($valor) {
                    return self::formatoFechaHtml($valor);
                },
            ];
           

            $this->especificarRespuesta('precargar', $precargar);
            $this->especificarRespuesta('resultado', $resultado);
            $this->especificarRespuesta('total_leidos', $importar->total_leidos);
            $this->especificarRespuesta('total_cargados', $importar->total_cargados);

            return $this->retornar(!$precargar ? __('global.saved_msg') : null);
        }

        return $this->retornar();
    }


    private static function verificarEmpleadoImportar(&$item, $items_guardar, $empresas) {
        //empresa
        {
            /*if (!Empresa::where('nombre', '=', $item['empresa'])->count()) {
                return [
                    'ok' => false,
                    'error' => 'La empresa no existe.',
                ];
            }*/
            $id_empresa = null;
            foreach ($empresas as $id => $empresa) {
                if (strtolower($item['empresa']) == strtolower($empresa)) {
                    $id_empresa = $id;
                    break;
                }
            }
            if (!$id_empresa) {
                return [
                    'ok' => false,
                    'error' => 'La empresa no existe.',
                ];
            }
        }

        $item['id_empresa'] = $id_empresa;

        //dni
        {
            if (Persona::where('dni', '=', $item['dni'])->count()) {
                return [
                    'ok' => false,
                    'error' => 'DNI ya existente.'
                ];
            }
            foreach ($items_guardar as $item_guardar) {
                if ($item['dni'] == $item_guardar['dni']) {
                    return [
                        'ok' => false,
                        'error' => 'DNI duplicado',
                    ];
                }
            }
        }

        usleep(200);

        return true;
    }


    private function guardarEmpleadoImportar($item) {
        $id_usuario = Auth::id();

        $id_sucursal = null;
        $id_departamento = null;
        $id_subdepartamento = null;

        if (!empty($item['sucursal'])) {
            if (!($sucursal = Sucursal::where('nombre', '=', $item['sucursal'])->where('id_empresa', '=', $item['id_empresa'])->first())) {
                $sucursal = Sucursal::create([
                    'nombre' => $item['sucursal'],
                    'id_empresa' => $item['id_empresa'],
                    'id_usuario' => $id_usuario,
                ]);

                if ($sucursal) {
                    $id_sucursal = $sucursal->id;

                    if (!($departamento = Departamento::where('nombre', '=', $item['departamento'])->where('id_sucursal', '=', $id_sucursal)->first())) {
                        $departamento = Departamento::create([
                            'nombre' => $item['departamento'],
                            'id_sucursal' => $id_sucursal,
                            'id_usuario' => $id_usuario,
                        ]);

                        if ($departamento) {
                            $id_departamento = $departamento->id;

                            if (!($subdepartamento = Subdepartamento::where('nombre', '=', $item['subdepartamento'])->where('id_departamento', '=', $id_departamento)->first())) {
                                $subdepartamento = Subdepartamento::create([
                                    'nombre' => $item['subdepartamento'],
                                    'id_departamento' => $id_departamento,
                                    'id_usuario' => $id_usuario,
                                ]);

                                if ($subdepartamento) {
                                    $id_subdepartamento = $subdepartamento->id;
                                }
                            }
                        }
                    }
                }
            }
        }

        $data_persona = [
            'id_usuario' => $id_usuario,
            'primer_nombre' => $item['primer_nombre'],
            'segundo_nombre' => $item['segundo_nombre'],
            'primer_apellido' => $item['primer_apellido'],
            'segundo_apellido' => $item['segundo_apellido'],
            'dni' => $item['dni'],
            'nacionalidad' => $item['nacionalidad'],
            'sexo' => strtolower(substr($item['sexo'],0,1)) == 'm' ? 0 : 1,
            'direccion_domicilio' => $item['direccion'],
            'fecha_nacimiento' => self::formatoFecha($item['fecha_nacimiento']),
            'foto' => null,
        ];

        if (!($persona = Persona::create($data_persona))) {
            return false;
        }

        $data_empleado = [
            'id_usuario' => $id_usuario,
            'id_persona' => $persona->id,
            'num_control' => $item['num_control'],
            'fecha_ingreso' => self::formatoFecha($item['fecha_ingreso']),
            'fecha_ingreso_inss' => self::formatoFecha($item['fecha_ingreso_inss']),
            'descripcion' => $item['descripcion'],
            'grado' => $item['grado'],
            'tipo_cargo' => $item['tipo_cargo'],
            'salario_actual' => null,
            'id_empresa' => $item['id_empresa'],
            'id_sucursal' => $id_sucursal,
            'id_departamento' => $id_departamento,
            'id_sub_departamento' => $id_subdepartamento,
        ];

        if (!($empleado = Empleado::create($data_empleado))) {
            return false;
        }

        $empleado->empresas()->attach($item['id_empresa'], [
            'id_sucursal' => $id_sucursal,
            'id_departamento' => $id_departamento,
            'id_sub_departamento' => $id_subdepartamento,
        ]);

        $usuario = User::create([
            'nombre' => self::nombreUsuario($persona),
            'contrasena' => '123',
        ]);

        if (!$usuario) {
            return false;
        }

        $usuario->persona()->sync([$persona->id]);

        usleep(200);

        return false;
    }


    private function formatoFecha($fecha) {
        return !empty($fecha) ? Funciones::formatoFechaSistema($fecha) : null;
    }

    private function formatoFechaHtml($fecha) {
        return !empty($fecha) ? Funciones::formatoFechaApp($fecha) : null;
    }

    private static function nombreUsuario($persona) {
        $nombre = strtolower(substr($persona->primer_nombre,0,1)) . strtolower($persona->primer_apellido);
        $indice = '';
        while (User::where('nombre', '=', $nombre)->count()) {
            if ($indice === '') {
                $indice = 1;
            } else {
                $indice++;
            }
            $nombre = $nombre . $indice;
        }
        return $nombre;
    }


    public function alternarHabilitarPost() {
        $id_usuario = (int)Input::get('id_usuario');

        if (!($usuario = User::find($id_usuario))) {
            return $this->retornarError(self::ERROR_NO_ENCONTRADO);
        }

        $usuario->alternarHabilitar();

        $this->especificarRespuesta('status', $usuario->status);
        return $this->retornar();
    }

}