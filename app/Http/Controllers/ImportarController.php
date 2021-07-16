<?php
/**
 * Created by PhpStorm.
 * User: Alfredo
 * Date: 23-May-2018
 * Time: 11:10 AM
 */

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Excel;
use Illuminate\Support\Str;

class ImportarController {

    const FORMATO_HTML = 0;
    const FORMATO_ARRAY = 1;

    const MODO_LEER = 0;
    const MODO_GUARDAR = 1;
    const MODO_LEER_Y_GUARDAR = 2;

    public $archivo;
    public $columnas;
    public $primera_fila;
    public $validaciones_columnas;
    public $formatos_columnas;
    public $formato_resultado;
    public $proceso_verificar;
    public $proceso_guardar;
    public $limite;
    public $log;
    public $nombre;
    public $modelo;
    public $errores;
    public $resultado;
    public $total_leidos;
    public $total_cargados;


    public function __construct() {
        $this->columnas = [];
        $this->validaciones_columnas = [];
        $this->formatos_columnas = [];
        $this->primera_fila = 1;
        $this->formato_resultado = self::FORMATO_ARRAY;
        $this->proceso_verificar = null;
        $this->proceso_guardar = null;
        $this->limite = 500;
        $this->log = true;
        $this->nombre = '';
        $this->modelo = null;
        $this->errores = [];
        $this->resultado = [];
        $this->total_leidos = 0;
        $this->total_cargados = 0;
    }


    public function leer($modo = self::MODO_LEER) {
        if (in_array($this->extension(), ['xls', 'xlsx'])) {
            return $this->leerExcel($modo);
        }
        $delimitador = ',';
        $n_columnas = count($this->columnas);
        $n_fila = 0;
        $n_total_cargados = 0;
        $error = false;
        $n_registros_a_guardar = 0;
        $registros_a_guardar = [];

        $fecha_actual = date('Y-m-d H:i:s');
        $id_usuario = Auth::user()->id;

        $f = fopen($this->archivo, 'r');
        $l = fopen('log_' . $this->nombre . '.txt', 'w');

        $resultado = [];

        if ($f !== false) {
            $primera_linea = true;

            $valores_fila_anterior = null;

            while (!feof($f)) {
                $valores = fgetcsv($f, 0, $delimitador);
                if ($primera_linea || !is_array($valores)) {
                    if ($primera_linea && is_array($valores)) {
                        if (count(explode(';', implode($valores))) > count(explode(',', implode($valores)))) $delimitador = ';';
                    }
                    $primera_linea = false;
                    continue;
                }

                $n_fila++;

                if ($n_fila < $this->primera_fila) continue;

                $this->log($l, PHP_EOL . $n_fila, $n_fila);
                if ($n_fila > $this->limite) {
                    $error_msj = 'Límite sobrepasado.';
                    $this->errores[$n_fila] = $error_msj;
                    $this->log($l, $error_msj, $n_fila);
                    $this->resultado[] = ['Invalido'];
                    $error = true;
                    break;
                }

                $item = [];
                $error_item = false;

                $n_columnas_encontradas = count($valores);

                $primero = true;
                if ($n_columnas_encontradas >= $n_columnas) {

                    //ignora valores de columnas adicionales
                    if ($n_columnas_encontradas > $n_columnas) {
                        $valores = array_slice($valores, 0, $n_columnas, true);
                    }

                    foreach ($this->columnas as $columna => $titulo) {

                        if (is_int($columna)) $columna = $titulo;

                        $val = $primero ? reset($valores) : next($valores); $primero = false;
                        $val = trim(preg_replace('!\s+!', ' ', $val));
                        $val = utf8_encode($val);
                        //if (empty($val) || $val == 'NULL' || $val == 'N/A') continue;

                        if (isset($this->validaciones_columnas[$columna])) {
                            if (is_callable($this->validaciones_columnas[$columna])) {
                                $item[$columna] = $this->validaciones_columnas[$columna]($val);
                                if ($item[$columna] === false) {
                                    $error_msj = 'Error en campo "' . $columna . '" (' . $val . ')';
                                    $this->log($l, $error_msj, $n_fila);
                                    $this->errores[$n_fila] = $error_msj;
                                    $error = true;
                                    $error_item = true;
                                }
                            }
                            else {
                                if (Validator::make([$columna => $val], [$columna => $this->validaciones_columnas[$columna]])->passes()) {
                                    $item[$columna] = $val;
                                }
                                else {
                                    $error_msj = 'Error en campo "' . $columna . '" (' . $val . ')';
                                    $this->log($l, $error_msj, $n_fila);
                                    $this->errores[$n_fila] = $error_msj;
                                    $error = true;
                                    $error_item = true;
                                }
                            }
                        }
                        else {
                            $item[$columna] = $val;
                        }
                    }

                    //si se especifica una función para verificar el ítem
                    $fn_proceso_verificar = $this->proceso_verificar;
                    if (!$error_item && is_callable($fn_proceso_verificar)) {
                        $verificacion = $fn_proceso_verificar($item, $valores_fila_anterior, $registros_a_guardar);
                        if ($verificacion === false || (isset($verificacion['ok']) && !$verificacion['ok'])) {
                            $error_msj = isset($verificacion['error']) ? $verificacion['error'] : 'Error';
                            $this->log($l, $error_msj, $n_fila);
                            $this->errores[$n_fila] = $error_msj;
                            $error = true;
                            $error_item = true;
                        }
                    }

                    if ($modo == self::MODO_GUARDAR || $modo == self::MODO_LEER_Y_GUARDAR) {
                        if (!$error_item) {
                            $registros_a_guardar[] = array_merge($item, [
                                'fecha_creacion' => $fecha_actual,
                                'fecha_actualizacion' => $fecha_actual,
                                'id_usuario' => $id_usuario
                            ]);

                            $n_registros_a_guardar++;

                            if ($n_registros_a_guardar == 30) { //guarda cada 30
                                $this->guardar($registros_a_guardar);
                                $registros_a_guardar = [];
                                $n_registros_a_guardar = 0;
                            }
                        }
                    }
                    elseif (is_callable($fn_proceso_verificar)) {
                        if (!$error_item) {
                            $registros_a_guardar[] = array_merge($item, [
                                'fecha_creacion' => $fecha_actual,
                                'fecha_actualizacion' => $fecha_actual,
                                'id_usuario' => $id_usuario
                            ]);
                        }
                    }

                    if ($modo != self::MODO_GUARDAR) {
                        $resultado[] = $item;
                    }

                    if (!$error_item) {
                        $valores_fila_anterior = $item;
                        $n_total_cargados++;
                    }
                }
                else {
                    $error_msj = "Cantidad de valores no coincide. Se leyeron {$n_columnas_encontradas}; se esperaban {$n_columnas}";
                    $this->log($l, $error_msj, $n_fila);
                    $this->errores[$n_fila] = $error_msj;
                    $resultado[] = ['Invalido'];
                    $error = true;
                }
            }

            if (($modo == self::MODO_GUARDAR || $modo == self::MODO_LEER_Y_GUARDAR) && count($registros_a_guardar)) {
                $this->guardar($registros_a_guardar);
            }

            fclose($f);
            fclose($l);
        }
        else {
            $error_msj = 'No se pudo leer el archivo.';
            $this->log($l, $error_msj, $n_fila);
            $this->errores[1] = $error_msj;
            $this->resultado[] = ['Invalido'];
            $error = true;
            fclose($l);
        }

        $this->resultado = $resultado;

        $this->total_leidos = $n_fila;
        $this->total_cargados = $n_total_cargados;

        return !$error;
    }


    private function leerExcel($modo = self::MODO_LEER) {
        $archivo = is_file($this->archivo) ? $this->archivo : (!empty($this->archivo) ? $this->archivo : null);

        $error = false;
        $n_fila = 0;
        $n_total_cargados = 0;

        $resultado = [];

        if ($archivo) {
            $data = Excel::load($archivo, function($reader) {});

            $filas = $data->all();

            if (!count($this->columnas)) {
                $this->columnas = $data->first()->keys()->toArray();
            }

            $n_columnas = count($this->columnas);
            $n_registros_a_guardar = 0;
            $registros_a_guardar = [];

            $fecha_actual = date('Y-m-d H:i:s');
            $id_usuario = Auth::user()->id;

            $valores_fila_anterior = null;

            $l = fopen('log_' . $this->nombre . '.txt', 'w');

            foreach ($filas as $fila) {
                $n_fila++;

                if ($n_fila < $this->primera_fila) continue;

                $valores = [];
                foreach ($fila as /*$campo => */$valor) {
                    $valores[] = $valor;
                }

                $this->log($l, PHP_EOL . $n_fila, $n_fila);
                if ($n_fila > $this->limite) {
                    $error_msj = 'Límite sobrepasado.';
                    $this->errores[$n_fila] = $error_msj;
                    $this->log($l, $error_msj, $n_fila);
                    $this->resultado[] = ['Invalido'];
                    $error = true;
                    break;
                }

                $item = [];
                $error_item = false;

                $n_columnas_encontradas = count($valores);

                $primero = true;
                if ($n_columnas_encontradas >= $n_columnas) {

                    //ignora valores de columnas adicionales
                    if ($n_columnas_encontradas > $n_columnas) {
                        $valores = array_slice($valores, 0, $n_columnas, true);
                    }

                    foreach ($this->columnas as $columna => $titulo) {

                        if (is_int($columna)) $columna = $titulo;

                        $val = $primero ? reset($valores) : next($valores); $primero = false;
                        $val = trim(preg_replace('!\s+!', ' ', $val));
                        //$val = utf8_encode($val);
                        //if (empty($val) || $val == 'NULL' || $val == 'N/A') continue;

                        if (isset($this->validaciones_columnas[$columna])) {
                            if (is_callable($this->validaciones_columnas[$columna])) {
                                $item[$columna] = $this->validaciones_columnas[$columna]($val);
                                if ($item[$columna] === false) {
                                    $error_msj = 'Error en campo "' . $columna . '" (' . $val . ')';
                                    $this->log($l, $error_msj, $n_fila);
                                    $this->errores[$n_fila] = $error_msj;
                                    $error = true;
                                    $error_item = true;
                                }
                            }
                            else {
                                if (Validator::make([$columna => $val], [$columna => $this->validaciones_columnas[$columna]])->passes()) {
                                    $item[$columna] = $val;
                                }
                                else {
                                    $error_msj = 'Error en campo "' . $columna . '" (' . $val . ')';
                                    $this->log($l, $error_msj, $n_fila);
                                    $this->errores[$n_fila] = $error_msj;
                                    $error = true;
                                    $error_item = true;
                                }
                            }
                        }
                        else {
                            $item[$columna] = $val;
                        }
                    }

                    //si se especifica una función para verificar el ítem
                    $fn_proceso_verificar = $this->proceso_verificar;
                    if (!$error_item && is_callable($fn_proceso_verificar)) {
                        $verificacion = $fn_proceso_verificar($item, $valores_fila_anterior, $registros_a_guardar);
                        if ($verificacion === false || (isset($verificacion['ok']) && !$verificacion['ok'])) {
                            $error_msj = isset($verificacion['error']) ? $verificacion['error'] : 'Error';
                            $this->log($l, $error_msj, $n_fila);
                            $this->errores[$n_fila] = $error_msj;
                            $error = true;
                            $error_item = true;
                        }
                    }

                    if ($modo == self::MODO_GUARDAR || $modo == self::MODO_LEER_Y_GUARDAR) {
                        if (!$error_item) {
                            $registros_a_guardar[] = array_merge($item, [
                                'fecha_creacion' => $fecha_actual,
                                'fecha_actualizacion' => $fecha_actual,
                                'id_usuario' => $id_usuario
                            ]);

                            $n_registros_a_guardar++;

                            if ($n_registros_a_guardar == 30) { //guarda cada 30
                                $this->guardar($registros_a_guardar);
                                $registros_a_guardar = [];
                                $n_registros_a_guardar = 0;
                            }
                        }
                    }
                    elseif (is_callable($fn_proceso_verificar)) {
                        if (!$error_item) {
                            $registros_a_guardar[] = array_merge($item, [
                                'fecha_creacion' => $fecha_actual,
                                'fecha_actualizacion' => $fecha_actual,
                                'id_usuario' => $id_usuario
                            ]);
                        }
                    }

                    if ($modo != self::MODO_GUARDAR) {
                        $resultado[] = $item;
                    }

                    if (!$error_item) {
                        $valores_fila_anterior = $item;
                        $n_total_cargados++;
                    }
                }
                else {
                    $error_msj = "Cantidad de valores no coincide. Se leyeron {$n_columnas_encontradas}; se esperaban {$n_columnas}";
                    $this->log($l, $error_msj, $n_fila);
                    $this->errores[$n_fila] = $error_msj;
                    $resultado[] = ['Invalido'];
                    $error = true;
                }
            }

            if (($modo == self::MODO_GUARDAR || $modo == self::MODO_LEER_Y_GUARDAR) && count($registros_a_guardar)) {
                $this->guardar($registros_a_guardar);
            }

            fclose($l);
        }

        $this->resultado = $resultado;

        $this->total_leidos = $n_fila;
        $this->total_cargados = $n_total_cargados;

        return !$error;
    }


    public function resultado($mostrar_solo_errores = false) {
        $resultado = $this->resultado;

        switch ($this->formato_resultado) {
            case self::FORMATO_HTML:
                $tabla = '<table class="table basic-table"><thead><tr><th>#</th>';
                foreach ($this->columnas as $columna) {
                    $tabla .= '<th>' . $columna . '</th>';
                }
                $tabla .= '</tr></thead><tbody>';

                $n = 1;

                //por cada registro leido
                foreach ($resultado as $fila) {
                    if (!$mostrar_solo_errores || isset($this->errores[$n])) {
                        $tabla .= '<tr><td class="text-muted">' . $n . '</td>';

                        //por cada columna
                        foreach (array_keys($this->columnas) as $columna) {

                            //si no hay errores para la fila
                            if (!isset($this->errores[$n])) {
                                $valor_raw = $valor = isset($fila[$columna]) ? $fila[$columna] : '?';

                                //aplica el formato a la columna si se ha especificado la función correspondiente
                                if (isset($this->formatos_columnas[$columna]) && is_callable($this->formatos_columnas[$columna])) {
                                    $valor = $this->formatos_columnas[$columna]($valor);
                                }
                                $tabla .= '<td title="' . $valor_raw . '">' . $valor . '</td>';
                            }
                            else {
                                $tabla .= '<td class="error text-white bg-danger" colspan="' . count($this->columnas) . '"><i class="fa fa-fw fa-exclamation"></i> ' . $this->errores[$n] . '</td>';
                                break;
                            }
                        }
                        $tabla .= '</tr>';
                    }
                    $n++;
                }

                $tabla .= '</tbody></table>';

                return <<<EOT
                <div>
                    <div style="padding:20px; text-align:center;">
                        Total registros encontrados: <strong>{$this->total_leidos}</strong> &nbsp;&nbsp; Total sin errores: <strong>{$this->total_cargados}</strong>
                    </div>
                    {$tabla}
                </div>
EOT;

            default:
                //aplica los formatos por columnas si se han especificado las funciones correspondientes
                if (is_array($this->formatos_columnas) && count($this->formatos_columnas)) {
                    foreach ($resultado as $key_fila => $fila) {
                        foreach ($fila as $columna => $valor) {
                            if (isset($this->formatos_columnas[$columna]) && is_callable($this->formatos_columnas[$columna])) {
                                $resultado[$key_fila][$columna] = $this->formatos_columnas[$columna]($valor);
                            }
                        }
                    }
                }
                return $resultado;
        }
    }


    private function guardar($registros) {
        if (empty($this->modelo)) {
            $modelo = '\App\\' . ucfirst(Str::camel($this->nombre));
        } else {
            $modelo = '\App\\' . $this->modelo;
        }

        if (!empty($modelo)) {
            if (count($registros)) {
                if (is_callable($this->proceso_guardar)) {
                    $fn_proceso_guardar = $this->proceso_guardar;
                    foreach ($registros as $key => $registro) {
                        $registros[$key] = $fn_proceso_guardar($registro);
                        if (empty($registros[$key])) { //si se retorna falso o nulo, se elimina de la lista a guardar
                            unset($registros[$key]);
                        }
                    }
                }
                if (count($registros)) {
                    if (class_exists($modelo)) {
                        $modelo::insert($registros);
                    }
                }
                return true;
            }
        }
        return false;
    }


    private function log($l, $msg, $n_rows, $is_line = false) {
        if ($this->log) {
            if ($is_line) {
                fwrite($l, $n_rows . ',' . $msg . PHP_EOL);
            } else {
                fwrite($l, $msg . ' - ');
            }
        }
    }


    private function extension() {
        if (!is_string($this->archivo) && is_file($this->archivo)) {
            $extension = $this->archivo->getClientOriginalExtension();
        }
        else {
            $extension = pathinfo($this->archivo, PATHINFO_EXTENSION);
        }
        return strtolower($extension);
    }

}