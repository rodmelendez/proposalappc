<template>
    <div class="submit-field radio-list">
        <h5 v-if="typeof etiqueta === 'string'">{{ etiqueta }}</h5>
        <div class="radio" v-for="item in items" @click="actualizarValor(item.id)">
            <input type="radio" :id="'r_' + nombre + item.id + _uid" :name="nombre + (arreglo ? '[]' : '')" :checked="item.id == value" :value="item.id">
            <label :for="'r_' + _uid"><span class="radio-label"></span> {{ item.nombre }}</label>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Radios.vue",

        props: {
            etiqueta: String,
            nombre: {
                type: String,
                required: true,
            },
            value: {
                //type: String,
                required: true
            },
            items: {
                type: Array,
                required: true,
            },
            arreglo: {
                type: Boolean,
                default: false,
            }
        },

        watch: {
            value() {
                this.$emit('input', this.value);
            }
        },

        methods: {
            actualizarValor(valor) {
                this.$emit('input', valor);
            },
        }

    }
</script>

<style scoped>
    .radio {
        margin-right: 20px;
    }
</style>