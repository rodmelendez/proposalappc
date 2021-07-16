<template>
    <main>
        <!-- Dashboard Headline -->
        <div class="dashboard-headline">
            <div class="row">
                <div class="col-sm-8">
                    <h3>
                        {{ titulo_plural }}
                        <span class="item-cargando">&nbsp;<i class="icon-line-awesome-hourglass-2 fa-spin"></i></span>
                    </h3>
                </div>

                <div class="col-sm-4">
                    <!--div class="input-with-icon">
                        <input type="text" placeholder="Buscar..." v-model="texto_buscado" @dblclick="texto_buscado = ''">
                        <i class="icon-material-outline-search"></i>
                    </div-->
                </div>
            </div>
        </div>

        <!-- Row -->
        <div class="row">

            <!-- Dashboard Box -->
            <div class="col-xl-12">
                <div class="dashboard-box margin-top-0">

                    <!-- Headline -->
                    <div class="headline">
                        <h3><i :class="icono"></i>{{ titulo_plural }}</h3>

                        <div class="actions">
                            <!--button type="button" class="popup-with-zoom-anim button dark ripple-effect">
                                <i class="icon-feather-plus"></i> Nuevo
                            </button-->
                        </div>
                    </div>

                    <div class="content">
                        <div class="barra-acciones">
                            <div class="row">
                                <div class="col-xl-12">
                                    <!--<div class="input-with-icon" v-if="puede_crear">
                                        <input type="text" class="margin-bottom-0" placeholder="Empleado">
                                        <i class="btn-actualizar-item icon-feather-check"></i>
                                    </div>-->
                                    <input-seleccion
                                            v-model="id_empleado"
                                            nombre="id_empleado"
                                            etiqueta="Empleado"
                                            :items="empleados"
                                            plantilla="avatar"
                                            @cambiado="cargarHorarioEmpleado"
                                    ></input-seleccion>
                                </div>
                            </div>

                            <div class="contenedor-acciones" v-show="id_empleado">
                                <div class="row">
                                    <div class="col-xl-12">
                                        <button type="button" class="button gray ico ico-text" title="Asignar horario" @click="mostrarFormularioAsignar">
                                            <i class="icon-line-awesome-calendar-plus-o"></i>
                                            Asignar horario
                                        </button>

                                        <button type="button" class="button gray ico ico-text" title="Asignar horario desde plantilla" @click="mostrarFormularioAsignarPlantilla">
                                            <i class="icon-material-outline-date-range"></i>
                                            Asignar horario desde plantilla
                                        </button>

                                        <button type="button" class="button gray ico ico-text" title="Quitar horario por rango" @click="mostrarFormularioQuitar">
                                            <i class="icon-feather-trash"></i>
                                            Quitar horario por rango
                                        </button>
                                    </div>
                                </div>

                                <div class="margin-top-20" v-if="mostrar_formulario_nuevo">
                                    <div class="row">
                                        <div class="col-xl-10">
                                            <div class="row">
                                                <div class="col-xl-3">
                                                    <input-fecha
                                                            v-model="nuevo_fecha_inicio"
                                                            nombre="fecha_inicio"
                                                            etiqueta="Fecha"
                                                    ></input-fecha>
                                                </div>

                                                <div class="col-xl-3">
                                                    <input-hora
                                                            v-model="nuevo_hora_inicio"
                                                            nombre="hora_inicio"
                                                            etiqueta="Hora inicio"
                                                    ></input-hora>
                                                </div>

                                                <div class="col-xl-3">
                                                    <input-hora
                                                            v-model="nuevo_hora_fin"
                                                            nombre="hora_fin"
                                                            etiqueta="Hora fin"
                                                    ></input-hora>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-xl-2">
                                            <a href="#" class="button ripple-effect button-sliding-icon big margin-top-40" @click.prevent="asignarNuevo">
                                                <span class="item-guardar">Asignar <i class="icon-feather-check"></i></span>
                                                <span class="item-guardando">Asignando... <i class="icon-line-awesome-hourglass-2 fa-spin"></i></span>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div class="margin-top-20" v-if="mostrar_formulario_plantilla">
                                    <div class="row">
                                        <div class="col-xl-4">
                                            <input-seleccion
                                                    v-model="id_horario_plantilla"
                                                    nombre="id_horario_plantilla"
                                                    etiqueta="Plantilla"
                                                    :items="plantillas"
                                            ></input-seleccion>
                                        </div>

                                        <div class="col-xl-3">
                                            <input-fecha
                                                    v-model="plantilla_fecha_inicio"
                                                    nombre="fecha_inicio"
                                                    etiqueta="Desde"
                                            ></input-fecha>
                                        </div>

                                        <div class="col-xl-3">
                                            <input-fecha
                                                    v-model="plantilla_fecha_fin"
                                                    nombre="fecha_fin"
                                                    etiqueta="Hasta"
                                            ></input-fecha>
                                        </div>

                                        <div class="col-xl-2">
                                            <a href="#" class="button ripple-effect button-sliding-icon big margin-top-40" @click.prevent="asignarPlantilla">
                                                <span class="item-guardar">Asignar <i class="icon-feather-check"></i></span>
                                                <span class="item-guardando">Asignando... <i class="icon-line-awesome-hourglass-2 fa-spin"></i></span>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div class="margin-top-20" v-if="mostrar_formulario_quitar">
                                    <div class="row">
                                        <div class="col-xl-3">
                                            <input-fecha
                                                    v-model="quitar_fecha_inicio"
                                                    nombre="fecha_inicio"
                                                    etiqueta="Desde"
                                            ></input-fecha>
                                        </div>

                                        <div class="col-xl-3">
                                            <input-fecha
                                                    v-model="quitar_fecha_fin"
                                                    nombre="fecha_fin"
                                                    etiqueta="Hasta"
                                            ></input-fecha>
                                        </div>

                                        <div class="col-xl-2">
                                            <a href="#" class="button ripple-effect button-sliding-icon big margin-top-40" @click.prevent="quitarEventos">
                                                <span class="item-guardar">Quitar <i class="icon-feather-trash"></i></span>
                                                <span class="item-guardando">Quitando... <i class="icon-line-awesome-hourglass-2 fa-spin"></i></span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="contenedor-calendario padding-top-10">
                            <div class="row">
                                <div :class="formulario_eventos ? 'col-xl-8' : 'col-xl-12'">
                                    <calendario
                                            :eventos="eventos"
                                            :permitir_arrastrar="true"
                                            :cargando="cargando_eventos"
                                            @cambiado="recargarHorarioEmpleado"
                                            @eventoCambiado="actualizarHorarioItem"
                                            @eventoSeleccionado="cargarEventos"
                                    ></calendario>
                                </div>
                                <div :class="formulario_eventos ? 'col-xl-4' : 'hidden'">
                                    <div>
                                        <h3 class="margin-top-10">Eventos</h3>
                                        <div class="contenedor-eventos">
                                            <div class="listings-container compact-list-layout margin-top-35">

                                                <template v-for="evento in eventos_item">
                                                    <a class="job-listing with-apply-button" :key="evento.id">
                                                        <div class="job-listing-details">
                                                            <!-- Details -->
                                                            <div class="job-listing-description padding-left-10">
                                                                <!-- Job Listing Footer -->
                                                                <div class="job-listing-footer padding-left-2">
                                                                    <ul>
                                                                        <!--<li><i class="icon-material-outline-business"></i> Hexagon</li>
                                                                        <li><i class="icon-material-outline-location-on"></i> San Francissco</li>-->
                                                                        <li><i class="icon-material-outline-business-center"></i> {{ descTipo(evento.tipo) }}</li>
                                                                        <li><i class="icon-material-outline-access-time"></i> {{ parseFloat(evento.horas) || 0 }} hora(s)</li>
                                                                    </ul>
                                                                    <p>{{ evento.comentario }}</p>
                                                                    <div v-if="typeof evento.archivos === 'object' && evento.archivos !== null">
                                                                        <div v-for="archivo in evento.archivos" :key="archivo.id">
                                                                            <a :href="urlArchivo(archivo)" target="_blank">Ver documento</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <button type="button" class="float-right" @click="eliminarEventoItem(evento)">
                                                            <i class="icon-feather-trash"></i>
                                                        </button>
                                                    </a>
                                                </template>
                                            </div>
                                        </div>

                                        <div class="contenedor-nuevo-evento-item barra-acciones">
                                            <form ref="frm_nuevo_evento_item" method="post">
                                                <input type="hidden" name="_fuente" value="Horario">
                                                <input type="hidden" name="_accion" value="registrarEvento">
                                                <input type="hidden" name="id_empleado" :value="id_empleado">
                                                <input type="hidden" name="id_horario" :value="nuevo_evento_item_id">

                                                <input-seleccion
                                                        v-model="nuevo_evento_item_tipo"
                                                        nombre="tipo"
                                                        etiqueta="Tipo"
                                                        :items="tipos_eventos"
                                                ></input-seleccion>

                                                <template v-if="nuevo_evento_item_tipo != 2">
                                                    <input-texto
                                                            v-model="nuevo_evento_item_horas"
                                                            nombre="horas"
                                                            etiqueta="Total horas"
                                                    ></input-texto>
                                                </template>

                                                <input-texto-multilinea
                                                        v-model="nuevo_evento_item_comentario"
                                                        nombre="comentario"
                                                        etiqueta="Comentario"
                                                ></input-texto-multilinea>

                                                <input-archivo
                                                        v-model="nuevo_evento_item_archivo"
                                                        nombre="archivo"
                                                        etiqueta="Documento"
                                                ></input-archivo>

                                                <a href="#" class="button ripple-effect button-sliding-icon big" @click.prevent="guardarEventoItem">
                                                    <span class="item-guardar">Guardar <i class="icon-feather-save"></i></span>
                                                    <span class="item-guardando">Guardando... <i class="icon-line-awesome-hourglass-2 fa-spin"></i></span>
                                                </a>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>
</template>

<script>
    export default {
        name: "Horario.vue",

        props: {
            urls: Object,
            avatar_defecto: String,
            empresas: {
                default: []
            }
        },

        data: () => ({
            fuente: 'Horario',
            titulo_singular: 'Horario',
            titulo_plural: 'Horarios',
            icono: 'icon-material-outline-access-time',
            items: [],
            item: {
                id: 0,
                nombre: '',
            },
            vista_items: true,
            vista_formulario: false,
            texto_buscado: '',
            propiedades_buscadas: [
                'nombre',
            ],

            puede_consultar: true,
            puede_crear: false,
            puede_editar: false,
            puede_eliminar: false,

            id_empleado: 0,
            fecha_inicio: '',
            fecha_fin: '',
            empleados: [],
            eventos: [/*{
                id: 1,
                inicio: '2019-07-17T09:45:00Z',
                fin: '2019-07-17T14:18:00Z',
                nombre: 'CanaKit!',
            }*/],
            cargando_eventos: false,
            mostrar_formulario_plantilla: false,
            mostrar_formulario_quitar: false,
            mostrar_formulario_nuevo: false,
            id_horario_plantilla: 0,
            plantilla_fecha_inicio: '',
            plantilla_fecha_fin: '',
            nuevo_fecha_inicio: '',
            nuevo_hora_inicio: '',
            nuevo_hora_fin: '',
            quitar_fecha_inicio: '',
            quitar_fecha_fin: '',
            plantillas: [],
            formulario_eventos: false,
            eventos_item: [],
            tipos_eventos: [
                {
                    id: 1,
                    nombre: 'Por defecto'
                },
                {
                    id: 2,
                    nombre: 'Sin actividad'
                }
            ],
            nuevo_evento_item_id: 0,
            nuevo_evento_item_tipo: '',
            nuevo_evento_item_comentario: '',
            nuevo_evento_item_horas: '',
            nuevo_evento_item_archivo: '',
        }),

        methods: {
            limpiarItem() {
                this.item.id = 0;
                this.item.nombre = '';
            },

            setItemData(data) {
                this.item.id = data.id;
                this.item.nombre = data.nombre;
            },

            cargarDataAdicional() {
                /*this.$http.get(this.urls.get, {
                    params: {
                        _fuente: this.fuente,
                        _accion: 'cargar',
                    }
                })
                    .then(response => {
                        if (response.status === 200) {
                            this.items_padres = response.data['items'];
                        }
                    });*/
            },

            cargarEmpleados() {
                this.$http.get(this.urls.get, {
                    params: {
                        _fuente: 'Empleado',
                        _accion: 'index',
                    }
                })
                    .then(response => {
                        if (response.status === 200) {
                            const empleados = response.data;
                            let items = [];

                            for (const empleado of empleados) {
                                items.push({
                                    id: empleado.id,
                                    nombre: this.nombresEmpleado(empleado),
                                    subtexto: empleado.dni + ' (' + empleado.num_control + ')',
                                    img: empleado.foto,
                                })
                            }

                            this.empleados = items;
                        }
                    });
            },

            cargarHorarioEmpleado(id_empleado) {
                id_empleado = parseInt(id_empleado) || 0;
                if (!id_empleado) return;

                this.cargando_eventos = true;

                this.$http.get(this.urls.get, {
                    params: {
                        _fuente: 'Horario',
                        _accion: 'cargarHorarioEmpleado',
                        id_empleado: id_empleado,
                        fecha_inicio: this.fecha_inicio,
                        fecha_fin: this.fecha_fin,
                    }
                })
                    .then(response => {
                        if (response.status === 200) {
                            const data = response.data;

                            this.eventos = data['eventos'];
                        }

                        this.cargando_eventos = false;
                    });
            },

            recargarHorarioEmpleado(fecha_inicio, fecha_fin) {
                if (typeof fecha_inicio !== 'undefined') this.fecha_inicio = fecha_inicio;
                if (typeof fecha_fin !== 'undefined') this.fecha_fin = fecha_fin;
                this.cargarHorarioEmpleado(this.id_empleado);
            },

            actualizarHorarioItem(id, inicio, fin) {
                this.$http.post(this.$url_post, {
                    _fuente: this.fuente,
                    _accion: 'actualizarHorarioItem',
                    id_empleado: this.id_empleado,
                    id: id,
                    inicio: inicio,
                    fin: fin,
                })
                    .then(response => {
                        if (response.status === 200) {
                            resultadoSolicitudDefecto(response.data);

                            if (response.data.ok) {

                            }
                        }
                        else if (response.status === 500) {
                            mensajeError('Error de servidor.');
                        }
                    });
            },

            cargarEventos(evento) {
                this.formulario_eventos = !this.formulario_eventos || evento.id != this.nuevo_evento_item_id;

                if (this.formulario_eventos) {
                    this.$http.get(this.urls.get, {
                        params: {
                            _fuente: 'Horario',
                            _accion: 'cargarEventos',
                            id: evento.id,
                        }
                    })
                        .then(response => {
                            if (response.status === 200) {
                                const data = response.data;

                                this.nuevo_evento_item_id = data.id;
                                this.eventos_item = data.eventos;
                            }
                        });
                }
            },

            nombresEmpleado(empleado) {
                let nombres = [];

                if (typeof empleado.primer_nombre === 'string' && empleado.primer_nombre.length) nombres.push(empleado.primer_nombre);
                if (typeof empleado.segundo_nombre === 'string' && empleado.segundo_nombre.length) nombres.push(empleado.segundo_nombre);
                if (typeof empleado.primer_apellido === 'string' && empleado.primer_apellido.length) nombres.push(empleado.primer_apellido);
                if (typeof empleado.segundo_apellido === 'string' && empleado.segundo_apellido.length) nombres.push(empleado.segundo_apellido);

                return nombres.join(' ');
            },

            cargarPlantillas() {
                this.$http.get(this.urls.get, {
                    params: {
                        _fuente: 'HorarioPlantilla',
                        _accion: 'index',
                    }
                })
                    .then(response => {
                        if (response.status === 200) {
                            let items = [];

                            for (const item of response.data) {
                                items.push({
                                    id: item.id,
                                    nombre: item.nombre,
                                });
                            }

                            this.plantillas = items;
                        }
                    });
            },

            mostrarFormularioAsignar() {
                this.mostrar_formulario_plantilla = false;
                this.mostrar_formulario_quitar = false;
                this.mostrar_formulario_nuevo = !this.mostrar_formulario_nuevo;
            },

            mostrarFormularioAsignarPlantilla() {
                this.mostrar_formulario_nuevo = false;
                this.mostrar_formulario_quitar = false;
                this.mostrar_formulario_plantilla = !this.mostrar_formulario_plantilla;

                if (this.mostrar_formulario_plantilla) {
                    this.cargarPlantillas();
                }
            },

            mostrarFormularioQuitar() {
                this.mostrar_formulario_nuevo = false;
                this.mostrar_formulario_plantilla = false;
                this.mostrar_formulario_quitar = !this.mostrar_formulario_quitar;
            },

            asignarPlantilla() {
                if (!parseInt(this.id_horario_plantilla)) {
                    mensajeError('Por favor seleccione la plantilla.');
                    return;
                }

                if (!this.plantilla_fecha_inicio.length || !this.plantilla_fecha_fin.length) {
                    mensajeError('Por favor especifique el rango desde-hasta.');
                    return;
                }

                const inicio = moment(this.plantilla_fecha_inicio, 'DD/MM/YYYY');
                const fin = moment(this.plantilla_fecha_fin, 'DD/MM/YYYY');

                if (fin.isBefore(inicio)) {
                    mensajeError('La fecha Desde es mayor a la fecha Hasta');
                    return;
                }

                const dias = fin.diff(inicio, 'days');
                const mostrar_confirmar = dias > 7;
                
                const self = this;

                if (mostrar_confirmar) {
                    confirmar('¿Está seguro que quiere asignar el horario al empleado?', function () {
                        self.enviarPostAsignarPlantilla();
                    });
                }
                else {
                    self.enviarPostAsignarPlantilla();
                }
            },
            
            enviarPostAsignarPlantilla() {
                this.statusGuardando(true);

                this.$http.post(this.$url_post, {
                    _fuente: this.fuente,
                    _accion: 'asignarPlantilla',
                    id_empleado: this.id_empleado,
                    id_horario_plantilla: this.id_horario_plantilla,
                    fecha_inicio: this.plantilla_fecha_inicio,
                    fecha_fin: this.plantilla_fecha_fin,
                })
                    .then(response => {
                        if (response.status === 200) {
                            resultadoSolicitudDefecto(response.data);

                            if (response.data.ok) {
                                this.recargarHorarioEmpleado();
                            }
                        }
                        else if (response.status === 500) {
                            mensajeError('Error de servidor.');
                        }

                        this.mostrar_formulario_plantilla = false;

                        this.statusGuardando(false);
                    });
            },

            asignarNuevo() {
                if (!parseInt(this.nuevo_fecha_inicio)) {
                    mensajeError('Por favor ingrese la fecha.');
                    return;
                }

                if (!this.nuevo_hora_inicio.length || !this.nuevo_hora_fin.length) {
                    mensajeError('Por favor especifique las horas de inicio y fin.');
                    return;
                }

                const hora_inicio = moment(this.nuevo_hora_inicio, 'hh:mm a');
                const hora_fin = moment(this.nuevo_hora_fin, 'hh:mm a');

                if (hora_fin.isBefore(hora_inicio)) {
                    mensajeError('La hora inicio es mayor a la hora fin');
                    return;
                }

                if (hora_fin.isSame(hora_inicio)) {
                    mensajeError('El rango de tiempo no es válido.');
                    return;
                }

                const self = this;

                self.enviarPostAsignarNuevo();
            },

            enviarPostAsignarNuevo() {
                this.statusGuardando(true);

                this.$http.post(this.$url_post, {
                    _fuente: this.fuente,
                    _accion: 'asignarNuevoHorario',
                    id_empleado: this.id_empleado,
                    fecha: this.nuevo_fecha_inicio,
                    hora_inicio: this.nuevo_hora_inicio,
                    hora_fin: this.nuevo_hora_fin,
                })
                    .then(response => {
                        if (response.status === 200) {
                            resultadoSolicitudDefecto(response.data);

                            if (response.data.ok) {
                                this.recargarHorarioEmpleado();
                            }
                        }
                        else if (response.status === 500) {
                            mensajeError('Error de servidor.');
                        }

                        this.mostrar_formulario_nuevo = false;

                        this.statusGuardando(false);
                    });
            },

            quitarEventos() {
                if (!this.quitar_fecha_inicio.length || !this.quitar_fecha_fin.length) {
                    mensajeError('Por favor especifique el rango desde-hasta.');
                    return;
                }

                const inicio = moment(this.quitar_fecha_inicio, 'DD/MM/YYYY');
                const fin = moment(this.quitar_fecha_fin, 'DD/MM/YYYY');

                if (fin.isBefore(inicio)) {
                    mensajeError('La fecha Desde es mayor a la fecha Hasta');
                    return;
                }

                const self = this;

                confirmar('¿Está seguro que quiere quitar el horario al empleado en el rango seleccionado?', function () {
                    self.enviarPostQuitarEventos();
                });
            },

            enviarPostQuitarEventos() {
                this.statusGuardando(true);

                this.$http.post(this.$url_post, {
                    _fuente: this.fuente,
                    _accion: 'quitarEventos',
                    id_empleado: this.id_empleado,
                    fecha_inicio: this.quitar_fecha_inicio,
                    fecha_fin: this.quitar_fecha_fin,
                })
                    .then(response => {
                        if (response.status === 200) {
                            resultadoSolicitudDefecto(response.data);

                            if (response.data.ok) {
                                this.recargarHorarioEmpleado();
                            }
                        }
                        else if (response.status === 500) {
                            mensajeError('Error de servidor.');
                        }

                        this.mostrar_formulario_quitar = false;

                        this.statusGuardando(false);
                    });
            },

            guardarEventoItem() {
                this.statusGuardando(true);

                const form = this.$refs['frm_nuevo_evento_item'];
                const form_data = new FormData(form);
                const config = { headers: { 'Content-Type': 'multipart/form-data' } };

                this.$http.post(this.$url_post, form_data, config)
                    .then(response => {
                        if (response.status === 200) {
                            resultadoSolicitudDefecto(response.data);

                            if (response.data.ok) {
                                //this.recargarHorarioEmpleado();
                                this.formulario_eventos = false;
                            }
                        }
                        else if (response.status === 500) {
                            mensajeError('Error de servidor.');
                        }

                        this.statusGuardando(false);
                    });
            },


            eliminarEventoItem(evento) {
                const self = this;

                confirmar('¿Está seguro que quiere quitar el evento?', function() {
                    self.$http.post(self.$url_post, {
                        _fuente: 'Horario',
                        _accion: 'eliminarEventoItem',
                        id_horario_evento: evento.id,
                    })
                        .then(response => {
                            if (response.status === 200) {
                                const data = response.data;

                                for (const indice in self.eventos_item) {
                                    if (self.eventos_item.hasOwnProperty(indice)) {
                                        if (self.eventos_item[indice].id == data.id) {
                                            self.eventos_item.splice(indice, 1);
                                            break;
                                        }
                                    }
                                }
                            }
                            else if (response.status === 500) {
                                mensajeError('Error de servidor.');
                            }
                        });
                });
            },

            descTipo(tipo) {
                for (const t_evento of this.tipos_eventos) {
                    if (t_evento.id == tipo) {
                        return t_evento.nombre;
                    }
                }
                return '';
            },

            urlArchivo(item) {
                return /*Vue.prototype*/this.$uploads_doc_dir + item.archivo;
            },
        },

        created() {
            const categoria = this.$route.path.split('/').pop();

            this.puede_consultar = this.tienePermiso(categoria, 'consultar');
            this.puede_crear = this.tienePermiso(categoria, 'crear');
            this.puede_editar = this.tienePermiso(categoria, 'editar');
            this.puede_eliminar = this.tienePermiso(categoria, 'eliminar');
        },

        mounted() {
            //this.cargarData();
            this.cargarEmpleados();
        },
    }
</script>

<style scoped>
    .barra-acciones {
        padding: 20px;
        background-color: #f2f2f2;
    }
</style>