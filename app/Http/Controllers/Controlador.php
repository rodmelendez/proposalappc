<?php
/**
 * Created by PhpStorm.
 * User: Alfredo
 * Date: 25/9/2017
 * Time: 12:35 PM
 */

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;

class Controlador {

    /**
     * Especifica el modelo con el que trabaja el controlador.
     * Se define en el controlador que extiende a este
     */
    protected $modelo;

    protected $subdirectorio = null;

    /**
     * Arreglo de valores que se enviarán como respuesta en JSON
     */
    protected $respuesta = [];

    //constantes de errores comunes
    const ERROR_NO_ENCONTRADO = 1;
    const ERROR_PERMISO_DENEGADO = 2;
    const ERROR_SERVIDOR = 3;

    //constantes de mensajes comunes
    const MSJ_GUARDADO = 1;
    const MSJ_ELIMINADO = 2;


    /**
     * Retorna la página principal para el modelo
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function admin() {
        $recurso = Str::snake($this->modelo);

        if (User::tienePermiso('consultar', ucfirst($this->modelo))) {
            return view($recurso)->with('fuente', $recurso);
        }

        return $this->sinPermiso();
    }


    /**
     * Retorna el JSON con los registros de la tabla
     * Usado por el DataTables
     *
     * @return string
     */
    public function index() {
        //lee los parametros enviados
        //TODO

        //prepara los datos para enviar al DataTables
        $dt = new DatatablesController;
        /*$dt->draw = 1;
        $dt->records_total = 0;
        $dt->records_filtered = 0;*/
        $dt->data = $this->data();

        return $dt->json();
    }


    public function indexGet() {
        $this->cargarModeloDesdeFuente();
        if (!empty($this->modelo)) {
            $obj_name = '\App\\' . (!empty($this->subdirectorio) ? ($this->subdirectorio . '\\') : '') . $this->modelo;
            $obj = new $obj_name;
            //return response()->json(["status" => "ok" , "data" => json_encode($obj) ]);
            //var_dump($obj); dd();
            return $obj->traerData();
        }

        return [];
    }


    /**
     * Retorna el JSON con los datos de un registro
     *
     * @return array
     */
    public function itemData() {
        if (!empty($this->modelo)) {
            $id = (int)Input::get('id');
            $obj_name = '\App\\' . (!empty($this->subdirectorio) ? ($this->subdirectorio . '\\') : '') . $this->modelo;
            $obj = $obj_name::find($id);
            
            if ($obj) {
                $this->respuesta = $obj->toArray();
                $this->itemDataAdicional($obj);
                return $this->retornar();
            }
        }
        
        return $this->retornarError(self::ERROR_NO_ENCONTRADO);
    }


    /**
     * Permite manipular o agregar valores adicionales al arreglo de datos en la respuesta.
     *
     * @param $item
     */
    public function itemDataAdicional($item) {

    }


    #GETTING

    /**
     * Redirecciona el listado según la fuente
     *
     * @return string
     */
    public function listadoGet() {
        $this->cargarModeloDesdeFuente();
        if (!empty($this->modelo)) {
            $nombre_controlador = '\App\Http\Controllers\\' . (!empty($this->subdirectorio) ? ($this->subdirectorio . '\\') : '') . $this->modelo . 'Controller';
            $obj = new $nombre_controlador;
            return $obj->listado();
        }

        return '[]';
    }


    /**
     * Realiza la búsqueda y retorna el JSON usado para el plugin Select2
     */
    public function listado() {
        $q = Input::get('busqueda');
        $id = Input::get('id');
        if (!empty($id)) {
            $items = $this->listadoItem($id);
        }
        else {
            $items = $this->buscar($q);
        }

        $resultado = $this->listadoResultado($items);

        $resultado_json = json_encode([
            'results' => $resultado
        ]);

        if ($resultado_json === false && json_last_error() == JSON_ERROR_UTF8) {
            $resultado_utf8 = [];
            foreach ($resultado as $item) {
                $item = array_map('utf8_decode', $item);
                $resultado_utf8[] = $item;
            }
            $resultado_json = json_encode([
                'results' => $resultado_utf8
            ]);
        }

        if ($resultado_json === false) {
            $resultado_json = 'ERROR. (Controlador@listado)';
        }

        return $resultado_json;
    }


    /**
     * Realiza la búsqueda por id
     */
    public function listadoItem($id = null) {
        if ($id === null) {
            $id = Input::get('id', 0);
        }

        $items = DB::table($this->tablaBusqueda());
        $campo_fecha_eliminacion = $this->campoFechaEliminacion();

        $items = $items->where('id', '=', (int)$id);

        if (!empty($campo_fecha_eliminacion)) {
            $items = $items->whereNull($campo_fecha_eliminacion);
        }

        return $items->get();
    }


    /**
     * Prepara el arreglo con los valores que se enviarán por JSON usado para el plugin Select2
     *
     * @param $items
     * @return array
     */
    public function listadoResultado($items) {
        $campo_principal = $this->campoPrincipal();
        $resultado = [];
        foreach ($items as $item) {
            $resultado[] = [
                'id' => $item->id,
                'text' => ucfirst(mb_strtolower($item->$campo_principal, 'UTF-8'))
            ];
        }
        return $resultado;
    }


    /**
     * Arreglo de elementos según la fuente
     *
     * @return string
     */
    public function arregloGet() {
        $this->cargarModeloDesdeFuente();
        if (!empty($this->modelo)) {
            $nombre_controlador = '\App\Http\Controllers\\' . (!empty($this->subdirectorio) ? ($this->subdirectorio . '\\') : '') . $this->modelo . 'Controller';
            $obj = new $nombre_controlador;
            return $obj->arreglo();
        }

        return '[]';
    }


    /**
     * Arreglo con los elementos del modelo
     */
    public function arreglo() {
        $modelo = '\App\\' . (!empty($this->subdirectorio) ? ($this->subdirectorio . '\\') : '') . $this->modelo;
        $items = $modelo::get()->toArray();
        $this->especificarRespuesta('items', $items);
        return $this->retornar();
    }


    /**
     * Llama la función itemData de un controlador según la fuente
     */
    public function get() {

        //_fuente se envia desde el formulario con el nombre del modelo
        $fuente = Input::get('_fuente');
        $subdirectorio = Input::get('_subdirectorio', null);

        if ($subdirectorio === null) {
            $str_tipo = Input::get('tipo', '');
            $tipo = is_string($str_tipo) && !empty($str_tipo) ? json_decode($str_tipo) : false;
            if (is_object($tipo) && !empty($tipo->subdirectorio)) {
                $subdirectorio = $tipo->subdirectorio;
            }
        }

        if ($fuente) {
            $this->modelo = ucfirst($fuente);
            $this->subdirectorio = $subdirectorio;

            //$id = (int)Input::get('id');
            $accion = Input::get('_accion');
            
            //if ($id) {
                $obj_name = '\App\Http\Controllers\\' . (!empty($subdirectorio) ? ($subdirectorio . '\\') : '') . ucfirst(Str::camel($fuente)) . 'Controller';
                $obj = new $obj_name;

                if ($obj) {
                    if (!empty($accion)) {
                        $accion = $accion.'Get';
                        //var_dump($obj_name);
                        if (method_exists($obj, $accion)) {
                            return $obj->$accion();
                        }

                        return $this->retornarError('Método ' . $accion  . ' no encontrado.');
                    }

                    return $obj->itemData();
                }
            //}
        }

        return $this->retornarError();
    }


    #POSTING

    /**
     * Llama la función crear, editar u otra definida por '_accion', de un modelo según la fuente
     *
     * @return array
     */
    public function post() {
        //_fuente se envia desde el formulario con el nombre del modelo
        $fuente = Input::get('_fuente');
        $subdirectorio = Input::get('_subdirectorio');
         
        if ($fuente) {
            $this->modelo = ucfirst(Str::camel($fuente));
            $this->subdirectorio = $subdirectorio;

            $obj_name = '\App\Http\Controllers\\' . (!empty($subdirectorio) ? ($subdirectorio . '\\') : '') . $this->modelo . 'Controller';
            $obj = new $obj_name;

            if ($obj) {
                $usuario = Auth::user();

                if (!$obj->prePost()) {
                    return $this->retornar();
                }

                $id = (int)Input::get('id');
                $accion = Input::get('_accion');
                //accion definida
                if (!empty($accion)) {
                    $accion = $accion . 'Post';
                    if (method_exists($obj, $accion)) {
                        return $obj->$accion();
                    }

                    return $this->retornarError('Método ' . $accion . ' no encontrado.');
                }

                //acciones bases

                $puede_crear = User::tienePermiso('crear', $this->modelo);

                if (!$id) {
                    //nuevo registro
                    if (!$puede_crear) {
                        //return $obj->retornarError(self::ERROR_PERMISO_DENEGADO);
                    }
                    return $obj->crearPost();
                    
                } else {
                    //editar registro
                    
                    if ($puede_crear) {
                        $modelo_nombre = '\App\\' . (!empty($this->subdirectorio) ? ($this->subdirectorio . '\\') : '') . $this->modelo;
                        $item_a_editar = $modelo_nombre::find((int)$id);
                        
                        $puede_eliminar = User::tienePermiso('eliminar', $this->modelo);
                        
                        //puede editar si puede crear y eliminar o si puede crear y el item a editar ha sido creado por el mismo usuario
                        $puede_editar = $puede_crear && ($puede_eliminar || ($item_a_editar && !empty($item_a_editar->id_usuario) && $item_a_editar->id_usuario == $usuario->id));
                    }
                    else {
                        $puede_editar = false;
                    }
                    
                    if (!$puede_editar) {
                        //return $obj->retornarError(self::ERROR_PERMISO_DENEGADO);
                    }
                    return $obj->editarPost();
                }
            }
        }
        
        return $this->retornarError();
    }
    
    
    /**
     * Lee y procesa las entradas para crear un registro
     */
    public function crearPost() {
        $modelo_nombre = '\App\\' . (!empty($this->subdirectorio) ? ($this->subdirectorio . '\\') : '') . $this->modelo;
        $obj = new $modelo_nombre;
        
        if (!$this->antesDeValidar()) {
            return $this->retornar();
        }
        
        $validacion = Validator::make(
            Input::all(),
            $obj->reglasValidacion(null, (int)Input::get('id'))
        );
        
        if ($validacion->passes()) {
            if (!$this->antesDeGuardar()) {
                return $this->retornar();
            }
            
            $this->verificarBooleanos($obj);
           
            $nuevo = $obj->create(Input::all());
        
            $this->especificarRespuesta('id', $nuevo->id);
            $this->especificarRespuesta('msj', __('global.saved_msg'));

            $this->despuesDeGuardar($nuevo);
        }
        else {
            //$msj = $validacion->errors()->first();
            list($msj, $campo) = $this->mensajeYCampoDeError($validacion);
            return $this->retornarError($msj, $campo);
        }

        return $this->retornar();
    }


    /**
     * Lee y procesa las entradas para editar un registro
     */
    public function editarPost() {
        $modelo_nombre = '\App\\' . (!empty($this->subdirectorio) ? ($this->subdirectorio . '\\') : '') . $this->modelo;
        $obj = new $modelo_nombre;

        if (!$this->antesDeValidar()) {
            return $this->retornar();
        }

        $id = (int)Input::get('id');

        $validacion = Validator::make(
            Input::all(),
            $obj->reglasValidacion(null, $id)
        );

        if ($validacion->passes()) {
            if (!$this->antesDeGuardar()) {
                return $this->retornar();
            }

            $this->verificarBooleanos($obj);

            $item = $modelo_nombre::find($id);
            if ($item) {
                $item->update(Input::all());

                $this->especificarRespuesta('id', $id);
                $this->especificarRespuesta('msj', __('global.saved_msg'));

                $this->despuesDeGuardar($item);
            }
            else {
                return $this->retornarError(self::ERROR_NO_ENCONTRADO);
            }
        }
        else {
            //$msj = $validacion->errors()->first();
            list($msj, $campo) = $this->mensajeYCampoDeError($validacion);
            return $this->retornarError($msj, $campo);
        }

        return $this->retornar();
    }


    /**
     * Lee y procesa las entradas para eliminar registros
     */
    public function eliminarPost() {
        $id = json_decode(Input::get('id'));
        $subdirectorio = Input::get('_subdirectorio');

        if (is_string($id)) {
            $id = json_decode($id); //id puede ser un arreglo
        }

        if (!empty($id)) {
            $modelo_nombre = '\App\\' . (!empty($subdirectorio) ? ($subdirectorio . '\\') : '') . $this->modelo;
            $this->subdirectorio = $subdirectorio;

            $elimindos = [];

            //múltiples registros
            if (is_array($id)) {
                $ids = $id;
                foreach ($ids as $id) {
                    $id = (int)$id;
                    $obj = $modelo_nombre::find($id);
                    if ($obj) {
                        $elimindos[] = $obj->id;
                        $obj->eliminar();
                    }
                }
            }

            //un solo registro
            else {
                $id = (int)$id;
                $obj = $modelo_nombre::find($id);
                if ($obj) {
                    $elimindos[] = $obj->id;
                    $obj->eliminar();
                }
            }

            $total_eliminados = count($elimindos);

            if ($total_eliminados) {
                $this->especificarRespuesta('eliminados', $elimindos);
                return $this->retornar(Lang::choice('global.del_msg_plural', $total_eliminados, ['n' => $total_eliminados]));
            }
            else {
                return $this->retornarError(self::ERROR_NO_ENCONTRADO);
            }
        }

        return $this->retornarError();
    }


    public function verificarBooleanos($obj = null) {
        if ($obj === null) {
            $modelo_nombre = '\App\\' . (!empty($this->subdirectorio) ? ($this->subdirectorio . '\\') : '') . $this->modelo;
            $obj = new $modelo_nombre;
        }

        $campos = $obj->booleanos;
        if (is_array($campos)) {
            foreach ($campos as $campo) {
                if (!Input::has($campo)) {
                    Input::merge([
                        $campo => 0
                    ]);
                }
            }
        }
    }


    /**
     * Procedimiento opcional que se ejecuta al enviarse una solicitud post
     * Si se retorna falso, se cancela el procedimiento de crear o editar
     */
    public function prePost() {
        return true;
    }


    /**
     * Procedimiento opcional para verifiar o manipular los Inputs antes de validar
     * Si se retorna falso, se cancela el procedimiento de crear o editar
     */
    public function antesDeValidar() {
        return true;
    }


    /**
     * Procedimiento opcional para manipular los Inputs después de validar y antes de guardar
     * Si se retorna falso, se cancela el procedimiento de crear
     */
    public function antesDeGuardar() {
        $this->antesDeGuardarDefecto();
        return true;
    }


    /**
     * Procedimiento opcional que se realizar después de guardar un registro
     *
     * @param $item
     */
    public function despuesDeGuardar($item) {

    }


    /**
     * Procedimiento opcional que se realizar después de eliminar un registro
     *
     * @param $item
     */
    public function despuesDeEliminar($item) {

    }


    /**
     * Proceso antes de guardar por defecto
     */
    public function antesDeGuardarDefecto() {
        //agrega el id del usuario para guardarse en la tabla
        $usuario_id = (int)Auth::user()->id;
        if ($usuario_id) {
            Input::merge([
                'id_usuario' => $usuario_id
            ]);
        }
    }


    #RESPUESTAS

    /**
     * Retorna el JSON contenido en respuesta
     *
     * @param null $msj
     * @return array
     */
    public function retornar($msj = null) {
        if (!isset($this->respuesta['ok'])) {
            $this->respuesta['ok'] = 1;
        }

        if (!empty($msj)) {
            if (is_int($msj)) {
                switch ($msj) {
                    case self::MSJ_GUARDADO: $msj = __('global.saved_msg'); break;
                    case self::MSJ_ELIMINADO: $msj = __('global.del_msg'); break;
                    //case ...
                    default: $msj = '';
                }
            }
            $this->respuesta['msj'] = $msj;
        }

        return /*json_encode(*/$this->respuesta/*)*/;
    }


    /**
     * Especifíca un error y lo retorna
     *
     * @param null $err
     * @param null $nombre_campo
     * @return array
     */
    public function retornarError($err = null, $nombre_campo = null) {
        if ($err === null) {
            $err = __('global.wrong_action');
        }
        elseif (is_int($err)) {
            switch ($err) {
                case self::ERROR_NO_ENCONTRADO: $err = __('global.not_found'); break;
                case self::ERROR_PERMISO_DENEGADO: $err = __('global.no_permission'); break;
                case self::ERROR_SERVIDOR: $err = __('global.unable_perform_action'); break;
                default: $err = __('global.wrong_action');
            }
        }

        $this->respuesta['ok'] = 0;
        $this->respuesta['err'] = $err;

        if (!empty($nombre_campo)) {
            $this->respuesta['err_campo'] = $nombre_campo;
        }

        return $this->retornar();
    }


    /**
     * Establece un valor a una propiedad que se enviará mediante JSON
     *
     * @param $propiedad
     * @param $valor
     */
    public function especificarRespuesta($propiedad, $valor) {
        $this->respuesta[$propiedad] = $valor;
    }


    /**
     * Agrega un arreglo de valores al arreglo de respuesta
     *
     * @param array $arr
     * @param bool $sobrescribir
     */
    public function agregarArregloEnRespuesta(Array $arr, $sobrescribir = false) {
        if (!$sobrescribir) {
            $this->respuesta += $arr;
        }
        else {
            $this->respuesta = array_merge($this->respuesta, $arr);
        }
    }


    #DATATABLES

    /**
     * Retorna la lista de las cabeceras que se van a cargar en el DataTables
     * El texto que se va a mostrar al usuario debe definirse en el archivo lang
     *
     * @return array
     */
    public static function cabeceras() {
        return [
            'id',
            'nombre',
            'descripcion'
        ];
    }


    /**
     * Retorna la lista de los pies que se van a cargar en el DataTables
     * El texto que se va a mostrar al usuario debe definirse en el archivo lang
     *
     * @return array
     */
    public static function pies() {
        return [];
    }


    /**
     * Retorna un arreglo con las columnas y nombre de las plantillas
     * que se pasarán al datatables. Las plantillas están definidas en la
     * función 'formatoColumna' en tabla.js
     *
     * @return array
     */
    public static function configuracionColumnasDataTables() {
        return [];
    }


    /**
     * Arreglo de todos los registros en la tabla
     *
     * @return mixed
     */
    public function data() {
        $modelo = '\App\\' . (!empty($this->subdirectorio) ? ($this->subdirectorio . '\\') : '') . $this->modelo;
        return $modelo::traerData();
    }


    /**
     * Especifica la tabla de la BD en la que se realiza la búsqueda.
     * Por defecto usa el mismo nombre del modelo en minúscula
     *
     * @return string
     */
    public function tablaBusqueda() {
        return Str::snake($this->modelo);
    }


    /**
     * Especifica los campos de la tabla en que se va a buscar.
     * Por defecto usa el arreglo 'fillable' definido en el modelo
     *
     * @return array
     */
    public function camposBusqueda() {
        $modelo = '\App\\' . (!empty($this->subdirectorio) ? ($this->subdirectorio . '\\') : '') . $this->modelo;
        $obj = new $modelo;
        return $obj->getFillable();
    }


    /**
     * Especifica el campo y dirección para ordenar los resultados de la búsqueda
     *
     * @return mixed
     */
    public function campoOrdenar() {
        return ['fecha_creacion', 'DESC'];
    }


    /**
     * Especifica el campo principal para mostrar en los resultados de la búsqueda
     *
     * @return mixed
     */
    public function campoPrincipal() {
        return 'nombre';
    }


    /**
     * Especifica el campo principal para mostrar en los resultados de la búsqueda
     *
     * @return mixed
     */
    public function campoFechaEliminacion() {
        $modelo = '\App\\' . (!empty($this->subdirectorio) ? ($this->subdirectorio . '\\') : '') . $this->modelo;
        $item = new $modelo;
        if (!$item->timestamps) return false;
        return $item->DELETED_AT;
    }


    /**
     * Busca y retorna coincidencias en los registros del modelo
     *
     * @param $q
     * @param int $pagina
     * @return array
     */
    public function buscar($q, $pagina = 1) {
        $items = DB::table($this->tablaBusqueda());
        $campos = $this->camposBusqueda();
        $campo_ordenar = $this->campoOrdenar();
        $campo_fecha_eliminacion = $this->campoFechaEliminacion();
        //$pagina = (int)$pagina;
        //if ($pagina <= 0) $pagina = 1;

        if ($q != '*') {
            //se separan los términos de búsqueda por espacios para comparar individualmente
            $q = explode(' ', $q);

            $first = true;
            foreach ($q as $termino) {
                $termino = trim($termino);
                if (strlen($termino) > 0) {
                    $items = $items->where(function ($sql_query) use ($campos, $termino) {
                        $first_query = true;
                        foreach ($campos as $attr) {
                            if ($first_query) {
                                //$sql_query->where($attr, 'LIKE', '%' . $termino . '%'); //<-- mysql

                                //el operador iLIKE permite buscar ignorando mayúsculas y minúsculas (postgres)
                                $sql_query->whereRaw('sin_acento(' . $attr . ') ILIKE sin_acento(\'%' . $termino . '%\')'); //<-- postgres

                                $first_query = false;
                            }
                            else {
                                $sql_query->orWhereRaw('sin_acento(' . $attr . ') ILIKE sin_acento(\'%' . $termino . '%\')');
                            }
                        }
                    });
                    $first = false;
                }
            }
        }
        else {
            $first = false;
        }

        if (!$first) {
            //para no traer registros eliminados
            if (!empty($campo_fecha_eliminacion)) {
                $items = $items->whereNull($campo_fecha_eliminacion);
            }

            //en casos donde hay condiciones adicionales
            $items = $this->posBuscar($items);

            //$total_coincidencias = $items->count();
            //$items = $items->skip(($pagina-1) * static::PAGE_LIMIT)->limit(static::PAGE_LIMIT);

            if (!empty($campo_ordenar)) {
                if (is_array($campo_ordenar)) {
                    $items = $items->orderBy($campo_ordenar[0], $campo_ordenar[1]);
                }
                else {
                    $items = $items->orderBy($campo_ordenar);
                }
            }

            $items = $items->get();

            return $items;
        }

        return [];
    }


    /**
     * Para búsquedas personalizadas; se ejecuta después de especificar las condiciones
     *
     * @param $items
     * @return mixed
     */
    public function posBuscar($items) {
        return $items;
    }


    # HELPERS

    /**
     * Extrae el primer mensaje y el campo correspondiente de la validación fallida
     *
     * @param Validator $validacion
     * @return array
     */
    protected function mensajeYCampoDeError($validacion) {
        $err = $validacion->messages()->toArray();
        $msj = reset($err)[0];
        $campo = key($err);
        return [$msj, $campo];
    }


    /**
     * Carga el modelo desde el parámetro fuente
     *
     * @param bool|false $forzar
     */
    protected function cargarModeloDesdeFuente($forzar = false) {
        
        if (!$forzar && !empty($this->modelo)) {
            return;
        }
        $fuente = Input::get('_fuente');
        dd($fuente);
        if (!empty($fuente)) {
            $this->modelo = ucfirst(Str::camel($fuente));
        }
    }


    /**
     * Guarda las relaciones de tipo muchos a muchos de un modelo.
     * La relación debe estar definida en el modelo
     *
     * @param $item
     * @param $nombre_campo
     * @param bool $validar_int
     */
    protected function guardarRelaciones($item, $nombre_campo, $validar_int = false) {
        $relaciones = Input::get($nombre_campo, []);
        if (is_array($relaciones)) {
            if ($validar_int) {
                $relaciones = array_map('intval', $relaciones);
            }
            $item->$nombre_campo()->sync($relaciones);
        }
    }


    /**
     * Guarda la relación para un campo de tipo moneda.
     * La relación debe estar definida en el modelo
     *
     * @param $item
     * @param $nombre_campo
     * @param $nombre_relacion
     */
    protected function guardarMoneda($item, $nombre_campo, $nombre_relacion) {
        $monto_id_moneda = (int)Input::get($nombre_campo . '_id_moneda');
        $monto = Input::get($nombre_campo, false);
        $monto_moneda = Input::get($nombre_campo . '_moneda', false);

        if (!empty($monto_id_moneda) && $monto_moneda !== false) {
            $usuario = Auth::user();
            $id_usuario = $usuario ? $usuario->id : null;

            $item->$nombre_relacion()->create([
                'id_servicio' => $item->id,
                'id_moneda' => $monto_id_moneda,
                'monto' => (float)$monto,
                'monto_moneda' => (float)$monto_moneda,
                'id_usuario' => $id_usuario
            ]);
        }
    }


    /**
     * Envía el nombre de un item relacionado en la respuesta,
     * para ser usada por el Select2
     *
     * @param $item
     * @param $campo
     * @param string $propiedad
     */
    protected function etiquetaCampoSeleccion($item, $campo, $propiedad = 'nombre') {
        $modelo = '\App\\' . (!empty($this->subdirectorio) ? ($this->subdirectorio . '\\') : '') . ucfirst(Str::camel($campo));
        $id_campo = 'id_' . $campo;
        if (!empty($item->$id_campo)) {
            $obj = $modelo::find((int)$item->$id_campo);
            if ($obj) {
                $this->especificarRespuesta('id_' . $campo . '_lbl', $obj->$propiedad);
            }
        }
    }


    /**
     * Envía los nombres y ids de los items relacionados en la respuesta,
     * para ser usada por el Select2
     *
     * @param $item
     * @param $nombre_relacion
     * @param string $propiedad
     */
    protected function etiquetaCampoSeleccionMultiples($item, $nombre_relacion, $propiedad = 'nombre') {
        $items = $item->$nombre_relacion()->pluck($propiedad, 'id')->toArray();

        $this->especificarRespuesta($nombre_relacion, $items);
    }


    /**
     * Retorna el total de items en el modelo
     */
    public function totalGet() {
        $total = 0;

        $this->cargarModeloDesdeFuente();
        if (!empty($this->modelo)) {
            $nombre_modelo = '\App\\' . (!empty($this->subdirectorio) ? ($this->subdirectorio . '\\') : '') . $this->modelo;
            $total = (int)$nombre_modelo::count();
        }

        $this->especificarRespuesta('total', $total);
        return $this->retornar();
    }


    public function noEncontrado() {
        abort(404);
        return '';
    }


    public function sinPermiso() {
        abort(401);
        return '';
    }

}