<template>
    <main>
        <section class="index" v-if="vista_items">
            <!-- Dashboard Headline -->
            <div class="dashboard-headline">
                <div class="row">
                    <div class="col-sm-8">
                        <h3>
                            Clientes
                            <span class="item-cargando">&nbsp;<i class="icon-line-awesome-hourglass-2 fa-spin"></i></span>
                        </h3>
                    </div>

                    <div class="col-sm-4">
                        <div class="input-with-icon">
                            <input type="text" placeholder="Buscar..." v-model="texto_buscado" @dblclick="texto_buscado = ''">
                            <i class="icon-material-outline-search"></i>
                        </div>
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
                            <h3><i class="icon-material-outline-supervisor-account"></i> {{ items.length }} {{ items.length === 1 ? 'cliente' : 'clientes' }}</h3>

                            <div class="actions">
                                <button type="button" class="popup-with-zoom-anim button dark ripple-effect" @click="mostrarFormularioNuevo">
                                    <i class="icon-feather-plus"></i> Nuevo
                                </button>
                            </div>
                        </div>

                        <!--Table list data-->
                        <div class="content" :class="clasesTipoVista">
                            <ul class="dashboard-box-list">
                                <template v-for="item in items"
                                    appear
                                    mode="out-in"
                                    enter-active-class="animated fadeIn"
                                    leave-active-class="animated zoomOut"
                                >
                                    <li v-show="esVisible(item)" @click="expandir" :key="item.id">
                                        <!-- Overview -->
                                        <div class="freelancer-overview manage-candidates">
                                            <div class="freelancer-overview-inner">
                                                <!-- Avatar -->
                                                <div class="freelancer-avatar">
                                                    <div class="verified-badge"></div>
                                                    <a href="#"><img :src="avatarUrl(item)" alt=""></a>
                                                </div>

                                                <!-- Name -->
                                                <div class="freelancer-name">
                                                    <h4 :class="item.status == 2 ? 'item-inhabilitado' : ''">
                                                        <span v-html="iconoStatus(item)"></span>{{ nombrePersona(item) }}<!-- <mark class="color">{ { item.nombre }}</mark>-->
                                                    </h4>

                                                    <!-- Details -->
                                                    <div>
                                                        <span class="freelancer-detail-item" v-if="item.id_empresa">
                                                            <span><i class="icon-material-outline-business"></i> {{ item.empresa/*nombreEmpresa(item.id_empresa)*/ }}</span>
                                                        </span>
                                                        
                                                        <!--<span class="freelancer-detail-item" v-if="item.id_sucursal">
                                                            <span><i class="icon-feather-square"></i> {{ nombreSucursal(item.id_sucursal) }}</span>
                                                        </span>-->
                                                        
                                                        <span class="freelancer-detail-item" v-if="item.id_departamento">
                                                            <span><i class="icon-feather-grid"></i> {{ nombreDepartamento(item.id_departamento) }}</span>
                                                        </span>

                                                        <span class="freelancer-detail-item" v-if="item.id_departamento">
                                                            <span><i class="icon-line-awesome-tag"></i> {{ item.num_control }}</span>
                                                        </span>
                                                    </div>
                                                    
                                                    <div>
                                                        <span class="freelancer-detail-item" v-if="item.correo">
                                                            <a :href="'mailto:' + item.correo"><i class="icon-feather-mail"></i> {{ item.correo }}</a>
                                                        </span>

                                                        <span class="freelancer-detail-item" v-if="item.telefono">
                                                            <a :href="'tel:' + item.telefono"><i class="icon-feather-phone"></i> {{ item.telefono }}</a>
                                                        </span>

                                                        <div>
                                                            <span class="freelancer-detail-item">
                                                                <strong>Usuario:</strong> <span>{{ item.nombre }}</span>
                                                            </span>
                                                            <span class="freelancer-detail-item">
                                                                <strong>DNI: </strong> <span>{{ item.dni }}</span>
                                                            </span>
                                                            <span class="freelancer-detail-item">
                                                                <strong>RUC: </strong> <span>{{ item.ruc }}</span>
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <!-- Buttons -->
                                                    <div class="buttons-to-right always-visible margin-top-25 margin-bottom-5">

                                                        <a href="#" class="button gray ripple-effect ico ico-text detail" :title="item.status == 2 ? 'Habilitar' : 'Inhabilitar'" @click.prevent="inhabilitarItem(item)">
                                                            <i :class="item.status == 2 ? 'icon-feather-check-circle' : 'icon-feather-slash'"></i> <span>{{ item.status == 2 ? 'Habilitar' : 'Inhabilitar' }}</span>
                                                        </a>

                                                        <a href="#" class="descriptive-button button gray ripple-effect ico" title="Telefonos"> <!-- data-tippy-placement="top" -->
                                                            <i class="icon-feather-map-pin"></i>
                                                            <span>{{(item.telefonos || []).length}}</span>
                                                        </a>

                                                        <a href="#" class="descriptive-button  button gray ripple-effect ico" title="Direcciones"> <!-- data-tippy-placement="top" -->
                                                            <i class="icon-feather-phone"></i>
                                                            <span>{{(item.direcciones || []).length}}</span>
                                                        </a>

                                                        <a href="#" class="button gray ripple-effect ico" title="Editar" @click="editarItem(item)"> <!-- data-tippy-placement="top" -->
                                                            <i class="icon-feather-edit"></i>
                                                        </a>

                                                        <a href="javascript:" class="button gray ripple-effect ico" title="Eliminar" @click="removerItem(item)"> <!-- data-tippy-placement="top" -->
                                                            <i class="icon-feather-trash-2"></i>
                                                        </a>

                                                        <!--Boton para ver detalles-->
                                                        <a href="#" class="button gray ripple-effect ico ico-text detail" @click="seeClientDetails(item)">
                                                            <i class="icon-feather-eye"></i>
                                                        </a>
                                                        <!--Fin Boton para ver detalles-->


                                                    </div>


                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </template>

                                <li v-if="false">
                                    <!-- Overview -->
                                    <div class="freelancer-overview manage-candidates">
                                        <div class="freelancer-overview-inner">

                                            <!-- Avatar -->
                                            <div class="freelancer-avatar">
                                                <div class="verified-badge"></div>
                                                <a href="#"><img src="images/user-avatar-big-03.jpg" alt=""></a>
                                            </div>

                                            <!-- Name -->
                                            <div class="freelancer-name">
                                                <h4><a href="#">Sindy Forest <img class="flag" src="images/flags/au.svg" alt="" title="Australia" data-tippy-placement="top"></a></h4>

                                                <!-- Details -->
                                                <span class="freelancer-detail-item"><a href="#"><i class="icon-feather-mail"></i> sindy@example.com</a></span>
                                                <span class="freelancer-detail-item"><i class="icon-feather-phone"></i> (+61) 123-456-789</span>

                                                <!-- Rating -->
                                                <div class="freelancer-rating">
                                                    <div class="star-rating" data-rating="5.0"></div>
                                                </div>

                                                <!-- Buttons -->
                                                <div class="buttons-to-right always-visible margin-top-25 margin-bottom-5">
                                                    <a href="#" class="button ripple-effect"><i class="icon-feather-file-text"></i> Download CV</a>
                                                    <a href="#small-dialog" class="popup-with-zoom-anim button dark ripple-effect"><i class="icon-feather-mail"></i> Send Message</a>
                                                    <a href="#" class="button gray ripple-effect ico" title="Remove Candidate" data-tippy-placement="top"><i class="icon-feather-trash-2"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>

            <paginador
                    v-show="!texto_buscado.length"
                    v-model="pagina_actual"
                    :total_items="items.length"
                    @itemschanged="changeItemsPerPage"
            ></paginador>
        </section>

        <section class="form" v-if="vista_formulario">
            <!-- Dashboard Headline -->
            <div class="dashboard-headline">
                <h3>
                    <button type="button" class="button" @click="ocultarFormulario">
                        <i class="icon-material-outline-arrow-back"></i>
                    </button>
                    &nbsp;
                    {{ item.id > 0 ? 'Modificar cliente' : 'Agregar cliente' }}
                </h3>
            </div>

            <form ref="form">
                <!-- Row -->
                <div class="row">

                    <!-- Dashboard Box -->
                    <div class="col-xl-12">
                        <div class="dashboard-box margin-top-0">

                            <!-- Headline -->
                            <div class="headline" v-if="false">
                                <h3><i class="icon-feather-folder-plus"></i> Agregar cliente</h3>
                            </div>

                            <!--Formulario de clientes-->
                            <div class="content with-padding padding-bottom-10">

                                <!--Datos de registro-->

                                <div class="message-time-sign">
                                    <span>Datos de registro</span>
                                </div>

                                <div class="row">
                                    <div class="col-xl-4 ">
                                        <input-texto
                                            v-model="item.nombre"
                                            nombre="nombre"
                                            etiqueta="Nombre"
                                        ></input-texto>
                                    </div>

                                    <div class="col-xl-4 ">
                                        <input-seleccion
                                            v-model="item.id_tipo"
                                            nombre="id_tipo"
                                            etiqueta="Tipo de persona"
                                            :items="id_tipo"
                                            :items_seleccionados="[item.id_tipo]"
                                            :buscador="true"
                                        />
                                    </div>
                                    <div class="col-xl-2">
                                        <input-texto
                                            v-model="item.ruc"
                                            nombre="ruc"
                                            etiqueta="RUC"
                                        ></input-texto>
                                    </div>
                                    <div class="col-xl-2">
                                        <input-texto
                                            v-model="item.dni"
                                            nombre="dni"
                                            etiqueta="DNI"
                                        ></input-texto>
                                    </div>
                                </div>

                                <!--Direccion-->

                                <div class="message-time-sign">
                                    <span>Datos de dirección</span>
                                </div>

                                <div class="row">
                                    <div class="col-xl-12">
                                        <button type="button" class="button" @click="addFormularioDireccion">
                                            <i class="icon-feather-folder-plus"></i>
                                        </button>
                                        <span>
                                            ANADIR DIRECCIÓN
                                        </span>
                                    </div>
                                </div>

                                <div class= "dashboard-box" v-for="(direccion,index) in item.direcciones" v-bind:key="direccion.indice">
                                    <!--Direccion form-->
                                    
                                    <div class = "row margin-bottom-50">
                                        <div class = "col-xl-12">
                                            <button type="button" class="button red" @click="eliminarDireccion(index)">
                                                <i class="icon-feather-trash-2"></i>
                                            </button>
                                            <span>Dirección {{"  "}}  #{{index + 1}}</span>

                                        </div>
                                    </div>
                                    

                                    <div class = "row">
                                        <div class="col-xl-5">
                                            <div class = "row">
                                                <div class  = "col-xl-12">
                                                    <input-seleccion
                                                        v-model="direccion.id_barrio"
                                                        nombre="barrios"
                                                        etiqueta="Barrio"
                                                        :items="barrios"
                                                        :items_seleccionados="[direccion.id_barrio]"
                                                        :buscador="true"
                                                    />
                                                </div>
                                                <div class  = "col-xl-12">
                                                    <input-seleccion
                                                        v-model="direccion.pertenece"
                                                        nombre="pertenece"
                                                        etiqueta="Pertenece"
                                                        :items="perteneceOpciones"
                                                        :items_seleccionados="[direccion.pertenece]"
                                                    />
                                                </div>
                                                <div class  = "col-xl-12">
                                                    <input-seleccion
                                                        v-model="direccion.tipo_direccion"
                                                        nombre="tipo"
                                                        etiqueta="Tipo"
                                                        :items="tipoDireccion"
                                                        :items_seleccionados="[direccion.tipo_direccion]"
                                                    />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-xl-7">
                                            <input-texto-multilinea
                                                v-model="direccion.descripcion"
                                                nombre="descripcion"
                                                etiqueta="Descripcion"
                                            />
                                        </div>
                                    </div>
                                </div>
                                

                                <!--Contacto-->
                                <div class="message-time-sign">
                                    <span>Datos de contacto</span>
                                </div>

                                <!--Contacto header-->
                                <div class="row">
                                    <div class="col-xl-12">
                                        <button type="button" class="button" @click="addFormularioContacto">
                                            <i class="icon-feather-folder-plus"></i>
                                        </button>
                                        <span>
                                            ANADIR CONTACTO
                                        </span>
                                    </div>
                                </div>

                                <div class= "dashboard-box" v-for="(contacto, index) in item.contactos" v-bind:key="contacto.indice">
                                    
                                    <div class = "row margin-bottom-50">
                                        <div class = "col-xl-12">
                                            <button type="button" class="button red" @click="eliminarContacto(index)">
                                                <i class="icon-feather-trash-2"></i>
                                            </button>
                                            <span>Contacto {{"  "}} #{{index + 1}}</span>

                                        </div>
                                    </div>
                                    
                                    
                                    <div class="row">
                                        <div class="col-xl-4">
                                            <div class = "row">
                                                <div class = "col-xl-12">
                                                    <input-seleccion
                                                        v-model="contacto.tipo"
                                                        nombre="tipoContacto"
                                                        etiqueta="Contacto"
                                                        :items="tipoContacto"
                                                        :items_seleccionados="[contacto.tipo]"
                                                    />
                                                </div>
                                                <div class = "col-xl-12">
                                                    <input-seleccion
                                                        v-model="contacto.pertenece"
                                                        nombre="pertenece"
                                                        etiqueta="Pertenece"
                                                        :items="perteneceOpciones"
                                                        :items_seleccionados="[contacto.pertenece]"
                                                    />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-xl-8">
                                            <div class ="row">
                                                <div class = "col-xl-12">
                                                    <input-texto-multilinea
                                                        v-model="contacto.descripcion"
                                                        nombre="contacto.descripcion"
                                                        etiqueta="Descripcion"
                                                    />
                                                </div>
                                                <div class = "col-xl-12">
                                                    <input-texto-multilinea
                                                        v-model="contacto.observacion"
                                                        nombre="observacion"
                                                        etiqueta="Observacion"
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                </div>
                            </div>
                        </div>

                    <input type="hidden" name="_fuente" :value="fuente">
                    <input type="hidden" name="id" v-model="item.id">

                    <div class="col-xl-12">
                        <a href="#" class="button ripple-effect button-sliding-icon big margin-top-30" @click="guardar">
                            <span class="item-guardar">Guardar <i class="icon-feather-save"></i></span>
                            <span class="item-guardando">Guardando... <i class="icon-line-awesome-hourglass-2 fa-spin"></i></span>
                        </a>
                    </div>

                </div>
                <!-- Row / End -->
            </form>
        </section>

        <section v-if="showClientDetailsView">
            
            <div class="dashboard-headline">
                <h3>
                    <button type="button" class="button" @click="toggleClientsView">
                        <i class="icon-material-outline-arrow-back"></i>
                    </button>
                    &nbsp;
                    Detalles de cliente
                </h3>
            </div>

            <div class="row">
                
                <!--Detalles de cliente-->
                <div class="col-xl-12 mb-1">
                    <div class="client-info-container">
                        <div class="client-headline">
                            <span class="client-name">{{ viewingClient.nombre }}</span>
                        </div>
                        <div class="client-contact">

                            <span>Direcciones</span>

                            <div class="tooltip-container">
                                <div 
                                    v-for="(direction , index) in viewingClient.directions || []" 
                                    :key="index"
                                    class="tooltip"
                                >
                                    <i class="icon-feather-plus"></i>{{ getDirection(direction) }}
                                </div>
                            </div>

                        </div>
                        <div class="client-contact">
                            <span>Contactos</span>
                            <div class="tooltip-container">
                                <div 
                                    v-for="(contact , index) in viewingClient.contacts || []" 
                                    :key="index"
                                    class="tooltip"
                                >
                                    <i class="icon-feather-plus"></i>{{' ' + contact.descripcion}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Fin Detalles de cliente-->

                <span class="font-weight-bold title"> Presolicitudes </span>


                <!--Presolicitudes-->
                <div class="col-xl-12 mb-1"> 

                    <ul class>

                        <template v-for="preapplication in viewingClient.preapplications"
                            appear
                            mode="out-in"
                            enter-active-class="animated fadeIn"
                            leave-active-class="animated zoomOut"
                        >
                            <li :key="preapplication.id">
                                <!-- Overview -->
                                <div>
                                    <div class="freelancer-overview-inner">
                                        <div class="freelancer-name">
                                            <span class="freelancer-detail-item" v-if="preapplication.fecha_solicitud">
                                                <span><strong>Fecha:</strong> {{ preapplication.fecha_solicitud }}</span>
                                            </span>

                                            <span class="freelancer-detail-item">
                                                <span><strong>Monto:</strong> {{ getDetailsFromAmount( preapplication ) }}</span>
                                            </span>

                                            <div class="row" v-if="typeof preapplication.nombreCreadorCredito === 'string' && preapplication.nombreCreadorCredito.length">
                                                <div class="col-xl-12">
                                                    <span><strong>Creado por:</strong> <span>{{ preapplication.nombreCreadorCredito}}</span></span>
                                                </div>
                                            </div>

                                            <div class="row" v-if="preapplication.estado_etapa">
                                                <div class="col-xl-12">
                                                    <span><strong>Actualmente:</strong> <span>{{ getCurrentStage( preapplication.estado_etapa ) }}</span></span>
                                                </div>
                                            </div>
                                            <div class="row" v-if="preapplication.estado_etapa">
                                                <div class="col-xl-12">
                                                    <span><strong>Estado del credito:</strong> <span>{{ getCreditState( preapplication.estado_vida ) }}</span></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </template>
                    </ul>

                </div>
                <!--Fin Presolicitudes-->

                <span class="font-weight-bold"> Personas </span>
                <span class="font-weight-bold"> Empresas </span>

                <!--Detalles de personas y empresas-->
                <div class="row col-xl-12">
                
                    <!--Personas-->
                    <div class="col-xl-6 row">
                        
                        <div
                            v-for="( people , index) in viewingClient.peoples"
                            :key="index"
                            class="people-card col-xl-6"
                        >

                            <span class="people-name"> {{getFullName(people)}} <i :class="getGender(people)"></i>  </span>
                            <span class="people-description"> <i class="las la-id-card" ></i>{{' DNI:' + people.dni + ' |RUC: ' + people.ruc}}</span>
                            <span class="people-description"> <i class="las la-calendar" ></i>{{ people.fecha_nacimiento }}  </span>
                            
                        </div>

                    </div>
                    <!--Fin Personas-->

                    <!--Empresas-->
                    <div class="col-xl-6 row">

                        <div
                            v-for="( company , index) in viewingClient.companies"
                            :key="index"
                            class="people-card col-xl-12"
                        >

                            <span class="people-name"> <i class="las la-industry"></i> {{company.nombre}} </span>
                            <span class="people-description"> <i class="las la-id-card" ></i>{{' RAZON:' + company.razon + ' |RUC: ' + company.ruc}}</span>
                            
                        </div>

                    </div>
                    <!--Fin Empresas-->

                </div>

            </div>

        </section>


    </main>
</template>

<script>
    window.Vue = require('vue');

    import monedas from '../../monedas.json';
    import moment, { defaultFormat } from 'moment';

    const stages = [
        {id: 1 , nombre: "Etapa de presolicitud" , icono: "las la-user"},
        {id: 2 , nombre: "Etapa de Analísis de Credito", icono: "las la-search-dollar"},
        {id: 3 , nombre: "Etapa de Supervisión de Crédito", icono: "las la-file-invoice"},
        {id: 4 , nombre: "Etapa de Comité de Crédito" , icono: "las la-users"},
        {id: 5 , nombre: "Etapa de Desembolso", icono: "las la-money-bill"}
    ]



    export default {
        name: "Clientes.vue",
        components: {},


        props: {
            urls: Object,
            avatar_defecto: String,
        },

        data: () => ({
            fuente: 'IntranetCliente',
            barrios: [],
            items: [],
            perteneceOpciones: [{id: 0 , nombre: "Persona"}, {id: 1, nombre:"Empresa"}],
            id_tipo:[{id: 0 , nombre: "Natural"}, {id: 1, nombre:"Juridica"}],
            tipoDireccion: [{id: 0 , nombre: "Casa"}, {id: 1, nombre:"Trabajo"}, {id: 2, nombre:"Negocio"},{id: 3, nombre:"TSucursal"}],
            tipoContacto: [{id: 0 , nombre: "Telefono"}, {id: 1, nombre:"Correo"}, {id: 2, nombre:"Facebook"},{id: 3, nombre:"Instagram"}],
            
            item: {
                id: 0,
                nombre: '',
                id_tipo: '',
                fecha_registro:'2020-12-31',
                ruc: '',
                dni: '',
                direccion: {
                    tipo_direccion: '',
                    pertenece: '',
                    descripcion: '',
                    id_barrio: '',
                },
                contacto: {
                    tipo: '',
                    pertenece: '',
                    descripcion: '',
                    observacion: '',
                },
                direcciones: [],
                contactos: []
            },
            vista_items: true,
            vista_formulario: false,
            vista_impresion_carnet: false,
            vista_permisos: false,
            showClientDetailsView: false,

            viewingClient: {},
            monedas: monedas,
            stages: stages,


            texto_buscado: '',
            propiedades_buscadas: [
                'nombre'
            ],
            tipo_vista: 'compacto',
            id_item: 0,
            indice: 0,
        }),

        methods: {

            cargarDataAdicional(){
                const self = this;
                this.$http.get(this.urls.get, {
                    params: {
                        _fuente: this.fuente,
                        _accion: 'cargarListados',
                    }
                })
                .then(response => {
                    self.debugResponse(response);
                    if (response.status === 200) {
                        const data = response.data;
                        this.barrios = data["barrios"];
                    }
                })
                .catch(err => {
                    self.debugError(err);
                });
            },  

            addFormularioDireccion(){

                const nuevaDireccion = {
                    indice: this.item.direcciones.length,
                    tipo_direccion: '',
                    pertenece: '',
                    descripcion: '',
                    id_barrio: '',
                }

                this.item.direcciones.push(nuevaDireccion)
            },

            addFormularioContacto(){
                const nuevoContacto = {
                    indice: this.item.contactos.length,
                    tipo: '',
                    pertenece: '',
                    descripcion: '',
                    observacion: '',
                }

                this.item.contactos.push(nuevoContacto)
            },

            eliminarDireccion(direccion){

                this.item.direcciones.splice( direccion.indice , 1 );

            },

            getDirection( direction ){

                return `${direction.barrio} , municipio ${direction.municipio}, ${direction.departamento} ${direction.pais}`

            },

            eliminarContacto(contacto, index){
                this.item.contactos.splice( index , 1 );
            },

            mostrarFormulario() {
                this.vista_items = false;
                this.vista_impresion_carnet = false;
                this.vista_permisos = false;
                this.vista_formulario = true;
                //this.$refs.campo_nombre.$el.focus();
            },

            ocultarFormulario() {
                this.vista_formulario = false;
                this.vista_impresion_carnet = false;
                this.vista_permisos = false;
                this.vista_items = true;
            },

            limpiarItem() {
                this.item.id = 0;
                this.item.nombre = '';

                //contacto
                this.item.contacto = {
                    descripcion: '',
                    observacion: '',
                    pertenece: '',
                    tipo: ''
                }

                //direccion
                this.item.direccion = {
                    descripcion: '',
                    pertenece: '',
                    tipo_direccion: '',
                    id_barrio: ''
                }
            },

            guardar() {
                this.statusGuardando(true);

                const params = {
                    _fuente: this.fuente,
                    _accion: 'registrarCliente'
                };
                //const frm = this.$refs.form;
                //const inputs = new FormData($(frm)[0]);
                //const inputs = $(frm).serialize();

                const form_data = new FormData();

                const direcciones = this.item.direcciones.map( direccion => ({
                    ...direccion,
                    tipo_direccion: this.tipoDireccion[direccion.tipo_direccion],
                    pertenece: this.id_tipo[direccion.pertenece]}))

                const contactos = this.item.contactos.map( contacto => ({
                    ...contacto,
                    tipo: this.tipoContacto[contacto.tipo],
                    pertenece: this.perteneceOpciones[contacto.pertenece]
                }))

                form_data.append("id", this.item.id || null );
                form_data.append("nombre", this.item.nombre);
                form_data.append("fecha_registro", '2020-12-12 00:00:00' );
                form_data.append("id_tipo", this.item.id_tipo);
                form_data.append("_fuente", this.fuente);
                form_data.append("direcciones", JSON.stringify(direcciones));
                form_data.append("contactos", JSON.stringify(contactos));
                form_data.append("ruc", this.item.ruc);
                form_data.append("dni", this.item.dni);
                
                if(!this.item.id)
                    form_data.append("_accion", "registrarCliente");

                this.debugForm(form_data);
                /*form_data.append('_fuente', this.fuente);
                for (const prop in this.item) {
                    if (this.item.hasOwnProperty(prop)) {
                        if (prop === 'foto') continue;
                        form_data.append(prop, this.item[prop] || '');
                    }
                }
                form_data.append('foto', $(this.$refs['foto'].$el).find('input')[0].files[0]);*/

                /*this.$http.post(this.urls.post, {
                    ...params,
                    ...this.item
                })*/
                const config = { headers: { 'Content-Type': 'multipart/form-data' } };
                this.$http.post(this.urls.post, form_data, config)
                .then(response => {
                    if (response.status === 200) {
                        this.debugResponse(response);
                        resultadoSolicitudDefecto(response.data);

                        if (response.data.ok) {
                            this.ocultarFormulario();
                            this.cargarData();
                            this.limpiarItem();
                        }
                    }
                    else if (response.status === 500) {
                        mensajeError('Error de servidor.');
                    }

                    this.statusGuardando(false);
                }).catch(err => {
                    this.debugError(err)
                });
            },

            editarItem(item) {
                for (const indice in this.items) {
                    if (this.items.hasOwnProperty(indice)) {
                        if (this.items[indice].id === item.id) {
                            this.$http.get(this.urls.get, {
                                params: {
                                    _fuente: this.fuente,
                                    id: item.id
                                }
                            })
                            .then(response => {

                                this.debugStuff({response,item},'pink' )

                                if (response.status === 200) {
                                    const data = response.data;

                                    this.item = {...data};

                                    this.mostrarFormulario();
                                }
                            })
                            .catch( err => {
                                this.debugError(err);
                            });
                            break;
                        }
                    }
                }
            },

            removerItem(item) {
                for (const indice in this.items) {
                    if (this.items.hasOwnProperty(indice)) {
                        if (this.items[indice].id === item.id) {
                            const self = this;
                            const c_item = this.items[indice];
                            const nombre_empleado = typeof c_item.nombre_persona === 'string' && c_item.nombre_persona.length ? c_item.nombre_persona : c_item.nombre;
                            confirmar('¿Está seguro que quiere eliminar el empleado ' + nombre_empleado + '?', function() {
                                const params = {
                                    _fuente: self.fuente,
                                    _accion: 'eliminar',
                                    id: item.id,
                                };
                                self.$http.post(self.urls.post, params)
                                    .then(response => {
                                        if (response.status === 200) {
                                            resultadoSolicitudDefecto(response.data);

                                            if (response.data.ok) {
                                                self.items.splice(indice, 1);
                                                self.$forceUpdate();
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
                }
            },

            /*Carga todo los detalles de un cliente*/
            async seeClientDetails(client){

                try{

                    const params = {
                        _fuente: this.fuente,
                        _accion: "detallesCliente",
                        id: client.id,
                    }

                    const response = await this.$http.get(
                        this.urls.get,
                        {params},
                        this.$defaultConfig
                    );


                    if( response.data.ok ){
                        
                        this.viewingClient = {
                            ...client,
                            contacts: response.data.contactos,
                            directions: response.data.direccion,
                            peoples: response.data.personas,
                            companies: response.data.empresas,
                            preapplications: response.data.presolicitudes
                        }

                        this.toggleClientsView();

                    }


                    this.debugResponse(response, "hotpink", "Ver cliente detalles");

                    if(response.data.ok){



                    }

                }catch(error){
                    this.debugError(error);
                    mensajeError('Error de servidor');
                }

            },

            toggleClientsView(){


                this.vista_items = !this.vista_items;
                this.vista_formulario = false;
                this.showClientDetailsView = !this.showClientDetailsView;+
                this.$forceUpdate();

            },

            expandir(e) {
                let $item = $(e.target);

                switch ($item.prop('tagName')) {
                    case 'BUTTON':

                    case 'A':
                        return;

                    case 'LI':
                        break;

                    default:
                        if (['BUTTON','A'].includes($item.parent().prop('tagName'))) {
                            return;
                        }
                        $item = $item.closest('li');
                }

                $item.toggleClass('abierto');
            },

            avatarUrl(item) {
                if (typeof item.foto === 'string' && item.foto.length) {
                    return this.$uploads_img_dir + 'm/' + item.foto; //Vue.prototype.$uploads_img_dir
                }
                return this.avatar_defecto;
            },

            nombrePersona(item) {
                return item.nombre;
            },

            iconoStatus(item) {
                if (item.status == 2) {
                    return `<i class="icon-feather-slash"></i> &nbsp;&nbsp;`;
                }
                return '';
            },

            getDetailsFromAmount( preapplication ){

                const moneda_simbolo = (this.monedas.find( x => parseInt(x.id) === parseInt(preapplication.moneda) ) || {}).simbolo;

                return (moneda_simbolo || '') + (parseFloat(preapplication.monto_solicitado) || 0).toFixed(2);

            },

            getCurrentStage( idStage ){

                return this.stages.find( x => x.id === parseInt(idStage) ).nombre

            },

            getCreditState(state){

                return state === 1 ? "En proceso" : "Rechazado"

            },


            getGender( people ){


                if( people.genero === "hombre" )
                    return "las la-mars color-blue"

                if( people.genero === "mujer" )
                    return "las la-venus color-pink"

                return "las la-mars color-blue"

            },

            getFullName(people){

                return `${people.primer_nombre || ""}${' '}${people.segundo_nombre || ""}`;

            },

            getFullLastName(people){
                return `${people.primer_apellido || ""}${' '}${people.segundo_apellido}`;

            },

            changeItemsPerPage(value){
                this.items_por_pagina = value;
                this.$forceUpdate();
            },



        },

        computed: {
            clasesTipoVista() {
                if (typeof this.tipo_vista !== 'string' || !this.tipo_vista.length) return '';

                switch (this.tipo_vista) {
                    case 'compacto':
                        return 'modo-compacto';
                }

                return '';
            },
        },

        mounted() {
            this.cargarData();
            document.documentElement.scrollTop = 0;
        },

        updated: function () {
            //this.$nextTick(function () {
                //initTooltips();
                //initKeywords();
            //})
        }
    }
</script>

<style scoped>

    .color-blue{
        color: blue;

    }

    .color-pink{
        color: hotpink;
    }

    .item-inhabilitado {
        opacity: .6;
    }

    .client-info-container{

        min-height: 200px;
        border-radius: 25px;
        border: 1px solid blue;
        margin-bottom: 1rem;
        contain: content;

    }

    .client-headline{

        padding: .8rem;
        text-align: center;
        background-color: blue;
        color: white;
        font-size: 1.5rem;
        font-weight: bold;

    }

    .client-contact {

        display: flex;
        flex-direction: column;
        padding: 1.5rem;

    }

    .descriptive-button{
        width: 60px!important;
    }

    .client-contact span {
        font-weight: bold;
        margin-bottom: .5rem;
        display:block;
    }

    .tooltip{

        display: inline-block;
        background-color: blue;
        border-radius: 50px;
        margin-left: .15rem;
        padding: .05rem .8rem;
        color: white;
        font-size: .8rem;
        font-weight: lighter;
        margin-left: .15rem;

    }

    .people-card {
        display: flex;
        flex-direction: column;
        text-align: center;
    }

    .font-weight-bold {
        font-weight: bold;
        margin-bottom: .5rem;
    }

    .title {
        font-size: 1.5rem;
    }

    .mb-1{
        margin-bottom: 1rem !important;

    }

</style>