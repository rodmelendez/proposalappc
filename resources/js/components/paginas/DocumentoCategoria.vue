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
                                            />
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
                        <h3><i :class="icono"></i> {{ items.length }} {{ items.length === 1 ? titulo_singular.toLowerCase() : titulo_plural.toLowerCase() }}</h3>

                        <div class="actions">
                            <!--button type="button" class="popup-with-zoom-anim button dark ripple-effect">
                                <i class="icon-feather-plus"></i> Nuevo
                            </button-->
                        </div>
                    </div>

                    <div class="content">
                        <div class="barra-acciones" v-if="puede_crear || puede_editar || puede_eliminar">
                            <div class="row" v-show="!directorio_seleccionado.id">
                                <div class="col-xl-12">
                                    <div class="input-with-icon" v-if="puede_crear">
                                        <input type="text" class="margin-bottom-0" placeholder="Agregar nueva categoría" v-model="directorio_nuevo" @keypress.enter="crearCategoria">
                                        <i class="btn-actualizar-item icon-feather-check" @click="crearCategoria"></i>
                                    </div>
                                </div>
                            </div>

                            <div v-show="directorio_seleccionado.id">
                                <div class="row">
                                    <div class="col-xl-9">
                                        <!--<input-texto
                                                v-model="directorio_seleccionado.nombre"
                                                placeholder="Nombre"
                                        ></input-texto>-->
                                        <div class="input-with-icon">
                                            <input type="text" class="margin-bottom-0" placeholder="Nombre" v-model="directorio_seleccionado.nombre" @keypress.enter="actualizarCategoria">
                                            <i class="btn-actualizar-item icon-feather-check" @click="actualizarCategoria"></i>
                                        </div>
                                    </div>

                                    <div class="col-xl-3">
                                        <button type="button" class="button big gray" @click="abrirSubirArchivo" v-if="puede_crear_archivos">
                                            <i class="icon-feather-file-plus" style="margin-left:-4px;"></i>
                                        </button>

                                        <button type="button" class="button big gray" @click="quitarDirectorio" v-if="puede_eliminar">
                                            <i class="icon-feather-trash-2" style="margin-left:-4px;"></i>
                                        </button>

                                        <button type="button" class="button big transparent float-right" @click="quitarSeleccionDirectorio">
                                            <i class="icon-line-awesome-close"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="row" v-if="subir_archivo">
                                    <div class="col-xl-12">
                                        <div class="cargar-archivo margin-top-10" :class="uploading ? 'status-uploading padded' : ''">
                                            <input-archivo
                                                    v-model="archivo_nuevo"
                                                    nombre="archivo"
                                                    clase="margin-bottom-0"
                                                    @modificado="subirArchivo"
                                            ></input-archivo>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <ul id="file_tree" class="file-tree file-list" ref="listado_directorio" v-if="puede_consultar">
                            <directorio
                                    v-for="item in this.items"
                                    :item="item"
                                    fuente="DocumentoCategoria"
                                    accion="cargar"
                                    accion_directorio="guardarCategoria"
                                    :permite_agregar_directorio="puede_crear"
                                    @crearDirectorio="crearDirectorio"
                                    @crearArchivo="crearArchivo"
                                    @seleccion="seleccionDirectorio"
                                    :key="item.id"
                            >
                            </directorio>
                        </ul>

                    </div>
                </div>
            </div>

        </div>
    </main>
</template>

<script>
    export default {
        name: "DocumentoCategoria.vue",

        props: {
            urls: Object,
            avatar_defecto: String,
            empresas: {
                default: []
            }
        },

        data: () => ({
            fuente: 'DocumentoCategoria',
            titulo_singular: 'Categoría de documento',
            titulo_plural: 'Categorías de documentos',
            icono: 'icon-line-awesome-folder',
            items: [],
            item: {
                id: 0,
                nombre: '',
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
        }),

        methods: {
            limpiarItem() {
                this.item.id = 0;
                this.item.id_documento_categoria = 0;
                this.item.nombre = '';
            },

            setItemData(data) {
                this.item.id = data.id;
                this.item.id_documento_categoria = data.id_documento_categoria;
                this.item.nombre = data.nombre;
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

            itemsPadres() {
                return [{id:0, nombre:'(Sin categoría)'}].concat(this.items_padres);
            },

            verSubcategorias(item) {
                /*this.$http.get(this.urls.get, {
                    params: {
                        _fuente: this.fuente,
                        _accion: 'cargar',
                        id_padre: item.id,
                    }
                })
                    .then(response => {
                        if (response.status === 200) {
                            //this.items = response.data['items'];

                        }
                    });*/
            },

            crearDirectorio(item) {

            },

            crearArchivo(item) {
                console.log('maybe you want a file', item);
            },

            seleccionDirectorio(item) {
                const $contenedor = $(this.$refs['listado_directorio']);

                $contenedor
                    .find('.seleccionado')
                    .not('[data-id_item="' + item.id + '"]')
                    .removeClass('seleccionado');

                //this.directorio_seleccionado = item;
                this.directorio_seleccionado = {
                    id: item.id,
                    nombre: item.nombre,
                };
                this.directorio_seleccionado_item = item;
            },

            quitarSeleccionDirectorio() {
                const $contenedor = $(this.$refs['listado_directorio']);

                $contenedor
                    .find('.seleccionado')
                    .removeClass('seleccionado');

                this.directorio_seleccionado = {
                    id: 0,
                    nombre: '',
                };
                this.directorio_seleccionado_item = {};
            },

            quitarDirectorio() {
                const self = this;
                confirmar('¿Está seguro que quiere eliminar la categoría?', function() {
                    self.$http.post(self.$url_post, {
                        _fuente: self.fuente,
                        _accion: 'eliminarCategoria',
                        id: self.directorio_seleccionado.id,
                    })
                        .then(response => {
                            if (response.status === 200) {
                                const data = response.data;

                                resultadoSolicitudDefecto(data);

                                if (data.ok) {
                                    self.quitarSeleccionDirectorio();

                                    const $contenedor = $(self.$refs['listado_directorio']);

                                    $contenedor
                                        .find('[data-id_item="' + data.id + '"]')
                                        .addClass('seleccionado')
                                        .hide();
                                }
                            }
                            else if (response.status === 500) {
                                mensajeError('Error de servidor.');
                            }
                        });
                });
            },

            actualizarCategoria() {
                if (!this.directorio_seleccionado.id) {
                    return;
                }

                this.$http.post(this.$url_post, {
                    _fuente: this.fuente,
                    _accion: 'actualizarCategoria',
                    id: this.directorio_seleccionado.id,
                    nombre: this.directorio_seleccionado.nombre,
                })
                    .then(response => {
                        if (response.status === 200) {
                            const data = response.data;

                            resultadoSolicitudDefecto(data);

                            if (data.ok) {
                                this.directorio_seleccionado_item.nombre = data.nombre;
                            }
                        }
                        else if (response.status === 500) {
                            mensajeError('Error de servidor.');
                        }
                    });
            },

            crearCategoria() {
                if (!this.directorio_nuevo.length) {
                    return;
                }

                this.$http.post(this.$url_post, {
                    _fuente: this.fuente,
                    _accion: 'guardarCategoria',
                    nombre: this.directorio_nuevo,
                })
                    .then(response => {
                        if (response.status === 200) {
                            const data = response.data;

                            resultadoSolicitudDefecto(data);

                            if (data.ok) {
                                this.directorio_nuevo = '';

                                this.items.push(data.item);
                            }
                        }
                        else if (response.status === 500) {
                            mensajeError('Error de servidor.');
                        }
                    });
            },

            abrirSubirArchivo() {
                this.subir_archivo = !this.subir_archivo;
            },

            subirArchivo(el) {
                this.uploading = true;

                let form = document.createElement('form');
                let in_fuente = document.createElement('input');
                let in_accion = document.createElement('input');
                let in_modificado = document.createElement('input');
                let in_id_documento_categoria = document.createElement('input');

                in_fuente.setAttribute('name', '_fuente');
                in_fuente.setAttribute('value', 'DocumentoCategoria');

                in_accion.setAttribute('name', '_accion');
                in_accion.setAttribute('value', 'subirArchivo');

                in_modificado.setAttribute('name', 'archivo_upload_modificado');
                in_modificado.setAttribute('value', '1');

                in_id_documento_categoria.setAttribute('name', 'id_documento_categoria');
                in_id_documento_categoria.setAttribute('value', this.directorio_seleccionado.id.toString());

                form.append(in_fuente);
                form.append(in_accion);
                form.append(in_modificado);
                form.append(in_id_documento_categoria);
                form.append($(el).clone().get(0));

                const form_data = new FormData(form);

                this.debugForm(form_data)

                const config = { headers: { 'Content-Type': 'multipart/form-data' } };

                const self = this;

                this.$http.post(this.urls.post, form_data, config)
                    .then(response => {
                        if (response.status === 200) {
                            const data = response.data;

                            resultadoSolicitudDefecto(data);

                            if (data.ok) {
                                self.subir_archivo = false;

                                const nuevo_archivo = {
                                    id: data.item.id,
                                    nombre: data.item.nombre,
                                    archivo: data.item.archivo,
                                };

                                if (typeof self.directorio_seleccionado_item.archivos === 'object') {
                                    self.directorio_seleccionado_item.archivos.push(nuevo_archivo);
                                } else {
                                    self.directorio_seleccionado_item.archivos = nuevo_archivo;
                                }

                                const $input = $(el);
                                $input.data('dropify').resetPreview();
                                $input.data('dropify').clearElement();
                            }
                        }
                        else if (response.status === 500) {
                            mensajeError('Error de servidor.');
                        }

                        self.uploading = false;
                    });
            },
        },

        created() {
            const categoria = this.$route.path.split('/').pop();

            this.puede_consultar = this.tienePermiso(categoria, 'consultar');
            this.puede_crear = this.tienePermiso(categoria, 'crear');
            this.puede_editar = this.tienePermiso(categoria, 'editar');
            this.puede_eliminar = this.tienePermiso(categoria, 'eliminar');

            this.puede_consultar_archivos = this.tienePermiso('documentos', 'consultar');
            this.puede_crear_archivos = this.tienePermiso('documentos', 'crear');
        },

        mounted() {
            this.cargarData();
        },
    }
</script>

<style scoped>
    .barra-acciones {
        padding: 20px;
        background-color: #f2f2f2;
    }

    .btn-actualizar-item {
        cursor: pointer;
        pointer-events: all;
    }
</style>