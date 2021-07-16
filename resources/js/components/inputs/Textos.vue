<template>
    <div class="submit-field">
        <h5>{{ etiqueta }} <i class="help-icon" data-tippy-placement="top" data-tippy="" :data-original-title="tip" v-if="typeof tip === 'string' && tip.length"></i></h5>
        <div class="keywords-container">
            <div class="keyword-input-container">
                <input type="text" :name="nombre + '_in'" class="keyword-input with-border-x" :placeholder="etiqueta" v-model="item" @keyup.enter="agregarItem" @blur="agregarItem">
                <input type="hidden" :name="nombre + '[]'" :value="valor" v-for="valor in items">
                <button type="button" class="keyword-input-button ripple-effect" @click="agregarItem" v-show="this.item.length">
                    <i class="icon-material-outline-add"></i>
                </button>
            </div>
            <div class="keywords-list" style="height: auto;" ref="keywords">
                <transition v-for="(item, indice) in items" :key="indice"
                            mode="out-in"
                            appear
                            enter-active-class=""
                            leave-active-class="keyword-removed"
                            v-on:after-enter="redimenzionar"
                            v-on:after-leave="redimenzionar"
                >
                    <span class="keyword">
                        <span class="keyword-remove" @click="quitarItems(indice)"></span>
                        <span class="keyword-text">{{ item }}</span>
                    </span>
                </transition>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Textos.vue",

        props: {
            nombre: String,
            etiqueta: String,
            tip: String,
            value: {
                //type: String,
                required: true,
            }
        },

        data: () => ({
            items: [],
            item: ''
        }),

        watch: {
            value() {
                this.$emit('input', this.value);
            }
        },

        mounted() {
            this.items = typeof this.value === 'string' && this.value.length ? this.value.split(',') : [];
            this.item = '';
            //console.log('setting items', this.items);
        },

        updated() {
            //this.redimenzionar();
        },

        methods: {
            agregarItem() {
                const item = this.item.trim();
                if (item.length && !this.items.includes(item)) {
                    this.items.push(item);
                    this.$emit('input', item);
                    this.item = '';
                }
            },

            quitarItems(indice) {
                this.items.splice(indice, 1);
            },

            redimenzionar() {
                const $item = $(this.$refs['keywords']);
                const height_now = $item.height();
                const height_full = $item.css({'max-height':'auto', 'height':'auto'}).height();

                $item.css({'height': height_now})
                    .animate({'height': height_full}, 200);
            }
        }
    }
</script>

<style scoped>

</style>