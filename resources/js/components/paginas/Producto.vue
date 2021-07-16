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
        <template slot="avatar" slot-scope="row">
            <div class="freelancer-avatar">
                <!--div class="verified-badge"></div-->
                <a href="#"><img :src="avatarUrl(row.item)" alt=""></a>
            </div>
        </template>

        <template slot="contenido_item" slot-scope="row">
            <h4>{{ row.item.nombre || row.item.tipo }} {{ row.item.marca }} <mark class="color" v-show="row.item.cantidad > 1">{{ row.item.cantidad }}</mark></h4>

            <!-- Details -->
            <span class="freelancer-detail-item" v-if="row.item.id_tipo">
                <span><b>Tipo:</b> {{ row.item.tipo }}</span>
            </span>

            <span class="freelancer-detail-item" v-if="row.item.id_marca">
                <span><b>Modelo:</b> {{ row.item.marca }}</span>
            </span>

            <span class="freelancer-detail-item" v-if="row.item.id_modelo">
                <span><b>Modelo:</b> {{ row.item.modelo }}</span>
            </span>

            <span class="freelancer-detail-item" v-if="row.item.serie">
                <span><b>Num. de Serie:</b> {{ row.item.serie }}</span>
            </span>

            <div>
                <span class="detail" v-if="row.item.id_ubicacion">
                    <span>{{ row.item.empresa }} &gt; {{ row.item.sucursal }} &gt; {{ row.item.departamento }} &gt; {{ row.item.sub_departamento }} &gt; {{ row.item.ubicacion }}</span>
                </span>
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

                                    <div class="row">
                                        <div class="col-xl-6">
                                            <input-texto
                                                    v-model="item.nombre"
                                                    nombre="nombre"
                                                    etiqueta="Nombre"
                                            ></input-texto>
                                        </div>

                                        <div class="col-xl-6">
                                            <input-texto
                                                    v-model="item.codigo_unico"
                                                    nombre="codigo_unico"
                                                    etiqueta="Num. de serie"
                                            ></input-texto>
                                        </div>
                                    </div>

                                    <input-objeto
                                            v-model="item.categoria.id"
                                            nombre="id_categoria"
                                            etiqueta="Categoría"
                                            fuente="Categoria"
                                            :items="categorias"
                                            :url="urls.post"
                                            @guardado="actualizarObjetoCategoria"
                                    >
                                        <template slot="modal">
                                            <div class="row">
                                                <div class="col-xl-8">
                                                    <input-texto
                                                            v-model="item.categoria.nombre"
                                                            nombre="nombre"
                                                            etiqueta="Nombre"
                                                    ></input-texto>
                                                </div>

                                                <div class="col-xl-4">
                                                    <input-texto
                                                            v-model="item.categoria.abreviatura"
                                                            nombre="abreviatura"
                                                            etiqueta="Abreviatura"
                                                    ></input-texto>
                                                </div>
                                            </div>
                                        </template>
                                    </input-objeto>

                                    <input-objeto
                                            v-model="item.tipo.id"
                                            nombre="id_tipo"
                                            etiqueta="Tipo"
                                            fuente="TipoProducto"
                                            :items="tipos"
                                            :url="urls.post"
                                            @guardado="actualizarObjetoTipo"
                                            @cambiado="actualizarIdTipo"
                                    >
                                        <template slot="modal">
                                            <div class="row">
                                                <div class="col-xl-8">
                                                    <input-texto
                                                            v-model="item.tipo.nombre"
                                                            nombre="nombre"
                                                            etiqueta="Nombre"
                                                    ></input-texto>
                                                </div>

                                                <div class="col-xl-4">
                                                    <input-texto
                                                            v-model="item.tipo.abreviatura"
                                                            nombre="abreviatura"
                                                            etiqueta="Abreviatura"
                                                    ></input-texto>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-xl-12">
                                                    <input-seleccion
                                                            v-model="item.tipo.atributo"
                                                            nombre="atributos"
                                                            etiqueta="Atributos"
                                                            :items="atributos"
                                                            :multiple="true"
                                                    ></input-seleccion>
                                                </div>
                                            </div>
                                        </template>
                                    </input-objeto>

                                    <input-objeto
                                            v-model="item.marca.id"
                                            nombre="id_marca"
                                            etiqueta="Marca"
                                            fuente="Marca"
                                            :items="marcas"
                                            :url="urls.post"
                                            @guardado="actualizarObjetoMarca"
                                    >
                                        <template slot="modal">
                                            <div class="row">
                                                <div class="col-xl-8">
                                                    <input-texto
                                                            v-model="item.marca.nombre"
                                                            nombre="nombre"
                                                            etiqueta="Nombre"
                                                    ></input-texto>
                                                </div>

                                                <div class="col-xl-4">
                                                    <input-texto
                                                            v-model="item.marca.abreviatura"
                                                            nombre="abreviatura"
                                                            etiqueta="Abreviatura"
                                                    ></input-texto>
                                                </div>
                                            </div>
                                        </template>
                                    </input-objeto>

                                    <input-objeto
                                            v-model="item.modelo.id"
                                            nombre="id_modelo"
                                            etiqueta="Modelo"
                                            fuente="ModeloProducto"
                                            :items="modelos"
                                            :url="urls.post"
                                            @guardado="actualizarObjetoModelo"
                                    >
                                        <template slot="modal">
                                            <div class="row">
                                                <div class="col-xl-8">
                                                    <input-texto
                                                            v-model="item.modelo.nombre"
                                                            nombre="nombre"
                                                            etiqueta="Nombre"
                                                    ></input-texto>
                                                </div>

                                                <div class="col-xl-4">
                                                    <input-texto
                                                            v-model="item.modelo.abreviatura"
                                                            nombre="abreviatura"
                                                            etiqueta="Abreviatura"
                                                    ></input-texto>
                                                </div>
                                            </div>
                                        </template>
                                    </input-objeto>
                                </div>

                                <div class="col-xl-6">
                                    <contenedor-multiple
                                            etiqueta="Foto"
                                            :total="3"
                                    >
                                        <template slot="contenido1">
                                            <input-imagen
                                                    v-model="item.foto"
                                                    nombre="foto_1"
                                                    etiqueta=""
                                                    altura="400"
                                            ></input-imagen>
                                        </template>

                                        <template slot="contenido2">
                                            <input-imagen
                                                    v-model="item.foto2"
                                                    nombre="foto_2"
                                                    etiqueta=""
                                                    altura="400"
                                            ></input-imagen>
                                        </template>

                                        <template slot="contenido3">
                                            <input-imagen
                                                    v-model="item.foto3"
                                                    nombre="foto_3"
                                                    etiqueta=""
                                                    altura="400"
                                            ></input-imagen>
                                        </template>
                                    </contenedor-multiple>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xl-12">
                                    <input-check
                                            v-model="establecer_ubicacion"
                                            nombre="establecer_ubicacion"
                                            etiqueta="Establecer ubicación"
                                    ></input-check>
                                </div>
                            </div>

                            <div v-if="establecer_ubicacion">
                                <div class="message-time-sign">
                                    <span>Ubicación</span>
                                </div>

                                <div class="row">
                                    <div class="col-xl-6">
                                        <input-seleccion
                                                v-model="item.id_empresa"
                                                nombre="id_empresa"
                                                etiqueta="Empresa"
                                                :items="empresas"
                                        ></input-seleccion>
                                    </div>

                                    <div class="col-xl-6">
                                        <input-seleccion
                                                v-model="item.id_sucursal"
                                                nombre="id_sucursal"
                                                etiqueta="Sucursal"
                                                :items="sucursalesFiltrados"
                                                :indice="indice_sucursales"
                                        ></input-seleccion>
                                    </div>

                                    <div class="col-xl-6">
                                        <input-seleccion
                                                v-model="item.id_departamento"
                                                nombre="id_departamento"
                                                etiqueta="Departamento"
                                                :items="departamentosFiltrados"
                                                :indice="indice_departamentos"
                                        ></input-seleccion>
                                    </div>

                                    <div class="col-xl-6">
                                        <input-seleccion
                                                v-model="item.id_sub_departamento"
                                                nombre="id_sub_departamento"
                                                etiqueta="Sub-Departamento"
                                                :items="subDepartamentosFiltrados"
                                                :indice="indice_sub_departamentos"
                                        ></input-seleccion>
                                    </div>

                                    <div class="col-xl-6">
                                        <input-seleccion
                                                v-model="item.id_ubicacion"
                                                nombre="id_ubicacion"
                                                etiqueta="Ubicación"
                                                :items="UbicacionesFiltradas"
                                                :indice="indice_ubicaciones"
                                        ></input-seleccion>
                                    </div>
                                </div>
                            </div>

                            <div class="message-time-sign" v-show="lista_atributos.length">
                                <span>Atributos</span>
                            </div>

                            <div class="row">
                                <div class="col-xl-4" v-for="attr in lista_atributos">
                                    <div v-if="attr.tipo === 1">
                                        <input-texto
                                                v-model="item.atributos[attr.nombre]"
                                                :nombre="'atributo_' + attr.id"
                                                :etiqueta="attr.nombre"
                                        ></input-texto>
                                    </div>

                                    <div v-else-if="attr.tipo === 2">
                                        <input-check
                                                v-model="item.atributos[attr.nombre]"
                                                :nombre="'atributo_' + attr.id"
                                                :etiqueta="attr.nombre"
                                        ></input-check>
                                    </div>

                                    <div v-else>
                                        <mark><i class="icon-line-awesome-warning"></i> Tipo de atributo no reconocido.</mark>
                                    </div>
                                </div>
                            </div>

                            <template v-if="!item.id">
                                <div class="message-time-sign">
                                    <span>Estado</span>
                                </div>

                                <div class="row">
                                    <div class="col-xl-4">
                                        <input-seleccion
                                                v-model="item.estado.status"
                                                nombre="status"
                                                etiqueta="Estado"
                                                :buscador="false"
                                                :items="estados"
                                        ></input-seleccion>
                                    </div>

                                    <div class="col-xl-8">
                                        <input-texto
                                                v-model="item.estado.observaciones"
                                                nombre="observaciones"
                                                etiqueta="Observaciones"
                                        ></input-texto>
                                    </div>
                                </div>
                            </template>

                        </div>
                    </div>
                </div>

            </div>
        </template>
    </crud>
</template>

<script>
    export default {
        name: "Producto",

        props: {
            urls: Object,
            avatar_defecto: String,
        },

        data: () => ({
            fuente: 'Producto',
            titulo_singular: 'Producto',
            titulo_plural: 'Productos',
            icono: 'icon-feather-box',
            items: [],
            item: {
                id: 0,
                /*id_marca: '',
                id_modelo: '',*/
                nombre: '',
                codigo_unico: '',
                foto: '',
                foto2: '',
                foto3: '',
                cantidad: '',

                id_empresa: null,
                id_sucursal: null,
                id_departamento: null,
                id_sub_departamento: null,
                id_ubicacion: null,

                tipo: {
                    id: 0,
                    nombre: '',
                    abreviatura: '',
                    atributo: '',
                },
                categoria: {
                    id: 0,
                    nombre: '',
                    abreviatura: '',
                },
                marca: {
                    id: 0,
                    nombre: '',
                    abreviatura: '',
                },
                modelo: {
                    id: 0,
                    nombre: '',
                    abreviatura: '',
                },

                estado: {
                    status: 1,
                    observaciones: '',
                },

                atributos: [],
            },
            vista_items: true,
            vista_formulario: false,
            texto_buscado: '',
            propiedades_buscadas: [
                'nombre',
                //'tipo',
                //'marca',
                //'modelo',
                'codigo_unico',
                'empresa',
                'sucursal',
                'departamento',
                'sub_departamento',
                'ubicacion',
                'tipo',
                'marca',
                'modelo',
            ],
            texto_confirmar_eliminar: '¿Está seguro que quiere eliminar el producto?',
            tipos: [],
            categorias: [],
            marcas: [],
            modelos: [],
            atributos: [],
            atributos_por_tipos: [],
            lista_atributos: [],
            establecer_ubicacion: false,
            empresas: [],
            sucursales: [],
            departamentos: [],
            sub_departamentos: [],
            ubicaciones: [],
            indice_sucursales: 0,
            indice_departamentos: 0,
            indice_sub_departamentos: 0,
            indice_ubicaciones: 0,
            estados: [
                {
                    id: 1,
                    nombre: 'Nuevo'
                },
                {
                    id: 2,
                    nombre: 'Buen estado'
                },
                {
                    id: 3,
                    nombre: 'Dañado'
                },
            ]
        }),

        computed: {
            sucursalesFiltrados() {
                if (this.item.id_empresa === null) return [];

                let lista = [];

                for (const item of this.sucursales) {
                    if (item.id_empresa == this.item.id_empresa) {
                        lista.push(item);
                    }
                }

                this.item.id_sucursal = null;
                this.item.id_departamento = null;
                this.item.id_sub_departamento = null;
                this.item.id_ubicacion = null;
                
                this.indice_sucursales++;

                return lista;
            },

            departamentosFiltrados() {
                if (this.item.id_sucursal === null) return [];

                let lista = [];

                for (const item of this.departamentos) {
                    if (item.id_sucursal == this.item.id_sucursal) {
                        console.log(item.nombre);
                        lista.push(item);
                    }
                }

                this.item.id_departamento = null;
                this.item.id_sub_departamento = null;
                this.item.id_ubicacion = null;

                this.indice_departamentos++;

                return lista;
            },

            subDepartamentosFiltrados() {
                if (this.item.id_departamento === null) return [];

                let lista = [];

                for (const item of this.sub_departamentos) {
                    if (item.id_departamento == this.item.id_departamento) {
                        lista.push(item);
                    }
                }

                this.item.id_sub_departamento = null;
                this.item.id_ubicacion = null;

                this.indice_sub_departamentos++;

                return lista;
            },

            UbicacionesFiltradas() {
                if (this.item.id_sub_departamento === null) return [];

                let lista = [];

                for (const item of this.ubicaciones) {
                    if (item.id_sub_departamento == this.item.id_sub_departamento) {
                        lista.push(item);
                    }
                }

                this.item.id_ubicacion = null;

                this.indice_ubicaciones++;

                return lista;
            }
        },

        methods: {
            limpiarItem() {
                this.item.id = 0;
                this.item.nombre = '';
                this.item.tipo = {
                    id: 0,
                    nombre: '',
                    abreviatura: '',
                    atributo: '',
                };
                this.item.categoria = {
                    id: 0,
                    nombre: '',
                    abreviatura: '',
                };
                this.item.marca = {
                    id: 0,
                    nombre: '',
                    abreviatura: '',
                };
                this.item.modelo = {
                    id: 0,
                    nombre: '',
                    abreviatura: '',
                };
                this.item.estado = {
                    status: 1,
                    observaciones: '',
                };
                this.item.atributos = [];
                this.item.serie = '';
                this.item.color = '';
                this.item.responsable = '';
                this.item.ubicacion = '';
                this.item.cantidad = '';
                this.item.foto = '';
                this.item.foto2 = '';
                this.item.foto3 = '';
                this.item.id_empresa = null;
                this.item.id_sucursal = null;
                this.item.id_departamento = null;
                this.item.id_sub_departamento = null;
                this.item.id_ubicacion = null;
            },

            setItemData(data) {
                //console.log({data})
                this.item.id = data.id;
                this.item.nombre = data.nombre;
                this.item.codigo_unico = data.codigo_unico;
                this.item.categoria.id = data.id_categoria;
                this.item.tipo.id = data.id_tipo;
                this.item.marca.id = data.id_marca;
                this.item.modelo.id = data.id_modelo;
                this.item.id_empresa = data.id_empresa;
                this.item.id_sucursal = data.id_sucursal;
                this.item.id_departamento = data.id_departamento;
                this.item.id_sub_departamento = data.id_sub_departamento;
                this.item.id_ubicacion = data.id_ubicacion;

                for (let i = 1; i <= 3; i++) {
                    const img = (typeof data['foto_' + i] === 'string' && data['foto_' + i].length) ? data['foto_' + i] : '';
                    this.item['foto' + (i > 1 ? i : '')] = img;
                }
                //this.item.serie = data.serie;
                //this.item.color = data.color;
                //this.item.estado = data.estado;
                //this.item.responsable = data.responsable;
                //this.item.ubicacion = data.ubicacion;
                //this.item.cantidad = data.cantidad;
                //this.item.foto = data.foto;

                const atributos = data.atributos;
                if (typeof atributos === 'object' && atributos !== null) {
                    for (const atributo of atributos) {
                        this.$set(this.item.atributos, atributo['nombre'], atributo.valor);
                    }
                }

                this.listaAtributos(this.item.tipo.id);
            },

            avatarUrl(item) {
                if (typeof item.foto === 'string' && item.foto.length) {
                    return this.$uploads_img_dir + 's/' + item.foto; //Vue.prototype.$uploads_img_dir
                }
                return this.$img_placeholder;
            },

            actualizarObjetoTipo(data) {
                this.tipos.push({
                    id: data.id,
                    nombre: this.item.tipo.nombre,
                    abreviatura: this.item.tipo.abreviatura.toUpperCase(),
                });

                this.item.tipo.id = data.id;
            },

            actualizarObjetoCategoria(data) {
                this.categorias.push({
                    id: data.id,
                    nombre: this.item.categoria.nombre,
                    abreviatura: this.item.categoria.abreviatura.toUpperCase(),
                });

                this.item.categoria.id = data.id;
            },

            actualizarObjetoMarca(data) {
                this.marcas.push({
                    id: data.id,
                    nombre: this.item.marca.nombre,
                    abreviatura: this.item.marca.abreviatura.toUpperCase(),
                });

                this.item.marca.id = data.id;
            },

            actualizarObjetoModelo(data) {
                this.modelos.push({
                    id: data.id,
                    nombre: this.item.modelo.nombre,
                    abreviatura: this.item.modelo.abreviatura.toUpperCase(),
                });

                this.item.modelo.id = data.id;
            },

            actualizarIdTipo(val) {
                //this.id_tipo = val;
                this.listaAtributos(val);
            },

            cargarDataAdicional() {
                this.$http.get(this.urls.get, {
                    params: {
                        _fuente: this.fuente,
                        _accion: 'cargarListados',
                    }
                })
                .then(response => {
                    if (response.status === 200) {
                        const data = response.data;

                        this.tipos = data['tipos'];
                        this.categorias = data['categorias'];
                        this.marcas = data['marcas'];
                        this.modelos = data['modelos'];
                        this.atributos = data['atributos'];
                        this.atributos_por_tipos = data['atributos_por_tipos'];

                        this.empresas = data['empresas'];
                        this.sucursales = data['sucursales'];
                        this.departamentos = data['departamentos'];
                        this.sub_departamentos = data['sub_departamentos'];
                        this.ubicaciones = data['ubicaciones'];

                        if (this.tipos.length) {
                            this.listaAtributos(this.tipos[0].id);
                        }
                    }
                });
            },

            listaAtributos(val) {
                if (!val) return;
                for (const it of this.atributos_por_tipos) {
                    if (it.id_tipo == val) {
                        let atributos = [];
                        for (const id_atributo of it.atributos) {
                            for (const atributo of this.atributos) {
                                if (atributo.id == id_atributo) {
                                    atributos.push(atributo);
                                    break;
                                }
                            }
                        }
                        this.lista_atributos = atributos;
                        return;
                    }
                }
                this.lista_atributos = [];
            },

            nombreModelo(id_modelo) {
                if (!id_modelo) return '';

                for (const modelo of this.modelos) {
                    if (modelo.id == id_modelo) {
                        return modelo.nombre;
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