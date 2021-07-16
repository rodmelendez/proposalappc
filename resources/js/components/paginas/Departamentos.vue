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

            <div class="row">
                <div class="col-sm-12">
                    <!-- Details -->
                    <span class="detail" v-if="row.item.id_sucursal">
                        <span>{{ row.item.empresa }} &gt; {{ row.item.sucursal }}</span>
                    </span>
                </div>
            </div>
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
                                            v-model="item.id_empresa"
                                            nombre="id_empresa"
                                            etiqueta="Empresa"
                                            :items="empresas"
                                    ></input-seleccion>
                                </div>

                                <div class="col-xl-6">
                                    <input-seleccion
                                            v-model="item.id_sucursal"
                                            nombre="id_sucursal"
                                            etiqueta="Sucursal"
                                            :items="sucursalesFiltrados"
                                    ></input-seleccion>
                                </div>

                                <div class="col-xl-5">
                                    <input-texto
                                            v-model="item.nombre"
                                            nombre="nombre"
                                            etiqueta="Nombre"
                                    ></input-texto>
                                </div>

                                <div class="col-xl-2">
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
        name: "Departamentos.vue",

        props: {
            urls: Object,
            avatar_defecto: String,
        },

        data: () => ({
            fuente: 'Departamento',
            titulo_singular: 'Departamento',
            titulo_plural: 'Departamentos',
            icono: 'icon-feather-grid',
            items: [],
            item: {
                id: 0,
                id_empresa: null,
                id_sucursal: null,
                nombre: '',
                tipo: null,
                abreviatura: '',
            },
            vista_items: true,
            vista_formulario: false,
            texto_buscado: '',
            propiedades_buscadas: [
                'nombre',
                'empresa',
                'sucursal',
            ],
            empresas: [],
            sucursales: [],
        }),

        computed: {
            sucursalesFiltrados() {
                if (this.item.id_empresa === null) return [];

                let lista = [];

                for (const item of this.sucursales) {
                    if (item.id_empresa == this.item.id_empresa) {
                        lista.push(item);
                    }
                }

                this.item.id_sucursal = null;

                return lista;
            },
        },

        methods: {
            limpiarItem() {
                this.item.id = 0;
                this.item.id_empresa = null;
                this.item.id_sucursal = null;
                this.item.nombre = '';
                this.item.tipo = null;
                this.item.abreviatura = '';
            },

            setItemData(data) {
                this.item.id = data.id;
                this.item.id_empresa = data.id_empresa;
                this.item.id_sucursal = data.id_empresa;
                this.item.nombre = data.nombre;
                this.item.tipo = data.tipo;
                this.item.abreviatura = data.abreviatura;
            },

            cargarDataAdicional() {
                this.$http.get(this.urls.get, {
                    params: {
                        _fuente: this.fuente,
                        _accion: 'cargarSucursales',
                    }
                })
                    .then(response => {
                        if (response.status === 200) {
                            const data = response.data;

                            if (data.ok) {
                                this.empresas = data['empresas'];
                                this.sucursales = data['sucursales'];
                            }
                        }
                    });
            },

            nombreSucursal(item) {
                if (!item.id_sucursal) return '';

                for (const sucursal of this.sucursales) {
                    if (sucursal.id == item.id_sucursal) {
                        return sucursal.nombre;
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