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
            <h4>{{ row.item.meta }}<span>: {{ row.item.valor }}</span></h4>

            <span class="detail">
                {{ formatoFecha(row.item.fecha) }}
            </span>
        </template>

        <template slot="formulario">
            <input type="hidden" name="id_usuario" :value="$route.params.id_usuario">

            <div class="row">

                <!-- Dashboard Box -->
                <div class="col-xl-12">
                    <div class="dashboard-box margin-top-0">

                        <div class="content with-padding padding-bottom-10">
                            <div class="row">
                                <div class="col-xl-7">
                                    <input-seleccion
                                            v-model="item.id_meta"
                                            nombre="id_meta"
                                            etiqueta="Meta"
                                            :items="metas"
                                    ></input-seleccion>
                                </div>

                                <div class="col-xl-5">
                                    <input-texto
                                            v-model="item.valor"
                                            nombre="valor"
                                            etiqueta="Valor"
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
        name: "EzadigitalMetaUsuario.vue",

        props: {
            urls: Object,
            avatar_defecto: String,
            id_usuario: Number,
        },

        data: () => ({
            fuente: 'MetaUsuario',
            subdirectorio: 'Ezadigital',
            titulo_singular: 'Meta',
            titulo_plural: 'Metas',
            icono: 'icon-line-awesome-crosshairs',
            items: [],
            item: {
                id: 0,
                id_meta: 0,
                id_usuario: 0,
                valor: '',
                fecha: '',
            },
            vista_items: true,
            vista_formulario: false,
            texto_buscado: '',
            propiedades_buscadas: [
                'nombre',
                'valor',
            ],
            metas: [],
        }),

        methods: {
            limpiarItem() {
                this.item.id = 0;
                this.item.id_meta = 0;
                this.item.id_usuario = this.$route.params.id_usuario;
                this.item.valor = '';
                this.item.fecha = '';
            },

            setItemData(data) {
                this.item.id = data.id;
                this.item.id_meta = data.id_meta;
                this.item.id_usuario = data.id_usuario;
                this.item.valor = data.valor;
                this.item.fecha = data.fecha;
            },

            cargarDataAdicional() {
                this.$http.get(this.urls.get, {
                    params: {
                        _fuente: 'MetaUsuario',
                        _subdirectorio: 'Ezadigital',
                        _accion: 'cargarMetas',
                    }
                })
                    .then(response => {
                        if (response.status === 200) {
                            const data = response.data;

                            if (typeof data === 'object' && data instanceof Array) {
                                this.metas = data;
                            }
                        }
                    });
            }
        },

        mounted() {
            if (typeof this.$route.params.id_usuario === 'undefined' || !this.$route.params.id_usuario) {
                this.$router.push('usuarios');
            }

            this.titulo_plural = 'Metas de ' + this.$route.params.nombre_usuario;

            this.cargarData({
                id_usuario: this.$route.params.id_usuario,
            });
        },
    }
</script>

<style scoped>

</style>