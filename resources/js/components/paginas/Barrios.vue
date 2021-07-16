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
            <span class="freelancer-detail-item" v-if="row.item.id">
                <span><b>Ubicado en:</b> {{row.item.pais}} </span><br>
                <span><b>Departamento:</b> {{row.item.departamento}} </span><br>
                <span><b>Municipio:</b> {{row.item.municipio}} </span>
            </span>
        
        </template>

        <template slot="formulario">
            <div class="row">

                <!-- Dashboard Box -->
                <div class="col-xl-12">
                    <div class="dashboard-box margin-top-0">
                        <div class="content with-padding padding-bottom-10">
                            <div class="row">

                                <div class="col-xl-12 col-md-4">
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

                                <div class="col-xl-12 col-md-4">
                                    <input-objeto
                                        v-model="item.pais.departamento.id"
                                        nombre="id_departamento"
                                        etiqueta="Depatarmentos"
                                        fuente="IntranetDepartamento"
                                        :items="departamentosDePais"
                                        :url="urls.post"
                                        @guardado="actualizarObjetoDepartamento"
                                    >
                                        <template slot="modal">
                                            <div class="row">
                                                <div class="col-xl-8">
                                                    <input-texto
                                                            v-model="item.pais.departamento.nombre"
                                                            nombre="nombre"
                                                            etiqueta="Nombre"
                                                    ></input-texto>
                                                </div>

                                                <div class="col-xl-4">
                                                    <input-texto
                                                            v-model="item.pais.departamento.abreviatura"
                                                            nombre="abreviatura"
                                                            etiqueta="Abreviatura"
                                                    ></input-texto>
                                                </div>
                                            </div>
                                        </template>
                                    </input-objeto>
                                </div>

                                <div class="col-xl-12 col-md-4">
                                    <input-objeto
                                        v-model="item.pais.departamento.municipio.id"
                                        nombre="id_municipio"
                                        etiqueta="Municipios"
                                        fuente="IntranetMunicipio"
                                        :items="municipiosDeDepartamento"
                                        :url="urls.post"
                                        @guardado="actualizarObjetoMunicipio"
                                    >
                                        <template slot="modal">
                                            <div class="row">
                                                <div class="col-xl-8">
                                                    <input-texto
                                                            v-model="item.pais.departamento.nombre"
                                                            nombre="nombre"
                                                            etiqueta="Nombre"
                                                    ></input-texto>
                                                </div>

                                                <div class="col-xl-4">
                                                    <input-texto
                                                            v-model="item.pais.departamento.abreviatura"
                                                            nombre="abreviatura"
                                                            etiqueta="Abreviatura"
                                                    ></input-texto>
                                                </div>
                                            </div>
                                        </template>
                                    </input-objeto>
                                </div>

                                <div class="col-xl-12 col-md-4">
                                    <input-texto
                                        v-model="item.nombre"
                                        nombre="nombre"
                                        etiqueta="Nombre Barrio"
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
        name: "Barrios",

        props: {
            urls: Object,
            avatar_defecto: String,
        },

        data: () => ({
            fuente: 'IntranetBarrio',
            titulo_singular: 'Barrio',
            titulo_plural: 'Barrios',
            icono: "icon-line-awesome-home",
            items: [],
            item: {
                id: 0,
                nombre: '',
                abreviatura: '',

                pais: {
                    id: '',
                    nombre: '',
                    abreviatura: '',

                    departamento: {
                        id: '',
                        nombre: '',
                        abreviatura: '',

                        municipio:{
                            id: '',
                            nombre: '',
                            abreviatura: ''
                        }
                    }
                }
            },
            vista_items: true,
            vista_formulario: false,
            texto_buscado: '',
            propiedades_buscadas: [
                'nombre',
                'abreviatura',
            ],
            atributos: [],

            departamentosDePais: [],
            municipiosDeDepartamento: [],

             //Data
            paises: [],
            atributos: []
        }),

        watch: {
            'item.pais.id': function(newIndex,old){

                const index = parseInt(newIndex);
                if(!isNaN(index)){
                    const paisesFiltrados = (this.paises || []).filter( pais => parseInt(pais.id) === index );
                    if(paisesFiltrados.length)
                        this.departamentosDePais = paisesFiltrados[0].departamentos || []
                }
            },

            'item.pais.departamento.id': function(newIndex, old){
                //alert("here")
                const index = parseInt(newIndex);
                if(!isNaN(index)){
                    const departamentosFiltrados = (this.departamentosDePais|| []).filter( dep => parseInt(dep.id) === index );
                    if(departamentosFiltrados.length)
                        this.municipiosDeDepartamento = departamentosFiltrados[0].municipios || []
                }
            }
        },


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

            actualizarObjetoPais(data){

            },

            actualizarObjetoDepartamento(data){

            },

            actualizarObjetoMunicipio(data){

            }
        },

        mounted() {
            this.cargarData();
        },

    }
</script>

<style scoped>

</style>