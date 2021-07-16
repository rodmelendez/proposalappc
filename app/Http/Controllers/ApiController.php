<?php
/**
 * Created by PhpStorm.
 * User: Alfredo
 * Date: 26-Jan-18
 * Time: 11:21 AM
 */

namespace App\Http\Controllers;

use App\Ezadigital\SimicroCredito;
use App\Funciones;
use App\GaleriaCredito;
use App\GaleriaCreditoCliente;
use App\GaleriaCreditoItem;
use App\Empresa;
use App\Empleado;
use App\Persona;
use App\User;
use App\UsuarioTouchId;
use App\UsuarioUbicacion;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class ApiController
{
    /* EZA DIGITAL */
    public function carteraOficialGet($token = null)
    {
        if ($token === null) {
            $token = Input::get('token');
        }

        if (!($usuario = $this->buscarUsuarioPorToken($token))) {
            $id_ejecutivo = Input::get('id_ejecutivo');

            if (!empty($id_ejecutivo)) {
                $usuario = User::whereRaw("nombre iLIKE '{$id_ejecutivo}%'")->first();

                if (!$usuario) {
                    return $this->error('Usuario inválido.');
                }
            }

            if (!$usuario) {
                return $this->error('Token inválido.');
            }
        }

        $fecha_fin = Funciones::formatoFechaSistema(Input::get('fecha_hasta'));
        if (empty($fecha_fin)) {
            $fecha_fin = self::ultimaFechaDisponible();
        }
        $fecha_inicio = Funciones::formatoFechaSistema(Input::get('fecha_desde'));
        if (empty($fecha_inicio)) {
            $fecha_inicio = date('Y-m', strtotime($fecha_fin)) . '-01';
        }
        $id_ejecutivo = explode('@', $usuario->nombre)[0];
        $id_usuario = $usuario ? $usuario->id : null;

        $ind_gerencia = Input::get('ind_gerencia', false);
        $totalizar = (bool)$ind_gerencia;

        return [
            //'prueba_francisco'=> $ind_gerencia,
            'total_clientes' => $this->coTotalClientes($id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario, $totalizar),
            'monto_cartera_activa' => $this->coMontoCarteraActiva($id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario, $totalizar),//50525.25,
            'monto_desembolsado' => $this->coMontoDesembolsado($id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario, $totalizar),//125505.50,
            'monto_desembolsado_general' => $this->coMontoDesembolsado($id_ejecutivo, false, false, $id_usuario, $totalizar),
            'total_clientes_desembolsado' => $this->coTotalClientesDesembolsado($id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario, $totalizar),
            'credito_mora' => $this->coCreditoMora($id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario, $totalizar),
            'credito_mora_monto' => $this->coCreditoMoraTotal($id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario, $totalizar),

            'estado_cartera' => [
                'vigentes' => [
                    'cantidad' => $this->coCreditoEstado('VIGENTE', $id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario,$totalizar),
                    'total' => $this->coCreditoEstadoTotal('VIGENTE', $id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario,$totalizar),
                ],
                'vencidos' => [
                    'cantidad' => $this->coCreditoEstado('VENCIDO', $id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario,$totalizar),
                    'total' => $this->coCreditoEstadoTotal('VENCIDO', $id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario,$totalizar),
                ],
                // 'cancelados' => [
                //     'cantidad' => $this->coCreditoEstado('CANCELADO', $id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario,$totalizar),
                //     'total' => $this->coCreditoEstadoTotal('CANCELADO', $id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario,$totalizar),
                // ],
                'saneados' => [
                    'cantidad' => $this->coCreditoEstado('SANEADO', $id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario,$totalizar),
                    'total' => $this->coCreditoEstadoTotal('SANEADO', $id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario,$totalizar),
                ],
                'reestructurados' => [
                    'cantidad' => $this->coCreditoEstado('REESTRUCTURADO', $id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario,$totalizar),
                    'total' => $this->coCreditoEstadoTotal('REESTRUCTURADO', $id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario,$totalizar),
                ],
                'prorrogados' => [
                    'cantidad' => $this->coCreditoEstado('PRORROGADO', $id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario,$totalizar),
                    'total' => $this->coCreditoEstadoTotal('PRORROGADO', $id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario,$totalizar),
                ],
                //'judiciales' => $this->coCreditoEstado('JUDICIAL', $id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario),
            ],


            //'credito_vigente_monto' => $this->coCreditoEstadoTotal('VIGENTE', $id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario,$totalizar),
         //   'credito_vigente_conteo' => $this->coCreditoVigenteConteo($id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario, $totalizar),
            'mora_par1' => $this->coMoraPar1($id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario, $totalizar),
            'mora_par30' => $this->coMoraPar30($id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario, $totalizar),
            'tipo_cambio_hoy' => $this->coTipoCambioHoy($id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario, $totalizar),
            'fecha_carga' => $this->coFechaCarga($id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario, $totalizar),
        ];
    }

    public function ultimaFechaDisponible()
    {
        $fecha_carga = SimicroCredito::max('fecha_sistema');
        $hoy = date('Y-m-d');

        if ($hoy < $fecha_carga) {
            return $hoy;
        }

        return $fecha_carga;
    }

    public function carteraOficialSucursalGet($token = null)
    {
        if ($token === null) {
            $token = Input::get('token');
        }

        if (!($usuario = $this->buscarUsuarioPorToken($token))) {
            $id_ejecutivo = Input::get('id_ejecutivo');

            if (!empty($id_ejecutivo)) {
                $usuario = User::whereRaw("nombre iLIKE '{$id_ejecutivo}%'")->first();

                if (!$usuario) {
                    return $this->error('Usuario inválido.');
                }
            }

            if (!$usuario) {
                return $this->error('Token inválido.');
            }
        }

        $fecha_fin = Funciones::formatoFechaSistema(Input::get('fecha_hasta'));
        if (empty($fecha_fin)) {
            $fecha_fin = self::ultimaFechaDisponible();
        }
        $fecha_inicio = Funciones::formatoFechaSistema(Input::get('fecha_desde'));
        if (empty($fecha_inicio)) {
            $fecha_inicio = date('Y-m', strtotime($fecha_fin)) . '-01';
        }
        $id_ejecutivo = explode('@', $usuario->nombre)[0];
        $id_usuario = $usuario ? $usuario->id : null;

        $ind_gerencia = Input::get('ind_gerencia', false);
        $totalizar = (bool)$ind_gerencia;

        $id_sucursal = Input::get('id_sucursal');

        if (!$id_sucursal) {
            $id_sucursales = SimicroCredito
                ::selectRaw('DISTINCT id_sucursal, sucursal')
                ->get(['id_sucursal', 'sucursal'])
                ->pluck('sucursal', 'id_sucursal')
                ->toArray();

            if (!count($id_sucursales)) return [];
        }
        else {
            $nombre_sucursal = SimicroCredito
                ::where('id_sucursal', '=', $id_sucursal)
                ->first(['sucursal']);

            $nombre_sucursal = $nombre_sucursal ? $nombre_sucursal->sucursal : '';

            $id_sucursales = [
                $id_sucursal => $nombre_sucursal
            ];
        }

        $data_sucursal = [];

        foreach ($id_sucursales as $id_sucursal => $sucursal) {
            $data_sucursal[] = [
                'id_sucursal' => $id_sucursal,
                'sucursal' => $sucursal,
                'total_clientes' => $this->coTotalClientes($id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario, $totalizar, $id_sucursal),
                'monto_cartera_activa' => $this->coMontoCarteraActiva($id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario, $totalizar, $id_sucursal),//50525.25,
                'monto_desembolsado' => $this->coMontoDesembolsado($id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario, $totalizar, $id_sucursal),//125505.50,
                'monto_desembolsado_general' => $this->coMontoDesembolsado($id_ejecutivo, false, false, $id_usuario, $totalizar, $id_sucursal),
                'total_clientes_desembolsado' => $this->coTotalClientesDesembolsado($id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario, $totalizar, $id_sucursal),
                'credito_mora' => $this->coCreditoMora($id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario, $totalizar, $id_sucursal),
                'credito_mora_monto' => $this->coCreditoMoraTotal($id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario, $totalizar, $id_sucursal),
                'mora_par1' => $this->coMoraPar1($id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario, $totalizar, $id_sucursal),
                'mora_par30' => $this->coMoraPar30($id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario, $totalizar, $id_sucursal),
                'tipo_cambio_hoy' => $this->coTipoCambioHoy($id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario, $totalizar),
                'fecha_carga' => $this->coFechaCarga($id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario, $totalizar),
            ];
        }

        return $data_sucursal;
    }

    /**
     * token (string)
     * general (bit)                    'si es 1, no filtra por usuario
     * fecha_desde (YYYY-MM-DD)         'fecha mínima
     * fecha_hasta (YYYY-MM-DD)         'fecha máxima
     * busqueda (string)                'busca coincidencias en los campos: nombre_cliente, id_ejecutivo o estado
     * nombre_cliente (string)          'busca coincidencias en nombre_cliente
     * id_ejecutivo (string)            'busca coincidencias en id_ejecutivo
     * fecha_desembolso (YYYY-MM-DD)    'busca coincidencias en fecha_desembolso
     * estado (string)                  'busca coincidencias en estado (vigente, saneado, cancelado, ...)
     *
     * @param null $token
     * @return mixed
     */
    public function carteraOficialDetalleGet($token = null)
    {
        if ($token === null) {
            $token = Input::get('token');
        }

        if (!($usuario = $this->buscarUsuarioPorToken($token))) {
            $id_ejecutivo = Input::get('id_ejecutivo');

            if (!empty($id_ejecutivo)) {
                $usuario = User::whereRaw("nombre iLIKE '{$id_ejecutivo}%'")->first();

                if (!$usuario) {
                    return $this->error('Usuario inválido.');
                }
            }

            if (!$usuario) {
                return $this->error('Token inválido.');
            }
        }

        if ($token === 'alfredo') {
            dd(Input::all());
        }

        $lista = [];

        if (!intval(Input::get('general'))) {
            $items = SimicroCredito::where('id_usuario', '=', (int)$usuario->id);
        }
        else {
            $items = SimicroCredito::take(1000);
        }

        if (Input::get('fecha_desde')) {
            $items = $items->where('fecha_sistema', '>=', Input::get('fecha_desde')); //fecha_desembolso?
        }

        if (Input::get('fecha_hasta')) {
            $items = $items->where('fecha_sistema', '<=', Input::get('fecha_hasta')); //fecha_desembolso?
        }

        if ($busqueda = Input::get('busqueda')) {
            $items = $items->where(function($q) use ($busqueda) {
                $q->where('nombre_cliente', 'iLIKE', '%' . $busqueda . '%');
                $q->orWhere('id_ejecutivo', 'iLIKE', $busqueda);
                $q->orWhere('estado', 'iLIKE', $busqueda);
            });
        }

        if ($nombre_cliente = Input::get('nombre_cliente')) {
            $items = $items->where('nombre_cliente', 'iLIKE', '%' . $busqueda . '%');
        }

        if ($id_ejecutivo = Input::get('id_ejecutivo')) {
            $items = $items->where('id_ejecutivo', 'iLIKE', $busqueda);
        }

        if ($fecha_desembolso = Input::get('fecha_desembolso')) {
            $items = $items->where('fecha_desembolso', '=', $fecha_desembolso);
        }

        if ($estado = Input::get('estado')) {
            $items = $items->where('estado', 'iLIKE', $estado);
        }

        if (Input::get('_sql')) {
            \App\Funciones::qdd($items);
        }

        $items = $items->get();

        foreach ($items as $item) {
            $lista[] = [
                'codigo_cliente' => $item->codigo_cliente,
                'nombre_cliente' => $item->nombre_cliente,
                'codigo_credito' => $item->codigo_credito,
                'saldo_principal' => $item->saldo_principal,
                'mon_saldo_mas_deslizamiento' => $item->mon_saldo_mas_deslizamiento,
                'fecha_desembolso' => $item->fecha_desembolso,
                'fecha_vencimiento' => $item->fecha_vencimiento,
                'dias_en_mora' => $item->dias_en_mora,
                'estado' => $item->estado,
                'ciclo' => $item->ciclo,
                'sucursal' => $item->sucursal,
                'monto_entregado' => $item->monto_entregado,
                'plazo_credito' => $item->plazo_credito,
                'periodicidad' => $item->periodicidad,
                'total_pagado_principal' => $item->total_pagado_principal,
                'monto_mora' => $item->monto_mora,
                'fecha_ult_pago_principal' => $item->fecha_ult_pago_principal,
                'num_id_cedula' => $item->num_id_cedula,
                'moneda' => $item->moneda,
                'desc_sexo' => $item->desc_sexo,
                'mantenimiento_valor' => $item->mantenimiento_valor,
                'interes_mora' => $item->total_int_mora_acumulado,
                'seguro' => $item->seguro,
                'total_adeudado' => $item->total_adeudado,
                'mon_al_dia' => $item->mon_al_dia,
                'cuotas_pagadas' => $item->cuotas_pagadas,
                'cuotas_pendientes' => $item->cuotas_pendientes,
                'interes' => $item->interes,
                'cuotas_mora' => $item->cuotas_mora,
                'direccion_negocio' => $item->direccion_negocio,
                'direccion_domicilio' => $item->direccion_domicilio,
                'telefono' => $item->telefono,
                'ind_gerencia' => $item->ind_gerencia,
            ];
        };

        return $lista;
    }

    public function simularCreditoGet()
    {
        $tipo_ingreso = Input::get('tipo_ingreso'); //negocio, salario
        $monto_solicitado = (float)Input::get('monto_solicitado');
        $plazo = (int)Input::get('plazo');
        
        switch ($tipo_ingreso) {
            case 'negocio':
                $monto_solicitado = max(200, $monto_solicitado);
                $monto_solicitado = min(200000, $monto_solicitado);
                break;

            case 'salario':
                $monto_solicitado = max(200, $monto_solicitado);
                $monto_solicitado = min(5000, $monto_solicitado);
                break;
        }

        if ($monto_solicitado <= 5000) {
            $tasa_interes = 54;
            $porcentaje_gastos_legales = 2;
            $porcentaje_comision_desembolso = 0;
            $plazo_max = 18;
        }
        else if ($monto_solicitado <= 10000) {
            $tasa_interes = 46;
            $porcentaje_gastos_legales = 2;
            $porcentaje_comision_desembolso = 1;
            $plazo_max = 24;
        }
        else if ($monto_solicitado <= 20900) {
            $tasa_interes = 24;
            $porcentaje_gastos_legales = 3;
            $porcentaje_comision_desembolso = 1;
            $plazo_max = 24;
        }
        else {// if ($monto_solicitado <= 200000) {
            $tasa_interes = 24;
            $porcentaje_gastos_legales = 2;
            $porcentaje_comision_desembolso = 2;
            $plazo_max = 36;
        }

        if ($plazo > $plazo_max) $plazo = $plazo_max;

        //monto_credito
        {
            $comision_desembolso = $monto_solicitado * $porcentaje_comision_desembolso / 100;
            $gastos_legales = $monto_solicitado * $porcentaje_gastos_legales / 100;

            $monto_credito = $monto_solicitado + $comision_desembolso + $gastos_legales;
        }

        //calcuar_cuota
        {
            $credito = $monto_credito;
            $tasa = $tasa_interes / 100;

            $cuota = round(($credito / ((1-pow(1+($tasa*365/360/12), $plazo*-1)) / ($tasa*365/360/12))) * 1.0005, 2);
        }

        return [
            'monto_solicitado' => $monto_solicitado,
            'plazo' => $plazo,
            'cuota' => $cuota,
        ];
    }

    private function coTotalClientes($id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario = null, $totalizado = false, $id_sucursal = null) {
        $total = SimicroCredito
            //->where('fecha_desembolso', '>=', $fecha_inicio)
            //->where('fecha_desembolso', '<=', $fecha_fin)
            ::where('estado', '<>', 'SANEADO')
            ->where('estado','<>','CANCELADO')
            ->selectRaw('COUNT(DISTINCT codigo_credito) AS total'); //COUNT(DISTINCT codigo_cliente) AS total
        if (!$totalizado) {
            $total = $total->where('id_usuario', '=', (int)$id_usuario);
        }
        if ($id_sucursal !== null) {
            $total = $total->where('id_sucursal', '=', (int)$id_sucursal);
        }
        return (int)$total->first()->total;
    }

    private function coTotalClientesDesembolsado($id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario = null, $totalizado = false, $id_sucursal = null) {
        if (true || empty($fecha_inicio)) {
            $fecha_fin = self::ultimaFechaDisponible();
            $fecha_inicio = date('Y-m' . '-01', strtotime($fecha_fin));
        }
        if (empty($fecha_fin)) {
            $t_inicio = strtotime($fecha_inicio);
            $mes = date('n', $t_inicio) + 1;
            $ano = date('Y', $t_inicio);
            if ($mes > 12) {
                $mes = 1;
                $ano++;
            }
            if ($mes < 10) $mes = '0' . $mes;
            $fecha_fin = $ano . '-' . $mes . '-01';
            $fecha_fin = date('Y-m-d', strtotime('-1 DAY', strtotime($fecha_fin)));
        }
        if ($fecha_fin < $fecha_inicio) {
            $fecha_inicio = '1999-01-01'; //why not
        }
        $total = SimicroCredito
            ::where('fecha_desembolso', '>=', $fecha_inicio)
            ->where('fecha_desembolso', '<=', $fecha_fin)
            ->where('estado', '<>', 'SANEADO')
            ->where('total_pagado_int_complementario', '>', 0)
            ->selectRaw('COUNT(DISTINCT codigo_cliente) AS total');
        if (!$totalizado) {
            $total = $total->where('id_usuario', '=', (int)$id_usuario);
        }
        if ($id_sucursal !== null) {
            $total = $total->where('id_sucursal', '=', (int)$id_sucursal);
        }
        return (int)$total->first()->total;
    }

    private function coMontoCarteraActiva($id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario = null, $totalizado = false, $id_sucursal = null) {
        //id_moneda: (1) Córdoba, (2) Dólar
        $total = SimicroCredito
            ::/*where('fecha_desembolso', '>=', $fecha_inicio)
            ->where('fecha_desembolso', '<=', $fecha_fin)
            ->*/where('estado', '<>', 'SANEADO')
            ->selectRaw('SUM(CASE WHEN id_moneda = 1 THEN ROUND(mon_saldo_mas_deslizamiento / tipo_cambio_hoy, 4) ELSE mon_saldo_mas_deslizamiento END) AS total'); //in dollars (new tasa) (other column)
            //->selectRaw('SUM(CASE WHEN id_moneda = 1 THEN ROUND(saldo_principal / tipo_cambio_hoy, 4) ELSE saldo_principal END) AS total'); //in dollars (new tasa)
            //->selectRaw('SUM(CASE WHEN id_moneda = 1 THEN ROUND(saldo_principal / tipo_cambio_desembolso, 4) ELSE saldo_principal END) AS total') //in dollars
            //->selectRaw('SUM(CASE WHEN id_moneda = 2 THEN saldo_principal * tipo_cambio_desembolso ELSE saldo_principal END)') //in cordobas
        if (!$totalizado) {
            $total = $total->where('id_usuario', '=', (int)$id_usuario);
        }
        if ($id_sucursal !== null) {
            $total = $total->where('id_sucursal', '=', (int)$id_sucursal);
        }
        return (float)$total->first()->total;
    }

    private function coMontoDesembolsado($id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario = null, $totalizado = false, $id_sucursal = null) {
        //id_moneda: (1) Córdoba, (2) Dólar
        if ($fecha_inicio !== false) {
            if (empty($fecha_inicio)) {
                $fecha_fin = self::ultimaFechaDisponible();
                $fecha_inicio = date('Y-m' . '-01', strtotime($fecha_fin));
            }
            if (empty($fecha_fin)) {
                $t_inicio = strtotime($fecha_inicio);
                $mes = date('n', $t_inicio) + 1;
                $ano = date('Y', $t_inicio);
                if ($mes > 12) {
                    $mes = 1;
                    $ano++;
                }
                if ($mes < 10) $mes = '0' . $mes;
                $fecha_fin = $ano . '-' . $mes . '-01';
                $fecha_fin = date('Y-m-d', strtotime('-1 DAY', strtotime($fecha_fin)));
            }
            if ($fecha_fin < $fecha_inicio) {
                $fecha_inicio = '1999-01-01'; //why not
            }
        }
        $total = SimicroCredito
            ::where('estado', '<>', 'SANEADO')
            ->selectRaw('SUM(CASE WHEN id_moneda = 1 THEN ROUND(total_pagado_int_complementario / tipo_cambio_hoy, 4) ELSE total_pagado_int_complementario END) AS total'); //in dollars (new tasa) (other field)
            //->selectRaw('SUM(CASE WHEN id_moneda = 1 THEN ROUND(monto_entregado / tipo_cambio_hoy, 4) ELSE monto_entregado END) AS total'); //in dollars (new tasa)
            //->selectRaw('SUM(CASE WHEN id_moneda = 1 THEN ROUND(monto_entregado / tipo_cambio_desembolso, 4) ELSE monto_entregado END) AS total') //in dollars
            //->selectRaw('SUM(CASE WHEN id_moneda = 2 THEN monto_entregado * tipo_cambio_desembolso ELSE monto_entregado END) AS total') //in cordobas;
        if ($fecha_inicio !== false) {
            $total = $total
                ->where('fecha_desembolso', '>=', $fecha_inicio)
                ->where('fecha_desembolso', '<=', $fecha_fin);
        }
        if (!$totalizado) {
            $total = $total->where('id_usuario', '=', (int)$id_usuario);
        }
        if ($id_sucursal !== null) {
            $total = $total->where('id_sucursal', '=', (int)$id_sucursal);
        }
        if (Input::get('_debug')) {
            \App\Funciones::qdd($total);
        }
        return (float)$total->first()->total;
    }

    private function coCreditoMora($id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario = null, $totalizado = false, $id_sucursal = null) {
        $total = SimicroCredito
            ::/*where('fecha_desembolso', '>=', $fecha_inicio)
            ->where('fecha_desembolso', '<=', $fecha_fin)
            ->*/where('estado', '<>', 'SANEADO')
            ->where('dias_en_mora', '>', 0)
            ->selectRaw('COUNT(DISTINCT cod_anterior) AS total');
        if (!$totalizado) {
            $total = $total->where('id_usuario', '=', (int)$id_usuario);
        }
        if ($id_sucursal !== null) {
            $total = $total->where('id_sucursal', '=', (int)$id_sucursal);
        }
        return (int)$total->first()->total;
    }

    private function coMoraPar1($id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario = null, $totalizado = false, $id_sucursal = null) {
        $t_creditos = SimicroCredito
            ::/*where('fecha_desembolso', '>=', $fecha_inicio)
            ->where('fecha_desembolso', '<=', $fecha_fin)
            ->*/where('estado', '<>', 'SANEADO')
            ->selectRaw('SUM(CASE WHEN id_moneda = 1 THEN ROUND(mon_saldo_mas_deslizamiento / tipo_cambio_hoy, 4) ELSE mon_saldo_mas_deslizamiento END) AS total');//->selectRaw('COUNT(DISTINCT cod_anterior) AS total');
        if (!$totalizado) {
            $t_creditos = $t_creditos->where('id_usuario', '=', (int)$id_usuario);
        }
        if ($id_sucursal !== null) {
            $t_creditos = $t_creditos->where('id_sucursal', '=', (int)$id_sucursal);
        }
        $t_creditos = $t_creditos->first()->total;

        $t_creditos_mora = SimicroCredito
            ::/*where('fecha_desembolso', '>=', $fecha_inicio)
            ->where('fecha_desembolso', '<=', $fecha_fin)
            ->*/where('estado', '<>', 'SANEADO')
            ->where('dias_en_mora', '>', 0)
            ->selectRaw('SUM(CASE WHEN id_moneda = 1 THEN ROUND(mon_saldo_mas_deslizamiento / tipo_cambio_hoy, 4) ELSE mon_saldo_mas_deslizamiento END) AS total');//->selectRaw('COUNT(DISTINCT cod_anterior) AS total');
        if (!$totalizado) {
            $t_creditos_mora = $t_creditos_mora->where('id_usuario', '=', (int)$id_usuario);
        }
        if ($id_sucursal !== null) {
            $t_creditos_mora = $t_creditos_mora->where('id_sucursal', '=', (int)$id_sucursal);
        }
        $t_creditos_mora = $t_creditos_mora->first()->total;

        return $t_creditos > 0 ? round($t_creditos_mora * 100 / $t_creditos, 2) : 0;
    }

    private function coMoraPar30($id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario = null, $totalizado = false, $id_sucursal = null) {
        $t_creditos = SimicroCredito
            ::/*where('fecha_desembolso', '>=', $fecha_inicio)
            ->where('fecha_desembolso', '<=', $fecha_fin)
            ->*/where('estado', '<>', 'SANEADO')
            ->selectRaw('SUM(CASE WHEN id_moneda = 1 THEN ROUND(mon_saldo_mas_deslizamiento / tipo_cambio_hoy, 4) ELSE mon_saldo_mas_deslizamiento END) AS total');//->selectRaw('COUNT(DISTINCT cod_anterior) AS total');
        if (!$totalizado) {
            $t_creditos = $t_creditos->where('id_usuario', '=', (int)$id_usuario);
        }
        if ($id_sucursal !== null) {
            $t_creditos = $t_creditos->where('id_sucursal', '=', (int)$id_sucursal);
        }
        $t_creditos = $t_creditos->first()->total;

        $t_creditos_mora = SimicroCredito
            ::/*where('fecha_desembolso', '>=', $fecha_inicio)
            ->where('fecha_desembolso', '<=', $fecha_fin)
            ->*/where('estado', '<>', 'SANEADO')
            ->where('dias_en_mora', '>', 30)
            ->selectRaw('SUM(CASE WHEN id_moneda = 1 THEN ROUND(mon_saldo_mas_deslizamiento / tipo_cambio_hoy, 4) ELSE mon_saldo_mas_deslizamiento END) AS total');//->selectRaw('COUNT(DISTINCT cod_anterior) AS total');
        if (!$totalizado) {
            $t_creditos_mora = $t_creditos_mora->where('id_usuario', '=', (int)$id_usuario);
        }
        if ($id_sucursal !== null) {
            $t_creditos_mora = $t_creditos_mora->where('id_sucursal', '=', (int)$id_sucursal);
        }
        $t_creditos_mora = $t_creditos_mora->first()->total;

        return $t_creditos > 0 ? round($t_creditos_mora * 100 / $t_creditos, 2) : 0;
    }

    private function coCreditoMoraTotal($id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario = null, $totalizado = false, $id_sucursal = null) {
        $total = SimicroCredito
            ::/*where('fecha_desembolso', '>=', $fecha_inicio)
            ->where('fecha_desembolso', '<=', $fecha_fin)
            ->*/where('estado', '<>', 'SANEADO')
            ->where('dias_en_mora', '>', 0)
            ->selectRaw('SUM(CASE WHEN id_moneda = 1 THEN ROUND(mon_saldo_mas_deslizamiento / tipo_cambio_hoy, 4) ELSE mon_saldo_mas_deslizamiento END) AS total');
        if (!$totalizado) {
            $total = $total->where('id_usuario', '=', (int)$id_usuario);
        }
        if ($id_sucursal !== null) {
            $total = $total->where('id_sucursal', '=', (int)$id_sucursal);
        }
        return (float)$total->first()->total;
    }

    // private function coCreditoVigenteTotal($id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario = null, $totalizado = false, $id_sucursal = null) {
    //     $total = SimicroCredito
    //         ::/*where('fecha_desembolso', '>=', $fecha_inicio)
    //         ->where('fecha_desembolso', '<=', $fecha_fin)
    //         ->*///where('estado', '<>', 'SANEADO')
    //         where('estado', '=', 'VIGENTE') 
    //         ->selectRaw('SUM(CASE WHEN id_moneda = 1 THEN ROUND(mon_saldo_mas_deslizamiento / tipo_cambio_hoy, 4) ELSE mon_saldo_mas_deslizamiento END) AS total');
      
    //         if (!$totalizado) {
    //             $total = $total->where('id_usuario', '=', (int)$id_usuario);
    //         }
    //         if ($id_sucursal !== null) {
    //             $total = $total->where('id_sucursal', '=', (int)$id_sucursal);
    //         }
       
    //     return (float)$total->first()->total;
    // }
    // //
    // private function coCreditoVigenteConteo($id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario = null, $totalizado = false, $id_sucursal = null) {
    //     $total = SimicroCredito
    //         ::/*where('fecha_desembolso', '>=', $fecha_inicio)
    //         ->where('fecha_desembolso', '<=', $fecha_fin)
    //         ->*///where('estado', '<>', 'SANEADO')
    //         where('estado', '=', 'VIGENTE') 
    //         ->selectRaw('COUNT(DISTINCT cod_anterior) AS total');
      
    //         if (!$totalizado) {
    //             $total = $total->where('id_usuario', '=', (int)$id_usuario);
    //         }
    //         if ($id_sucursal !== null) {
    //             $total = $total->where('id_sucursal', '=', (int)$id_sucursal);
    //         }
       
    //     return (float)$total->first()->total;
    // }

    private function coTipoCambioHoy($id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario = null, $totalizado = false) {
        $item = SimicroCredito
            ::whereNotNull('tipo_cambio_hoy')
            ->orderBy('fecha_sistema', 'DESC')
            ->select('tipo_cambio_hoy');

        if (!$totalizado) {
            $item = $item->where('id_usuario', '=', (int)$id_usuario);
        }

        $item = $item->first();

        return $item ? /*(float)*/$item->tipo_cambio_hoy : null;
    }

    private function coFechaCarga($id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario = null, $totalizado = false) {
        $item = SimicroCredito
            ::whereNotNull('fecha_sistema')
            ->orderBy('fecha_sistema', 'DESC')
            ->select('fecha_sistema');

        if (!$totalizado) {
            $item = $item->where('id_usuario', '=', (int)$id_usuario);
        }

        $item = $item->first();

        return $item ? $item->fecha_sistema : null;
    }

    private static function coIndGerencia($usuario) {
        if (!$usuario) return 0;

        $item = SimicroCredito
            ::where('id_usuario', '=', (int)$usuario->id)
            ->orderBy('ind_gerencia', 'DESC')
            ->first();

        return $item ? ($item->ind_gerencia ? 1 : 0) : 0;
    }

    private function coCreditoEstado($estado, $id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario = null,$totalizado = false,$id_sucursal = null) {
        $total = SimicroCredito
            //->where('fecha_desembolso', '>=', $fecha_inicio)
            //->where('fecha_desembolso', '<=', $fecha_fin)
            ::where('estado', '=', $estado)
           // ->where('dias_en_mora', '>', 0)
            ->selectRaw('COUNT(DISTINCT cod_anterior) AS total');
            //->where('id_usuario', '=', (int)$id_usuario)
            //->first()
            //->total;
            if (!$totalizado) {
                $total = $total->where('id_usuario', '=', (int)$id_usuario);
                }
            if ($id_sucursal !== null) {
                 $total = $total->where('id_sucursal', '=', (int)$id_sucursal);
               }
        return (float)$total->first()->total;    
    }

    private function coCreditoEstadoTotal($estado, $id_ejecutivo, $fecha_inicio, $fecha_fin,  $id_usuario = null, $totalizado = false,$id_sucursal = null) {
        $total = SimicroCredito
            //->where('fecha_desembolso', '>=', $fecha_inicio)
            //->where('fecha_desembolso', '<=', $fecha_fin)
            ::where('estado', '=', $estado)
            ->selectRaw('SUM(CASE WHEN id_moneda = 1 THEN ROUND(mon_saldo_mas_deslizamiento / tipo_cambio_hoy, 4) ELSE mon_saldo_mas_deslizamiento END) AS total');
            //->where('id_usuario', '=', (int)$id_usuario)
          // ->first()
           // ->total;
           if (!$totalizado) {
                $total = $total->where('id_usuario', '=', (int)$id_usuario);
                }
            if ($id_sucursal !== null) {
                 $total = $total->where('id_sucursal', '=', (int)$id_sucursal);
               }
        return (float)$total->first()->total;       
      }
    /* END EZA DIGITAL */


    /* NUEVO EZA DIGITAL */
    public function carteraOficialListaGet($token = null) {
        /*
            [{
                "token": "sz0tIHMMlSOKLsunCgWnQu2Bcj6ZXUD87w3Q6XzBqiqdTOnYbtXpj6KTbqZJ",
                "num_control": null,
                "nombre": "Jose Paradiso",
                "foto": "http:\/\/intreza.sisteza.com:8083\/public\/uploads\/img\/s\/5d57217207938.jpg",
                "fecha_ingreso": null,
                "tipo_cargo": "IT",
                "empresas": [{
                    "id": 4,
                    "nombre": "Sisteza",
                    "ubicacion": "",
                    "logo": "",
                    "color": "FF562B",
                    "color_fondo": "FFFFFF",
                    "telefono": "",
                    "website": "sisteza.com"
                }],
                cartera :[{   <-- ¿"[{"? =====> "{"
                    "total_clientes": 87,
                    "monto_cartera_activa": 188743.5361,
                    "monto_desembolsado": 0,
                    "total_clientes_desembolsado": 0,
                    "credito_mora": 11,
                    "mora_par1": 4.95,
                    "mora_par30": 0.29,
                    "tipo_cambio_hoy": "34.5115",
                    "fecha_carga": "2020-08-31"
                }]
            }]
        */

        if ($token === null) {
            $token = Input::get('token');
        }

        if (!$this->buscarUsuarioPorToken($token)) {
            return $this->error();
        }

        $id_usuarios = SimicroCredito
            ::selectRaw('DISTINCT id_usuario')
            ->where('id_usuario', '>', 0)
            ->get()
            ->pluck('id_usuario')
            ->toArray();

        $usuarios = User
            ::whereIn('id', $id_usuarios)
            ->get();

        $listado = [];

        $fecha_fin = Funciones::formatoFechaSistema(Input::get('fecha_hasta'));
        if (empty($fecha_fin)) {
            $fecha_fin = self::ultimaFechaDisponible();
        }
        $fecha_inicio = Funciones::formatoFechaSistema(Input::get('fecha_desde'));
        if (empty($fecha_inicio)) {
            $fecha_inicio = date('Y-m', strtotime($fecha_fin)) . '-01';
        }

        foreach ($usuarios as $usuario) {
            $empleado = Empleado::deUsuario($usuario);

            $persona = Persona::find((int)$empleado->id_persona);

            $empresas = $empleado ? $empleado->empresas()->get() : [];

            $metas = $this->metasUsuario($usuario->id);

            $id_ejecutivo = $usuario->nombre;
            $id_usuario = $usuario->id;

            $cartera = [
                'total_clientes' => $this->coTotalClientes($id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario),
                'monto_cartera_activa' => $this->coMontoCarteraActiva($id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario),//50525.25,
                'monto_desembolsado' => $this->coMontoDesembolsado($id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario),//125505.50,
                'total_clientes_desembolsado' => $this->coTotalClientesDesembolsado($id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario),
                'credito_mora' => $this->coCreditoMora($id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario),
                'mora_par1' => $this->coMoraPar1($id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario),
                'mora_par30' => $this->coMoraPar30($id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario),
                'estados' => [
                    'vigentes' => [
                        'cantidad' => $this->coCreditoEstado('VIGENTE', $id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario),
                        'total' => $this->coCreditoEstadoTotal('VIGENTE', $id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario),
                    ],
                    'vencidos' => [
                        'cantidad' => $this->coCreditoEstado('VENCIDO', $id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario),
                        'total' => $this->coCreditoEstadoTotal('VENCIDO', $id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario),
                    ],
                    'cancelados' => [
                        'cantidad' => $this->coCreditoEstado('CANCELADO', $id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario),
                        'total' => $this->coCreditoEstadoTotal('CANCELADO', $id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario),
                    ],
                    'saneados' => [
                        'cantidad' => $this->coCreditoEstado('SANEADO', $id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario),
                        'total' => $this->coCreditoEstadoTotal('SANEADO', $id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario),
                    ],
                    'reestructurados' => [
                        'cantidad' => $this->coCreditoEstado('REESTRUCTURADO', $id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario),
                        'total' => $this->coCreditoEstadoTotal('REESTRUCTURADO', $id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario),
                    ],
                    'prorrogados' => [
                        'cantidad' => $this->coCreditoEstado('PRORROGADO', $id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario),
                        'total' => $this->coCreditoEstadoTotal('PRORROGADO', $id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario),
                    ],
                    //'judiciales' => $this->coCreditoEstado('JUDICIAL', $id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario),
                ],
                'tipo_cambio_hoy' => $this->coTipoCambioHoy($id_ejecutivo, $fecha_inicio, $fecha_fin, $id_usuario),
                'fecha_carga' => $this->coFechaCarga($id_usuario, $fecha_inicio, $fecha_fin, $id_usuario),
                'metas' => $metas,
            ];

            $listado[] = [
                'id' => $empleado->id,
                'num_control' => $empleado->num_control,
                'nombre' => $persona ? $persona->nombres() : '',
                'foto' => PersonaController::urlFoto($persona ? $persona->foto : null),
                'fecha_ingreso' => $empleado ? $empleado->fecha_ingreso : '',
                'tipo_cargo' => $empleado ? $empleado->tipo_cargo : '',
                'activo' => $empleado->status == Empleado::STATUS_INHABILITADO ? false : true,
                'empresas' => $empresas,
                'cartera' => $cartera,
            ];
        }

        return $listado;
    }

    public function metasUsuarioGet($token) {
        if ($token === null) {
            $token = Input::get('token');
        }

        if (!($usuario = $this->buscarUsuarioPorToken($token))) {
            return $this->error('Token inválido.');
        }

        return $this->metasUsuario($usuario->id);
    }

    private function metasUsuario($id_usuario) {
        $metas = \App\Ezadigital\Meta::get();

        $lista = [];

        foreach ($metas as $meta) {
            $u_meta = \App\Ezadigital\MetaUsuario
                ::join('ezadigital.meta', 'meta_usuario.id_meta', '=', 'meta.id')
                ->where('id_meta', '=', (int)$meta->id)
                ->where('meta_usuario.id_usuario', '=', (int)$id_usuario)
                ->orderBy('meta_usuario.fecha', 'DESC')
                ->selectRaw('meta.id, meta.nombre, meta_usuario.valor, meta_usuario.fecha')
                ->first();

            $lista[] = [
                'id' => $meta->id,
                'nombre' => $meta->nombre,
                'valor' => $u_meta ? (float)$u_meta->valor : null,
                'fecha' => $u_meta ? $u_meta->fecha : null,
            ];
        }

        return $lista;
    }
    /* NUEVO EZA DIGITAL */


    public function iniciarSesionPost() {
        $usuario = $this->iniciarSesionPorImei();

        if (!$usuario) {
            $nombre = Input::get('nombre');
            $contrasena = Input::get('contrasena');

            if (empty($nombre) || empty($contrasena)) {
                return $this->error('Valores inválidos.');
            }

            $usuario = $this->iniciarSesion($nombre, $contrasena);

            if ($usuario === false) {
                return $this->error('Valores incorrectos.');
            }

            $this->registrarSesionPorImei($usuario);
        }

        return self::jsonUsuario($usuario);
    }

    public function iniciarSesionPorImei() {
        $imei = Input::get('imei');
        $serial = Input::get('serial');

        if (empty($imei) || empty($serial)) {
            return false;
        }

        $u_touchid = UsuarioTouchId::where('imei', '=', $imei)
            ->where('serial_dispositivo', '=', $serial)
            ->first();

        if (!$u_touchid) {
            return false;
        }

        $usuario = User::find((int)$u_touchid->id_usuario);
        return $usuario;

        //return $this->iniciarSesionPorUsuario($usuario);
    }

    public function registrarImeiSerialPost() {
        if (!($nombre_usuario = Input::get('usuario'))) {
            return $this->error('Usuario inválido.');
        }

        if (!($usuario = User::where('nombre', '=', $nombre_usuario)->first())) {
            return $this->error('Usuario no encontrado.');
        }

        if (!$this->registrarSesionPorImei($usuario)) {
            return $this->error('Datos no válidos.');
        }

        return [
            'ok' => 1,
        ];
    }

    public function registrarSesionPorImei($usuario) {
        $imei = Input::get('imei');
        $serial = Input::get('serial');
        $ejecutivo_simicro = Input::get('ejecutivo_simicro', null);

        if (empty($imei) || empty($serial)) {
            return false;
        }

        if (!($empleado = Empleado::deUsuario($usuario))) {
            return false;
        }

        $existente = UsuarioTouchId::where('id_empleado', '=', (int)$empleado->id)
                        ->where('imei', '=', $imei)
                        ->where('serial_dispositivo', '=', $serial)
                        ->count();

        if ($existente) {
            return false;
        }

        UsuarioTouchId::create([
            'id_usuario' => $usuario->id,
            'id_empleado' => $empleado->id,
            'imei' => $imei,
            'serial_dispositivo' => $serial,
            'ejecutivo_simicro' => $ejecutivo_simicro,
        ]);

        return true;
    }

    public function listadoEmpresas() {
        $empresas = Empresa::get([
            'id',
            'nombre',
            'ubicacion',
            'logo',
            'color',
            'color_fondo',
            'telefono',
            'website',
            'descripcion',
        ])->toArray();

        foreach ($empresas as $key => $empresa) {
            if (!empty($empresa['logo'])) {
                $empresas[$key]['logo'] = ImagenController::urlImagen($empresa['logo']);
            }
        }

        return $empresas;
    }

    public function listadoEmpleados() {
        $id_empresa = (int)Input::get('id_empresa');

        /*if (!($empresa = Empresa::find($id_empresa))) {
            return $this->error('Empresa no encontrada.');
        }

        $empleados = $empresa->empleados()->with()->get();*/

        $empleados = Empleado::where('id_empresa', '=', $id_empresa)
            ->get();

        $listado = [];

        foreach ($empleados as $empleado) {
            $persona = Persona::find((int)$empleado->id_persona);

            $listado[] = [
                'id' => $empleado->id,
                'num_control' => $empleado->num_control,
                'nombre' => $persona ? $persona->nombres() : '',
                'foto' => PersonaController::urlFoto($persona ? $persona->foto : null),
                'fecha_ingreso' => $empleado ? $empleado->fecha_ingreso : '',
                'tipo_cargo' => $empleado ? $empleado->tipo_cargo : '',
                'activo' => $empleado->status == Empleado::STATUS_INHABILITADO ? false : true,
            ];
        }

        return $listado;
    }

    public function registrarUbicacionUsuarioPost($token = null) {
        if ($token === null) {
            $token = Input::get('token');
        }

        if (!($usuario = $this->iniciarSesionPorImei())) {
            return $this->error('IMEI inválido.');
        }

        /*if (!($usuario = $this->buscarUsuarioPorToken($token))) {
            return $this->error('Token inválido.');
        }*/

        //como json
        /*$data = json_decode(Input::get('json', ''));

        if (!is_object($data) || !is_object($data->cliente)) {
            return $this->error('Valores incorrectos.');
        }

        if (empty($data->latitud) || empty($data->longitud)) {
            return $this->error('Coordenas no válidas.');
        }
        $latitud = $data->latitud;
        $longitud = $data->longitud;
        $fecha_hora = $data->fecha_hora;
        */

        //como inputs
        $latitud = Input::get('latitud');
        $longitud = Input::get('longitud');
        $telefono = Input::get('telefono');
        $estado_bateria = Input::get('estado_bateria');
        $fecha_hora = Input::get('fecha_hora');

        if (empty($latitud) || empty($longitud)) {
            return $this->error('Coordenas no válidas.');
        }

        $item = UsuarioUbicacion::create([
            'id_usuario' => $usuario->id,
            'latitud' => $latitud,
            'longitud' => $longitud,
            'telefono' => $telefono,
            'estado_bateria' => $estado_bateria,
            'fecha_hora' => $fecha_hora ?: date('Y-m-d H:i:s'),
        ]);

        return [
            'ok' => (int)!!$item,
        ];
    }

    public function cargarUbicacionesUsuarioGet($token = null) {
    	if (!($usuario = $this->iniciarSesionPorImei())) {
            return $this->error('IMEI inválido.');
        }

        /*if (!($id_usuario = Input::get('id_usuario'))) {
        	if ($token === null) {
	            $token = Input::get('token');
	        }
	        if (!($usuario = $this->buscarUsuarioPorToken($token))) {
	            return $this->error('Token inválido.');
	        }
	    }
	    else {
	    	if (!($usuario = User::find((int)$id_usuario))) {
	    		return $this->error('Usuario no encontrado.');
	    	}
	    }*/

        $fecha = date('Y-m-d');

        $items = UsuarioUbicacion
        	::where('id_usuario', '=', (int)$usuario->id)
            ->where('fecha_hora', '>=', $fecha . ' 00:00:00')
            ->where('fecha_hora', '<=', $fecha . ' 23:59:59')
        	->orderBy('fecha_hora', 'DESC')
        	->orderBy('fecha_creacion', 'DESC')
        	->get([
        		'latitud',
		        'longitud',
		        'estado_bateria',
		        'fecha_hora',
    		]);

        return $items;
    }

    public function registrarGaleriaCreditoPost($token = null) {
        /*$date_test = date('YmdHis');
        $f = fopen('test_post' . $date_test . '.txt', 'w');
        $data_json = $request->json()->all();
        fwrite($f, json_encode(Input::all()));
        fwrite($f, '>' . Input::get('token', '-') . '<');
        fwrite($f, json_encode($data_json));
        fclose($f);*/

        if ($token === null) {
            $token = Input::get('token');
        }

        //$data = $data_json;//Input::all();
        //if (is_string($data)) $data = json_decode($data);

        if (empty($token)) {
            $token = !empty($data['token']) ? $data['token'] : '';
        }

        if (empty($token) && !empty($data['token'])) {
            $token = $data['token'];
        }

        if (empty($token) || !($usuario = $this->buscarUsuarioPorToken($token))) {
            return $this->error('Token inválido.');
        }

        //$data = json_decode(Input::get('json', ''));
        $data = Input::all();

        if (empty($data['cliente']['nombre'])) {
            return $this->error('Valores incorrectos.');
        }

        $cliente = (object)$data['cliente'];

        //se busca por dni
        if (empty($cliente->dni) || !($galeria_credito_cliente = GaleriaCreditoCliente::where('dni', '=', $cliente->dni)->first())) {
            $galeria_credito_cliente = GaleriaCreditoCliente::create([
                'id_usuario' => $usuario->id,
                'nombre' => $cliente->nombre,
                'dni' => !empty($cliente->dni) ? $cliente->dni : null,
                'negocio' => !empty($cliente->empresa) ? $cliente->empresa : null,
                'ruc' => !empty($cliente->ruc) ? $cliente->ruc : null,
                'direccion' => !empty($cliente->direccion) ? $cliente->direccion : null,
                'telefono' => !empty($cliente->telefono) ? $cliente->telefono : null,
            ]);
        }

        if (!$galeria_credito_cliente) {
            return $this->error('No se pudo registrar el cliente.');
        }

        $monedas = [
            'USD' => [
                'id' => 1,
                'codigo_iso' => 'USD',
                'simbolo' => '$',
            ],
            'NIO' => [
                'id' => 2,
                'codigo_iso' => 'NIO',
                'simbolo' => 'C$',
            ],
        ];

        if (empty($data['moneda'])) {
            $moneda = $monedas['USD'];
        }
        else {
            if (!isset($monedas)) {
                return $this->error('Moneda inválida.');
            }
            $moneda = $monedas[$data['moneda']];
        }

        $galeria_credito = GaleriaCredito::create([
            'id_usuario' => $usuario->id,
            'id_galeria_credito_cliente' => $galeria_credito_cliente->id,
            'nombre' => $data['titulo'],
            'fecha' => Funciones::formatoFechaSistema($data['fecha']),
            'monto' => (float)$data['monto'],
            'id_moneda' => $moneda['id'],
            'moneda_iso' => $moneda['codigo_iso'],
            'moneda_simbolo' => $moneda['simbolo'],
            'observaciones' => '',
        ]);

        if (!$galeria_credito) {
            return $this->error('No se pudo registrar el documento.');
        }

        if (isset($data['fotos']) && is_array($data['fotos']) && count($data['fotos'])) {
            $this->registrarGaleriaCreditoFotos($galeria_credito->id, $data['fotos'], $usuario);
        }

        return [
            'id_documento' => $galeria_credito ? $galeria_credito->id : 0,
        ];
    }

    public function registrarGaleriaCreditoFotosPost($token = null) {
        if ($token === null) {
            $token = Input::get('token');
        }

        if (!($usuario = $this->buscarUsuarioPorToken($token))) {
            return $this->error('Token inválido.');
        }

        $id_documento = (int)Input::get('id_documento');
        $fotos = Input::get('fotos');

        if (empty($id_documento) || !($galeria_credito = GaleriaCredito::find($id_documento))) {
            return $this->error('Documento no encontrado.');
        }

        if (isset($fotos) && is_array($fotos) && count($fotos)) {
            $this->registrarGaleriaCreditoFotos($galeria_credito->id, $fotos, $usuario);
        }

        return [
            'ok' => 1,
        ];
    }

    public function registrarGaleriaCreditoFotos($id_galeria_credito, $fotos, $usuario) {
        if (!($galeria_credito = GaleriaCredito::find($id_galeria_credito))) {
            return $this->error('Documento no encontrado.');
        }

        $n_fotos_cargadas = 0;

        $indice = $galeria_credito->fotos()->max('indice');

        $tipos = GaleriaCreditoItem::tipos();

        $fecha_actual = date('Y-m-d');

        foreach ($fotos as $foto) {
            $foto = (object)$foto;
            if  (empty($foto->base64)) continue;

            if ($nombre_imagen = ImagenController::crearDesdeBase64($foto->base64)) {
                $atributos = ImagenController::atributos($nombre_imagen);

                if ($atributos === null) $atributos = [];

                $tipo = 1;

                if ($key = array_search($foto->categoria, $tipos)) {
                    $tipo = $key;
                }
                elseif (isset($tipos[$foto->categoria])) {
                    $tipo = $foto->categoria;
                }

                $camara = [];
                if (!empty($atributos['marca'])) $camara[] = $atributos['marca'];
                if (!empty($atributos['modelo'])) $camara[] = $atributos['modelo'];

                $galeria_item = GaleriaCreditoItem::create([
                    'id_usuario' => $usuario->id,
                    'id_galeria_credito' => (int)$id_galeria_credito,
                    'nombre' => $foto->categoria,
                    'tipo' => $tipo,
                    'foto' => $nombre_imagen,
                    'fecha' => $fecha_actual,
                    'visible' => 1,
                    'observaciones' => null,
                    'ancho' => $atributos['ancho'],
                    'alto' => $atributos['alto'],
                    'latitud' => !empty($foto->latitud) ? $foto->latitud : null,
                    'longitud' => !empty($foto->longitud) ? $foto->longitud : null,
                    'kb' => $atributos['kbs'],
                    'fecha_captura' => empty($atributos['timestamp']) ? date('Y-m-d H:i:s', $atributos['timestamp']) : null,
                    'nombre_original' => null,
                    'camara' => implode(' ', $camara),
                    'indice' => ++$indice,
                ]);

                if ($galeria_item) {
                    $n_fotos_cargadas++;
                }
            }
        }

        return $n_fotos_cargadas;
    }

    public function listadoGaleriaCreditoGet($token = null) {
        if ($token === null) {
            $token = Input::get('token');
        }

        if (!($usuario = $this->buscarUsuarioPorToken($token))) {
            return $this->error('Token inválido.');
        }

        $items = GaleriaCredito::traerData($usuario->id);

        $listado = [];

        foreach ($items as $item) {
            $cliente = [
                'nombre' => $item['nombre_cliente'] ?: '',
                'dni' => $item['dni_cliente'] ?: '',
                'direccion' => $item['direccion_cliente'] ?: '',
                'empresa' => $item['negocio_cliente'] ?: '',
                'ruc' => $item['ruc_cliente'] ?: '',
                'telefono' => $item['telefono_cliente'] ?: '',
            ];

            $fotos = GaleriaCreditoItem::items($item['id']);
            $tipos = GaleriaCreditoItem::tipos();
            $listado_fotos = [];
            foreach ($fotos as $foto_item) {
                $listado_fotos[] = [
                    'url' => ImagenController::urlImagen($foto_item->foto),
                    'categoria' => $tipos[$foto_item->tipo] ?: '',
                    'latitud' => $foto_item->latitud,
                    'longitud' => $foto_item->longitud,
                ];
            }

            $listado[] = [
                'id' => $item['id'],
                'titulo' => $item['nombre'],
                'fecha' => Funciones::formatoFechaApp($item['fecha']),
                'monto' => (float)$item['monto'],
                'moneda' => $item['moneda_iso'],
                'cliente' => $cliente,
                'fotos' => $listado_fotos,
            ];
        }

        return $listado;
    }

    public function iniciarSesion($nombre, $contrasena) {
        $usuario = User::where('nombre', '=', $nombre)->first();
        if ($usuario) {
            if (Hash::check($contrasena, $usuario->contrasena)) {
                return $this->iniciarSesionPorUsuario($usuario);
            }
        }
        return false;
    }

    public function iniciarSesionPorUsuario($usuario) {
        $token = Str::random(60);

        $usuario->api_token = $token;
        $usuario->fecha_ultimo_ingreso = date('Y-m-d H:i:s');
        $usuario->save();

        return $usuario;
    }

    public function buscarUsuarioPorToken($token) {
        if (empty($token)) return null;

        //temp for testing
        if ($token == 'alfredo') {
            return User
                ::whereNotNull('api_token')
                ->where('api_token', '<>', '')
                ->first();
        }

        return User::where('api_token', '=', $token)->first();
    }

    public function obtenerUsuarios() {
        $empresas =   SimicroCredito
        ::
       distinct()
        ->get([
            'id_ejecutivo',
            'id_usuario',
           // 'sucursal'
        ])
       ;
      
        return $empresas;
    }

    /**
     * @param \App\User $usuario
     * @return array
     */

    public static function jsonUsuario($usuario) {
        $persona = $usuario->traerPersona(); 

        $empleado = $persona ? $persona->empleado() : null;

        //$empresa = $empleado ? Empresa::find((int)$empleado->id_empresa) : null;
        $empresas = $empleado ? $empleado->empresas()->get() : [];

        $empresa = null;
        $data_empresas = [];
        if (count($empresas)) {
            foreach ($empresas as $empresa) {
                $data_empresas[] = [
                    'id' => $empresa->id,
                    'nombre' => $empresa->nombre,
                    'ubicacion' => $empresa->ubicacion ?: '',
                    'logo' => EmpresaController::urlLogo($empresa->logo),
                    'color' => $empresa->color ?: '',
                    'color_fondo' => $empresa->color_fondo ?: '',
                    'telefono' => $empresa->telefono ?: '',
                    'website' => $empresa->website ?: '',
                ];
            }
        }

        return [
            'token' => $usuario->api_token,
            'num_control' => $empleado ? $empleado->num_control : '',
            'nombre' => $persona ? $persona->nombres() : $usuario->nombre,
            'foto' => PersonaController::urlFoto($persona ? $persona->foto : null),
            'fecha_ingreso' => $empleado ? $empleado->fecha_ingreso : '',
            'tipo_cargo' => $empleado ? $empleado->tipo_cargo : '',
            'empresas' => $data_empresas,
            'ind_gerencia' => self::coIndGerencia($usuario),
        ];
    }

    private function error($mensaje = null) {
        $arr = ['token' => ''];
        if (!empty($mensaje)) {
            $arr['mensaje'] = $mensaje;
        }
        return $arr;
    }
}