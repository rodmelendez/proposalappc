<template>
    <div class="filter-container">
        <span class="caret-icon" @click="expandFilter">
            <i class="las la-angle-down"></i>
        </span>
        <div :class="{filter: true, active: showFilter}" @click.stop>
            <input-seleccion
                v-model="stage"
                :items="stages"
                etiqueta="Etapa"
                nombre="input-stage"
                class="input-filter"
                @input="handleSelection"
            />
        </div>
    </div>
</template>

<script>

const stages = [
    {id: 1 , nombre: "Etapa de presolicitud" , icono: "las la-user"},
    {id: 2 , nombre: "Etapa de Analísis de Credito", icono: "las la-search-dollar"},
    {id: 3 , nombre: "Etapa de Supervisión de Crédito", icono: "las la-file-invoice"},
    {id: 4 , nombre: "Etapa de Comité de Crédito" , icono: "las la-users"},
    {id: 5 , nombre: "Etapa de Desembolso", icono: "las la-money-bill"}
];


export default {
    name: "StageFilter",

    props:{
        data: Array,
        accesor: String
    },

    data: () => ({
        showFilter: false,
        stage: '',
        stages: stages
    }),

    methods: {
        expandFilter(){

            this.showFilter = true;

            setTimeout( () => {
                ["click"].forEach( event => {
                    document.body.addEventListener( event, this.collapseFilter )
                })
            }, 100)

        },

        collapseFilter(){
            this.showFilter = false;

            setTimeout( () => {
                ["click"].forEach( event => {
                    document.body.removeEventListener( event, this.collapseFilter )
                })
            }, 100);
        },

        handleSelection(){

            
            const stage = this.stages[ this.stage - 1]; 
            this.debugStuff( stage, "hotpink", "Etapa" );

            this.$emit('filterChange',stage,"stageFilter");

        }
    },

    computed:{
        matchedData: function(){

            if( !this.search )
                return this.data

            const value = this.search.toLowerCase();

            return this.data.filter( d => d[this.accesor].toLowerCase().indexOf(value) > -1 );

        }
    }
}
</script>

<style scoped>

    .filter-container{
        display: flex;
        width: 20px;
        height: 20px;
        margin-left: .8rem;
        transition: all 500ms ease;
        position: relative;
    }

    .caret-icon{
        color: #272e38;
        visibility: hidden;
    }

    .input-filter{
        margin: 0;
        min-width: 150px;
    }

    .filter{
        display: none;
        position: absolute;
        min-width: 200px;
        border-radius: 5px;
        top: 160%;
        left: 0;
        color: #272e38;
        background-color: #fff;
        border: 1px solid ;
        padding: 1rem;
    }

    .list ul{
        list-style-type: none;
        margin: 0;
        padding: 0;
        color: #a2a2a2;
    }

    .active{
        display: flex;
    }

    .list-item{
        display: flex;
    }

    .input-filter{
        margin: 0;
    }

    .checkbox-container {
        display: block;
        position: relative;
        padding-left: 35px;
        margin-bottom: 12px;
        cursor: pointer;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    .checkbox-container input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
        height: 0;
        width: 0;
    }

    /* On mouse-over, add a grey background color */
    .checkbox-container:hover input ~ .checkmark {
        background-color: #ccc;
    }

    /* When the checkbox is checked, add a blue background */
    .checkbox-container input:checked ~ .checkmark {
        background-color: #2196F3;
        border-radius: 5px;
        border: none;
    }

    /* Create the checkmark/indicator (hidden when not checked) */
    .checkmark:after {
        content: "";
        position: absolute;
        display: none;
    }

    /* Show the checkmark when checked */
    .checkbox-container input:checked ~ .checkmark:after {
        display: block;
    }

    /* Style the checkmark/indicator */
    .checkbox-container .checkmark:after {
        left: 10px;
        top: 7px;
        width: 5px;
        height: 10px;
        border: solid white;
        border-width: 0 3px 3px 0;
        -webkit-transform: rotate(45deg);
        -ms-transform: rotate(45deg);
        transform: rotate(45deg);
    }

    .checkmark {
        position: absolute;
        top: 0;
        left: 0;
        height: 25px;
        width: 25px;
        background-color: #eee;
        border: 1px solid #a2a2a2;
        border-radius: 5px;
    }

    .icon-button{
        FONT-WEIGHT: 100;
        background-color: #2196F3;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        color: #fff;
        position: absolute;
        right: 5%;
        bottom: 30%;
    }

</style>