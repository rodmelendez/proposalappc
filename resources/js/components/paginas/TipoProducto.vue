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
            <h4><strong>{{ row.item.abreviatura }}</strong> - {{ row.item.nombre }}</h4>

            <!-- Details -->
            <span class="freelancer-detail-item" v-if="row.item.lista_atributos">
                <span><b>Atributos:</b> {{ row.item.lista_atributos }}</span>
            </span>
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

                            <div class="row">
                                <div class="col-xl-12">
                                    <input-seleccion
                                            v-model="item.atributo"
                                            nombre="atributos"
                                            etiqueta="Atributos"
                                            :items="atributos"
                                            :multiple="true"
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
        name: "TipoProducto",

        props: {
            urls: Object,
            avatar_defecto: String,
        },

        data: () => ({
            fuente: 'TipoProducto',
            titulo_singular: 'Tipo de producto',
            titulo_plural: 'Tipos de productos',
            icono: 'icon-feather-file',
            items: [],
            item: {
                id: 0,
                nombre: '',
                abreviatura: '',
                atributo: ''
            },
            vista_items: true,
            vista_formulario: false,
            texto_buscado: '',
            propiedades_buscadas: [
                'nombre',
                'abreviatura',
            ],
            texto_confirmar_eliminar: '¿Está seguro que quiere eliminar el tipo de producto?',
            atributos: [],
        }),

        methods: {
            limpiarItem() {
                this.item.id = 0;
                this.item.nombre = '';
                this.item.abreviatura = '';
                this.item.atributo = '';
            },

            setItemData(data) {
                this.item.id = data.id;
                this.item.nombre = data.nombre;
                this.item.abreviatura = data.abreviatura;
                this.item.atributo = data.atributos;
            },

            cargarDataAdicional() {
                this.$http.get(this.urls.get, {
                    params: {
                        _fuente: this.fuente,
                        _accion: 'cargarAtributos',
                    }
                })
                    .then(response => {
                        if (response.status === 200) {
                            const data = response.data;

                            this.atributos = data['atributos'];
                        }
                    });
            }
        },

        mounted() {
            this.cargarData();
        },
    }
</script>

<style scoped>

</style>