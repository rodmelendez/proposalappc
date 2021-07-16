<template>
    <main>
        <section class="index">
            <div class="dashboard-headline">
                <div class="row">
                    <div class="col-sm-8">
                        <h3>
                            {{ title }}
                            <span class="item-cargando">&nbsp;<i class="icon-line-awesome-hourglass-2 fa-spin"></i></span>
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
                            <i class="icon-material-outline-search"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <slot name="filters"></slot>

            </div>

            <div class="row">
                <div class="table-container">
                    <table>
                        <slot name = "header" :headers="headers">
                            <thead class="header">
                                <tr>
                                    <th v-for="(header, index) in headers" :key="index">
                                        {{header}}
                                    </th>
                                </tr>
                            </thead>
                        </slot>

                        <slot name = "body" :items="data">
                            <tbody class="tbody">
                                <tr v-for="(item, index) in filteredData" :key="index">
                                    <td v-for="(accesor, _index) in accesors" :key="`${index}_${_index}`"  >
                                        {{ item[accesor] || '' }}    
                                    </td>
                                </tr>
                            </tbody>
                        </slot>

                    </table>
                </div>
            </div>
            
        </section>
    </main>
</template>

<script>
export default {
    name: "CrudTable",

    props:{
        title: String,
        columns: Array,
        data: Array,
        filterData: Object,
        filters: Array
    },

    computed: {

        accesors: function(){

            const accesors = this.columns.map( col => col.accesor || '' );

            return accesors;

        },

        headers: function(){

            const headers = this.columns.map( col => (
                {
                    header: col.header || '', 
                    filter: col.filter,
                    data: this.data,
                    accesor: col.accesor,
                    filterData:  this.filterData[col.accesor] || []
                } 
            ));

            return headers;

        },

        filteredData: function(){

            if(!this.filters.length){
                return this.data;
            }

            const filterData = this.data.filter( d => {

                let flag = false;
                
                const tested = this.filters.filter( filter => {

                    return filter(d)

                })

                return tested.length;

            });

            return filterData;


        }

    }

}
</script>

<style>

    .table-container{
        display: flex;
        overflow-x: scroll;
    }

    table {
        border-collapse: collapse;
        width: 100%;
    }

    th, td {
        min-width: 250px;
        padding: 0.8rem;
        text-align: left;
    }

    .theader{
        display: flex;
        transition: all 200ms ease;
        cursor: pointer;
    }

    .header{
        display: block;
    }

    .tbody{
        min-height: 300px;
        display: block;
    }

    th{
        font-size: 0.75rem;
        color: #a2a2a2;
    }

    td{
        color: #272e38;
    }

    .theader:hover .filter-container .caret-icon{
        visibility: visible;
    }

    .theader:hover{
        font-size: .8rem;
        color: #272e38;
    }

    .filters-box{
        display:flex;
        margin: 0 auto;
        font-size: .8rem;
    }

    .filter-title{
        font-weight: bold;
        color: #272e38;
    }

    .filters-box .filters{
        display: flex;
    }

    .filters .tooltip{
        margin-left: .8rem;
        color: #a2a2a2;
    }

    .filters .clear-all{
        margin-left: .8rem;
        font-weight: bold;
        color: #272e38;
        cursor: pointer;
    }

    .filters .clear-all:hover{
        color: blue;
    }

    .quit-filter:hover{
        cursor: pointer;
        color:blue;
    }

</style>