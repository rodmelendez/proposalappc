<template>
    <main>
        <crud
                :_titulo_singular="titulo_singular"
                :_titulo_plural="titulo_plural"
                :_icono="icono"
                :_fuente="fuente"
                :_subdirectorio="subdirectorio"
                :_propiedades_buscadas="propiedades_buscadas"
                :_items="itemsFiltrados"
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
                :indice="indice_crud"
        >
            <template slot="cabecera">
                <div style="padding:20px">
                    <div class="notify-box margin-top-15">

                        <!-- celda -->
                        <div class="sort-by margin-left-20">
                            <span>Celda:</span>
                            <div class="btn-group bootstrap-select hide-tick">
                                <button type="button" class="btn dropdown-toggle btn-default" data-toggle="dropdown" role="button" title="Relevance">
                                    <span class="filter-option pull-left">{{ descCeldaSeleccionada }}</span>&nbsp;<span class="bs-caret"><span class="caret"></span></span>
                                </button>
                                <div class="dropdown-menu open" role="combobox">
                                    <ul class="dropdown-menu inner" role="listbox" aria-expanded="false">
                                        <li data-original-index="0" class="selected" v-for="celda in celdasFiltradas" :key="celda.id">
                                            <a tabindex="0" class="" data-tokens="null" role="option" @click="cambiarCeldaFiltro(celda.id)">
                                                <span class="text">{{ celda.nombre }}</span>
                                                <span class="glyphicon glyphicon-ok check-mark"></span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- división -->
                        <div class="sort-by margin-left-20">
                            <span>División:</span>
                            <div class="btn-group bootstrap-select hide-tick">
                                <button type="button" class="btn dropdown-toggle btn-default" data-toggle="dropdown" role="button" title="Relevance">
                                    <span class="filter-option pull-left">{{ descDivisionSeleccionada }}</span>&nbsp;<span class="bs-caret"><span class="caret"></span></span>
                                </button>
                                <div class="dropdown-menu open" role="combobox">
                                    <ul class="dropdown-menu inner" role="listbox" aria-expanded="false">
                                        <li data-original-index="0" class="selected" v-for="division in divisionesFiltradas" :key="division.id">
                                            <a tabindex="0" class="" data-tokens="null" role="option" @click="cambiarDivisionFiltro(division.id)">
                                                <span class="text">{{ division.nombre }}</span>
                                                <span class="glyphicon glyphicon-ok check-mark"></span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- bodega -->
                        <div class="sort-by">
                            <span>Bodega:</span>
                            <div class="btn-group bootstrap-select hide-tick">
                                <button type="button" class="btn dropdown-toggle btn-default" data-toggle="dropdown" role="button" title="Relevance">
                                    <span class="filter-option pull-left">{{ descBodegaSeleccionada }}</span>&nbsp;<span class="bs-caret"><span class="caret"></span></span>
                                </button>
                                <div class="dropdown-menu open" role="combobox">
                                    <ul class="dropdown-menu inner" role="listbox" aria-expanded="false">
                                        <li data-original-index="0" class="selected" v-for="bodega in bodegasFiltro" :key="bodega.id">
                                            <a tabindex="0" class="" data-tokens="null" role="option" @click="cambiarBodegaFiltro(bodega.id)">
                                                <span class="text">{{ bodega.nombre }}</span>
                                                <span class="glyphicon glyphicon-ok check-mark"></span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </template>

            <template slot="avatar" slot-scope="row">
                <div class="freelancer-avatar">
                    <!--div class="verified-badge"></div-->
                    <a><img :src="avatarUrl(row.item)" alt=""></a>
                </div>
            </template>

            <template slot="contenido_item" slot-scope="row">
                <h4>{{ row.item.referencia }} - {{ row.item.nombre }}&nbsp; <small>({{ row.item.cantidad }})</small></h4>

                <!-- Details -->
                <span class="freelancer-detail-item">
                    <span><b>Tipo:</b> {{ itemTipoProducto(row.item.id_tipo_producto).nombre }}</span>
                </span>

                <div class="row">
                    <div class="col-xl-12">
                        <!-- categoría -->
                        <span class="detail">
                            <span><b>Categoría:</b> {{ itemCategoria(row.item.id_categoria).nombre }}</span>
                        </span>

                        <!-- subcategoría -->
                        <span class="detail">
                            <span><b>Sub-Categoría:</b> {{ itemSubcategoria(row.item.id_subcategoria).nombre }}</span>
                        </span>
                    </div>

                    <div class="col-xl-12">
                        <!-- marca -->
                        <span class="detail">
                            <span><b>Marca:</b> {{ itemMarca(row.item.id_marca).nombre }}</span>
                        </span>

                        <!-- modelo -->
                        <span class="detail">
                            <span><b>Modelo:</b> {{ itemModelo(row.item.id_modelo).nombre }}</span>
                        </span>
                    </div>
                </div>

                <div class="row detail margin-top-20">
                    <div class="col-xl-12">
                        <div v-html="descCeldasCantidades(row.item)"></div>
                    </div>
                </div>
            </template>

            <template slot="botones" slot-scope="row">
                <a class="button gray ripple-effect ico" title="Ver movimientos" @click="verMovimientos(row.item)">
                    <i class="icon-feather-list"></i>
                </a>
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
                                            <div class="col-xl-12">
                                                <input-texto
                                                        v-model="item.nombre"
                                                        nombre="nombre"
                                                        etiqueta="Nombre"
                                                ></input-texto>
                                            </div>

                                            <div class="col-xl-6">
                                                <input-texto
                                                        v-model="item.referencia"
                                                        nombre="referencia"
                                                        etiqueta="Referencia"
                                                ></input-texto>
                                            </div>

                                            <div class="col-xl-6">
                                                <input-texto
                                                        v-model="item.referencia_alternativa"
                                                        nombre="referencia_alternativa"
                                                        etiqueta="Referencia secundaria"
                                                ></input-texto>
                                            </div>
                                        </div>

                                        <input-objeto
                                                v-model="item.tipo_producto.id"
                                                nombre="id_tipo_producto"
                                                etiqueta="Tipo de Producto"
                                                fuente="TipoProducto"
                                                _subdirectorio="Almacen"
                                                :items="tipos_productos"
                                                :url="urls.post"
                                                @guardado="actualizarObjetoTipoProducto"
                                        >
                                            <template slot="modal">
                                                <div class="row">
                                                    <div class="col-xl-12">
                                                        <input-texto
                                                                v-model="item.tipo_producto.nombre"
                                                                nombre="nombre"
                                                                etiqueta="Nombre"
                                                        ></input-texto>
                                                    </div>
                                                </div>
                                            </template>
                                        </input-objeto>

                                        <input-objeto
                                                v-model="item.categoria.id"
                                                nombre="id_categoria"
                                                etiqueta="Categoría"
                                                fuente="Categoria"
                                                _subdirectorio="Almacen"
                                                :items="categorias"
                                                :url="urls.post"
                                                @guardado="actualizarObjetoCategoria"
                                                @cambiado="actualizarIdCategoria"
                                        >
                                            <template slot="modal">
                                                <div class="row">
                                                    <div class="col-xl-12">
                                                        <input-texto
                                                                v-model="item.categoria.nombre"
                                                                nombre="nombre"
                                                                etiqueta="Nombre"
                                                        ></input-texto>
                                                    </div>
                                                </div>
                                            </template>
                                        </input-objeto>

                                        <input-objeto
                                                v-model="item.subcategoria.id"
                                                nombre="id_subcategoria"
                                                etiqueta="Sub-categoría"
                                                fuente="Subcategoria"
                                                _subdirectorio="Almacen"
                                                :items="listaSubcategorias"
                                                :url="urls.post"
                                                @guardado="actualizarObjetoSubcategoria"
                                                cambiado="actualizarIdTipo"
                                                :indice="indice"
                                        >
                                            <template slot="modal">
                                                <div class="row">
                                                    <div class="col-xl-12">
                                                        <input-texto
                                                                v-model="item.subcategoria.nombre"
                                                                nombre="nombre"
                                                                etiqueta="Nombre"
                                                        ></input-texto>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="id_categoria" :value="item.categoria.id">
                                            </template>
                                        </input-objeto>

                                        <input-objeto
                                                v-model="item.marca.id"
                                                nombre="id_marca"
                                                etiqueta="Marca"
                                                fuente="Marca"
                                                _subdirectorio="Almacen"
                                                :items="marcas"
                                                :url="urls.post"
                                                @guardado="actualizarObjetoMarca"
                                        >
                                            <template slot="modal">
                                                <div class="row">
                                                    <div class="col-xl-12">
                                                        <input-texto
                                                                v-model="item.marca.nombre"
                                                                nombre="nombre"
                                                                etiqueta="Nombre"
                                                        ></input-texto>
                                                    </div>
                                                </div>
                                            </template>
                                        </input-objeto>

                                        <input-objeto
                                                v-model="item.modelo.id"
                                                nombre="id_modelo"
                                                etiqueta="Modelo"
                                                fuente="Modelo"
                                                _subdirectorio="Almacen"
                                                :items="listaModelos"
                                                :url="urls.post"
                                                @guardado="actualizarObjetoModelo"
                                        >
                                            <template slot="modal">
                                                <div class="row">
                                                    <div class="col-xl-12">
                                                        <input-texto
                                                                v-model="item.modelo.nombre"
                                                                nombre="nombre"
                                                                etiqueta="Nombre"
                                                        ></input-texto>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="id_marca" :value="item.marca.id">
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
                                                        v-model="item.foto1"
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

                                        <input-textos
                                                v-model="item.upc"
                                                nombre="upcs"
                                                etiqueta="Códigos UPC"
                                        ></input-textos>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </template>
        </crud>

        <sweet-modal ref="modal_movimientos">
            <template>
                <ul class="dashboard-box-list">
                    <li v-for="movimiento in lista_movimientos" :key="movimiento.id">
                        <span class="notification-icon"><i :class="movimiento.cantidad > 0 ? 'icon-line-awesome-sign-in' : 'icon-line-awesome-sign-out'"></i></span>
                        <span class="notification-text">
                            <strong>{{ fechaHoraDesc(movimiento.fecha_creacion) }}</strong>, {{ movimiento.cantidad > 0 ? 'entrada' : 'salida' }} de <strong>{{ Math.abs(movimiento.cantidad) }}</strong> unidades. <a></a>
                        </span>
                        <!-- Buttons -->
                        <!--div class="buttons-to-right single-right-button">
                            <a href="#" class="button ripple-effect ico" data-tippy-placement="left" data-tippy="" data-original-title="Mark as read"><i class="icon-feather-check-square"></i></a>
                        </div-->
                    </li>
                </ul>

                <button class="button button-ok ripple-effect button-sliding-icon big margin-top-30 margin-bottom-20" @click="cerrarModalMovimientos">
                    OK <i class="icon-feather-check"></i>
                </button>
            </template>
        </sweet-modal>
    </main>
</template>

<script>
    import { SweetModal } from 'sweet-modal-vue'

    export default {
        name: "Producto",

        components: {
            SweetModal,
        },

        props: {
            urls: Object,
            avatar_defecto: String,
        },

        data: () => ({
            fuente: 'Producto',
            subdirectorio: 'Almacen',
            titulo_singular: 'Producto',
            titulo_plural: 'Productos',
            icono: 'icon-line-awesome-cubes',
            items: [],
            item: {
                id: 0,
                nombre: '',
                referencia: '',
                referencia_alternativa: '',
                tipo_producto: {
                    id: 0,
                    nombre: '',
                },
                categoria: {
                    id: 0,
                    nombre: '',
                },
                subcategoria: {
                    id: 0,
                    nombre: '',
                },
                marca: {
                    id: 0,
                    nombre: '',
                },
                modelo: {
                    id: 0,
                    nombre: '',
                },
                upc: '',
                foto1: '',
                foto2: '',
                foto3: '',
            },
            propiedades_buscadas: [
                'nombre',
                'referencia',
                'referencia_alternativa',
                'upc',
            ],
            atributos: [],
            tipos_productos: [],
            categorias: [],
            subcategorias: [],
            marcas: [],
            modelos: [],

            bodegas: [],
            divisiones: [],
            celdas: [],

            id_bodega_filtro: 0,
            id_division_filtro: 0,
            id_celda_filtro: 0,

            lista_movimientos: [],

            indice_crud: 0,
            indice: 0,
        }),

        computed: {
            itemsFiltrados() {
                if (!this.id_bodega_filtro) {
                    return this.items;
                }

                const id_bodega = parseInt(this.id_bodega_filtro) || 0;
                const id_division = parseInt(this.id_division_filtro) || 0;
                const id_celda = parseInt(this.id_celda_filtro) || 0;

                let items = [];

                for (const item of this.items) {
                    if (this.id_bodega_filtro && !item.bodegas.split('|').map(Number).includes(id_bodega)) {
                        continue;
                    }

                    if (this.id_division_filtro && !item.divisiones.split('|').map(Number).includes(id_division)) {
                        continue;
                    }

                    if (this.id_celda_filtro && !item.celdas.split('|').map(Number).includes(id_celda)) {
                        continue;
                    }

                    items.push(item);
                }

                /*setTimeout(() => {
                    this.indice_crud++;
                }, 500);*/

                return items;
            },

            listaSubcategorias() {
                let items = [];

                for (const item of this.subcategorias) {
                    if (item.id_categoria == this.item.categoria.id) {
                        items.push(item);
                    }
                }

                this.indice++;

                return items;
            },

            listaModelos() {
                let items = [];

                for (const item of this.modelos) {
                    if (item.id_marca == this.item.marca.id) {
                        items.push(item);
                    }
                }

                return items;
            },

            bodegasFiltro() {
                return Array.prototype.concat([
                    {
                        id: 0,
                        nombre: '—',
                    }
                ], this.bodegas);
            },

            divisionesFiltradas() {
                let items = [
                    {
                        id: 0,
                        nombre: '—',
                    }
                ];

                for (const item of this.divisiones) {
                    if (item.id_bodega == this.id_bodega_filtro) {
                        items.push(item);
                    }
                }

                return items;
            },

            celdasFiltradas() {
                let items = [
                    {
                        id: 0,
                        nombre: '—',
                    }
                ];

                for (const item of this.celdas) {
                    if (item.id_division == this.id_division_filtro) {
                        items.push(item);
                    }
                }

                return items;
            },

            descBodegaSeleccionada() {
                if (!this.id_bodega_filtro) {
                    return '(Seleccionar)';
                }

                return this.itemBodega(this.id_bodega_filtro).nombre;
            },

            descDivisionSeleccionada() {
                if (!this.id_division_filtro) {
                    return '(Seleccionar)';
                }

                return this.itemDivision(this.id_division_filtro).nombre;
            },

            descCeldaSeleccionada() {
                if (!this.id_celda_filtro) {
                    return '(Seleccionar)';
                }

                return this.itemCelda(this.id_celda_filtro).nombre;
            },
        },

        methods: {
            limpiarItem() {
                this.item.id = 0;
                this.item.nombre = '';
                this.item.referencia = '';
                this.item.referencia_alternativa = '';
                this.item.tipo_producto = {
                    id: 0,
                    nombre: '',
                };
                this.item.categoria = {
                    id: 0,
                    nombre: '',
                };
                this.item.subcategoria = {
                    id: 0,
                    nombre: '',
                };
                this.item.marca = {
                    id: 0,
                    nombre: '',
                };
                this.item.modelo = {
                    id: 0,
                    nombre: '',
                };
                this.item.upc = '';
                this.item.foto1 = '';
                this.item.foto2 = '';
                this.item.foto3 = '';
            },

            setItemData(data) {
                this.item.id = data.id;
                this.item.nombre = data.nombre;
                this.item.referencia = data.referencia;
                this.item.referencia_alternativa = data.referencia_alternativa;
                this.item.tipo_producto = this.itemTipoProducto(data.id_tipo_producto);
                this.item.categoria = this.itemCategoria(data.id_categoria);
                this.item.subcategoria = this.itemSubcategoria(data.id_subcategoria);
                this.item.marca = this.itemMarca(data.id_marca);
                this.item.modelo = this.itemModelo(data.id_modelo);
                this.item.upc = data.upc;
                this.item.foto1 = typeof data.foto1 === 'string' ? data.foto1 : '';
                this.item.foto2 = typeof data.foto2 === 'string' ? data.foto2 : '';
                this.item.foto3 = typeof data.foto3 === 'string' ? data.foto3 : '';

                setTimeout(() => {
                    this.$forceUpdate();
                }, 250);
            },

            cargarDataAdicional() {
                this.$http.get(this.urls.get, {
                    params: {
                        _fuente: 'Producto',
                        _subdirectorio: 'Almacen',
                        _accion: 'listaParametros',
                    }
                })
                    .then(response => {
                        if (response.status === 200) {
                            const data = response.data;

                            if (typeof data === 'object') {
                                this.tipos_productos = data['tipos_productos'];
                                this.categorias = data['categorias'];
                                this.subcategorias = data['subcategorias'];
                                this.marcas = data['marcas'];
                                this.modelos = data['modelos'];

                                this.bodegas = data['bodegas'];
                                this.divisiones = data['divisiones'];
                                this.celdas = data['celdas'];
                            }
                        }
                    });
            },

            itemTipoProducto(id) {
                return this.itemEnItems(id, this.tipos_productos);
            },

            itemCategoria(id) {
                return this.itemEnItems(id, this.categorias);
            },

            itemSubcategoria(id) {
                return this.itemEnItems(id, this.subcategorias);
            },

            itemMarca(id) {
                return this.itemEnItems(id, this.marcas);
            },

            itemModelo(id) {
                return this.itemEnItems(id, this.modelos);
            },

            itemBodega(id) {
                return this.itemEnItems(id, this.bodegas);
            },

            itemDivision(id) {
                return this.itemEnItems(id, this.divisiones);
            },

            itemCelda(id) {
                return this.itemEnItems(id, this.celdas);
            },

            itemEnItems(id, items) {
                for (const item of items) {
                    if (item.id == id) {
                        return item;
                    }
                }
                return { id: 0, nombre: '' };
            },

            actualizarObjetoTipoProducto(data) {
                this.tipos_productos.push({
                    id: data.id,
                    nombre: this.item.tipo_producto.nombre,
                });

                this.item.tipo_producto.id = data.id;
            },

            actualizarObjetoCategoria(data) {
                this.categorias.push({
                    id: data.id,
                    nombre: this.item.categoria.nombre,
                });

                this.item.categoria.id = data.id;
            },

            actualizarObjetoSubcategoria(data) {
                this.subcategorias.push({
                    id: data.id,
                    id_categoria: this.item.categoria.id,
                    nombre: this.item.subcategoria.nombre,
                });

                this.item.subcategoria.id = data.id;
            },

            actualizarObjetoMarca(data) {
                this.marcas.push({
                    id: data.id,
                    nombre: this.item.marca.nombre,
                });

                this.item.marca.id = data.id;
            },

            actualizarObjetoModelo(data) {
                this.modelos.push({
                    id: data.id,
                    id_marca: this.item.marca.id,
                    nombre: this.item.modelo.nombre,
                });

                this.item.modelo.id = data.id;
            },

            actualizarIdCategoria(val) {
                //this.item.categoria.id = val || 0;
            },

            avatarUrl(item) {
                if (typeof item.foto === 'string' && item.foto.length) {
                    return this.$uploads_img_dir + item.foto; //Vue.prototype.$uploads_img_dir
                }
                return this.$img_placeholder;
            },

            descCeldasCantidades(item) {
                const celdas = typeof item.celdas_cantidades === 'string' ? item.celdas_cantidades.split('|') : [];
                let html = '';

                for (const celda of celdas) {
                    const bdcc = celda.split(',');
                    if (bdcc.length === 3) {
                        const bodega = this.itemBodega(bdcc[0]).nombre;
                        const division = this.itemDivision(bdcc[1]).nombre;
                        const celda_cantidad = bdcc[2].split(':');
                        if (celda_cantidad.length !== 2) continue;
                        const celda = this.itemCelda(celda_cantidad[0]).nombre;
                        const cantidad = parseInt(celda_cantidad[1]) || 0;

                        html += `
                            <div>
                                <small>
                                    ${bodega} <i class="icon-line-awesome-angle-right"></i>
                                    ${division} <i class="icon-line-awesome-angle-right"></i>
                                    ${celda} &nbsp;
                                    <strong>(${cantidad})</strong>
                                </small>
                            </div>
                        `;
                    }
                }

                return html;
            },

            cambiarBodegaFiltro(id_bodega) {
                if (this.id_bodega_filtro !== id_bodega) {
                    this.id_bodega_filtro = id_bodega;

                    this.id_division_filtro = 0;
                    this.id_celda_filtro = 0;
                }
            },

            cambiarDivisionFiltro(id_division) {
                if (this.id_division_filtro !== id_division) {
                    this.id_division_filtro = id_division;

                    this.id_celda_filtro = 0;
                }
            },

            cambiarCeldaFiltro(id_celda) {
                this.id_celda_filtro = id_celda;
            },

            verMovimientos(item) {
                this.$http.get(this.urls.get, {
                    params: {
                        _fuente: 'Producto',
                        _subdirectorio: 'Almacen',
                        _accion: 'movimientos',
                        id_producto: item.id,
                    }
                })
                    .then(response => {
                        if (response.status === 200) {
                            const data = response.data;

                            this.lista_movimientos = data['movimientos'];

                            this.$refs['modal_movimientos'].open();
                        }
                    });
            },

            cerrarModalMovimientos() {
                this.$refs['modal_movimientos'].close();
            },

            fechaHoraDesc(fecha_hora) {
                return moment(fecha_hora, 'YYYY-MM-DD HH:mm:ss').format('dddd, D MMM YYYY, h:mma');
            },
        },

        mounted() {
            this.cargarData();
        },
    }
</script>

<style scoped>

</style>