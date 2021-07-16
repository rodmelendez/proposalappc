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
        </template>

        <template slot="formulario">
            <div class="row">

                <!-- Dashboard Box -->
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

                                    <input-texto
                                            v-model="item.dni"
                                            nombre="dni"
                                            etiqueta="DNI"
                                    ></input-texto>

                                    <input-texto
                                            v-model="item.telefono"
                                            nombre="telefono"
                                            etiqueta="Teléfono"
                                    ></input-texto>

                                    <input-texto
                                            v-model="item.direccion"
                                            nombre="direccion"
                                            etiqueta="Dirección"
                                    ></input-texto>
                                </div>

                                <div class="col-xl-6">
                                    <input-imagen
                                            v-model="item.foto"
                                            nombre="foto"
                                            etiqueta="Foto"
                                            altura="380"
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
        name: "Proveedor",

        props: {
            urls: Object,
            avatar_defecto: String,
        },

        data: () => ({
            fuente: 'Proveedor',
            subdirectorio: 'Almacen',
            titulo_singular: 'Proveedor',
            titulo_plural: 'Proveedores',
            icono: 'icon-line-awesome-truck',
            items: [],
            item: {
                id: 0,
                nombre: '',
            },
            propiedades_buscadas: [
                'nombre',
                'dni',
            ],
            atributos: []
        }),

        methods: {
            limpiarItem() {
                this.item.id = 0;
                this.item.nombre = '';
                this.item.dni = '';
                this.item.telefono = '';
                this.item.direccion = '';
                this.item.foto = '';
            },

            setItemData(data) {
                this.item.id = data.id;
                this.item.nombre = data.nombre;
                this.item.dni = data.dni;
                this.item.telefono = data.telefono;
                this.item.direccion = data.direccion;
                this.item.foto = data.foto;
            },
        },

        mounted() {
            this.cargarData();
        },
    }
</script>

<style scoped>

</style>