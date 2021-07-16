
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

import VueCarousel from 'vue-carousel';
import Vuelidate from 'vuelidate';
import { Drag, Drop } from 'vue-drag-drop';

/*
export default {
  components: { VueperSlides, VueperSlide },
  .*..
}*/

window.Vue = require('vue');

//vue-router
/*window.VueRouter = require('vue-router');*/
import axios from 'axios';
import VueRouter from 'vue-router';
import PagAlmacenBodega from './components/paginas/almacen/Bodega.vue';
import PagAlmacenCategoria from './components/paginas/almacen/Categoria.vue';
import PagAlmacenCelda from './components/paginas/almacen/Celda.vue';
import PagAlmacenDivision from './components/paginas/almacen/Division.vue';
import PagAlmacenDocumento from './components/paginas/almacen/Documento.vue';
import PagAlmacenMarca from './components/paginas/almacen/Marca.vue';
import PagAlmacenModelo from './components/paginas/almacen/Modelo.vue';
import PagAlmacenProducto from './components/paginas/almacen/Producto.vue';
//control de almacenes
import PagAlmacenProveedor from './components/paginas/almacen/Proveedor.vue';
import PagAlmacenSubcategoria from './components/paginas/almacen/Subcategoria.vue';
import PagAlmacenTipoProducto from './components/paginas/almacen/TipoProducto.vue';
//import PagInventario from './components/paginas/Inventario.vue';
import PagAtributos from './components/paginas/Atributo.vue';
import PagCategorias from './components/paginas/Categorias.vue';
import PagCodigoQr from './components/paginas/CodigoQr.vue';
import PagComunicados from './components/paginas/Comunicados.vue';
import PagCredenciales from './components/paginas/Credencial.vue';
//import PagOpciones from './components/paginas/Opciones.vue';
import PagDashboard from './components/paginas/Dashboard.vue';
import PagDepartamentos from './components/paginas/Departamentos.vue';
import PagDocumentoCategorias from './components/paginas/DocumentoCategoria.vue';
import PagDocumentos from './components/paginas/Documentos.vue';
import PagEmpresas from './components/paginas/Empresas.vue';
import PagEntregas from './components/paginas/Entregas.vue';
import PagGaleriaCreditos from './components/paginas/GaleriaCreditos.vue';
import PagHorario from './components/paginas/Horario.vue';
import PagHorarioPlantilla from './components/paginas/HorarioPlantilla.vue';
import PagImportar from './components/paginas/Importar.vue';
import PagProductos from './components/paginas/IntranetProductos';
import PagReportePresolicitud from './components/paginas/ReportePresolicitud';
import PagMarcas from './components/paginas/Marcas.vue';

import PagEzaDigitalMetas from './components/paginas/EzadigitalMeta.vue';
import PagEzaDigitalMetasUsuario from './components/paginas/EzadigitalMetaUsuario.vue';
import PagEzaDigitalUbicacionesUsuario from './components/paginas/EzadigitalUbicacionesUsuario.vue';


//Catalogo
import PagPaises from './components/paginas/Paises.vue';
import PagBarrios from './components/paginas/Barrios.vue';
import PagMunicipios from './components/paginas/Municipios.vue';
import PagDepartamentoGeografico from './components/paginas/DepartamentoGeografico.vue';
import PagDocumentosIntreza from './components/paginas/DocumentosIntreza.vue'

//Cliente
import PagClientes from './components/paginas/Clientes.vue';
import PagClientesPersona from './components/paginas/ClientePersona.vue';
import PagClientesEmpresa from './components/paginas/ClienteEmpresa.vue';

//Credito

import PagCreditos from './components/paginas/Creditos.vue';
import PagSeguimientoCredito from './components/paginas/SeguimientoDeCredito.vue';

//Vue.component('app-stepper', require('./components/Stepper.vue').default);
/*Vue.component('login-button', require('./components/LoginButtonComponent.vue'));
Vue.component('register-button', require('./components/RegisterButtonComponent.vue'));
Vue.component('remember-password', require('./components/RememberPasswordComponent.vue'));
Vue.component('reset-password', require('./components/ResetPasswordComponent.vue'));
Vue.component('snackbar', require('./components/SnackBarComponent.vue'));
Vue.component('gravatar', require('./components/GravatarComponent.vue'));*/
/*window.Vuetify = require('vuetify');
Vue.use(Vuetify);*/
/*import store from './store'
import * as actions from './store/action-types'
import * as mutations from './store/mutation-types'

import { mapGetters } from 'vuex'
import withSnackbar from './components/mixins/withSnackbar'

if (window.user) {
  store.commit(mutations.USER,  user)
  store.commit(mutations.LOGGED, true)
}*/
//routes
import PagMiCuenta from './components/paginas/MiCuenta.vue';
import PagModelos from './components/paginas/Modelos.vue';
import PagProducto from './components/paginas/Producto.vue';
import PagSubdepartamentos from './components/paginas/Subdepartamentos.vue';
import PagSucursales from './components/paginas/Sucursales.vue';
import PagTipos from './components/paginas/TipoProducto.vue';
import PagUbicaciones from './components/paginas/Ubicaciones.vue';
import PagUsuarios from './components/paginas/Usuarios.vue';
import PagUsuarioTouchId from './components/paginas/UsuarioTouchId.vue';
import VueTippy, { TippyComponent } from "vue-tippy";

Vue.use(VueTippy);
Vue.component("tooltip", TippyComponent);

Vue.config.devtools = true

Vue.use(VueRouter);
Vue.use(VueCarousel);
Vue.use(Vuelidate);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

//Vue.component('pagina-dashboard', require('./components/paginas/Dashboard.vue').default);
Vue.component('app-main', require('./components/layouts/Main.vue').default);
Vue.component('input-texto', require('./components/inputs/Texto.vue').default);
Vue.component('input-textos', require('./components/inputs/Textos.vue').default);
Vue.component('input-texto-multilinea', require('./components/inputs/TextoMultilinea.vue').default);
Vue.component('input-check', require('./components/inputs/Check.vue').default);
Vue.component('input-radios', require('./components/inputs/Radios.vue').default);
Vue.component('input-contrasena', require('./components/inputs/Contrasena.vue').default);
Vue.component('input-fecha', require('./components/inputs/Fecha.vue').default);
Vue.component('input-hora', require('./components/inputs/Hora.vue').default);
Vue.component('input-seleccion', require('./components/inputs/Seleccion.vue').default);
Vue.component('input-opcion', require('./components/inputs/Opcion.vue').default);
Vue.component('input-imagen', require('./components/inputs/Imagen.vue').default);
Vue.component('input-archivo', require('./components/inputs/Archivo.vue').default);
Vue.component('input-color', require('./components/inputs/Color.vue').default);
Vue.component('input-objeto', require('./components/inputs/Objeto.vue').default);
Vue.component('input-moneda', require('./components/inputs/Moneda.vue').default);
Vue.component('input-editor', require('./components/inputs/Editor.vue').default);

Vue.component('crud', require('./components/layouts/Crud.vue').default);
Vue.component('contenedor-multiple', require('./components/ContenedorMultiple.vue').default);
Vue.component('paginador', require('./components/Paginador.vue').default);
//Vue.component('modal', require('./components/Modal.vue').default);
Vue.component('directorio', require('./components/Directorio.vue').default);
Vue.component('directorio-plantilla', require('./components/DirectorioTemplate.vue').default);
Vue.component('calendario', require('./components/Calendario.vue').default);
Vue.component('input-fecha-vue' , require('./components/inputs/FechaVue.vue').default);
Vue.component('v-modal', require('./components/VModal.vue').default );
Vue.component('enhanced-crud', require('./components/layouts/EnhancedCrud.vue').default );
Vue.component('crud-table' , require('./components/layouts/CrudTable.vue').default );
Vue.component('file-field' , require('./components/inputs/File.vue').default)

Vue.component('drag', Drag);
Vue.component('drop', Drop);

const routes = [
    
    { path: '/', component: PagDashboard },
    { path: '/usuarios', component: PagUsuarios, /*children: []*/ },
    { path: '/mi-cuenta', component: PagMiCuenta },

    //{ path: '/horarios-plantillas', component: PagHorarioPlantilla },
    //{ path: '/horarios', component: PagHorario },
    { path: '/usuario-dispositivos', component: PagUsuarioTouchId },

    //{ path: '/opciones', component: PagOpciones },

    //estructura
    //{ path: '/empresas', component: PagEmpresas },
    //{ path: '/sucursales', component: PagSucursales },
    //{ path: '/departamentos', component: PagDepartamentos },
    //{ path: '/subdepartamentos', component: PagSubdepartamentos },
    //{ path: '/ubicaciones', component: PagUbicaciones },
    //{ path: '/comunicados', component: PagComunicados },

    //documentos
    //{ path: '/categorias-documentos', component: PagDocumentoCategorias },
    //{ path: '/documentos', component: PagDocumentos },

    //{ path: '/credenciales', component: PagCredenciales },

    //control de activos
    //{ path: '/inventario', component: PagInventario },
    //{ path: '/atributos', component: PagAtributos },
    //{ path: '/tipos', component: PagTipos },
    //{ path: '/categorias', component: PagCategorias },
    //{ path: '/marcas', component: PagMarcas },
    //{ path: '/modelos', component: PagModelos },
    //{ path: '/productos', component: PagProducto },
    //{ path: '/entregas', component: PagEntregas },
    //{ path: '/documentos-creditos', component: PagGaleriaCreditos },
    { path: '/codigos-qr', component: PagCodigoQr },

    //Catalogo
    { path: '/paises', component: PagPaises },
    { path: '/barrios', component: PagBarrios },
    { path: '/municipios', component: PagMunicipios },
    { path: '/departamentos-geo' , component: PagDepartamentoGeografico},
    { path: '/documentos-intreza', component: PagDocumentosIntreza },
    { path: '/productos', component: PagProductos},

    { path: '/ezadigital-metas', component: PagEzaDigitalMetas},
    { path: '/ezadigital-metas-usuario', name: 'ezadigital_metas_usuario', component: PagEzaDigitalMetasUsuario, props: { id_usuario: 0 }},
    { path: '/ezadigital-ubicaciones-usuario', name: 'ezadigital_ubicaciones_usuario', component: PagEzaDigitalUbicacionesUsuario, props: { id_usuario: 0 }},

    //Cliente
    { path: '/clientes' , component: PagClientes},
    { path: '/clientes-empresa' , component: PagClientesEmpresa},
    { path: '/clientes-persona' , component: PagClientesPersona},

    //Credito

    { path: '/presolicitud' , component: PagCreditos },
    { path: '/seguimiento-credito' , component: PagSeguimientoCredito },
    { path: '/reporte-presolicitud' , component: PagReportePresolicitud},

    //control de almacenes
    //{ path: '/almacen-proveedor', component: PagAlmacenProveedor },
    //{ path: '/almacen-marca', component: PagAlmacenMarca },
    //{ path: '/almacen-modelo', component: PagAlmacenModelo },
    //{ path: '/almacen-categoria', component: PagAlmacenCategoria },
    //{ path: '/almacen-subcategoria', component: PagAlmacenSubcategoria },
    //{ path: '/almacen-bodega', component: PagAlmacenBodega },
    //{ path: '/almacen-division', component: PagAlmacenDivision },
    //{ path: '/almacen-celda', component: PagAlmacenCelda },
    //{ path: '/almacen-tipo-producto', component: PagAlmacenTipoProducto },
    //{ path: '/almacen-producto', component: PagAlmacenProducto },
    //{ path: '/almacen-entrada', component: PagAlmacenDocumento, props: { tipo: 1 } },
    //{ path: '/almacen-salida', component: PagAlmacenDocumento, props: { tipo: 0 } },
    //{ path: '/almacen-ajuste', component: PagAlmacenDocumento, props: { tipo: 2 } },
    { path: '/importar', component: PagImportar },
];

const router = new VueRouter({
    routes
});

//variables globales
Vue.mixin({
    /*props: {
        urls: Object,
        avatar_defecto: String,
    },*/

    data: () => ({
        texto_confirmar_eliminar: '¿Está seguro que quiere eliminar?',
        titulo_singular: 'Item',
        titulo_plural: 'Items',
        icono: 'icon-feather-file',
        desde: 1,
        hasta: 5,
        pagina_actual: 1,
        items_por_pagina: 5,
        vista_items: true,
        vista_formulario: false,
        texto_buscado: '',
        propiedades_buscadas: [],
        //items: [],
        //item: {}
        subdirectorio: '',
    }),

    watch: {
        pagina_actual() {
            this.desde = ((this.pagina_actual - 1) * this.items_por_pagina) + 1;
            this.hasta = (this.desde - 1) + this.items_por_pagina;
        },

        items_por_pagina(){
            this.desde = ((this.pagina_actual - 1) * this.items_por_pagina) + 1;
            this.hasta = (this.desde - 1) + this.items_por_pagina;
        }
    },

    methods: {
        parametrosAdicionales() {
            return {};
        },

        cargarData(parametros) {
            this.statusCargando(true);
    
            if (typeof parametros === 'undefined') parametros = {};

            this.$http.get(this.urls.get, {
                params: {
                    ...{
                        _fuente: this.fuente,
                        _accion: 'index',
                        _subdirectorio: this.subdirectorio,
                    },
                    ...this.parametrosAdicionales(),
                    ...parametros,
                }
            })
            .then(response => {

                if (response.status === 200) {
                    this.debugResponse({response})
                    let data = response.data;
                    let n = 1;

                    for (const prop in data) {
                        if (data.hasOwnProperty(prop)) {
                            data[prop]['_indice'] = n;
                            n++;
                        }
                    }

                    this.items = [ ...data ];
                    
                    this.debugStuff({data, t: this},"hotpink","Data");
                    this.$forceUpdate();
                    //initTooltips();

                    this.procesarCargaData(data);
                    this.cargarDataAdicional();
                }

                this.statusCargando(false);
            }).catch( error => {
                this.debugError(error)
            });
        },

        cargarDataAdicional() {

        },

        procesarData(data) {

        },

        procesarCargaData(data) {

        },

        postCargarData(data) {
            this.items = data;
        },

        limpiarItem() {

        },

        hideForm(){

        },

        setItemData(data) {

        },

        itemDataSet(data) {
            this.setItemData(data);
        },

        mostrarFormulario() {
            this.vista_items = false;
            this.vista_formulario = true;
            //this.$refs.campo_nombre.$el.focus();
            //this.$router.replace('?_v=n');
        },

        ocultarFormulario() {
            this.vista_formulario = false;
            this.vista_items = true;
        },

        mostrarFormularioNuevo() {
            this.limpiarItem();
            this.mostrarFormulario();
        },

        statusGuardando(status) {
            Vue.prototype.$guardando = !!status;

            if (Vue.prototype.$guardando) {
                document.getElementById('app').classList.add('guardando');
            }
            else {
                document.getElementById('app').classList.remove('guardando');
            }
        },

        statusCargando(status) {
            Vue.prototype.$cargando = !!status;

            if (Vue.prototype.$cargando) {
                document.getElementById('app').classList.add('cargando');
            }
            else {
                document.getElementById('app').classList.remove('cargando');
            }
        },

        guardar($frm) {
            this.statusGuardando(true);
            this.enviarForm($frm);
        },

        //Envia un dato o varios a la api
        //@payload: datos a enviar | object
        //@onSucces: callback utilizado al completarse el request | function
        async handleSubmit(payload, onSuccess){

            this.statusGuardando(true); //Hace toggle al boton

            try{

                const form = this.createFormData(payload);

                const response = this.$http.post(this.urls.post, form , this.$defaultConfig );

                if(response.status === 200){

                    this.debugResponse(response);

                    if(typeof onSucces === "function") {
                        
                        if( onSuccess(response.data) === false )
                            return;

                    }

                    resultadoSolicitudDefecto(response.data);

                    if(response.data.ok){

                        if(this.procesarCargaData(response.data) === false)
                            return;
                        
                        this.ocultarFormulario();
                        this.cargarData(); //Carga la data nuevamente
                        this.limpiarItem();
                        this.hideForm();

                    }

                }

            }catch(error){

                this.debugError(error);
                mensajeError('Error de servidor.');

            }

            this.statusGuardando(false); //Se hace toggle al boton

        },

        enviarForm(frm, fn_done) {

            const form_data = new FormData(typeof frm === 'undefined' ? this.$refs['form'] : frm);
            const config = { headers: { 'Content-Type': 'multipart/form-data' } };
            
            this.$http.post(this.urls.post, form_data, config)
                .then(response => {

                    if (response.status === 200) {
                        this.debugResponse({response})
                        if (typeof fn_done === 'function') {
                            if (fn_done(response.data) === false) return;
                        }

                        resultadoSolicitudDefecto(response.data);

                        if (response.data.ok) {
                            if (this.procesarData(response.data) === false) return;
                            this.ocultarFormulario();
                            this.cargarData();
                            this.limpiarItem();
                            this.hideForm();
                        }
                    }
                    else if (response.status === 500) {
                        mensajeError('Error de servidor.');
                    }

                    this.statusGuardando(false);
                }).catch( error => {
                    this.debugError(error);
                });
        },

        editarItem(item, items, fn_listo) {

            this.debugStuff(items,"hotpink","Debug items en editar Item");

            if (typeof items === 'undefined') items = this.items;

            //Indice = key
            /*Busca el objeto a editar en el arreglo*/
            /*hace una doble validacion con hasownproperty*/
            /*luego cuando el id del item coincida con un id de un item en items*/
            /*hace una peticion a la api*/
            for (const indice in items) {

                if ( items.hasOwnProperty(indice) ) {

                    if (items[indice].id === item.id) {

                        this.$http.get(this.urls.get, {
                            params: {
                                _fuente: this.fuente,
                                _subdirectorio: this.subdirectorio,
                                id: item.id
                            }
                        })
                        .then(response => {

                            if (response.status === 200) {

                                this.debugStuff({response, item, items}, "hotpink", "respuesta editar item");
                                
                                const data = response.data;

                                this.setItemData(data);

                                if (typeof fn_listo === 'function') {
                                    this.mostrarFormulario();
                                    fn_listo(data);
                                }

                            }
                        }).catch( err => {
                            this.debugError(err);
                        });
                        break;
                    }
                }
            }
        },

        removerItem(item, items) {
            if (typeof items === 'undefined') items = this.items;
            if (typeof items !== 'object' || !items instanceof Array) return;
            for (const indice in items) {
                if ( items.hasOwnProperty(indice) ) 
                    if ( items[indice].id === item.id ) {
                        const self = this;
                        confirmar( this.texto_confirmar_eliminar, function() {
                            const params = {
                                _fuente: self.fuente,
                                _subdirectorio: self.subdirectorio,
                                _accion: 'eliminar',
                                id: item.id,
                            };
                            self.$http
                                .post(self.urls.post, params)
                                .then(response => {
                                    self.debugResponse({self, response})

                                    if (response.status === 200) {
                                        resultadoSolicitudDefecto(response.data);

                                        if (response.data.ok) {
                                            //self.items.splice(indice, 1);
                                            
                                            /*console.log({
                                                indice,
                                                items,
                                                first: "Primero"
                                            })*/
                                            items.splice(indice, 1);

                                            if (typeof self.itemEliminado === 'function') {
                                                /*console.log({
                                                    indice,
                                                    items
                                                })*/
                                                //self.itemEliminado(indice);
                                            }

                                            self.actualizarIndices();
                                        }
                                    }
                                    else if (response.status === 500) {
                                        mensajeError('Error de servidor.');
                                    }
                                });
                        });
                        break;
                    }
            }
        },

        postItemEliminado(indice) {
            this.items.splice(indice, 1);
            this.actualizarIndices();
        },

        actualizarIndices() {
            let n = 1;
            for (const indice in this.items) {
                if (this.items.hasOwnProperty(indice)) {
                    this.items[indice]['_indice'] = n;
                    n++;
                }
            }
        },

        esVisible(item/*, index*/) {
            const index = item._indice;

            if (!this.texto_buscado.length) 
                return !(index < this.desde || index > this.hasta);

            const textos = this.texto_buscado.split(' ');

            for (const texto of textos) {

                const t = sinAcentos(texto);

                if (!t.length) 
                    continue;

                let valida_t = false;

                for (const prop of this.propiedades_buscadas) {

                    if(typeof item[prop] === 'number'){
                        
                        if( item[prop].toString().indexOf(t) !== -1){
                            valida_t = true;
                            break;
                        };

                    }

                    if (typeof item[prop] !== 'string') 
                        continue;
                        
                    const value = item[prop];
                    
                    if (sinAcentos(value).indexOf(t) !== -1) {
                        //return true;
                        valida_t = true;
                        break;
                    }
                }

                if (!valida_t) {
                    return false;
                }
            }

            /*if (index < this.desde || index > this.hasta) {
                return false;
            }*/

            return true;
        },

        formatoFecha(val, formato_origen, formato_destino) {
            if (typeof val !== 'string' || !val.length) {
                return '';
            }
            return formatoFechaApp(val, formato_origen, formato_destino);
        },

        esAdmin() {
            return typeof this.$usuario === 'object' && typeof this.$usuario.admin === 'boolean' && this.$usuario.admin;
        },

        tienePermiso(categoria, accion) {
            if (this.$usuario.admin) {
                return true;
            }
            for (const permiso of this.$usuario.permisos) {
                if (permiso.categoria === categoria) {
                    if (typeof accion === 'string') {
                        if (permiso.nombre === accion) {
                            return true;
                        }
                    }
                    else if (permiso.nombre !== 'todos' && permiso.nombre !== 'solo_propios') {
                        return true;
                    }
                }
            }

            return false;
        },

        //Metodo para depurar errores [NO USAR EN PRODUCCION!!]
        debugError(error){

            //comentar esto en produccion!
           // if(true) return;
           
            console.log('%c Debug Error', `color: red; font-weight: bold; background-color: black;`)
            !error.response && console.error(error);
            console.log({...(error.response || {})})
            console.log('%cFin Debug Error', `color: red; font-weight: bold; background-color: black;`)
            
        },

        debugResponse(response){
            
            console.log('%c Debug Response', `color: teal; font-weight: bold; background-color: black;`)
            console.log(response);
            console.log('%c Fin Debug Response', `color: teal; font-weight: bold; background-color: black;`)
            
        },

        debugForm(form, message){
            //Testing comentar en produccion
            
            const formdatadebug = [];
            for (var pair of form.entries()) {
                formdatadebug.push(pair);
            }
            console.log(`%c ${message || "debug Formulario"}`, "color:hotpink; background-color: black; font-size: 15px;")
            console.log({
                form,
                formdatadebug
            })
            console.log(`%c Fin ${message || "debug Formulario"}`, "color:hotpink; background-color: black; font-size: 15px;")
            
            //Fin testing
        },

        debugStuff( stuff , color, message){
            
            console.log(`%c ${message || "Debug stuff"}`, `color: ${color || 'pink'}; font-weight: bold; background-color: black; font-size: 15px`)
            console.log(stuff);
            console.log(`%c ${message || "Debug stuff"}`, `color: ${color || 'pink'}; font-weight: bold; background-color: black; font-size: 15px`)
            
        },

        crearFormData(objeto){

            const form = new FormData;

            for( const propiedad in objeto ){
                
                //console.log({propiedad, o: objeto[propiedad], c: typeof objeto[propiedad] })

                if( Array.isArray( objeto[propiedad] ) ){

                    form.append( propiedad , JSON.stringify(objeto[propiedad]) )
                }

                else
                    form.append( propiedad , objeto[propiedad] )
            }

            return form;

        },


        createFormData(payload){


            const form = new FormData;

            for(const key in payload ){

                if( Array.isArray( payload[key] ) ){
                    form.append( key , JSON.stringify(payload[key]));
                }

                else{
                    form.append( key ,payload[key] );
                }

            } 

            return form;

        }

    }
});

Vue.prototype.$http = axios;

Vue.prototype.$uploads_img_dir = '';
Vue.prototype.$uploads_doc_dir = '';
Vue.prototype.$public_dir = '';
Vue.prototype.$avatar_defecto = '';
Vue.prototype.$img_placeholder = '';
Vue.prototype.$url_get = '';
Vue.prototype.$url_post = '';
Vue.prototype.$usuario = {};
Vue.prototype.$empresa = {};
Vue.prototype.$cargando = false;
Vue.prototype.$guardando = false;
Vue.prototype.$defaultConfig = { headers: { 'Content-Type': 'multipart/form-data' } };

new Vue({
    el: '#app',
    router,
    axios,

    data: () => ({

    }),

    computed: {

    },

    methods: {

    },

    created() {

    }
});
