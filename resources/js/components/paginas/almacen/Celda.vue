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
            <h4><strong>{{ nombreBodega(row.item.id_bodega) }} - {{ nombreDivision(row.item.id_division) }}</strong> - {{ row.item.nombre }}</h4>
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
                                            v-model="item.id_bodega"
                                            nombre="id_bodega"
                                            etiqueta="Bodega"
                                            :items="bodegas"
                                    ></input-seleccion>

                                    <input-seleccion
                                            v-model="item.id_division"
                                            nombre="id_division"
                                            etiqueta="División"
                                            :items="listaDivisiones"
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
            fuente: 'Celda',
            subdirectorio: 'Almacen',
            titulo_singular: 'Celda',
            titulo_plural: 'Celdas',
            icono: 'icon-line-awesome-database',
            items: [],
            item: {
                id: 0,
                id_bodega: 0,
                id_division: 0,
                nombre: '',
            },
            propiedades_buscadas: [
                'nombre',
            ],
            atributos: [],
            bodegas: [],
            divisiones: [],
        }),

        computed: {
            listaDivisiones() {
                let items = [];

                for (const division of this.divisiones) {
                    if (division.id_bodega == this.item.id_bodega) {
                        items.push(division);
                    }
                }

                return items;
            }
        },

        methods: {
            limpiarItem() {
                this.item.id = 0;
                this.item.id_bodega = 0;
                this.item.nombre = '';
                this.item.foto = '';
            },

            setItemData(data) {
                this.item.id = data.id;
                this.item.id_bodega = data.id_bodega;
                this.item.nombre = data.nombre;
                this.item.foto = data.foto;
            },

            cargarDataAdicional() {
                this.$http.get(this.urls.get, {
                    params: {
                        _fuente: 'Celda',
                        _subdirectorio: 'Almacen',
                        _accion: 'listaBodegasDivisiones',
                    }
                })
                    .then(response => {
                        if (response.status === 200) {
                            const data = response.data;

                            if (typeof data === 'object') {
                                this.bodegas = data['bodegas'];
                                this.divisiones = data['divisiones'];
                            }
                        }
                    });
            },

            nombreBodega(id_bodega) {
                for (const bodega of this.bodegas) {
                    if (bodega.id == id_bodega) {
                        return bodega.nombre;
                    }
                }
                return '';
            },

            nombreDivision(id_division) {
                for (const division of this.divisiones) {
                    if (division.id == id_division) {
                        return division.nombre;
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