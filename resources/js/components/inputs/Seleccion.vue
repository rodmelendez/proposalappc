<template>
    <div class="submit-field" :class="clases">
        <h5>{{ etiqueta }}</h5>
        <select :name="nombre + (multiple ? '[]' : '')" class="selectpicker with-border-x" data-size="7" :title="etiqueta" :ref="nombreReferencia" :data-live-search="buscador" :multiple="multiple" :indice="indice">
            <option
                    v-for="item in items"
                    :value="item[accesor]"
                    v-bind:key="item.id"
                    :selected="value instanceof Array ? value.includes(item.id) : item.id === value"
                    :data-text="item.nombre"
                    :data-subtext="subTexto(item)"
                    :data-img="imgUrl(item)"
            >
                {{ item.nombre }}
            </option>
        </select>
    </div>
</template>

<script>
    export default {
        name: "Seleccion.vue",

        props: {
            showSearchAlways: {
                type: Boolean,
                default: false
            },
            etiqueta: String,
            nombre: {
                type: String,
                required: true
            },
            items: Array,
            items_seleccionados: Array,
            clases: String,
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
            plantilla: {
                type: String,
                default: ''
            },
            campo_subtexto: {
                type: String,
                default: 'subtexto'
            },
            indice: {
                type: Number,
                required: false,
                default: 0,
            },
            accesor: {
                type: String,
                default: "id"
            }
        },

        data: () => ({
            /*nombre,
            items: [],
            items_seleccionados: [],*/
        }),

        computed: {
            nombreReferencia() {
                return this.nombre.split('[')[0];
            }
        },

        watch: {
            /*value() {
                this.$emit('input', this.value);
            },*/

            items() {
                const $item = $(this.$refs[this.nombreReferencia]);
                $item.select2('destroy');
                this.buildSelect($item);
            },
        },

        mounted() {
            const self = this;
            const $item = $(this.$refs[this.nombreReferencia]);

            //$(this.$refs[this.nombre]).selectpicker();
            this.buildSelect($item);

            $item.on('change', function(e) {
                self.$emit('cambiado', e.target.value);
                self.actualizarValor();
            }).change();

            //$item.change(this.actualizarValor);
        },

        updated() {
            $(this.$refs[this.nombreReferencia])
                .val(this.value)
                .trigger('change.select2');
        },

        methods: {
            buildSelect($item) {
                if (typeof $item === 'undefined') {
                    $item = $(this.$refs[this.nombreReferencia]);
                }
                $item.select2({
                    language: 'es',
                    minimumResultsForSearch: this.showSearchAlways ? 0 : !this.buscador ? Infinity : 5,
                    /*ajax: typeof url !== 'string' || !url.length ? null : {
                        url: url,
                        dataType: 'json',
                        delay: 300,
                        data: function(params) {
                            return {
                                busqueda: params.term,
                                _fuente: fuente,
                                campo: $campo.attr('id') || ''
                            };
                        }
                    },*/
                    templateSelection: this.formatoPlantilla,
                    templateResult: this.formatoPlantilla,
                });
            },

            actualizarValor() {
                this.$emit('input', $(this.$refs[this.nombreReferencia]).val());
            },

            formatoPlantilla(state) {
                if (typeof this.plantilla === 'string' && this.plantilla.length && state.id) {
                    switch (this.plantilla) {
                        case 'avatar':
                            if (typeof state.img === 'undefined' || !state.img.length) {
                                if (state.element) { //intenta obtener la data desde el elemento
                                    const $item = $(state.element);
                                    const data = $item.data();
                                    if (typeof data.img !== 'undefined' && data.img.length) state = data;
                                }
                                if (typeof state.img === 'undefined' || !state.img.length) {
                                    return state.text;
                                }
                            }

                            const subtext = typeof state.subtext !== 'undefined' && state.subtext.length ? ('<br><span class="small">&nbsp;&nbsp;' + state.subtext + '</span>') : '';
                            const img_style = '';//subtext.length ? ' style="display:inline-block;margin-top:-22px"' : '';
                            const txt_style = subtext.length ? ' style="margin-top:0;"'  : '';

                            return $(
                                '<div><img class="avatar"' + img_style + ' src="' + state.img + '" alt=""> <span class="etiqueta"' + txt_style + '>&nbsp;&nbsp;<b>' + state.text + '</b>' + subtext + '</span></div>'
                            );
                    }
                }
                return state.text;
            },

            subTexto(item) {
                return typeof item[this.campo_subtexto] === 'string' ? item[this.campo_subtexto] : '';
            },

            imgUrl(item) {
                if (this.plantilla === 'avatar') {
                    if (typeof item.img === 'string' && item.img.length) {
                        return this.$uploads_img_dir + item.img;
                    }
                    else {
                        return this.$avatar_defecto;
                    }
                }
                return '';
            }
        }
    }
</script>

<style scoped>

</style>