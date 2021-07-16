<template>
    <div 
        class="submit-field input-archivo" 
        :class="typeof clase !== 'undefined' && clase !== null ? clase : ''"
        v-bind:style="inputStyles"
    >
        <h5 v-if="typeof etiqueta === 'string' && etiqueta.length">{{ etiqueta }}</h5>
        <input type="hidden" :name="nombre" :value="value || ''">
        <input type="hidden" :name="nombre + '_upload_modificado'" class="input-modificado" :value="modificado">
        <input 
            type="file" 
            :name="nombre + '_upload'" 
            class="dropify" 
            :data-default-file="url" 
            :data-height="altura" 
            :ref="nombre" 
            :id_etiqueta="id_etiqueta"
            :disabled="disabled"
            @change="marcarComoModificado"
        >
        <div v-if="typeof documento !== 'undefined' && documento !== null && documento.loaded">
            <a 
                :href="downloadLink" 
                :download="documento.document.nombre"
                class="button gray ripple-effect ico" 
            >   
                <i class="icon-feather-download" />
            </a>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Archivo",

        props: {
            disabled: {
                type: Boolean,
                default: false
            },

            id_etiqueta:{
                type: Number,
                default: -1
            },
            
            nombre: {
                type: String,
                required: true,
            },
            etiqueta: {
                type: String
            },
            clase: {
                default: null,
            },
            altura: {
                //type: Number,
                default: 300,
            },
            anchura: {
                //type: Number,
                default: 300,
            },
            value: {
                //type: String,
                required: true
            },
            documento: {
                type: Object,
                default: null,
            },
            required: {
                type: Boolean
            }
        },

        data: () => ({
            modificado: 0
        }),

        watch: {
            value() {
                this.$emit('input', this.value);
            }
        },

        mounted() {
            $(this.$refs[this.nombre]).dropify({
                messages: {
                    'default': '',
                    'replace': '',
                    'remove':  'Quitar',
                    'error':   'Error.'
                },
                tpl: {
                    message: '<div class="dropify-message"><i class="icon-feather-file"></i></div>',
                    clearButton: !this.disabled ? '<button type="button" class="dropify-clear"><i class="icon-feather-trash-2"></i></button>' : ''
                }
            })
            .on('dropify.afterClear', function(/*e, el*/) {
                $(this)
                    .closest('.input-archivo')
                    .find('input.input-modificado')
                    .val('1');
            });
        },

        computed: {
            url() {
                if (typeof this.documento !== 'undefined' && this.documento !== null && this.documento.document && this.documento.document.nombre) {
                  return (this.$uploads_doc_dir + this.documento.document.nombre);
                }

                return this.value ? (this.$uploads_doc_dir + this.value) : ''; //Vue.prototype.$uploads_img_dir
            },

            downloadLink() {
                if (typeof this.documento !== 'undefined' && this.documento !== null && this.documento.document && this.documento.document.link) {
                  return (this.$uploads_doc_dir + this.documento.document.link);
                }

                return '';
            },

            inputStyles() {
                return({           
                    minWidth: `${this.anchura}px`
                });
            }
        },

        methods: {
            marcarComoModificado() {

                //this.modificado = 1;
                const input = this.$refs[this.nombre];
                const $contenedor = $(input).closest('.dropify-wrapper');
                const self = this;

                setTimeout(() => {
                    self.modificado = 1;

                    if (!$contenedor.hasClass('has-error')) {
                        self.$emit('modificado', input , this.documento);
                    }
                }, 700);
            },

            clearDocument(){

            }


        }
    }
</script>

<style>
    .input-archivo .dropify-font-file:before {
        font-family: "Feather-Icons" !important;
        content: '\E961';
    }
</style>