<template>
    <div class="submit-field">
        <h5>{{ etiqueta }}</h5>
        <input type="text" :name="nombre" :ref="nombre" :value="value || ''" @input="actualizarValor">
    </div>
</template>

<script>
    export default {
        name: "Color.vue",

        props: {
            value: {
                //type: String,
                required: true
            },
            etiqueta: String,
            nombre: {
                type: String,
                required: true
            },
        },

        watch: {
            value() {
                const input = this.$refs[this.nombre];

                if (typeof input.jscolor !== 'undefined') {
                    input.jscolor.fromString(typeof this.value === 'string' && this.value.length ? this.value : 'FFFFFF');
                }

                this.$emit('input', this.value);
            }
        },

        mounted() {
            const input = this.$refs[this.nombre];

            if (typeof input.jscolor === 'undefined') {
                new jscolor(input);
            }
        },

        methods: {
            actualizarValor() {
                this.$emit('input', this.$refs[this.nombre].value);
            }
        }
    }
</script>

<style scoped>

</style>