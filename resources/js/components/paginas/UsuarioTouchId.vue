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
    >
        <template slot="contenido_item" slot-scope="row">
            <h4><strong>{{ nombresEmpleado(row.item) }}</strong> - IMEI: {{ row.item.imei }} / Serial: {{ row.item.serial_dispositivo }}</h4>
        </template>

        <template slot="formulario">
            <div class="row">

                <!-- Dashboard Box -->
                <div class="col-xl-12">
                    <div class="dashboard-box margin-top-0">

                        <div class="content with-padding padding-bottom-10">
                            <div class="row">
                                <div class="col-xl-3">
                                    <input-texto
                                            v-model="item.imei"
                                            nombre="imei"
                                            etiqueta="IMEI"
                                    ></input-texto>
                                </div>

                                <div class="col-xl-3">
                                    <input-texto
                                            v-model="item.serial_dispositivo"
                                            nombre="serial_dispositivo"
                                            etiqueta="Serial"
                                    ></input-texto>
                                </div>

                                <div class="col-xl-6">
                                    <input-seleccion
                                            v-model="id_empleado"
                                            nombre="id_empleado"
                                            etiqueta="Asignado a"
                                            :items="empleados"
                                            plantilla="avatar"
                                    ></input-seleccion>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </template>
    </crud>
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
            fuente: 'UsuarioTouchId',
            titulo_singular: 'Dispositivo Asignado',
            titulo_plural: 'Dispositivos Asignados',
            icono: 'icon-feather-tablet',
            items: [],
            item: {
                id: 0,
                imei: '',
                serial_dispositivo: '',
            },
            vista_items: true,
            vista_formulario: false,
            texto_buscado: '',
            propiedades_buscadas: [
                'imei',
                'serial_dispositivo',
            ],

            puede_consultar: true,
            puede_crear: false,
            puede_editar: false,
            puede_eliminar: false,

            id_empleado: null,
            empleados: [],
        }),

        methods: {
            limpiarItem() {
                this.item.id = 0;
                this.item.imei = '';
                this.item.serial_dispositivo = '';
            },

            setItemData(data) {
                this.item.id = data.id;
                this.item.imei = data.imei;
                this.item.serial_dispositivo = data.serial_dispositivo;
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
                                    subtexto: empleado.dni + (empleado.num_control !== null ? (' (' + empleado.num_control + ')') : ''),
                                    img: empleado.foto,
                                })
                            }

                            this.empleados = items;
                        }
                    });
            },

            nombresEmpleado(empleado) {
                let nombres = [];

                if (typeof empleado.primer_nombre === 'string' && empleado.primer_nombre.length) nombres.push(empleado.primer_nombre);
                if (typeof empleado.segundo_nombre === 'string' && empleado.segundo_nombre.length) nombres.push(empleado.segundo_nombre);
                if (typeof empleado.primer_apellido === 'string' && empleado.primer_apellido.length) nombres.push(empleado.primer_apellido);
                if (typeof empleado.segundo_apellido === 'string' && empleado.segundo_apellido.length) nombres.push(empleado.segundo_apellido);

                return nombres.join(' ');
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
            this.cargarEmpleados();
        },
    }
</script>

<style scoped>

</style>