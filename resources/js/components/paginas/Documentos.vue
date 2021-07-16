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
            <h4>{{ row.item.nombre }}</h4>

            <!-- Details -->
            <span class="freelancer-detail-item">

            </span>
        </template>

        <template slot="botones" slot-scope="row">
            <a :href="urlDocumento(row.item)" class="button gray ripple-effect ico" title="Ver documento" target="_blank">
                <i class="icon-feather-external-link"></i>
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

<script>
    export default {
        name: "Documentos.vue",

        props: {
            urls: Object,
            avatar_defecto: String,
            empresas: {
                default: []
            }
        },

        data: () => ({
            fuente: 'Documento',
            titulo_singular: 'Documento',
            titulo_plural: 'Documentos',
            icono: 'icon-feather-file-text',
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

            },

            urlDocumento(item) {
                if (typeof item.archivo !== 'string' || !item.archivo.length) return 'javascript:';
                return Vue.prototype.$uploads_doc_dir + item.archivo;
            },
        },

        mounted() {
            this.cargarData();
        },
    }
</script>

<style scoped>

</style>