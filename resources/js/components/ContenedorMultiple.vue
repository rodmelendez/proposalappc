<template>
    <div class="contenedor submit-field margin-bottom-0">
        <h5>{{ etiqueta || '' }} {{ actual }}</h5>
        <transition v-for="n in total" :key="n"
                    appear
                    :enter-active-class="'animated ' + (direccion === 'L' ? 'fadeInLeft' : 'fadeInRight')"
        >
            <div v-show="n === actual">
                <slot :name="'contenido' + n"></slot>
            </div>
        </transition>
        <nav class="tabs-nav">
            <span class="tab-prev" @click="anterior"><i class="icon-material-outline-keyboard-arrow-left"></i></span>
            <span class="tab-next" @click="siguiente"><i class="icon-material-outline-keyboard-arrow-right"></i></span>
        </nav>
    </div>
</template>

<script>
    export default {
        name: "ContenedorMultiple.vue",

        props: {
            etiqueta: String,
            total: {
                type: Number,
                default: 1,
            },
            /*actual: {
                type: Number,
                default: 1,
            }*/
        },

        data: () => ({
            actual: 1,
            direccion: null,
        }),

        methods: {
            anterior() {
                if (this.actual > 1) {
                    this.direccion = 'L';
                    this.actual--;
                }
            },

            siguiente() {
                if (this.actual < this.total) {
                    this.direccion = 'R';
                    this.actual++;
                }
            }
        }
    }
</script>

<style scoped>
    .contenedor {
        position: relative;
        overflow: hidden;
    }

    .tabs-nav {
        top: 0;
        color: #000;
        align-items: normal;
        padding-right: 0;
    }
</style>