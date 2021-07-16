<?php
/**
 * Created by PhpStorm.
 * User: Alfredo
 * Date: 28/11/2017
 * Time: 3:55 PM
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

class Permiso extends Model {

    public $timestamps = false;

    protected $table = 'permiso';

    /**
     * Los atributos que se pueden guardar
     *
     * @var array
     */
    protected $fillable = [
        'nombre',
        'descripcion',
        'categoria'
    ];

    const TIPO_BOOLEANO = 0;
    const TIPO_SELECCION = 1;


    # RELACIONES

    public function roles() {
        return $this->belongsToMany('App\Rol', 'rol_permiso','id_permiso', 'id_rol');
    }


    # FILTROS

    function scopeCategoria($query, $valor) {
        return $query->where('categoria', '=', $valor);
    }

    function scopenombre($query, $valor) {
        return $query->where('nombre', '=', $valor);
    }


    # ASIGNACIONES


    # LECTURAS


    # METODOS

    /**
     * Actualiza la lista de permisos en la base de datos.
     * Solo debe ejecutarse cuando se realicen cambios
     *
     * Métodos de ejecución:
     * 1) http://.../public/rol?actualizar_permisos=1
     * 2) php artisan actualizar_permisos
     */
    public static function actualizarPermisos() {
        //TODO: Considera usar un JSON o un XML para cargar esta estructura
        $modelos = [
            'Dashboard',
            'Anualidad',
            'Carrera',
            'Decanatura',
            'Estudiante',
            'Facultad',
            'Evento',
            'Materia',
            'Examen',
            'Modalidad',
            'PlanEstudio',
            'Profesor',
            'TipoCarrera',
            'Sucursal',
            'Salon',
            'Matricula',
            'Inscripcion',
            'Nota',
            'Documento',
            'Pago',
            'Moneda',
            'Biblioteca',
            'Libro',
            'Mensaje',
            'EducacionContinua',
            'User'
        ];

        $acciones_basicas = [
            'consultar',
            'crear',
            'eliminar',
        ];

        $acciones_adicionales = [
            'PlanEstudio' => ['cerrar', 'editar_cerrado'],
            'Carrera' => ['*'], //el * muestra un select para seleccionar los registros permitidos
            'User' => ['cambiar_contrasenas'],
            'Estudiante' => [
                'todos',
                'cambiar_foto',
                'imprimir_carnet',
                'cambiar_plan_estudio',
                'admin_documentos',
                'agregar_servicios',
                'agregar_servicios_precios',
                'quitar_servicios',
                'registrar_responsable',
                'importar_notas',
                'agregar_creditos',
                'agregar_beca',
                'habilitar_inhabilitar',
                'cambiar_fecha_vencimiento_servicios',
                'modificar_montos',
            ],
            'Profesor' => [
                'admin_documentos',
                'habilitar_inhabilitar',
                'cambiar_foto',
                'imprimir_carnet',
            ],
            'Evento' => [
                'ver_todos',
                'crear_eventos_definidos',
                'ver_notas',
                'ver_asistencias',
                'editar_notas',
                'editar_asistencias',
                'seleccionar_objetivos',
                'seleccionar_profesor',
                'seleccionar_salon',
                'seleccionar_materia',
                'seleccionar_servicios',
                'admin_dias_inhabilitados',
                'inscribir_estudiantes',
                'modificar_horas',
                'agregar_mensaje',
                'cerrar',
            ],
            'Materia' => [
                'admin_contenido',
                //'admin_examenes',
            ],
            'Examen' => [
                'corregir',
            ],
            'Matricula' => [
                'admin_servicios',
            ],
            'Libro' => [
                'modificar_inventario',
                'registrar_prestamos',
                'subir_archivo',
                'revisar',
                'confirmar',
            ],
            'Pago' => [
                'admin_pagos_por_confirmar',
                'exonerar_multas',
                'exonerar_servicios',
                'registrar_recibo_manual',
                'anular_pagos',
                'ingresar_montos_menores'
            ],
            'Inscripcion' => [
                'seleccionar_materias_inhabilitadas'
            ],
            'EducacionContinua' => [
                'admin_aperturas',
                'admin_servicios',
                'admin_inscripciones',
            ],
            'Moneda' => [
                'ingresar_tasas',
            ],
        ];
        //Nota: los textos se definen en lang/xx/rol.php

        $exclusiones = [
            'Dashboard' => ['crear', 'eliminar'],
            'Pago' => ['eliminar'],
            'Evento' => ['crear']
        ];

        foreach ($modelos as $modelo) {
            $acciones = array_merge($acciones_basicas, isset($acciones_adicionales[$modelo]) && is_array($acciones_adicionales[$modelo]) ? $acciones_adicionales[$modelo] : []);
            $acciones_modelo = [];

            foreach ($acciones as $accion) {
                if (!isset($exclusiones[$modelo]) || !is_array($exclusiones[$modelo]) || !in_array($accion, $exclusiones[$modelo])) {
                    //si no existe, se agrega
                    if (!self::categoria($modelo)->nombre($accion)->first()) {
                        self::create([
                            'nombre'    => $accion,
                            'categoria' => $modelo
                        ]);
                    }
                    $acciones_modelo[] = $accion;
                }
            }

            //elimina todas las que no se hayan especificado arriba
            self::categoria($modelo)->whereNotIn('nombre', $acciones_modelo)->delete();
        }
    }

}