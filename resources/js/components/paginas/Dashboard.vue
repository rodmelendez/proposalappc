<template>
    <main>
        <div class="fullwidth-carousel-container">
            <div class="testimonial-carousel testimonials">

                <!-- Item -->
                <div class="fw-carousel-review" v-for="comunicado in comunicados" :key="comunicado.id">
                    <div class="testimonial-box">
                        <div class="testimonial-avatar">
                            <img :src="comunicado.usuario.foto" alt="">
                        </div>
                        <div class="testimonial-author">
                            <h4>{{ comunicado.usuario.nombre }}</h4>
                            <span>{{ fechaComunicado(comunicado.fecha) }}</span>
                        </div>
                        <div class="testimonial">
                            <h3 class="margin-bottom-4 text-left" v-if="comunicado.titulo">{{ comunicado.titulo }}</h3>
                            <div class="row">
                                <div :class="comunicado.imagen ? 'col-xl-6' : 'col-xl-12'">
                                    <div class="contenido" v-html="comunicado.contenido"></div>
                                </div>

                                <div class="col-xl-6" v-if="comunicado.imagen">
                                    <img :src="comunicado.imagen" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="fw-carousel-review" v-if="!comunicados.length">
                    <div class="testimonial-box">
                        <!--div class="testimonial-avatar">
                            <img src="images/user-avatar-small-01.jpg" alt="">
                        </div-->
                        <div class="testimonial-author">
                            <h4>Bienvenido</h4>
                        </div>
                        <div class="testimonial">No hay comunicados aún.</div>
                    </div>
                </div>
            </div>

        </div>

        <div v-if="false">
            <div class="fun-facts-container">
                <div class="fun-fact" data-fun-fact-color="#36bd78">
                    <div class="fun-fact-text">
                        <span>Empleados</span>
                        <h4>{{ total_empleados }}</h4>
                    </div>
                    <div class="fun-fact-icon">
                        <i class="icon-material-outline-group"></i>
                    </div>
                </div>

                <div class="fun-fact" data-fun-fact-color="#b81b7f">
                    <div class="fun-fact-text">
                        <span>Empresas</span>
                        <h4>{{ total_empresas }}</h4>
                    </div>
                    <div class="fun-fact-icon">
                        <i class="icon-material-outline-business"></i>
                    </div>
                </div>

                <div class="fun-fact" data-fun-fact-color="#b81b7f" v-if="false">
                    <div class="fun-fact-text">
                        <span>Jobs Applied</span>
                        <h4>4</h4>
                    </div>
                    <div class="fun-fact-icon" style="background-color: rgba(184, 27, 127, 0.07);"><i class="icon-material-outline-business-center" style="color: rgb(184, 27, 127);"></i></div>
                </div>

                <div class="fun-fact" data-fun-fact-color="#efa80f" v-if="false">
                    <div class="fun-fact-text">
                        <span>Reviews</span>
                        <h4>28</h4>
                    </div>
                    <div class="fun-fact-icon" style="background-color: rgba(239, 168, 15, 0.07);"><i class="icon-material-outline-rate-review" style="color: rgb(239, 168, 15);"></i></div>
                </div>

                <!-- Last one has to be hidden below 1600px, sorry :( -->
                <div class="fun-fact" data-fun-fact-color="#2a41e6" v-if="false">
                    <div class="fun-fact-text">
                        <span>This Month Views</span>
                        <h4>987</h4>
                    </div>
                    <div class="fun-fact-icon" style="background-color: rgba(42, 65, 230, 0.07);"><i class="icon-feather-trending-up" style="color: rgb(42, 65, 230);"></i></div>
                </div>

                <div class="fun-fact"></div>
            </div>
        </div>
    </main>
</template>

<script>
    export default {
        name: "Dashboard.vue",

        props: {
            urls: Object,
            usuario: Object
        },

        data: () => ({
            total_empleados: 0,
            total_empresas: 0,
            comunicados: [],
        }),

        /*watch: {
            comunicados() {

            }
        },*/

        methods: {
            fechaComunicado(fecha) {
                return moment(fecha, 'YYYY-MM-DD HH:mm:ss').format('LL');
            },

            cargarTotales() {
                this.$http.get(this.urls.get, {
                    params: {
                        _fuente: 'App',
                        _accion: 'cargarTotales'
                    }
                })
                .then(response => {
                    if (response.status === 200) {
                        const data = response.data;

                        this.total_empleados = data.total_empleados;
                        this.total_empresas = data.total_empresas;
                    }
                });
            }
        },

        mounted() {
            //this.cargarTotales();

            //se cargan los comunicados
            {
                this.$http.get(this.urls.get, {
                    params: {
                        _fuente: 'Comunicado',
                        _accion: 'cargar',
                    }
                })
                    .then(response => {
                        if (response.status === 200) {
                            this.comunicados = response.data;

                            setTimeout(function() {
                                const $contenedor = $('.testimonial-carousel');

                                if ($contenedor.data('slick')) {
                                    $contenedor.unslick();
                                }

                                $contenedor.slick({
                                    centerMode: true,
                                    centerPadding: '0%',
                                    slidesToShow: 1,
                                    dots: true,
                                    arrows: false,
                                    adaptiveHeight: true,
                                    //infinite: false,
                                });
                            }, 500);
                        }
                    });
            }
        },

        updated() {
            const $items = $('.fun-fact h4');
            if ($items.hasClass('counted')) return;

            $items.counterUp({
                delay: 10,
                time: 800
            });
            $items.addClass('counted');
        }/*,

        beforeRouteEnter (to, from, next) {
            console.log('before entering route (Dashboard.vue)');
            next();
        }*/
    }
</script>

<style scoped>

</style>