<?php
/**
 * Created by PhpStorm.
 * User: Alfredo Fleming
 * Date: 12/11/2019
 * Time: 4:26 PM
 */

namespace App\Ezadigital;

class SimicroCredito extends \App\Modelo {

    public $timestamps = false;

    protected $table = 'ezadigital.simicro_credito'; 

    /**
     * Los atributos que se pueden guardar
     *
     * @var array
     */
    protected $fillable = [
        'metodologia',
        'cod_anterior',
        'ciclo',
        'fondo',
        'sucursal',
        'nombre_cliente',
        'producto_crediticio',
        'sector_economico',
        'rango_credito',
        'monto_entregado',
        'saldo_principal',
        'mon_saldo_mas_deslizamiento',
        'fecha_desembolso',
        'fecha_vencimiento',
        'actividad_economica',
        'id_ejecutivo',
        'asesor_credito',
        'cant_hombres',
        'cant_mujeres',
        'total_clientes',
        'codigo_cliente',
        'codigo_credito',
        'monto_desembolsado_hombres',
        'monto_desembolsado_mujeres',
        'principal_mujeres',
        'principal_hombres',
        'gasto_legales',
        'tasa_interes',
        'tasa_interes_complementario',
        'tasa_monetaria',
        'plazo_credito',
        'periodicidad',
        'rango_plazos',
        'rango_ciclos',
        'rango_mora',
        'dias_en_mora',
        'tipo_cambio',
        'fecha_sistema',
        'total_cargos_administrativos',
        'total_cobranza',
        'total_comision',
        'total_deslizado',
        'total_int_corriente_pagado',
        'total_int_mora_acumulado',
        'total_pagado_principal',
        'total_pagado_int_complementario',
        'total_cargos_administrativos_hombres',
        'total_cargos_administrativos_mujeres',
        'total_cobranza_hombres',
        'total_cobranza_mujeres',
        'total_comision_hombres',
        'total_comision_mujeres',
        'total_deslizado_hombres',
        'total_deslizado_mujeres',
        'total_interes_hombres',
        'total_interes_mujeres',
        'total_int_mora_hombres_acumulado',
        'total_int_mora_mujeres_acumulado',
        'total_principal_hombres',
        'total_principal_mujeres',
        'total_int_complementario_hombres',
        'total_int_complementario_mujeres',
        'monto_garantia_fiduciaria',
        'monto_garantia_hipotecaria',
        'monto_garantia_prendaria',
        'monto_garantia_bancaria',
        'monto_total_vencido',
        'monto_interes',
        'monto_mora',
        'monto_deslizado',
        'monto_desembolsado',
        'fecha_ult_pago_principal',
        'fecha_primer_pago_principal',
        'estado',
        'direccion_negocio',
        'cantidad_cuotas_mora',
        'id_moneda',
        'num_id_cedula',
        'moneda',
        'id_sucursal_usuario',
        'mon_desembolsado_grupo',
        'mon_saldo_grupo',
        'nom_cliente_grupo',
        'num_linea',
        'desc_segregacion',
        'mon_int_pagado',
        'mon_mora_pagada',
        'mon_desli_pagado',
        'mon_tic_pagado',
        'mon_cobranza_pagado',
        'mon_cargo_pagado',
        'mon_pagado_principal',
        'barrio_negocio',
        'municipio_negocio',
        'mon_cobranza',
        'dir_sucursal',
        'desc_sexo',
        'mon_cuota_pendiente',
        'observaciones',
        'desc_actividad',
        'telefono',
        'desc_convenio',
        'departamento_negocio',
        'departamento_domicilio',
        'municipio_domicilio',
        'barrio_domicilio',
        'direccion_domicilio',
        'id_tipo_linea',
        'desc_tipo_linea',
        'nom_ejecutivo_origen',
        'nom_ejecutivo_destino',
        'monto_seguro',
        'porcentaje_comision_desembolso',
        'monto_comision_desembolso',
        'porcentaje_gastos_legales',
        'monto_gastos_legales',
        'on_otorgcre',
        'tipo_cambio_desembolso',
        'fec_ult_pago',
        'id_usuario',
        'rango_mora2',
        'cant_total',
        'id_metodo',
        'id_sucursal',
        'id_estado',
        'id_calificacion',
        'sec_credito_ant',
        'mantenimiento_valor',
        'interes',
        'mora',
        'seguro',
        'total_adeudado',
        'mon_al_dia',
        'cuotas_pagadas',
        'cuotas_mora',
        'cuotas_pendientes',
        'tipo_cambio_hoy',
        'ind_gerencia',
    ];


    /**
     * Devuélve las reglas de validación para un campo específico o el arreglo de reglas por defecto
     *
     * @param string $campo     Nombre del campo del que se quiere las reglas de validación.
     * @param int $ignorar_id    ID del elemento que se está editando, si es el caso.
     * @return array|string
     */
    public static function reglasValidacion($campo = null, $ignorar_id = 0) {
        $reglas = [
            'id_usuario'    => 'integer',
        ];
        if ($campo === null) {
            return $reglas;
        }
        return isset($reglas[$campo]) ? $reglas[$campo] : '';
    }


    # RELACIONES


    # FILTROS


    # ASIGNACIONES


    # LECTURAS


    # METODOS

    public static function traerData($campos = null) {
        $campos = [
            'id',
        ];

        $items = self::orderBy('id');

        //self::verificarPermiso($items, 'almacen-proveedor');

        return $items
            ->selectRaw(implode(',', $campos))
            ->get()
            ->toArray();
    }

}