<template>
    <div class = "no-outline">
        <input 
            class="file-input"
            type="file"
            :ref="name"
            :id="fileId"
            :disabled="disabled"
            @change="handleFileInputChange"
        >
        <tooltip 
            interactive 
            :animate-fill="false" 
            placement="top" 
            animation="fade" 
            arrow
        >
            <template slot="trigger">
                <label 
                    :for="fileId" 
                    :id="fileLabelId"
                    class = "file-container"
                >
                    <span :class="fileClass">
                        <span class="file-icon" >
                            <i  class="icon-feather-file"/>
                        </span>
                        <span class="file-label">
                            <span>
                                {{label}}
                            </span>
                        </span>
                    </span>
                </label>
            </template>

            <span class="file-actions">
                <div v-if="fileWasLoaded">
                    <span class="file-actions">
                        {{this.labelContent}}
                        <a 
                            class = "file-link"
                            :href="fileDownloadLink" 
                            :download="fileContent"
                        >   
                            <i class="icon-feather-download"></i>
                        </a>
                    </span>
                </div>
                <div v-else>
                    <strong>{{this.labelContent}}</strong>
                </div>
            </span>
        </tooltip>
    </div>
</template>

<script>
    export default {
        name: "Archivo",

        data: () => ({
            tooltip: null
        }),

        props: {
            id: String,

            //Archivo que ya ha sido cargado
            fileValue: null,

            loaded: {
                type: Boolean,
                default: false
            },

            label: {
                type: String,
                default: ""
            },

            name: {
                type: String,
                required: true
            },

            size: {
                type: String,
                default: "small"
            },

            disabled: {
                type: Boolean,
                default: false
            }
        },

        computed: {
            fileId(){
                return this.id || `${this.name}-file-input`
            },

            fileLabelId(){
                return `${this.id}-tooltip`
            },

            fileLabelTemplateId(){
                return `${this.id}-template`
            },

            fileClass(){
                return`file-${this.size}`
            },
            
            fileWasLoaded(){
                return this.fileValue && this.fileValue.document && this.fileValue.loaded;
            },  

            fileDownloadLink(){
                if( this.fileValue.document && this.fileValue.document.link )
                    return (this.$uploads_doc_dir + (this.fileValue.document.link || this.fileValue.document.nombre)) 

                return "#"
            },

            fileContent(){


                if(this.fileValue && this.fileValue.document && this.fileValue.document.nombre ){
                    const label =  document.getElementById(this.fileLabelId);

                    return this.fileValue.document.nombre
                }
                
                return "Archivo"

            },

            labelContent(){

                if(this.fileWasLoaded){
                    return `${this.label}`
                }

                return `${this.label} | No cargado`

            }
        },

        methods: {

            handleFileInputChange(event){
                
                const file = event.target.files[0];

                this.$emit('change',file,this.fileValue)

            },

            getFileName(){
                return this.label 
            }

        },
    }
</script>

<style>

    .file-container{
        position: relative;
        outline: 0;
    }

    .file-input{
        display: none;
    }

    .file-small{
        display: flex;
        flex-direction: column;
        justify-content: center;
        width: 100px;
        height: 100px;
        padding: 8px;
        border: 1px solid blue;
        border-radius: 10px;
        margin-right: 28px;
        cursor: pointer;
        transition: all 250ms cubic-bezier(0.4, 0, 0.2, 1) 0ms;
    }

    .file-small .file-icon{
        display: block;
        color: blue;
        place-content: center;
        display: flex;
        font-size: 3em;
    }

    .file-small .file-label > span{
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
        display: block;
        color: blue;
    }

    .file-small:hover{
        background-color: blue;
    }

    .file-small:hover  * ,  
    .file-small:hover .file-label > span{
        color: white;
    }

    /*File tooltip*/
    .file-tooltip{
        position: absolute;
        padding: 2px 4px;
        background-color: black;
        display: flex;
        color: white;
        top: -100px;
        left: 0;
    }

    .label-template{
        display: none;
    }

    .file-actions .file-link {
        color: red !important;
        transition: all 150ms ease;
    }

    .file-actions:hover .file-link {
        scale: 1.1;
    }
    
    .no-outline *{
        outline: 0;
    }

</style>