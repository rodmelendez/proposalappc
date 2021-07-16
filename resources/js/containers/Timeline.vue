<template>

<div class="page">

    <div class="timeline-duration">
        <span> {{ totalDuration }} </span>

        <div class ="timeline-stage-duration">

            <span v-for="stage in stages" :key="stage.id" class="timeline-breakdown"><i :class="stage.icono" /> {{ stage.duration }} </span>
        </div>
    </div>

<div class="timeline">

    <div v-for="(event,index) in events" :key="index" class="timeline-group" >

        <span class="timeline-year"> {{event.title}} </span>

        <div 
            v-for="(subEvent, id) in event.events" 
            :key="`${event.title}-${id}`" 
            class="timeline-box"
        >

            <div class="timeline-date">
                <span class="timeline-day"> {{ getDayFromDate(subEvent.date) }} </span>
                <span class="timeline-month"> {{ getMonthFromDate( subEvent.date ) }} </span>
            </div>
            <div class="timeline-post">
                <div class="timeline-content">
                    <p class="font-weight-bold"> {{ subEvent.description }} </p>
                    <span class="observations">Observaciones: {{ subEvent.observation}} </span>
                </div>
                <div class="timeline-footer">
                    <p> {{ `${subEvent.footer}${' '}${getTimeHumanize(subEvent.date)}` }} </p>
                    <span> {{ subEvent.date }}   </span>
                </div>
            </div>


        </div>

    </div>

</div>
</div>

<!--
<div class="page">
    <div class="timeline">
        <div class="timeline__group">
            <span class="timeline__year">2008</span>
            <div class="timeline__box">
                <div class="timeline__date">
                    <span class="timeline__day">2</span>
                    <span class="timeline__month">Feb</span>
                </div>
                <div class="timeline__post">
                    <div class="timeline__content">
                        <p>Attends the Philadelphia Museum School of Industrial Art. Studies design with Alexey Brodovitch, art director at Harper's Bazaar, and works as his assistant.</p>
                    </div>
                </div>
            </div>
        <div class="timeline__box">
            <div class="timeline__date">
            <span class="timeline__day">1</span>
            <span class="timeline__month">Sept</span>
            </div>
            <div class="timeline__post">
            <div class="timeline__content">
                <p>Started from University of Pennsylvania. This is an important stage of my career. Here I worked in the local magazine. The experience greatly affected me</p>
            </div>
            </div>
        </div>
    </div>
    <div class="timeline__group">
      <span class="timeline__year">2014</span>
      <div class="timeline__box">
        <div class="timeline__date">
          <span class="timeline__day">14</span>
          <span class="timeline__month">Jul</span>
        </div>
        <div class="timeline__post">
          <div class="timeline__content">
            <p>Travels to France, Italy, Spain, and Peru. After completing fashion editorial in Lima, prolongs stay to make portraits of local people in a daylight studio</p>
          </div>
        </div>
      </div>
    </div>
    <div class="timeline__group">
      <span class="timeline__year">2016</span>
      <div class="timeline__box">
        <div class="timeline__date">
          <span class="timeline__day">28</span>
          <span class="timeline__month">Aug</span>
        </div>
        <div class="timeline__post">
          <div class="timeline__content">
            <p>Upon moving to Brooklyn that summer, I began photographing weddings in Chicago</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
-->



</template>

<script>

import moment from 'moment';
import {getDurationBetween} from '../utils/functions';
import 'moment/locale/es';

export default {
    name: "Timeline.vue",

    props: {
        events: Array,
        totalDuration: String,
        stages: Array,
    },

    mounted(){
        moment.locale('es');
    },

    methods: {

        getMonthFromDate(date){
            return moment(date).format("MMMM");
        },

        getDayFromDate(date){
            return moment(date).format("D");
        },

        getTimeHumanize(date){
            
            return moment.duration( getDurationBetween( date, null ) , "hours" ).humanize(true);

        }

    }
}
</script>

<style scoped>

    .page{
        max-width: 800px;
        padding: 2rem 2rem 3rem;
        margin-left: auto;
        margin-right: auto;
        order: 1;
    }

    .timeline{

        --uiTimelineMainColor: var(--timelineMainColor, #222);
        --uiTimelineSecondaryColor: var(--timelineSecondaryColor, #fff);

        position: relative;
        padding-top: 3rem;
        padding-bottom: 3rem;
        
    }

    .timeline:before{
        content: "";
        width: 4px;
        height: 100%;
        background-color: var(--uiTimelineMainColor);

        position: absolute;
        top: 0;
    }

    .timeline-group{
        position: relative;
    }

    .timeline-group:not(:first-of-type){
        margin-top: 4rem;
    }

    .timeline-year{
        padding: .5rem 1.5rem;
        color: var(--uiTimelineSecondaryColor);
        background-color: var(--uiTimelineMainColor);

        position: absolute;
        left: 0;
        top: 0;
    }

    .timeline-box{
        position: relative;
    }

    .timeline-box:not(:last-of-type){
        margin-bottom: 30px;
    }

    .timeline-box:before{
        content: "";
        width: 100%;
        height: 2px;
        background-color: var(--uiTimelineMainColor);

        position: absolute;
        left: 0;
        z-index: 0;
    }

    .timeline-date{
        min-width: 65px;
        position: absolute;
        left: 0;

        box-sizing: border-box;
        padding: .5rem 1.5rem;
        text-align: center;

        background-color: var(--uiTimelineMainColor);
        color: var(--uiTimelineSecondaryColor);
        z-index: 20;
    }

    .timeline-day{
        font-size: 2rem;
        font-weight: 700;
        display: block;
    }

    .timeline-month{
        display: block;
        font-size: .8em;
        text-transform: uppercase;
    }

    .timeline-duration{
        font-size: .8rem;
        text-transform: uppercase;
        display: block;
        padding: 1rem;
        color: white;
        background-color: #4557bb
    }

    .timeline-post{
        position: relative;
        padding: 1.5rem 2rem;
        border-radius: 2px;
        border-left: 3px solid var(--uiTimelineMainColor);
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, .12), 0 1px 2px 0 rgba(0, 0, 0, .24);
        background-color: var(--uiTimelineSecondaryColor);
        z-index: 30;
    }

    .timeline-stage-duration{
        display: flex;
        flex-direction: column;
    }

    .timeline-stage-duration span{
        margin-bottom: .5rem;
    }

    .timeline-footer p {
        color: #757575;
        font-size: .8rem;
    }

    .font-weight-bold{
        font-weight: bold;
    }

    @media screen and (min-width: 641px){

        .timeline:before{
            left: 30px;
        }

        .timeline-group{
            padding-top: 55px;
        }

        .timeline-box{
            padding-left: 125px;
        }

        .timeline-box:before{
            top: 50%;
            transform: translateY(-50%);  
        }  

        .timeline-date{
            top: 50%;
            margin-top: -27px;
        }
    }

    @media screen and (max-width: 640px){

        .timeline:before{
            left: 0;
        }

        .timeline-group{
            padding-top: 40px;
        }

        .timeline-box{
            padding-left: 20px;
            padding-top: 70px;
        }

        .timeline-box:before{
            top: 90px;
        }    

        .timeline-date{
            top: 0;
        }
    }

    .timeline{
        --timelineMainColor: #4557bb;
        font-size: 16px;
    }


</style>