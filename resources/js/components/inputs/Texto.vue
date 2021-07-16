<template>
    <div class="submit-field">
        <h5 v-if="typeof etiqueta === 'string' && etiqueta.length">{{ etiqueta }}</h5>
        <input 
            :type="tipo" 
            :name="nombre" 
            class="with-border" 
            :placeholder="(etiqueta || placeholder) || ''" 
            :ref="nombre" 
            :disabled="disabled"
            :value="value || ''" @input="actualizarValor"><!-- @keyup.enter="onEnter($event)"-->
    </div>
</template>

<script>
    export default {
        name: "Texto.vue",

        props: {
            nombre: String,
            etiqueta: {
                default: false,
            },
            placeholder: {
                type: String,
            },
            tipo: {
                type: String,
                default: 'text'
            },
            value: {
                //type: String,
                required: true
            },
            disabled: {
                type: Boolean,
                default: false
            }
        },

        /*data: () => ({

        }),*/

        watch: {
            value() {
                this.$emit('input', this.value);
            }
        },

        methods: {
            actualizarValor() {
                this.$emit('input', this.$refs[this.nombre].value);
            },

            /*onEnter(e) {
                e.preventDefault();
                e.stopPropagation();
                this.$emit('enter');
                return false;
            }*/
        }
    }
</script>

<style scoped>

</style>