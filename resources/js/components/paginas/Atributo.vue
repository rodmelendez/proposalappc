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
            <span class="freelancer-detail-item" v-if="row.item.tipo">
                <span><b>Tipo:</b> {{ nombreTipo(row.item.tipo) }}</span>
            </span>
        </template>

        <template slot="formulario">
            <div class="row">

                <!-- Dashboard Box -->
                <div class="col-xl-12">
                    <div class="dashboard-box margin-top-0">

                        <div class="content with-padding padding-bottom-10">
                            <div class="row">
                                <div class="col-xl-7">
                                    <input-texto
                                            v-model="item.nombre"
                                            nombre="nombre"
                                            etiqueta="Nombre"
                                    ></input-texto>
                                </div>

                                <div class="col-xl-5">
                                    <input-seleccion
                                            v-model="item.tipo"
                                            nombre="tipo"
                                            etiqueta="Tipo"
                                            :buscador="false"
                                            :items="tipos"
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
        name: "Atributo",

        props: {
            urls: Object,
            avatar_defecto: String,
        },

        data: () => ({
            fuente: 'Atributo',
            titulo_singular: 'Atributo',
            titulo_plural: 'Atributos',
            icono: 'icon-line-awesome-list-alt',
            items: [],
            item: {
                id: 0,
                nombre: '',
                tipo: null,
            },
            vista_items: true,
            vista_formulario: false,
            texto_buscado: '',
            propiedades_buscadas: [
                'nombre',
            ],
            texto_confirmar_eliminar: '¿Está seguro que quiere eliminar el atributo?',
            tipos: [
                {
                    id: 1,
                    nombre: 'Texto'
                },
                {
                    id: 2,
                    nombre: 'Check'
                }
            ],
        }),

        methods: {
            limpiarItem() {
                this.item.id = 0;
                this.item.nombre = '';
                this.item.tipo = null;
            },

            setItemData(data) {
                this.item.id = data.id;
                this.item.nombre = data.nombre;
                this.item.tipo = data.tipo;
            },

            nombreTipo(id_tipo) {
                for (const prop in this.tipos) {
                    if (this.tipos.hasOwnProperty(prop)) {
                        if (this.tipos[prop].id === id_tipo) {
                            return this.tipos[prop].nombre;
                        }
                    }
                }
                return '';
            }
        },

        mounted() {
            this.cargarData();
        },
    }
</script>

<style scoped>

</style>