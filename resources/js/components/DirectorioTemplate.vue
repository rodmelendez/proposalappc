<template>
    <li 
        :class="[tieneHijos?'folder-root':'', abierto?'open':'closed', seleccionado?'seleccionado':'']" 
        :data-id_item="item.id"
    >
        <a href="#" @click.prevent="cargarSubcategorias(item)">
            {{ item.nombre }}
            <span class="loader" v-show="cargando">&nbsp;<i class="icon-line-awesome-hourglass-2 fa-spin"></i></span>
        </a>
        <ul>
            <template v-if="tieneHijos">
                <directorio-plantilla
                    v-for="sub_item in item.items"
                    :item="sub_item"
                    :fuente="fuente"
                    :accion="accion"
                    :accion_directorio="accion_directorio"
                    :permite_agregar="permite_agregar"
                    :permite_agregar_directorio="permite_agregar_directorio"
                    :mostrar_archivos="mostrar_archivos"
                    @crearDirectorio="crearDirectorio"
                    @crearArchivo="crearArchivo"
                    @seleccion="seleccion"
                    @seleccionDocumento="seleccionDocumento"
                    :key="sub_item.id"
                />
            </template>

            <li class="comando acciones-directorio" v-if="permite_agregar_directorio">
                <a href="#">
                    <i class="las la-plus-square"></i> Nuevo
                </a>
                <a href="#" @click.prevent="crearArchivo(item)" >
                    <i class = "las la-file"/>
                    <span>Archivo</span>
                </a>
                <a href="#" @click.prevent="crearDirectorio(item)" >
                    <i class = "icon-feather-folder-plus"/>
                    <span>Categoría</span>
                </a>
            </li>

            <li v-if="agregar_directorio">
                <div class="input-with-icon margin-right-50">
                    <input type="text" placeholder="Nombre de la categoría" v-model="nuevo_directorio" @keypress.enter="guardarDirectorio(item)">
                    <i class="btn-crear-categoria icon-feather-check" @click="guardarDirectorio(item)"></i>
                    <i class="icon-line-awesome-close first-icon" @click.prevent="crearDirectorio(item)" ></i>
                </div>
            </li>

            <li v-if="agregar_archivo" class="nuevo-archivo d-flex">
                <div class="input-with-icon margin-right-50">
                    <input 
                        type="text" 
                        placeholder="Nombre del archivo" 
                        v-model="itemDocumento.nombre"
                    >
                </div>
                <div class="input-with-icon margin-right-50">
                    <input 
                        type="text" 
                        placeholder="Descripcion" 
                        v-model="itemDocumento.descripcion"
                    >
                </div>
                <input-radios
                    v-model="documento.tipo"
                    nombre="tipo_documento"
                    :items="tipos_documento"
                />
                <div class="switch-container">
                    <label class="switch">
                        <input v-model="documento.opcional" type="checkbox" name="opcional" @change="cambiarOpcional($event)">
                        <span class="switch-button"></span> Opcional   
                    </label>
                </div>

                <div 
                    class="link cursor-active gray" 
                    style="margin-left:1rem;" 
                    @click="guardarArchivo(item)"
                >
                    <i class="icon-feather-check"></i>
                </div>
            </li>

            <template v-if="tieneArchivos">
                <li
                    v-for="(archivo,index) in item.archivos"
                    class="file-item"
                    :data-id_item="item.id"
                    :key="`${archivo.id}_${index}`"
                    :document_data="archivo.id"
                    @click="editarDocumento(archivo)"

                >
                    <a href="#">{{ archivo.nombre }}</a>
                </li>
            </template>
        </ul>
    </li>
</template>

<script>
    export default {
        name: "DirectorioTemplate.vue",

        props: {
            item: {
                type: Object,
                required: true,
            },
            fuente: {
                type: String,
                default: '',
            },
            accion: {
                type: String,
                default: 'cargar',
            },
            accion_directorio: {
                type: String,
                default: 'guardarDirectorio',
            },
            permite_agregar: {
                type: Boolean,
                default: true,
            },
            permite_agregar_directorio: {
                type: Boolean,
                default: true,
            },
            mostrar_archivos: {
                type: Boolean,
                default: true,
            }
        },

        data: () => ({
            abierto: false,
            cargando: false,
            agregar_directorio: false,
            agregar_archivo: false,
            nuevo_directorio: '',
            seleccionado: false,

            tipos_documento: [{id: 1 , nombre: 'Foto'}, {id:2, nombre: 'PDF'}],

            documento: {
                tipo: '',
                opcional: false
            },

            itemDocumento:{
                opcional: false,
                nombre: '',
                tipo: 1,
                descripcion: '',
            }
            
        }),

        methods: {
            tieneHijos() {
                return typeof item.items === 'object' && item.items !== null && item.items.length;
            },

            tieneArchivos() {
                console.log('ma', this.mostrar_archivos);
                if (!this.mostrar_archivos) return false;
                return typeof item.archivos === 'object' && item.archivos !== null && item.archivos.length;
            },

            editarDocumento(item){
                this.$emit('seleccionDocumento', item);
            },

            cargarSubcategorias(item, abierto) {
                this.$emit('seleccion', item);

                this.seleccionado = true;

                if (typeof abierto === 'boolean') {
                    this.abierto = abierto;
                } else {
                    this.abierto = !this.abierto;
                }

                if (this.abierto) {
                    if (typeof this.fuente !== 'string' || !this.fuente.length) {
                        return;
                    }

                    this.cargando = true;

                    this.$http.get(Vue.prototype.$url_get, {
                        params: {
                            _fuente: this.fuente,
                            _accion: this.accion,
                            id_documento_categoria: item.id
                        }
                    })
                        .then(response => {
                            this.debugResponse(response);

                            if (response.status === 200) {
                                const data = response.data;

                                const {archivos, ...rest} = data.items;

                                if (data.ok) {
                                    item.archivos = archivos|| [];
                                    item.items = rest;
                                    this.$forceUpdate();
                                }
                            }

                            this.cargando = false;
                        }).catch(err => {
                            this.debugError(err)
                        });
                }
            },

            crearDirectorio(item) {
                if (typeof this.fuente === 'string' && this.fuente.length && this.accion_directorio) {
                    this.agregar_directorio = !this.agregar_directorio;
                    this.agregar_archivo = false;
                }
                else {
                    this.$emit('crearDirectorio', item);
                }
            },

            crearArchivo(item) {
                if (typeof this.fuente === 'string' && this.fuente.length ) {
                    this.agregar_archivo = !this.agregar_archivo;
                    this.agregar_directorio = false;
                }
            },

            cambiarOpcional(e){
                this.debugStuff(e.target.checked);
                this.documento.opcional = e.target.checked;
            },

            guardarDirectorio(item) {
                if (this.nuevo_directorio.length) {
                    this.agregar_directorio = false;

                    this.$http.post(Vue.prototype.$url_post, {
                        _fuente: this.fuente,
                        _accion: this.accion_directorio,
                        nombre: this.nuevo_directorio,
                        id_documento_categoria: item ? item.id : null,
                    })
                        .then(response => {
                            this.debugResponse(response)
                            if (response.status === 200) {
                                const data = response.data;

                                resultadoSolicitudDefecto(data);

                                if (data.ok) {
                                    this.cargarSubcategorias(item, true);
                                    //this.$forceUpdate();
                                }
                            }
                            else if (response.status === 500) {
                                mensajeError('Error de servidor.');
                            }
                        })
                        .catch(err => {
                            //alert("aaa")
                            this.debugError(err);
                        });
                }
            },

            //Guarda un achivo o documento en la bd
            guardarArchivo(item) {
                this.agregar_directorio = false;
                this.agregar_archivo = false;
                
                const { tipo , ...rest } = this.itemDocumento;

                this.debugStuff({item, doc: this.itemDocumento})

                const form_data = this.crearFormData({
                    ...this.itemDocumento, 
                    ...this.documento,
                    id_documento_categoria: item ? item.id : null, 
                    id_usuario: this.$usuario.id,
                    _fuente: "IntranetDocumento"
                });

                this.debugForm(form_data)
                const config = { headers: { 'Content-Type': 'multipart/form-data' } };

                this.$http.post(
                    Vue.prototype.$url_post,
                    form_data,
                    config
                )
                .then(response => {

                    this.debugResponse(response);

                    if (response.status === 200) {
                        const data = response.data;

                        resultadoSolicitudDefecto(data);

                        if (data.ok) {

                            this.itemDocumento = {
                                opcional: false,
                                nombre: '',
                                tipo: 1,
                                descripcion: '',
                            }

                            this.documento = {
                                tipo: '',
                                opcional: false
                            }

                            this.cargarSubcategorias(item, true);
                            //this.$forceUpdate();
                        }
                    }
                    else if (response.status === 500) {
                        mensajeError('Error de servidor.');
                    }
                }).catch(err => {
                    this.debugError(err);
                });
            },

            seleccion(item) {
                this.$emit('seleccion', item);
            },

            seleccionDocumento(item) {
                this.$emit('seleccionDocumento', item);
            },

            urlArchivo(nombre_archivo) {
                return Vue.prototype.$uploads_doc_dir + nombre_archivo;
            },
        }
    }
</script>

<style scoped>
    .loader i {
        display: inline-block;
    }

    .btn-crear-categoria {
        cursor: pointer;
        pointer-events: all;
    }

    .comando {
        padding-left: 2px;
    }

    .comando a {
        color: #888;
    }

    .acciones-directorio {
        display: flex;
        position: relative;
    }

    .acciones-directorio a{
        display: block;
        margin-left: 1rem;
        position: absolute;
        left: -999px;        
    }

    .acciones-directorio a:hover{
        color: #2a41e8;
    }

    .acciones-directorio a:first-child{
        display: block;
        position: unset;

    }

    .acciones-directorio:hover a:nth-child(2){
        display: block;
        left: 4.8rem;
    }

    .acciones-directorio:hover a:nth-child(3){
        display: block;
        left: 10rem;
    }

    .seleccionado > a {
        font-weight: bold;
    }

    .first-icon{
        right: 5%;
    }
</style>