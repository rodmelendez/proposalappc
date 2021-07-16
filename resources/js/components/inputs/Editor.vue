<template>
    <div class="submit-field">
        <h5 v-if="etiqueta.length">{{ etiqueta }}</h5>
        <div :id="nombre"></div>
        <input type="hidden" :name="nombre" value="" ref="valor" autocomplete="off">
        <input type="hidden" :name="nombre + '_json'" value="" ref="valor_json" autocomplete="off">
    </div>
</template>

<script>
    export default {
        name: "Editor.vue",

        props: {
            nombre: {
                type: String,
                required: true,
            },
            etiqueta: {
                type: String,
                default: '',
            },
            value: {
                type: String,
                required: true
            },
        },

        mounted() {
            const self = this;

            let data = {};

            if (this.value.length) {
                try {
                    data = JSON.parse(this.value);
                } catch (e) {}
            }

            let editor = new EditorJS({
                holder: this.nombre,
                placeholder: this.etiqueta,
                data: data,

                onChange: () => {
                    editor.save().then((data) => {
                        let html = '';

                        for (const item of data.blocks) {
                            html += item.data.text + '<br>';
                        }

                        $(self.$refs['valor']).val(html);
                        $(self.$refs['valor_json']).val(JSON.stringify(data));
                    });
                }
            });
        }
    }
</script>

<style>
    .codex-editor {
        box-shadow: 0 1px 4px 0 rgba(0, 0, 0, .12) !important;
        padding-left: .7em;
    }

    .codex-editor__redactor {
        padding-bottom: 50px !important;
    }

    .ce-paragraph[data-placeholder="Tell your story..."]:before {
        visibility: hidden !important;
    }
</style>