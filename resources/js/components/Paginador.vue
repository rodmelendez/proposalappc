<template>
    <div class="pagination-container margin-top-30 margin-bottom-0 d-flex justify-content-center">
        <div class = "d-flex mr-auto">
            <span class = "nav-label">Items por pagina:</span>
            <input-seleccion
                v-model="items_per_page"
                nombre="items_per_page"
                etiqueta=""
                clases="inline"
                :buscador="false"
                :items="items_per_page_options"
                @cambiado="changeItemsPerPage"
            ></input-seleccion>
        </div>
        <nav class="pagination mr-auto">
            <ul>
                <li class="pagination-arrow">
                    <a href="#" class="ripple-effect" @click.prevent="anterior">
                        <i class="icon-material-outline-keyboard-arrow-left"></i>
                    </a>
                </li>
                <template v-if="totalPaginas > 20">
                    <li>
                        <input-seleccion
                            v-model="pagina_actual"
                            nombre="pagina_actual"
                            etiqueta=""
                            clases="inline"
                            :buscador="false"
                            :items="listaPaginas"
                            @cambiado="paginaCambiada"
                        ></input-seleccion>
                    </li>
                </template>
                <template v-else>
                    <li v-for="index in totalPaginas" :key="index">
                        <a href="#" class="ripple-effect" :class="index == pagina_actual ? 'current-page' : ''" @click.prevent="cambiarPagina(index)">{{ index }}</a>
                    </li>
                </template>
                <li class="pagination-arrow">
                    <a href="#" class="ripple-effect" @click.prevent="siguiente">
                        <i class="icon-material-outline-keyboard-arrow-right"></i>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</template>

<script>
    export default {
        name: "Paginador.vue",

        props: {
            total_items: {
                default: 1,
            },
            value: {
                default: 1,
            },
            /*items_por_pagina: {
                default: 10,
            }*/
        },

        data: () => ({
            pagina_actual: 1,
            items_per_page: 20,
            items_per_page_options: [
                {id: 1, nombre: 20},
                {id: 2, nombre: 30},
                {id: 3, nombre: 40}
            ]
        }),

        computed: {
            totalPaginas() {
                return Math.ceil(this.total_items / this.items_por_pagina);
            },

            listaPaginas() {
                const total_paginas = this.totalPaginas;
                let lista = [];

                for (let p = 1; p <= total_paginas; p++) {
                    lista.push({
                        id: p,
                        nombre: p,
                    });
                }

                return lista;
            }
        },

        methods: {
            anterior() {
                if (this.pagina_actual > 1) this.pagina_actual--;
                this.$emit('input', this.pagina_actual);
            },

            siguiente() {
                if (this.pagina_actual < this.totalPaginas) this.pagina_actual++;
                this.$emit('input', this.pagina_actual);
            },

            cambiarPagina(index) {
                this.pagina_actual = index;
                this.$emit('input', this.pagina_actual);
            },

            paginaCambiada(valor) {
                this.$emit('input', valor);
            },

            changeItemsPerPage(value){

                value = this.items_per_page_options.find( x => parseInt(x.id) === parseInt(value) );
                this.items_por_pagina = value.nombre;
                this.$emit('itemschanged', value.nombre);

            }
        },

        mounted() {
            this.pagina_actual = this.value;
        }
    }
</script>

<style scoped>
    .mr-auto{
        margin-right: auto;
    }

    .nav-label{
        padding: 16px;
        margin-top: -20px;
    }
</style>