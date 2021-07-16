<template>
    <main>

        <!-- Dashboard Headline -->
        <div class="dashboard-headline">
            <div class="row">
                <div class="col-sm-8">
                    <h3>
                        {{ titulo_plural }}
                        <span class="item-cargando">&nbsp;<i class="icon-line-awesome-hourglass-2 fa-spin"></i></span>
                    </h3>
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
                    </div>

                    <div class="content">
                        <div class="barra-acciones" v-if="puede_crear || puede_editar || puede_eliminar">
                            
                            <div class="row" v-show="!directorio_seleccionado.id && !documento_seleccionado.id">
                                <div class="col-xl-12">
                                    <div class="input-with-icon" v-if="puede_crear">
                                        <input type="text" class="margin-bottom-0" placeholder="Agregar nueva categoría" v-model="directorio_nuevo" @keypress.enter="crearCategoria">
                                        <i class="btn-actualizar-item icon-feather-check" @click="crearCategoria"></i>
                                    </div>
                                </div>
                            </div>

                            <!--Acciones cuando hay un directorio seleccionado-->
                            <div v-show="directorio_seleccionado.id">
                                <div class="row">
                                    <div class="col-xl-9">
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
                            <!--Fin Acciones cuando hay un directorio seleccionado-->

                            <!--Acciones cuando hay un documento seleccionado-->
                            <div v-show="documento_seleccionado.id">
                                <div class="row">
                                    <div class="col-xl-9">
                                        <div class="input-with-icon">
                                            <input 
                                                type="text" 
                                                class="margin-bottom-0" 
                                                placeholder="Nombre"
                                                v-model="documento_seleccionado.nombre" 
                                                @keypress.enter="actualizarCategoria"
                                            >
                                            <i class="btn-actualizar-item icon-feather-check" @click="actualizarDocumento"></i>
                                        </div>
                                        <div>
                                            <input 
                                                type="text" 
                                                class="margin-bottom-0 margin-top-10" 
                                                placeholder="Descripcion"
                                                v-model="documento_seleccionado.descripcion" 
                                                @keypress.enter="actualizarCategoria"
                                            >
                                        </div>
                                    </div>

                                    <div class="col-xl-3">

                                        <button type="button" class="button big gray" @click="quitarDocumento" v-if="puede_eliminar">
                                            <i class="icon-feather-trash-2" style="margin-left:-4px;"></i>
                                        </button>

                                        <button type="button" class="button big transparent float-right" @click="quitarSeleccionDocumento">
                                            <i class="icon-line-awesome-close"></i>
                                        </button>

                                        <input-radios
                                            v-model="documento_seleccionado.tipo"
                                            nombre="tipo_documento"
                                            :items="tipo_documento"
                                        />

                                        <div class="switch-container">
                                            <label class="switch">
                                                <input v-model="documento_seleccionado.opcional"  type="checkbox" name="opcional" @change="cambiarOpcional($event)">
                                                <span class="switch-button"></span> Opcional   
                                            </label>
                                        </div>

                                    </div>
                                </div>

                            </div>
                            <!--Fin Acciones cuando hay un documento seleccionado-->

                        </div>

                        <ul 
                            id="file_tree" 
                            class="file-tree file-list" 
                            ref="listado_directorio" 
                            v-if="puede_consultar"
                        >
                            <directorio-plantilla
                                v-for="item in this.items"
                                :item="item"
                                fuente="IntranetDocumentoCategoria"
                                accion="cargar"
                                accion_directorio="guardarCategoria"
                                :permite_agregar_directorio="puede_crear"
                                @crearDirectorio="crearDirectorio"
                                @crearArchivo="crearArchivo"
                                @seleccion="seleccionDirectorio"
                                @seleccionDocumento="seleccionDocumento"
                                :key="item.id"
                            />
                        </ul>

                    </div>
                </div>
            </div>

        </div>
    </main>
</template>

<script>
    export default {
        name: "IntranetDocumentoCategoria.vue",

        props: {
            urls: Object,
            avatar_defecto: String,
            empresas: {
                default: []
            }
        },

        data: () => ({
            fuente: 'IntranetDocumentoCategoria',
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
            documento_seleccionado: {
                opcional: false,
                nombre: '',
                tipo: 1,
                descripcion: '',
                id:0
            },
            directorio_seleccionado_item: {},
            documento_seleccionado_item: {},
            archivo_nuevo: '',
            subir_archivo: false,
            uploading: false,
            tipo_documento: [{id: 1 , nombre: 'Foto'}, {id:2, nombre: 'PDF'}]
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

            itemsPadres() {
                return [{id:0, nombre:'(Sin categoría)'}].concat(this.items_padres);
            },

            actualizarDocumento(){
                if (!this.documento_seleccionado.id) {
                    return;
                }
               // alert("aaa")
                const form = this.crearFormData({...this.documento_seleccionado, _fuente: 'IntranetDocumento', _accion: 'editarDocumento'});
                this.debugForm(form);
                this.$http
                    .post(this.$url_post, form ,this.$defaultConfig)
                    .then(response => {
                        if (response.status === 200) {
                            this.debugResponse(response);
                            const data = response.data;

                            resultadoSolicitudDefecto(data);

                            if (data.ok) {
                                Object.assign( this.documento_seleccionado_item , data.item ) ;
                            }
                        }
                        else if (response.status === 500) {
                            mensajeError('Error de servidor.');
                        }
                    })
                    .catch( err => {
                        this.debugError(err)
                    });
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

                this.documento_seleccionado = {
                    opcional: false,
                    nombre: '',
                    tipo: 1,
                    descripcion: '',
                }

                this.directorio_seleccionado_item = item;
            },

            seleccionDocumento(item) {

                //alert("aaa")

                const $contenedor = $(this.$refs['listado_directorio']);

                /*$contenedor
                    .find('.seleccionado')
                    .not('[data-id_item="' + item.id + '"]')
                    .removeClass('seleccionado');*/
                //this.directorio_seleccionado = item;
                
                this.documento_seleccionado = {
                    ...item
                };

                this.documento_seleccionado_item = item;

                this.directorio_seleccionado = {
                    id: 0,
                    nombre: '',
                }

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

            quitarSeleccionDocumento() {
                this.directorio_seleccionado = {
                    id: 0,
                    nombre: '',
                };
                this.documento_seleccionado = {
                    opcional: false,
                    nombre: '',
                    tipo: 1,
                    descripcion: '',
                };

                this.documento_seleccionado_item = {}
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

                                    const $contenedor = $(self.$refs['listado_directorio']);

                                    $contenedor
                                        .find('[data-id_item="' + data.id + '"]')
                                        .addClass('seleccionado')
                                        .hide();

                                    self.quitarSeleccionDirectorio();
                                }
                            }
                            else if (response.status === 500) {
                                mensajeError('Error de servidor.');
                            }
                        });
                });
            },

            quitarDocumento(){
                const self = this;
                confirmar('¿Está seguro que quiere eliminar el documento?', function() {
                    self.$http.post(self.$url_post, {
                        _fuente: 'IntranetDocumento',
                        _accion: 'eliminar',
                        id: self.documento_seleccionado.id,
                    })
                        .then(response => {
                            
                            self.debugResponse(response)

                            if (response.status === 200) {
                                const data = response.data;

                                resultadoSolicitudDefecto(data);

                                if (data.ok) {
                                    self.quitarSeleccionDocumento();

                                    const $contenedor = $(self.$refs['listado_directorio']);

                                    $contenedor
                                        .find('[document_data="' + data.eliminados[0] + '"]')
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
                    id_documento_categoria: null
                })
                    .then(response => {
                        if (response.status === 200) {
                            const data = response.data;

                            resultadoSolicitudDefecto(data);

                            this.debugResponse(response,"hotpink","Añadir documento padre");

                            if (data.ok) {
                                this.directorio_nuevo = '';

                                this.items.push(data.item);
                            }
                        }
                        else if (response.status === 500) {
                            mensajeError('Error de servidor.');
                        }
                    }).catch( err => {
                        this.debugError(err);
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

            crearArchivo(item){

            },

            crearDirectorio(item){

            },

            cambiarOpcional(e){
                this.debugStuff(e.target.checked);
                this.documento_seleccionado.opcional = e.target.checked;
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