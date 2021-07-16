<template>
    
    <a v-if ="can_save" 
        href="#" 
        class="button ripple-effect button-sliding-icon big margin-top-30"
        @click="handleSave"
    >
        <span class="item-guardar">Guardar <i class="icon-feather-save"></i></span>
        <span class="item-guardando">Guardando... <i class="icon-line-awesome-hourglass-2 fa-spin"></i></span>
    </a>

</template>

<script>
export default {
    name: "SubmitButton.vue",
    
    props: {
        
        can_save: {
            type: Boolean,
            default: true
        },

        on_submit: {
            type: Function
        }

    },

    data: () => ({

    }),

    methods: {

        handleSave(event){

            //Si existe la funcion on_submit
            if(typeof this.on_submit === 'function' && this.on_submit ){
                this.on_submit(event)
                return;
            }   

            this.$emit("submit",event);
            //this.guardar();

        }

    }

}
</script>