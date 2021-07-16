<template>
    <li :class="[tieneHijos?'folder-root':'', abierto?'open':'closed', seleccionado?'seleccionado':'']" :data-id_item="item.id">
        <a href="#" @click.prevent="cargarSubcategorias(item)">
            {{ item.nombre }}
            <span class="loader" v-show="cargando">&nbsp;<i class="icon-line-awesome-hourglass-2 fa-spin"></i></span>
        </a>
        <ul>
            <template v-if="tieneHijos">
                <directorio
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
                    :key="sub_item.id"
                >
                </directorio>
            </template>
            <!--<li class="comando" v-if="permite_agregar">
                <a href="#" @click.prevent="crearArchivo(item)">
                    <i class="icon-feather-file-plus"></i> Nuevo archivo
                </a>
            </li>-->
            <li class="comando" v-if="permite_agregar_directorio">
                <a href="#" @click.prevent="crearDirectorio(item)">
                    <i class="icon-feather-folder-plus"></i> Nueva categoría
                </a>
            </li>
            <li v-if="agregar_directorio">
                <div class="input-with-icon margin-right-50">
                    <input type="text" placeholder="Nombre de la categoría" v-model="nuevo_directorio" @keypress.enter="guardarDirectorio(item)">
                    <i class="btn-crear-categoria icon-feather-check" @click="guardarDirectorio(item)"></i>
                </div>
            </li>

            <template v-if="tieneArchivos">
                <li
                    v-for="archivo in item.archivos"
                    class="file-item"
                    :data-id_item="item.id"
                    :key="archivo.id"
                >
                    <a :href="urlArchivo(archivo.archivo)" target="_blank" download>{{ archivo.nombre }}</a>
                </li>
            </template>
        </ul>
    </li>
</template>

<script>
    export default {
        name: "Directorio.vue",

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
            nuevo_directorio: '',
            seleccionado: false,
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
                            id_padre: item.id
                        }
                    })
                        .then(response => {
                            if (response.status === 200) {
                                const data = response.data;

                                if (data.ok) {
                                    item.items = data.items;
                                    this.$forceUpdate();
                                }
                            }

                            this.cargando = false;
                        });
                }
            },

            crearDirectorio(item) {
                if (typeof this.fuente === 'string' && this.fuente.length && this.accion_directorio) {
                    this.agregar_directorio = !this.agregar_directorio;
                }
                else {
                    this.$emit('crearDirectorio', item);
                }
            },

            crearArchivo(item) {
                this.$emit('crearArchivo', item);
            },

            guardarDirectorio(item) {
                if (this.nuevo_directorio.length) {
                    this.agregar_directorio = false;

                    this.$http.post(Vue.prototype.$url_post, {
                        _fuente: this.fuente,
                        _accion: this.accion_directorio,
                        nombre: this.nuevo_directorio,
                        id_padre: item ? item.id : null,
                    })
                        .then(response => {
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
                        });
                }
            },

            seleccion(item) {
                this.$emit('seleccion', item);
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

    .seleccionado > a {
        font-weight: bold;
    }
</style>