<template>
    <div class="submit-field">
        <h5>{{ etiqueta }}</h5>
        <div class="input-with-icon-left no-border">
            <i class="simbolo-moneda" @click="cambiarMoneda">{{ moneda.simbolo }}</i>
            <input 
                :type="tipo" 
                :name="nombre" 
                class="with-border-x" 
                :placeholder="etiqueta" 
                :ref="nombre" 
                :value="value || ''" 
                @input="actualizarValor" 
                title=""
            >
            <input type="hidden" :name="nombre + '_id_moneda'" :value="moneda.id">
            <input type="hidden" :name="nombre + '_moneda_iso'" :value="moneda.codigo_iso">
            <input type="hidden" :name="nombre + '_moneda_simbolo'" :value="moneda.simbolo">
        </div>
    </div>
</template>

<script>
    import monedas from '../../monedas.json'
    import {positive} from '../../utils/validators'

    export default {
        name: "Moneda.vue",

        props: {
            nombre: String,
            etiqueta: String,
            tipo: {
                type: String,
                default: 'text'
            },
            value: {
                //type: String,
                required: true
            },
            moneda_seleccionada: Object,
            id_moneda: {
                default: 0,
            },
            /*validations: {
                type: Object,
                default: {
                    [this.nombre]: {
                        required,
                        positive
                    }
                }
            }*/
        },

        data: () => ({
            monedas,
            moneda: {
                id: 0,
                nombre: '',
                simbolo: '',
                codigo_iso: '',
            },
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

            cambiarMoneda() {
                let indice = 0;

                for (const i in this.monedas) {
                    if (this.monedas[i].id === this.moneda.id) {
                        if (i < this.monedas.length - 1) {
                            indice = (parseInt(i) || 0) + 1;
                        }
                    }
                }

                this.moneda = this.monedas[indice];
                this.moneda_seleccionada = this.monedas[indice];

                 this.$emit('cambia_moneda', this.moneda );

            }
        },

        created() {
            let cargada = false;

            if (this.id_moneda) {
                for (const moneda of this.monedas) {
                    if (moneda.id == this.id_moneda) {
                        this.moneda = moneda;
                        this.moneda_seleccionada = moneda;
                        this.$emit('cambia_moneda', this.moneda );
                        cargada = true;
                        break;
                    }
                }
            }

            if (!cargada) {
                this.moneda = this.monedas[0];
                //this.moneda_seleccionada = this.monedas[0];
                this.$emit('cambia_moneda', this.moneda );
            }
        },
    }
</script>

<style scoped>
    .simbolo-moneda {
        font-style: normal;
        font-weight: bold;
        cursor: pointer;
    }
</style>