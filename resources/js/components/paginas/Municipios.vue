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
                <span><b>Departamento:</b> {{row.item.departamento}} </span>
            </span>
        </template>

        <template slot="formulario">
            <div class="row">

                <!-- Dashboard Box -->
                <div class="col-xl-12">
                    <div class="dashboard-box margin-top-0">
                        <div class="content with-padding padding-bottom-10">
                            <div class="row">
                                <div class="col-xl-4">
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
                                                <div class="col-xl-12 col-md-8">
                                                    <input-texto
                                                            v-model="item.pais.nombre"
                                                            nombre="nombre"
                                                            etiqueta="Nombre"
                                                    ></input-texto>
                                                </div>

                                                <div class="col-xl-12 col-md-4">
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

                                <div class="col-xl-4">
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

                                <div class="col-xl-4">
                                    <input-texto
                                        v-model="item.nombre"
                                        nombre="nombre"
                                        etiqueta="Nombre Municipio"
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
        name: "Paises",

        props: {
            urls: Object,
            avatar_defecto: String,
        },

        data: () => ({
            fuente: 'IntranetMunicipio',
            titulo_singular: 'Municipio',
            titulo_plural: 'Municipios',
            icono: 'icon-line-awesome-map-marker',
            departamentosDePais: [],
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
                        abreviatura: ''
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
            //Data
            paises: [],
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

            }

        },

        watch: {
            'item.pais.id': function(newIndex,old){
                //alert("here");
                /*console.log({
                    _new,
                    old,
                    this: this
                })*/
                const index = parseInt(newIndex);
                //console.log(index)
                if(!isNaN(index)){
                    const paisesFiltrados = (this.paises || []).filter( pais => parseInt(pais.id) === index );

                    if(paisesFiltrados.length)
                        this.departamentosDePais = paisesFiltrados[0].departamentos || []
                }
            }
        },

        mounted() {
            this.cargarData();
        },
    }
</script>

<style scoped>

</style>