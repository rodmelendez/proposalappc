<template>
    <div class="submit-field">
        <h5 v-if="typeof etiqueta === 'string' && etiqueta.length">{{ etiqueta }}</h5>
        <textarea :name="nombre" class="with-border" :placeholder="(etiqueta || placeholder) || ''" :ref="nombre" :value="value || ''" @input="actualizarValor"><!-- @keyup.enter="onEnter($event)"--></textarea>
    </div>
</template>

<script>
    export default {
        name: "TextoMultilinea.vue",

        props: {
            nombre: String,
            etiqueta: {
                default: false,
            },
            placeholder: {
                type: String,
            },
            value: {
                //type: String,
                required: true
            },
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