<template>
    
    <div class ="galery-container">
        
        <button @click="closeGalery" class="galery-button close-button">
            <i class="las la-times"></i>
        </button>


        <div class="viewing-container">
            <button @click="previousPicture" class="galery-button">
               <i class="las la-angle-left"></i>
            </button>
            <div class="portrait">

                <img :src="currentPicture.url"/>
                <div class="picture-details">
                    <div class="freelancer-details-list">
                        <ul>
                            <li>Ubicación 
                                <strong>
                                <a 
                                    :href="'http://www.google.com/maps/place/' + currentPicture.latitud + ',' + currentPicture.longitud" 
                                    v-if="typeof currentPicture.longitud === 'string' && currentPicture.longitud.length" 
                                    target="_blank"
                                >
                                    <i class="icon-material-outline-location-on"></i>
                                </a>
                                <span v-else>
                                    &mdash;
                                </span>
                                </strong>
                            </li>
                            <li>Dimensiones <strong>{{ currentPicture.ancho || '' }}x{{ currentPicture.alto || '' }}</strong></li>
                            <li>Tamaño <strong>{{ currentPicture.kb || '0' }}Kbs</strong></li>
                        </ul>
                    </div>
                </div>
            </div>
            <button @click="nextPicture" class="galery-button">
                <i class="las la-angle-right"></i>
            </button>
        </div>


        <div class="thumbnail-container">

            <div class="thumbnail-box">

                <div 
                    class="thumbnail" 
                    :class="{active: index === currentPicture.index}"
                    v-for="(picture, index) in pictures"
                    :key="index"
                >
                    <img :src="picture.url"/>
                </div>

            </div>

        </div>

    </div>

</template>

<script>
export default {
    name: "Galery",

    props:{
        picturesArray: Array,
        startPictureId: Number
    },

    data: () => ({
        index: null
    }),

    methods:{

        previousPicture(){

            if(this.index - 1 < 0 )
                this.index = this.pictures.length - 1;
            
            else
                this.index = (this.currentPicture.index - 1)
        },

        nextPicture(){
            this.index = (this.currentPicture.index + 1) % this.pictures.length;
        },

        closeGalery(){
            this.$emit("closeGalery");
        }

    },


    computed: {

        pictures: function(){


            this.debugStuff(this.picturesArray, "hotpink", "imagenes para mostrar en galeria");

            const thePictures = (this.picturesArray || [])
            .filter(p => p.picture && p.picture.nombre)
            .map( p => ({
                url: `${this.$uploads_img_dir}m/${p.picture.nombre}`
            }));

            return thePictures;

        },

        currentPicture: function(){

            if(this.index){
                return { 
                    url: `${this.$uploads_img_dir}m/${this.picturesArray[this.index].picture.nombre}`, 
                    index: this.index,
                    ...this.picturesArray[this.index].picture
                };
            }

            return { 
                url: `${this.$uploads_img_dir}m/${this.picturesArray[this.startPictureId].picture.nombre}`, 
                index: this.startPictureId,
                ...this.picturesArray[this.startPictureId].picture
            };

        }

    },

    mounted(){

        const galery = this.$el;

        this.debugStuff(this.$el.parentNode,"hotpink", "node");

        if(this.$el.parentNode)
            this.$el.parentNode.removeChild(this.$el);

        document.getElementById("app").appendChild(galery);
    },

    beforeDestroy(){


    }

}
</script>

<style scoped>

    .galery-container{
        width: 100vw;
        height: 100vh;
        background: #000;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1000;
    }

    .picture-details{
        width:100%;
        height:10%;
    }

    .viewing-container{
        width: 100%;
        height: 80%;
        display: flex;
        padding: 1rem;
    }

    .close-button{
        position: fixed;
        right: 5%;
        top: 5%;
    }

    .thumbnail-box{
        display: flex;
        padding: 1rem 2rem;
        height: 100%;
    }

    .thumbnail-container{
        width: 100%;
        height: 20%;
        padding: .25rem 1rem;
    }

    .thumbnail{
        width: 150px;
        height: 100%;
        margin-right: 2rem;
        text-align: center;
        transition: all 500ms ease;
    }

    .active{
        transform: scale(1.2);
    }

    .thumbnail img{
        max-width: 100%;
        height: 100%;
    }

    .galery-button{

        font-size: 50px;
        display: flex;
        vertical-align: middle;
        color: #757575;
        transition: all 500ms ease;

    }

    .galery-button:hover {
        font-size: 60px;
        color: #007bff;
    }

    .portrait{
        width: 90%;
        height: 100%;
        text-align: center;
    }

    .portrait img{
        height: 90%;
        width: auto;
    }

</style>