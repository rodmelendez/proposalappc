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
        <template slot="avatar" slot-scope="row">
            <div class="freelancer-avatar">
                <a href="#"><img class="contain" :src="avatarUrl(row.item)" alt=""></a>
            </div>
        </template>

        <template slot="contenido_item" slot-scope="row">
            <h4>{{ nombreEmpresa(row.item) }}</h4>
        </template>

        <template slot="formulario">
            <div class="row">

                <!-- Dashboard Box -->
                <div class="col-xl-12">
                    <div class="dashboard-box margin-top-0">

                        <div class="content with-padding padding-bottom-10">
                            <div class="row">
                                <div class="col-xl-5">
                                    <input-imagen
                                            v-model="item.logo"
                                            nombre="logo"
                                            etiqueta="Logo"
                                            altura="380"
                                            ref="logo"
                                    ></input-imagen>
                                </div>

                                <div class="col-xl-7">
                                    <div class="row">
                                        <div class="col-xl-12">
                                            <input-texto
                                                    v-model="item.nombre"
                                                    nombre="nombre"
                                                    etiqueta="Nombre de la empresa"
                                            ></input-texto>
                                        </div>

                                        <div class="col-xl-12">
                                            <input-texto
                                                    v-model="item.ubicacion"
                                                    nombre="ubicacion"
                                                    etiqueta="Ubicación"
                                            ></input-texto>
                                        </div>

                                        <div class="col-xl-6">
                                            <input-texto
                                                    v-model="item.telefono"
                                                    nombre="telefono"
                                                    etiqueta="Teléfono"
                                            ></input-texto>
                                        </div>

                                        <div class="col-xl-6">
                                            <input-texto
                                                    v-model="item.website"
                                                    nombre="website"
                                                    etiqueta="Página Web"
                                            ></input-texto>
                                        </div>

                                        <div class="col-xl-6">
                                            <input-color
                                                    v-model="item.color"
                                                    nombre="color"
                                                    etiqueta="Color"
                                            ></input-color>
                                        </div>

                                        <div class="col-xl-6">
                                            <input-color
                                                    v-model="item.color_fondo"
                                                    nombre="color_fondo"
                                                    etiqueta="Color de fondo"
                                            ></input-color>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-12">
                                    <input-texto
                                            v-model="item.descripcion"
                                            nombre="descripcion"
                                            etiqueta="Descripción"
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

<script>
    export default {
        name: "Empresas.vue",

        props: {
            urls: Object,
            avatar_defecto: String,
        },

        data: () => ({
            fuente: 'Empresa',
            titulo_singular: 'Empresa',
            titulo_plural: 'Empresas',
            icono: 'icon-material-outline-business',
            items: [],
            item: {
                id: 0,
                id_empresa: null,
                nombre: '',
                ubicacion: '',
                telefono: '',
                website: '',
                descripcion: '',
                logo: '',
                color: '',
                color_fondo: '',
                tipo: 1,
            },
            vista_items: true,
            vista_formulario: false,
            texto_buscado: '',
            propiedades_buscadas: [
                'nombre',
            ],
        }),

        methods: {
            limpiarItem() {
                this.item.id = 0;
                this.item.id_empresa = null;
                this.item.nombre = '';
                this.item.ubicacion = '';
                this.item.telefono = '';
                this.item.website = '';
                this.item.descripcion = '';
                this.item.logo = '';
                this.item.color = '';
                this.item.color_fondo = '';
                this.item.tipo = 1;
            },

            setItemData(data) {
                this.item.id = data.id;
                this.item.id_empresa = data.id_empresa;
                this.item.nombre = data.nombre;
                this.item.ubicacion = data.ubicacion;
                this.item.telefono = data.telefono;
                this.item.website = data.website;
                this.item.descripcion = data.descripcion;
                this.item.logo = data.logo;
                this.item.color = data.color;
                this.item.color_fondo = data.color_fondo;
                this.item.tipo = data.tipo;
            },

            avatarUrl(item) {
                if (typeof item.logo === 'string' && item.logo.length) {
                    return this.$uploads_img_dir + item.logo; //Vue.prototype.$uploads_img_dir
                }
                return this.avatar_defecto;
            },

            nombreEmpresa(item) {
                if (typeof item.nombre === 'string' && item.nombre.length) {
                    return item.nombre;
                }
                return '';
            },
        },

        mounted() {
            this.cargarData();
        },
    }
</script>

<style scoped>

</style>