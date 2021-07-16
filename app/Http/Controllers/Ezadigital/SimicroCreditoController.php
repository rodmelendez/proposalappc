<?php
/**
 * Created by PhpStorm.
 * User: Alfredo Fleming
 * Date: 12/11/2019
 * Time: 4:27 PM
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

class SimicroCreditoController extends \App\Http\Controllers\Controlador {

    protected $modelo = 'Proveedor';

    public $subdirectorio = 'Ezadigital';


    public function antesDeGuardar() {
        $this->antesDeGuardarDefecto();

        /*if ($empleado = Empleado::deUsuario()) {
            Input::merge([
                'id_empresa' => $empleado->id_empresa,
            ]);
        }*/

        return true;
    }


    public function antesDeGuardarDefecto() {

    }


    public function despuesDeGuardar($item) {
        //ImagenController::subirImagenParaItem($item, 'foto');
    }


    /*public function itemDataAdicional($item) {

    }*/


    public function importarPost() {
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
                'id_imf' => 'ID_IMF',
                'metodologia' => 'METODOLOGIA',
                'cod_anterior' => 'COD_ANTERIOR',
                'ciclo' => 'CICLO',
                'fondo' => 'FONDO',
                'sucursal' => 'SUCURSAL',
                'nombre_cliente' => 'NOMBRE_CLIENTE',
                'producto_crediticio' => 'PRODUCTO_CREDITICIO',
                'sector_economico' => 'SECTOR_ECONOMICO',
                'rango_credito' => 'RANGO_CREDITO',
                'monto_entregado' => 'MONTO_ENTREGADO', //mon_desembolsado
                'saldo_principal' => 'SALDO_PRINCIPAL', //mon_saldo
                'mon_saldo_mas_deslizamiento' => 'MON_SALDO_MAS_DESLIZAMIENTO',
                'fecha_desembolso' => 'FECHA_DESEMBOLSO',
                'fecha_vencimiento' => 'FECHA_VENCIMIENTO',
                'actividad_economica' => 'ACTIVIDAD_ECONOMICA',
                'id_ejecutivo' => 'ID_EJECUTIVO',
                'asesor_credito' => 'ASESOR_CREDITO', //nom_usuario
                'cant_hombres' => 'CANT_HOMBRES',
                'cant_mujeres' => 'CANT_MUJERES',
                'total_clientes' => 'TOTAL_CLIENTES',
                'codigo_cliente' => 'CODIGO_CLIENTE', //sec_cliente
                'codigo_credito' => 'CODIGO_CREDITO', //sec_credito
                'monto_desembolsado_hombres' => 'MONTO_DESEMBOLSADO_HOMBRES',
                'monto_desembolsado_mujeres' => 'MONTO_DESEMBOLSADO_MUJERES',
                'monto_desembolsado_total' => 'MONTO_DESEMBOLSADO_TOTAL', //++++++++++
                'principal_mujeres' => 'PRINCIPAL_MUJERES', //mon_saldo_f
                'principal_hombres' => 'PRINCIPAL_HOMBRES', //mon_saldo_m
                'monto_saldo_total' => 'MONTO_SALDO_TOTAL', //++++++++++
                'gasto_legales' => 'GASTO_LEGALES',
                'tasa_interes' => 'TASA_INTERES',
                'tasa_interes_complementario' => 'TASA_INTERES_COMPLEMENTARIO',
                'tasa_monetaria' => 'TASA_MONETARIA', //tasa_mora
                'plazo_credito' => 'PLAZO_CREDITO',
                'periodicidad' => 'PERIODICIDAD', //desc_per_cuota
                'rango_plazos' => 'RANGO_PLAZOS',
                'rango_ciclos' => 'RANGO_CICLOS',
                'rango_mora' => 'RANGO_MORA',
                'rango_mora2' => 'RANGO_MORA 2', //+++++++++++
                'dias_en_mora' => 'DIAS_EN_MORA',
                'desc_tipo_plazo' => 'DESC_TIPO_PLAZO', //+++++++++
                'tipo_cambio' => 'TIPO_CAMBIO',
                'fecha_sistema' => 'FECHA_SISTEMA', //fec_cierre
                'total_cargos_administrativos' => 'TOTAL_CARGOS_ADMINISTRATIVOS',
                'total_cobranza' => 'TOTAL_COBRANZA',
                'total_comision' => 'TOTAL_COMISION',
                'total_deslizado' => 'TOTAL_DESLIZADO',
                'total_int_corriente_pagado' => 'TOTAL_INT_CORRIENTE_PAGADO',
                'total_int_mora_acumulado' => 'TOTAL_INT_MORA_ACUMULADO',
                'total_pagado_principal' => 'TOTAL_PAGADO_PRINCIPAL',
                'total_pagado_int_complementario' => 'TOTAL_PAGADO_INT_COMPLEMENTARIO',
                'total_cargos_administrativos_hombres' => 'TOTAL_CARGOS_ADMINISTRATIVOS_HOMBRES',
                'total_cargos_administrativos_mujeres' => 'TOTAL_CARGOS_ADMINISTRATIVOS_MUJERES',
                'total_cobranza_hombres' => 'TOTAL_COBRANZA_HOMBRES',
                'total_cobranza_mujeres' => 'TOTAL_COBRANZA_MUJERES',
                'total_comision_hombres' => 'TOTAL_COMISION_HOMBRES',
                'total_comision_mujeres' => 'TOTAL_COMISION_MUJERES',
                'total_deslizado_hombres' => 'TOTAL_DESLIZADO_HOMBRES',
                'total_deslizado_mujeres' => 'TOTAL_DESLIZADO_MUJERES',
                'total_interes_hombres' => 'TOTAL_INTERES_HOMBRES',
                'total_interes_mujeres' => 'TOTAL_INTERES_MUJERES',
                'total_int_mora_hombres_acumulado' => 'TOTAL_INT_MORA_HOMBRES_ACUMULADO',
                'total_int_mora_mujeres_acumulado' => 'TOTAL_INT_MORA_MUJERES_ACUMULADO',
                'total_principal_hombres' => 'TOTAL_PRINCIPAL_HOMBRES',
                'total_principal_mujeres' => 'TOTAL_PRINCIPAL_MUJERES',
                'total_int_complementario_hombres' => 'TOTAL_INT_COMPLEMENTARIO_HOMBRES',
                'total_int_complementario_mujeres' => 'TOTAL_INT_COMPLEMENTARIO_MUJERES',
                'monto_garantia_fiduciaria' => 'MONTO_GARANTIA_FIDUCIARIA',
                'monto_garantia_hipotecaria' => 'MONTO_GARANTIA_HIPOTECARIA',
                'monto_garantia_prendaria' => 'MONTO_GARANTIA_PRENDARIA',
                'monto_garantia_bancaria' => 'MONTO_GARANTIA_BANCARIA',
                'cant_total' => 'CANT_TOTAL', //++++++++
                'monto_total_vencido' => 'MONTO_TOTAL_VENCIDO',
                'monto_interes' => 'MONTO_INTERES',
                'monto_mora' => 'MONTO_MORA',
                'monto_deslizado' => 'MONTO_DESLIZADO',
                'monto_desembolsado' => 'MONTO_DESEMBOLSADO',
                'fecha_ult_pago_principal' => 'FECHA_ULT_PAGO_PRINCIPAL',
                'fecha_primer_pago_principal' => 'FECHA_PRIMER_PAGO_PRINCIPAL',
                'estado' => 'ESTADO',
                'direccion_negocio' => 'DIRECCION_NEGOCIO',
                'id_metodo' => 'ID_METODO', //+++++++
                'cantidad_cuotas_mora' => 'CANTIDAD_CUOTAS_MORA',
                'id_sucursal' => 'ID_SUCURSAL', //++++++++
                'id_moneda' => 'ID_MONEDA',
                'num_id_cedula' => 'NUM_ID_CEDULA',
                'moneda' => 'MONEDA',
                'id_sucursal_usuario' => 'ID_SUCURSAL_USUARIO',
                'id_usuario' => 'ID_USUARIO', //+++++++
                'mon_desembolsado_grupo' => 'MON_DESEMBOLSADO_GRUPO',
                'mon_saldo_grupo' => 'MON_SALDO_GRUPO',
                'nom_cliente_grupo' => 'NOM_CLIENTE_GRUPO',
                'num_linea' => 'NUM_LINEA',
                'desc_segregacion' => 'DESC_SEGREGACION',
                'mon_int_pagado' => 'MON_INT_PAGADO',
                'mon_mora_pagada' => 'MON_MORA_PAGADA',
                'mon_desli_pagado' => 'MON_DESLI_PAGADO',
                'mon_tic_pagado' => 'MON_TIC_PAGADO',
                'mon_cobranza_pagado' => 'MON_COBRANZA_PAGADO',
                'mon_cargo_pagado' => 'MON_CARGO_PAGADO',
                'mon_pagado_principal' => 'MON_PAGADO_PRINCIPAL',
                'barrio_negocio' => 'BARRIO_NEGOCIO',
                'municipio_negocio' => 'MUNICIPIO_NEGOCIO',
                'mon_cobranza' => 'MON_COBRANZA',
                'dir_sucursal' => 'DIR_SUCURSAL',
                'desc_sexo' => 'DESC_SEXO',
                'mon_cuota_pendiente' => 'MON_CUOTA_PENDIENTE',
                'observaciones' => 'OBSERVACIONES',
                'desc_actividad' => 'DESC_ACTIVIDAD',
                'telefono' => 'TELEFONO',
                'desc_convenio' => 'DESC_CONVENIO',
                'departamento_negocio' => 'DEPARTAMENTO_NEGOCIO',
                'departamento_domicilio' => 'DEPARTAMENTO_DOMICILIO',
                'municipio_domicilio' => 'MUNICIPIO_DOMICILIO',
                'barrio_domicilio' => 'BARRIO_DOMICILIO',
                'direccion_domicilio' => 'DIRECCION_DOMICILIO',
                'id_tipo_linea' => 'ID_TIPO_LINEA',
                'desc_tipo_linea' => 'DESC_TIPO_LINEA',
                'nom_ejecutivo_origen' => 'NOM_EJECUTIVO_ORIGEN',
                'nom_ejecutivo_destino' => 'NOM_EJECUTIVO_DESTINO',
                'monto_seguro' => 'MONTO_SEGURO',
                'porcentaje_comision_desembolso' => 'PORCENTAJE_COMISION_DESEMBOLSO',
                'monto_comision_desembolso' => 'MONTO_COMISION_DESEMBOLSO',
                'porcentaje_gastos_legales' => 'PORCENTAJE_GASTOS_LEGALES',
                'monto_gastos_legales' => 'MONTO_GASTOS_LEGALES',
                'on_otorgcre' => 'ON_OTORGCRE',
                'tipo_cambio_desembolso' => 'TIPO_CAMBIO_DESEMBOLSO',
                'fec_ult_pago' => 'FEC_ULT_PAGO',
                'id_estado' => 'ID_ESTADO', //++++++
                'id_calificacion' => 'ID_CALIFICACION', //++++++
                'sec_credito_ant' => 'SEC_CREDITO_ANT', //++++++
                'mantenimiento_valor' => 'MANTENIMIENTO_VALOR', //++++++
                'interes' => 'INTERES', //++++++
                'mora' => 'MORA', //++++++
                'seguro' => 'SEGURO', //++++++
                'total_adeudado' => 'TOTAL_ADEUDADO', //++++++
                'mon_al_dia' => 'MON_AL_DIA', //++++++
                'cuotas_pagadas' => 'CUOTAS_PAGADAS', //++++++
                'cuotas_mora' => 'CUOTAS_MORA', //++++++
                'cuotas_pendientes' => 'CUOTAS_PENDIENTES', //++++++
                'tipo_cambio_hoy' => 'TIPO_CAMBIO_HOY', //++++++
                'ind_gerencia' => 'IND_GERENCIA', //++++++
            ];

            $importar = new ImportarController;

            $importar->archivo = $archivo;
            $importar->columnas = $cabecera;
            $importar->nombre = 'simicro_credito';
            $importar->modelo = 'SimicroCredito';
            $importar->formato_resultado = ImportarController::FORMATO_HTML;
            $importar->limite = 5000;

            if ($vaciar) {
                $modelo = '\App\\Ezadigital\\SimicroCredito';
                $modelo::where('id', '>', 0)->forceDelete();
            }

            //validaciones
            /*$importar->validaciones_columnas = [
                'nombre' => 'required',
                'abreviatura' => 'required',
            ];*/

            $usuarios = \App\UsuarioTouchId
                ::whereNotNull('ejecutivo_simicro')
                ->get([
                    'id_usuario',
                    'ejecutivo_simicro',
                ])
                ->pluck('id_usuario', 'ejecutivo_simicro')
                ->toArray();

            $usuarios_sistema = User::get(['id', 'nombre']);
            foreach ($usuarios_sistema as $usuario_sistema) {
                if (!in_array($usuario_sistema->id, $usuarios)) {
                    $usuarios[explode('@', strtolower($usuario_sistema->nombre))[0]] = $usuario_sistema->id;
                }
            }

            //verificación
            $importar->proceso_verificar = function(&$item) use ($cabecera, $usuarios) {
                return self::verificarImportar($item, $cabecera, $usuarios);
            };

            //al guardar
            $importar->proceso_guardar = function($item) use ($usuarios) {
                //return self::guardarProductoImportar($item);

                /*$id_usuarios = [];

                if (!empty($item['id_ejecutivo']) && isset($id_usuarios[$item['id_ejecutivo']])) {
                    $id_usuario = $id_usuarios[$item['id_ejecutivo']];
                } else {
                    $id_usuario = self::idUsuarioDesdeNombreUsuario($item['id_ejecutivo']);
                    $id_usuarios[$item['id_ejecutivo']] = $id_usuario;
                }*/
                
                /*$id_usuario = $item['id_ejecutivo'] ? ($usuarios[$item['id_ejecutivo']] ?? null) : null;
                if (!$id_usuario) {
                    $id_usuario = self::idUsuarioDesdeNombreUsuario($item['id_ejecutivo']);
                }*/

                $item['id_usuario'] = !empty($item['id_usuario_final']) ? $item['id_usuario_final'] : null;

                SimicroCredito::create($item);

                return false;
            };

            //al previsualizar
            if ($precargar) {
                $importar->leer(ImportarController::MODO_LEER);
                $resultado = $importar->resultado(Input::get('mostrar_solo_errores', false));
            }
            //al guardar
            else {
                $importar->leer(ImportarController::MODO_GUARDAR);
                $resultado = '';
            }

            $this->especificarRespuesta('precargar', $precargar);
            $this->especificarRespuesta('resultado', $resultado);
            $this->especificarRespuesta('total_leidos', $importar->total_leidos);
            $this->especificarRespuesta('total_cargados', $importar->total_cargados);

            return $this->retornar(!$precargar ? __('global.saved_msg') : null);
        }

        return $this->retornar();
    }


    private static function verificarImportar(&$item, $cabeceras, $usuarios = []) {
        //fechas
        /*$item['fecha_desembolso'] = Funciones::formatoFechaSistema($item['fecha_desembolso']);
        $item['fecha_vencimiento'] = Funciones::formatoFechaSistema($item['fecha_vencimiento']);
        $item['fecha_sistema'] = Funciones::formatoFechaSistema($item['fecha_sistema']);
        $item['fecha_ult_pago_principal'] = Funciones::formatoFechaSistema($item['fecha_ult_pago_principal']);
        $item['fecha_primer_pago_principal'] = Funciones::formatoFechaSistema($item['fecha_primer_pago_principal']);*/

        foreach ($cabeceras as $cabecera => $desc) {
            if (substr($cabecera, 0, 6) == 'monto_'
                || substr($cabecera, 0, 4) == 'mon_'
                || substr($cabecera, 0, 6) == 'saldo_'
                || substr($cabecera, 0, 5) == 'tasa_'
                || (
                    substr($cabecera, 0, 6) == 'total_'
                    && substr($cabecera, 0, 14) != 'total_clientes'
                    && substr($cabecera, 0, 12) != 'total_cargos'
                   )
                || substr($cabecera, 0, 11) == 'porcentaje_'
                || substr($cabecera, 0, 6) == 'gasto_'
                || substr($cabecera, 0, 11) == 'tipo_cambio'
                || substr($cabecera, 0, 15) == 'tipo_cambio_hoy'
                || substr($cabecera, 0, 13) == 'plazo_credito'
                || substr($cabecera, 0, 15) == 'sec_credito_ant'
                || substr($cabecera, 0, 19) == 'mantenimiento_valor'
                || substr($cabecera, 0, 7) == 'interes'
                || substr($cabecera, 0, 4) == 'mora'
                || substr($cabecera, 0, 6) == 'seguro'
            )
            {
                $item[$cabecera] = round((float)Funciones::decimalsForSystem($item[$cabecera], 4), 4);
            }
            elseif (substr($cabecera, 0, 6) == 'fecha_'
                || substr($cabecera, 0, 4) == 'fec_'
            )
            {
                $item[$cabecera] = Funciones::formatoFechaSistema(explode(' ', $item[$cabecera])[0]);
            }
            elseif (substr($cabecera, 0, 5) == 'cant_'
                || substr($cabecera, 0, 9) == 'cantidad_'
                || (substr($cabecera, 0, 4) == 'num_' && $cabecera != 'num_id_cedula')
                || substr($cabecera, 0, 5) == 'ciclo'
                || substr($cabecera, 0, 6) == 'total_'
                || substr($cabecera, 0, 5) == 'dias_'
                || substr($cabecera, 0, 7) == 'cuotas_'
                || (substr($cabecera, 0, 3) == 'id_' && $cabecera != 'id_ejecutivo' && $cabecera != 'id_usuario' && $cabecera != 'id_estado' && $cabecera != 'id_calificacion')
            ) {
                $item[$cabecera] = (int)$item[$cabecera];
            }
        }

        //id_usuario
        $id_ejecutivo = $item['id_ejecutivo'] ?? '';
        
        //cuando el id_ejecutivo tiene un '-', ej: 'CH-SLOPEZ'
        $id_ejecutivo_parte = explode('-', $id_ejecutivo);
        if (count($id_ejecutivo_parte) == 2) {
            $id_ejecutivo = $id_ejecutivo_parte[1];
        }

        $id_ejecutivo = mb_strtolower($id_ejecutivo);

        if (!isset($usuarios[$id_ejecutivo]) && strpos($id_ejecutivo, 'ñ') !== FALSE) {
            $id_ejecutivo = str_replace('ñ', 'n', $id_ejecutivo);
        }

        if (!$id_ejecutivo || !isset($usuarios[$id_ejecutivo])) {
            return [
                'ok' => false,
                'error' => 'ID_EJECUTIVO (' . $id_ejecutivo . ') no relacionado con usuario',
            ];
        }

        $item['id_usuario_final'] = $usuarios[$id_ejecutivo];

        return true;
    }


    private static function idUsuarioDesdeNombre($valor) {
        $nombres = explode(' ', $valor);

        $persona = Persona::take(2);
        $buscado = false;

        foreach ($nombres as $nombre) {
            $nombre = trim($nombre);
            if (!empty($nombre)) {
                $persona = $persona->where(function($q) use ($nombre) {
                    $q->whereRaw('sin_acento(primer_nombre) ILIKE sin_acento(\'%' . $nombre . '%\')');
                    $q->orWhereRaw('sin_acento(segundo_nombre) ILIKE sin_acento(\'%' . $nombre . '%\')');
                    $q->orWhereRaw('sin_acento(primer_apellido) ILIKE sin_acento(\'%' . $nombre . '%\')');
                    $q->orWhereRaw('sin_acento(segundo_apellido) ILIKE sin_acento(\'%' . $nombre . '%\')');
                });
            }
            $buscado = true;
        }

        if (!$buscado) return null;

        $persona = $persona->get();

        $match = null;

        foreach ($persona as $p) {
            if ($match === null) {
                $match = $p;
            } else {
                return null;
            }
        }

        return $match ? $match->id_usuario : null;
    }

    private static function idUsuarioDesdeNombreUsuario($valor) {
        if (empty($valor)) return null;

        $usuario = User::whereRaw("nombre iLIKE '{$valor}%'")->get();

        $match = null;

        foreach ($usuario as $u) {
            if ($match === null) {
                $match = $u;
            } else {
                return null;
            }
        }

        return $match ? $match->id : null;
    }


    private static function idUsuarioDesdeTouchId($valor) {
        $item = \App\UsuarioTouchId
            ::where('ejecutivo_simicro', '=', $valor)
            ->first([
                'id_usuario'
            ]);

        if (!$item) return null;

        if (!$usuario = User::find((int)$item->id_usuario)) {
            return null;
        }

        return $usuario->id;
    }


    public function vincularIdUsuarioIdEjecutivo() {
        $items = SimicroCredito::get();

        foreach ($items as $item) {
            $id_usuario = self::idUsuarioDesdeNombreUsuario($item->id_ejecutivo);
            $item->id_usuario = $id_usuario;
            $item->save();
            usleep(5);
        }

        return 'Fertig.';
    }
}