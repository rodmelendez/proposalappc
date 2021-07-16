<template>
    <main>

        <!--Seccion vista resumen: visualiza todos los items como una tabla-->

        <section class="index" v-if="vista_items">

            <!--Panel de dashboard: visualiza el titulo del modulo y boton para agregar nuevo etc-->
            <div class="dashboard-headline">
                <div class="row">
                    <div class="col-sm-8">
                        <h3>
                            {{ _titulo_plural }}
                            <span 
                                class="item-cargando"
                            >
                                &nbsp;<i class="icon-line-awesome-hourglass-2 fa-spin"></i>
                            </span>
                        </h3>
                    </div>

                    <div class="col-sm-4">
                        <div class="input-with-icon">
                            <input 
                                type="text" 
                                class="input-crud-buscar" 
                                placeholder="Buscar..." 
                                v-model="texto_buscado" 
                                @dblclick="texto_buscado = ''"
                            >
                            <i class="icon-material-outline-search"/>
                        </div>
                    </div>
                </div>
            </div>
            <!--Fin Panel de dashboard: visualiza el titulo del modulo y boton para agregar nuevo etc-->

            <!-- Fila: se muestra cada record -->
            <div class="row">

                <!-- Dashboard Box -->
                <div class="col-xl-12">

                    <div class="dashboard-box margin-top-0">
                        
                        <!-- Headline | Cabecera | -->
                        <div class="headline">

                            <h3>
                                <i :class="_icono"></i> {{ _items.length }} {{ _items.length === 1 ? _titulo_singular.toLowerCase() : _titulo_plural.toLowerCase() }}
                            </h3>

                            <div class="actions">
                                <button 
                                    type="button" 
                                    class="popup-with-zoom-anim button dark ripple-effect" 
                                    @click="showNewItemForm" 
                                    v-if="puede_crear"
                                >
                                    <i class="icon-feather-plus"></i> Nuevo
                                </button>
                            </div>

                        </div>
                        <!--Fin Headline | Cabecera | -->

                        <div class="content" :class="getViewClasses">

                            <slot name="cabecera"></slot>

                            <!--Renderizacion de la lista-->
                            <ul class="dashboard-box-list">

                                <template v-for="item in _items"
                                    appear
                                    mode="out-in"
                                    enter-active-class="animated fadeIn"
                                    leave-active-class="animated zoomOut"
                                >
                                    <li 
                                        v-show="esVisible(item)" 
                                        @click="expand" 
                                        :key="item.id"
                                    >

                                        <!-- Overview | Resumen -->
                                        <div class="freelancer-overview manage-candidates">
                                            <div class="freelancer-overview-inner">

                                                <!--Slot para colocar avatar o icono de usuario-->
                                                <slot name="avatar" :item="item"></slot>

                                                <div class="freelancer-name">

                                                    <slot name="contenido_item" :item="item"></slot>

                                                    <!-- Buttons | Botones -->
                                                    <div class="buttons-to-right always-visible margin-top-25 margin-bottom-5">
                                                        
                                                        <!--Slot para colocar botones propios-->
                                                        <slot name="botones" :item="item"></slot>

                                                        <!--Botones por defecto -->
                                                        <!--Para evitar que se muestre coloque la propiedad-->
                                                        <!--mostrarBotonesPorDefecto como false-->
                                                        <template>

                                                            <a 
                                                                class="button gray ripple-effect ico" 
                                                                :title="puede_editar ? 'Editar' : 'Ver'" 
                                                                @click="showEditItemForm(item, _items)"
                                                            >
                                                                <i :class="puede_editar ? 'icon-feather-edit' : 'icon-feather-eye'"></i>
                                                            </a>

                                                            <a 
                                                                class="button gray ripple-effect ico" 
                                                                title="Eliminar" 
                                                                @click="removerItem(item, _items)" v-if="puede_eliminar"
                                                            > <!-- data-tippy-placement="top" -->
                                                                <i class="icon-feather-trash-2"></i>
                                                            </a>
                                                        </template>

                                                    </div>
                                                    <!--Fin Buttons | Botones -->
                                                </div>

                                            </div>
                                        </div>
                                        
                                    </li>
                                </template>
                                <!-- Fin Overview | Resumen -->

                            </ul>
                            <!--Fin Renderizacion de la lista-->
                        </div>
                    </div>
                </div>

            </div>


            <paginador
                    v-show="!this.texto_buscado.length"
                    v-model="pagina_actual"
                    :total_items="_items.length"
                    @itemschanged="changeItemsPerPage"
            ></paginador>
        
        </section>

        <!--Fin seccion vista-->

        <!--Seccion formulario-->
        <section class="form" v-if="vista_formulario">

            <!-- Dashboard Headline | Formulario cabecera -->
            <div class="dashboard-headline">
                <h3>
                    <!--Boton volver-->
                    <button type="button" class="button" @click="hideForm">
                        <i class="icon-material-outline-arrow-back"></i>
                    </button>
                    &nbsp;

                    <template v-if="puede_crear || puede_editar">
                        {{ moduleTitle }}
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

                    <template v-if="showDefaultButtons" >
                        <a v-if ="canSave" 
                            href="#" 
                            class="button ripple-effect button-sliding-icon big margin-top-30"
                            @click="saveItem"
                        >
                            <span class="item-guardar">Guardar <i class="icon-feather-save"></i></span>
                            <span class="item-guardando">Guardando... <i class="icon-line-awesome-hourglass-2 fa-spin"></i></span>
                        </a>
                    </template>

                    <slot name="form_footer"></slot>
                </div>

            </form>
        </section>
        <!--Fin Seccion formulario-->

        <!--Muestra otras seciones en la vista-->
        <slot name="secciones"></slot>

    </main>
</template>

<script>
    export default {
        name: "EnhancedCrud.vue",


        props: {

            _fuente: String, //Nombre del controller
            _subdirectorio: String, //Si el controlador existe en una subcarpeta se indica en esta propiedad
            _titulo_singular: String, //Titulo del modulo
            _titulo_plural: String, //Titulo plural del modulo
            _icono: String, //Icono del modulo
            _items: Array, //Records de la bd para renderizar en forma de lista 
            _item: Object, //Record que se enviara a la bd
            _propiedades_buscadas: Array,
            _avatar_defecto: String,
            _tipo_vista: String,
            _vista_items: Boolean,
            _vista_formulario: Boolean,
            _handle_save: Function,
            _puede_guardar: Boolean,
            _show_default_buttons: {
                type: Boolean,
                default: true
            },
            urls: null,

        },

        /*props: [
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
        ],*/

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
            
            getViewClasses() {

                if ( typeof this._tipo_vista !== 'string' || !this._tipo_vista.length ) 
                    return '';

                switch (this._tipo_vista) {
                    
                    case 'compacto':
                        return 'modo-compacto';

                    default: 
                        return '';

                }

            },

            moduleTitle: function(){

               return  this.item.id  > 0 ? ('Modificar ' + this._titulo_singular.toLowerCase()) : ('Agregar ' + this._titulo_singular.toLowerCase()) 

            },

            canSave(){
                
                if( this.puede_guardar )
                    return this.puede_guardar;

                return true;
            },

            showDefaultButtons(){

                if( this._show_default_buttons !== null && this._show_default_buttons !== undefined){

                    return this._show_default_buttons;

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

            //Metodo para ser utilizado por 
            //default en la instancia de app
            //y poder llamar al setItemData 
            //del componente padre
            setItemData(data) {
                this.$emit('itemDataSet', data);
            },

            itemEliminado(indice) {
                this.$emit('postItemEliminado', indice);
                this.$$forceUpdate();
            },

            procesarCargaData(data) {

                this.$emit('postCargarData', data);
            },

            hideForm(){

                this.$emit("formHide");
                this.ocultarFormulario();

            },

            expand(e) {

                let $item = $(e.target);

                switch ( $item.prop('tagName') ) {
                    
                    case 'A':
                        return;

                    case 'LI':
                        break;

                    default:

                        if ( ['BUTTON','A'].includes( $item.parent().prop('tagName') ) ) {
                            return;
                        }

                        $item = $item.closest('li');
                }

                $item.toggleClass('abierto');

            },

            changeItemsPerPage(value){
                this.items_por_pagina = value;
                this.$emit("changeItemsPerPage",value);
            },


            showNewItemForm() {
                this.mostrarFormularioNuevo();
                this.$emit('formWasShown');
            },

            showEditItemForm(item, _items) {
                
                const self = this;

                this.debugStuff( item , "hotpink" , "ITEM EN EDITAR FORMULARIO");

                this.editarItem(item, _items, function() {
                    self.$emit('editFormWasShown');
                });

            },

            saveItem() {

                if (typeof this._handle_save === 'function') {
                    this._handle_save(this.$refs['form']);
                    return;
                }

                this.guardar();
            },


        },

        created() {

            const categoria = this.$route.path.split('/').pop();

            this.puede_consultar = this.tienePermiso(categoria, 'consultar');
            this.puede_crear     = this.tienePermiso(categoria, 'crear');
            this.puede_editar    = this.tienePermiso(categoria, 'editar');
            this.puede_eliminar  = this.tienePermiso(categoria, 'eliminar');

            this.subdirectorio   = this._subdirectorio;
            
        },

        mounted() {

            this.fuente = this._fuente;
            this.ruta   = this.$route.path.split('/').pop();
            this.propiedades_buscadas = this._propiedades_buscadas;
            this.item = this._item;

            this.vista_formulario = this._vista_formulario || !this.puede_consultar;
            this.vista_items      = this._vista_items && this.puede_consultar;

            document.documentElement.scrollTop = 0;

            $(this.$refs['form']).on('submit', function(e) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            });
        },

    }
</script>

<style scoped>

</style>