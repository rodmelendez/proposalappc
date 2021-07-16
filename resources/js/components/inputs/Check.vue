<template>
    <div class="submit-field switches-list">
        <div class="switch-container">
            <label class="switch">
                <input type="checkbox" :name="nombre" :checked="!!value" :ref="nombre" @change="actualizarValor">
                <span class="switch-button"></span> {{ etiqueta || '' }}
            </label>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Check.vue",

        props: {
            etiqueta: String,
            nombre: {
                type: String,
                required: true,
            },
            value: {
                //type: String,
                required: true
            }
        },

        watch: {
            value() {
                this.$emit('input', this.value);
            }
        },

        methods: {
            actualizarValor() {
                this.$emit('input', $(this.$refs[this.nombre]).is(':checked'));
            },
        }

    }
</script>

<style scoped>
    .switches-list {
        margin-top: 50px;
    }
</style>