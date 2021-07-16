<template>
    <div class="filter-container">
        <span class="caret-icon" @click="expandFilter">
            <i class="las la-angle-down"></i>
        </span>
        <div :class="{filter: true, active: showFilter}" @click.stop>
            <input-texto
                v-model="range.min"
                :value="range.min"
                etiqueta="Minimo"
                nombre="input-min"
                class="input-filter"
                type="number"
            />
            <input-texto
                v-model="range.max"
                :value="range.max"
                etiqueta="Maximo"
                nombre="input-max"
                class="input-filter"
                type="number"
            />
            <span class="coin-container">
                <i class="simbolo-moneda" @click="changeCoin">
                    {{ currentCoin.simbolo }}
                </i>
            </span>

            <button v-show="range.min && range.max" class="icon-button" @click="handleClick">
                <i class="las la-angle-right"></i>
            </button>
        </div>
    </div>
</template>

<script>

import monedas from '../monedas.json';

export default {
    name: "NumberFilter",

    props:{
        data: Array,
        accesor: String
    },

    data: () => ({
        currentCoin: monedas[0],
        showFilter: false,
        range: { min: '' , max: ''}
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

        handleClick(){

            if(this.range.min && this.range.max){
                this.$emit('filterChange',{...this.range, coin: this.currentCoin},"numberFilter")
                Object.assign( this.range , { min: '' , max:'' });
            }

        },

        changeCoin(){

            this.currentCoin = parseInt(monedas[1].id) === parseInt(this.currentCoin.id) ? monedas[0] : monedas[1];

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
        min-width: 130px;
        width: 350px;
    }

    .filter{
        display: none;
        position: absolute;
        min-width: 200px;
        width: 385px;
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

    .coin-container{
        display: flex;
        width: 30px;
        height: 30px;
        position: absolute;
        bottom: 39px;
        background-color: #2196F3;
        border-radius: 50%;
        right: 16%;
        align-items: center;
        justify-content: center;
        color: #fff;
    }

    .simbolo-moneda{
        font-weight: bold;
        font-style: normal;
        font-size: 1rem;
        user-select: none;
    }

</style>