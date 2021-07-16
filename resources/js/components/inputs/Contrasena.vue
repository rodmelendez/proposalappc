<template>
    <div class="submit-field">
        <h5>{{ etiqueta }}</h5>
        <div class="input-with-icon">
            <input :type="tipo" :name="nombre" class="with-border" :placeholder="etiqueta" :ref="nombre" :value="value || ''" @input="actualizarValor">
            <i class="btn-cambiar-vista" :class="tipo === 'password' ? 'icon-feather-eye' : 'icon-feather-eye-off'" @click="alternarVista"></i>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Contrasena.vue",

        props: {
            nombre: String,
            etiqueta: String,
            value: {
                //type: String,
                required: true
            },
        },

        data: () => ({
            tipo: 'password',
        }),

        watch: {
            value() {
                this.$emit('input', this.value);
            }
        },

        methods: {
            actualizarValor() {
                this.$emit('input', this.$refs[this.nombre].value);
            },

            alternarVista() {
                this.tipo = this.tipo === 'text' ? 'password' : 'text';
            }
        }
    }
</script>

<style scoped>
    .btn-cambiar-vista {
        cursor: pointer;
        pointer-events: all !important;
    }
</style>