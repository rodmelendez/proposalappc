<template>
    <main>
        <template>
            <crud
                    :_titulo_singular="titulo_singular"
                    :_titulo_plural="titulo_plural"
                    :_icono="icono"
                    :_fuente="fuente"
                    :_propiedades_buscadas="propiedades_buscadas"
                    :_items="items"
                    :_item="item"
                    :_vista_items="vista_items"
                    :_vista_formulario="vista_formulario"
                    _tipo_vista="compacto"
                    :urls="urls"
                    @itemDataSet="itemDataSet"
                    @postItemEliminado="postItemEliminado"
                    @postCargarData="postCargarData"
                    @formularioMostrado="mostrarFormularioNuevo"
                    @formularioEditarMostrado="mostrarFormulario"
                    :key="instance"
            >
                <template slot="contenido_item" slot-scope="row">
                    <h4>
                        {{ row.item.nombre }}
                    </h4>

                    <!-- Details -->
                    <span class="freelancer-detail-item">

                    </span>
                </template>

                <template slot="botones" slot-scope="row">
                    <a class="button gray ripple-effect ico detail" title="Ver horario" @click="verCalendario(row.item)">
                        <i class="icon-feather-calendar"></i>
                    </a>
                </template>

                <template slot="formulario">
                    <div class="row">

                        <div class="col-xl-12">
                            <div class="dashboard-box margin-top-0">

                                <div class="content with-padding padding-bottom-10">
                                    <div class="row">
                                        <div class="col-xl-6">
                                            <input-texto
                                                    v-model="item.nombre"
                                                    nombre="nombre"
                                                    etiqueta="Nombre"
                                            ></input-texto>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </template>
            </crud>
        </template>

        <template v-if="vista_calendario">
            <!-- Dashboard Headline -->
            <div class="dashboard-headline">
                <div class="row">
                    <div class="col-sm-8">
                        <h3>
                            <button type="button" class="button" @click="ocultarFormulario">
                                <i class="icon-material-outline-arrow-back"></i>
                            </button>

                            Horario
                            <span class="item-cargando">&nbsp;<i class="icon-line-awesome-hourglass-2 fa-spin"></i></span>
                        </h3>
                    </div>
                </div>
            </div>

            <div class="dashboard-box margin-top-0">

                <!-- Headline -->
                <div class="headline">
                    <h3>{{ item_seleccionado.nombre }}</h3>

                    <div class="actions">
                        <button type="button" class="popup-with-zoom-anim button dark ripple-effect" @click="mostrarFormularioNuevoEvento">
                            <i class="icon-feather-plus"></i> Nuevo
                        </button>
                    </div>

                    <div class="frm-agregar-evento clearfix" v-if="agregar_nuevo_item">
                        <div class="row">
                            <div class="col-sm-4">
                                <input-seleccion
                                        v-model="item_evento.dia"
                                        nombre="dia"
                                        etiqueta="Día"
                                        :items="dias"
                                ></input-seleccion>
                            </div>

                            <div class="col-sm-3">
                                <input-hora
                                        v-model="item_evento.hora_inicio"
                                        nombre="hora_inicio"
                                        etiqueta="Inicio"
                                ></input-hora>
                            </div>

                            <div class="col-sm-3">
                                <input-hora
                                        v-model="item_evento.hora_fin"
                                        nombre="hora_fin"
                                        etiqueta="Fin"
                                ></input-hora>
                            </div>

                            <div class="col-sm-2">
                                <div class="submit-field" v-if="!item_evento._key">
                                    <h5>&nbsp;</h5>
                                    <button type="button" class="button big ico" @click="agregarEvento">
                                        <i class="icon-feather-plus"></i>
                                    </button>
                                </div>

                                <div class="submit-field" v-else="item_evento._key">
                                    <h5>&nbsp;</h5>
                                    <button type="button" class="button big ico gray " @click="agregarEvento">
                                        <i class="icon-material-outline-check"></i>
                                    </button>

                                    <button type="button" class="button big ico red" @click="quitarEvento">
                                        <i class="icon-feather-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="content">
                    <vue-cal style="height: 1050px; background-color: #fff;"
                             locale="es"
                             default-view="week"
                             hide-view-selector
                             hide-title-bar
                             :disable-views="['years', 'year', 'month', 'day']"
                             :dblClickToNavigate="false"
                             minDate="2018-01-01"
                             maxDate="2018-01-07"
                             selectedDate="2018-01-01"
                             :12Hour="true"
                             :events="eventos"
                             :on-event-create="onEventCreate"
                             :on-event-click="onEventClick"
                    ></vue-cal>
                    <!--editable-events-->
                </div>
            </div>

            <a href="#" class="button ripple-effect button-sliding-icon big margin-top-30" @click="guardarEventos">
                <span class="item-guardar">Guardar <i class="icon-feather-save"></i></span>
                <span class="item-guardando">Guardando... <i class="icon-line-awesome-hourglass-2 fa-spin"></i></span>
            </a>
        </template>
    </main>
</template>

<script>
    Vue.component('vue-cal', require('vue-cal'));
    import 'vue-cal/dist/vuecal.css'

    export default {
        name: "HorarioPlantilla.vue",

        props: {
            urls: Object,
            avatar_defecto: String,
            empresas: {
                default: []
            }
        },

        data: () => ({
            fuente: 'HorarioPlantilla',
            titulo_singular: 'Plantilla de Horario',
            titulo_plural: 'Plantillas de Horario',
            icono: 'icon-material-outline-date-range',
            items: [],
            item: {
                id: 0,
                nombre: '',
            },
            vista_items: true,
            vista_formulario: false,
            vista_calendario: false,
            texto_buscado: '',
            propiedades_buscadas: [
                'nombre',
            ],

            puede_consultar: true,
            puede_crear: false,
            puede_editar: false,
            puede_eliminar: false,

            instance: 0,

            eventos: [/*
                {
                    start: '2018-01-02 13:30:00',
                    end: '2018-01-02 16:25:00',
                    title: 'Golf with John',
                    icon: 'golf_course', // Custom attribute.
                    content: 'Do I need to tell how many holes?',
                    contentFull: 'Okay.<br>It will be a 18 hole golf course.', // Custom attribute.
                    class: 'sport'
                }
            */],

            item_evento: {
                _key: 0,
                dia: 1,
                hora_inicio: '',
                hora_fin: '',
            },

            item_seleccionado: {
                id: 0,
                nombre: '',
            },

            dias: [
                { id: 1, nombre: 'Lunes' },
                { id: 2, nombre: 'Martes' },
                { id: 3, nombre: 'Miércoles' },
                { id: 4, nombre: 'Jueves' },
                { id: 5, nombre: 'Viernes' },
                { id: 6, nombre: 'Sábado' },
                { id: 7, nombre: 'Domnigo' },
            ],

            agregar_nuevo_item: false,
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

            cargarHorario(id_horario_plantilla, ) {
                this.$http.get(this.urls.get, {
                    params: {
                        _fuente: this.fuente,
                        _accion: 'cargarHorario',
                        id_horario_plantilla: id_horario_plantilla,
                    }
                })
                    .then(response => {
                        if (response.status === 200) {
                            const data = response.data;

                            if (data.ok) {
                                let items = data['items'];

                                for (const index in items) {
                                    if (items.hasOwnProperty(index)) {
                                        const item = items[index];

                                        items[index]._key = uniqueNumber();
                                        items[index].start = '2018-01-0' + item.dia + ' ' + item.hora_inicio;
                                        items[index].end = '2018-01-0' + item.dia + ' ' + item.hora_fin;
                                    }
                                }

                                this.eventos = items;

                                this.statusCargando(false);
                            }
                        }
                    });
            },

            verCalendario(item) {
                this.vista_formulario = false;
                this.vista_items = false;
                this.vista_calendario = true;

                this.item_seleccionado.id = item.id;
                this.item_seleccionado.nombre = item.nombre;

                this.statusCargando(true);

                this.cargarHorario(item.id);

                setTimeout(() => {
                    this.accionesCalendario();
                }, 500);

                this.instance++;
            },

            ocultarFormulario() {
                this.vista_formulario = false;
                this.vista_calendario = false;
                this.vista_items = true;
            },

            limpiarItemEvento() {
                this.item_evento = {
                    _key: 0,
                    dia: 1,
                    hora_inicio: '',
                    hora_fin: '',
                };
            },

            mostrarFormularioNuevoEvento() {
                const editando = !!this.item_evento._key;

                this.limpiarItemEvento();

                this.agregar_nuevo_item = editando || !this.agregar_nuevo_item;
            },

            agregarEvento() {
                if (!this.item_evento.hora_inicio.length || !this.item_evento.hora_fin.length) {
                    return;
                }

                let hora_inicio = moment(this.item_evento.hora_inicio, 'hh:mm a');
                let hora_fin = moment(this.item_evento.hora_fin, 'hh:mm a');

                if (hora_fin.isSame(hora_inicio)) {
                    hora_fin.add(1, 'hour');
                }
                else if (hora_inicio.isAfter(hora_inicio)) {
                    const tmp = hora_inicio;
                    hora_fin = hora_inicio;
                    hora_inicio = tmp;
                }

                const inicio = '2018-01-0' + this.item_evento.dia + ' ' + hora_inicio.format('HH:mm:00');
                const fin = '2018-01-0' + this.item_evento.dia + ' ' + hora_fin.format('HH:mm:00');

                if (!this.item_evento._key) {
                    const evento = {
                        title: '',
                        start: inicio,
                        end: fin,

                        _key: uniqueNumber(),
                        dia: this.item_evento.dia,
                        hora_inicio: hora_inicio.format('HH:mm:00'),
                        hora_fin: hora_fin.format('HH:mm:00'),
                    };

                    this.eventos.push(evento);

                } else {
                    for (const index in this.eventos) {
                        if (this.eventos.hasOwnProperty(index)) {
                            if (this.eventos[index]._key === this.item_evento._key) {
                                this.eventos[index].start = inicio;
                                this.eventos[index].end = fin;

                                this.eventos[index].dia = this.item_evento.dia;
                                this.eventos[index].hora_inicio = hora_inicio.format('HH:mm:00');
                                this.eventos[index].hora_fin = hora_fin.format('HH:mm:00');

                                this.agregar_nuevo_item = false;
                                break;
                            }
                        }
                    }
                }

                if (this.item_evento.dia < 7) {
                    this.item_evento.dia++;
                }
                else {
                    this.limpiarItemEvento();
                }
            },

            quitarEvento() {
                for (const index in this.eventos) {
                    if (this.eventos.hasOwnProperty(index)) {
                        if (this.eventos[index]._key === this.item_evento._key) {
                            this.eventos.splice(index, 1);
                            this.limpiarItemEvento();
                            this.agregar_nuevo_item = false;
                            break;
                        }
                    }
                }
            },

            onEventCreate (event/*, deleteEventFunction*/) {
                event.title = '';
                return event;
            },

            onEventClick (event) {
                this.item_evento._key = event._key;
                this.item_evento.dia = event.dia;
                //this.item_evento.hora_inicio = moment(event.hora_inicio, 'HH:mm:ss').format('hh:mm a');
                this.item_evento.hora_inicio = moment(event.start, 'YYYY-MM-DD HH:mm:ss').format('hh:mm a');
                //this.item_evento.hora_fin = moment(event.hora_fin, 'HH:mm:ss').format('hh:mm a');
                this.item_evento.hora_fin = moment(event.end, 'YYYY-MM-DD HH:mm:ss').format('hh:mm a');

                this.agregar_nuevo_item = true;
            },

            accionesCalendario() {
                /*const self = this;

                $('.vuecal__cell').on('click', function(e) {
                    if (!$(e.target).hasClass('vuecal__cell-content')) {
                        return;
                    }
                    const $el = $(this);
                    const offset = $el.offset();
                    //const x = Math.round(e.clientX - offset.left);
                    const dia = $(this).index() + 1;
                    const y = Math.round(e.clientY - offset.top + $(window).scrollTop());

                    const hora_inicio = Math.round(y / 40);
                    const hora_fin = Math.min(24, hora_inicio + 2);

                    self.eventos.push({
                        start: '2018-01-0' + dia + ' ' + (hora_inicio < 10 ? ('0' + hora_inicio) : hora_inicio) + ':00:00',
                        end: '2018-01-0' + dia + ' ' + (hora_fin === 24 ? '23:59:59' : ((hora_fin < 10 ? ('0' + hora_fin) : hora_fin) + ':00:00')),
                        title: '',
                    });
                });

                $('.vuecal__cell-events').on('dragstart', '.vuecal__event', function() {
                    console.log('drag');
                });*/
            },

            guardarEventos() {
                this.$http.post(this.$url_post, {
                    _fuente: this.fuente,
                    _accion: 'guardarEventos',
                    id_horario_plantilla: this.item_seleccionado.id,
                    items: JSON.stringify(this.eventos),
                })
                    .then(response => {
                        if (response.status === 200) {
                            const data = response.data;

                            resultadoSolicitudDefecto(data);

                            if (data.ok) {

                            }
                        }
                        else if (response.status === 500) {
                            mensajeError('Error de servidor.');
                        }
                    });
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
            this.cargarData();
        },
    }
</script>

<style>
    .vuecal__heading span span:last-child {
        display: none;
    }

    .vuecal__no-event {
        display: none;
    }

    .vuecal__event {
        background-color: #2a41e8;
        border: 1px solid #fff;
        color: #fff;
    }

    .vuecal__cell.selected {
        background-color: transparent;
    }
</style>