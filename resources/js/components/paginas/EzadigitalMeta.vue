<template>
    <crud
            :_titulo_singular="titulo_singular"
            :_titulo_plural="titulo_plural"
            :_icono="icono"
            :_fuente="fuente"
            :_subdirectorio="subdirectorio"
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

            <span class="detail" v-if="typeof row.item.descripcion === 'string' && row.item.descripcion.length">
                {{ row.item.descripcion }}
            </span>
        </template>

        <template slot="formulario">
            <div class="row">

                <!-- Dashboard Box -->
                <div class="col-xl-12">
                    <div class="dashboard-box margin-top-0">

                        <div class="content with-padding padding-bottom-10">
                            <div class="row">
                                <div class="col-xl-5">
                                    <input-texto
                                            v-model="item.nombre"
                                            nombre="nombre"
                                            etiqueta="Nombre de la meta"
                                    ></input-texto>
                                </div>

                                <div class="col-xl-7">
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
        name: "EzadigitalMeta.vue",

        props: {
            urls: Object,
            avatar_defecto: String,
        },

        data: () => ({
            fuente: 'Meta',
            subdirectorio: 'Ezadigital',
            titulo_singular: 'Meta',
            titulo_plural: 'Metas',
            icono: 'icon-line-awesome-crosshairs',
            items: [],
            item: {
                id: 0,
                nombre: '',
                descripcion: '',
                status: 1,
            },
            vista_items: true,
            vista_formulario: false,
            texto_buscado: '',
            propiedades_buscadas: [
                'nombre',
                'descripcion',
            ],
        }),

        methods: {
            limpiarItem() {
                this.item.id = 0;
                this.item.nombre = '';
                this.item.descripcion = '';
                this.item.status = 1;
            },

            setItemData(data) {
                this.item.id = data.id;
                this.item.nombre = data.nombre;
                this.item.descripcion = data.descripcion;
                this.item.status = data.status;
            },
        },

        mounted() {
            this.cargarData();
        },
    }
</script>

<style scoped>

</style>