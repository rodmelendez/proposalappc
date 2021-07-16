<template>
    <main :class="cargando_items ? 'cargando' : ''">
        <div ref="calendario"></div>
    </main>
</template>

<script>
    export default {
        name: "Calendario.vue",

        props: {
            fecha: {
                type: String,
                default: '',
            },
            permitir_arrastrar: {
                type: Boolean,
                default: true,
            },
            botones: {
                type: String,
                default: 'mesAnterior,prev,next,mesSiguiente',
            },
            formato_titulo: {
                type: String,
                default: 'ddd, D MMM YYYY'
            },
            limite: {
                default: null,
            },
            eventos: {
                type: Array,
                default: () => [],
            },
            cargando: {
                type: Boolean,
                default: false,
            },
        },

        data: () => ({
            //items: [],
            _fecha: moment(),
            cargando_items: false,
        }),

        watch: {
            eventos() {
                this.actualizarItems();
            },

            cargando() {
                this.cargando_items = this.cargando;
            },
        },

        computed: {

        },

        created() {
            if (typeof this.fecha === 'string' && this.fecha.length) {
                this._fecha = moment(this.fecha, 'YYYY-MM-DD');
            }
        },

        mounted() {
            const $calendario = $(this.$refs['calendario']);

            console.log('allow', this.permitir_arrastrar);

            $calendario.fullCalendar({
                now: this._fecha,
                locale: 'es',
                editable: this.permitir_arrastrar,
                droppable: this.permitir_arrastrar,
                eventOverlap: false,
                height: 'auto',
                scrollTime: '00:00',
                allDaySlot: false,
                customButtons: {
                    mesAnterior: {
                        text: '',
                        icon: 'left-double-arrow',
                        click: this.accionMesAnterior
                    },
                    mesSiguiente: {
                        text: '',
                        icon: 'right-double-arrow',
                        click: this.accionMesSiguiente
                    }
                },
                header: {
                    left: this.botones,//'today prev,next',
                    center: 'title',
                    right: '', //timelineDay,timelineWeek,agendaWeek,month
                },
                defaultView: 'agendaWeek',
                views: {
                    agendaWeek: {
                        timeFormat: 'h(:mm)t',
                        slotLabelFormat: 'h(:mm)t',
                        slotLabelInterval: '01:00',
                        columnFormat: 'ddd D'
                    }
                },
                titleFormat: this.formato_titulo,
                slotDuration: '00:30:00',
                //slotWidth: '7',
                minTime: '07:00:00',
                maxTime: '23:00:00',
                visibleRange: this.limite,
                validRange: this.limite,
                events: this.listaItems(),
                /*drop: function(date, jsEvent, ui, resourceId) {
                    console.log('dropped', date.format(), resourceId);
                },*/
                //eventReceive: this.eventoArrastrarItem.bind(this), //cuando viene desde una fuente externa
                eventDrop: this.eventoMoverItem, //cuando ya está en el calendario
                eventResize: this.eventoMoverItem, //cuando se cambia la duración
                //eventRender: this.eventoMostrarItem.bind(this), //cuando se va a mostrar el item en el calendario
                eventClick: this.eventoClicItem, //cuando se hace clic en el item del calendario,
                viewRender: this.eventoCambioDeVista //cuando se cambia la fecha en el calendario
            });
        },

        methods: {
            accionMesAnterior() {
                const $calendario = $(this.$refs['calendario']);
                let mes = $calendario.fullCalendar('getDate');
                mes.add(1, 'months');
                $calendario.fullCalendar('gotoDate', mes);
                this._fecha = mes;
            },

            accionMesSiguiente() {
                const $calendario = $(this.$refs['calendario']);
                let mes = $calendario.fullCalendar('getDate');
                mes.subtract(1, 'months');
                $calendario.fullCalendar('gotoDate', mes);
                this._fecha = mes;
            },

            listaItems() {
                let items = [];

                for (const evento of this.eventos) {
                    items.push({
                        id: evento.id || uniqueNumber(),
                        start: evento['inicio'],
                        end: evento['fin'],
                        title: evento['nombre'] || '',
                    });
                }

                return items;
            },

            actualizarItems() {
                const $calendario = $(this.$refs['calendario']);
                $calendario.fullCalendar('removeEventSources');
                $calendario.fullCalendar('addEventSource', this.listaItems());
            },

            eventoMoverItem(e) {
                this.$emit('eventoCambiado', e.id, e.start.format('YYYY-MM-DD HH:mm'), e.end.format('YYYY-MM-DD HH:mm'), e);
            },

            eventoClicItem(e) {
                this.$emit('eventoSeleccionado', e);
            },

            eventoCambioDeVista(e) {
                this.$emit('cambiado', e.start.format('YYYY-MM-DD'), e.end.format('YYYY-MM-DD'), e.type);
            },

        },
    }
</script>

<style>
    .fc-event {
        background-color: #2a41e8;
    }

    .fc-event .fc-content {
        color: #fff;
    }

    .cargando .fc {
        pointer-events: none;
        filter: grayscale(1) opacity(.7);
    }
</style>