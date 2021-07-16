<template>

    <div class="submit-field">
        <h5>{{ etiqueta }}</h5>
        <datepicker 
            :placeholder="placeholder || ''" 
            :name="nombre"
            :ref ="nombre"
            :value="$attrs.value"
            @input="actualizarValor"
            :language="languages[language]"
        />
    </div>
    
</template>

<script>

import Datepicker from "vuejs-datepicker/dist/vuejs-datepicker.esm.js";
import * as lang from "vuejs-datepicker/src/locale";
import moment from 'moment';

export default {

    name: "FechaVue.vue",

    components: {
        Datepicker
    },

    props: {
        nombre: String,
        placeholder: String,
        etiqueta: String,
        formato: {
            type: String,
            default: 'YYYY-MM-DD HH:mm:ss'
        }
    },

    data: () => ({
        language: "es",
        languages: lang,
    }),

    methods: {

        actualizarValor(date){

            //alert("aaaa")
            //console.log({c:this.$refs[this.nombre].value,date})

            this.$emit('input', moment(date).format(this.formato) );

        }


    }
}

</script>