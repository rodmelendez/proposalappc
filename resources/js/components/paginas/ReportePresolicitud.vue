<template>
    <crud-table
        :title="title"
        :columns="columns"
        :data="parseData"
        :filters="filtersFunc"
        :filterData="filterData"
    >
        <template slot="filters">
            <div class="filters-box" v-if="filters.length">
                <span class="filter-title">Filtros: </span>
                <div class="filters">
                    <div
                        class="tooltip" 
                        v-for="(filter, index) in filters" 
                        :key="index"
                    > 
                        {{filter}} 
                        <span class="quit-filter" @click="quitFilter(index)"><i class="las la-times"></i></span>
                    </div>
                    <div
                        @click="quitAllFilters"
                        class="clear-all"
                    >
                        Limpiar Todo
                    </div>
                </div>
            </div>
        </template>

        <template slot="header" slot-scope="slotProps">
            <thead class="header">
                <tr>
                    <th v-for="(header, index) in slotProps.headers" :key="index">
                        <div class="theader">
                            {{header.header}}
                            <component 
                                v-if="header.filter"
                                :is="header.filter" 
                                :data="header.filterData"
                                :accesor="header.accesor"
                                @filterChange="addFilter"
                            ></component>
                        </div>
                    </th>
                </tr>
            </thead>
        </template>

    </crud-table>
</template>

<script>

    import ListFilter from '../../filters/ListFilter.vue';
    import DateFilter from '../../filters/DateFilter.vue';
    import NumberFilter from '../../filters/NumberFilter.vue';
    import StageFIlter from '../../filters/StageFilter.vue';

    import {getDurationBetween} from '../../utils/functions';
    import moment from 'moment';

    export default {
        name: "Reporte-presolicitud",

        components:{
            'list-filter' : ListFilter,
            'date-filter' : DateFilter,
            'number-filter': NumberFilter,
            'stage-filter': StageFIlter
        },

        props: {
            urls: Object,
            avatar_defecto: String
        },

        methods:{

            cargarDataAdicional(){

                const self = this;
                this.$http.get(this.urls.get, {
                    params: {
                        _fuente: this.fuente,
                        _accion: 'cargarListados',
                    }
                })
                .then(response => {

                    this.debugStuff(response,"hotpink", "dataAdicional")

                    if (response.status === 200) {
                        const data = response.data;
                        this.filterData.nombreCliente = data["clientes"].map( client => ({name: client.nombre}) );
                        this.filterData.nombreProducto = data["productos"].map( product => ({name: product.nombre}) );
                        this.$forceUpdate();
                    }
                })
                .catch(err => {
                    self.debugError(err);
                });
            },

            async loadPreapplications(){

                try{

                    const response = await this.$http.get(
                        this.urls.get ,
                        {
                            params: {
                                _fuente: this.fuente,
                                _accion: 'presolicitudes'
                            }
                        }
                    )

                    this.debugStuff(response, 'hotpink', 'obtener presolicitudes');

                    this.preapplications = [...response.data["presolicitudes"]];

                }catch(error){

                    this.debugError(error);
                }

            },

            addFilter( event , type ){

                this.debugStuff( type , "hotpink", "type" );

                switch(type){

                    case 'dateFilter':{
                        const range = event;
                        const start = moment(range.start).format("YYYY-MM-DD");
                        const end   = moment(range.end).format("YYYY-MM-DD");

                        this.$set( this.filters, this.filters.length , `${start} - ${end}`);
                        
                        const filterFunc = (data) => {

                            const isBetween = moment( data.fecha_solicitud ).isBetween( start, end ) 

                            return isBetween
                        }       

                        this.$set( this.filtersFunc , this.filtersFunc.length , filterFunc)

                        return;
                    }

                    case 'numberFilter':{
                        const range = event;
                        const min   = range.min;
                        const max   = range.max;

                        this.$set( this.filters, this.filters.length , `${min}${range.coin.simbolo} - ${max}${range.coin.simbolo}`);

                        const filterFunc = (data) => {

                            const isBetween = parseInt(data.moneda) === parseInt(range.coin.id) && parseFloat(data.monto_solicitado) >= min && parseFloat(data.monto_solicitado) <= max;

                            return isBetween
                        }  

                        this.$set( this.filtersFunc , this.filtersFunc.length , filterFunc);

                        return;
                    }

                    case 'stageFilter': {

                        const stage = event;

                        this.$set( this.filters, this.filters.length , `${stage.nombre}`);

                        const filterFunc = (data) => {

                            const belongToStage = parseInt(data.estado_etapa) === stage.id

                            return belongToStage
                        }  

                        this.$set( this.filtersFunc , this.filtersFunc.length , filterFunc);

                    }

                    case 'listFilter': {

                        const checked = event.target.checked;
                        const value   = event.target.value;

                        this.debugStuff( {checked,value} , "hotpink", "addFilter" );

                        if(checked){
                            this.$set( this.filters, this.filters.length , value );
                            this.$forceUpdate();

                            const filterFunc = (data) => {

                                const belongToClient = data.nombreCliente === value;

                                return belongToClient
                            }  

                            this.$set( this.filtersFunc , this.filtersFunc.length , filterFunc);

                        }else{

                            const index = this.filters.findIndex( x => x.indexOf(value) > -1 );

                            this.debugStuff({index} ,"hotpink" ,"indice")

                            if(index > -1){

                                this.filters.splice( index , 1 );
                                this.filtersFunc.splice( index , 1 );


                                this.$forceUpdate();
                            }

                        }

                    }
                }

            },

            quitFilter(indexOfFilter){

                 this.filters.splice(indexOfFilter,1);
                 this.filtersFunc.splice(indexOfFilter,1);

                this.$forceUpdate()
                
            },

            quitAllFilters(){
                this.filters = [];
                this.filtersFunc = [];
            }
        },

        data: () => ({
            title: "Presolicitudes",
            titleSingular: "Presolicitud",
            
            filterData : {
                nombreCliente: [],
                nombreProducto: []
            },

            preapplications: [],

            columns: [ 
                { header: "N° Presolicitud" , accesor: "id"                                                                       },
                { header: "Usuario"         , accesor: "nombreCreadorCredito"                                                     },
                { header: "Cliente"         , accesor: "nombreCliente"       , filter: 'list-filter' ,                            },
                { header: "Producto"        , accesor: "nombreProducto"      , filter: 'list-filter' ,                            },
                { header: "Fecha"           , accesor: "fecha_solicitud"     , filter: 'date-filter'                              },
                { header: "Monto"           , accesor: "monto_solicitado"    , filter: 'number-filter'                            },
                { header: "Meses"           , accesor: "plazo_solicitado"                                                         },
                { header: "Etapa"           , accesor: "estado_etapa"        , filter: 'stage-filter'                             },
                { header: "Duracion"        , accesor: "duracion"                                                                 },
                { header: "Estado"          , accesor: "estado"                                                                   }
            ],

            filters: [],
            filtersFunc: [],
            
            fuente: "IntranetPresolicitud",
            icono: "las la-donate",

            item: {},
            items: []
        }),

        mounted(){
            this.cargarData();
            this.loadPreapplications();
        },

        computed:{

            parseData: function(){

                return this.preapplications.map( item => {

                    //Fecha con la que se hace la comparacion
                    
                    const discriminator = item.etapas.length ?  item.etapas[0].fecha_registro : item.fecha_creacion || item.fecha_solicitud;

                    const duration = parseFloat( getDurationBetween( null , discriminator ) );

                    this.debugStuff(duration)

                    const timeSpend = moment.duration( parseFloat(duration) , "hours" );

                    const durationString =  `${timeSpend.years() ? timeSpend.years() + " Años" : ""} ${timeSpend.months() ? timeSpend.months() + " Mese(s)" : ""} ${timeSpend.days() ? timeSpend.days() + " Dia(s)" : ""} ${timeSpend.hours() ? timeSpend.hours() + " Hora(s)" : ""}  ${timeSpend.minutes()} Minuto(s)`;

                    return({...item, duracion: `${durationString}` , estado: item.estado_vida ? "En proceso" : "Rechazado"} )

                });

            }

        }
    }
</script>

