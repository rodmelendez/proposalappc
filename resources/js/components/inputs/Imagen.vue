<template>
    <div class="submit-field input-imagen width-100">
        <h5 v-if="typeof etiqueta === 'string' && etiqueta.length">{{ etiqueta }}</h5>
        <input type="hidden" :name="nombre" :value="value || ''">
        <input type="hidden" :name="nombre + '_upload_modificado'" class="input-modificado" :value="modificado">
        <input 
            type="file" 
            :name="nombre + '_upload'" 
            class="dropify" 
            data-allowed-file-extensions="jpg jpeg png gif" 
            data-max-file-size="5M" 
            :data-default-file="url" 
            :data-height="altura" 
            :data-width="width" 
            :disabled="disabled"
            :ref="nombre" @change="marcarComoModificado"
        >
    </div>
</template>

<script>
    export default {
        name: "Imagen",

        props: {

            disabled: {
                type: Boolean,
                default: false
            },

            nombre: {
                type: String,
                required: true,
            },
            etiqueta: String,

            altura: {
                //type: Number,
                default: 300,
            },
            anchura:{

                default:300
            },
            value: {
                //type: String,
                required: true
            },
            icono_defecto: {
                default: 'icon-feather-image'
            },
            width: {
                default: 300,
                type: Number
            },
            
            documento: {
                type: Object
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
                error: {
                    'fileSize': 'La imagen excede el tamaño máximo ({{ value }}).',
                    'minWidth': 'El ancho de la imagen es muy pequeño ({{ value }}}px min).',
                    'maxWidth': 'El ancho de la imagen es muy grande ({{ value }}}px max).',
                    'minHeight': 'El alto de la imagen es muy pequeño ({{ value }}}px min).',
                    'maxHeight': 'El alto de la imagen es muy grande ({{ value }}px max).',
                    'imageFormat': 'El formato del archivo no es válido (solo {{ value }}).',
                    'fileFormat': 'El formato del archivo no es válido (solo {{ value }}).'
                },
                tpl: {
                    message: '<div class="dropify-message"><i class="' + this.icono_defecto + '"></i></div>', //icon-line-awesome-cloud-upload
                    clearButton: !this.disabled ? '<button type="button" class="dropify-clear"><i class="icon-feather-trash-2"></i></button>' : ''
                }
            })
            .on('dropify.afterClear', function(/*e, el*/) {
                $(this)
                    .closest('.input-imagen')
                    .find('input.input-modificado')
                    .val('1');
            });
        },

        computed: {
            url() {
                return this.value ? (this.$uploads_img_dir + this.value) : ''; //Vue.prototype.$uploads_img_dir
            }
        },

        methods: {
            marcarComoModificado() {
                //se marca como modificado solo si no hay errores
                const input = this.$refs[this.nombre];
                const $contenedor = $(input).closest('.dropify-wrapper');
                const self = this;

                setTimeout(() => {
                    self.modificado = 1;

                    if (!$contenedor.hasClass('has-error')) {
                        self.$emit('modificado', input , this.documento);
                    }
                    
                }, 700);
            }
        }
    }
</script>

<style scoped>

    .width-100 {
        width: 100% !important;
    }

</style>