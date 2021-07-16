<?php
/**
 * Created by PhpStorm.
 * User: Alfredo Fleming
 * Date: 3/5/2019
 * Time: 6:54 PM
 */

namespace App\Http\Controllers;


use App\Atributo;
use App\Categoria;
use App\Departamento;
use App\Empresa;
use App\EventoProducto;
use App\Marca;
use App\Modelo;
use App\ModeloProducto;
use App\Producto;
use App\Subdepartamento;
use App\Sucursal;
use App\TipoProducto;
use App\Ubicacion;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProductoController extends Controlador {

    protected $modelo = 'Producto';


    public function antesDeGuardar() {
        $this->antesDeGuardarDefecto();

        //codigo sistema
        {
            //ubicacion
            //-
            //tipo producto
            //marca
            //-
            //secuencia ^^ 1..

            if (Input::has('id_ubicacion')) {
                if (!($id_ubicacion = (int)Input::get('id_ubicacion')) || !($ubicacion = Ubicacion::find($id_ubicacion))) {
                    $this->retornarError('Ubicación no válida.');
                    return false;
                }
            }

            if (!($id_tipo = (int)Input::get('id_tipo')) || !($tipo = TipoProducto::find($id_tipo))) {
                $this->retornarError('Tipo de producto no válido.');
                return false;
            }

            if (!($id_marca = (int)Input::get('id_marca')) || !($marca = Marca::find($id_marca))) {
                $this->retornarError('Marca no válida.');
                return false;
            }

            if (!($id_modelo = (int)Input::get('id_modelo')) || !($modelo = ModeloProducto::find($id_modelo))) {
                $this->retornarError('Modelo no válido.');
                return false;
            }

            $codigo = (!empty($ubicacion) ? ($ubicacion->abreviatura . '-') : '') . $tipo->abreviatura . $marca->abreviatura;

            $cantidad_productos = (int)Producto::whereRaw("codigo_sistema LIKE '{$codigo}%'")->count() + 1;

            $codigo_final = $codigo . '-' . str_pad($cantidad_productos, 3, '0', STR_PAD_LEFT);

            Input::merge([
                'codigo_sistema' => $codigo_final
            ]);
        }

        //nombre
        {
            $nombre = trim(Input::get('nombre'));

            if (empty($nombre)) {
                /*$id_modelo = (int)Input::get('id_modelo');
                $modelo = $id_modelo ? ModeloProducto::find($id_modelo) : null;*/

                $nombre = $tipo->nombre . ' ' . $marca->nombre . ($modelo ? (' ' . $modelo->nombre) : '');

                Input::merge([
                    'nombre' => $nombre
                ]);
            }
        }

        return true;
    }


    /**
     * @param Producto $item
     */
    public function despuesDeGuardar($item) {
        if (!($id_evento = Input::get('id_evento'))) {
            $evento = $item
                ->eventos()
                ->orderBy('fecha_creacion', 'DESC')
                ->first();

            if ($evento) {
                $id_evento = $evento->id;
            }
        }

        if (!$id_evento) {
            $status = (int)Input::get('status');
            if ($status) {
                $evento = EventoProducto::create([
                    'id_producto' => $item->id,
                    'status' => $status,
                    'observaciones' => Input::get('observaciones'),
                ]);
            }
        }
        else {
            if ($evento = EventoProducto::find((int)$id_evento)) {
                $evento->fotos()->delete();
            }
        }

        //carga las imagenes
        if (!empty($evento)) {
            $cantidad_imagenes = 3;

            $primera_imagen = true;
            for ($i = 1; $i <= $cantidad_imagenes; $i++) {
                $nombre_imagen = ImagenController::subirImagenParaItem(null, 'foto_' . $i);

                if ($nombre_imagen) {
                    if ($primera_imagen) {
                        $item->foto = $nombre_imagen;
                        $item->save();
                        $primera_imagen = false;
                    }

                    if ($evento) {
                        $evento->fotos()->create([
                            'foto' => $nombre_imagen,
                        ]);
                    }
                }
            }

            if ($primera_imagen) { //no se cargó ninguna imagen
                $item->foto = '';
                $item->save();
            }
        }

        //atributos
        {
            $atributos = Atributo::get(['id']);
            $p_atributos = [];

            foreach ($atributos as $atributo) {
                if ($valor_atributo = Input::get('atributo_' . $atributo->id)) {
                    $p_atributos[$atributo->id] = [
                        'valor' => $valor_atributo == 'on' ? '1' : $valor_atributo
                    ];
                }
            }

            $item->atributos()->sync($p_atributos);
        }
    }


    /**
     * @param Producto $item
     */
    public function itemDataAdicional($item) {
        $evento = $item->eventos()->orderBy('fecha_creacion', 'DESC')->first();

        if ($evento) {
            $fotos = $evento->fotos()->get(['foto'])->pluck('foto')->toArray();
            $n = 1;

            foreach ($fotos as $foto) {
                $this->especificarRespuesta('foto_' . $n, $foto);
                $n++;
            }
        }

        //atributos
        $atributos = $item->listaAtributos();
        $this->especificarRespuesta('atributos', $atributos);
    }


    public function cargarListadosGet() {
        $items = [
            'tipos' => 'TipoProducto',
            'categorias' => 'Categoria',
            'marcas' => 'Marca',
            'modelos' => 'ModeloProducto',
        ];

        foreach ($items as $item => $modelo) {
            $obj = '\App\\' . $modelo;

            $listado = $obj::orderBy('nombre')
                ->get(['id', 'nombre', 'abreviatura'])
                ->toArray();

            $this->especificarRespuesta($item, $listado);
        }

        //atributos
        {
            $atributos = Atributo::orderBy('nombre')
                ->get([
                    'id',
                    'nombre',
                    'tipo',
                ])
                ->toArray();

            $this->especificarRespuesta('atributos', $atributos);
        }

        //atributos por tipos
        {
            $t_atributos = TipoProducto::with('atributos')
                ->get()
                ->toArray();

            $lista = [];
            foreach ($t_atributos as $t_atributo) {
                $lista[] = [
                    'id_tipo' => $t_atributo['id'],
                    'atributos' => array_column($t_atributo['atributos'], 'id'),
                ];
            }

            $this->especificarRespuesta('atributos_por_tipos', $lista);
        }

        //empresas
        {
            $empresas = Empresa::orderBy('nombre')
                ->get(['id', 'nombre'])
                ->toArray();

            $this->especificarRespuesta('empresas', $empresas);
        }

        //sucursales
        {
            $sucursales = Sucursal::orderBy('nombre')
                ->get(['id', 'nombre', 'id_empresa'])
                ->toArray();

            $this->especificarRespuesta('sucursales', $sucursales);
        }

        //departamentos
        {
            $departamentos = Departamento::orderBy('nombre')
                ->get(['id', 'nombre', 'tipo', 'id_sucursal'])
                ->toArray();

            $this->especificarRespuesta('departamentos', $departamentos);
        }

        //sub departamentos
        {
            $sub_departamentos = Subdepartamento::orderBy('nombre')
                ->get(['id', 'nombre', 'tipo', 'id_departamento'])
                ->toArray();

            $this->especificarRespuesta('sub_departamentos', $sub_departamentos);
        }

        //ubicación
        {
            $ubicaciones = Ubicacion::orderBy('nombre')
                ->get(['id', 'nombre', 'tipo', 'id_sucursal', 'id_sub_departamento'])
                ->toArray();

            $this->especificarRespuesta('ubicaciones', $ubicaciones);
        }

        return $this->retornar();
    }


    //region Importar Productos
    public function importarProductoPost() {
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
                'codigo_sistema'        => 'Código Sistema',
                'cantidad'              => 'Cantidad',
                'nombre'                => 'Nombre', //AKA descripcion
                'abreviatura_categoria' => 'Abreviatura Categoría',
                'nombre_tipo'           => 'Nombre Tipo', //AKA descripcion del activo
                'abreviatura_tipo'      => 'Abreviatura Tipo',
                'nombre_marca'          => 'Nombre Marca',
                'abreviatura_marca'     => 'Abreviatura Marca',
                'nombre_modelo'         => 'Nombre modelo',
                'num_serie'             => 'N° de serie', //atributo
                'color'                 => 'Color', //atributo
                'desc_estado'           => 'Estado',
                'responsable'           => 'Responsable',
                'nombre_ubicacion'      => 'Nombre Ubicación',
                'abreviatura_ubicacion' => 'Abreviatura Ubicación',
                'observaciones'         => 'Observaciones',
            ];

          

            //validaciones
            $importar->validaciones_columnas = [
                //'codigo_sistema' => 'required',
                'abreviatura_tipo' => 'required',
                'abreviatura_marca' => 'required',
                'nombre_modelo' => 'required',
                //'abreviatura_ubicacion' => 'required',
            ];

            //empresa
            {
                if (!($empresa = Empresa::first())) {
                    return $this->retornarError('No hay una empresa definida');
                }
                $id_empresa = $empresa->id;
            }

            //atributo num de serie
            {
                if (!($atributo_num_serie = Atributo::where('nombre', '=', 'Número de Serie')->first())) {
                    return $this->retornarError('Atributo Num de Serie no existe.');
                }
                $id_atributo_num_serie = $atributo_num_serie->id;
            }

            //atributo color
            {
                if (!($atributo_color = Atributo::where('nombre', '=', 'Color')->first())) {
                    return $this->retornarError('Atributo Color no existe.');
                }
                $id_atributo_color = $atributo_color->id;
            }

            //verificación
            $importar->proceso_verificar = function(&$item) use ($id_empresa) {
                return self::verificarProductoImportar($item, $id_empresa);
            };

            //al guardar
            $importar->proceso_guardar = function($item) use ($id_atributo_num_serie, $id_atributo_color) {
                return self::guardarProductoImportar($item, $id_atributo_num_serie, $id_atributo_color);
            };

            //formato para las columnas al previsualizar
            $importar->formatos_columnas = [
                /*'fecha_inscripcion' => function($valor) {
                    return self::formatoFecha($valor);
                },
                'fecha_nacimiento' => function($valor) {
                    return self::formatoFecha($valor);
                },
                'turno' => function($valor) {
                    return self::formatoTurno($valor);
                },
                'beca' => function($valor) {
                    return !empty($valor) ? (floatval($valor) . '%') : '';
                }*/
            ];

        

            $this->especificarRespuesta('precargar', $precargar);
            $this->especificarRespuesta('resultado', $resultado);
            $this->especificarRespuesta('total_leidos', $importar->total_leidos);
            $this->especificarRespuesta('total_cargados', $importar->total_cargados);

            return $this->retornar(!$precargar ? __('global.saved_msg') : null);
        }

        return $this->retornar();
    }


    private static function verificarProductoImportar(&$item, $id_empresa) {
        //tipo
        {
            if (!($tipo = TipoProducto::where('abreviatura', '=', $item['abreviatura_tipo'])->first())) {
                return [
                    'ok' => false,
                    'error' => 'Tipo de producto no existente.'
                ];
            }
            $item['id_tipo'] = $tipo->id;
        }

        //marca
        {
            if (!($marca = Marca::where('abreviatura', '=', $item['abreviatura_marca'])->first())) {
                return [
                    'ok' => false,
                    'error' => 'Marca no existente.'
                ];
            }
            $item['id_marca'] = $marca->id;
        }

        //modelo
        {
            //if (!($modelo = ModeloProducto::where('abreviatura', '=', $item['abreviatura_modelo'])->get())) {
            if (!($modelo = ModeloProducto::where('nombre', '=', $item['nombre_modelo'])->first())) {
                return [
                    'ok' => false,
                    'error' => 'Modelo no existente.'
                ];
            }
            $item['id_modelo'] = $modelo->id;
        }

        //empresa
        {
            $item['id_empresa'] = $id_empresa;
        }

        //ubicacion
        if (!empty($item['abreviatura_ubicacion'])) {
            if (!($ubicacion = Ubicacion::where('abreviatura', '=', $item['abreviatura_ubicacion'])->first())) {
                return [
                    'ok' => false,
                    'error' => 'Ubicación no existente.'
                ];
            }
            $item['id_ubicacion'] = $ubicacion->id;
        }
        else {
            $item['id_ubicacion'] = null;
        }

        //sub-departamento
        {
            /*if (!($sub_departamento = Subdepartamento::find((int)$ubicacion->id_sub_departamento))) {
                return [
                    'ok' => false,
                    'error' => 'Sub-Departamento no existente.'
                ];
            }*/
            $item['id_sub_departamento'] = null;//$sub_departamento->id;
        }

        //departamento
        {
            /*if (!($departamento = Departamento::find((int)$sub_departamento->id_departamento))) {
                return [
                    'ok' => false,
                    'error' => 'Departamento no existente.'
                ];
            }*/
            $item['id_departamento'] = null;//$departamento->id;
        }

        //categoria
        {
            if (!($categoria = Categoria::where('abreviatura', '=', $item['abreviatura_categoria'])->first())) {
                return [
                    'ok' => false,
                    'error' => 'Categoría no existente.'
                ];
            }
            $item['id_categoria'] = $categoria->id;
        }

        //nombre
        {
            if (empty(trim($item['nombre']))) {
                $item['nombre'] = $tipo->nombre . ' ' . $marca->nombre . ' ' . $modelo->nombre;
            }
        }

        //codigo unico
        {
            /*if (!empty($item['num_serie'])) {
                $codigo_unico = trim($item['num_serie']);

                if (Producto::where('codigo_unico', '=', $codigo_unico)->first()) {
                    return [
                        'ok' => false,
                        'error' => 'Código unico ya existente.'
                    ];
                }
                $item['codigo_unico'] = $item['num_serie'];
            }*/
        }

        //cantidad
        $item['cantidad'] = (int)$item['cantidad'];

        //codigo sistema
        {
            $codigo = $ubicacion->abreviatura . '-' . $tipo->abreviatura . $marca->abreviatura;
            $cantidad_productos = (int)Producto::whereRaw("codigo_sistema LIKE '{$codigo}%'")->count() + 1;
            $codigo_final = $codigo . '-' . str_pad($cantidad_productos, 3, '0', STR_PAD_LEFT);

            $item['codigo_sistema'] = $codigo_final;
        }

        return true;
    }


    private static function guardarProductoImportar($item, $id_atributo_num_serie, $id_atributo_color) {
        if (!($producto = Producto::create($item))) {
            return false;
        }

        //atributos
        {
            $p_atributos = [];

            if (!empty(trim($item['num_serie']))) {
                $p_atributos[$id_atributo_num_serie] = [
                    'valor' => trim($item['num_serie'])
                ];
            }

            if (!empty(trim($item['color']))) {
                $p_atributos[$id_atributo_color] = [
                    'valor' => trim($item['color'])
                ];
            }

            if (count($p_atributos)) {
                $producto->atributos()->sync($p_atributos);
            }
        }

        //evento
        {
            switch (strtolower($item['desc_estado'])) {
                case 'nuevo':
                    $estado = 1;
                    break;

                case 'be':
                    $estado = 2;
                    break;

                case 'dañado':
                    $estado = 3;
                    break;

                default:
                    $estado = 2;
            }

            $producto->eventos()->create([
                'observaciones' => $item['observaciones'],
                'status' => $estado,
            ]);
        }

        return false;
    }
    //endregion

    /*public function importarMarcaPost() {
        return $this->importarItemPost('marca');
    }

    public function importarModeloPost() {
        return $this->importarItemPost('modelo');
    }*/

    public function importarItemPost($nombre) {
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
                'nombre'        => 'Nombre',
                'abreviatura'   => 'Abreviatura',
            ];

          

            //validaciones
            $importar->validaciones_columnas = [
                'nombre' => 'required',
                'abreviatura' => $nombre != 'modelo_producto' ? 'required' : '',
            ];

            //verificación
            /*$importar->proceso_verificar = function(&$item) use ($id_empresa) {
                return self::verificarProductoImportar($item, $id_empresa);
            };

            //al guardar
            $importar->proceso_guardar = function($item) use ($id_atributo_num_serie, $id_atributo_color) {
                return self::guardarProductoImportar($item, $id_atributo_num_serie, $id_atributo_color);
            };*/

            //al previsualizar
 

            $this->especificarRespuesta('precargar', $precargar);
            $this->especificarRespuesta('resultado', $resultado);
            $this->especificarRespuesta('total_leidos', $importar->total_leidos);
            $this->especificarRespuesta('total_cargados', $importar->total_cargados);

            return $this->retornar(!$precargar ? __('global.saved_msg') : null);
        }

        return $this->retornar();
    }

    public function importarUbicacionPost() {
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
                'nombre'        => 'Nombre',
                'abreviatura'   => 'Abreviatura',
            ];

     

            //validaciones
            $importar->validaciones_columnas = [
                'nombre' => 'required',
                'abreviatura' => 'required',
            ];

            //verificación
            /*$importar->proceso_verificar = function(&$item) use ($id_empresa) {
                return self::verificarProductoImportar($item, $id_empresa);
            };*/

            $sucursal = Sucursal::first();
            $id_sucursal = $sucursal ? $sucursal->id : null;

            //al guardar
            $importar->proceso_guardar = function($item) use ($id_sucursal) {
                //return self::guardarProductoImportar($item);

                $item['id_sucursal'] = $id_sucursal;

                Ubicacion::create($item);

                return false;
            };

   

            $this->especificarRespuesta('precargar', $precargar);
            $this->especificarRespuesta('resultado', $resultado);
            $this->especificarRespuesta('total_leidos', $importar->total_leidos);
            $this->especificarRespuesta('total_cargados', $importar->total_cargados);

            return $this->retornar(!$precargar ? __('global.saved_msg') : null);
        }

        return $this->retornar();
    }
}