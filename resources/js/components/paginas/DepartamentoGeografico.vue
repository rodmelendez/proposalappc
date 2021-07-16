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
            <span class="freelancer-detail-item" v-if="row.item.id_pais">
                <span><b>Ubicado en:</b> {{row.item.pais}} </span>
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
                                <div class="col-xl-8">
                                    <input-objeto
                                        v-model="item.pais.id"
                                        nombre="id_pais"
                                        etiqueta="Pais"
                                        fuente="IntranetPais"
                                        :items="paises"
                                        :url="urls.post"
                                        @guardado="actualizarObjetoPais"
                                    >
                                        <template slot="modal">
                                            <div class="row">
                                                <div class="col-xl-8">
                                                    <input-texto
                                                            v-model="item.pais.nombre"
                                                            nombre="nombre"
                                                            etiqueta="Nombre"
                                                    ></input-texto>
                                                </div>

                                                <div class="col-xl-4">
                                                    <input-texto
                                                            v-model="item.pais.abreviatura"
                                                            nombre="abreviatura"
                                                            etiqueta="Abreviatura"
                                                    ></input-texto>
                                                </div>
                                            </div>
                                        </template>
                                    </input-objeto>
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
        name: "Paises",

        props: {
            urls: Object,
            avatar_defecto: String,
        },

        data: () => ({
            fuente: 'IntranetDepartamento',
            titulo_singular: 'Departamento',
            titulo_plural: 'Departamentos',
            icono: 'icon-line-awesome-building',
            items: [],
            item: {
                id: 0,
                nombre: '',
                abreviatura: '',
                id_pais: null,
                pais:{
                    id:0,
                    nombre: '',
                    abreviatura: ''
                }
            },
            vista_items: true,
            vista_formulario: false,
            texto_buscado: '',
            propiedades_buscadas: [
                'nombre',
                'abreviatura',
                'pais'
            ],
            atributos: [],
            paises: []
        }),

        methods: {

            actualizarObjetoPais(data) {
                this.pais.push({
                    id: data.id,
                    nombre: this.item.pais.nombre,
                    abreviatura: this.item.pais.abreviatura.toUpperCase(),
                });
                this.item.pais.id = data.id;
            },

            debug(o){
                console.log(o)
            },

            limpiarItem() {
                this.item.id = 0;
                this.item.nombre = '';
                this.item.abreviatura = '';
                this.item.pais = '';
                this.item.id_pais = null;

                this.item.pais = {
                    id: 0,
                    nombre: '',
                    abreviatura: ''
                }
            },

            setItemData(data) {
                //console.log({hola: 'aaaaa', data});
                this.item.id = data.id;
                this.item.nombre = data.nombre;
                this.item.abreviatura = data.abreviatura;
                //this.item.id_pais = parseInt(data.id_pais);
                this.item.pais.id = parseInt(data.id_pais);

            },

            cargarDataAdicional(){
                const self = this;
                this.$http.get(this.urls.get, {
                    params: {
                        _fuente: this.fuente,
                        _accion: 'cargarListados',
                    }
                })
                .then(response => {
                    self.debugResponse(response);
                    if (response.status === 200) {
                        const data = response.data;
                        this.paises = data["paises"];
                    }
                })
                .catch(err => {
                    self.debugError(err);
                });
            },

            actualizarObjetoPais(data) {
                this.paises.push({
                    id: data.id,
                    nombre: this.item.pais.nombre,
                    abreviatura: '',
                });

                this.item.pais.id = data.id;
            },

        },

        mounted() {
            this.cargarData();
        },
    }
</script>

<style scoped>

</style>