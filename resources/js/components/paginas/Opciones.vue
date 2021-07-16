<template>
    <section>
        <!-- Dashboard Headline -->
        <div class="dashboard-headline">
            <h3>Opciones</h3>
        </div>

        <form ref="form">
            <!-- Row -->
            <div class="row">

                <!-- Dashboard Box -->
                <div class="col-xl-12">
                    <div class="dashboard-box margin-top-0">

                        <!-- Headline -->
                        <div class="headline">
                            <h3><i class="icon-line-awesome-key"></i> Generales</h3>
                        </div>

                        <div class="content with-padding padding-bottom-10">
                            <div class="row">
                                <div class="col-xl-4">
                                    <input-color
                                            v-model="color_fondo"
                                            name="color_fondo"
                                            etiqueta="Color de fondo"
                                    ></input-color>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="_fuente" value="User">
                <input type="hidden" name="_accion" value="cambiarContrasena">

                <div class="col-xl-12">
                    <a href="#" class="button ripple-effect button-sliding-icon big margin-top-30" @click="guardar">
                        Guardar
                        <i class="icon-feather-save"></i>
                    </a>
                </div>

            </div>
            <!-- Row / End -->
        </form>
    </section>
</template>

<script>
    export default {
        name: "Opciones.vue",

        props: {
            urls: Object,
            avatar_defecto: String,
        },

        data: () => ({
            color_fondo: '',
        }),

        methods: {

        },

        mounted() {
            //$('.header-notifications.user-menu').removeClass('active');
        },

        updated() {

        },
    }
</script>

<style scoped>

</style>