<template>
    <div class="submit-field">
        <h5>{{ etiqueta }}</h5>
        <input type="text" :name="nombre" class="with-border" :placeholder="etiqueta" :ref="nombre" :value="value" @input="actualizarValor">
    </div>
</template>

<script>
    export default {
        name: "Hora.vue",

        props: {
            nombre: String,
            etiqueta: String,
            value: {
                type: String,
                required: true
            },
            formato: {
                type: String,
                default: 'HH:ii p',
            },
        },

        /*data: () => ({

        }),*/

        watch: {
            value() {
                this.$emit('input', this.value);
            }
        },

        mounted() {
            const $el = $(this.$refs[this.nombre]);

            $el.datetimepicker({
                /*format: this.formato,
                //weekStart: 1,
                language: 'es',
                autoclose: true,
                todayHighlight: true,
                startView: 'hour',*/
                format: 'hh:mm a',
                locale: 'es',
                icons: {
                    time: 'far fa-clock',
                    date: 'far fa-calendar',
                    up: 'fa fa-chevron-up',
                    down: 'fa fa-chevron-down',
                    previous: 'fa fa-chevron-left',
                    next: 'fa fa-chevron-right',
                    today: 'far fa-calendar-check',
                    clear: 'far fa-trash-alt',
                    close: 'fa fa-times'
                },
            });

            const self = this;

            $el.on('dp.change', function() {
                self.actualizarValor();
            });
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