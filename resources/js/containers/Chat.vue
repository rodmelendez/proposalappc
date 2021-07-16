<template>
    
    <div class ="chat-container">

        <div class ="chat-messages" ref="chat">

            <div
                class="chat-message"
                v-for="(msg,index) in messages"
                :key="msg.id"
            >
                <div class="user-info">
                    {{msg.sender}} <span class="message-date">{{msg.date}} </span>
                </div>
                <div class ="message-body question">
                    {{msg.body}}
                    <div class="message-actions">
                        <i class="las la-reply clickable" @click="replyMessage(msg)"></i>
                        <i class="las la-eye clickable" @click="showAnswer(msg, index)"></i>
                    </div>
                </div>

                <div class="chat-thread">

                    <div 
                        class="chat-answer"
                        v-for=" (answer, index) in msg.answers"
                        :key="index"
                    >
                        <div class="user-info">
                            <span class="message-date">{{answer.date}} </span>{{answer.sender}} 
                        </div>
                        <div 
                            class ="message-body answer"
                        >
                            {{answer.body}}
                        </div>
                    </div>


                    <div class="chat-input-thread input-with-icon margin-right-50" @keydown.enter="sendReply(msg, index)">
                        <input
                            v-if="viewingInputId === msg.id"
                            v-model="reply"
                            placeholder="Escriba respuesta aqui"
                            class="margin-none"
                        />
                        <i
                            class="las la-chevron-circle-right clickable margin-bottom-10"
                            v-if="reply !== '' && viewingInputId === msg.id"
                            @click="sendReply(msg, index)"
                        />
                    </div>

                </div>
            </div>

        </div>

        <div class ="chat-input" @click.stop  @keydown.enter="sendMessage">
            <div class="chat-keyboard">
                <input-texto
                    placeholder="Escribe un mensaje aqui"
                    nombre="chat-input"
                    v-model="message"
                />
            </div>
            <div class = "chat-actions clickable" @click="sendMessage">
                <i
                    class="las la-chevron-circle-right"
                    v-if="message !== ''"
                />
            </div>

        </div>

    </div>

</template>

<script>
    import { defaultDateFormat } from '../utils/constants';
    import moment from 'moment';

    export default {
        name: "Chat",

        props: {
            messages: {
                type: Array,
                default: () => []
            },

            urls: Object,

            idPresolicitud: {
                type: null,
                default: null
            },

            messageSource: String ,

            answerSource: String
        },

        data: () => ({

            //Data para el chat
            message: '',
            reply: '',
            viewingInputId: null

        }),

        methods: {

            async showAnswer(msg,index){

                if( this.messages[index].answers && this.messages[index].answers.length){

                    this.$emit("appendAnswers", {answers: [] ,index} );
                    this.$forceUpdate();

                }

                try{

                    const response = await this.$http.get(
                        this.urls.get,
                        { 
                            params: {
                                id_pregunta: msg.id,                         
                                _fuente: this.messageSource,
                                _accion: 'respuestaPregunta'
                            }
                        },
                        this.$defaultConfig,
                    );

                    this.debugStuff(response,"hotpink","respuesta a traer las respuestas de una pregunta");

                    if(response.data.ok){

                        const answers = response.data.pregunta.respuestas.map( a => {
                            return({
                                id: a.id,
                                userId: a.id_usuario,
                                body: a.respuesta,
                                id_preapplication: msg.id_presolicitud,
                                date: a.fecha,
                                sender: a.usuario.nombre
                            })
                        });

                        this.$emit("appendAnswers", {answers ,index} );
                        this.$forceUpdate();

                    }

                }catch(err){
                    this.debugError(err);
                    mensajeError("Error Servidor.")
                }

            },

            replyMessage(msg){
                this.viewingInputId = msg.id
            },

            async sendMessage(){

                if(this.message === '')
                    return;

                try{

                    const message = {
                         _fuente: this.messageSource,
                        id_usuario: this.$usuario.id,
                        pregunta: this.message,
                        id_presolicitud: this.idPresolicitud,
                        fecha: moment().format(defaultDateFormat)
                    }

                    const form =  this.createFormData({
                        ...message
                    });

                    const response = await this.$http.post(
                        this.urls.post,
                        form,
                        this.$defaultConfig
                    );

                    this.debugStuff(response, "hotpink", "respuesta a enviar mensaje");

                    if( response.data.ok ){

                        const newMessage = {
                            id: response.data.id,
                            userId: message.id_usuario,
                            body: message.pregunta,
                            id_preapplication: message.id_presolicitud,
                            date: message.fecha,
                            sender: this.$usuario.nombre
                        }

                        this.message = '';
                        this.$emit("newMessage", newMessage );

                    }


                }catch(error){

                    this.debugError(error);
                    mensajeError("Error servidor.");

                }


            },

            async sendReply(msg,index){

                if(this.reply === '')
                    return;

                try{

                    const message = {
                         _fuente: this.answerSource,
                        id_usuario: this.$usuario.id,
                        respuesta: this.reply,
                        id_pregunta: msg.id,
                        id_presolicitud: this.idPresolicitud,
                        fecha: moment().format(defaultDateFormat)
                    }

                    const form =  this.createFormData({
                        ...message
                    });

                    const response = await this.$http.post(
                        this.urls.post,
                        form,
                        this.$defaultConfig
                    );

                    this.debugStuff(response, "hotpink", "Respuesta a enviar respuesta");

                    if( response.data.ok ){

                        const newMessage = {
                            id: response.data.id,
                            userId: message.id_usuario,
                            body: message.respuesta,
                            id_preapplication: message.id_presolicitud,
                            date: message.fecha,
                            sender: this.$usuario.nombre
                        }

                        this.reply = '';
                        this.viewingInputId = null;
                        this.$emit("appendAnswers", {answers: newMessage, index} );
                        this.$forceUpdate();

                    }


                }catch(error){

                    this.debugError(error);
                    mensajeError("Error servidor.");

                }

            }

        }
    }

</script>

<style scoped>

    .clickable{
        cursor: pointer;
    }

    .chat-container{
        position: relative;
        height: 100%;
        width: 100%;
        --chat-input-height: 50px;
    }

    .chat-input{
        position: absolute;
        bottom: 0;
        width: 100%;
        display: flex;
        max-height: var(--chat-input-height) ;
    }

    .chat-messages{
        height: 75%; /*calc( 100% - var(--chat-input-height) );*/
        overflow-y: scroll;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }

    .chat-message{
        margin-top: 0.5rem;
        display: flex;
        flex-direction: column;
        width: 100%;
    }

    .chat-thread{
        display: flex;
        flex-direction: column;
        position: relative;
        align-items: flex-end;
    }

    .chat-thread::before{
        position: absolute;
        content: '';
        width: 4px;
        height: 100%;
        top: -5%;
        right: 6px;
        background-color: hotpink;
    }

    .chat-thread::after{
        position: absolute;
        content: '';
        width: 15px;
        height: 15px;
        border-radius: 50px;
        top: -10%;
        right: 1px;
        background-color: pink;
    }

    .chat-input-thread{
        align-self: flex-end;
    }

    .user-info{
        display: flex;
        font-weight: bold;
    }

    .message-date{
        font-weight: lighter;
        color: #757575;
        margin-left: 0.5rem;
        margin-right: 0.5rem;

    }

    .message-body{
        padding: .25rem .8rem;
        margin-top: .25rem;
        background-color: #007bff;
        max-width: 80%;
        display: inline-flex;
        align-self: flex-start;
        color: #fff;
        border-radius: 10px;
        position: relative;
    }

    .answer{
        align-self: flex-end !important;
        background-color: #666 !important;
    }

    .answer::before{
        content: '';
        border-left: 8px solid transparent;
        border-right: 8px solid transparent;
        width: 0;
        height: 0;
        border-bottom: 8px solid #666;
        position: absolute;
        top: -7px;
        left: 10px;
    }

    .question::before{
        content: '';
        border-left: 8px solid transparent;
        border-right: 8px solid transparent;
        width: 0;
        height: 0;
        border-bottom: 8px solid #007bff;
        position: absolute;
        top: -7px;
        left: 10px;
    }

    .message-body:hover .message-actions{
        display: flex;
    }

    .message-actions:hover{
        display: flex;
    }

    .message-actions{
        font-size: 30px;
        color: #007bff;
        position: absolute;
        left: 100%;
        display: none;
    }

    .message-actions i {
        margin-left: 1rem;
    }

    .chat-answer{
        margin-right: 2rem;
        position: relative;
        display: flex;
        flex-direction: column;
        margin-bottom: 1rem;
    }

    .chat-answer::before{
        position: absolute;
        content: '';
        width: 15px;
        height: 15px;
        border-radius: 50px;
        top: calc(50% + 7.5px);
        right: -31px;
        background-color: pink;
    }
    

    .chat-keyboard{
        width: 85%;
    }

    .chat-actions{
        width: 15%;
    }

    .chat-actions i {
        font-size: 3rem;
        margin-top: auto;
        margin-bottom: 24px;
    }

    .margin-none{
        margin: 0 !important;
    }

</style>