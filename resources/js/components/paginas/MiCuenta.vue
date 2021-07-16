<template>
    <section>
        <!-- Dashboard Headline -->
        <div class="dashboard-headline">
            <h3>
                <!--<button type="button" class="button" @click="ocultarFormulario">
                    <i class="icon-material-outline-arrow-back"></i>
                </button>-->
                &nbsp;
                Mi Cuenta
            </h3>
        </div>

        <form ref="form">
            <!-- Row -->
            <div class="row">

                <!-- Dashboard Box -->
                <div class="col-xl-12">
                    <div class="dashboard-box margin-top-0">

                        <!-- Headline -->
                        <div class="headline">
                            <h3><i class="icon-line-awesome-key"></i> Cambiar mi contraseña</h3>
                        </div>

                        <div class="content with-padding padding-bottom-10">
                            <div class="row">
                                <div class="col-xl-4">
                                    <input-contrasena
                                            v-model="item.contrasena_actual"
                                            nombre="contrasena_actual"
                                            etiqueta="Contraseña actual"
                                    ></input-contrasena>
                                </div>

                                <div class="col-xl-4">
                                    <input-contrasena
                                            v-model="item.contrasena_nueva"
                                            nombre="contrasena_nueva"
                                            etiqueta="Contraseña nueva"
                                    ></input-contrasena>
                                </div>

                                <div class="col-xl-4">
                                    <input-contrasena
                                            v-model="item.contrasena_nueva_confirmar"
                                            nombre="contrasena_nueva_confirmar"
                                            etiqueta="Confirmar contraseña nueva"
                                    ></input-contrasena>
                                </div>
                            </div>

                            <div>
                                <input type="hidden" name="_fuente" value="User">
                                <input type="hidden" name="_accion" value="cambiarContrasena">

                                <a href="#" class="button ripple-effect button-sliding-icon big" @click="guardar">
                                    <span class="item-guardar">Guardar <i class="icon-feather-save"></i></span>
                                    <span class="item-guardando">Guardando... <i class="icon-line-awesome-hourglass-2 fa-spin"></i></span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- Row / End -->
        </form>

        <form ref="form_personalizacion" class="margin-top-30">
            <!-- Row -->
            <div class="row">

                <!-- Dashboard Box -->
                <div class="col-xl-12">
                    <div class="dashboard-box margin-top-0">

                        <!-- Headline -->
                        <div class="headline">
                            <h3><i class="icon-line-awesome-paint-brush"></i> Personalizar</h3>
                        </div>

                        <div class="content with-padding padding-bottom-10">
                            <div class="row">
                                <div class="col-xl-4">
                                    <input-color
                                            v-model="personalizar.color_fondo"
                                            nombre="color_fondo"
                                            etiqueta="Color de fondo"
                                    ></input-color>
                                </div>
                            </div>

                            <div>
                                <input type="hidden" name="_fuente" value="User">
                                <input type="hidden" name="_accion" value="personalizar">

                                <a href="#" class="button ripple-effect button-sliding-icon big" @click="guardarPersonalizacion">
                                    <span class="item-guardar">Guardar <i class="icon-feather-save"></i></span>
                                    <span class="item-guardando">Guardando... <i class="icon-line-awesome-hourglass-2 fa-spin"></i></span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- Row / End -->
        </form>
    </section>
</template>

<script>
    export default {
        name: "MiCuenta.vue",

        props: {
            urls: Object,
            avatar_defecto: String,
        },

        data: () => ({
            item: {
                contrasena_actual: '',
                contrasena_nueva: '',
                contrasena_nueva_confirmar: '',
            },
            personalizar: {
                color_fondo: '',
            },
        }),

        methods: {
            limpiarItem() {
                this.item.contrasena_actual = '';
                this.item.contrasena_nueva = '';
                this.item.contrasena_nueva_confirmar = '';
            },

            guardarPersonalizacion() {
                const frm = this.$refs['form_personalizacion'];

                frm.classList.add('guardando');

                this.enviarForm(frm, function(data) {

                    resultadoSolicitudDefecto(data);
                    if (data.ok) {
                        actualizarColorFondo(data.opciones['color_fondo']);
                    }

                    frm.classList.remove('guardando');

                    return false;
                });
            },

            cargarData() {
                this.$http.get(this.urls.get, {
                    params: {
                        _fuente: 'User',
                        _accion: 'cargarData',
                    }
                })
                    .then(response => {
                        if (response.status === 200) {
                            const data = response.data;

                            if (typeof data['color_fondo'] === 'string' && data['color_fondo'].length) {
                                this.personalizar.color_fondo = data['color_fondo'];
                            }

                            //this.$forceUpdate();
                        }
                    });
            }
        },

        mounted() {
            $('.header-notifications.user-menu').removeClass('active');

            this.cargarData();
        },

        updated() {

        },
    }
</script>

<style scoped>

</style>