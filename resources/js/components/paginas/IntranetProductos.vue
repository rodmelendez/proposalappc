<template>
    <enhanced-crud
        :_titulo_singular="titulo_singular"
        :_titulo_plural="titulo_plural"
        :_icono="icono"
        :_fuente="fuente"
        :_propiedades_buscadas="propiedades_buscadas"
        :_items="items"
        :_item="item"
        :_vista_items="vista_items"
        :_vista_formulario="vista_formulario"
        :_puede_guardar="true"
        :_show_default_buttons="true"
        :urls="urls"
         _tipo_vista="compacto"
        @editFormWasShow="mostrarFormulario"
        @formWasShown="mostrarFormularioNuevo"
        @itemDataSet="itemDataSet"
        @formHide="limpiarItem"
        @postCargarData="postCargarData"
    >

        <template slot="contenido_item" slot-scope="row">
            <h4><b>{{ row.item.nombre }}</b></h4>
        </template>

        <template slot="formulario">
            <div class="row">

                <!-- Dashboard Box -->
                <div class="col-xl-12">
                    <div class="dashboard-box margin-top-0">

                        <div class="content with-padding padding-bottom-10">
                            <div class="row">
                                <div class="col-xl-8">
                                    <input-texto
                                        v-model="item.nombre"
                                        nombre="nombre"
                                        etiqueta="Nombre"
                                    ></input-texto>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </template>

    </enhanced-crud>
</template>

<script>
export default {
    name:"Productos.vue",
    
    props: {
        urls: Object,
        avatar_defecto: String,
    },

    methods: {

        limpiarItem(){
            console.log(this.items);
            this.item.id = 0;
            this.item.nombre = '';
            this.item.id_usuario = this.$usuario.id;
        },

        setItemData(data){
            this.item.id = data.id;
            this.item.nombre = data.nombre;
            this.item.id_usuario = this.$usuario.id;
        }

    },

    data: () => ({

        titulo_singular: "Producto",
        titulo_plural: "Productos",
        icono: "las la boxes",
        fuente: "IntranetPresolicitudProducto",
        propiedades_buscadas: ["nombre"],


        items: [],
        item: {
            id: null,
            nombre: '',
        }
    }),


    mounted() {
        this.cargarData();
    },
}
</script>