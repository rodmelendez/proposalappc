<template>
    <main>
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
                :_fn_guardar="guardarItem"
                @itemDataSet="itemDataSet"
                @postItemEliminado="postItemEliminado"
                @postCargarData="postCargarData"
                @formularioMostrado="mostrarFormularioNuevo"
                @formularioEditarMostrado="mostrarFormulario"
        >
            <template slot="contenido_item" slot-scope="row">
                <h4>{{ row.item.referencia }} - {{ fechaDocumento(row.item.fecha) }} &nbsp; <span v-html="iconoStatus(row.item)"></span></h4>

                <span class="freelancer-detail-item">
                    <span><b>Estado:</b> {{ row.item.status == 2 ? 'Procesada' : 'Pendiente' }}</span>
                </span>

                <div class="row">
                    <div class="col-xl-12">
                        <span class="freelancer-detail-item">
                            <span><b>Total productos:</b> {{ row.item.total_productos }}</span>
                        </span>
                    </div>
                </div>

                <div class="row" v-if="row.item.comentario">
                    <div class="col-xl-12">
                        <span class="freelancer-detail-item">
                            <span><i>{{ row.item.comentario }}</i></span>
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
                                    <div class="col-xl-4">
                                        <input-fecha
                                                v-model="item.fecha"
                                                nombre="fecha"
                                                etiqueta="Fecha"
                                        ></input-fecha>
                                    </div>

                                    <div class="col-xl-8">
                                        <input-texto
                                                v-model="item.comentario"
                                                nombre="comentario"
                                                etiqueta="Comentario"
                                        ></input-texto>
                                    </div>
                                </div>

                                <div class="message-time-sign">
                                    <span>Productos</span>
                                </div>

                                <div class="row">
                                    <div class="col-xl-3">
                                        <div class="input-with-icon">
                                            <input type="text" placeholder="Buscar y agregar" class="with-border" v-model="buscar_codigo" @keypress.enter="agregarProducto">
                                            <i class="icon-line-awesome-barcode"></i>
                                        </div>
                                    </div>

                                    <div class="col-xl-1">
                                        <button type="button" class="button ico big" @click="agregarProducto">
                                            <i class="icon-line-awesome-plus"></i>
                                        </button>
                                    </div>
                                </div>

                                <table class="contenedor-productos basic-table margin-bottom-20">
                                    <thead>
                                        <tr>
                                            <th>Nombre</th>
                                            <th>Referencia</th>
                                            <th>Cantidad</th>
                                            <th>{{ tipo === 1 ? 'Destino' : 'Origen' }}</th>
                                            <th>&nbsp;</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <tr class="producto-item" v-for="producto in productos" :key="producto.id">
                                            <td>
                                                <input type="hidden" name="id_producto[]" :value="producto.id">
                                                {{ producto.nombre }}
                                            </td>

                                            <td>
                                                {{ producto.referencia }}
                                            </td>

                                            <td>
                                                <strong>{{ producto.cantidad }}<small>{{ tipo === 0 && typeof producto.cantidad_disponible !== 'undefined' ? ('/' + producto.cantidad_disponible) : '' }}</small></strong>
                                            </td>

                                            <td>
                                                <a href="#" @click.prevent="seleccionarBodegas(producto)" v-html="listaBodegas(producto) "></a>
                                            </td>
                                            
                                            <td>
                                                <a href="#" @click.prevent="quitarItem(producto)">
                                                    <i class="icon-line-awesome-trash-o"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                                <div class="row">
                                    <div class="col-xl-5">
                                        <input-check v-if="!item.finalizado"
                                                     v-model="item.finalizar"
                                                     nombre="finalizar"
                                                     :etiqueta="'Finalizar ' + titulo_singular.toLowerCase()"
                                        ></input-check>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="tipo" :value="tipo">
                </div>
            </template>
        </crud>

        <sweet-modal
                ref="modal"
                close=""
        >
            <div v-if="typeof producto_seleccionado === 'object' && producto_seleccionado !== null">
                <h4>{{ producto_seleccionado.nombre }}</h4>
                <div>
                    <small>{{ producto_seleccionado.referencia }}</small>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-8">&nbsp;</div>
                <!--<div class="col-xl-4 text-center">{ { totalDiferenciaProductoCeldas }}</div>-->
            </div>

            <div class="coontenedor-bodegas" :key="modal_bodegas_key">
                <template v-for="celda in celdas">
                    <div class="row celda-item" :key="celda.id">
                        <div class="col-xl-8">
                            <strong>{{ celda.nombre }}</strong>
                            <div>
                                <span><small>{{ nombreBodega(celda.id_bodega) }}</small></span> <i class="icon-line-awesome-angle-right"></i>
                                <span><small>{{ nombreDivision(celda.id_division) }}</small></span>
                            </div>
                        </div>

                        <div class="col-xl-3">
                            <input type="text" class="text-center" placeholder="Cantidad" v-model="celda.cantidad">
                        </div>

                        <div class="col-xl-1 text-left" v-if="tipo === 0">
                            <div class="padding-top-10">
                                <small>/{{ cantidadDisponibleCelda(celda.id) }}</small>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <button class="button button-ok ripple-effect button-sliding-icon big margin-top-30 margin-bottom-20" @click="guardarCantidades">
                OK <i class="icon-feather-check"></i>
            </button>
        </sweet-modal>

        <sweet-modal
                ref="modal_productos"
        >
            <template v-if="mostrar_seleccionar_productos">
                <div class="row celda-item" v-for="producto in productos_encontrados" :key="producto.id">
                    <div class="col-xl-9 text-left">
                        <div>{{ producto.nombre }}</div>
                        <small>{{ producto.referencia || producto.referencia_secundaria }}</small>
                    </div>

                    <div class="col-xl-3">
                        <div class="checkbox">
                            <input type="checkbox" :id="'chk_p_encontrado' + producto.id" @change="seleccionarProducto(producto.id, $event)">
                            <label :for="'chk_p_encontrado' + producto.id"><span class="checkbox-icon"></span></label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-12 text-center">
                        Total productos seleccionados: {{ totalProductosSeleccionados }}
                    </div>
                </div>

                <button class="button button-ok ripple-effect button-sliding-icon big margin-top-30 margin-bottom-20" @click="agregarProductos">
                    OK <i class="icon-feather-check"></i>
                </button>
            </template>
        </sweet-modal>
    </main>
</template>

<script>
    import { SweetModal } from 'sweet-modal-vue'

    export default {
        name: "Documento",

        components: {
            SweetModal,
        },

        props: {
            urls: Object,
            avatar_defecto: String,
            tipo: {
                default: 1,
            },
        },

        data: () => ({
            fuente: 'Documento',
            subdirectorio: 'Almacen',
            titulo_singular: '',
            titulo_plural: '',
            icono: '',
            items: [],
            item: {
                id: 0,
                id_empresa: 0,
                referencia: '',
                numero: 0,
                comentario: '',
                fecha: '',
                fecha_aprobacion: '',
                id_usuario_aprobacion: 0,
                id_usuario: 0,
                finalizar: false,
                finalizado: false,
            },
            propiedades_buscadas: [
                'referencia',
            ],
            bodegas: [{
                id: 0,
                nombre: '-',
            }],
            divisiones: [{
                id: 0,
                id_bodega: 0,
                nombre: '-',
            }],
            celdas: [{
                id: 0,
                id_bodega: 0,
                id_division: 0,
                nombre: '-',
                cantidad: 0,
            }],
            productos: [
                {
                    id: 99,
                    nombre: 'Producto de prueba',
                    referencia: '',
                    cantidad: 2,
                    celdas: [],
                }
            ],
            buscar_codigo: '',
            producto_seleccionado: null,
            productos_encontrados: [],
            productos_seleccionados: [],
            cantidades_celdas: [],
            mostrar_seleccionar_bodegas: false,
            mostrar_seleccionar_productos: false,
            modal_bodegas_key: 0,
        }),

        computed: {
            /*listaDivisiones() {
                let items = [];

                for (const division of this.divisiones) {
                    if (division.id_bodega == this.item.id_bodega) {
                        items.push(division);
                    }
                }

                return items;
            },*/

            /*totalDiferenciaProductoCeldas() {
                console.log('enter computing');

                if (typeof this.producto_seleccionado !== 'object' || this.producto_seleccionado === null || typeof this.producto_seleccionado.celdas !== 'object') {
                    return 0;
                }

                console.log('computing', this.producto_seleccionado.celdas.length);

                let total_ingresado = 0;

                for (const celda of this.producto_seleccionado.celdas) {
                    total_ingresado += celda.cantidad || 0;
                }

                return (this.producto_seleccionado.cantidad || 0) - total_ingresado;
            },*/

            totalProductosSeleccionados() {
                return this.productos_seleccionados.length;
            },
        },

        methods: {
            guardarItem($frm) {
                this.guardar($frm);
            },

            limpiarItem() {
                this.item.id = 0;
                this.item.fecha = moment().format('DD/MM/YYYY');
                this.item.comentario = '';
                this.item.finalizar = false;
                this.item.finalizado = false;

                this.productos = [];
            },

            setItemData(data) {
                this.item.id = data.id;
                this.item.fecha = formatoFechaApp(data.fecha);
                this.item.comentario = data.comentario;
                this.item.finalizar = false;
                this.item.finalizado = parseInt(data.status) === 2;

                const items = data.items;
                let productos = [];

                for (const item of items) {
                    let encontrado = false;

                    for (const producto of productos) {
                        if (item['id_producto'] === producto.id) {

                            producto.cantidad += parseInt(item.cantidad) || 0;

                            producto.celdas.push({
                                id: item['id_celda'],
                                nombre: this.nombreCelda(item['id_bodega']),
                                division: this.nombreDivision(item['id_division']),
                                bodega: this.nombreBodega(item['id_bodega']),
                                cantidad: item.cantidad,
                            });

                            encontrado = true;
                            break;
                        }
                    }

                    if (!encontrado) {
                        productos.push({
                            id: item['id_producto'],
                            nombre: item['nombre'],
                            referencia: item['referencia'] || item['referencia_alternativa'],
                            cantidad: item.cantidad,
                            celdas: [
                                {
                                    id: item['id_celda'],
                                    nombre: this.nombreCelda(item['id_bodega']),
                                    division: this.nombreDivision(item['id_division']),
                                    bodega: this.nombreBodega(item['id_bodega']),
                                    cantidad: item['cantidad'],
                                }
                            ],
                        });
                    }
                }

                this.productos = productos;
            },

            parametrosAdicionales() {
                return {
                    tipo: this.tipo,
                };
            },

            cargarDataAdicional() {
                this.$http.get(this.urls.get, {
                    params: {
                        _fuente: 'Bodega',
                        _subdirectorio: 'Almacen',
                        _accion: 'listaCeldas',
                    }
                })
                    .then(response => {
                        if (response.status === 200) {
                            const data = response.data;

                            if (data.ok && typeof data === 'object') {
                                this.bodegas = data['bodegas'];
                                this.divisiones = data['divisiones'];
                                this.celdas = data['celdas'];
                            }
                        }
                    });
            },

            fechaDocumento(fecha) {
                return moment(fecha, 'YYYY-MM-DD HH:mm:ss').format('LL');
            },

            buscarProductos(val, fn_listo) {
                this.$http.get(this.urls.get, {
                    params: {
                        _fuente: 'Producto',
                        _subdirectorio: 'Almacen',
                        _accion: 'buscarProductos',
                        valor: val,
                    }
                })
                    .then(response => {
                        if (response.status === 200) {
                            const data = response.data;

                            if (data.ok && typeof fn_listo === 'function') {
                                fn_listo(data);
                            }
                        }
                    });
            },

            agregarProducto() {
                if (!this.buscar_codigo.length) return;

                const self = this;

                this.buscarProductos(this.buscar_codigo, function(data) {
                    const productos_encontrados = data['productos'];

                    if (productos_encontrados.length === 1) {
                        const item = productos_encontrados.pop();
                        self.insertarProducto(item);
                    }
                    else {
                        self.productos_encontrados = productos_encontrados;
                        self.productos_seleccionados = [];
                        self.mostrar_seleccionar_productos = true;
                        self.$refs['modal_productos'].open();
                    }
                });

                this.buscar_codigo = '';
            },

            insertarProducto(item) {
                let existente = false;

                for (const indice in this.productos) {
                    if (this.productos.hasOwnProperty(indice)) {
                        if (this.productos[indice].id == item.id) {
                            this.productos[indice].cantidad++;

                            //se le suma a la bodega que este seleccionada
                            for (const icelda in this.productos[indice].celdas) {
                                if (this.productos[indice].celdas.hasOwnProperty(icelda)) {
                                    if (this.productos[indice].celdas[icelda].cantidad) {
                                        this.productos[indice].celdas[icelda].cantidad++;
                                        break;
                                    }
                                }
                            }

                            existente = true;
                        }
                    }
                }

                if (!existente) {
                    const nuevo = {
                        id: item.id,
                        nombre: item.nombre,
                        referencia: item.referencia || item.referencia_alternativa,
                        cantidad: 1,
                        cantidad_disponible: item.cantidad,
                        celdas: [],
                    };

                    this.productos.push(nuevo);
                }
            },

            agregarProductos() {
                if (this.productos_seleccionados.length) {
                    this.$http.get(this.urls.get, {
                        params: {
                            _fuente: 'Producto',
                            _subdirectorio: 'Almacen',
                            _accion: 'buscarProductosPorId',
                            ids: this.productos_seleccionados,
                        }
                    })
                        .then(response => {
                            if (response.status === 200) {
                                const data = response.data;

                                if (data.ok) {
                                    const items = data.items;

                                    for (const item of items) {
                                        this.insertarProducto(item);
                                    }
                                }
                            }
                        });
                }

                this.mostrar_seleccionar_productos = false;
                this.$refs['modal_productos'].close();
            },

            listaBodegas(producto) {
                const lbl_seleccionar = '(seleccionar)';

                if (!producto.celdas.length) {
                    return lbl_seleccionar;
                }

                let items = [];

                for (const celda of producto.celdas) {
                    if (celda.cantidad) {
                        const input = `<input type="hidden" name="producto_celda[${producto.id}][${celda.id}]" value="${celda.cantidad}">`;
                        items.push(celda.bodega + input + '<i class="icon-line-awesome-angle-right"></i>' + celda.division + '<i class="icon-line-awesome-angle-right"></i>' + celda.nombre + ': <mark class="color">' + celda.cantidad + '</mark>');
                    }
                }

                return items.length ? items.join('<br>') : lbl_seleccionar;
            },

            quitarItem(producto) {
                const self = this;
                
                confirmar('¿Está seguro que quiere quitar el producto?', function () {
                    for (const indice in self.productos) {
                        if (self.productos.hasOwnProperty(indice)) {
                            if (self.productos[indice].id === producto.id) {
                                self.productos.splice(indice, 1);
                                return true;
                            }
                        }
                    }
                    return false;
                });
            },

            cargarCantidadesDisponibles(id_producto) {
                this.$http.get(this.urls.get, {
                    params: {
                        _fuente: 'Producto',
                        _subdirectorio: 'Almacen',
                        _accion: 'cantidadesDisponibles',
                        id_producto: id_producto,
                    }
                })
                    .then(response => {
                        if (response.status === 200) {
                            const data = response.data;
                            this.cantidades_celdas = data.cantidades;
                        }
                    });
            },

            seleccionarBodegas(producto) {
                this.cargarCantidadesDisponibles(producto.id);

                for (const icelda in this.celdas) {
                    if (this.celdas.hasOwnProperty(icelda)) {
                        let cantidad_seleccionada = 0;

                        for (const pcelda of producto.celdas) {
                            if (pcelda.id == this.celdas[icelda].id) {
                                cantidad_seleccionada = parseInt(pcelda.cantidad) || 0;
                            }
                        }

                        this.celdas[icelda].cantidad = cantidad_seleccionada;
                    }
                }

                this.producto_seleccionado = producto;
                this.productos_seleccionados = [];
                this.mostrar_seleccionar_bodegas = true;
                this.$refs['modal'].open();

                setTimeout(() => {
                    this.modal_bodegas_key++;
                    //this.$forceUpdate();
                }, 100);
            },

            guardarCantidades() {
                let items = [];
                let cantidad_total = 0;

                for (const celda of this.celdas) {
                    const cantidad = parseInt(celda.cantidad) || 0;

                    if (cantidad) {
                        if (this.tipo === 0 && this.cantidadDisponibleCelda(celda.id) < cantidad) {
                            continue;
                        }

                        items.push({
                            id: celda.id,
                            nombre: celda.nombre,
                            division: this.nombreDivision(celda.id_division),
                            bodega: this.nombreBodega(celda.id_bodega),
                            cantidad: cantidad,
                        });

                        cantidad_total += cantidad;
                    }
                }

                this.producto_seleccionado.celdas = items;
                this.producto_seleccionado.cantidad = cantidad_total;

                this.mostrar_seleccionar_bodegas = false;
                this.$refs['modal'].close();
            },

            nombreBodega(id_bodega) {
                for (const bodega of this.bodegas) {
                    if (bodega.id == id_bodega) {
                        return bodega.nombre;
                    }
                }
                return '';
            },

            nombreDivision(id_division) {
                for (const division of this.divisiones) {
                    if (division.id == id_division) {
                        return division.nombre;
                    }
                }
                return '';
            },

            nombreCelda(id_celda) {
                for (const celda of this.celdas) {
                    if (celda.id == id_celda) {
                        return celda.nombre;
                    }
                }
                return '';
            },

            seleccionarProducto(id_producto, e) {
                const $e = $(e.target);

                if ($e.is(':checked')) {
                    if (!this.productos_seleccionados.includes(id_producto)) {
                        this.productos_seleccionados.push(id_producto);
                    }
                }
                else {
                    for (const indice in this.productos_seleccionados) {
                        if (this.productos_seleccionados.hasOwnProperty(indice)) {
                            if (this.productos_seleccionados[indice] === id_producto) {
                                this.productos_seleccionados.splice(indice, 1);
                                return;
                            }
                        }
                    }
                }
            },

            iconoStatus(item) {
                return item.status == 2 ? '<i class="icon-line-awesome-check"></i>' : '';
            },

            cantidadDisponibleCelda(id_celda) {
                if (typeof this.cantidades_celdas !== 'object') return;
                for (const cantidad_celda of this.cantidades_celdas) {
                    if (cantidad_celda.id_celda == id_celda) {
                        return parseInt(cantidad_celda.cantidad) || 0;
                    }
                }
                return 0;
            },
        },

        created() {
            this.titulo_singular = this.tipo === 1 ? 'Entrada' : 'Salida';
            this.titulo_plural = this.tipo === 1 ? 'Entradas' : 'Salidas';
            this.icono = this.tipo === 1 ? 'icon-line-awesome-sign-in' : 'icon-line-awesome-sign-out';
        },

        mounted() {
            this.cargarData();
        },
    }
</script>

<style scoped>
    .celda-item {
        margin: 10px;
        padding-top: 15px;
    }

    .celda-item:hover {
        background-color: #fafafa;
    }
</style>