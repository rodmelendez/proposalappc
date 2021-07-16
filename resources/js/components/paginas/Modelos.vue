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
            <h4><b>{{ row.item.abreviatura }}</b> - {{ row.item.nombre }}</h4>
        </template>

        <template slot="formulario">
            <div class="row">

                <!-- Dashboard Box -->
                <div class="col-xl-12">
                    <div class="dashboard-box margin-top-0">

                        <div class="content with-padding padding-bottom-10">
                            <div class="row">
                                <div class="col-xl-8">
                                    <input-texto
                                            v-model="item.nombre"
                                            nombre="nombre"
                                            etiqueta="Nombre"
                                    ></input-texto>
                                </div>

                                <div class="col-xl-4">
                                    <input-texto
                                            v-model="item.abreviatura"
                                            nombre="abreviatura"
                                            etiqueta="Abreviatura"
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
        name: "Marca",

        props: {
            urls: Object,
            avatar_defecto: String,
        },

        data: () => ({
            fuente: 'ModeloProducto',
            titulo_singular: 'Modelo',
            titulo_plural: 'Modelos',
            icono: 'icon-feather-file',
            items: [],
            item: {
                id: 0,
                nombre: '',
                abreviatura: '',
            },
            propiedades_buscadas: [
                'nombre',
                'abreviatura',
            ],
            atributos: []
        }),

        methods: {
            limpiarItem() {
                this.item.id = 0;
                this.item.nombre = '';
                this.item.abreviatura = '';
            },

            setItemData(data) {
                this.item.id = data.id;
                this.item.nombre = data.nombre;
                this.item.abreviatura = data.abreviatura;
            },
        },

        mounted() {
            this.cargarData();
        },
    }
</script>

<style scoped>

</style>