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
            <h4><strong>{{ nombreCategoria(row.item.id_categoria) }}</strong> - {{ row.item.nombre }}</h4>
        </template>

        <template slot="formulario">
            <div class="row">

                <!-- Dashboard Box -->
                <div class="col-xl-12">
                    <div class="dashboard-box margin-top-0">

                        <div class="content with-padding padding-bottom-10">
                            <div class="row">
                                <div class="col-xl-6">
                                    <input-seleccion
                                            v-model="item.id_categoria"
                                            nombre="id_categoria"
                                            etiqueta="Categoría"
                                            :items="categorias"
                                    ></input-seleccion>

                                    <input-texto
                                            v-model="item.nombre"
                                            nombre="nombre"
                                            etiqueta="Nombre"
                                    ></input-texto>
                                </div>

                                <div class="col-xl-6">
                                    <input-imagen
                                            v-model="item.foto"
                                            nombre="foto"
                                            etiqueta="Foto"
                                            altura="300"
                                    ></input-imagen>
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
        name: "Modelo",

        props: {
            urls: Object,
            avatar_defecto: String,
        },

        data: () => ({
            fuente: 'Subcategoria',
            subdirectorio: 'Almacen',
            titulo_singular: 'Sub-Categoría',
            titulo_plural: 'Sub-categorías',
            icono: 'icon-line-awesome-columns',
            items: [],
            item: {
                id: 0,
                id_categoria: 0,
                nombre: '',
            },
            propiedades_buscadas: [
                'nombre',
            ],
            atributos: [],
            categorias: [],
        }),

        methods: {
            limpiarItem() {
                this.item.id = 0;
                this.item.id_categoria = 0;
                this.item.nombre = '';
                this.item.foto = '';
            },

            setItemData(data) {
                this.item.id = data.id;
                this.item.id_categoria = data.id_categoria;
                this.item.nombre = data.nombre;
                this.item.foto = data.foto;
            },

            cargarDataAdicional() {
                this.$http.get(this.urls.get, {
                    params: {
                        _fuente: 'Categoria',
                        _subdirectorio: 'Almacen',
                        _accion: 'index',
                    }
                })
                    .then(response => {
                        if (response.status === 200) {
                            const data = response.data;

                            if (typeof data === 'object' && data instanceof Array) {
                                this.categorias = data;
                            }
                        }
                    });
            },

            nombreCategoria(id_categoria) {
                for (const categoria of this.categorias) {
                    if (categoria.id == id_categoria) {
                        return categoria.nombre;
                    }
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