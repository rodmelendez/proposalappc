<template>
    <main>
        <section class="index" v-show="vista_items">
            <!-- Dashboard Headline -->
            <div class="dashboard-headline">
                <div class="row">
                    <div class="col-sm-8">
                        <h3>Importación</h3>
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
                            <h3><i :class="icono"></i></h3>

                            <div class="actions">
                                <button type="button" class="popup-with-zoom-anim button ripple-effect margin-right-5" v-show="!precargar" @click="guardarPost">
                                    <i class="icon-feather-save"></i>&nbsp; Guardar
                                </button>

                                <button type="button" class="popup-with-zoom-anim button ripple-effect dark" @click="mostrarFormularioNuevo">
                                    <i class="icon-feather-plus"></i> Nuevo
                                </button>
                            </div>
                        </div>

                        <div class="content">
                            <div v-html="resultado"></div>
                        </div>

                        <div class="content" v-if="false">
                            <ul class="dashboard-box-list">

                                <transition v-for="item in items" :key="item.id"
                                            appear
                                            mode="out-in"
                                            enter-active-class="animated fadeIn"
                                            leave-active-class="animated zoomOut"
                                >
                                    <li v-show="esVisible(item)">
                                        <!-- Overview -->
                                        <div class="freelancer-overview manage-candidates">
                                            <div class="freelancer-overview-inner">
                                                <!-- Name -->
                                                <div class="freelancer-name">
                                                    <h4><strong>{{ item.abreviatura }}</strong> - {{ item.nombre }}</h4>

                                                    <!-- Details -->
                                                    <span class="freelancer-detail-item" v-if="item.lista_atributos">
                                                        <span><b>Atributos:</b> {{ item.lista_atributos }}</span>
                                                    </span>

                                                    <!-- Buttons -->
                                                    <div class="buttons-to-right always-visible margin-top-25 margin-bottom-5">
                                                        <a class="button gray ripple-effect ico" title="Editar" @click="editarItem(item)"> <!-- data-tippy-placement="top" -->
                                                            <i class="icon-feather-edit"></i>
                                                        </a>
                                                        <a class="button gray ripple-effect ico" title="Eliminar" @click="removerItem(item)"> <!-- data-tippy-placement="top" -->
                                                            <i class="icon-feather-trash-2"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </transition>

                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-xl-12" v-show="!precargar">
                    <button type="button" class="button ripple-effect button-sliding-icon big margin-top-30" @click="guardarPost">
                        Guardar
                        <i class="icon-feather-save"></i>
                    </button>
                </div>

            </div>
        </section>

        <section class="form" v-show="vista_formulario">
            <!-- Dashboard Headline -->
            <div class="dashboard-headline">
                <h3>
                    <button type="button" class="button" @click="ocultarFormulario">
                        <i class="icon-material-outline-arrow-back"></i>
                    </button>
                    &nbsp;
                    Importar
                </h3>
            </div>

            <form ref="form">
                <!-- Row -->
                <div class="row">

                    <!-- Dashboard Box -->
                    <div class="col-xl-12">
                        <div class="dashboard-box margin-top-0">

                            <div class="content with-padding padding-bottom-10">
                                <!--<input-seleccion
                                        v-model="destino"
                                        nombre="destino"
                                        etiqueta="Destino"
                                        :items="destinos"
                                ></input-seleccion>-->
                              <input type="hidden" name="destino" value="simicro_credito">

                                <input-archivo
                                        v-model="archivo"
                                        nombre="archivo"
                                        etiqueta="Archivo"
                                ></input-archivo>

                                <input-check
                                        v-model="vaciar"
                                        nombre="vaciar"
                                        etiqueta="Borrar datos existentes"
                                ></input-check>

                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="_fuente" :value="fuente">
                    <input type="hidden" name="_accion" :value="accion">
                    <input type="hidden" name="precargar" :value="precargar ? 1 : 0">

                    <div class="col-xl-12">
                        <button type="button" class="button ripple-effect button-sliding-icon big margin-top-30" @click="guardarPost">
                            Precargar
                            <i class="icon-feather-eye"></i>
                        </button>
                    </div>

                </div>
                <!-- Row / End -->
            </form>
        </section>
    </main>
</template>

<script>
    export default {
        name: "Importar",

        props: {
            urls: Object,
            avatar_defecto: String,
        },

        data: () => ({
            fuente: 'App',
            accion: 'importar',
            icono: 'icon-feather-upload',
            vista_items: false,
            vista_formulario: true,
            destino: '',
            archivo: '',
            precargar: true,
            vaciar: false,
            resultado: '',
            destinos: [
                /*{
                    id: 'marca',
                    nombre: 'Marcas',
                },
                {
                    id: 'modelo_producto',
                    nombre: 'Modelos',
                },
                {
                    id: 'categoria',
                    nombre: 'Categorías',
                },
                {
                    id: 'ubicacion',
                    nombre: 'Ubicaciones',
                },
                {
                    id: 'tipo_producto',
                    nombre: 'Tipos de Productos',
                },
                {
                    id: 'producto',
                    nombre: 'Productos',
                },
                {
                    id: 'empleado',
                    nombre: 'Empleados',
                },*/
                {
                    id: 'simicro_credito',
                    nombre: 'EZA Digital - Simicro Crédito',
                },
            ]
        }),

        methods: {
            limpiarItem() {
                this.precargar = true;
                this.vaciar = false;
                this.resultado = '';
            },

            cargarData() {},

            procesarData(data) {
                this.precargar = !data.precargar;
                this.resultado = data.resultado;
                this.vista_formulario = false;
                this.vista_items = true;
                this.statusGuardando(false);
                return false;
            },

            guardarPost() {
                this.guardar();
            }
        },

        mounted() {

        },
    }
</script>

<style scoped>
    .content {
        overflow-x: auto;
    }
</style>