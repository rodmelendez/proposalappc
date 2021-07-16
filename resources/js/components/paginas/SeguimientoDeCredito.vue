<template>
    
    <slider>
        <slide v-for="i in 5" :key="i" :title="i.toString()" />
    </slider>

</template>

<script>

    window.Vue = require('vue');


    export default {
        name: 'SeguimientoCredito.vue'
    }


</script>