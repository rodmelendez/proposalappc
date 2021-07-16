<template>
    <main>
        <template v-if="false">
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
                    <h4>
                        {{ row.item.nombre }}
                    </h4>

                    <!-- Details -->
                    <span class="freelancer-detail-item">

                    </span>
                </template>

                <template slot="botones" slot-scope="row">
                    <a class="button gray ripple-effect ico" title="Ver sub-categorías" @click="verSubcategorias(row.item)">
                        <i class="icon-feather-corner-down-right"></i>
                    </a>
                </template>

                <template slot="formulario">
                    <div class="row">

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
                                        </div>

                                        <div class="col-xl-6">
                                            <input-seleccion
                                                    v-model="item.id_documento_categoria"
                                                    nombre="id_documento_categoria"
                                                    etiqueta="Categoría padre"
                                                    :items="itemsPadres()"
                                            ></input-seleccion>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </template>

                <template slot="secciones">
                    <section v-if="true">
                        <div class="file-tree">
                            <directorio
                                    v-for="item in this.items"
                                    :item="item"
                                    fuente="DocumentoCategoria"
                                    accion="cargar"
                                    accion_directorio="guardarCategoria"
                                    :mostrar_archivos="puede_consultar_archivos"
                                    :key="item.id"
                            >
                            </directorio>
                        </div>
                    </section>
                </template>
            </crud>
        </template>

        <!-- Dashboard Headline -->
        <div class="dashboard-headline">
            <div class="row">
                <div class="col-sm-8">
                    <h3>
                        {{ titulo_plural }}
                        <span class="item-cargando">&nbsp;<i class="icon-line-awesome-hourglass-2 fa-spin"></i></span>
                    </h3>
                </div>

                <div class="col-sm-4">
                    <!--div class="input-with-icon">
                        <input type="text" placeholder="Buscar..." v-model="texto_buscado" @dblclick="texto_buscado = ''">
                        <i class="icon-material-outline-search"></i>
                    </div-->
                </div>
            </div>
        </div>

        <!-- Row -->
        <div class="row">

            <!-- Dashboard Box -->
            <div class="col-xl-12">
                <div class="dashboard-box margin-top-0">

                    <!-- Headline -->
                    <div class="headline">
                        <h3 v-if="false"><i :class="icono"></i> {{ items.length }} {{ items.length === 1 ? titulo_singular.toLowerCase() : titulo_plural.toLowerCase() }}</h3>
                        <h3><i :class="icono"></i> {{ titulo_singular }}</h3>

                        <div class="actions">
                            <!--button type="button" class="popup-with-zoom-anim button dark ripple-effect">
                                <i class="icon-feather-plus"></i> Nuevo
                            </button-->
                        </div>
                    </div>

                    <div class="content">

                        <form method="post" :action="urlPost()" ref="frm_descargar">
                            <input type="hidden" name="_fuente" :value="this.fuente">
                            <input type="hidden" name="_accion" value="descargar">
                            <input type="hidden" name="_token" value="">

                            <input type="hidden" name="tipo" :value="item.tipo">

                            <div class="row">
                                <div class="col-xl-7">
                                    <div class="row">

                                        <div class="col-xl-12">
                                            <div class="content with-padding padding-bottom-10">

                                                <div class="barra-tipos margin-bottom-10">
                                                    <a href="#" class="button ripple-effect-dark" :class="item.tipo !== 0 ? 'gray' : ''" @click.prevent="setTipo(0)">
                                                        URL <i class="icon-material-outline-language"></i>
                                                    </a>

                                                    <a href="#" class="button ripple-effect-dark" :class="item.tipo !== 1? 'gray' : ''" @click.prevent="setTipo(1)">
                                                        Contacto <i class="icon-material-outline-person-pin"></i>
                                                    </a>
                                                </div>

                                                <div class="row" v-show="item.tipo === 0">
                                                    <div class="col-xl-12">
                                                        <input-texto
                                                                v-model="item.url"
                                                                nombre="url"
                                                                etiqueta="URL"
                                                        ></input-texto>
                                                    </div>
                                                </div>

                                                <div class="row" v-show="item.tipo === 1">
                                                    <div class="col-xl-6">
                                                        <input-texto
                                                                v-model="item.nombre"
                                                                nombre="nombre"
                                                                etiqueta="Nombre"
                                                        ></input-texto>
                                                    </div>

                                                    <div class="col-xl-6">
                                                        <input-texto
                                                                v-model="item.apellido"
                                                                nombre="apellido"
                                                                etiqueta="Apellido"
                                                        ></input-texto>
                                                    </div>

                                                    <div class="col-xl-6">
                                                        <input-texto
                                                                v-model="item.telefono"
                                                                nombre="telefono"
                                                                etiqueta="Teléfono"
                                                        ></input-texto>
                                                    </div>

                                                    <div class="col-xl-6">
                                                        <input-texto
                                                                v-model="item.correo"
                                                                nombre="correo"
                                                                etiqueta="Correo"
                                                        ></input-texto>
                                                    </div>

                                                    <div class="col-xl-6">
                                                        <input-texto
                                                                v-model="item.empresa"
                                                                nombre="empresa"
                                                                etiqueta="Empresa"
                                                        ></input-texto>
                                                    </div>

                                                    <div class="col-xl-6">
                                                        <input-texto
                                                                v-model="item.cargo"
                                                                nombre="cargo"
                                                                etiqueta="Cargo"
                                                        ></input-texto>
                                                    </div>

                                                    <div class="col-xl-12">
                                                        <input-texto
                                                                v-model="item.direccion"
                                                                nombre="direccion"
                                                                etiqueta="Dirección"
                                                        ></input-texto>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="col-xl-5">
                                    <div class="margin-top-30 margin-right-20">
                                        <a href="#" class="popup-with-zoom-anim button full-width button-sliding-icon" @click.prevent="generarQr">
                                            Generar QR <i class="icon-material-outline-arrow-right-alt"></i>
                                        </a>

                                        <div class="margin-top-20 margin-bottom-20 text-center" v-html="qr_code_img">

                                        </div>

                                        <div class="margin-bottom-30 text-center" v-if="qr_code_img.length">
                                            <input-texto
                                                    v-model="item.tamano"
                                                    nombre="tamano"
                                                    :etiqueta="false"
                                                    placeholder="Tamaño (Defecto: 400)"
                                            ></input-texto>

                                            <button type="button" class="button big ripple-effect button-sliding-icon" @click="descargarQr">
                                                Descargar <i class="icon-line-awesome-download"></i>
                                            </button>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </form>

                    </div>
                </div>
            </div>

        </div>
    </main>
</template>

<script>
    export default {
        name: "CodigoQr.vue",

        props: {
            urls: Object,
            avatar_defecto: String,
            empresas: {
                default: []
            }
        },

        data: () => ({
            fuente: 'CodigoQr',
            titulo_singular: 'Código QR',
            titulo_plural: 'Códigos QR',
            icono: 'icon-line-awesome-qrcode',
            items: [],
            item: {
                id: 0,
                tipo: 0,
                tamano: '',

                nombre: '',
                apellido: '',
                telefono: '',
                correo: '',
                empresa: '',
                cargo: '',
                direccion: '',

                url: '',
            },
            vista_items: true,
            vista_formulario: false,
            texto_buscado: '',
            propiedades_buscadas: [
                'nombre',
            ],

            puede_consultar: true,
            puede_crear: false,
            puede_editar: false,
            puede_eliminar: false,
            puede_consultar_archivos: false,
            puede_crear_archivos: false,

            items_padres: [],
            documento_padre: {},
            directorio_nuevo: '',
            directorio_seleccionado: {
                id: 0,
                nombre: '',
            },
            directorio_seleccionado_item: {},
            archivo_nuevo: '',
            subir_archivo: false,
            uploading: false,

            qr_code_img: '',
        }),

        methods: {
            limpiarItem() {
                this.item.id = 0;

                this.item.nombre = '';
                this.item.apellido = '';
                this.item.telefono = '';
                this.item.correo = '';
                this.item.empresa = '';
                this.item.cargo = '';
                this.item.direccion = '';

                this.item.url = '';
            },

            setItemData(data) {
                this.item.id = data.id;

            },

            cargarDataAdicional() {
                /*this.$http.get(this.urls.get, {
                    params: {
                        _fuente: this.fuente,
                        _accion: 'cargar',
                    }
                })
                    .then(response => {
                        if (response.status === 200) {
                            this.items_padres = response.data['items'];
                        }
                    });*/
            },

            setTipo(tipo) {
                this.item.tipo = tipo;
            },

            urlPost() {
                return this.urls.post;
            },

            params() {
                return this.item/*{
                    tipo: this.item.tipo,
                    nombre: this.item.nombre,
                    apellido: this.item.apellido,
                    telefono: this.item.telefono,
                    correo: this.item.correo,
                    url: this.item.url,
                }*/;
            },

            generarQr() {
                this.$http.get(this.urls.get, {
                    params: {
                        ...{
                            _fuente: this.fuente,
                            _accion: 'generar',
                        },
                        ...this.params(),
                    }
                })
                    .then(response => {
                        if (response.status === 200) {
                            const data = response.data;

                            if (data.ok) {
                                this.qr_code_img = data['qr_code'];
                            }
                        }
                    });
            },

            descargarQr() {
                const $frm = $(this.$refs['frm_descargar']);
                const token = $('meta[name="csrf-token"]').attr('content');

                $frm.find('input[name=_token]').val(token);

                $frm.submit();

                /*this.$http.post(this.$url_post, {
                    ...{
                        _fuente: this.fuente,
                        _accion: 'descargar',
                    },
                    ...this.params(),
                })
                    .then(response => {
                        if (response.status === 200) {

                        }
                        else if (response.status === 500) {
                            mensajeError('Error de servidor.');
                        }
                    });*/
            },
        },

        created() {
            const categoria = this.$route.path.split('/').pop();

            this.puede_consultar = this.tienePermiso(categoria, 'consultar');
            this.puede_crear = this.tienePermiso(categoria, 'crear');
            this.puede_editar = this.tienePermiso(categoria, 'editar');
            this.puede_eliminar = this.tienePermiso(categoria, 'eliminar');
        },

        mounted() {
            //this.cargarData();
        },
    }
</script>

<style scoped>

</style>