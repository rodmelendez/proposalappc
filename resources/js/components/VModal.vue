<template>
    <transition name="fade">

        <div class="modal-backdrop" v-show="isModalOpen">
            
            <div class="modal" :class="`modal-${size}`" role="dialog">
                <div class="modal-guts">
                    <header class="modal-header" v-if="mostrar_cabecera">
                        <slot name="header">
                            &nbsp;
                        </slot>

                        <button type="button" class="btn-close" @click="close" >x</button>
                    </header>

                    <section class="modal-body">
                        <slot name = "body">
                            
                        </slot>
                    </section>

                    <footer class="modal-footer" v-if="mostrar_pie">
                        <slot name="footer">
                            <button class="button button-ok ripple-effect button-sliding-icon big" @click="ok">
                                OK <i class="icon-feather-check"></i>
                            </button>
                        </slot>
                    </footer>
                </div>
            </div>

        </div>
    </transition>

    <!--<div class="dialog" :class="value ? 'dialog&#45;&#45;open' : ''" ref="dialogo">
        <div class="dialog__overlay"></div>
        <div class="dialog__content">
            <slot></slot>
            <button class="action" data-dialog-close v-show="false">X</button>
        </div>
    </div>-->
</template>

<script>
    export default {
        name: "VModal.vue",

        props: {
            abierto: {
                type: Boolean,
                default: true,
            },
            mostrar_cabecera: {
                type: Boolean,
                default: true,
            },
            mostrar_pie: {
                type: Boolean,
                default: true,
            },
            size: {
                type: String,
                default: "sm"
            }
        },

        watch: {
            /*value() {
                if (this.value) {
                    this.$refs['dialogo'].classList.add('dialog--open');
                    document.getElementById('app').classList.add('container--move');
                }
                else {
                    this.$refs['dialogo'].classList.remove('dialog--open');
                    document.getElementById('app').classList.remove('container--move');
                }
            }*/
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

        methods: {
            close() {
                this.$emit('close', false);
                //this.abierto = false ;
            },

            ok() {
                this.close();
                this.$emit('success');
            },
        },

        computed: {

            isModalOpen(){

                return this.abierto

            }

        }

    }
</script>

<style scoped>
    .modal-backdrop {
        position: fixed;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        background-color: rgba(0, 0, 0, .3);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        overflow-y: auto;
    }

    .modal {
        background: #fff;
        box-shadow: 2px 2px 20px 1px;
        height: 80%;
        max-height: 80%;
        position: relative;
    }

    .modal-guts{
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;

        /* spacing as needed */
        padding: 2rem;

        /* let it scroll */
        overflow: auto;
    }

    .modal-guts::-webkit-scrollbar {
        width: 0.25rem;
    }

    .modal-guts::-webkit-scrollbar-track{
        background: #1e1e24;
    }

    .modal-guts::-webkit-scrollbar-thumb{
        background: blue;
    }


    .modal-sm {
        width: 30%!important;
    }

    .modal-md{

        width: 60%!important;

    }

    .modal-lg{
        width: 80%!important;
    }

    .modal-header,
    .modal-footer {
        padding: 15px;
        display: flex;
    }

    .modal-header {
        border-bottom: 1px solid #eee;
        justify-content: space-between;
        font-weight: bold;
    }

    .modal-footer {
        border-top: 1px solid #eee;
        justify-content: flex-end;
    }

    .modal-body {
        position: relative;
        padding: 20px 10px;
    }

    .modal-body > * {

        margin-bottom: 0.5rem;

    }

    .btn-close {
        border: none;
        font-size: 20px;
        padding: 0;
        cursor: pointer;
        font-weight: bold;
        color: #4aae9b;
        background: transparent;
    }

    .button-ok {
        min-width: 100px;
    }

    .fade-enter-active, .fade-leave-active {
        transition: opacity .5s;
    }
    .fade-enter, .fade-leave-to {
        opacity: 0;
    }
</style>