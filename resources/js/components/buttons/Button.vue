<template>
    
    <a
        href="#" 
        class="button ripple-effect button-sliding-icon big margin-top-30"
        @click="handleClick"
    >
        <span class="item-guardar"><slot></slot><i class="icon-feather-save"></i></span>
    </a>

</template>

<script>
export default {
    name: "SubmitButton.vue",
    
    data: () => ({

    }),

    methods: {

        handleClick(event){

            this.$emit( "click" , event)

        }

    }

}
</script>