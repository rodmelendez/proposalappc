<template>
    <main>
        <section class="index" v-if="vista_items">
            <!-- Dashboard Headline -->
            <div class="dashboard-headline">
                <div class="row">
                    <div class="col-sm-8">
                        <h3>
                            {{ _titulo_plural }}
                            <span class="item-cargando">&nbsp;<i class="icon-line-awesome-hourglass-2 fa-spin"></i></span>
                        </h3>
                    </div>

                    <div class="col-sm-4">
                        <div class="input-with-icon">
                            <input type="text" class="input-crud-buscar" placeholder="Buscar..." v-model="texto_buscado" @dblclick="texto_buscado = ''">
                            <i class="icon-material-outline-search"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row -->
            <div class="row">

                <!-- Dashboard Box -->
                <div class="col-xl-12">
                    <div class="dashboard-box margin-top-0">

                        <!-- Headline -->
                        <div class="headline">
                            <h3><i :class="_icono"></i> {{ _items.length }} {{ _items.length === 1 ? _titulo_singular.toLowerCase() : _titulo_plural.toLowerCase() }}</h3>

                            <div class="actions">
                                <button type="button" class="popup-with-zoom-anim button dark ripple-effect" @click="ejecutarMostrarFormularioNuevo" v-if="puede_crear">
                                    <i class="icon-feather-plus"></i> Nuevo
                                </button>
                            </div>
                        </div>

                        <div class="content" :class="clasesTipoVista">
                            <slot name="cabecera"></slot>

                            <ul class="dashboard-box-list">
                                <template v-for="item in _items"
                                        appear
                                        mode="out-in"
                                        enter-active-class="animated fadeIn"
                                        leave-active-class="animated zoomOut"
                                >
                                    <li v-show="esVisible(item)" @click="expandir" :key="item.id">
                                        <!-- Overview -->
                                        <div class="freelancer-overview manage-candidates">
                                            <div class="freelancer-overview-inner">
                                                <slot name="avatar" :item="item"></slot>

                                                <!-- Name -->
                                                <div class="freelancer-name">
                                                    <slot name="contenido_item" :item="item"></slot>

                                                    <!-- Buttons -->
                                                    <div class="buttons-to-right always-visible margin-top-25 margin-bottom-5">
                                                        <slot name="botones" :item="item"></slot>

                                                        <template
                                                            v-if="mostrarBotonesPorDefecto"
                                                        >
                                                            <a class="button gray ripple-effect ico" :title="puede_editar ? 'Editar' : 'Ver'" @click="ejecutarMostrarFormularioEditar(item, _items)"> <!-- data-tippy-placement="top" -->
                                                                <i :class="puede_editar ? 'icon-feather-edit' : 'icon-feather-eye'"></i>
                                                            </a>

                                                            <a class="button gray ripple-effect ico" title="Eliminar" @click="removerItem(item, _items)" v-if="puede_eliminar"> <!-- data-tippy-placement="top" -->
                                                                <i class="icon-feather-trash-2"></i>
                                                            </a>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </template>

                            </ul>
                        </div>
                    </div>
                </div>

            </div>

            <paginador
                v-show="!this.texto_buscado.length"
                v-model="pagina_actual"
                @itemschanged="changeItemsPerPage"
                :total_items="_items.length"
            ></paginador>
        </section>

        <section class="form" v-if="vista_formulario">
            <!-- Dashboard Headline -->
            <div class="dashboard-headline">
                <h3>
                    <button type="button" class="button" @click="ocultarFormulario">
                        <i class="icon-material-outline-arrow-back"></i>
                    </button>
                    &nbsp;
                    <template v-if="puede_crear || puede_editar">
                        {{ _item.id > 0 ? ('Modificar ' + _titulo_singular.toLowerCase()) : ('Agregar ' + _titulo_singular.toLowerCase()) }}
                    </template>
                    <template v-else>
                        {{ _titulo_singular }}
                    </template>
                </h3>
            </div>

            <form ref="form" method="post" :disabled="!puede_crear || !puede_editar">
                
                <slot name="formulario"></slot>

                <div class="col-xl-12" v-if="puede_crear || puede_editar">
                    <input type="hidden" name="_fuente" :value="fuente">
                    <input type="hidden" name="_subdirectorio" :value="subdirectorio">
                    <input type="hidden" name="_ruta" :value="ruta">
                    <input type="hidden" name="id" v-model="_item.id">

                    <a v-if ="_puede_guardar" href="#" class="button ripple-effect button-sliding-icon big margin-top-30" @click="guardarFuente">
                        <span class="item-guardar">Guardar <i class="icon-feather-save"></i></span>
                        <span class="item-guardando">Guardando... <i class="icon-line-awesome-hourglass-2 fa-spin"></i></span>
                    </a>

                    <slot name="form_footer"></slot>
                </div>

            </form>
        </section>

        <slot name="secciones">

        </slot>
        
    </main>
</template>

<script>
    export default {
        name: "Crud.vue",

        props: [
            '_fuente',
            '_subdirectorio',
            '_titulo_singular',
            '_titulo_plural',
            '_icono',
            '_items',
            '_item',
            '_propiedades_buscadas',
            'urls',
            'avatar_defecto',
            '_tipo_vista',
            '_vista_items',
            '_vista_formulario',
            '_fn_guardar',
            'puede_guardar',
            'mostrar_botones_por_defecto'
        ],

        data: () => ({
            puede_consultar: true,
            puede_crear: false,
            puede_editar: false,
            puede_eliminar: false,
        }),

        /*watch: {
            _items() {
                this.items = this._items;
            }
        },*/

        computed: {
            clasesTipoVista() {
                if (typeof this._tipo_vista !== 'string' || !this._tipo_vista.length) return '';

                switch (this._tipo_vista) {
                    case 'compacto':
                        return 'modo-compacto';
                }

                return '';
            },

            _puede_guardar(){
                
                if( this.puede_guardar )
                    return this.puede_guardar;

                return true;
            },

            mostrarBotonesPorDefecto(){

                if( this.mostrar_botones_por_defecto !== null && this.mostrar_botones_por_defecto !== undefined){
                    return this.mostrar_botones_por_defecto
                }

                return true;

            }
        },

        watch: {
            _vista_items() {
                this.vista_items = this._vista_items && this.puede_consultar;
            },

            _vista_formulario() {
                this.vista_formulario = this._vista_formulario || !this.puede_consultar;
            },

            _subdirectorio() {
                this.subdirectorio = this._subdirectorio;
            },
        },

        methods: {

            setItemData(data) {
                this.$emit('itemDataSet', data);
            },

            itemEliminado(indice) {
                this.$emit('postItemEliminado', indice);
            },

            procesarCargaData(data) {
                this.$emit('postCargarData', data);
            },

            expandir(e) {
                let $item = $(e.target);

                switch ($item.prop('tagName')) {
                    case 'BUTTON':
                    case 'A':
                        return;

                    case 'LI':
                        break;

                    default:
                        if (['BUTTON','A'].includes($item.parent().prop('tagName'))) {
                            return;
                        }
                        $item = $item.closest('li');
                }

                $item.toggleClass('abierto');
            },

            ejecutarMostrarFormularioNuevo() {
                this.mostrarFormularioNuevo();
                this.$emit('formularioMostrado');
            },

            ejecutarMostrarFormularioEditar(item, _items) {
                const self = this;
                this.editarItem(item, _items, function() {
                    self.$emit('formularioEditarMostrado');
                });
            },

            changeItemsPerPage(value){
                this.items_por_pagina = value;
                this.$forceUpdate();
            },

            guardarFuente() {
                if (typeof this._fn_guardar === 'function') {
                    this._fn_guardar(this.$refs['form']);
                    return;
                }
                this.guardar();
            }
        },

        created() {
            const categoria = this.$route.path.split('/').pop(); //this._fuente;

            this.puede_consultar = this.tienePermiso(categoria, 'consultar');
            this.puede_crear = this.tienePermiso(categoria, 'crear');
            this.puede_editar = this.tienePermiso(categoria, 'editar');
            this.puede_eliminar = this.tienePermiso(categoria, 'eliminar');

            this.subdirectorio = this._subdirectorio;
        },

        mounted() {
            this.fuente = this._fuente;
            this.ruta = this.$route.path.split('/').pop();
            this.propiedades_buscadas = this._propiedades_buscadas;
            this.item = this._item;

            this.vista_formulario = this._vista_formulario || !this.puede_consultar;
            this.vista_items = this._vista_items && this.puede_consultar;

            document.documentElement.scrollTop = 0;

            $(this.$refs['form']).on('submit', function(e) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            });
        }
    }
</script>

<style scoped>

</style>