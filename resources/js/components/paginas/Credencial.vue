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
            <h4 v-html="itemDesc(row.item)"></h4>

            <!-- Details -->
            <span class="freelancer-detail-item" v-if="row.item.categoria">
                <span><i class="icon-line-awesome-tag"></i> {{ row.item.categoria }}</span>
            </span>

            <span class="freelancer-detail-item" v-if="row.item.dominio">
                <span><i class="icon-line-awesome-globe"></i> {{ row.item.dominio }}</span>
            </span>

            <transition
                    appear
                    mode="out-in"
                    enter-active-class="animated zoomInDown"
                    leave-active-class="animated zoomOutUp"
            >
                <div class="notification warning closeable margin-top-5 detail" v-show="typeof row.item.contrasena === 'string' && row.item.contrasena.length">
                    <p class="psw-holder">
                        <span v-show="row.item.usuario">
                            <b>Usuario: </b> {{ row.item.usuario }}<br>
                        </span>

                        <span v-show="row.item.puerto">
                            <b>Puerto: </b> {{ row.item.puerto }}<br>
                        </span>

                        <span v-show="row.item.protocolo">
                            <b>Protocolo: </b> {{ row.item.protocolo }}<br>
                        </span>

                        <span v-show="row.item.dominio">
                            <b>Dominio: </b> {{ row.item.dominio }}<br>
                        </span>

                        <span v-show="row.item.dominio">
                            <b>Dominio: </b> {{ row.item.dominio }}<br>
                        </span>

                        <span class="psw">{{ row.item.contrasena || '' }}</span>

                        &nbsp;
                        <a class="link" @click="copiarContrasena">
                            <i class="icon-line-awesome-copy"></i>
                        </a>
                    </p>
                    <a class="close" @click="ocultarContrasena(row.item)"></a>
                </div>
            </transition>
        </template>

        <template slot="botones" slot-scope="row">
            <a class="button gray ripple-effect ico detail" title="Ver credenciales" @click="verContrasena(row.item)">
                <i class="icon-feather-eye"></i>
            </a>
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
                                            v-model="item.tipo"
                                            nombre="tipo"
                                            etiqueta="Tipo"
                                            :items="tipos"
                                    ></input-seleccion>
                                </div>

                                <div class="col-xl-4">
                                    <input-objeto
                                            v-model="item.id_credencial_categoria"
                                            nombre="id_credencial_categoria"
                                            etiqueta="Categoría"
                                            fuente="CredencialCategoria"
                                            :items="credencial_categorias"
                                            :url="urls.post"
                                            @guardado="actualizarObjetoCredencialCategoria"
                                    >
                                        <template slot="modal">
                                            <div class="row">
                                                <div class="col-xl-12">
                                                    <input-texto
                                                            v-model="item.credencial_categoria.nombre"
                                                            nombre="nombre"
                                                            etiqueta="Nombre de la Categoría"
                                                    ></input-texto>
                                                </div>
                                            </div>
                                        </template>
                                    </input-objeto>
                                </div>
                            </div>

                            <div  v-if="item.tipo == TIPO_WIFI">
                                <div class="row">
                                    <div class="col-xl-6">
                                        <input-texto
                                                v-model="item.nombre"
                                                nombre="nombre"
                                                etiqueta="Nombre (SSID)"
                                        ></input-texto>
                                    </div>

                                    <div class="col-xl-6">
                                        <input-contrasena
                                                v-model="item.contrasena"
                                                nombre="contrasena"
                                                etiqueta="Contraseña"
                                        ></input-contrasena>
                                    </div>
                                </div>
                            </div>

                            <div v-else-if="item.tipo == TIPO_PAGINA">
                                <div class="row">
                                    <div class="col-xl-6">
                                        <input-texto
                                                v-model="item.usuario"
                                                nombre="usuario"
                                                etiqueta="Nombre de usuario"
                                        ></input-texto>
                                    </div>

                                    <div class="col-xl-6">
                                        <input-contrasena
                                                v-model="item.contrasena"
                                                nombre="contrasena"
                                                etiqueta="Contraseña"
                                        ></input-contrasena>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-xl-6">
                                        <input-texto
                                                v-model="item.correo"
                                                nombre="correo"
                                                etiqueta="Correo"
                                        ></input-texto>
                                    </div>

                                    <div class="col-xl-6">
                                        <input-texto
                                                v-model="item.telefono"
                                                nombre="telefono"
                                                etiqueta="Teléfono"
                                        ></input-texto>
                                    </div>

                                    <div class="col-xl-12">
                                        <input-texto
                                                v-model="item.url"
                                                nombre="url"
                                                etiqueta="URL"
                                        ></input-texto>
                                    </div>
                                </div>
                            </div>

                            <div v-else-if="item.tipo == TIPO_FTP">
                                <div class="row">
                                    <div class="col-xl-8">
                                        <input-texto
                                                v-model="item.dominio"
                                                nombre="dominio"
                                                etiqueta="Dominio"
                                        ></input-texto>
                                    </div>

                                    <div class="col-xl-4">
                                        <input-texto
                                                v-model="item.puerto"
                                                nombre="puerto"
                                                etiqueta="Puerto"
                                        ></input-texto>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-xl-6">
                                        <input-texto
                                                v-model="item.usuario"
                                                nombre="usuario"
                                                etiqueta="Nombre de usuario"
                                        ></input-texto>
                                    </div>

                                    <div class="col-xl-6">
                                        <input-contrasena
                                                v-model="item.contrasena"
                                                nombre="contrasena"
                                                etiqueta="Contraseña"
                                        ></input-contrasena>
                                    </div>
                                </div>
                            </div>

                            <div v-else-if="item.tipo == TIPO_CORREO">
                                <div class="row">
                                    <div class="col-xl-6">
                                        <input-texto
                                                v-model="item.correo"
                                                nombre="correo"
                                                etiqueta="Correo"
                                        ></input-texto>
                                    </div>

                                    <div class="col-xl-6">
                                        <input-contrasena
                                                v-model="item.contrasena"
                                                nombre="contrasena"
                                                etiqueta="Contraseña"
                                        ></input-contrasena>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-xl-12">
                                        <input-texto
                                                v-model="item.url"
                                                nombre="url"
                                                etiqueta="URL"
                                        ></input-texto>
                                    </div>
                                </div>
                            </div>

                            <div v-else-if="item.tipo == TIPO_OTRO">
                                <div class="row">
                                    <div class="col-xl-6">
                                        <input-texto
                                                v-model="item.usuario"
                                                nombre="usuario"
                                                etiqueta="Nombre de usuario"
                                        ></input-texto>
                                    </div>

                                    <div class="col-xl-6">
                                        <input-contrasena
                                                v-model="item.contrasena"
                                                nombre="contrasena"
                                                etiqueta="Contraseña"
                                        ></input-contrasena>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-xl-6">
                                        <input-texto
                                                v-model="item.dominio"
                                                nombre="dominio"
                                                etiqueta="Dominio"
                                        ></input-texto>
                                    </div>

                                    <div class="col-xl-2">
                                        <input-texto
                                                v-model="item.puerto"
                                                nombre="puerto"
                                                etiqueta="Puerto"
                                        ></input-texto>
                                    </div>

                                    <div class="col-xl-4">
                                        <input-texto
                                                v-model="item.protocolo"
                                                nombre="protocolo"
                                                etiqueta="Protocolo"
                                        ></input-texto>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-xl-6">
                                        <input-texto
                                                v-model="item.correo"
                                                nombre="correo"
                                                etiqueta="Correo"
                                        ></input-texto>

                                        <input-texto
                                                v-model="item.telefono"
                                                nombre="telefono"
                                                etiqueta="Teléfono"
                                        ></input-texto>

                                        <input-texto
                                                v-model="item.url"
                                                nombre="url"
                                                etiqueta="URL"
                                        ></input-texto>
                                    </div>

                                    <div class="col-xl-6">
                                        <input-imagen
                                                v-model="item.imagen"
                                                nombre="imagen"
                                                etiqueta="Imagen"
                                                altura="260"
                                        ></input-imagen>
                                    </div>
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
    const _TIPO_WIFI = 1;
    const _TIPO_PAGINA = 2;
    const _TIPO_FTP = 3;
    const _TIPO_CORREO = 4;
    const _TIPO_OTRO = 9;

    export default {
        name: "Credencial",

        props: {
            urls: Object,
            avatar_defecto: String,
        },

        data: () => ({
            fuente: 'Credencial',
            titulo_singular: 'Credencial',
            titulo_plural: 'Credenciales',
            icono: 'icon-material-outline-fingerprint',
            items: [],
            item: {
                id: 0,
                tipo: '',
                categoria: '',
                id_credencial_categoria: null,
                nombre: '',
                usuario: '',
                contrasena: '',
                puerto: '',
                dominio: '',
                protocolo: '',
                correo: '',
                telefono: '',
                url: '',
                imagen: '',
                credencial_categoria: {
                    id: 0,
                    nombre: '',
                },
            },
            vista_items: true,
            vista_formulario: false,
            texto_buscado: '',
            propiedades_buscadas: [
                'nombre',
                'usuario',
                'dominio',
                'correo',
                'url',
                'telefono',
                'categoria',
            ],
            credencial_categorias: [],
            TIPO_WIFI: _TIPO_WIFI,
            TIPO_PAGINA: _TIPO_PAGINA,
            TIPO_FTP: _TIPO_FTP,
            TIPO_CORREO: _TIPO_CORREO,
            TIPO_OTRO: _TIPO_OTRO,
            tipos: [
                {
                    id: _TIPO_WIFI,
                    nombre: 'WiFi'
                },
                {
                    id: _TIPO_PAGINA,
                    nombre: 'Página'
                },
                {
                    id: _TIPO_FTP,
                    nombre: 'FTP'
                },
                {
                    id: _TIPO_CORREO,
                    nombre: 'Correo'
                },
                {
                    id: _TIPO_OTRO,
                    nombre: 'Otro'
                },
            ],
        }),

        methods: {
            limpiarItem() {
                this.item.id = 0;
                this.item.tipo = this.TIPO_WIFI;
                this.item.categoria = '';
                this.item.id_credencial_categoria = null;
                this.item.nombre = '';
                this.item.usuario = '';
                this.item.contrasena = '';
                this.item.puerto = '';
                this.item.dominio = '';
                this.item.protocolo = '';
                this.item.correo = '';
                this.item.telefono = '';
                this.item.url = '';
                this.item.imagen = '';
                this.item.credencial_categoria = {
                    id: 0,
                    nombre: '',
                };
            },

            setItemData(data) {
                this.item.id = data.id;
                this.item.tipo = data.tipo;
                this.item.categoria = data.categoria;
                this.item.id_credencial_categoria = data.id_credencial_categoria;
                this.item.nombre = data.nombre;
                this.item.usuario = data.usuario;
                this.item.contrasena = data.contrasena;
                this.item.puerto = data.puerto;
                this.item.dominio = data.dominio;
                this.item.protocolo = data.protocolo;
                this.item.correo = data.correo;
                this.item.telefono = data.telefono;
                this.item.url = data.url;
                this.item.imagen = data.imagen;
            },

            cargarDataAdicional() {
                this.$http.get(this.urls.get, {
                    params: {
                        _fuente: 'Credencial',
                        _accion: 'listaParametros',
                    }
                })
                    .then(response => {
                        if (response.status === 200) {
                            const data = response.data;

                            if (typeof data === 'object') {
                                this.credencial_categorias = data['credencial_categorias'];
                            }
                        }
                    });
            },

            actualizarObjetoCredencialCategoria(data) {
                this.credencial_categorias.push({
                    id: data.id,
                    nombre: this.item.credencial_categoria.nombre,
                });

                this.item.id_credencial_categoria = data.id;
                this.item.categoria = this.item.credencial_categoria.nombre;
            },

            verContrasena(item) {
                const self = this;

                this.$http.get(this.urls.get, {
                    params: {
                        _fuente: this.fuente,
                        _accion: 'cargarContrasena',
                        id: item.id,
                    }
                })
                    .then(response => {
                        if (response.status === 200) {
                            const data = response.data;

                            if (data.ok) {
                                item.contrasena = data.contrasena;
                                self.$forceUpdate(); //^^ no se actualiza automáticamente, por eso se llama el forceUpdate
                            }
                        }
                    });
            },

            ocultarContrasena(item) {
                item.contrasena = '';
                this.$forceUpdate(); //^^ no se actualiza automáticamente, por eso se llama el forceUpdate
            },

            copiarContrasena(e) {
                const el = $(e.target).closest('.psw-holder').find('.psw').get(0);
                copiarAlPortapapeles(el);
                mensaje('Copiado al portapapeles.');
            },

            itemDesc(item) {
                let desc = '';

                switch (parseInt(item.tipo)) {
                    case this.TIPO_WIFI:
                        desc = '<i class="fa fa-fw fa-wifi"></i>&nbsp;';
                        desc += item.nombre;
                        break;

                    case this.TIPO_PAGINA:
                        desc = '<i class="fa fa-fw fa-globe"></i>&nbsp;';
                        desc += item.usuario + '(' + item.url + ')';
                        break;

                    case this.TIPO_FTP:
                        desc = '<i class="fa fa-fw fa-network-wired"></i>&nbsp;';
                        desc += item.usuario + '@' + item.dominio;
                        break;

                    case this.TIPO_CORREO:
                        desc = '<i class="fa fa-fw fa-envelope"></i>&nbsp;';
                        desc += item.correo;
                        break;

                    default:
                        desc = '<i class="fa fa-fw fa-passport"></i>&nbsp;';
                        if (typeof item.nombre === 'string' && item.nombre.length) {
                            desc += item.nombre;
                        }
                        else if (item.correo === 'string' && item.correo.length) {
                            desc += item.correo;
                        }
                        else if (item.dominio === 'string' && item.dominio.length) {
                            desc += item.dominio;
                        }
                        else if (item.usuario === 'string' && item.usuario.length) {
                            desc += item.usuario;
                        }
                        else {
                            desc += '(Credencial Personalizada)';
                        }
                        break;
                }

                return desc;
            },
        },

        mounted() {
            this.cargarData();
        },
    }
</script>

<style scoped>
    .psw {
        letter-spacing: 1px;
        font-weight: 600;
        font-size: 18px;
        color: #222;
    }
</style>