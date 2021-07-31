<template>

  <div class="galeria-item freelancer unpop small sin-transicion" :class="[!activo ? 'inactivo' : '', cargando ? 'cargando' : '']" :data-id="item.id" :data-key="item._key" ref="main_item">

    <!-- Overview -->
    <div class="freelancer-overview" :style="'background-image:url(' + urlFoto + ')'">
      <div class="freelancer-overview-inner">

        <!-- Actions -->
        <div class="item-actions" :class="acciones_expandidas ? 'expandido' : ''" @mouseover="expandirAcciones" @mouseleave="ocultarAcciones">
                    <span class="bookmark-icon" @click="rotar">
                        <i class="icon-feather-rotate-ccw"></i>
                    </span>
          <span class="bookmark-icon remove" @click="quitar">
                        <i class="icon-feather-trash-2"></i>
                    </span>
          <span class="bookmark-icon" @click="cambiarVisibilidad">
                        <i :class="activo ? 'icon-feather-eye' : 'icon-feather-eye-off'"></i>
                    </span>
        </div>

      </div>
    </div>

    <input type="text" :name="nombre + '_titulo[]'" class="custom-input" placeholder="Ingrese el título aquí" :value="item.nombre" @input="actualizarNombre" ref="input_nombre">

    <!-- Details -->
    <div class="freelancer-details">
      <div class="freelancer-details-list">
        <ul>
          <li>Ubicación <strong>
            <a :href="'http://www.google.com/maps/place/' + imageFile.latitud + ',' + imageFile.longitud" v-if="typeof imageFile.longitud === 'string' && imageFile.length" target="_blank">
              <i class="icon-material-outline-location-on"></i>
            </a>
            <span v-else>
                                &mdash;
                            </span>
          </strong>
          </li>
          <li>Dimensiones <strong>{{ imageFile.ancho || '' }}x{{ imageFile.alto || '' }}</strong></li>
          <li>Tamaño <strong>{{ imageFile.kbs || '0' }}Kbs</strong></li>
        </ul>
      </div>
      <div>
        <!--input-radios
                v-model="item.tipo"
                :nombre="nombre + '_tipo'"
                :arreglo="true"
                :items="tipos"
        ></input-radios-->
        <div class="contenedor-iconos">
          <div class="contenedor-icono"
               v-for="tipo in tipos"
               :key="tipo.id"
               :class="item.tipo == tipo.id ? 'seleccionado' : ''"
               :title="tipo.nombre"
               data-tippy-placement="top"
               @click="actualizarTipo(tipo.id)"
          >
            <figure class="icono" :class="tipo.clase" :style="'background-image:url(' + urlIcono(tipo.icono) + ')'"></figure>
          </div>
        </div>
      </div>
      <template v-if="false">
        <a href="#" class="button button-sliding-icon ripple-effect">View Profile <i class="icon-material-outline-arrow-right-alt"></i></a>
      </template>
    </div>

    <input type="hidden" :name="nombre + '_id[]'" :value="item.id">
    <input type="hidden" :name="nombre + '_indice[]'" :value="item.indice">
    <input type="hidden" :name="nombre + '_foto[]'" :value="item.foto">
    <input type="hidden" :name="nombre + '_tipo[]'" :value="item.tipo">
    <input type="hidden" :name="nombre + '_visible[]'" :value="activo ? 1 : 0">
    <input type="hidden" :name="nombre + '_kb[]'" :value="item.kbs">
    <input type="hidden" :name="nombre + '_camara[]'" :value="item.camara">
    <input type="hidden" :name="nombre + '_latitud[]'" :value="item.latitud">
    <input type="hidden" :name="nombre + '_longitud[]'" :value="item.longitud">

    <!--garantia
    negocio
    ubicacion
    inventario-->
  </div>
</template>

<script>
export default {
  name: "GaleriaItem.vue",

  props: {
    nombre: {
      required: true
    },
    item: {
      default: () => ({
        _key: 0,
        id: 0,
        nombre: '',
        tipo: 1,
        foto: '',
        visible: true,
        indice: 0,
        ancho: 0,
        alto: 0,
        kbs: 0,
        camara: '',
        latitud: '',
        longitud: '',
      })
    },
  },

  data: () => ({
    activo: true,
    cargando: false,
    acciones_expandidas: false,
    timestamp: 0,
    tipos: [
      {
        id: 1,
        nombre: 'Garantía',
        clase: 'icono-garantia',
        icono: 'icono-garantia-c.png'
      },
      {
        id: 2,
        nombre: 'Negocio',
        clase: 'icono-negocio',
        icono: 'icono-negocio-c.png'
      },
      {
        id: 3,
        nombre: 'Ubicación',
        clase: 'icono-ubicacion',
        icono: 'icono-ubicacion-c.png'
      },
      {
        id: 4,
        nombre: 'Inventario',
        clase: 'icono-inventario',
        icono: 'icono-inventario-c.png'
      },
      {
        id: 5,
        nombre: 'Documento',
        clase: 'icono-documento',
        icono: 'icono-documento-c.png'
      }
    ]
  }),

  computed: {
    urlFoto() {
      if (typeof this.item.foto !== 'string' || !this.item.foto.length) {
        return this.$img_placeholder;
      }

      let cache = '';
      if (this.timestamp) {
        cache = '?c=' + this.timestamp.toString();
      }

      return this.$uploads_img_dir + 'm/' + this.item.foto + cache;
    }
  },

  methods: {
    estadoCargando(estado) {
      if (estado) {
        this.$refs['main_item'].classList.add('cargando');
      } else {
        this.$refs['main_item'].classList.remove('cargando');
      }
    },

    expandirAcciones() {
      this.acciones_expandidas = true;
    },

    ocultarAcciones() {
      this.acciones_expandidas = false;
    },

    cambiarVisibilidad() {
      this.estadoCargando(true);

      this.debugStuff(this.imageFile)

      this.$http.post(this.$url_post, {
        _fuente: 'GaleriaCredito',
        _accion: 'cambiarVisibilidad',
        id: this.item.id,
        visible: !this.activo,
      })
          .then(response => {
            if (response.status === 200) {

            }
            else if (response.status === 500) {
              mensajeError('Error de servidor.');
            }

            this.estadoCargando(false);
          });

      this.activo = !this.activo;
      this.$emit('visibilidadCambiada', this.item._key);
    },

    quitar() {
      this.$emit('eliminarItem', this.item._key);
    },

    rotar() {
      this.estadoCargando(true);

      const self = this;

      this.$http.post(this.$url_post, {
        _fuente: 'GaleriaCredito',
        _accion: 'rotar',
        nombre_foto: self.item.foto,
      })
          .then(response => {
            if (response.status === 200) {
              resultadoSolicitudDefecto(response.data);

              if (response.data.ok) {
                //se cambia el timestamp para que se actualice la foto en el navegador
                self.timestamp = (new Date()).getTime();
                //se invierte el valor de ancho y alto
                const ancho = self.item.ancho;
                self.item.ancho = self.item.alto;
                self.item.alto = ancho;
                //self.$emit('ItemRotado', self.item._key);
              }
            }
            else if (response.status === 500) {
              mensajeError('Error de servidor.');
            }

            this.estadoCargando(false);
          });
    },

    actualizarNombre() {
      this.$emit('nombreActualizado', this.$refs['input_nombre'].value, this.item._key);
    },

    actualizarTipo(id_tipo) {
      this.$emit('tipoActualizado', id_tipo, this.item._key);
    },

    urlIcono(icono) {
      return this.$public_dir + 'img/' + icono;
    }
  },

  mounted() {
    initTooltips();

    this.activo = !!this.item.visible;
  }
}
</script>

<style scoped>
.galeria-item.inactivo {
  opacity: .6;
}

.galeria-item.cargando {
  filter: blur(1px);
  pointer-events: none;
}

.freelancer-overview {
  position: relative;
  min-height: 250px;
}

.freelancer-details {
  padding: 5px;
}

.freelancer-details-list ul {
  text-align: center;
  margin-bottom: -12px;
}

.custom-input {
  border: 0;
  box-shadow: none;
  margin-bottom: 0;
}

.item-actions .bookmark-icon {
  visibility: hidden;
  transition-delay: .1s;
}

.item-actions .bookmark-icon:last-child {
  visibility: visible;
}

.item-actions .remove {
  background-color: red;
}

.item-actions.expandido .bookmark-icon:nth-child(1)  { visibility: visible; top:  80px !important; }
.item-actions.expandido .bookmark-icon:nth-child(2)  { visibility: visible; top: 125px !important; }

.contenedor-iconos {
  width: 100%;
  display: flex;
  justify-content: space-around;
  margin: 5px 0;
  background-color: #fff;
  border-radius: 20px;
  padding: 6px;
}

.contenedor-icono {
  cursor: pointer;
}
.contenedor-icono:hover .icono {
  opacity: .8;
}
/*.contenedor-icono.seleccionado {
    background-color: #2a41e8;
    width: 34px;
    height: 34px;
    padding: 5px;
    margin-top: -5px;
}*/

.icono {
  width: 24px;
  height: 24px;
  background: transparent no-repeat center center;
  background-size: cover;
  margin: 0;
  filter: brightness(0);
}
.contenedor-icono.seleccionado .icono {
  /*filter: brightness(0) invert(1);*/
  /*filter: hue-rotate(180deg);*/
  filter: unset;
}
</style>