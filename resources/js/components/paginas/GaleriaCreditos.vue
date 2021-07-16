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
            _tipo_vista="expandido"
            :urls="urls"
            @itemDataSet="itemDataSet"
            @postItemEliminado="postItemEliminado"
            @postCargarData="postCargarData"
            @formularioMostrado="inicializarFormulario"
            @formularioEditarMostrado="mostrarFormulario"
            :key="instance"
    >
        <template slot="contenido_item" slot-scope="row">
            <h4>
                {{ row.item.nombre }}
            </h4>

            <!-- Details -->
            <span class="freelancer-detail-item">
                <span><strong>Fecha:</strong> {{ formatoFecha(row.item.fecha) }}</span>
            </span>

            <span class="freelancer-detail-item">
                <span><strong>Monto:</strong> {{ detalleMonto(row.item) }}</span>
            </span>

            <span class="freelancer-detail-item" v-if="row.item.nombre_cliente">
                <span><strong>Cliente:</strong> {{ nombreCliente(row.item) }}</span>
            </span>

            <span class="freelancer-detail-item" v-if="row.item.negocio_cliente">
                <span><strong>Negocio:</strong> {{ nombreNegocio(row.item) }}</span>
            </span>

            <div class="row" v-if="typeof row.item.observaciones === 'string' && row.item.observaciones.length">
                <div class="col-xl-12">
                    <span><strong>Observaciones:</strong> <i>{{ observacionesDocumento(row.item.observaciones) }}</i></span>
                </div>
            </div>

            <div class="row" v-if="typeof row.item.nombre_usuario === 'string' && row.item.nombre_usuario.length">
                <div class="col-xl-12">
                    <span><strong>Creado por:</strong> <span>{{ row.item.nombre_usuario }}</span></span>
                </div>
            </div>
        </template>

        <template slot="botones" slot-scope="row">
            <span class="button-group detail">
                <a class="button ripple-effect ico" :class="row.item.status == status.pendiente ? 'activo dark' : 'gray'" title="Pendiente" @click="cambiarStatus(row.item.id, status.pendiente)">
                    <i class="icon-line-awesome-clock-o"></i>
                </a>
                <a class="button ripple-effect ico" :class="row.item.status == status.rechazado ? 'activo dark' : 'gray'" title="Rechazado" @click="cambiarStatus(row.item.id, status.rechazado)">
                    <i class="icon-line-awesome-times-circle-o"></i>
                </a>
                <a class="button ripple-effect ico" :class="row.item.status == status.aprobado ? 'activo dark' : 'gray'" title="Aprobado" @click="cambiarStatus(row.item.id, status.aprobado)">
                    <i class="icon-line-awesome-check-circle-o"></i>
                </a>
            </span>

            <a class="button gray ripple-effect ico" title="Imprimir documento" @click="verImprimirItem(row.item)">
                <i class="icon-feather-printer"></i>
            </a>
        </template>

        <template slot="formulario">
            <div class="row">
                <div class="col-xl-12">
                    <div class="dashboard-box margin-top-0">
                        <div class="content with-padding padding-bottom-10">

                            <div class="row">
                                <div class="col-xl-5">
                                    <input-texto
                                            v-model="item.nombre"
                                            nombre="nombre"
                                            etiqueta="Descripción"
                                    ></input-texto>
                                </div>

                                <div class="col-xl-4">
                                    <input-fecha
                                            v-model="item.fecha"
                                            nombre="fecha"
                                            etiqueta="Fecha"
                                    ></input-fecha>
                                </div>

                                <div class="col-xl-3">
                                    <input-moneda
                                            v-model="item.monto"
                                            nombre="monto"
                                            etiqueta="Monto"
                                    ></input-moneda>
                                </div>
                            </div>

                            <div class="message-time-sign">
                                <span>Cliente</span>
                            </div>

                            <div class="row">
                                <input type="hidden" name="id_cliente" :value="item.cliente.id">

                                <div class="col-xl-12">
                                    <input-radios
                                            v-model="item.cliente.tipo"
                                            nombre="tipo_cliente"
                                            :items="tipos_clientes"
                                    ></input-radios>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xl-7">
                                    <input-texto
                                            v-model="item.cliente.nombre"
                                            nombre="nombre_cliente"
                                            etiqueta="Nombre"
                                    ></input-texto>
                                </div>

                                <div class="col-xl-5">
                                    <input-texto
                                            v-model="item.cliente.dni"
                                            nombre="dni_cliente"
                                            etiqueta="DNI"
                                    ></input-texto>
                                </div>
                            </div>

                            <div class="row" v-show="item.cliente.tipo == 2">
                                <div class="col-xl-7">
                                    <input-texto
                                            v-model="item.cliente.negocio"
                                            nombre="negocio_cliente"
                                            etiqueta="Negocio"
                                    ></input-texto>
                                </div>

                                <div class="col-xl-5">
                                    <input-texto
                                            v-model="item.cliente.ruc"
                                            nombre="ruc_cliente"
                                            etiqueta="RUC"
                                    ></input-texto>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xl-6">
                                    <input-texto
                                            v-model="item.cliente.direccion"
                                            nombre="direccion_cliente"
                                            etiqueta="Dirección"
                                    ></input-texto>
                                </div>

                                <div class="col-xl-6">
                                    <input-textos
                                            v-model="item.cliente.telefono"
                                            nombre="telefono_cliente"
                                            etiqueta="Teléfonos"
                                    ></input-textos>
                                </div>
                            </div>

                            <div class="message-time-sign">
                                <span>Fotos</span>
                            </div>

                            <div class="contenedor-galeria-items freelancers-container freelancers-grid-layout margin-top-35" :class="uploading ? 'status-uploading' : ''" ref="contenedor_fotos">
                                <transition v-for="(galeria_item,indice) in item.items" :key="indice"
                                    appear
                                    enter-active-class="animated zoomIn"
                                >
                                    <galeria-item
                                        nombre="galeria_item"
                                        :item="galeria_item"
                                        @nombreActualizado="actualizarNombre"
                                        @tipoActualizado="actualizarTipo"
                                        @eliminarItem="eliminarItem"
                                        @visibilidadCambiada="cambiarVisibilidad"
                                    ></galeria-item>
                                </transition>

                                <input-imagen
                                    v-model="nueva_foto"
                                    nombre="foto"
                                    etiqueta=""
                                    icono_defecto="icon-feather-plus"
                                    altura="340"
                                    @modificado="crearNuevoItem"
                                    v-if="mostrar_agregar_nuevo"
                                >
                                </input-imagen>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </template>

        <template slot="form_footer">
            <a href="#" class="button gray ripple-effect button-sliding-icon big margin-top-30 margin-left-5" @click="guardarImprimir">
                Guardar e imprimir
                <i class="icon-feather-printer"></i>
            </a>
        </template>

        <template slot="secciones">
            <section class="impresion" v-if="vista_impresion">
                <div class="dashboard-headline">
                    <h3>
                        <button type="button" class="button" @click="mostrarIndex">
                            <i class="icon-material-outline-arrow-back"></i>
                        </button>
                        &nbsp;
                        Impresión
                    </h3>
                </div>

                <div class="row">
                    <div class="col-xl-12">
                        <div class="dashboard-box margin-top-0">
                            <div class="content with-padding">

                                <div class="notify-box margin-top-15">
                                    <button type="button" class="margin-right-10 boton-orientacion" @click="cambiarOrientacion">
                                        <i :class="orientacion === 'horizontal' ? 'icon-line-awesome-sticky-note' : 'icon-line-awesome-file-o'"></i>
                                    </button>

                                    <span v-show="orientacion === 'vertical'">
                                        <button type="button" class="margin-right-10" @click="cambiarLayout(9)">
                                            <!--<i class="icon-line-awesome-table"></i>-->
                                            <img class="icono-barra" :class="layout == 9 ? 'seleccionado' : ''" :src="urlIcono('layout-9-c.png')" alt="9 por pág.">
                                        </button>

                                        <button type="button" class="margin-right-10" @click="cambiarLayout(4)">
                                            <!--<i class="icon-feather-grid"></i>-->
                                            <img class="icono-barra" :class="layout == 4 ? 'seleccionado' : ''" :src="urlIcono('layout-4-c.png')" alt="4 por pág.">
                                        </button>

                                        <button type="button" class="margin-right-10" @click="cambiarLayout(1)">
                                            <!--<i class="icon-feather-square"></i>-->
                                            <img class="icono-barra" :class="layout == 1 ? 'seleccionado' : ''" :src="urlIcono('layout-1-c.png')" alt="1 por pág.">
                                        </button>
                                    </span>

                                    <span v-show="orientacion === 'horizontal'">
                                        <button type="button" class="margin-right-10" @click="cambiarLayout(6)">
                                            <!--<i class="icon-line-awesome-table"></i>-->
                                            <img class="icono-barra" :class="layout == 6 ? 'seleccionado' : ''" :src="urlIcono('layout-6-c.png')" alt="6 por pág.">
                                        </button>

                                        <button type="button" class="margin-right-10" @click="cambiarLayout(2)">
                                            <!--<i class="icon-feather-grid"></i>-->
                                            <img class="icono-barra" :class="layout == 2 ? 'seleccionado' : ''" :src="urlIcono('layout-2-c.png')" alt="2 por pág.">
                                        </button>

                                        <button type="button" class="margin-right-10" @click="cambiarLayout(1)">
                                            <!--<i class="icon-feather-square"></i>-->
                                            <img class="icono-barra" :class="layout == 1 ? 'seleccionado' : ''" :src="urlIcono('layout-1h-c.png')" alt="1 por pág.">
                                        </button>
                                    </span>

                                    <!--<div class="switch-container">
                                        <label class="switch">
                                            <input type="checkbox">
                                            <span class="switch-button"></span>
                                            <span class="switch-text">Incluir ocultos</span>
                                        </label>
                                    </div>-->

                                    <div class="sort-by">
                                        <span>Ordenar por:</span>
                                        <div class="btn-group bootstrap-select hide-tick">
                                            <button type="button" class="btn dropdown-toggle btn-default" data-toggle="dropdown" role="button" title="Relevance">
                                                <span class="filter-option pull-left">{{ descripcionOrdenarItem() }}</span>&nbsp;<span class="bs-caret"><span class="caret"></span></span>
                                            </button>
                                            <div class="dropdown-menu open" role="combobox">
                                                <ul class="dropdown-menu inner" role="listbox" aria-expanded="false">
                                                    <li data-original-index="0" class="selected" v-for="ordenar_item in ordenar_items" :key="ordenar_item.id">
                                                        <a tabindex="0" class="" data-tokens="null" role="option" aria-disabled="false" aria-selected="true" @click="cambiarOrden(ordenar_item.id)">
                                                            <span class="text">{{ ordenar_item.nombre }}</span>
                                                            <span class="glyphicon glyphicon-ok check-mark"></span>
                                                        </a>
                                                    </li>
                                                    <!--<li data-original-index="1">
                                                        <a tabindex="0" class="" data-tokens="null" role="option" aria-disabled="false" aria-selected="false">
                                                            <span class="text">Categorías</span>
                                                            <span class="glyphicon glyphicon-ok check-mark"></span>
                                                        </a>
                                                    </li>
                                                    <li data-original-index="2">
                                                        <a tabindex="0" class="" data-tokens="null" role="option" aria-disabled="false" aria-selected="false">
                                                            <span class="text">Nombre</span>
                                                            <span class="glyphicon glyphicon-ok check-mark"></span>
                                                        </a>
                                                    </li>-->
                                                </ul>
                                            </div>
                                            <!--<select class="selectpicker hide-tick" tabindex="-98">
                                                <option>Orden inicial</option>
                                                <option>Categorías</option>
                                                <option>Nombre</option>
                                            </select>-->
                                        </div>

                                        <button type="button" class="button ripple-effect margin-left-30" @click="submitGenerarPdf">Generar PDF</button>
                                    </div>
                                </div>

                                <div v-html="html_impresion"></div>

                            </div>
                        </div>
                    </div>
                </div>

                <form :action="urlSubmitImpresion()" target="_blank" ref="form_generar_pdf">
                    <div class="col-xl-12">
                        <input type="hidden" name="id" :value="id_item">
                        <input type="hidden" name="_fuente" :value="fuente">
                        <input type="hidden" name="_accion" value="cargarImpresion">
                        <input type="hidden" name="layout" :value="layout">
                        <input type="hidden" name="orientacion" :value="orientacion">
                        <input type="hidden" name="ordenar_por" :value="id_ordenar_item">

                        <button type="submit" class="button ripple-effect button-sliding-icon big margin-top-30"><!-- @click="generarPdf"-->
                            Generar PDF
                            <i class="icon-line-awesome-file-pdf-o"></i>
                        </button>
                    </div>
                </form>

            </section>
        </template>
    </crud>
</template>

<script>
    window.Vue = require('vue');
    Vue.component('galeria-item', require('../GaleriaItem.vue').default);

    export default {
        name: "GaleriaCreditos.vue",

        props: {
            urls: Object,
            avatar_defecto: String,
        },

        data: () => ({
            fuente: 'GaleriaCredito',
            titulo_singular: 'Documento de crédito',
            titulo_plural: 'Documentos de créditos',
            icono: 'icon-line-awesome-credit-card',
            items: [],
            item: {
                id: 0,
                nombre: '',
                fecha: '',
                monto: '',
                id_moneda: null,
                moneda_simbolo: '',
                moneda_iso: '',
                cliente: {
                    id: 0,
                    nombre: '',
                    dni: '',
                    negocio: '',
                    ruc: '',
                    direccion: '',
                    telefono: '',
                    tipo: 1
                },
                items: [],
                status: 1,
            },
            vista_items: true,
            vista_formulario: false,
            vista_impresion: false,
            texto_buscado: '',
            propiedades_buscadas: [
                'nombre',
                'nombre_cliente',
                'dni_cliente',
                'negocio_cliente',
                'ruc_cliente',
            ],
            nueva_foto: '',
            tipos_clientes: [
                {
                    id: 1,
                    nombre: 'Personal'
                },
                {
                    id: 2,
                    nombre: 'Jurídico'
                }
            ],
            ordenar_items: [
                {
                    id: 'orden_inicial',
                    nombre: 'Orden inicial',
                },
                {
                    id: 'categorias',
                    nombre: 'Categorías',
                },
                {
                    id: 'nombre',
                    nombre: 'Título',
                },
            ],
            id_ordenar_item: 'orden_inicial',
            html_impresion: '',
            id_item: 0,
            layout: 9,
            orientacion: 'vertical',
            uploading: false,
            instance: 0,

            status: {
                pendiente: 1,
                aprobado: 2,
                rechazado: 3,
            },

            mostrar_agregar_nuevo: true,
        }),

        methods: {
            limpiarItem() {
                this.item.id = 0;
                this.item.nombre = '';
                this.item.fecha = '';
                this.item.monto = '';
                this.item.cliente.id = 0;
                this.item.cliente.nombre = '';
                this.item.cliente.dni = '';
                this.item.cliente.negocio = '';
                this.item.cliente.ruc = '';
                this.item.cliente.direccion = '';
                this.item.cliente.telefono = '';
                this.item.cliente.tipo = 1;
                this.item.items = [];
            },

            setItemData(data) {
                this.item.id = data.id;
                this.item.nombre = data.nombre;
                this.item.fecha = formatoFechaApp(data.fecha, 'fecha');
                this.item.monto = data.monto;
                this.item.id_moneda = data.id_moneda;
                this.item.moneda_simbolo = data.moneda_simbolo;
                this.item.codigo_iso = data.moneda_iso;
                const cliente = data.cliente;
                this.item.cliente.id = cliente.id;
                this.item.cliente.tipo = cliente.tipo;
                this.item.cliente.nombre = cliente.nombre;
                this.item.cliente.dni = cliente.dni;
                this.item.cliente.direccion = cliente.direccion;
                this.item.cliente.telefono = cliente.telefono;
                this.item.cliente.negocio = cliente.negocio;
                this.item.cliente.ruc = cliente.ruc;
                this.item.status = data.status;

                const items = data.fotos;
                let fotos = [];
                let n = 0;

                for (const item of items) {
                    fotos.push({
                        id: item.id,
                        nombre: item.nombre,
                        tipo: item.tipo,
                        foto: item.foto,
                        indice: n,
                        kbs: item.kb,
                        ancho: item.ancho,
                        alto: item.alto,
                        visible: !!item.visible,
                        camara: item.camara,
                        latitud: item.latitud,
                        longitud: item.longitud,
                        _key: item.id,
                    });

                    n++;
                }

                this.item.items = fotos;
            },

            cargarDataAdicional() {

            },

            inicializarFormulario() {
                const self = this;

                self.limpiarItem();

                setTimeout(function() {
                    const $contenedor = $(self.$refs['contenedor_fotos']);

                    $('.contenedor-galeria-items').sortable({
                        items: '.galeria-item',
                        tolerance: 'pointer',
                        update: function() {
                            const $items = $contenedor.find('.galeria-item');

                            $items.each(function() {
                                const $item = $(this);

                                for (const indice in self.item.items) {
                                    if (self.item.items.hasOwnProperty(indice)) {
                                        if (self.item.items[indice]._key == $item.data('key')) {
                                            self.item.items[indice].indice = $item.index();
                                            break;
                                        }
                                    }
                                }
                            });
                        },
                    });
                }, 2000);
            },

            guardarImprimir(e) {
                const self = this;
                const frm = $(e.target).closest('main').find('form').get(0);

                this.statusGuardando(true);

                this.enviarForm(frm, function(data) {
                    resultadoSolicitudDefecto(data);

                    if (data.ok) {
                        self.verImprimirItem({
                            id: data.id
                        });

                        /*setTimeout(() => {
                            self.cargarData();
                        }, 2000);*/
                    }

                    self.statusGuardando(false);
                    return false;
                });
            },

            crearNuevoItem(el) {
                this.uploading = true;

                const $input_ori = $(el);
                //const $input_cln = $(el).clone(true);

                //$input_cln.insertAfter($input_ori);

                let form = document.createElement('form');
                let in_fuente = document.createElement('input');
                let in_accion = document.createElement('input');
                let in_modificado = document.createElement('input');

                in_fuente.setAttribute('name', '_fuente');
                in_fuente.setAttribute('value', 'GaleriaCredito');

                in_accion.setAttribute('name', '_accion');
                in_accion.setAttribute('value', 'subirImagen');

                in_modificado.setAttribute('name', 'foto_upload_modificado');
                in_modificado.setAttribute('value', '1');

                form.append(in_fuente);
                form.append(in_accion);
                form.append(in_modificado);
                //form.append($(el).clone().get(0));
                form.append($input_ori.get(0));

                const form_data = new FormData(form);

                this.debugForm(form_data);

                const config = { headers: { 'Content-Type': 'multipart/form-data' } };

                const self = this;

                this.$http.post(this.urls.post, form_data, config)
                    .then(response => {
                        if (response.status === 200) {
                            resultadoSolicitudDefecto(response.data);

                            if (response.data.ok) {
                                const nombre_foto = response.data.nombre;
                                const atributos = response.data.atributos;

                                self.agregarItem(nombre_foto, atributos);

                                const $input = $(el);
                                $input.data('dropify').resetPreview();
                                $input.data('dropify').clearElement();

                                self.uploading = false;

                                //reconstruye el componente para que se pueda subir mas imagenes
                                self.mostrar_agregar_nuevo = false;
                                setTimeout(() => {
                                    self.mostrar_agregar_nuevo = true;
                                }, 700);
                            }
                        }
                        else if (response.status === 500) {
                            mensajeError('Error de servidor.');
                        }
                    });
            },

            agregarItem(nombre_foto, atributos) {
                if (typeof atributos === 'undefined' || atributos === null) {
                    atributos = [];
                }

                this.item.items.push({
                    id: 0,
                    nombre: '',
                    tipo: 1,
                    foto: nombre_foto,
                    indice: this.item.items.length,
                    kbs: typeof atributos['kbs'] !== 'undefined' ? atributos['kbs'] : 0,
                    ancho: typeof atributos['ancho'] !== 'undefined' ? atributos['ancho'] : 0,
                    alto: typeof atributos['alto'] !== 'undefined' ? atributos['alto'] : 0,
                    camara: ((atributos['marca'] || '') + ' ' + (atributos['modelo'] || '')).trim(),
                    latitud: typeof atributos['latitud'] === 'string' ? atributos['latitud'] : null,
                    longitud: typeof atributos['longitud'] === 'string' ? atributos['longitud'] : null,
                    visible: true,
                    _key: uniqueNumber(),
                });
            },

            actualizarNombre(nombre, item_key) {
                const indice = this.indiceParaKey(item_key);
                if (indice === null) return;
                this.item.items[indice].nombre = nombre;
            },

            actualizarTipo(id_tipo, item_key) {
                const indice = this.indiceParaKey(item_key);
                if (indice === null) return;
                this.item.items[indice].tipo = id_tipo;
            },

            eliminarItem(item_key) {
                const indice = this.indiceParaKey(item_key);
                if (indice === null) return;
                this.item.items.splice(indice, 1);
            },

            cambiarVisibilidad(item_key) {
                const indice = this.indiceParaKey(item_key);
                if (indice === null) return;
                this.item.items[indice].visible = !this.item.items[indice].visible;
            },

            indiceParaKey(item_key) {
                for (const indice in this.item.items) {
                    if (this.item.items.hasOwnProperty(indice)) {
                        if (this.item.items[indice]._key == item_key) {
                            return indice;
                        }
                    }
                }
                return null;
            },

            nombreCliente(item) {
                return item.nombre_cliente + (typeof item.nombre_dni === 'string' && item.nombre_dni.length ? (' (' + item.nombre_dni + ')') : '');
            },

            nombreNegocio(item) {
                return item.negocio_cliente + (typeof item.ruc_cliente === 'string' && item.ruc_cliente.length ? (' (' + item.ruc_cliente + ')') : '');
            },

            detalleMonto(item) {
                return (item.moneda_simbolo || '') + (parseFloat(item.monto) || 0).toFixed(2);
            },

            observacionesDocumento(str) {
                if (str.length > 200) {
                    return str.substr(0, 195) + '...';
                }
                return str;
            },

            verImprimirItem(item) {
                const self = this;

                this.$http.get(this.urls.get, {
                    params: {
                        _fuente: this.fuente,
                        _accion: 'cargarImpresion',
                        layout: this.layout,
                        orientacion: this.orientacion,
                        ordenar_por: this.id_ordenar_item,
                        id: item.id
                    }
                })
                .then(response => {
                    if (response.status === 200) {
                        const data = response.data;

                        self.html_impresion = data.html;
                        self.mostrarImpresion();
                        self.id_item = data.id;
                    }
                });
            },

            cambiarStatus(id_galeria_credito, status) {
                console.log('attempting to update to', status);
                const self = this;

                this.$http.post(this.$url_post, {
                    _fuente: 'GaleriaCredito',
                    _accion: 'cambiarStatus',
                    id_galeria_credito: id_galeria_credito,
                    status: status,
                })
                    .then(response => {
                        if (response.status === 200) {
                            resultadoSolicitudDefecto(response.data);

                            if (response.data.ok) {
                                for (const prop in self.items) {
                                    if (self.items.hasOwnProperty(prop)) {
                                        if (self.items[prop].id == id_galeria_credito) {
                                            self.items[prop].status = status;
                                            //self.instance++;
                                            self.$forceUpdate();
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                        else if (response.status === 500) {
                            mensajeError('Error de servidor.');
                        }
                    });
            },

            mostrarIndex() {
                this.vista_formulario = false;
                this.vista_impresion = false;
                this.vista_items = true;

                this.$forceUpdate();

                /*const $doc = $(document);
                $doc.find('section.form').hide();
                $doc.find('section.form').hide();*/
            },

            mostrarImpresion() {
                this.vista_formulario = false;
                this.vista_items = false;
                this.vista_impresion = true;

                this.instance++;
            },

            urlSubmitImpresion() {
                return this.$url_get;
            },

            submitGenerarPdf() {
                const form = this.$refs['form_generar_pdf'];
                form.submit();
            },

            urlIcono(img) {
                return this.$public_dir + 'img/' + img;
            },

            cambiarLayout(layout) {
                this.layout = layout;
                this.verImprimirItem({id:this.id_item});
            },

            cambiarOrientacion() {
                this.orientacion = this.orientacion === 'vertical' ? 'horizontal' : 'vertical';
                this.layout = this.orientacion === 'horizontal' ? 6 : 9;
                this.verImprimirItem({id:this.id_item});
            },

            cambiarOrden(id_ordenar_item) {
                this.id_ordenar_item = id_ordenar_item;
                this.verImprimirItem({id:this.id_item});
            },

            descripcionOrdenarItem() {
                for (const ordenar_item of this.ordenar_items) {
                    if (this.id_ordenar_item == ordenar_item.id) {
                        return ordenar_item.nombre;
                    }
                }
                return '';
            },

            /*postItemEliminado(indice) {
                this.items.splice(indice, 1);
                this.actualizarIndices();
                this.instance++;
            },*/
        },

        mounted() {
            this.cargarData();
        },
    }
</script>

<style scoped>
    .input-imagen {
        width: calc(100% * (1/3) - 30px);
    }

    .icono-barra {
        width: 24px;
        height: 24px;
        filter: grayscale(1) invert(1);
    }
    .icono-barra.seleccionado {
        filter: unset !important;
    }

    .boton-orientacion {
        border-right: 1px solid #999;
        padding-right: 5px;
    }
    .boton-orientacion i {
        font-size: 24px;
    }

    .button-group a {
        margin-right: -4px !important;
        border-radius: 0;
    }
    .button-group a:first-child {
        border-bottom-left-radius: 4px;
        border-top-left-radius: 4px;
    }
    .button-group a:last-child {
        border-bottom-right-radius: 4px;
        border-top-right-radius: 4px;
        margin-right: 3px !important;
    }
    .button-group a.activo {
        color: #fff;
    }
</style>