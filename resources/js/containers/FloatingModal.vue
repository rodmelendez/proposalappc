<template>
    
    <div
        v-show="showBall"
        v-if="showBall"
         class="modal-backdrop"
        :class="`modal-backdrop-${ isOpen ? 'up' : 'down' }`"
    >
        <div 
            class="ball-modal position-bottom-right"
            :class="isOpen ? 'ball-up' : 'ball-down' "
            @click="toggleModal"
        >

            <div class="content" :data-title="title">

                <div class="body" @click.stop>
                    <slot name="body"></slot>
                </div>

            </div>

        </div>
    </div>

</template>

<script>
export default {
    name: "FloatingModal.vue",

    props: {
        isOpen: {
            type: Boolean,
            default: false
        },
        showBall: {
            type: Boolean,
            default: false
        },
        title: String
    },

    methods: {

        //Cambia la visibilidad del modal
        toggleModal(event){

            this.$emit("toggleModal", event );
        }

    },

    mounted() {

        //this.debugStuff(this.$el, "hotpink", "Elemento");

        const modal = this.$el;

        this.$el.parentNode.removeChild(this.$el);

        document.getElementById("app").appendChild(modal);

        /*const dialog = this.$refs['dialogo'];

        const self = this;

        new DialogFx(dialog, {
            onOpenDialog : function(/!*instance*!/) {
                //container.classList.add('container--move');
                self.$emit('input', true);
                self.$emit('modalAbierto');
            },
            onCloseDialog : function(/!*instance*!/) {
                //container.classList.remove('container--move');
                self.$emit('input', false);
                self.$emit('modalCerrado');
            }
        });*/
    },

    beforeDestroy(){

        document.getElementById("app").removeChild(this.$el)

    }

}
</script>

<style scoped>

    .ball-modal{

        position: fixed;
        background: url(https://source.unsplash.com/random/600x600), linear-gradient(45deg, #bf35c5, #62e5f5);
        background-size: cover;
        background-repeat: no-repeat;
        background-blend-mode: overlay;
        width: 50px;
        height: 50px;
        border-radius: 100px;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        transform-style: preserve-3d;
        --offset: 30px;
        z-index: 999999;

    }

    .modal-backdrop::before{
        content:"";
        width: 0;
        height: 0;
        position: fixed;
        top:0;
        left: 0;
        opacity: 0;
        background-color: rgba(0, 0, 0, 0.3);
        transition: height 0.6s ease-in-out 0.6s, width 0s linear 0.6s, opacity 0.4s ease-out 0.6s;
        z-index: 9999;
    }

    .modal-backdrop-down::before {


    }

    .content{

        position: relative;
        height: 0%;
        max-height: 60%;
        transition: height 0.8s cubic-bezier(.05,.72,.42,1);
        background-color: #FFF;
        box-sizing: border-box;
        box-shadow: inset 0px 15px 10px -10px rgba(0, 0, 0, 0.1);
        border-bottom-right-radius: 5px;
        border-bottom-left-radius: 5px;

    }
    
    .content::before{
        content: attr(data-title);
        position: absolute;
        bottom: calc(100% + 15px);
        left: 15px;
        font-size: 1.6rem;
        font-weight: bold;
        color: #FFF;
        width: calc(60% - 30px);
        pointer-events: none;
        opacity: 0;
    }

    .content .body{

        display: inline-block;
        padding: 1.5rem;
        opacity: 0;
        max-height: 100%;
        overflow-y: auto;
        color: #333;
        width: 100%;
        height: 100%;
        overflow-y: hidden;

    }

    .content .body h1 {
        color: darken(#42a5f5, 5%);

    }

    .ball-up {
        /*top: 30px;
        left: var(--offset);
        /*transform: translateX(-50%);
        width: calc(100% - 60px);*/
        border-radius: 5px;
        height: calc(100% - 60px);
        transition: width 0.7s cubic-bezier(.77,.07,.53,1), border-radius 0.5s cubic-bezier(.05,.72,.42,1), height 0.7s cubic-bezier(.77,.07,.53,1), top 0.8s cubic-bezier(.05,.72,.42,1), left 0.5s ease-out, box-shadow 0.3s linear;
        z-index: 9999;
        width: 60%;
        left: 50%;
        transform: translate(-50%, -50%);
        top: 50%;
    }

    .modal-backdrop-up::before{
        width: 100vw;
        height: 100vh;
        opacity: 1;
    }

    .ball-up .content {
        height: 60%;
        /*transition: all 1.4s cubic-bezier(.77,.07,.53,1);*/
        transition: height 0.8s ease-in-out 0.5s;
    }

    .ball-up .content .body{

        opacity: 1;
        transition: opacity 1.2s cubic-bezier(1,.01,1,.01);

    }

    .ball-up::before {

        opacity: 1;
        transition: opacity 0.6s ease-in-out 0.3s;

    }

    .ball-down {
        top: calc(95% - 50px);
        left: calc(95% - 50px);
        transition: all 0.4s ease-out, border-radius 0.5s cubic-bezier(.05,.72,.42,1), top 0.5s ease-out, left 0.5s ease-in, box-shadow 0.8s ease-out;
        cursor: pointer;
        box-shadow: 0px 0px 0px rgba(0, 0, 0, 0);
        transition-delay: 0.4s;
    }

    .ball-down::before{

        /*opacity: 0;*/
        height: 0;
        width: 0;
        top: calc(100% + 60px);
        opacity: 0;
        transition: top 0.4s ease-out 0s, height 0.4s ease-in, width 0s ease-out 0.4s, opacity 0.4s ease-out;
    }
      
    .ball-down .content::before {
          opacity: 0;
          transition: opacity 0.3s ease-out 0.2s;
    }

            
    .ball-down .content .body {
        opacity: 0;
        transition: opacity 0.3s ease-out;
    }
      
    .ball-down:hover  {
        transform: scale(1.2);
    }

    .position-bottom-right{

        bottom: 30px;
        right:  40px;

    }

</style>