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
            <h4>
                {{ titulo(row.item.titulo) }} <span v-html="iconoStatus(row.item)"></span>
            </h4>

            <!-- Details -->
            <span class="freelancer-detail-item">
                <span><b>Fecha:</b> <span v-html="fecha(row.item.fecha_activacion)"></span></span>
            </span>
        </template>

        <template slot="formulario">
            <div class="row">

                <div class="col-xl-12">
                    <div class="dashboard-box margin-top-0">

                        <div class="content with-padding padding-bottom-10">
                            <div class="row">
                                <div class="col-xl-6">
                                    <input-texto
                                            v-model="item.titulo"
                                            nombre="titulo"
                                            etiqueta="Título"
                                    ></input-texto>

                                    <input-editor
                                            v-model="item.contenido"
                                            nombre="contenido"
                                            etiqueta="Contenido"
                                    ></input-editor>
                                </div>

                                <div class="col-xl-6">
                                    <input-imagen
                                            v-model="item.imagen"
                                            nombre="imagen"
                                            etiqueta="Imagen"
                                    ></input-imagen>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xl-6">
                                    <template v-show="empresas.length">
                                        <input-seleccion
                                                v-model="item.empresas"
                                                nombre="id_empresa"
                                                etiqueta="Mostrar a empleados de empresa"
                                                placeholder="Deje vacío para todas las empresas"
                                                :multiple="true"
                                                :items="empresas"
                                        ></input-seleccion>
                                    </template>
                                </div>

                                <div class="col-xl-3">
                                    <input-fecha
                                            v-model="item.fecha_activacion"
                                            nombre="fecha_activacion"
                                            etiqueta="Fecha de publicación"
                                    ></input-fecha>
                                </div>

                                <div class="col-xl-3">
                                    <input-fecha
                                            v-model="item.fecha_vencimiento"
                                            nombre="fecha_vencimiento"
                                            etiqueta="Fecha de vencimiento"
                                    ></input-fecha>
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
        name: "Comunicados.vue",

        props: {
            urls: Object,
            avatar_defecto: String,
            empresas: {
                default: []
            }
        },

        data: () => ({
            fuente: 'Comunicado',
            titulo_singular: 'Comunicado',
            titulo_plural: 'Comunicados',
            icono: 'icon-line-awesome-comments',
            items: [],
            item: {
                id: 0,
                titulo: '',
                contenido: '',
                imagen: '',
                empresas: [],
                fecha_activacion: '',
                fecha_vencimiento: '',
            },
            vista_items: true,
            vista_formulario: false,
            texto_buscado: '',
            propiedades_buscadas: [
                'titulo',
            ],
        }),

        methods: {

            limpiarItem() {
                this.item.id = 0;
                this.item.titulo = '';
                this.item.contenido = '';
                this.item.imagen = '';
                this.item.empresas = this.empresas.length ? [this.empresas[0].id] : [];
                this.fecha_activacion = '';
                this.fecha_vencimiento = '';
            },

            setItemData(data) {
                this.item.id = data.id;
                this.item.titulo = data.titulo;
                this.item.contenido = data.data;
                this.item.imagen = data.imagen;
                this.item.empresas = data.empresas;
                this.item.fecha_activacion = formatoFechaApp(data.fecha_activacion);
                this.item.fecha_vencimiento = formatoFechaApp(data.fecha_vencimiento);
            },

            cargarDataAdicional() {
                /*this.$http.get(this.urls.get, {
                    params: {
                        _fuente: this.fuente,
                        _accion: 'cargarSucursales',
                    }
                })
                    .then(response => {
                        if (response.status === 200) {
                            const data = response.data;

                            if (data.ok) {
                                this.sucursales = data['sucursales'];
                            }
                        }
                    });*/
            },

            titulo(str) {
                if (typeof str !== 'string') return '';
                if (str.length > 100) {
                    return str.substr(0, 97) + '...';
                }
                return str;
            },

            fecha(val) {
                const fecha = moment(val, 'YYYY-MM-DD HH:mm:ss');
                /*ilet icon = '';
                f (fecha.isAfter()) {
                    icon = '&nbsp; <i class="icon-line-awesome-clock-o"></i>';
                }*/
                return fecha.format('LL')/* + icon*/;
            },

            iconoStatus(item) {
                const fecha = moment(item.fecha_activacion, 'YYYY-MM-DD HH:mm:ss');
                let icon = '';
                if (fecha.isAfter()) {
                    icon = '&nbsp; <i class="icon-feather-clock"></i>';
                }
                return icon;
            },
        },

        mounted() {
            this.cargarData();
        },
    }
</script>

<style scoped>

</style>