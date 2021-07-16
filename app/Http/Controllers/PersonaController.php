<?php
/**
 * Created by PhpStorm.
 * User: Alfredo
 * Date: 29/9/2017
 * Time: 4:48 PM
 */

namespace App\Http\Controllers;


use App\Funciones;
use App\Persona;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\URL;

class PersonaController extends Controlador {

    protected $modelo = 'Persona';

    const NACIONALIDAD_DEFECTO = 'nicaraguense';


    /*public static function cabeceras() {
        return [
            'id',
            'nombres',
            'apellidos',
            'dni',
            'sexo',
            'direccion_domicilio',
            'fecha_nacimiento',
            'fecha_creacion'
        ];
    }


    public static function pies() {
        return [];
    }*/


    public function camposBusqueda() {
        return [
            'primer_nombre',
            'segundo_nombre',
            'primer_apellido',
            'segundo_apellido',
            'dni'
        ];
    }


    /*public static function listaNacionalidades() {
        return [
            'afgano' => __('persona.afgano'),
            'aleman' => __('persona.aleman'),
            'arabe' => __('persona.arabe'),
            'argentino' => __('persona.argentino'),
            'australiano' => __('persona.australiano'),
            'belga' => __('persona.belga'),
            'boliviano' => __('persona.boliviano'),
            'brasileno' => __('persona.brasileno'),
            'camboyano' => __('persona.camboyano'),
            'canadiense' => __('persona.canadiense'),
            'chileno' => __('persona.chileno'),
            'chino' => __('persona.chino'),
            'colombiano' => __('persona.colombiano'),
            'coreano' => __('persona.coreano'),
            'costarricense' => __('persona.costarricense'),
            'cubano' => __('persona.cubano'),
            'danes' => __('persona.danes'),
            'ecuatoriano' => __('persona.ecuatoriano'),
            'egipcio' => __('persona.egipcio'),
            'salvadoreno' => __('persona.salvadoreno'),
            'escoces' => __('persona.escoces'),
            'espanol' => __('persona.espanol'),
            'estadounidense' => __('persona.estadounidense'),
            'estonio' => __('persona.estonio'),
            'etiope' => __('persona.etiope'),
            'filipino' => __('persona.filipino'),
            'finlandes' => __('persona.finlandes'),
            'frances' => __('persona.frances'),
            'gales' => __('persona.gales'),
            'griego' => __('persona.griego'),
            'guatemalteco' => __('persona.guatemalteco'),
            'haitiano' => __('persona.haitiano'),
            'holandes' => __('persona.holandes'),
            'hondureno' => __('persona.hondureno'),
            'indones' => __('persona.indones'),
            'ingles' => __('persona.ingles'),
            'iraqui' => __('persona.iraqui'),
            'irani' => __('persona.irani'),
            'irlandes' => __('persona.irlandes'),
            'israeli' => __('persona.israeli'),
            'italiano' => __('persona.italiano'),
            'japones' => __('persona.japones'),
            'jordano' => __('persona.jordano'),
            'laosiano' => __('persona.laosiano'),
            'leton' => __('persona.leton'),
            'letones' => __('persona.letones'),
            'malayo' => __('persona.malayo'),
            'marroqui' => __('persona.marroqui'),
            'mexicano' => __('persona.mexicano'),
            'nicaraguense' => __('persona.nicaraguense'),
            'noruego' => __('persona.noruego'),
            'neozelandes' => __('persona.neozelandes'),
            'panameno' => __('persona.panameno'),
            'paraguayo' => __('persona.paraguayo'),
            'peruano' => __('persona.peruano'),
            'polaco' => __('persona.polaco'),
            'portugues' => __('persona.portugues'),
            'puertorriqueno' => __('persona.puertorriqueno'),
            'dominicano' => __('persona.dominicano'),
            'rumano' => __('persona.rumano'),
            'ruso' => __('persona.ruso'),
            'sueco' => __('persona.sueco'),
            'suizo' => __('persona.suizo'),
            'tailandes' => __('persona.tailandes'),
            'taiwanes' => __('persona.taiwanes'),
            'turco' => __('persona.turco'),
            'ucraniano' => __('persona.ucraniano'),
            'uruguayo' => __('persona.uruguayo'),
            'venezolano' => __('persona.venezolano'),
            'vietnamita' => __('persona.vietnamita'),
        ];
    }*/


    /**
     * Después de guardar, se actualiza la foto si es el caso
     *
     * @param \App\Persona $item
     */
    public function despuesDeGuardar($item) {
        ImagenController::subirImagenParaItem($item, 'foto');

        //teléfonos
        $telefonos = Input::get('telefonos', []);
        $item->guardarTelefonos($telefonos);

        //correos
        $correos = Input::get('correos', []);
        $item->guardarCorreos($correos);
    }


    /**
     * Retorna el arreglo de resultados para el select2 incluyendo la foto
     *
     * @param $items
     * @return array
     */
    public function listadoResultado($items) {
        $avatar_defecto = asset('img/avatar-defecto.jpg');

        $resultado = [];

        foreach ($items as $item) {
            $nombre = self::concatenarNombres($item);
            $resultado[] = [
                'id' => $item->id,
                //'text' => ucwords(mb_strtolower($item->primer_nombre . ' ' . $item->primer_apellido, 'UTF-8')),
                'text' => ucwords(mb_strtolower($nombre, 'UTF-8')), //TODO: ucwords no funciona con acentos; nombres con acentos en la primera letra no se van a pasar a mayúsculas
                'subtext' => strtoupper($item->dni),
                'img' => self::urlFoto($item->foto, 's', $avatar_defecto),
            ];
        }
        return $resultado;
    }


    /**
     * Retorna la inicial de un nombre, ignorando casos que incluyan por ejemplo "De" o "Del" o "O'"
     *
     * @param $nombre
     * @return string
     */
    private static function inicialNombrePersonalizada($nombre) {
        $nombres = explode(' ', $nombre);
        $inicial = [];
        foreach ($nombres as $nombre) {
            if (strlen($nombre) > 3) {
                $inicial[] = strtoupper(substr(Funciones::removeAccents($nombre), 0, 1)) . '.';
            }
            else {
                $inicial[] = $nombre;
            }
        }
        return implode(' ', $inicial);
    }


    /**
     * Retorna un solo texto basado en las propiedades nombres y apellidos de la persona
     *
     * @param $item
     * @param bool $iniciales_segundos
     * @param bool $primero_apellidos
     * @return string
     */
    public static function concatenarNombres($item, $iniciales_segundos = false, $primero_apellidos = false) {
        $nombres = [];
        $apellidos = [];
        if (!empty($item->primer_nombre)) $nombres[] = ucfirst($item->primer_nombre);
        if (!empty($item->segundo_nombre)) $nombres[] = $iniciales_segundos ? self::inicialNombrePersonalizada($item->segundo_nombre) : ucfirst($item->segundo_nombre);
        if (!empty($item->primer_apellido)) $apellidos[] = ucfirst($item->primer_apellido);
        if (!empty($item->segundo_apellido)) $apellidos[] = $iniciales_segundos ? self::inicialNombrePersonalizada($item->segundo_apellido) : ucfirst($item->segundo_apellido);

        if ($primero_apellidos) {
            return implode(' ', $apellidos) . ', ' . implode(' ', $nombres);
        }

        return implode(' ', array_merge($nombres, $apellidos));
    }


    /**
     * Retorna un solo texto basado en las propiedades nombres, apellidos y dni de la persona
     *
     * @param $item
     * @param bool $iniciales_segundos
     * @param bool $primero_apellidos
     * @param bool $incluir_etiqueta_small
     * @return string
     */
    public static function concatenarNombresYDni($item, $iniciales_segundos = false, $primero_apellidos = false, $incluir_etiqueta_small = false) {
        $nombres = self::concatenarNombres($item, $iniciales_segundos, $primero_apellidos);

        return $nombres . ($incluir_etiqueta_small ? '<small>' : '') . Funciones::encloseStr($item->dni) . ($incluir_etiqueta_small ? '</small>' : '');
    }


    /**
     * Retorna la url completa para la foto de la persona o el de una imagen por defecto si no tiene una cargada
     *
     * @param null $nombre_foto
     * @param string $tamano
     * @param null $defecto
     * @return string
     */
    public static function urlFoto($nombre_foto = null, $tamano = 's', $defecto = null) {
        if ($defecto === null) {
            $avatar_defecto = asset('img/default-avatar.jpg');
        }
        else {
            $avatar_defecto = $defecto;
        }
        if (!empty($tamano)) $tamano = '/' . $tamano;
        return !empty($nombre_foto) ? asset(config('app.uploads_img_dir') . $tamano . '/' . $nombre_foto) : $avatar_defecto;
    }


    /**
     * Procesa la solicitud para cambiar la foto de perfil de un usuario
     *
     * @return array
     */
    public function subirFotoPost() {
        if (!Input::hasFile('foto_upload')) {
            return $this->retornar(__('persona.foto_no_ingresada'));
        }

        $id_persona = Input::get('id_persona');

        if (!($persona = Persona::find((int)$id_persona))) {
            return $this->retornarError(self::ERROR_NO_ENCONTRADO);
        }

        $nombre = ImagenController::subirImagenParaItem($persona, 'foto');

        $this->especificarRespuesta('url_foto', self::urlFoto($nombre, null));
        return $this->retornar(__('persona.foto_actualizada'));
    }


    public function buscarPorDniGet() {
        $dni = Input::get('valor');

        if (empty($dni)) {
            return $this->retornarError();
        }

        $persona = Persona::where('dni', '=', $dni)->first();
        
        $this->especificarRespuesta('persona', $persona);
        return $this->retornar();
    }


    public static function validarDni($dni, $ignorar_id = null) {
        if (empty($dni)) return true;

        $item = Persona::where('dni', '=', $dni);

        if ($ignorar_id !== null) {
            $item = $item->where('id', '<>', (int)$ignorar_id);
        }

        return $item->count() == 0;
    }


    public static function extenderModeloPersona(&$items, $fn_adicional = null) {
        foreach ($items as $key => $item) {
            unset($items[$key]->fecha_actualizacion);
            unset($items[$key]->fecha_eliminacion);
            $items[$key]->foto = self::urlFoto($item->foto);
            $items[$key]->sexo = $item->sexo == Persona::SEXO_MASCULINO ? __('persona.masculino') : __('persona.femenino');
            if (is_callable($fn_adicional)) {
                $fn_adicional($item);
            }
        }
    }

}