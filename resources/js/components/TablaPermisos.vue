<template>
    <div>
        <div class="row">
            <div class="col-xl-1">
                &nbsp;
            </div>

            <div class="col-xl-4">
                <div class="switches-list" ref="contenedor">
                    <div class="switch-container">
                        <label class="switch">
                            <input type="checkbox" name="permisos[]" :value="nombre + '|consultar'" :checked="consultar" @change="verificarEstado('consultar', $event)" data-accion="consultar">
                            <span class="switch-button"></span> Consultar
                        </label>
                    </div>
                    <div class="switch-container">
                        <label class="switch">
                            <input type="checkbox" name="permisos[]" :value="nombre + '|crear'" :checked="crear" @change="verificarEstado('crear', $event)" data-accion="crear">
                            <span class="switch-button"></span> Crear
                        </label>
                    </div>
                    <div class="switch-container">
                        <label class="switch">
                            <input type="checkbox" name="permisos[]" :value="nombre + '|editar'" :checked="editar" @change="verificarEstado('editar', $event)" data-accion="editar">
                            <span class="switch-button"></span> Editar
                        </label>
                    </div>
                    <div class="switch-container">
                        <label class="switch">
                            <input type="checkbox" name="permisos[]" :value="nombre + '|eliminar'" :checked="eliminar" @change="verificarEstado('eliminar', $event)" data-accion="eliminar">
                            <span class="switch-button"></span> Eliminar
                        </label>
                    </div>
                </div>
            </div>

            <div class="col-xl-6">
                <!--<div class="switches-list">
                    <div class="switch-container">
                        <label class="switch">
                            <input type="checkbox" name="permisos[]" :value="nombre + '|todos'">
                            <span class="switch-button"></span> Todos
                        </label>
                    </div>
                </div>-->
                <div class="seleccion-afecta" :data-categoria="nombre" v-show="mostrarInputAfecta" ref="campo_afecta">
                    <input-seleccion
                            :items="[
                                {
                                    id: 'todos',
                                    nombre: 'Todos',
                                },
                                {
                                    id: 'solo_propios',
                                    nombre: 'Propios',
                                },
                            ]"
                            nombre="permisos[]"
                            :value="afecta"
                    ></input-seleccion>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "TablaPermisos.vue",

        props: {
            nombre: {
                type: String,
                required: true,
            },
            padre: {},
            /*valores: {
                type: Object,
                default: {
                    consultar: false,
                    crear: false,
                    editar: false,
                    eliminar: false,
                    afecta: 'solo_propios',
                }
            },*/
            inicializacion: {
                type: Boolean,
                default: true,
            },
        },

        data: () => ({
            consultar: false,
            crear: false,
            editar: false,
            eliminar: false,
            afecta: null,
            fuente: null,
        }),

        computed: {
            mostrarInputAfecta() {
                return this.consultar || this.editar || this.eliminar;
            }
        },

        watch: {
            padre() {
                if (this.fuente !== 'self' && !this.inicializacion) {
                    this.consultar = !!this.padre;
                    this.crear = !!this.padre;
                    this.editar = !!this.padre;
                    this.eliminar = !!this.padre;
                }
            },

            inicializacion() {
                if (!this.inicializacion) {
                    const $contenedor = $(this.$refs['contenedor']);
                    const $contenedor_afecta = $(this.$refs['campo_afecta']);

                    this.consultar = $contenedor.find('input[data-accion="consultar"]').is(':checked');
                    this.crear = $contenedor.find('input[data-accion="crear"]').is(':checked');
                    this.editar = $contenedor.find('input[data-accion="editar"]').is(':checked');
                    this.eliminar = $contenedor.find('input[data-accion="eliminar"]').is(':checked');
                    this.afecta = $contenedor_afecta.find('select[name="permisos[]"]').val();//'todos';
                    //this.emitirCambio();
                }
            }
        },

        methods: {
            verificarEstado(key, e) {
                this.fuente = 'self';

                const seleccionado = $(e.target).is(':checked');

                this.$set(this, key, seleccionado);

                if (key === 'editar' || key === 'eliminar') {
                    if (seleccionado) {
                        this.consultar = true;
                        this.$forceUpdate();
                    }
                }
                else if (key === 'consultar') {
                    if (!seleccionado) {
                        this.editar = false;
                        this.eliminar = false;
                        this.$forceUpdate();
                    }
                }

                this.emitirCambio();

                setTimeout(() => {
                    this.fuente = null;
                }, 600);
            },

            emitirCambio() {
                if (!this.inicializacion) {
                    this.$emit('cambiado', this.nombre, (this.consultar && this.crear && this.editar && this.eliminar), (this.consultar || this.crear || this.editar || this.eliminar));
                }
            }
        }
    }
</script>

<style scoped>

</style>