<template>
    <div class="submit-field feedback-yes-no margin-top-0">
        <h5>{{ etiqueta }}</h5>
        <div class="radio" v-for="item in items" v-bind:key ="item.id">
            <input 
                type="radio" 
                :id="nombre + item.id" 
                :name="nombre" 
                :value="accesor ? item[accesor] :item.id" 
                :checked="item.id == value" 
                @change="actualizarValor(item)">
            <label :for="nombre + item.id">
                <span class="radio-label"></span> {{ item.nombre }}
            </label>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Opcion.vue",

        props: {
            etiqueta: String,
            nombre: String,
            items: Array,
            value: {
                //type: String,
                required: true,
            },
            accesor: String
        },

        data: () => ({
            /*nombre,
            items: [],
            items_seleccionados: [],*/
        }),

        mounted() {
            //$(this.$refs[this.nombre]).selectpicker();
        },

        updated() {
            //$(this.$refs[this.nombre]).selectpicker('val', this.items_seleccionados);
        },

        watch: {
            value() {
                this.$emit('input', this.value);
            }
        },

        methods: {
            actualizarValor(item) {
                this.$emit('input', item.id);
            }
        }
    }
</script>

<style scoped>

</style>