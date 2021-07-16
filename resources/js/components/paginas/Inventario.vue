<template>
    <main>
        <section class="index" v-if="vista_items">
            <!-- Dashboard Headline -->
            <div class="dashboard-headline">
                <div class="row">
                    <div class="col-sm-8">
                        <h3>Inventario</h3>
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
                            <h3><i class="icon-material-outline-fingerprint"></i> {{ items.length }} {{ items.length === 1 ? 'item' : 'items' }}</h3>

                            <div class="actions">
                                <button type="button" class="popup-with-zoom-anim button dark ripple-effect" @click="mostrarFormularioNuevo">
                                    <i class="icon-feather-plus"></i> Nuevo
                                </button>
                            </div>
                        </div>

                        <div class="content">
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

                                                <!-- Avatar -->
                                                <div class="freelancer-avatar">
                                                    <!--div class="verified-badge"></div-->
                                                    <a href="#"><img :src="avatarUrl(item)" alt=""></a>
                                                </div>

                                                <!-- Name -->
                                                <div class="freelancer-name">
                                                    <h4>{{ item.nombre || item.tipo }} {{ item.marca }} <mark class="color" v-show="item.cantidad > 1">{{ item.cantidad }}</mark></h4>

                                                    <!-- Details -->
                                                    <span class="freelancer-detail-item" v-if="item.tipo && !item.nombre">
                                                        <span><b>Tipo:</b> {{ item.tipo }}</span>
                                                    </span>

                                                    <span class="freelancer-detail-item" v-if="item.modelo">
                                                        <span><b>Modelo:</b> {{ item.modelo }}</span>
                                                    </span>

                                                    <span class="freelancer-detail-item" v-if="item.serie">
                                                        <span><b>Num. de Serie:</b> {{ item.serie }}</span>
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

            </div>
        </section>

        <section class="form" v-if="vista_formulario">
            <!-- Dashboard Headline -->
            <div class="dashboard-headline">
                <h3>
                    <button type="button" class="button" @click="ocultarFormulario">
                        <i class="icon-material-outline-arrow-back"></i>
                    </button>
                    &nbsp;
                    {{ item.id > 0 ? 'Modificar item' : 'Agregar item' }}
                </h3>
            </div>

            <form ref="form">
                <!-- Row -->
                <div class="row">

                    <!-- Dashboard Box -->
                    <div class="col-xl-12">
                        <div class="dashboard-box margin-top-0">

                            <div class="content with-padding padding-bottom-10">
                                <div class="row">
                                    <div class="col-xl-6">
                                        <input-texto
                                                v-model="item.tipo"
                                                nombre="tipo"
                                                etiqueta="Tipo"
                                        ></input-texto>

                                        <input-texto
                                                v-model="item.marca"
                                                nombre="marca"
                                                etiqueta="Marca"
                                        ></input-texto>

                                        <input-texto
                                                v-model="item.modelo"
                                                nombre="modelo"
                                                etiqueta="Modelo"
                                        ></input-texto>

                                        <input-texto
                                                v-model="item.serie"
                                                nombre="serie"
                                                etiqueta="Num. de serie"
                                        ></input-texto>
                                    </div>

                                    <div class="col-xl-6">
                                        <input-imagen
                                                v-model="item.foto"
                                                nombre="foto"
                                                etiqueta="Foto"
                                                altura="380"
                                                ref="foto"
                                        ></input-imagen>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-xl-5">
                                        <input-texto
                                                v-model="item.color"
                                                nombre="color"
                                                etiqueta="Color"
                                        ></input-texto>
                                    </div>

                                    <div class="col-xl-5">
                                        <input-texto
                                                v-model="item.estado"
                                                nombre="estado"
                                                etiqueta="Estado"
                                        ></input-texto>
                                    </div>

                                    <div class="col-xl-2">
                                        <input-texto
                                                v-model="item.cantidad"
                                                nombre="cantidad"
                                                etiqueta="Cantidad"
                                        ></input-texto>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-xl-6">
                                        <input-texto
                                                v-model="item.ubicacion"
                                                nombre="ubicacion"
                                                etiqueta="Ubicacion"
                                        ></input-texto>
                                    </div>

                                    <div class="col-xl-6">
                                        <input-texto
                                                v-model="item.responsable"
                                                nombre="responsable"
                                                etiqueta="Responsable"
                                        ></input-texto>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="_fuente" :value="fuente">
                    <input type="hidden" name="id" v-model="item.id">

                    <div class="col-xl-12">
                        <a href="#" class="button ripple-effect big margin-top-30" @click="guardar">
                            <i class="icon-feather-plus"></i> Guardar
                        </a>
                    </div>

                </div>
                <!-- Row / End -->
            </form>
        </section>
    </main>
</template>

<script>
    //const TXT_CONFIRMAR_ELIMINAR = '¿Está seguro que quiere eliminar el item?';

    export default {
        name: "Inventario",

        props: {
            urls: Object,
            avatar_defecto: String,
        },

        data: () => ({
            fuente: 'Inventario',
            items: [],
            item: {
                id: 0,
                nombre: '',
                tipo: '',
                marca: '',
                modelo: '',
                serie: '',
                color: '',
                estado: '',
                responsable: '',
                ubicacion: '',
                cantidad: '',
                foto: '',
            },
            vista_items: true,
            vista_formulario: false,
            texto_buscado: '',
            propiedades_buscadas: [
                'nombre',
                'tipo',
                'marca',
                'modelo',
                'serie',
                'responsable',
                'ubicacion',
            ],
            texto_confirmar_eliminar: '¿Está seguro que quiere eliminar el item?',
        }),

        methods: {
            limpiarItem() {
                this.item.id = 0;
                this.item.nombre = '';
                this.item.tipo = '';
                this.item.marca = '';
                this.item.modelo = '';
                this.item.serie = '';
                this.item.color = '';
                this.item.estado = '';
                this.item.responsable = '';
                this.item.ubicacion = '';
                this.item.cantidad = '';
                this.item.foto = '';
            },

            setItemData(data) {
                this.item.id = data.id;
                this.item.nombre = data.nombre;
                this.item.tipo = data.tipo;
                this.item.marca = data.marca;
                this.item.modelo = data.modelo;
                this.item.serie = data.serie;
                this.item.color = data.color;
                this.item.estado = data.estado;
                this.item.responsable = data.responsable;
                this.item.ubicacion = data.ubicacion;
                this.item.cantidad = data.cantidad;
                this.item.foto = data.foto;
            },

            avatarUrl(item) {
                if (typeof item.foto === 'string' && item.foto.length) {
                    return this.$uploads_img_dir + item.foto; //Vue.prototype.$uploads_img_dir
                }
                return this.avatar_defecto;
            },
        },

        mounted() {
            this.cargarData();
        },

        updated: function () {
            //this.$nextTick(function () {
            /*initTooltips();
            initKeywords();*/
            //})
        }
    }
</script>

<style scoped>

</style>