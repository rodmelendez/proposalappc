<template>
    <div class="submit-field">
        <h5>{{ etiqueta || '' }} <i class="help-icon" data-tippy-placement="top" data-tippy="" :data-original-title="tip" v-if="typeof tip === 'string' && tip.length"></i></h5>
        <div class="keywords-container">
            <div class="keyword-input-container">
                <!--<input type="text" :name="nombre + '_in'" class="keyword-input with-border" :placeholder="etiqueta" v-model="item.nombre">-->
                <select
                        :name="nombre + (multiple ? '[]' : '')"
                        class="selectpicker with-border"
                        data-size="7"
                        :title="etiqueta || ''"
                        :ref="nombre"
                        :data-live-search="buscador"
                        :multiple="multiple"
                >
                    <option
                            v-for="item in items"
                            v-bind:key="item.id"
                            :value="item.id"
                            :selected="value instanceof Array ? value.includes(item.id) : item.id === value"
                    >
                        {{ typeof item.abreviatura === 'string' && item.abreviatura.length ? (item.abreviatura + ' - ') : '' }}
                        {{ item.nombre }}
                    </option>
                </select>
                <button
                        type="button"
                        class="keyword-input-button ripple-effect"
                        @click="mostrarModal"
                >
                    <i :class="!modal ? 'icon-material-outline-library-add' : 'icon-material-outline-keyboard-arrow-up'"></i>
                </button>
            </div>

            <div class="clearfix"></div>
        </div>

        <div class="modal zoom-anim-dialog mfp-hide2 dialog-with-tabs" :ref="nombre + '_modal'" v-show="modal">
            <!--Tabs -->
            <div class="sign-in-form">

                <!--<ul class="popup-tabs-nav">
                </ul>-->

                <div class="popup-tabs-container">

                    <!-- Tab -->
                    <div class="popup-tab-content">

                        <!-- Welcome Text -->
                        <!--<div class="welcome-text">
                            <h3>Registrar</h3>
                        </div>-->

                        <!-- Form -->
                        <form :ref="nombre + '_frm'">
                            <slot name="modal"></slot>
                            <input type="hidden" name="_fuente" :value="fuente || ''" v-if="typeof fuente === 'string' && fuente.length">
                            <input type="hidden" name="_subdirectorio" :value="subdirectorio">
                        </form>

                        <!-- Button -->
                        <button class="button full-width button-sliding-icon ripple-effect" type="submit" form="leave-review-form" @click="guardar">
                            GUARDAR
                            <i class="icon-material-outline-arrow-right-alt"></i>
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Objeto.vue",

        props: {
            etiqueta: String,
            tip: String,
            fuente: String,
            _subdirectorio: {
                type: String,
                default: '',
            },
            url: {
                type: String,
                required: true,
            },
            nombre: {
                type: String,
                required: true,
            },
            item: {
                type: Object,
                default: () => ({
                    id: 0,
                    nombre: ''
                })
            },
            items: {
                type: Array,
                default: () => []
            },
            multiple: {
                type: Boolean,
                default: false
            },
            buscador: {
                type: Boolean,
                default: true
            },
            value: {
                //type: String,
                required: true
            },
        },

        watch: {
            _subdirectorio() {
                this.subdirectorio = this._subdirectorio;
            },

            items() {
                setTimeout(() => {
                    const $select = $(this.$refs[this.nombre]);
                    $select.select2('destroy');
                    $select.select2({
                        language: 'es',
                        minimumResultsForSearch: 5
                    });
                }, 150);
            },
        },

        data: () => ({
            modal: false,
        }),

        created() {
            this.subdirectorio = this._subdirectorio || '';
        },

        mounted() {
            const $item = $(this.$refs[this.nombre]);

            $item.select2({
                language: 'es',
                minimumResultsForSearch: 5
            });

            const self = this;

            $item.on('change', function(e) {
                self.$emit('cambiado', e.target.value);
                self.$emit('input', e.target.value);
            });

            $item.change(this.actualizarValor);

            if (typeof this.value !== 'string' || typeof this.value !== 'number' || !parseInt(this.value.toString())) {
                $item.val(null).trigger('change.select2');
            }

            /*$(this.$refs[this.nombre + '_btn']).magnificPopup({
                items: {
                    src: '#' + this.nombre + '_modal',
                    type: 'inline'
                },
                type: 'inline',

                fixedContentPos: false,
                fixedBgPos: true,

                overflowY: 'auto',

                closeBtnInside: true,
                preloader: false,

                midClick: true,
                removalDelay: 300,
                mainClass: 'my-mfp-zoom-in'
            });*/
        },

        updated() {
            $(this.$refs[this.nombre])
                .val(this.value)
                .trigger('change.select2');
        },

        methods: {
            mostrarModal() {
                this.modal = !this.modal;
            },

            guardar() {
                const form = this.$refs[this.nombre + '_frm'];//document.querySelector('form');
                const form_data = new FormData(form);

                const config = { headers: { 'Content-Type': 'multipart/form-data' } };
                this.$http.post(this.url, form_data, config)
                    .then(response => {
                        if (response.status === 200) {
                            resultadoSolicitudDefecto(response.data);

                            if (response.data.ok && response.data.id) {
                                this.modal = false;

                                this.$emit('guardado', response.data);
                            }
                        }
                        else if (response.status === 500) {
                            mensajeError('Error de servidor.');
                        }
                    });
            },
        },
    }
</script>

<style scoped>
    .modal {
        padding: 0;
        max-width: 540px;
        box-shadow: 0 0 25px rgba(0, 0, 0, .25);
    }
</style>