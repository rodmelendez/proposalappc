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
            <h4>{{ row.item.colaborador_nombre }}</h4>

            <div class="row">
                <div class="col-sm-12">
                    <!-- Details -->
                    <span class="freelancer-detail-item" v-if="row.item.id_empresa">
                        <span><b>Empresa:</b> {{ row.item.empresa }}</span>
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
                                    <input-texto
                                            v-model="item.nombre"
                                            nombre="nombre"
                                            etiqueta="Nombre de la sucursal"
                                    ></input-texto>
                                </div>

                                <div class="col-xl-6">
                                    <input-seleccion
                                            v-model="item.id_empresa"
                                            nombre="id_empresa"
                                            etiqueta="Empresa"
                                            :items="empresas"
                                    ></input-seleccion>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xl-12">
                                    <input-texto
                                            v-model="item.ubicacion"
                                            nombre="ubicacion"
                                            etiqueta="Ubicación"
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
        name: "Entregas.vue",

        props: {
            urls: Object,
            avatar_defecto: String,
        },

        data: () => ({
            fuente: 'Entrega',
            titulo_singular: 'Entrega',
            titulo_plural: 'Entregas',
            icono: 'icon-feather-file-minus',
            items: [],
            item: {
                id: 0,
                fecha: '',
                codigo: '',
                num_documento: '',
                version: '',
                descripcion: '',
                observacion: '',
                colaborador_cargo: '',
                colaborador_departamento: '',
                colaborador_correo: '',
                colaborador_direccion: '',
                colaborador_cedula: '',
                colaborador_nombre: '',
                colaborador_telefono: '',
                nombre_recibe: '',
                nombre_entrega: '',
                nombre_autoriza: '',
                id_usuario_emisor: null,
                id_usuario_receptor: null,
                id_usuario_autoriza: null,
            },
            vista_items: true,
            vista_formulario: false,
            texto_buscado: '',
            propiedades_buscadas: [
                'codigo',
                'num_documento',
                'colaborador_nombre',
                'nombre_recibe',
                'nombre_entrega',
                'nombre_autoriza',
            ],
            empresas: [],
        }),

        methods: {
            limpiarItem() {
                this.item.id = 0;
                this.item.id_empresa = null;
                this.item.nombre = '';
                this.item.ubicacion = '';
            },

            setItemData(data) {
                this.item.id = data.id;
                this.item.id_empresa = data.id_empresa;
                this.item.nombre = data.nombre;
                this.item.ubicacion = data.ubicacion;
            },

            cargarDataAdicional() {
                this.$http.get(this.urls.get, {
                    params: {
                        _fuente: this.fuente,
                        _accion: 'cargarEmpresas',
                    }
                })
                    .then(response => {
                        if (response.status === 200) {
                            const data = response.data;

                            if (data.ok) {
                                this.empresas = data['empresas'];
                            }
                        }
                    });
            },

            nombreEmpresa(item) {
                if (!item.id_empresa) return '';

                for (const empresa of this.empresas) {
                    if (empresa.id == item.id_empresa) {
                        return empresa.nombre;
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