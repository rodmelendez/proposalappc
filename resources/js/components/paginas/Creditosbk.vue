<template>
  <enhanced-crud
      :_titulo_singular="titulo_singular"
      :_titulo_plural="titulo_plural"
      :_icono="icono"
      :_fuente="fuente"
      :_propiedades_buscadas="propiedades_buscadas"
      :_items="items"
      :_item="item"
      :_vista_items="vista_items"
      :_vista_formulario="vista_formulario"
      :_puede_guardar="formulario_esta_validado"
      :_show_default_buttons="false"
      :urls="urls"
      _tipo_vista="expandido"
      @editFormWasShow="mostrarFormulario"
      @formWasShown="mostrarFormularioNuevo"
      @itemDataSet="itemDataSet"
      @changeItemsPerPage="changeItemsPerPage"
      @formHide="limpiarItem"
  >

    <!--Vista de creditos (Resumen)-->
    <template slot="contenido_item" slot-scope="row">
      <h4>
        Presolicitud para: {{ row.item.nombreCliente }}
      </h4>

      <span class="freelancer-detail-item" v-if="row.item.fecha_solicitud">
                <span><strong>Fecha:</strong> {{ formatoFecha(row.item.fecha_solicitud) }}</span>
            </span>

      <div class="row" v-if="row.item.id">
        <div class="col-xl-12">
          <span><strong>Monto:</strong> <i>{{ detalleMonto(row.item) }}</i></span>
        </div>
      </div>

      <div class="row" v-if="row.item.id">
        <div class="col-xl-12">
          <span><strong>N° presolicitud:</strong> <i>{{ row.item.id }}</i></span>
        </div>
      </div>

      <div class="row" v-if="row.item.id_simi">
        <div class="col-xl-12">
          <span><strong>Codigo Simicro:</strong> <i>{{ row.item.id_simi }}</i></span>
        </div>
      </div>

      <div class="row" v-if="typeof row.item.observaciones === 'string' && row.item.observaciones.length">
        <div class="col-xl-12">
          <span><strong>Observaciones:</strong> <i>{{ observacionesDocumento(row.item.observaciones) }}</i></span>
        </div>
      </div>

      <div class="row" v-if="typeof row.item.nombreCreadorCredito === 'string' && row.item.nombreCreadorCredito.length">
        <div class="col-xl-12">
          <span><strong>Creado por:</strong> <span>{{ row.item.nombreCreadorCredito}}</span></span>
        </div>
      </div>
      <div class="row" v-if="row.item.estado_etapa">
        <div class="col-xl-12">
          <span><strong>Actualmente:</strong> <span>{{ getEtapaActual( row.item.estado_etapa ) }}</span></span>
        </div>
      </div>
      <div class="row" v-if="row.item.estado_etapa">
        <div class="col-xl-12">
          <span><strong>Estado del credito:</strong> <span>{{ getEstadoDelCredito( row.item.estado_vida ) }}</span></span>
        </div>
      </div>
    </template>
    <!--Fin Vista de creditos (Resumen)-->

    <!--Formulario de credito-->
    <template slot="formulario">

      <div class="row">

        <!--Datos Basicos del credito-->
        <div class="col-xl-12">
          <div class="row">
            <div class="col-xl-4">
              <input-seleccion
                  v-model="item.id_cliente"
                  nombre="cliente"
                  etiqueta="Cliente"
                  :items="clients"
                  :showSearchAlways="true"
              />
            </div>

            <div class="col-xl-4">
              <input-fecha-vue
                  nombre="fecha"
                  etiqueta="Fecha"
                  v-model="item.fecha_solicitud"
              />
            </div>

            <div class="col-xl-4">
              <input-moneda
                  v-model="item.monto_solicitado"
                  nombre="monto"
                  etiqueta="Monto"
                  :moneda_seleccionada="item.moneda_seleccionada"
                  @cambia_moneda="cambiarMoneda"
              />
            </div>
          </div>

          <div class="row">
            <div class="col-xl-6">
              <input-texto
                  v-model="item.plazo_solicitado"
                  nombre="Plazo Solicitado"
                  etiqueta="Plazo"
                  accesor="nombre"
              />
            </div>

            <div class="col-xl-6">
              <input-seleccion
                  v-model="item.producto"
                  nombre="Producto"
                  etiqueta="Producto"
                  :items="products"
                  accesor="id"
              />
            </div>
          </div>


          <div class="row">
            <div class="col-xl-6">
              <input-texto
                  :disabled="false"
                  v-model="item.tasa_interes"
                  nombre="tasa_interes"
                  etiqueta="Tasa de interes"
              />
            </div>

            <div class="col-xl-6">
              <input-seleccion
                  v-model="item.sucursal"
                  nombre="Sucursal"
                  etiqueta="Sucursal"
                  :items="sucursals"
                  accesor="id"
              />
            </div>
          </div>


          <div class="row">
            <div class="col-xl-6">
              <input-texto
                  :disabled="true"
                  v-model="item.id_simi"
                  nombre="estado_presolicitud"
                  etiqueta="Estado de crédito SIMICRO"
              />
            </div>

            <div class="col-xl-6">
              <input-texto
                  :disabled="true"
                  v-model="item.estado_presolicitud"
                  nombre="estado_presolicitud"
                  etiqueta="N° Simicro"
              />
            </div>
          </div>


          <div class="row">
            <div class="col-xl-4">
              <input-texto
                  :disabled="false"
                  v-model="item.descripcion"
                  nombre="descripcion"
                  etiqueta="Descripción"
              />
            </div>

            <div class="col-xl-4">
              <input-texto
                  :disabled="false"
                  v-model="item.direccion"
                  nombre="direccion"
                  etiqueta="Dirección"
              />
            </div>

            <div class="col-xl-4">
              <submit-button
                  v-if="!item.id"
                  @submit="savePreApplication"
              />
              <v-button v-if="item.id" @click="openHistoryModal"> Historial</v-button>
            </div>
          </div>


          <div class="row">

          </div>

        </div>

      </div>

      <div class="row">
        <!--Visualizacion del documento-->
        <div class="col-xl-12" v-if="!item.id || showCreditBoard">

          <a href="#" @click="toggleCreditBoard" v-if="item.id">Ver detalles</a>

          <h2 class = "text-center margin-bottom-30">ORGANIZACIÓN DE PRESOLICITUD</h2>
          <!--Si no hay id se muestra esta seccion vacia-->
          <div class="request-credit-void row">

            <div class= "col-xl-4">

              <div class="content">
                <h3>Empleados</h3>
                <ul class = "users-list">
                  <li v-for="user in users" :key="user.id">
                    <drag
                        class="list-item"
                        :class="{dragging: isDragging}"
                        @drag="setGrabbing"
                        effect-allowed="all"
                        :transfer-data="user"
                    >
                      <div class="user-container">
                        <div class="user-list-avatar">
                          <img :src="avatarUrl(item)" alt="">
                        </div>
                        <!-- Name -->
                        <div>
                          <h4>
                            {{ user.first_name }}. {{ user.last_name }}
                            <span>
                                                            {{user.name}}
                                                        </span>
                          </h4>
                        </div>
                      </div>
                    </drag>
                  </li>
                </ul>
              </div>

            </div>

            <div class= "col-xl-8">

              <div class = "participants-section" v-if="!item.id">

                <div class = "participant">

                  <div class = "participant-image">
                    <img :src="avatarUrl(participants.executive)" alt="">
                  </div>

                  <span>Ejecutivo: {{ participants.executive.name }}</span>

                </div>

              </div>

              <div class = "participants-section-dropzone" v-else>
                <!--dropzone-->
                <drop
                    class="dropzone"
                    :class="{ 'active' : dropzoneActive === 'executive'  }"
                    @drop="handleDropParticipant('executive', ...arguments)"
                    @dragover="dropzoneActive = 'executive'"
                    @dragleave="dropzoneActive = '' "
                    @dragend="dropzoneActive = '' "
                >
                  <i class="icon-feather-plus" />
                  <span> Añadir usuario como ejecutivo </span>
                </drop>
                <!--fin dropzone-->

                <!--participants-->
                <div class="participants">
                  <h4 class="margin-bottom-10"> Ejecutivo </h4>
                  <div class="participants-box">
                    <div class = "participant" v-if="participants.executive.name">

                      <div class = "participant-image">
                        <div class="participant-actions" v-if="item.id" >
                          <span @click="deleteParticipant(participants.executive, 'executive')" ><i class="las la-trash"></i></span>
                        </div>
                        <img :src="avatarUrl(participants.executive)" alt="">
                      </div>

                      <span>{{ participants.executive.name }}</span>

                    </div>
                  </div>
                </div>
                <!--fin participants-->
              </div>

              <div class = "participants-section-dropzone">
                <!--dropzone-->
                <drop
                    class="dropzone"
                    :class="{ 'active' : dropzoneActive === 'supervisor'  }"
                    @drop="handleDropParticipant('supervisor', ...arguments)"
                    @dragover="dropzoneActive = 'supervisor'"
                    @dragleave="dropzoneActive = '' "
                    @dragend="dropzoneActive = '' "
                >
                  <i class="icon-feather-plus" />
                  <span> Añadir usuario como supervisor </span>
                </drop>
                <!--fin dropzone-->

                <!--participants-->
                <div class="participants">
                  <h4 class="margin-bottom-10"> Supervisor </h4>
                  <div class="participants-box">
                    <div class = "participant" v-if="participants.supervisor.name">

                      <div class = "participant-image">
                        <div class="participant-actions" v-if="item.id" >
                          <span @click="deleteParticipant(participants.supervisor, 'supervisor')" ><i class="las la-trash"></i></span>
                        </div>
                        <img :src="avatarUrl(participants.supervisor, 'supervisor')" alt="">
                      </div>

                      <span>{{ participants.supervisor.name }}</span>

                    </div>
                  </div>
                </div>
                <!--fin participants-->
              </div>

              <div class = "participants-section-dropzone">
                <!--dropzone-->
                <drop
                    class="dropzone"
                    :class="{ 'active' : dropzoneActive === 'committee'  }"
                    @drop="handleDropParticipant('committee', ...arguments)"
                    @dragover="dropzoneActive = 'committee'"
                    @dragleave="dropzoneActive = '' "
                    @dragend="dropzoneActive = '' "
                >
                  <i class="icon-feather-plus" />
                  <span> Añadir usuario a comite </span>
                </drop>
                <!--fin dropzone-->

                <!--participants-->
                <div class="participants">
                  <h4 class="margin-bottom-10"> Comité </h4>
                  <div class="participants-box">
                    <div
                        class="participant"
                        v-for="user in participants.committee"
                        :key="user.id"
                    >
                      <div class = "participant-image">
                        <div class="participant-actions" v-if="item.id" >
                          <span @click="deleteParticipant(user,'committee')" ><i class="las la-trash"></i></span>
                        </div>
                        <img :src="avatarUrl(user)" alt="">
                      </div>

                      <span>{{ user.name }}</span>
                    </div>
                  </div>

                </div>
                <!--fin participants-->
              </div>

              <div class = "participants-section-dropzone">
                <!--dropzone-->
                <drop
                    class="dropzone"
                    :class="{ 'active' : dropzoneActive === 'operations'  }"
                    @drop="handleDropParticipant('operations', ...arguments)"
                    @dragover="dropzoneActive = 'operations'"
                    @dragleave="dropzoneActive = '' "
                    @dragend="dropzoneActive = '' "
                >
                  <i class="icon-feather-plus" />
                  <span> Añadir usuario a operaciones </span>
                </drop>
                <!--fin dropzone-->

                <!--participants-->
                <div class="participants">
                  <h4 class="margin-bottom-10"> operaciones </h4>
                  <div class="participants-box">
                    <div
                        class="participant"
                        v-for="user in participants.operations"
                        :key="user.id"
                    >
                      <div class = "participant-image">
                        <div class="participant-actions" v-if="item.id" >
                          <span @click="deleteParticipant(user, 'operations')" ><i class="las la-trash"></i></span>
                        </div>
                        <img :src="avatarUrl(user)" alt="">
                      </div>

                      <span>{{ user.name }}</span>
                    </div>
                  </div>

                </div>
                <!--fin participants-->
              </div>

            </div>


          </div>

        </div>
        <!--fin visualizacion credito-->

        <div class="col-xl-12" v-if="item.id && !showCreditBoard">

          <template v-if ="item.id && folders.length" >

            <div class="col-xl-12 toolbar">
              <a href="#" @click="toggleCreditBoard">Organización de presolicitud</a>
            </div>

            <!--Stepper-->
            <div class = "col-xl-12">
              <div
                  class = "stepper contenedor-iconos"
                  id="stepper-container"
              >
                <div
                    v-for="stage in stages"
                    :key ="stage.id"
                    class="etapa contenedor-icono"
                    :class="getStepperClasses(stage)"
                    :title="stage.nombre"
                    data-tippy-placement="top"
                >
                  <i
                      class="icono-etapa"
                      :class="stage.icono"
                  />
                  <div :class="getStepperActions()" v-if="parseInt(stage.id) === parseInt(item.estado_etapa)">
                    <i
                        :class="`las la-check`"
                        @click="approveStage(stage)"
                    />
                    <i
                        class="icon-feather-arrow-up-left"
                        @click="backStage(stage)"
                    />
                    <i
                        class="las la-times"
                        @click="togglePreapplicationModal()"
                    />
                  </div>

                </div>
              </div>
            </div>
            <!--Fin Stepper-->

            <!--carpetas-->
            <div class = "col-xl-12">

              <div class="message-time-sign">
                <span>Carpetas</span>
              </div>

              <div class= "d-flex preapplication-view">
                <carousel
                    class="no-shadow"
                    :per-page="4"
                    :space-padding="10"
                    :pagination-enabled="false"
                >
                  <slide v-for="(folder, index) in folders" :key="index">
                    <div
                        class="folder-card"
                        :class="{ selected: folder.selected }"
                        :key="folder.id"
                        @click="loadDocumentsFromFolder(folder,index)"
                    >
                      <i :class =" !folder.isButton ? 'las la-archive' : 'las la-undo'"/>
                      <span>{{folder.nombre}}</span>
                    </div>
                  </slide>
                </carousel>
              </div>

            </div>
            <!--Fin Carpetas-->

            <!--fotos-->
            <div class= "col-xl-12" >

              <!--Fotos archivos-->
              <div class="message-time-sign">
                <span>Fotos</span>
              </div>

              <div class=galery-carousel>
                <carousel
                    class="no-shadow"
                    :perPageCustom="[[480, 2], [768, 2]]"
                    :space-padding="10"
                    :pagination-enabled="false"
                >
                  <slide v-for="( imageFile , index) in imageFiles" :key="index">
                    <galery-item
                        :label="getFileName(imageFile)"
                        :item="imageFile"
                        :imageFile ="imageFile"
                        :urls="urls"
                        :currentStage="item.estado_etapa"
                        :id_presolicitud="item.id"
                        source = "IntranetPresolicitudDocumento"
                        :width="150"
                        height="300"
                        @imageChange="addImageToPreapplication"
                        @expand="showImageInGalery"
                        :disabled="!creditIsAlive()"
                    />
                  </slide>
                  <slide>
                    <galery-item
                        label="Otro"
                        :id_presolicitud="item.id"
                        source = "IntranetPresolicitudDocumento"
                        :width="150"
                        height="300"
                        :item="item"
                        :imageFile="genericPictureTemplate"
                        :urls="urls"
                        @imageChange="addImageToPreapplication"
                        :disabled="!creditIsAlive()"
                    />
                  </slide>
                </carousel>
              </div>
              <!--Fin Fotos archivos-->

            </div>
            <!--fin fotos-->

            <!--documentos-->
            <div class="col-xl-12">

              <div class="message-time-sign">
                <span>Documentos</span>
              </div>

              <div>
                <carousel
                    id="documents-carousel"
                    class="no-shadow"
                    :visible-slides="3"
                    :slide-ratio="1 / 4"
                    :dragging-distance="70"
                    :pagination-enabled="false"
                >
                  <slide v-for="(document, index) in documentFiles" :key="index">
                    <file-field
                        :id ="`documento-${index}`"
                        :label ="getFileName(document)"
                        :fileValue  ="document"
                        :disabled="parseInt(item.estado_vida) === 0 || document.stage > item.estado_etapa || document.loaded  "
                        name ="documento"
                        v-model ="documentModel"
                        v-if="mostrar_agregar_nuevo"
                        @change ="addDocumentToPreapplication"
                    />
                  </slide>
                  <slide>
                    <file-field
                        :id="`documento-opcional`"
                        :label="`Otro`"
                        :fileValue="genericDocumentTemplate"
                        :disabled="parseInt(item.estado_vida) === 0"
                        :loaded="genericDocumentTemplate.loaded"
                        name="documento"
                        v-model="documentModel"
                        @change="addDocumentToPreapplication"
                    />
                  </slide>
                </carousel>
              </div>

            </div>
            <!--fin documentos-->

          </template>

        </div>
      </div>

      <!--Modal para aprobar credito-->
      <v-modal
          :abierto="showApproveModal"
          @close="closeApproveModal"
          @success="advanceToNextStage"
          size = "md"
      >

        <template slot="header">
          <h3>Desea aprobar etapa?</h3>
        </template>

        <template slot="body">
          <input-texto
              v-model="approveStageMessage"
              etiqueta="Observación"
              nombre="observacion"
          />
        </template>

      </v-modal>
      <!--Modal para aprobar credito-->

      <!--Modal para reprobar credito-->
      <v-modal
          :abierto="showBackStageModal"
          @close="closeBackStageModal"
          @success="backToPreviousStage"
          size = "md"
      >

        <template slot="header">
          <h3>Desea retroceder a etapa anterior?</h3>
        </template>

        <template slot="body">
          <input-texto
              v-model="approveStageMessage"
              etiqueta="Observación"
              nombre="observacion"
          />
        </template>

      </v-modal>
      <!--Modal para aprobar credito-->

      <!--Modal historial-->
      <v-modal
          :abierto="showHistoryModal"
          @close="closeHistoryModal"
          @success="closeHistoryModal"
          size = "md"
      >

        <template slot="header">
          <h3>Historial de presolicitud</h3>
        </template>

        <template slot="body">

          <timeline
              :events="getPreapplicationMovements()"
              :totalDuration="getTotalDurationFromPreapplication()"
              :stages="getStagesDuration()"
          />

        </template>

      </v-modal>
      <!--Modal historia-->

      <!--Modal historial-->
      <v-modal
          :abierto="showRefusePreapplicationModal"
          @close="togglePreapplicationModal"
          @success="refusePreapplication"
          size = "md"
      >

        <template slot="header">
          <h3>Rechazar Presolicitud</h3>
        </template>

        <template slot="body">

          <input-texto
              v-model="approveStageMessage"
              etiqueta="Observación"
              nombre="observacion"
          />

        </template>

      </v-modal>
      <!--Modal historia-->

      <!--Modal para el chat-->
      <floating-modal
          :showBall="item.id !== null || item.id"
          :isOpen="showChat"
          :title="'Foro de credito'"
          @toggleModal="toggleChatModal"
      >
        <template slot ="body">
          <chat
              :idPresolicitud="item.id"
              :urls="urls"
              :messageSource="'IntranetPreguntaPresolicitud'"
              :answerSource="'IntranetRespuestaPresolicitud'"
              :messages="messages"
              @newMessage="(newMessage) => {messages.push(newMessage)}"
              @appendAnswers="appendAnswers"
          />
        </template>
      </floating-modal>
      <!--Fin Modal para el chat-->

      <!--Galeria de fotos-->
      <galery
          v-if="startPictureId !== null"
          :startPictureId="startPictureId"
          :picturesArray="imageFiles"
          @closeGalery="startPictureId = null"
      />
      <!--fin galeria de fotos-->

    </template>

  </enhanced-crud>
</template>

<script>

/*ImageFile*/
/*Es un objeto compuesto por una imagen una plantilla de documento y otros datos*/
/**
 *  {
 *     picture: Object ,
 *     template: Object,
 *     ...otros
 *  }
 *
 */

import monedas from '../../monedas.json';
import moment, { defaultFormat } from 'moment';
import {defaultDateFormat} from '../../utils/constants';
import Timeline from '../../containers/Timeline.vue';
import VButton from '../buttons/Button';
import FloatingModal from '../../containers/FloatingModal.vue';
import Chat from '../../containers/Chat.vue';
import Galery from '../../containers/Galery.vue';

window.Vue = require('vue');
Vue.component('galeria-item', require('../GaleriaItem.vue').default);
Vue.component('galery-item', require('../others/GaleryItem.vue').default);
Vue.component('submit-button', require('../buttons/SubmitButton.vue').default);

const terms = [
  {id:1 , nombre: 3 },
  {id:2 , nombre: 6 },
  {id:3 , nombre: 9 },
  {id:4 , nombre: 12 },
  {id:5 , nombre: 15 },
  {id:6 , nombre: 18 },
  {id:7 , nombre: 21 },
  {id:8 , nombre: 24 }
]

const stages = [
  {id: 1 , nombre: "Etapa de presolicitud" , icono: "las la-user"},
  {id: 2 , nombre: "Etapa de Analísis de Credito", icono: "las la-search-dollar"},
  {id: 3 , nombre: "Etapa de Supervisión de Crédito", icono: "las la-file-invoice"},
  {id: 4 , nombre: "Etapa de Comité de Crédito" , icono: "las la-users"},
  {id: 5 , nombre: "Etapa de Desembolso", icono: "las la-money-bill"}
];

const users = [
  {id: 1 , name: "Taylor Swift"},
  {id: 2 , name: "Demi Lovato"},
  {id: 3 , name: "Selena Gomez"},
  {id: 4 , name: "Sia"},
  {id: 5 , name: "Weyes Blood"},
  {id: 6 , name: "Conan Gray"},
  {id: 7 , name: "Bililish"}
]

const getDurationBetween = ( date1 , date2 ) => {

  const parseDate1 = date1 || moment();
  const parseDate2 = moment(date2);

  const differenceBetweenDates = parseDate1.diff( parseDate2 );

  const duration = moment.duration( differenceBetweenDates ).asHours();

  /*console.log({
      parseDate1,
      parseDate2,
      date1,
      date2,
      differenceBetweenDates,
      duration
  });*/

  return duration;

}

export default {
  name: "Creditos.vue",

  components: {

    'v-button': VButton,
    'timeline': Timeline,
    'floating-modal': FloatingModal,
    'chat': Chat,
    'galery': Galery
  },

  props: {
    urls: Object,
    avatar_defecto: String,
  },

  data: () => ({

    /*Data basica para request y layout*/
    fuente: 'IntranetPresolicitud',
    titulo_singular: 'Presolicitud',
    titulo_plural: 'Presolicitudes',
    icono: 'icon-line-awesome-credit-card',

    /*Booleans para controlar las vistas*/
    vista_items: true,
    vista_formulario: false,
    vista_datos_basicos: true,
    vista_estado_presolicitud: false,
    vista_actual: 0,
    formulario_esta_validado: false,
    mostrar_agregar_nuevo: true,
    uploading: false,
    showApproveModal: false,
    showBackStageModal: false,
    showHistoryModal: false,
    showRefusePreapplicationModal: false,
    showChat: false,
    isDragging: false,
    dropzoneActive: '',
    showCreditBoard: false,
    showGalery: false,
    startPictureId: null,
    rootFoldersIds: [null],

    /*Filtros*/
    texto_buscado: '',
    propiedades_buscadas: [
      'nombre',
      'nombreCliente',
      'dni_cliente',
      'id_simi',
      'id'
    ],


    /*Data para el funcionamiento del modulo*/
    folders: [],
    terms: terms,
    stages: stages,
    users: [],
    clients: [],
    products: [],
    sucursals: [],
    monedas: [],
    documentModel: '',
    hiddenTemplates: [],
    participants: {  executive: {} , supervisor: {}, committee: [], operations: [] },
    messages: [],
    genericTemplates: {},


    /*Records de la base de datos*/
    items: [],

    /*Objeto a enviar a la base de datos | tambien se utiliza en el editar*/
    item: {
      id: null,
      id_cliente: 0,
      cliente: null,
      id_usuario: null,
      monto_solicitado: 0,
      fecha_solicitud: '',
      plazo_solicitado: null,
      moneda_simbolo: '',
      moneda_iso: '',
      moneda_seleccionada: monedas[0],
      moneda: 0,
      items: [],
      etapas: [],
      documents: [],
      pictures: [],
      picturesValidated: [],
      documentsValidated: [],
      imageFiles: [],
      documentFiles: [],
      estado_etapa: 1 ,
      producto: null,
      forma_credito: '',
      tasa_interes: '',
      //Id_sucursal,id_sucursal: 0,
      sucursal: null,
      estado_presolicitud: '',
      descripcion: '',
      direccion: ''
    },

    approveStageMessage: '',

    classes: {
      selected: false,
      completed: false,
      'with-error': false,
    },

    moneda_seleccionada: null,
    carpetaSeleccionada: null,

  }),

  methods: {

    /*Resetea el formulario por medio*/
    /*de la limpieza de item*/
    limpiarItem() {

      this.participants = {  executive: {} , supervisor: {}, committee: [], operations: [] },

          this.participants.executive = {
            name: this.$usuario.nombre
          }

      Object.assign( this.item , {
        id: 0,
        id_cliente: 0,
        id_usuario: null,
        monto_solicitado: 0,
        fecha_solicitud: '',
        plazo_solicitado: null,
        moneda_simbolo: '',
        moneda_iso: '',
        moneda: 0,
        id_presolicitud: null,
        items: [],
        documents: [],
        pictures: [],
        picturesValidated: [],
        documentsValidated: [],
        imageFiles: [],
        documentFiles: [],
        etapas: [],
        estado_etapa: 1,
        forma_credito: '',
        tasa_interes: '',
        id_sucursal: 0, //Id_sucursal,
        estado_presolicitud: '',
        descripcion: '',
        direccion: ''
      });

      this.$forceUpdate();
    },

    changeItemsPerPage(value){
      this.items_por_pagina = value;
      this.$forceUpdate();
    },

    /*Inicializa el formulario por medio*/
    /*del set de item*/
    setItemData(data) {

      const width = (data.estado_etapa - 1) * 20;

      setTimeout( () => {
        document.getElementById("stepper-container").style.setProperty('--stepper-width', `${width}%` );
      } , 1000 )

      Object.assign( this.item , data , { tasa_interes: data.tasa_interes + "%" });

      this.debugStuff(data, "hotpink", "setItemDAta");

      if(data.usuariosResponsables){

        data.usuariosResponsables.forEach(user => {

          switch(user.role){

            case 1:
              this.participants.executive = {
                name: user.detalle.nombre,
                id: user.id_usuario
              }
              break;
            case 2:
              this.participants.supervisor = {
                name: user.detalle.nombre,
                id: user.id_usuario
              }
              break;
            case 3:
              this.participants.committee.push({
                name: user.detalle.nombre,
                id: user.id_usuario
              });
              break;
            case 4:
              this.participants.operations.push({
                name: user.detalle.nombre,
                id: user.id_usuario
              });
              break;
          }

        });

      }

      this.folders = data["carpetas"].map( f => ({...f, selected: false}) );
      this.genericTemplates = data["genericos"];

      this.item.id = data.id;

      this.$forceUpdate();

    },

    /*Retorna la imagen de un usuario*/
    avatarUrl(item) {
      if (typeof item.foto === 'string' && item.foto.length) {
        return this.$uploads_img_dir + 'm/' + item.foto; //Vue.prototype.$uploads_img_dir
      }
      return this.avatar_defecto;
    },

    /*Carga otra data que se necesite*/
    /*para el funcionamiento del modulo*/
    cargarDataAdicional(){
      const self = this;
      this.$http.get(this.urls.get, {
        params: {
          _fuente: this.fuente,
          _accion: 'cargarListados',
        }
      })
          .then(response => {
            self.debugStuff(response, "hotpink", "Aqui deberia llegar tambien los usuarios");
            if (response.status === 200) {
              const data = response.data;
              this.clients = data["clientes"];
              this.products = data["productos"];
              this.sucursals = data["sucursales"];
              this.folders = data["documentos"].map( f => ({...f, selected: false}) );
              this.users   = data["usuariosPermitidos"].map( u => ({
                name: u.nombre,
                id: u.id,
                first_name: u.first_name,
                last_name: u.last_nama ?  u.last_name.split('').unshift() : ""
              }));
            }
          })
          .catch(err => {
            self.debugError(err);
          });
    },

    getStepperClasses(stage){

      const classes = {
        'etapa-actual': stage.id === parseInt(this.item.estado_etapa),
        'etapa-revisada': stage.id <= parseInt(this.item.estado_etapa),
      }

      return classes;

    },

    getStepperActions(){

      const classes = { "stepper-acciones": this.creditIsAlive() , "d-none": !this.creditIsAlive() }

      return classes;
    },

    closeApproveModal(){

      this.showApproveModal = false;

    },

    /*Añade una imagen a una presolicitud*/
    async addImageToPreapplication( input_origin , imageFile, callback ){

      //alert("here");
      //this.debugStuff(imageFile);

      try{

        const form = this.createFormData({
          _fuente: 'IntranetPresolicitudDocumento',
          _accion: 'subirImagen',
          foto_upload_modificado: 1,
          foto_upload: input_origin.get(0).files[0],
          id_presolicitud: this.item.id,
          fecha: moment().format(defaultDateFormat),
        });


        //Se sube la imagen a la base de datos
        const uploadedImageResponse = await this.$http.post(
            this.urls.post,
            form,
            this.$defaultConfig
        );

        this.debugStuff(uploadedImageResponse, "hotpink", "repsuesta subir imagen");

        if( uploadedImageResponse.data.ok ){

          imageFile.picture = {
            id: uploadedImageResponse.data.id,
            nombre: uploadedImageResponse.data.nombre,
            ...uploadedImageResponse.data["atributos"]
          }

        }

        //Se enlaza la foto a la presolicitud
        const bindImageResponse = await this.bindImageToPreapplication(imageFile);

        //this.debugStuff(uploadedImageResponse,"hotpink","Debug enlazar foto con presolicitud");
        //this.debugStuff(bindImageResponse,"hotpink","Debug enlazar foto con presolicitud");

        //Se cambia la imagen
        if(bindImageResponse.data.ok){

          //alert("aaaa");

          const index = imageFile.index;

          if( index >= this.imageFiles.length || index < 0 ){

            this.imageFiles.push( {...imageFile, loaded: true}  );

          }else {

            this.imageFiles[index] = { ...imageFile, loaded: true }
            this.imageFiles[index].loaded = true;

          }

          this.$forceUpdate();
          callback();

          input_origin.data('dropify').resetPreview();
          input_origin.data('dropify').clearElement();

        }


      }catch(error){

        this.debugError(error);
        mensajeError("Error servidor.");

      }

    },

    /*Enlaza una imagen a una presolicitud*/
    async bindImageToPreapplication( image ){

      //alert("here");
      //this.debugStuff(image, "hotpink", "Imagen donde esta la metadata");

      try{

        const form = this.createFormData(
            image.id_folder === image.id_root_folder ? {
              ...image.picture,
              _fuente: 'IntranetPresolicitudDocumento',
              tipo: image.template.tipo,
              id_presolicitud: this.item.id,
              id_usuario: this.$usuario.id,
              fecha: moment().format(defaultDateFormat),
              nombre: image.picture.nombre ,
              id_documento: image.template.id,
              id_carpeta: image.id_root_folder,
              observaciones: image.observations,
              generic: image.template.nombre === 'foto_generico' ? 1 : 0
            } : {
              ...image.picture,
              _fuente: 'IntranetPresolicitudDocumento',
              tipo: image.template.tipo,
              id_presolicitud: this.item.id,
              id_usuario: this.$usuario.id,
              fecha: moment().format(defaultDateFormat),
              nombre: image.picture.nombre ,
              id_documento: image.template.id,
              id_subcarpeta: image.id_folder === image.id_root_folder ? null : image.id_folder,
              id_carpeta: image.id_root_folder,
              observaciones: image.observations,
              generic: image.template.nombre === 'foto_generico' ? 1 : 0
            });

        const response = await this.$http.post(
            this.urls.post,
            form,
            this.$defaultConfig
        )

        this.debugResponse(response,"hotpink","Debug enlazar foto con presolicitud");

        if(response.data.ok){

          //alert("aaaa");
          this.item.picturesValidated.push(image);

          return response;
        }


      }catch(error){

        this.debugError(error);
        mensajeError("Error servidor.");

      }

    },

    /*Añade un documento a una presolicitud*/
    async addDocumentToPreapplication(file,document){

      try{

        this.debugStuff(document,"hotpink","Cargar documento a bd");

        const form = this.createFormData({
          _fuente: 'IntranetPresolicitudDocumento',
          _accion: 'subirArchivo',
          archivo_upload_modificado: 1,
          archivo_upload: file,
          fecha: moment().format('YYYY-MM-DD HH:mm:ss'),
          id_presolicitud: this.item.id
        });

        //this.debugForm(form);

        const uploadedDocumentResponse = await this.$http.post(
            this.urls.post,
            form,
            this.$defaultConfig
        );


        //this.debugStuff(uploadedDocumentResponse,"hotpink","Cargar documento a bd");

        resultadoSolicitudDefecto(uploadedDocumentResponse.data);

        if(uploadedDocumentResponse.data.ok){

          const data = uploadedDocumentResponse.data;

          document.document = { ...document.document,nombre: data.nombre }

          if( document.template.nombre === "documentoOpcionalSecret"){

            document.template.nombre = "documento generico"

          }

        }

        const bindDocumentResponse = await this.bindDocumentToPreapplication(document);

        this.debugResponse(bindDocumentResponse,"hotpink","Debug enlazar documento con presolicitud");

        if(bindDocumentResponse.data.ok){

          const index = document.index;

          if( index >= this.documentFiles.length ){

            alert("if")
            this.documentFiles.push( {...document, loaded: true}  );

          }else {

            alert("elset");
            this.documentFiles[index] = {...document, loaded: true};
          }

          this.documentFiles[index].loaded = true;

          Object.assign(
              this.item,
              {
                ...this.item
              }
          )

          this.$forceUpdate();

        }

      }catch(error){

        this.debugError(error);
        mensajeError("Error servidor.");

      }

    },

    /*Enlaza un documento a una presolicitud*/
    async bindDocumentToPreapplication( document ){

      //alert("here");
      //this.debugStuff(document);

      try{

        const form = this.createFormData(document.document.id_folder === document.document.id_root_folder ? {
          ...document.document,
          _fuente: 'IntranetPresolicitudDocumento',
          tipo: document.template.tipo === 1 ? 2 : document.template.tipo,
          id_presolicitud: this.item.id,
          id_usuario: this.$usuario.id,
          fecha: moment().format(defaultDateFormat),
          nombre: document.document.nombre ,
          id_documento: document.template.id,
          observaciones: document.observations,
          id_carpeta: document.document.id_root_folder
        } : {
          ...document.document,
          _fuente: 'IntranetPresolicitudDocumento',
          tipo: document.template.tipo === 1 ? 2 : document.template.tipo,
          id_presolicitud: this.item.id,
          id_usuario: this.$usuario.id,
          fecha: moment().format(defaultDateFormat),
          nombre: document.document.nombre ,
          id_documento: document.template.id,
          observaciones: document.observations,
          id_subcarpeta: document.document.id_folder === document.document.id_root_folder ? null : document.document.id_folder,
          id_carpeta: document.document.id_root_folder
        } )

        const response = await this.$http.post(
            this.urls.post,
            form,
            this.$defaultConfig
        )

        this.debugResponse(response,"hotpink","Debug enlazar documento con presolicitud");

        if(response.data.ok){

          this.item.documentsValidated.push(document);

          return response;
        }


      }catch(error){

        this.debugError(error);
        mensajeError("Error servidor.");

      }

    },

    /*Guarda una presolicitud en la bd*/
    async savePreApplication(){

      this.statusGuardando(true);

      try{

        const creditUsers = Object.entries(this.participants).reduce( ( usersArray , [ role , users ]) => {

          /*switch(role){
              case "supervisor":
                  return [...usersArray ,  [users.id,  2] ];
              case "executive":
                  return [...usersArray , [this.$usuario.id, 1] ];
              case "committee":
                  return [...usersArray , ...users.map( u => ([ u.id, 3 ]) )]
              default:
                  return [...usersArray , ...users.map( u => ([ u.id, 4 ]) )]
          }*/

          switch(role){
            case "supervisor":
              return [...usersArray , {id_usuario: users.id, role: 2 }];
            case "executive":
              return [...usersArray , {id_usuario: this.$usuario.id, role: 1 }];
            case "committee":
              return [...usersArray , ...users.map( u => ({ id_usuario: u.id, role: 3 }) )]
            default:
              return [...usersArray , ...users.map( u => ({ id_usuario: u.id, role: 4 }) )]
          }

        }, []);

        this.debugStuff(creditUsers,"hotpink", "usuarios de credito");

        const form = this.createFormData({
          _fuente: this.fuente,
          id_cliente: this.item.id_cliente,
          fecha_solicitud: this.item.fecha_solicitud,
          plazo_solicitado: this.item.plazo_solicitado,
          monto_solicitado: this.item.monto_solicitado,
          fecha: moment().format(defaultDateFormat),
          moneda: this.item.moneda_seleccionada.id,
          id_producto: this.item.producto,
          id_sucursal: this.item.sucursal,
          estado_etapa: this.item.estado_etapa, //cuando se crea la solicitud la etapa es 1
          "user-role": creditUsers
        });

        this.debugForm(form, "Formulario presolicitud");

        const response = await this.$http.post(
            this.urls.post,
            form,
            this.$defaultConfig
        )

        //this.debugStuff(response);

        resultadoSolicitudDefecto(response.data);

        const {ok , ...theData } = response.data;
        const {presolicitud} = theData;

        if(ok){

          Object.assign( this.item , {
            ...this.item,
            ...presolicitud,
          })
          this.item.etapas = [...this.item.etapas, response.data.etapaAnterior];
          this.$forceUpdate();

        }

      }catch(error){

        this.debugError(error);
        mensajeError('Error de servidor.');

      }

      this.limpiarItem();
      this.cargarData();
      this.vista_items = true;
      this.vista_formulario = false;

      this.statusGuardando(false);

    },

    /*Carga los documentos dentro de una carpeta*/
    /*tanto plantilla como documentos puros*/
    /*para ello hace un llamado a la api*/
    async loadDocumentsFromFolder(folder, index){

      /*if( this.item.estado_etapa < index + 1 ){
          mensaje("Aun no puede cargar documentos en esta etapa");
          return;
      }*/

      //Se filtran las carpetas
      //para conseguir la carpeta seleccionada actual
      //luego esa carpeta se deselecciona
      const folderToDiselectIndex = this.folders.findIndex( f => f.selected );

      if(folder.isButton){
        this.rootFoldersIds.pop();
      }else{
        this.rootFoldersIds.push(folder.id);
      }

      this.debugStuff(folder,"hotpink","carpetas");

      try{

        const response = await this.$http.get(
            this.urls.get,
            {
              params: {
                _fuente: this.fuente,
                _accion: "documentosPorCarpeta",
                id: this.item.id,
                id_carpeta: folder.id || null
              }
            },
            this.$defaultConfig
        );

        //this.debugStuff(response, "hotpink", "Respuesta loadDocuments");


        //Dividimos los documentos en fotos y documentos

        if(response.data.ok){

          const files = response.data.carpetas;

          this.debugStuff(response, "hotpink", "Respuesta cargar documentos de carpeta");

          if(Array.isArray(files)){

            this.folders = files;

          }else{

            let subFolders = files.subCarpetas;

            const backButton = {
              id: files.id_documento_categoria,
              nombre: 'volver',
              isButton: true
            }

            if(subFolders){
              subFolders = subFolders.map( f => ({...f, isSubFolder: true}) )
            }

            this.folders = subFolders ? [ files , ...subFolders, backButton ] : [ files, backButton  ];

          }

        }


      }catch(error){

        this.debugError(error);
        mensajeError('Error de servidor.');

      }

    },

    creditIsAlive(){
      return this.item.estado_vida === 1;
    },

    /*retorna true si la presolicitud puede avanzar de etapa*/
    canAdvanceToNextStage(){

      if( !this.creditIsAlive() )
        return;

      const hasFiles = this.item.imageFiles.length && this.item.documentFiles.length;

      const hasRequiredImages = this.item.imageFiles.filter( i => !i.loaded && i.template.opcional !== true ).length <= 0;

      const hasRequiredFiles  = this.item.documentFiles.filter( d => !d.loaded && d.template.opcional !== true  ).length <= 0;

      return hasFiles && hasRequiredImages && hasRequiredFiles;

    },

    /*Aprueba un etapa*/
    approveStage(stage){

      //Se valida que se cumpla con los requisitos para
      //pasar de etapa

      /*if( !this.canAdvanceToNextStage()  ){

          mensaje("no puede aprobar etapa, no ha cargado todos los documentos requeridos");
          return;

      }*/

      this.showApproveModal = true;
      this.$forceUpdate();

    },

    /*Peticion a la api para aprobar etapa*/
    async advanceToNextStage(){

      if( !this.creditIsAlive() )
        return;

      try{

        //Etapa actual
        const currentStageIndex = this.item.etapas.map( s => parseInt(s.etapa) ).lastIndexOf( parseInt(this.item.estado_etapa) );
        const currentStage = this.item.etapas[currentStageIndex];
        //Duracion en la etapa actual | Si aun no hay etapa actual el calculo se hace respecto a la fecha de creacion de la presolicitud
        //this.debugStuff({durationOnStage,currentStage, currentStageIndex, fecha: currentStage.fecha_registro }, "hotpink" , "Duracion");

        const durationOnStage = getDurationBetween( null , currentStage.fecha_registro );


        const data = {
          _fuente: this.fuente,
          id: this.item.id,
          duracion: durationOnStage,
          fecha: moment().format(defaultDateFormat) ,
          fecha_solicitud: this.item.fecha_solicitud,
          usuario: this.$usuario.id,
          movimiento: 'avanzar',
          observacion: this.approveStageMessage,
          estado_etapa: this.item.estado_etapa,
        }

        const form = this.createFormData(data);

        this.debugStuff(data, "hotpink" , "DATA QUE SE ENVIA AL SUBIR DE ETAPA");

        const response = await this.$http.post(
            this.urls.post,
            form,
            this.$defaultConfig
        );

        this.debugStuff( response , "hotpink", "response avanzar de etapa" );

        resultadoSolicitudDefecto(response.data);

        if(response.data.ok){

          const index= this.item.etapas.length - 1;

          this.item.etapas[index] = response.data.etapaAnterior ? { ...response.data.etapaAnterior } : { ...this.item.etapas[index] };

          if(response.data.nuevaEtapa){
            this.item.etapas.push( response.data.nuevaEtapa );
          }

          this.approveStageMessage = '';

          if( response.data.presolicitud &&
              response.data.presolicitud.estado_etapa > this.item.estado_etapa
          ){
            const width = this.item.estado_etapa * 20;
            document.getElementById("stepper-container").style.setProperty('--stepper-width', `${width}%` );
          }

          this.item.estado_etapa = response.data.presolicitud.estado_etapa;

          this.$forceUpdate();

        }


      }catch(error){

        this.debugError(error);
        mensajeError('Error de servidor.');

      }


    },

    backStage(stage){

      if( !this.creditIsAlive() )
        return;

      if(stage.id <= 1){

        mensaje("No se puede retroceder etapa");
        return;

      }

      this.openBackStageModal();

    },

    openBackStageModal(){
      if( !this.creditIsAlive() )
        return;

      this.showBackStageModal = true;
    },

    closeBackStageModal(){


      this.showBackStageModal = false;
    },

    openHistoryModal(){
      this.showHistoryModal = true;
    },

    closeHistoryModal(){
      this.showHistoryModal = false;
    },

    /*Retrocede una presolicitud a la etapa anterior*/
    async backToPreviousStage(){

      if( !this.creditIsAlive() )
        return;

      try{

        //Etapa actual
        const currentStageIndex = this.item.etapas.map( s => parseInt(s.etapa) ).lastIndexOf( parseInt(this.item.estado_etapa) );
        const currentStage = this.item.etapas[currentStageIndex];
        //Duracion en la etapa actual
        const durationOnStage = getDurationBetween( null , currentStage.fecha_registro );

        const form = this.createFormData({
          _fuente: this.fuente,
          id: this.item.id,
          duracion: durationOnStage,
          fecha: moment().format(defaultDateFormat) ,
          fecha_solicitud: this.item.fecha_solicitud,
          usuario: this.$usuario.id,
          movimiento: 'retroceder',
          observacion: this.approveStageMessage,
          estado_etapa: this.item.estado_etapa
        });

        this.debugForm(form);

        const response = await this.$http.post(
            this.urls.post,
            form,
            this.$defaultConfig
        );

        resultadoSolicitudDefecto(response.data);

        this.debugStuff( response , "hotpink" , "Response retroceder etapa");

        if( response.data.ok ){

          const index= this.item.etapas.length - 1;
          this.item.etapas[index] = response.data.etapaAnterior ? { ...response.data.etapaAnterior } : { ...this.item.etapas[index] };
          this.item.etapas.push( response.data.nuevaEtapa );
          this.approveStageMessage = '';

          const width = ( (response.data.presolicitud.estado_etapa || this.item.estado_etapa) - 1) * 20;

          document.getElementById("stepper-container").style.setProperty('--stepper-width', `${width}%` );

          this.item.estado_etapa = response.data.presolicitud.estado_etapa;

          this.$forceUpdate();

        }

      }catch(error){

        this.debugError(error);
        mensajeError('Error de servidor.');

      }

    },

    /*Abre el modal para rechazar credito*/
    togglePreapplicationModal(){

      this.showRefusePreapplicationModal = !this.showRefusePreapplicationModal;

    },

    /*Rechaza el credito por completo*/
    async refusePreapplication(){

      if( !this.creditIsAlive() )
        return;

      try{

        //Etapa actual
        const currentStageIndex = this.item.etapas.map( s => parseInt(s.etapa) ).lastIndexOf( parseInt(this.item.estado_etapa) );
        const currentStage = this.item.etapas[currentStageIndex];
        //Duracion en la etapa actual
        const durationOnStage = getDurationBetween( null , currentStage.fecha_registro );

        const form = this.createFormData({
          _fuente: this.fuente,
          id: this.item.id,
          duracion: durationOnStage,
          fecha: moment().format(defaultDateFormat) ,
          fecha_solicitud: this.item.fecha_solicitud,
          usuario: this.$usuario.id,
          movimiento: 'credito-rechazado',
          observacion: this.approveStageMessage
        });

        const response = await this.$http.post(
            this.urls.post,
            form,
            this.$defaultConfig
        );

        resultadoSolicitudDefecto(response.data);

        //this.debugStuff( response , "hotpink", "Respuesta rechazar presolocitidud" );

        if( response.data.ok ){

          this.item.estado_vida = response.data.presolicitud.estado_vida;


          const index = this.item.etapas.length - 1;
          this.item.etapas[index] = {...response.data.etapaAnterior}
          this.$forceUpdate();

        }

      }catch(error){

        this.debugError(error);
        mensajeError('Error de servidor.');

      }
    },

    /*Obtiene todos lo movimientos de una presolicitud*/
    getPreapplicationMovements(){

      if(!this.item.etapas)
        return [];

      const  messageDescription = {
        "creacion-credito": (props) => `Presolicitud creada por ${props.usuario.nombre}`
      }

      const movements = this.item.etapas.map( (stage, index) => {

        const events = (stage.movimientos || []).map( movement => ({

          date: movement.fecha_registro,
          title: `${ movement.movimiento === "avanzar" ? "Avance de etapa" : "Retroceso de etapa" }`,
          description: typeof messageDescription[movement.movimiento] === 'function' ? messageDescription[movement.movimiento](movement) : messageDescription[movement.movimiento] || (movement.movimiento) ,
          observation: movement.descripcion,
          footer: `creado por: Admin`,

        }))

        return({
          date: stage.fecha_registro,
          title: this.stages.find( s => s.id === stage.etapa ).nombre,
          description: '',
          footer: '',
          events
        });

      });

      return movements;

    },

    /*Retorna la plantilla opcional de una imagen*/
    getOptionalImageTemplate(){

      const template = this.hiddenTemplates.find( t => t.nombre === "imagenOpcionalSecret" );

      return ({template, picture: {} , index: this.item.imageFiles.length, loaded: false, generic: true});


    },

    /*Retorna la plantilla opcional de un documento*/
    getOptionalDocumentTemplate(){

      const template = this.hiddenTemplates.find( t => t.nombre === "documentoOpcionalSecret" ) || {};

      return ({template, document: {} , generirc: true, index: this.item.documentFiles.length, loaded: false});

    },

    getTotalDurationFromPreapplication(){

      //Fecha con la que se hace la comparacion
      const discriminator = this.item.etapas.length ?  this.item.etapas[0].fecha_registro : this.item.fecha_creacion;

      const duration = parseFloat( getDurationBetween( null , discriminator ) );

      const timeSpend = moment.duration( parseFloat(duration) , "hours");

      const durationString =  `${timeSpend.years() ? timeSpend.years() + " Años" : ""} ${timeSpend.months() ? timeSpend.months() + " Mese(s)" : ""} ${timeSpend.days() ? timeSpend.days() + " Dia(s)" : ""} ${timeSpend.hours() ? timeSpend.hours() + " Hora(s)" : ""}  ${timeSpend.minutes()} Minuto(s)`;

      return `Duración total: ${' '} ${' '} ${durationString}`

    },

    getStagesDuration(){

      const stages = this.stages.map( s => {

        const stageIndex = this.item.etapas.map( e => parseInt(e.etapa) ).lastIndexOf( parseInt(s.id) );

        let durationString = "aun no se llega a esta etapa";

        if( stageIndex > -1 ){

          const currentStage = this.item.etapas[stageIndex];
          let timeSpend = 0;

          if( parseFloat(currentStage.duracion) <= 0 ){

            const date =  currentStage.fecha_registro
            const auxDuration = getDurationBetween( null , date);

            //this.debugStuff({auxDuration,date}, "hotpink", "Duracion auxiliar" );

            timeSpend = moment.duration( parseFloat( auxDuration ) , "hours" );

          }else{

            timeSpend = moment.duration( parseFloat(currentStage.duracion) , "hours");
          }


          //this.debugStuff({timeSpend, currentStage}, "hotpink", "Calculando duracion de etapas");

          durationString =  `${timeSpend.years() ? timeSpend.years() + " Años" : ""} ${timeSpend.months() ? timeSpend.months() + " Meses" : ""} ${timeSpend.hours() ? timeSpend.hours() + " Horas(s)" : ""}  ${timeSpend.minutes()} Minuto(s)`;


        }



        return({
          ...s,
          duration: `${s.nombre}: ${durationString}`
        })

      })

      return stages;


    },

    async toggleChatModal(){

      if(!this.showChat){

        let _messages = await this.getMessages();

        if(_messages && Array.isArray(_messages)){

          const messages = _messages.map( message => {

            return {
              id: message.id,
              userId: message.id_usuario,
              body: message.pregunta,
              id_preapplication: message.id_presolicitud,
              date: message.fecha,
              sender: message.usuarioPregunta ? message.usuarioPregunta.nombre : "Luis"
            }

          });

          this.messages = messages;

          this.$forceUpdate();

        }

        this.debugStuff(_messages, "hotpink", "Aqui estan los mensajes");

      }

      this.showChat = !this.showChat;
    },

    cambiarMoneda(nueva_moneda){
      this.item.moneda_seleccionada = nueva_moneda;
    },

    inicializarFormulario() {
      const self = this;

      self.limpiarItem();

      setTimeout(function() {
        const $contenedor = $(self.$refs['contenedor_fotos']);

        $('.contenedor-galeria-items').sortable({
          items: '.galeria-item',
          tolerance: 'pointer',
          update: function() {
            const $items = $contenedor.find('.galeria-item');

            $items.each(function() {
              const $item = $(this);

              for (const indice in self.item.items) {
                if (self.item.items.hasOwnProperty(indice)) {
                  if (self.item.items[indice]._key == $item.data('key')) {
                    self.item.items[indice].indice = $item.index();
                    break;
                  }
                }
              }
            });
          },
        });
      }, 2000);
    },

    agregarItem(nombre_foto, atributos, documento) {
      if (typeof atributos === 'undefined' || atributos === null) {
        atributos = [];
      }

      this.item.fotos.push({
        id: 0,
        nombre: '',
        tipo: 1,
        foto: nombre_foto,
        indice: this.item.items.length,
        kbs: typeof atributos['kbs'] !== 'undefined' ? atributos['kbs'] : 0,
        ancho: typeof atributos['ancho'] !== 'undefined' ? atributos['ancho'] : 0,
        alto: typeof atributos['alto'] !== 'undefined' ? atributos['alto'] : 0,
        camara: ((atributos['marca'] || '') + ' ' + (atributos['modelo'] || '')).trim(),
        latitud: typeof atributos['latitud'] === 'string' ? atributos['latitud'] : null,
        longitud: typeof atributos['longitud'] === 'string' ? atributos['longitud'] : null,
        visible: true,
        _key: uniqueNumber(),
        documentoAlQuePertenece: documento
      });




    },

    actualizarNombre(nombre, item_key) {
      const indice = this.indiceParaKey(item_key);
      if (indice === null) return;
      this.item.items[indice].nombre = nombre;
    },

    actualizarTipo(id_tipo, item_key) {
      const indice = this.indiceParaKey(item_key);
      if (indice === null) return;
      this.item.items[indice].tipo = id_tipo;
    },

    eliminarItem(item_key) {
      const indice = this.indiceParaKey(item_key);
      if (indice === null) return;
      this.item.items.splice(indice, 1);
    },

    cambiarVisibilidad(item_key) {
      const indice = this.indiceParaKey(item_key);
      if (indice === null) return;
      this.item.items[indice].visible = !this.item.items[indice].visible;
    },

    indiceParaKey(item_key) {
      for (const indice in this.item.items) {
        if (this.item.items.hasOwnProperty(indice)) {
          if (this.item.items[indice]._key == item_key) {
            return indice;
          }
        }
      }
      return null;
    },

    nombreCliente(item) {
      return item.nombre_cliente + (typeof item.nombre_dni === 'string' && item.nombre_dni.length ? (' (' + item.nombre_dni + ')') : '');
    },

    getFileName(file){

      if( !file.template )
        return ""

      if( file.template.opcional === undefined || file.template.opcional === null )
        return `${file.template.nombre}`;

      return `${file.template.nombre}${file.template.opcional === true ? "" : "*"}`

    },

    async getMessages(){

      try{

        const response = await this.$http.get(this.urls.get, {
          params: {
            _fuente: 'IntranetPreguntaPresolicitud',
            id: this.item.id,
            _accion: 'preguntas'
          }
        });

        this.debugStuff(response, "hotpink" , "Respuesta obtener todas las preguntas");

        if(response.data.ok)
          return response.data.preguntas;

      }catch(e){

        this.debugError(e);
        mensajeError('Error de servidor.');

      }

      return null

    },

    nombreNegocio(item) {
      return item.negocio_cliente + (typeof item.ruc_cliente === 'string' && item.ruc_cliente.length ? (' (' + item.ruc_cliente + ')') : '');
    },

    setDropzoneActive( zone, data, event ){
      this.debugStuff({event}, "hotpink", "setDropzoneActive")

      this.dropzoneActive = zone;
    },

    getDropzoneClasses(zone){

      if( this.dropzoneActive === zone)
        return {"zone-active": true};
      else{
        return null;
      }


    },

    detalleMonto(item) {

      const moneda_simbolo = (monedas.find( x => parseInt(x.id) === parseInt(item.moneda) ) || {}).simbolo;

      return `${moneda_simbolo} ${(parseFloat(item.monto_solicitado) || 0).toFixed(2)}`
    },

    getEtapaActual( idEtapa ){

      return this.stages.find( x => x.id === parseInt(idEtapa) ).nombre

    },

    getEstadoDelCredito( estadoDeVida ){

      return estadoDeVida === 1 ? "En proceso" : "Rechazado"

    },

    mostrarIndex() {
      this.vista_formulario = false;
      this.vista_impresion = false;
      this.vista_items = true;

      this.$forceUpdate();

      /*const $doc = $(document);
      $doc.find('section.form').hide();
      $doc.find('section.form').hide();*/
    },

    handleDropParticipant(section , user , event){

      if(section === 'supervisor'){
        this.participants.supervisor = user;
      }

      else if( section === 'committee'){

        const idx = this.participants.committee.findIndex( u => u.id === user.id );

        if( idx > -1 ){
          this.participants.committee[idx] = user;
        }else{
          this.participants.committee.push(user);
        }

      }

      else if( section === 'operations'){

        const idx = this.participants.operations.findIndex( u => u.id === user.id );

        if( idx > -1 ){
          this.participants.operations[idx] = user;
        }else{
          this.participants.operations.push(user);
        }

      }

      else{
        this.participants.executive = user;
      }

      if(this.item.id){

        this.addParticipant(user, section)

      }

      this.dropzoneActive = '';

    },

    async addParticipant(user, section){

      try{

        let role = null;

        switch(section){
          case 'supervisor':
            role = 2;
            break;
          case 'committee':
            role = 3;
            break;
          case 'operations':
            role = 4;
            break;
          case 'executive':
            role = 1;
            break;
        }

        const form = this.createFormData({
          _fuente: 'IntranetPresolicitudRole',
          _accion: 'crearRolUsuario',
          id_presolicitud: this.item.id,
          id_role: role,
          id_usuario: user.id,
          fecha: moment().format(defaultDateFormat)
        })

        const response = await this.$http.post(
            this.urls.post,
            form,
            this.$defaultConfig
        )

        this.debugStuff(response, "hotpink","update role");

        return response;

      }catch(error){
        mensajeError(error);
        this.debugError(error);
      }

    },

    async deleteParticipant(user, section){

      try{

        let role = null;

        switch(section){
          case 'supervisor':
            role = 2;
            break;
          case 'committee':
            role = 3;
            break;
          case 'operations':
            role = 4;
            break;
          case 'executive':
            role = 1;
            break;
        }


        this.debugStuff(user,"usuario","usuario eliminado")

        const form = this.createFormData({
          _fuente: 'IntranetPresolicitudRole',
          _accion: 'eliminarRolUsuario',
          id_presolicitud: this.item.id,
          id_usuario: user.id,
          id_role: role,
          fecha: moment().format(defaultDateFormat)
        })

        const response = await this.$http.post(
            this.urls.post,
            form,
            this.$defaultConfig
        )

        this.debugStuff(response, "hotpink","eliminar role");

        if(response.data.ok){

          let index;

          switch(section){
            case 'supervisor':
              this.supervisor = {};
              break;
            case 'commitee':
              index = this.participants.committee.findIndex( x.name === user.name );

              if(index > -1){
                this.participants.committee.splice( index , 1 );
              }

              break;
            case 'operations':
              index = this.participants.operations.findIndex( x.name === user.name );

              if(index > -1){
                this.participants.operations.splice( index , 1 );
              }
              break;
            case 'executive':
              this.executive = {};
              break;
          }

        }

        return response;

      }catch(error){

        mensajeError(error);
        this.debugError(error);

      }

    },

    mostrarEstadoPresolicitud(item){

      //this.itemRevisando = { ...item };

      const self = this

      for (const llave in this.items) {
        if (this.items.hasOwnProperty(llave)) {
          if (this.items[llave].id === item.id) {
            this.$http.get(this.urls.get, {
              params: {
                _fuente: this.fuente,
                id: item.id
              }
            })
                .then(response => {

                  //this.debugStuff({response,item},'pink' )

                  if (response.status === 200) {
                    const data = response.data;

                    self.itemRevisando = {...data};

                    self.vista_items = false;
                    self.vista_formulario = false;
                    self.vista_estado_presolicitud = true;

                  }
                })
                .catch( err => {
                  this.debugError(err);
                });
            break;
          }
        }
      }

    },

    ocultarVistaEstadoPresolicitud(){

      this.vista_items = true;
      this.vista_formulario = false;
      this.vista_estado_presolicitud = false;

    },

    cambiarOrden(id_ordenar_item) {
      this.id_ordenar_item = id_ordenar_item;
      this.verImprimirItem({id:this.id_item});
    },

    descripcionOrdenarItem() {
      for (const ordenar_item of this.ordenar_items) {
        if (this.id_ordenar_item == ordenar_item.id) {
          return ordenar_item.nombre;
        }
      }
      return '';
    },

    setGrabbing(d,e){
      event.dataTransfer.dropEffect = "move";
      e.preventDefault();
      e.target.style.cursor = "grabbing"
    },

    showImageInGalery( imageIndex ){

      //this.debugStuff(imageIndex, "hotpink", "index");

      this.startPictureId = imageIndex;

    },

    closeGalery(){
      this.startPictureId = null
    },

    appendAnswers({answers, index}){
      this.debugStuff({answers,index},"hotpink", "appendAnswers")

      if(Array.isArray(answers))
        this.messages[index].answers = answers;
      else{

        if( !this.messages[index].answers ){

          this.messages[index].answers = [];

        }

        this.messages[index].answers.push(answers);
      }
      this.$forceUpdate();
    },

    toggleCreditBoard(){
      this.showCreditBoard = !this.showCreditBoard;
    }

  },

  computed: {

    imageFiles: function(){

      function getImages( folder, rootFolderId, stage ){

        let images = folder.documentos.filter(d => d.tipo === 1 && ( d.nombre !== "foto_generico" || d.detalle ) )
        images = images.map( (p,index) => {

          const { detalle  , ...rest } = p;

          if(!detalle){
            return ({ stage, picture: {}, id_root_folder: rootFolderId , id_folder: folder.id, template: rest , observations: '', index, loaded: detalle ? true : false });
          }

          const {metadata, ...picture} = detalle;

          const theMetadata = metadata || {};

          return ({ stage, picture: { id_picture: picture.id, ...picture,...metadata}, id_root_folder: rootFolderId , id_folder: folder.id, template: rest , observations: '', index, loaded: detalle ? true : false});

        });

        if( !folder.subCarpetas || !folder.subCarpetas.length){

          return images;

        }

        const _images = folder.subCarpetas.reduce( ( imageFiles , currentFolder ) => {

          return [...imageFiles , ...getImages(currentFolder, rootFolderId, stage) ]

        }, []);


        return [...images, ..._images];


      }

      const _imageFiles = (this.folders || []).reduce( ( imageFiles , currentFolder ) => {

        if(!currentFolder.isButton && !currentFolder.isSubFolder){

          const index = this.rootFoldersIds.length - 2 < 0 ? 0 : this.rootFoldersIds.length - 2;

          const rootFolderId = this.rootFoldersIds[index];

          const stage = this.rootFoldersIds.length < 1 ? null :  this.rootFoldersIds[1];

          this.debugStuff(rootFolderId, "hotpink","carpeta padre");

          return [...imageFiles , ...getImages(currentFolder, rootFolderId || currentFolder.id , stage || currentFolder.id ) ];
        }

        return[...imageFiles];


      }, []);


      this.debugStuff( _imageFiles , "hotpink", "Todos los documentos" );

      return _imageFiles.map( (i,index) => ({...i , index}) );

    },

    documentFiles: function(){
      function getDocuments( folder , rootFolderId, stage ){

        let documents = folder.documentos.filter(d => d.tipo !== 1)

        documents = documents.map( (d,index) => {

          return({
            stage,
            document: {
              nombre: d.nombre ,
              id_root_folder: rootFolderId,
              id_folder: folder.id,
              link: (d.detalle || {} ).nombre || ""} ,
            template: d ,
            observations: '' ,
            index,
            loaded: d.detalle ? true : false
          });
        });

        if( !folder.subCarpetas || !folder.subCarpetas.length){
          return documents;
        }


        const _documents = folder.subCarpetas.reduce( ( documentFiles , currentFolder ) => {

          return [...documentFiles , ...getDocuments( currentFolder, rootFolderId, stage ) ]

        }, []);

        return [...documents, ..._documents];


      }

      const _documentFiles = (this.folders || []).reduce( ( documentFiles , currentFolder ) => {

        if(!currentFolder.isButton && !currentFolder.isSubFolder){

          const index = this.rootFoldersIds.length - 2 < 0 ? 0 : this.rootFoldersIds.length - 2;

          const rootFolderId = this.rootFoldersIds[index];

          const stage = this.rootFoldersIds.length < 1 ? null :  this.rootFoldersIds[1];

          this.debugStuff(rootFolderId, "hotpink","carpeta padre");

          return [...documentFiles , ...getDocuments(currentFolder, rootFolderId || currentFolder.id , stage || currentFolder.id ) ];
        }

        return[...documentFiles];


      }, []);


      this.debugStuff( _documentFiles , "hotpink", "Todos los documentos documentos" );

      return _documentFiles.map( (d,index) => ({...d, index}));

    },

    genericPictureTemplate: function(){

      if( !Object.entries(this.genericTemplates).length){

        return {};
      }

      const others = {id: 6}

      const { detalle  , ...rest } = this.genericTemplates.foto_generico

      if(!detalle)
        return ({ picture: {}, id_root_folder: others.id , id_folder: others.id, template: rest , observations: '', index: -1, loaded: detalle ? true : false });

      const {metadata, ...picture} = detalle;

      const theMetadata = metadata || {};

      return ({ picture: { ...picture,...metadata}, id_root_folder: others.id , id_folder: others.id, template: rest , observations: '', loaded: detalle ? true : false});

    },

    genericDocumentTemplate: function(){

      if( !Object.keys(this.genericTemplates).length){
        return {};
      }


      const others = {id:6};

      const d = this.genericTemplates.pdf_generico;

      return( {document: {nombre: d.nombre , id_root_folder: others.id,  id_folder: others.id, link: (d.detalle || {} ).nombre || ""} , template: d , observations: '' , loaded: d.detalle ? true : false });

    }


  },

  mounted() {
    this.cargarData();
  },
}
</script>

<style scoped>

.VueCarousel-slide{

  flex-basis: unset;

}

.preapplication-view{
  contain: content;
}

.input-imagen {
  width: calc(100% * (1/3) - 30px);
}

.input-archivo {
  min-width: 300px;
}

.icono-barra {
  width: 24px;
  height: 24px;
  filter: grayscale(1) invert(1);
}
.icono-barra.seleccionado {
  filter: unset !important;
}

.boton-orientacion {
  border-right: 1px solid #999;
  padding-right: 5px;
}

.boton-orientacion i {
  font-size: 24px;
}

.button-group a {
  margin-right: -4px !important;
  border-radius: 0;
}

.button-group a:first-child {
  border-bottom-left-radius: 4px;
  border-top-left-radius: 4px;
}

.button-group a:last-child {
  border-bottom-right-radius: 4px;
  border-top-right-radius: 4px;
  margin-right: 3px !important;
}

.button-group a.activo {
  color: #fff;
}

.file-slide{
  width:150px;
  height: 150px;
  display: flex;
  align-items: center;
  padding: 0;
  margin: auto 0;
  color: gray;
  flex-direction: column;
  border: 3px solid gray;
  padding: 2rem;
  border-radius: 8px;
  position: relative;
}

.file-slide:hover .add-document {
  display: flex !important;
}

.file-slide:hover{
  border: none;
}

.input-imagen{
  width:300px;
}

.file-slide i{
  font-size: 50px;
  color: gray
}

.folder-card{
  width: 125px;
  height: 125px;
  display: flex;
  align-items: center;
  border: 2px solid blue;
  border-radius: 8px;
  flex-direction: column;
  color: blue;
  margin-left: 3.5rem;
  padding: 1rem;
  text-align:  center;
  cursor: pointer;
}

.folder-card:hover{
  background-color: blue;
  color: white !important;
  border: none;
}

.add-document{
  width:150px;
  height: 150px;
  background-color: blue;
  opacity: 0.8;
  border-radius: 8px;
  border: 3px solid gray;

  display: none;
  position:absolute;
  top:0;
  left: 0;
  padding: 2.5rem;
  flex-direction: column;
  align-items: center;
}

.add-document i{
  font-size: 40px;
  color: white;
  opacity: 1;
}

.add-document span{
  font-size: 2rem;
  color: white;
  font-weight: bold;
}

.folder-card i{
  font-size: 35px;
}

.selected{
  background-color: blue;
  color: white !important;
}


.contenedor-iconos {
  width: 100%;
  display: flex;
  justify-content: space-around;
  margin: 20px 0;
  background-color: #fff;
  border-radius: 20px;
  padding: 6px;
  position: relative;
  z-index: 10;
  --stepper-width: 0%;
}

.contenedor-iconos::before {
  content: '';
  width: 80%;
  height: 10px;
  top: 40%;
  background-color: gray;
  position: absolute;
  z-index: 1;
}

.contenedor-iconos::after {
  content: '';
  width: var(--stepper-width);
  max-width: 80%;
  left: 10%;
  height: 10px;
  top: 40%;
  background-color: blue;
  position: absolute;
  z-index: 2;
  transition: width 600ms ease;

}

.contenedor-icono {
  cursor: pointer;
  position: relative;
  z-index: 10;
}

.icono-etapa {
  width: 80px;
  height: 80px;
  background-color: gray;
  border-radius: 50%;
  font-size: 50px;
  display: flex;
  align-items: center;
  position: relative;
  padding: 1rem;
  margin: 0 auto;
}

.d-none {
  display: none !important;
}

.stepper-acciones{
  display: flex;
  flex-direction: column;
  position: absolute;
  bottom: 10px;
  left: 0;
}

.stepper-acciones i {
  display: none;
  border-radius: 50%;
  color: white;
  width: 30px;
  height: 30px;
  background-color: black;
  padding: 0.40rem;
  margin-bottom: 2px;
}

.etapa-revisada > i {
  background-color: #2a41e8;
  color: white;
}

.etapa-no-revisada .stepper-acciones{
  display: none !important;
}

.etapa-actual > i {
  padding: 1.55rem;
  width: 100px;
  height: 100px;
}

.etapa-actual .stepper-acciones{
  bottom: 0;
}


.stepper-acciones i:first-child {
  display: block;
}

.stepper-acciones:hover {
  bottom: -64px;
}

.stepper-acciones:hover i{
  display: block;
}

.list-item{
  padding: 0 !important;
}

.list-item:hover{
  cursor: grab;
}

.dragging{
  cursor: grabbing !important;
}

.participants-section{
  display:flex;
  justify-content: center;
  flex-wrap: wrap;
  height: 200px;
  margin-bottom: 0.8rem;
  padding: .5rem;
  padding: 1rem;
}

.participant{
  height: 100px;
  min-width: 150px;
  display: flex;
  flex-direction: column;
  margin-left:1rem;
}

.participant-image{
  text-align: center;
  height: 80%;
  position: relative;
}

.participant-actions{
  display: none;
  position: absolute;
  flex-direction: column;
  padding: 0.25rem;
  top: 0;
  right: 25%;
  font-size: 20px;
}

.participant-actions span{
  cursor: pointer;
  width: 25px;
  height: 25px;
  background-color: #666;
  color: white;
  border-radius: 50%;
  vertical-align: middle;
  justify-content: center;
}

.participant-actions span i {
  padding: 3px;
}

.participant-image:hover .participant-actions{
  display: flex;
}


.participant-image img{
  max-height: 100%;
}

.participant span{
  text-align: center;
  font-weight: bold;
}

.dropzone{
  width: 100%;
  height: 100%;
  background-color: transparent;
  display: flex;
  flex-direction: column;
  position: absolute;
  margin-bottom: .5rem;
  text-align: center;
  font-weight: bold;
  justify-content: center;
  vertical-align: middle;
  border-radius: 20px;
  top: 7%;
}

.dropzone *{
  pointer-events: none;
}

.dropzone i,
.dropzone span{
  display:none;
  color: #fff;
}

.active i,
.active span{
  display: inline;
}

.dropzone i {
  font-size: 50px;
}

.active{
  background-color: rgba(0,0,0,.5);
}

.helper{
  display: none;
}

.active .helper {

  display: flex;
  flex-direction: column;
  text-align: center;
  color: #fff;
  width: 100%;
  height: 100%;
  top: 0;
  left: 0;
  visibility: visible;
}

.helper-content {
  width: 40%;
  height: 100%;
  margin: auto auto;
}


.helper i {
  font-size: 50px;
}

.participants-section-dropzone{
  display: flex;
  position: relative;
  width: 100%;
  height: 150px;
  margin-top: 1rem;
}

.participants-section{
  display:flex;
  width: 100%;
  height: 150px;
}

.user-container{
  display: flex;
  max-height: 50px;
  padding: .5rem;
}

.user-list-avatar{
  max-height: 100%;
}

.user-list-avatar img{
  max-height: 30px;
  border-radius: 50%;
  margin-right: .8rem;
  max-width: 30px;
}

.users-list{
  list-style: none;
  padding: 0.5rem;
  font-weight: bold;
}

.users-list li{
  padding:0rem;
}

.participants{
  width: 100%;
  height: 100%;
  padding: 1.5rem;

}

.participants h4{
  font-weight: bold;
  text-align: center;
  color: gray;
}

.participants-box{
  display:flex;
  justify-content: center;
  overflow-x: scroll;
}

.VueCarousel-slide {
  position: relative;
  color: #fff;
  text-align: center;
}

.galery-carousel
.VueCarousel-wrapper
.VueCarousel-inner
.VueCarousel-slide{
  flex-basis: inherit !important;
  text-align: center;
  display: flex;
  justify-content: center;
}
</style>