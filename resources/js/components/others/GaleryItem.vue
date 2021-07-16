<template>
    
    <div class = "contenedor-galeria-items freelancers-container freelancers-grid-layout galery-item" :class="uploading ? 'status-uploading' : '' ">

        <!--Input Imagen -->
        <template v-if="!imageFile.loaded">

            <input-imagen
                v-model="picture"
                nombre ="picture"
                :etiqueta="label"
                icono_defecto="icon-feather-plus"
                altura="160"
                :width="width"
                @modificado="makeNewPicture"
                
            />

        </template>
        <!--Fin Input Imagen-->

        <!--Galeria Item-->
        <template v-else>

            <div 
                class="galeria-item freelancer unpop small sin-transicion galery-item-raw" 
                :class="[ !active ? 'inactivo' : '', loading ? 'cargando' : '']" 
                :data-id="item.id" 
                :data-key="item._key" ref="main_item"
            >
                <div 
                    class="freelancer-overview image-container" 
                    :style="'background-image:url(' + pictureUrl + ')'"
                >
                    <div class="freelancer-overview-inner">
                        <!-- Actions -->
                        <div 
                            class="item-actions" 
                            :class="are_actions_expanded ? 'expandido' : ''" 
                            @mouseover="expandActions" 
                            @mouseleave="hideActions"
                        >
                            <span class="bookmark-icon" @click="rotate">
                                <i class="icon-feather-rotate-ccw"></i>
                            </span>
                            <span class="bookmark-icon remove" @click="remove">
                                <i class="icon-feather-trash-2"></i>
                            </span>
                            <span class="bookmark-icon" @click="expandImageInGalery">
                                <i :class="active ? 'icon-feather-eye' : 'icon-feather-eye-off'"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <h4 class="picture-title"> {{imageFile.template.nombre}}</h4>
                <input 
                    type="text" 
                    :name="label + '_titulo[]'" 
                    class="custom-input" 
                    placeholder="Observaciones..."
                    :value="imageFile.picture.observaciones === 'ninguna' ? '' : imageFile.picture.observaciones || ''" 
                    @input="updateName" 
                    ref="input_nombre"
                >

                <!--
                <div class="freelancer-details">
                    <div class="freelancer-details-list">
                        <ul>
                            <li>Ubicación 
                                <strong>
                                <a 
                                    :href="'http://www.google.com/maps/place/' + imageFile.picture.latitud + ',' + imageFile.picture.longitud" 
                                    v-if="typeof imageFile.picture.longitud === 'string' && imageFile.picture.longitud.length" 
                                    target="_blank"
                                >
                                    <i class="icon-material-outline-location-on"></i>
                                </a>
                                <span v-else>
                                    &mdash;
                                </span>
                                </strong>
                            </li>
                            <li>Dimensiones <strong>{{ imageFile.picture.ancho || '' }}x{{ imageFile.picture.alto || '' }}</strong></li>
                            <li>Tamaño <strong>{{ imageFile.picture.kb || '0' }}Kbs</strong></li>
                        </ul>
                    </div>
                </div>
                -->

                <input type="hidden" :name="name + '_id[]'"       :value="(imageFile.picture.id || 0)">
                <input type="hidden" :name="name + '_indice[]'"   :value="imageFile.picture.indice">
                <input type="hidden" :name="name + '_foto[]'"     :value="imageFile.picture.foto">
                <input type="hidden" :name="name + '_tipo[]'"     :value="imageFile.picture.tipo">
                <input type="hidden" :name="name + '_kb[]'"       :value="imageFile.picture.kbs">
                <input type="hidden" :name="name + '_camara[]'"   :value="imageFile.picturecamara">
                <input type="hidden" :name="name + '_latitud[]'"  :value="imageFile.picture.latitud">
                <input type="hidden" :name="name + '_longitud[]'" :value="imageFile.picture.longitud">
                <input type="hidden" :name="name + '_visible[]'"  :value="active ? 1 : 0">
            
            </div>
                <!--Iconos | actualmente no se usa
                <div class="contenedor-iconos">
                    <div class="contenedor-icono"
                         v-for="tipo in tipos"
                         :key="tipo.id"
                         :class="item.tipo == tipo.id ? 'seleccionado' : ''"
                         :title="tipo.nombre"
                         data-tippy-placement="top"
                         @click="actualizarTipo(tipo.id)"
                    >
                        <figure class="icono" :class="tipo.clase" :style="'background-image:url(' + urlIcono(tipo.icono) + ')'"></figure>
                    </div>
                </div>
                -->
            <!--</div>-->
        </template>
        <!--Fin Galeria Item-->
    </div>

</template>

<script>

const defaultFormat = 'YYYY-MM-DD HH:mm:ss';

export default {
    
    name: "GaleryItem.vue",

    props: {
        source: String, //Controlador al que se manda la imagen
        action: {
            type: String,
            default: "subirImagen"
        },

        id_presolicitud: Number,

        item: Object,

        template: Object,

        urls: null ,

        width: {
            type: Number,
            default: 200
        },

        name: {
            type: String,
            default: "item-galeria"
        },

        label: String,

        currentStage: Number,

        galeryItem: {
            
            type: Object,
            default: () => ({
                name: "otro",
                picture: {
                    id: 0
                },
            })
            
        },

        disabled: Boolean,

        imageFile: {

            type:Object,
            default: () => ({
                picture: {
                    _key: 0,
                    id: 0,
                    nombre: '',
                    tipo: 1,
                    foto: '',
                    visible: true,
                    indice: 0,
                    ancho: 0,
                    alto: 0,
                    kbs: 0,
                    camara: '',
                    latitud: '',
                    longitud: '',
                },
                template: {},
                observations: ''
            })

        }
    },

    data: () => ({

        imageWasLoaded: false,
        pictureInput: '',
        picture: '',
        uploading: false, //Desata la animación de carga
        active: true,
        loading: false,
        are_actions_expanded: false,
        timestamp: 0,

    }),

    methods: {

        //Metodos para el objeto galeria

        //Toggle el estado de cargad de la imagen
        setLoadingState(state) {
            if (state) {
                this.$refs['main_item'].classList.add('cargando');
            } else {
                this.$refs['main_item'].classList.remove('cargando');
            }
        },

        expandActions() {
            if(!this.disabled)
                this.are_actions_expanded = true;
        },

        hideActions() {
            this.are_actions_expanded = false;
        },

        expandImageInGalery() { //Cambia la visibilida de un objeto galeria

            this.$emit("expand", this.imageFile.index);

        },

        remove() {
            this.$emit('eliminarItem', this.item._key);
        },

        async rotate() {
            
            try {   

                this.setLoadingState(true);

                const response = await this.$http.post(this.$url_post, {
                    _fuente: 'IntranetPresolicitudDocumento',
                    _accion: 'rotarImagen',
                    nombre: this.imageFile.picture.nombre,
                    id: this.imageFile.picture.id,
                    id_presolicitud: this.id_presolicitud
                });

                this.debugStuff( response, "hotpink", "Respuesta rotar imagen");

                if (response.status === 200) {
                    //resultadoSolicitudDefecto(response.data);

                    const self = this;
                    if (response.data.ok) {
                         //se cambia el timestamp para que se actualice la foto en el navegador
                        this.timestamp = ( new Date() ).getTime();
                        //se invierte el valor de ancho y alto
                        const ancho = self.imageFile.picture.ancho;
                        self.imageFile.picture.alto = self.imageFile.picture.alto;
                        self.imageFile.picture.ancho = ancho;
                        this.debugStuff(self, "hotpink","self")
                        //self.$emit('ItemRotado', self.item._key);
                    }
                }

            } catch (error) {
                this.debugError(error);
                mensajeError('Error de servidor.');
            }
            this.setLoadingState(false);
        },

        makeNewPicture(element){ //Carga la foto al comoponente

            this.uploading = true;
            const $input_origin = $(element);
            const self = this

            ///("here");

            this.$emit("imageChange", $input_origin , this.imageFile,  function(){

                self.uploading = false;

            })

        },

        async updateName() {

            try{

                const form = this.createFormData({
                    _fuente: 'IntranetPresolicitudDocumento',
                    _accion: 'addObservacion',
                    id_documento: this.imageFile.picture.id_picture,
                    id: this.imageFile.picture.id_picture,
                    observacion: this.$refs['input_nombre'].value
                });

                const response = await this.$http.post( this.urls.post, form , this.$defaultConfig );

                this.debugStuff( response , "hotpink", "Respuesta observaciones" );


            }catch(error){

                this.debugError(error);

            }

            this.$emit('nombreActualizado', this.$refs['input_nombre'].value, this.item._key);
        },


        /*urlIcono(icono) {
            return this.$public_dir + 'img/' + icono;
        }*/

    },

    computed: {

        pictureUrl() {

            alert("aaaa");
            this.debugStuff(this.imageFile,"green","imageFile");

            if ( (typeof this.imageFile.picture.name !== 'string' || !this.imageFile.picture.name.length) && (typeof this.imageFile.picture.nombre !== 'string' || !this.imageFile.picture.nombre.length ) ) {
                return this.$img_placeholder;
            }

            let cache = '';

            if (this.timestamp) {
                cache = '?c=' + this.timestamp.toString();
            }

            const pictureName = this.imageFile.picture ? this.imageFile.picture.nombre : this.picture.name

            return this.$uploads_img_dir + 'm/' + pictureName + cache;
        },


    }

}

</script>

<style scoped>

    .galery-item {
        width: 300px !important;
        height: 200px !important;
    }

    .galery-item-raw {
        width: 300px !important;
        height: 200px !important;
    }

    .image-container{
        min-height: 0 !important;

    }

    .galeria-item.inactivo {
        opacity: .6;
    }

    .galeria-item.cargando {
        filter: blur(1px);
        pointer-events: none;
    }

    .freelancer-overview {
        position: relative;
        min-height: 250px;
    }

    .freelancer-details {
        padding: 5px;
    }

    .freelancer-details-list ul {
        text-align: center;
        margin-bottom: -12px;
    }

    .custom-input {
        border: 0;
        box-shadow: none;
        margin-bottom: 0;
    }

    .item-actions .bookmark-icon {
        visibility: hidden;
        transition-delay: .1s;
    }

    .item-actions .bookmark-icon:last-child {
        visibility: visible;
    }

    .item-actions .remove {
        background-color: red;
    }

    .item-actions.expandido .bookmark-icon:nth-child(1)  { visibility: visible; top:  80px !important; }
    .item-actions.expandido .bookmark-icon:nth-child(2)  { visibility: visible; top: 125px !important; }

    .contenedor-iconos {
        width: 100%;
        display: flex;
        justify-content: space-around;
        margin: 5px 0;
        background-color: #fff;
        border-radius: 20px;
        padding: 6px;
    }

    .contenedor-icono {
        cursor: pointer;
    }
    .contenedor-icono:hover .icono {
        opacity: .8;
    }
    /*.contenedor-icono.seleccionado {
        background-color: #2a41e8;
        width: 34px;
        height: 34px;
        padding: 5px;
        margin-top: -5px;
    }*/

    .picture-title{
        font-weight: bold;
        text-align: center;
    }

    .icono {
        width: 24px;
        height: 24px;
        background: transparent no-repeat center center;
        background-size: cover;
        margin: 0;
        filter: brightness(0);
    }
    .contenedor-icono.seleccionado .icono {
        /*filter: brightness(0) invert(1);*/
        /*filter: hue-rotate(180deg);*/
        filter: unset;
    }
</style>