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
                    <span class="detail" v-if="row.item.id_departamento">
                        <span>{{ row.item.empresa }} &gt; {{ row.item.sucursal }} &gt; {{ row.item.departamento }}</span>
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
                                <div class="col-xl-4">
                                    <input-seleccion
                                            v-model="item.id_empresa"
                                            nombre="id_empresa"
                                            etiqueta="Empresa"
                                            :items="empresas"
                                            @cambiado="actualizarLista"
                                    ></input-seleccion>
                                </div>

                                <div class="col-xl-4">
                                    <input-seleccion
                                            v-model="item.id_sucursal"
                                            nombre="id_sucursal"
                                            etiqueta="Sucursal"
                                            :items="sucursalesFiltrados"
                                            :indice="indice_sucursales"
                                    ></input-seleccion>
                                </div>

                                <div class="col-xl-4">
                                    <input-seleccion
                                            v-model="item.id_departamento"
                                            nombre="id_departamento"
                                            etiqueta="Departamento"
                                            :items="departamentosFiltrados"
                                            :indice="indice_departamentos"
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
        name: "Subdepartamentos.vue",

        props: {
            urls: Object,
            avatar_defecto: String,
        },

        data: () => ({
            fuente: 'Subdepartamento',
            titulo_singular: 'Sub-departamento',
            titulo_plural: 'Sub-departamentos',
            icono: 'icon-material-outline-layers',
            items: [],
            item: {
                id: 0,
                id_empresa: null,
                id_sucursal: null,
                id_departamento: null,
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
                'departamento',
            ],
            empresas: [],
            sucursales: [],
            departamentos: [],
            indice_sucursales: 0,
            indice_departamentos: 0,
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
                //this.item.id_departamento = null;
                this.indice_sucursales++;
                //this.indice_departamentos++;

                return lista;
            },

            departamentosFiltrados() {
                if (this.item.id_sucursal === null) return [];

                let lista = [];

                for (const item of this.departamentos) {
                    if (item.id_sucursal == this.item.id_sucursal) {
                        lista.push(item);
                    }
                }

                this.item.id_departamento = null;
                this.indice_departamentos++;

                return lista;
            },
        },

        methods: {
            limpiarItem() {
                this.item.id = 0;
                this.item.id_empresa = null;
                this.item.id_sucursal = null;
                this.item.id_departamento = null;
                this.item.nombre = '';
                this.item.tipo = null;
                this.item.abreviatura = '';
            },

            setItemData(data) {
                this.item.id = data.id;
                this.item.id_departamento = data.id_departamento;
                this.item.nombre = data.nombre;
                this.item.tipo = data.tipo;
                this.item.abreviatura = data.abreviatura;
            },

            cargarDataAdicional() {
                this.$http.get(this.urls.get, {
                    params: {
                        _fuente: this.fuente,
                        _accion: 'cargarDepartamentos',
                    }
                })
                    .then(response => {
                        if (response.status === 200) {
                            const data = response.data;

                            if (data.ok) {
                                this.empresas = data['empresas'];
                                this.sucursales = data['sucursales'];
                                this.departamentos = data['departamentos'];
                            }
                        }
                    });
            },

            nombreDepartamento(item) {
                if (!item.id_departamento) return '';

                for (const departamento of this.departamentos) {
                    if (departamento.id == item.id_departamento) {
                        return departamento.nombre;
                    }
                }

                return '';
            },

            actualizarLista() {
                this.indice_sucursales++;
                this.indice_departamentos++;
            },
        },

        mounted() {
            this.cargarData();
        },
    }
</script>

<style scoped>

</style>