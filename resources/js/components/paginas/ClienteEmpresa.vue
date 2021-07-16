<template>
    <main>
        <section class="index" v-if="vista_items">
            <!-- Dashboard Headline -->
            <div class="dashboard-headline">
                <div class="row">
                    <div class="col-sm-8">
                        <h3>
                            Empresas
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
                            <h3><i class="icon-material-outline-supervisor-account"></i> {{ items.length }} {{ items.length === 1 ? 'empresa' : 'empresas' }}</h3>

                            <div class="actions">
                                <button type="button" class="popup-with-zoom-anim button dark ripple-effect" @click="mostrarFormularioNuevo">
                                    <i class="icon-feather-plus"></i> Nuevo
                                </button>
                            </div>
                        </div>

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
                                                        <span v-html="iconoStatus(item)"></span>{{ nombreEmpresa(item) }}<!-- <mark class="color">{ { item.nombre }}</mark>-->
                                                    </h4>

                                                    <!-- Details -->
                                                    <div>
                                                        <span class="freelancer-detail-item" v-if="item.id_empresa">
                                                            <span><i class="icon-material-outline-business"></i> {{ item.empresa/*nombreEmpresa(item.id_empresa)*/ }}</span>
                                                        </span>
                                                        
                                                        <span class="freelancer-detail-item" v-if="item.id_sucursal">
                                                            <span><i class="icon-feather-square"></i> {{ nombreSucursal(item.id_sucursal) }}</span>
                                                        </span>
                                                        
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
                                                                <strong>RUC:</strong> <span>{{ item.ruc }}</span>{{`    `}}
                                                                <strong class = "margin-left-25">Razon:</strong> <span>{{ item.razon }}</span>
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <!-- Buttons -->
                                                    <div class="buttons-to-right always-visible margin-top-25 margin-bottom-5">

                                                        <a href="#" class="button gray ripple-effect ico ico-text detail" :title="item.status == 2 ? 'Habilitar' : 'Inhabilitar'" @click.prevent="inhabilitarItem(item)">
                                                            <i :class="item.status == 2 ? 'icon-feather-check-circle' : 'icon-feather-slash'"></i> <span>{{ item.status == 2 ? 'Habilitar' : 'Inhabilitar' }}</span>
                                                        </a>

                                                        <a href="#" class="button gray ripple-effect ico" title="Editar" @click="editarItem(item)"> <!-- data-tippy-placement="top" -->
                                                            <i class="icon-feather-edit"></i>
                                                        </a>

                                                        <a href="javascript:" class="button gray ripple-effect ico" title="Eliminar" @click="removerItem(item)"> <!-- data-tippy-placement="top" -->
                                                            <i class="icon-feather-trash-2"></i>
                                                        </a>
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

                                <li v-if="false">
                                    <!-- Overview -->
                                    <div class="freelancer-overview manage-candidates">
                                        <div class="freelancer-overview-inner">

                                            <!-- Avatar -->
                                            <div class="freelancer-avatar">
                                                <a href="#"><img src="images/user-avatar-placeholder.png" alt=""></a>
                                            </div>

                                            <!-- Name -->
                                            <div class="freelancer-name">
                                                <h4><a href="#">Sebastiano Piccio <img class="flag" src="images/flags/it.svg" alt="" title="Italy" data-tippy-placement="top"></a></h4>


                                                <!-- Details -->
                                                <span class="freelancer-detail-item"><a href="#"><i class="icon-feather-mail"></i> sebastiano@example.com</a></span>
                                                <span class="freelancer-detail-item"><i class="icon-feather-phone"></i> (+39) 123-456-789</span>

                                                <!-- Rating -->
                                                <br>
                                                <span class="company-not-rated">Minimum of 3 votes required</span>

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

                                <li v-if="false">
                                    <!-- Overview -->
                                    <div class="freelancer-overview manage-candidates">
                                        <div class="freelancer-overview-inner">

                                            <!-- Avatar -->
                                            <div class="freelancer-avatar">
                                                <a href="#"><img src="images/user-avatar-placeholder.png" alt=""></a>
                                            </div>

                                            <!-- Name -->
                                            <div class="freelancer-name">
                                                <h4><a href="#">Nikolay Azarov <img class="flag" src="images/flags/ru.svg" alt="" title="Russia" data-tippy-placement="top"></a></h4>

                                                <!-- Details -->
                                                <span class="freelancer-detail-item"><a href="#"><i class="icon-feather-mail"></i> nikolay@example.com</a></span>
                                                <span class="freelancer-detail-item"><i class="icon-feather-phone"></i> (+7) 123-456-789</span>

                                                <!-- Rating -->
                                                <br>
                                                <span class="company-not-rated">Minimum of 3 votes required</span>

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
            />
        </section>

        <section class="form" v-if="vista_formulario">
            <!-- Dashboard Headline -->
            <div class="dashboard-headline">
                <h3>
                    <button type="button" class="button" @click="ocultarFormulario">
                        <i class="icon-material-outline-arrow-back"></i>
                    </button>
                    &nbsp;
                    {{ item.id > 0 ? 'Modificar empresa' : 'Agregar empresa' }}
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
                                <h3><i class="icon-feather-folder-plus"></i> Agregar empresa</h3>
                            </div>

                            <!--Formulario de empresa-->
                            <div class="content with-padding padding-bottom-10">

                                <!--Datos de empresa-->

                                <div class="message-time-sign">
                                    <span>Datos de empresa</span>
                                </div>

                                <div class="row">
                                    <div class="col-xl-12">
                                        <input-seleccion
                                            v-model="item.id_cliente"
                                            nombre="id_cliente"
                                            etiqueta="Cliente"
                                            :items="clientes"
                                        />
                                    </div>
                                </div>

                                    <!--Imagen y nombre-->
                                    <div class="row">
                                        <div class="col-xl-12">
                                            <input-texto
                                                v-model="item.nombre"
                                                nombre="nombre"
                                                etiqueta="Nombre de la empresa"
                                            />
                                        </div>
                                    </div>

                                    <!--Ruc y razon-->
                                    <div class="row">
                                        <div class="col-xl-6">
                                            <input-texto
                                                v-model="item.ruc"
                                                nombre="ruc"
                                                etiqueta="RUC"
                                            />
                                        </div>
                                        <div class="col-xl-6">
                                            <input-texto
                                                v-model="item.razon"
                                                nombre="razon"
                                                etiqueta="Razon"
                                            />
                                        </div>
                                    </div>
                            </div>
                        </div>
                    </div>

                        <input type="hidden" name="_fuente" :value="fuente">
                        <input type="hidden" name="id" v-model="item.id">
                        <input type="hidden" name="id_persona" v-model="item.id_persona">

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

    </main>
</template>

<script>
    window.Vue = require('vue');

    export default {
        name: "ClientePersona.vue",
        components: {},


        props: {
            urls: Object,
            avatar_defecto: String,
        },

        data: () => ({

            fuente: 'IntranetEmpresa',
            clientes: [],
            items: [],

            //Formulario Item
            item: {
                id: 0,
                id_cliente: '',
                nombre: '',
                razon: '',
                ruc: '',
                pasaporte: '',
                primer_nombre: '',
                segund_nombre: '',
                primer_apellido: '',
                segundo_apellido: '',
                dni: '',
                nacionalidad: 'nicaraguense',
                genero: null,
                fecha_nacimiento: '',
                foto: '',
            },
            vista_items: true,
            vista_formulario: false,
            vista_impresion_carnet: false,
            vista_permisos: false,
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
                        this.clientes = data["clientes"];
                    }
                })
                .catch(err => {
                    self.debugError(err);
                });
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
                this.item.id_cliente = 0;
                this.item.nombre = '';
                this.nombre = '';
                this.razon = '';
                this.ruc = '';
                this.pasaporte= '';
                this.primer_nombre= '';
                this.segund_nombre= '';
                this.primer_apellido='';
                this.segundo_apellido= '';
                this.dni= '';
                this.nacionalidad= 'nicaraguense';
                this.genero= null;
                this.fecha_nacimiento= '';
                this.foto= '';
            },

            guardar() {
                this.statusGuardando(true);

                const params = {
                    _fuente: this.fuente,
                };
                //const frm = this.$refs.form;
                //const inputs = new FormData($(frm)[0]);
                //const inputs = $(frm).serialize();

                const form_data = new FormData(this.$refs['form']);
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
                        this.debugStuff({response})
                        this.debugForm(form_data)
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
                })
                .catch( err => {
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
                                this.debugStuff({response})
                                if (response.status === 200) {
                                    const data = response.data;

                                    this.item = {...data}

                                    this.mostrarFormulario();
                                }
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

            nombreEmpresa(item) {
                return item.nombre;
            },

            iconoStatus(item) {
                if (item.status == 2) {
                    return `<i class="icon-feather-slash"></i> &nbsp;&nbsp;`;
                }
                return '';
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
    .item-inhabilitado {
        opacity: .6;
    }
</style>