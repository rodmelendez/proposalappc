<template>
    <main>
        <section class="index" v-if="vista_items">
            <!-- Dashboard Headline -->
            <div class="dashboard-headline">
                <div class="row">
                    <div class="col-sm-8">
                        <h3>
                            Empleados
                            <span class="item-cargando">&nbsp;<i class="icon-line-awesome-hourglass-2 fa-spin"></i></span>
                        </h3>
                    </div>

                    <div class="col-sm-4">
                        <div class="input-with-icon">
                            <input type="text" placeholder="Buscar..." v-model="texto_buscado" @dblclick="texto_buscado = ''">
                            <i class="icon-material-outline-search"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row -->
            <div class="row">

                <!-- Dashboard Box -->
                <div class="col-xl-12">
                    <div class="dashboard-box margin-top-0">

                        <!-- Headline -->
                        <div class="headline">
                            <h3><i class="icon-material-outline-supervisor-account"></i> {{ items.length }} {{ items.length === 1 ? 'empleado' : 'empleados' }}</h3>

                            <div class="actions">
                                <button type="button" class="popup-with-zoom-anim button dark ripple-effect" @click="mostrarFormularioNuevo">
                                    <i class="icon-feather-plus"></i> Nuevo
                                </button>
                            </div>
                        </div>

                        <div class="content" :class="clasesTipoVista">
                            <ul class="dashboard-box-list">
                                <template v-for="item in items"
                                            appear
                                            mode="out-in"
                                            enter-active-class="animated fadeIn"
                                            leave-active-class="animated zoomOut"
                                >
                                    <li v-show="esVisible(item)" @click="expandir" :key="item.id">
                                        <!-- Overview -->
                                        <div class="freelancer-overview manage-candidates">
                                            <div class="freelancer-overview-inner">

                                                <!-- Avatar -->
                                                <div class="freelancer-avatar">
                                                    <div class="verified-badge"></div>
                                                    <a href="#"><img :src="avatarUrl(item)" alt=""></a>
                                                </div>

                                                <!-- Name -->
                                                <div class="freelancer-name">
                                                    <h4 :class="item.status == 2 ? 'item-inhabilitado' : ''">
                                                        <span v-html="iconoStatus(item)"></span>{{ nombrePersona(item) }}<!-- <mark class="color">{ { item.nombre }}</mark>-->
                                                    </h4>

                                                    <!-- Details -->
                                                    <div>
                                                        <span class="freelancer-detail-item" v-if="item.id_empresa">
                                                            <span><i class="icon-material-outline-business"></i> {{ item.empresa/*nombreEmpresa(item.id_empresa)*/ }}</span>
                                                        </span>
                                                        
                                                        <span class="freelancer-detail-item" v-if="item.id_sucursal">
                                                            <span><i class="icon-feather-square"></i> {{ nombreSucursal(item.id_sucursal) }}</span>
                                                        </span>
                                                        
                                                        <span class="freelancer-detail-item" v-if="item.id_departamento">
                                                            <span><i class="icon-feather-grid"></i> {{ nombreDepartamento(item.id_departamento) }}</span>
                                                        </span>

                                                        <span class="freelancer-detail-item" v-if="item.id_departamento">
                                                            <span><i class="icon-line-awesome-tag"></i> {{ item.num_control }}</span>
                                                        </span>
                                                    </div>
                                                    
                                                    <div>
                                                        <span class="freelancer-detail-item" v-if="item.correo">
                                                            <a :href="'mailto:' + item.correo"><i class="icon-feather-mail"></i> {{ item.correo }}</a>
                                                        </span>

                                                        <span class="freelancer-detail-item" v-if="item.telefono">
                                                            <a :href="'tel:' + item.telefono"><i class="icon-feather-phone"></i> {{ item.telefono }}</a>
                                                        </span>

                                                        <div>
                                                            <span class="freelancer-detail-item">
                                                                <strong>Usuario:</strong> <span>{{ item.nombre }}</span>
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <transition
                                                            appear
                                                            mode="out-in"
                                                            enter-active-class="animated zoomInDown"
                                                            leave-active-class="animated zoomOutUp"
                                                    >
                                                        <div class="notification warning closeable margin-top-5 detail" v-show="typeof item.codigo_qr === 'string' && item.codigo_qr.length">
                                                            <div class="codigo-qr-holder" v-html="item.codigo_qr">

                                                            </div>
                                                            <a class="close" @click="ocultarCodigoQr(item)"></a>
                                                        </div>
                                                    </transition>

                                                    <!-- Buttons -->
                                                    <div class="buttons-to-right always-visible margin-top-25 margin-bottom-5">

                                                        <a href="#" class="button gray ripple-effect ico ico-text detail" title="Carnet" @click="vistaImprimirCarnet(item)">
                                                            <i class="icon-material-outline-person-pin"></i> <span>Carnet</span>
                                                        </a>

                                                        <a href="#" class="button gray ripple-effect ico ico-text detail" title="Permisos" v-if="esAdmin()" @click="vistaPermisos(item)">
                                                            <i class="icon-feather-user-check"></i> <span>Permisos</span>
                                                        </a>

                                                        <a href="#" class="button gray ripple-effect ico ico-text detail" :title="item.status == 2 ? 'Habilitar' : 'Inhabilitar'" @click.prevent="inhabilitarItem(item)">
                                                            <i :class="item.status == 2 ? 'icon-feather-check-circle' : 'icon-feather-slash'"></i> <span>{{ item.status == 2 ? 'Habilitar' : 'Inhabilitar' }}</span>
                                                        </a>

                                                        <a href="#" class="button gray ripple-effect ico ico-text detail" title="Metas" @click.prevent="mostrarMetas(item)">
                                                            <i class="icon-line-awesome-crosshairs"></i> <span>Metas</span>
                                                        </a>

                                                        <a href="#" class="button gray ripple-effect ico ico-text detail" title="Ubicaciones" @click.prevent="mostrarUbicaciones(item)">
                                                            <i class="icon-line-awesome-map-marker"></i> <span>Ubicaciones</span>
                                                        </a>

                                                        <a href="#" class="button gray ripple-effect ico detail" title="Código QR" @click.prevent="mostrarCodigoQrItem(item)">
                                                            <i class="icon-line-awesome-qrcode"></i>
                                                        </a>

                                                        <a href="#" class="button gray ripple-effect ico" title="Editar" @click="editarItem(item)"> <!-- data-tippy-placement="top" -->
                                                            <i class="icon-feather-edit"></i>
                                                        </a>

                                                        <a href="javascript:" class="button gray ripple-effect ico" title="Eliminar" @click="removerItem(item)"> <!-- data-tippy-placement="top" -->
                                                            <i class="icon-feather-trash-2"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </template>

                                <li v-if="false">
                                    <!-- Overview -->
                                    <div class="freelancer-overview manage-candidates">
                                        <div class="freelancer-overview-inner">

                                            <!-- Avatar -->
                                            <div class="freelancer-avatar">
                                                <div class="verified-badge"></div>
                                                <a href="#"><img src="images/user-avatar-big-03.jpg" alt=""></a>
                                            </div>

                                            <!-- Name -->
                                            <div class="freelancer-name">
                                                <h4><a href="#">Sindy Forest <img class="flag" src="images/flags/au.svg" alt="" title="Australia" data-tippy-placement="top"></a></h4>

                                                <!-- Details -->
                                                <span class="freelancer-detail-item"><a href="#"><i class="icon-feather-mail"></i> sindy@example.com</a></span>
                                                <span class="freelancer-detail-item"><i class="icon-feather-phone"></i> (+61) 123-456-789</span>

                                                <!-- Rating -->
                                                <div class="freelancer-rating">
                                                    <div class="star-rating" data-rating="5.0"></div>
                                                </div>

                                                <!-- Buttons -->
                                                <div class="buttons-to-right always-visible margin-top-25 margin-bottom-5">
                                                    <a href="#" class="button ripple-effect"><i class="icon-feather-file-text"></i> Download CV</a>
                                                    <a href="#small-dialog" class="popup-with-zoom-anim button dark ripple-effect"><i class="icon-feather-mail"></i> Send Message</a>
                                                    <a href="#" class="button gray ripple-effect ico" title="Remove Candidate" data-tippy-placement="top"><i class="icon-feather-trash-2"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>

                                <li v-if="false">
                                    <!-- Overview -->
                                    <div class="freelancer-overview manage-candidates">
                                        <div class="freelancer-overview-inner">

                                            <!-- Avatar -->
                                            <div class="freelancer-avatar">
                                                <a href="#"><img src="images/user-avatar-placeholder.png" alt=""></a>
                                            </div>

                                            <!-- Name -->
                                            <div class="freelancer-name">
                                                <h4><a href="#">Sebastiano Piccio <img class="flag" src="images/flags/it.svg" alt="" title="Italy" data-tippy-placement="top"></a></h4>


                                                <!-- Details -->
                                                <span class="freelancer-detail-item"><a href="#"><i class="icon-feather-mail"></i> sebastiano@example.com</a></span>
                                                <span class="freelancer-detail-item"><i class="icon-feather-phone"></i> (+39) 123-456-789</span>

                                                <!-- Rating -->
                                                <br>
                                                <span class="company-not-rated">Minimum of 3 votes required</span>

                                                <!-- Buttons -->
                                                <div class="buttons-to-right always-visible margin-top-25 margin-bottom-5">
                                                    <a href="#" class="button ripple-effect"><i class="icon-feather-file-text"></i> Download CV</a>
                                                    <a href="#small-dialog" class="popup-with-zoom-anim button dark ripple-effect"><i class="icon-feather-mail"></i> Send Message</a>
                                                    <a href="#" class="button gray ripple-effect ico" title="Remove Candidate" data-tippy-placement="top"><i class="icon-feather-trash-2"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>

                                <li v-if="false">
                                    <!-- Overview -->
                                    <div class="freelancer-overview manage-candidates">
                                        <div class="freelancer-overview-inner">

                                            <!-- Avatar -->
                                            <div class="freelancer-avatar">
                                                <a href="#"><img src="images/user-avatar-placeholder.png" alt=""></a>
                                            </div>

                                            <!-- Name -->
                                            <div class="freelancer-name">
                                                <h4><a href="#">Nikolay Azarov <img class="flag" src="images/flags/ru.svg" alt="" title="Russia" data-tippy-placement="top"></a></h4>

                                                <!-- Details -->
                                                <span class="freelancer-detail-item"><a href="#"><i class="icon-feather-mail"></i> nikolay@example.com</a></span>
                                                <span class="freelancer-detail-item"><i class="icon-feather-phone"></i> (+7) 123-456-789</span>

                                                <!-- Rating -->
                                                <br>
                                                <span class="company-not-rated">Minimum of 3 votes required</span>

                                                <!-- Buttons -->
                                                <div class="buttons-to-right always-visible margin-top-25 margin-bottom-5">
                                                    <a href="#" class="button ripple-effect"><i class="icon-feather-file-text"></i> Download CV</a>
                                                    <a href="#small-dialog" class="popup-with-zoom-anim button dark ripple-effect"><i class="icon-feather-mail"></i> Send Message</a>
                                                    <a href="#" class="button gray ripple-effect ico" title="Remove Candidate" data-tippy-placement="top"><i class="icon-feather-trash-2"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>

                            </ul>
                        </div>
                    </div>
                </div>

            </div>

            <paginador
                    v-show="!texto_buscado.length"
                    v-model="pagina_actual"
                    @itemschanged="changeItemsPerPage"
                    :total_items="items.length"
            ></paginador>
        </section>

        <section class="form" v-if="vista_formulario">
            <!-- Dashboard Headline -->
            <div class="dashboard-headline">
                <h3>
                    <button type="button" class="button" @click="ocultarFormulario">
                        <i class="icon-material-outline-arrow-back"></i>
                    </button>
                    &nbsp;
                    {{ item.id > 0 ? 'Modificar empleado' : 'Agregar empleado' }}
                </h3>
            </div>

            <form ref="form">
                <!-- Row -->
                <div class="row">

                    <!-- Dashboard Box -->
                    <div class="col-xl-12">
                        <div class="dashboard-box margin-top-0">

                            <!-- Headline -->
                            <div class="headline" v-if="false">
                                <h3><i class="icon-feather-folder-plus"></i> Agregar empleado</h3>
                            </div>

                            <div class="content with-padding padding-bottom-10">
                                <div class="message-time-sign">
                                    <span>Datos de la persona</span>
                                </div>

                                <div class="row">
                                    <div class="col-xl-4">
                                        <input-imagen
                                                v-model="item.foto"
                                                nombre="foto"
                                                etiqueta="Foto"
                                                altura="153"
                                                ref="foto"
                                        ></input-imagen>
                                    </div>

                                    <div class="col-xl-8">
                                        <div class="row">
                                            <div class="col-xl-6">
                                                <input-texto
                                                        v-model="item.primer_nombre"
                                                        nombre="primer_nombre"
                                                        etiqueta="Primer nombre"
                                                ></input-texto>
                                            </div>
                                            <div class="col-xl-6">
                                                <input-texto
                                                        v-model="item.segundo_nombre"
                                                        nombre="segundo_nombre"
                                                        etiqueta="Segundo nombre"
                                                ></input-texto>
                                            </div>
                                            <div class="col-xl-6">
                                                <input-texto
                                                        v-model="item.primer_apellido"
                                                        nombre="primer_apellido"
                                                        etiqueta="Primer apellido"
                                                ></input-texto>
                                            </div>
                                            <div class="col-xl-6">
                                                <input-texto
                                                        v-model="item.segundo_apellido"
                                                        nombre="segundo_apellido"
                                                        etiqueta="Segundo apellido"
                                                ></input-texto>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-xl-3">
                                        <input-texto
                                                v-model="item.dni"
                                                nombre="dni"
                                                etiqueta="DNI"
                                        ></input-texto>
                                    </div>

                                    <div class="col-xl-9">
                                        <div class="row">
                                            <div class="col-xl-6">
                                                <input-seleccion
                                                        v-model="item.nacionalidad"
                                                        nombre="nacionalidad"
                                                        etiqueta="Nacionalidad"
                                                        :items="nacionalidades"
                                                        :items_seleccionados="[item.nacionalidad]"
                                                        :buscador="true"
                                                >
                                                </input-seleccion>
                                            </div>

                                            <div class="col-xl-6">
                                                <input-fecha
                                                        v-model="item.fecha_nacimiento"
                                                        nombre="fecha_nacimiento"
                                                        etiqueta="Fecha de nacimineto"
                                                ></input-fecha>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-xl-3">
                                        <input-opcion
                                                v-model="item.sexo"
                                                nombre="sexo"
                                                etiqueta="Sexo"
                                                :items="[
                                                    {
                                                        id: '0',
                                                        nombre: 'Femenino'
                                                    },
                                                    {
                                                        id: '1',
                                                        nombre: 'Masculino'
                                                    }
                                                ]"
                                        ></input-opcion>
                                    </div>

                                    <div class="col-xl-9">
                                        <input-texto
                                                v-model="item.direccion_domicilio"
                                                nombre="direccion_domicilio"
                                                etiqueta="Dirección de domicilio"
                                        ></input-texto>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-xl-6">
                                        <input-textos
                                                v-model="item.telefono"
                                                nombre="telefonos"
                                                etiqueta="Teléfonos"
                                        ></input-textos>
                                    </div>

                                    <div class="col-xl-6">
                                        <input-textos
                                                v-model="item.correo"
                                                nombre="correos"
                                                etiqueta="Correos"
                                        ></input-textos>
                                    </div>
                                </div>

                                <div class="message-time-sign">
                                    <span>Datos del empleado</span>
                                </div>

                                <div class="row">
                                    <div class="col-xl-3">
                                        <input-seleccion
                                                v-model="item.empresa"
                                                nombre="empresa"
                                                etiqueta="Empresa"
                                                :items="empresas"
                                                :multiple="true"
                                        ></input-seleccion>
                                    </div>

                                    <div class="col-xl-3">
                                        <input-seleccion
                                                v-model="item.id_sucursal"
                                                nombre="id_sucursal"
                                                etiqueta="Sucursal"
                                                :items="sucursalesFiltradas"
                                        ></input-seleccion>
                                    </div>

                                    <div class="col-xl-3">
                                        <input-seleccion
                                                v-model="item.id_departamento"
                                                nombre="id_departamento"
                                                etiqueta="Departamento"
                                                :items="departamentosFiltrados"
                                        ></input-seleccion>
                                    </div>

                                    <div class="col-xl-3">
                                        <input-seleccion
                                                v-model="item.id_sub_departamento"
                                                nombre="id_sub_departamento"
                                                etiqueta="Sub-departamento"
                                                :items="subDepartamentosFiltrados"
                                        ></input-seleccion>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-xl-4">
                                        <input-texto
                                                v-model="item.num_control"
                                                nombre="num_control"
                                                etiqueta="Número de control"
                                        ></input-texto>
                                    </div>

                                    <div class="col-xl-4">
                                        <input-fecha
                                                v-model="item.fecha_ingreso"
                                                nombre="fecha_ingreso"
                                                etiqueta="Fecha de ingreso"
                                        ></input-fecha>
                                    </div>

                                    <div class="col-xl-4">
                                        <input-fecha
                                                v-model="item.fecha_ingreso_inss"
                                                nombre="fecha_ingreso_inss"
                                                etiqueta="Fecha de ingreso INSS"
                                        ></input-fecha>
                                    </div>

                                    <div class="col-xl-12">
                                        <input-texto
                                                v-model="item.descripcion"
                                                nombre="descripcion"
                                                etiqueta="Descripción"
                                        ></input-texto>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-xl-4">
                                        <input-texto
                                                v-model="item.grado"
                                                nombre="grado"
                                                etiqueta="Grado"
                                        ></input-texto>
                                    </div>

                                    <div class="col-xl-4">
                                        <input-texto
                                                v-model="item.tipo_cargo"
                                                nombre="tipo_cargo"
                                                etiqueta="Tipo de cargo"
                                        ></input-texto>
                                    </div>

                                    <div class="col-xl-4">
                                        <input-texto
                                                v-model="item.salario_actual"
                                                nombre="salario_actual"
                                                etiqueta="Salario actual"
                                        ></input-texto>
                                    </div>
                                </div>

                                <div class="message-time-sign">
                                    <span>Datos del usuario</span>
                                </div>

                                <div class="row">
                                    <div class="col-xl-3">
                                        <div class="submit-field">
                                            <h5>Nombre de usuario</h5>
                                            <input type="text" name="nombre" class="with-border" placeholder="Nombre" v-model="item.nombre">
                                        </div>
                                    </div>

                                    <div class="col-xl-3">
                                        <div class="submit-field">
                                            <h5>Contraseña</h5>
                                            <input type="password" name="contrasena" class="with-border" placeholder="Contraseña" v-model="item.contrasena">
                                        </div>
                                    </div>

                                    <div class="col-xl-3">
                                        <div class="submit-field">
                                            <h5>Confirmar contraseña</h5>
                                            <input type="password" name="contrasena_confirmar" class="with-border" placeholder="Confirmar contraseña" v-model="item.contrasena_confirmar">
                                        </div>
                                    </div>

                                    <div class="col-xl-3">
                                        <!--<div class="submit-field">
                                            <h5>Rol</h5>
                                            <select class="selectpicker with-border" data-size="7" title="Seleccione el rol">
                                                <option>Administrador</option>
                                                <option>Otro</option>
                                            </select>
                                        </div>-->
                                        <!--<input-seleccion
                                                v-model="item.rol"
                                                nombre="rol"
                                                etiqueta="Rol"
                                                :items="[
                                                    {
                                                        id: 'admin',
                                                        nombre: 'Administrador'
                                                    },
                                                    {
                                                        id: 2,
                                                        nombre: 'Otro'
                                                    }
                                                ]"
                                                :items_seleccionados="[item.rol]"
                                        >
                                        </input-seleccion>-->
                                        <input-check
                                                v-model="item.admin"
                                                nombre="admin"
                                                etiqueta="Administrador"
                                        ></input-check>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="_fuente" :value="fuente">
                    <input type="hidden" name="id" v-model="item.id">
                    <input type="hidden" name="id_persona" v-model="item.id_persona">

                    <div class="col-xl-12">
                        <a href="#" class="button ripple-effect button-sliding-icon big margin-top-30" @click="guardar">
                            <span class="item-guardar">Guardar <i class="icon-feather-save"></i></span>
                            <span class="item-guardando">Guardando... <i class="icon-line-awesome-hourglass-2 fa-spin"></i></span>
                        </a>
                    </div>

                </div>
                <!-- Row / End -->
            </form>
        </section>

        <section class="form" v-if="vista_impresion_carnet">
            <!-- Dashboard Headline -->
            <div class="dashboard-headline">
                <h3>
                    <button type="button" class="button" @click="ocultarFormulario">
                        <i class="icon-material-outline-arrow-back"></i>
                    </button>
                    &nbsp;
                    Imprimir carnet
                </h3>
            </div>

            <div class="row">
                <!-- Dashboard Box -->
                <div class="col-xl-12">
                    <div class="dashboard-box margin-top-0">
                        <div class="content with-padding">
                            <div class="row">

                                <div class="col-xl-4">
                                    <div ref="contenedor_carnet" v-show="!cargando_carnet">
                                        <div class="carnet-previo" v-html="carnet_previo"></div>
                                    </div>
                                </div>

                                <div class="col-xl-8">
                                    <!--<input-texto
                                            v-model="carnet.nombre"
                                            nombre="carnet_nombre"
                                            etiqueta="Nombre"
                                    ></input-texto>-->

                                    <input-seleccion
                                            v-model="carnet.tipo"
                                            nombre="tipo"
                                            etiqueta="Tipo"
                                            :items="[
                                                {
                                                    id: 1,
                                                    nombre: 'Tipo 1',
                                                },
                                                {
                                                    id: 2,
                                                    nombre: 'Tipo 2',
                                                }
                                            ]"
                                            @cambiado="tipoCarnetCambiado"
                                    ></input-seleccion>

                                    <input-texto
                                            v-model="carnet.fecha_vencimiento"
                                            nombre="fecha_vencimiento"
                                            etiqueta="Fecha de vencimiento"
                                    ></input-texto>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <form :action="urlSubmitImpresion()" target="_blank" ref="form_generar_pdf">
                <div class="col-xl-12">
                    <input type="hidden" name="_fuente" value="Empleado">
                    <input type="hidden" name="_accion" value="cargarCarnet">
                    <input type="hidden" name="id" :value="id_item">
                    <input type="hidden" name="tipo_carnet" :value="carnet.tipo">
                    <input type="hidden" name="direccion" :value="carnet.direccion">
                    <input type="hidden" name="telefono" :value="carnet.telefono">
                    <input type="hidden" name="website" :value="carnet.website">
                    <input type="hidden" name="fecha_vencimiento" :value="carnet.fecha_vencimiento">

                    <button type="submit" class="button ripple-effect button-sliding-icon big margin-top-30">
                        Generar PDF
                        <i class="icon-line-awesome-file-pdf-o"></i>
                    </button>
                </div>
            </form>
        </section>

        <section class="form" v-if="vista_permisos">
            <!-- Dashboard Headline -->
            <div class="dashboard-headline">
                <h3>
                    <button type="button" class="button" @click="ocultarFormulario">
                        <i class="icon-material-outline-arrow-back"></i>
                    </button>
                    &nbsp;
                    Permisos
                </h3>
            </div>

            <div class="row">
                <!-- Dashboard Box -->
                <div class="col-xl-12">
                    <div class="dashboard-box margin-top-0">
                        <div class="content with-padding margin-left-20">

                            <div class="notify-box margin-top-15 margin-bottom-30">
                                <div class="sort-by">
                                    <div class="btn-group bootstrap-select hide-tick">
                                        <button type="button" class="btn dropdown-toggle btn-default" data-toggle="dropdown" role="button" title="Relevance">
                                            <span class="filter-option pull-left">Acciones</span>&nbsp;<span class="bs-caret"><span class="caret"></span></span>
                                        </button>
                                        <div class="dropdown-menu open" role="combobox">
                                            <ul class="dropdown-menu inner" role="listbox" aria-expanded="false">
                                                <li>
                                                    <a tabindex="0" role="option" @click="seleccionarTodosPermisos(true)">
                                                        <span class="text">Seleccionar todos</span>
                                                        <span class="glyphicon glyphicon-ok check-mark"></span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a tabindex="0" role="option" @click="seleccionarTodosPermisos(false)">
                                                        <span class="text">Deseleccionar todos</span>
                                                        <span class="glyphicon glyphicon-ok check-mark"></span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    <button type="button" class="button ripple-effect margin-left-30" @click="guardarPermisos">Guardar</button>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xl-12">

                                    <div v-if="usuario_es_admin">
                                        <div class="notification warning">
                                            <p>Los permisos no aplican a los usuarios administradores.</p>
                                        </div>
                                    </div>

                                    <div class="accordion js-accordion" ref="contenedor_permisos">

                                        <template v-for="(menu_item,index) in menu_items">
                                            <template v-if="(typeof menu_item.ruta === 'string' && menu_item.ruta.length) && (typeof menu_item.admin === 'undefined' || !menu_item.admin)">

                                                <div class="accordion__item js-accordion-item" :key="index">
                                                    <div class="accordion-header js-accordion-header alt">

                                                        <div class="switch-container">
                                                            <label class="switch">
                                                                <!--<input type="checkbox" name="permisos[]" :checked="!!permisos_seleccionados[menu_item.ruta]" @change="actualizarPermiso(menu_item.ruta, $event)" :value="menu_item.ruta">-->
                                                                <input type="checkbox" name="permisos[]" :checked="!!permisos_seleccionados[menu_item.ruta]" @change="actualizarPermiso(menu_item.ruta, $event)" :value="menu_item.ruta">
                                                                <span class="switch-button"></span> {{ typeof menu_item.texto_alt === 'string' ? menu_item.texto_alt : menu_item.texto }}
                                                            </label>
                                                        </div>

                                                    </div>

                                                    <!-- Accordtion Body -->
                                                    <div class="accordion-body js-accordion-body">
                                                        <div class="accordion-body__contents">

                                                            <tabla-permisos
                                                                    :nombre="menu_item.ruta"
                                                                    :padre="permisos_seleccionados[menu_item.ruta]"
                                                                    :inicializacion="cargando_permisos"
                                                                    @cambiado="actualizarPermisoDesdeHijos"
                                                            ></tabla-permisos>

                                                        </div>
                                                    </div>

                                                </div>
                                            </template>

                                            <template v-if="typeof menu_item.items === 'object' && menu_item.items.length">

                                                <template v-for="(sub_menu_item,index2) in menu_item.items">
                                                    <template v-if="(typeof sub_menu_item.ruta === 'string' && sub_menu_item.ruta.length) && (typeof sub_menu_item.admin === 'undefined' || !sub_menu_item.admin)">

                                                        <div class="accordion__item js-accordion-item" :key="'s_' + index + '_' + index2">
                                                            <div class="accordion-header js-accordion-header alt">

                                                                <div class="switch-container">
                                                                    <label class="switch">
                                                                        <!--<input type="checkbox" name="permisos[]" :value="sub_menu_item.ruta" :checked="!!permisos_seleccionados[sub_menu_item.ruta]" @change="actualizarPermiso(sub_menu_item.ruta, $event)">-->
                                                                        <input type="checkbox" name="permisos[]" :checked="!!permisos_seleccionados[sub_menu_item.ruta]" @change="actualizarPermiso(sub_menu_item.ruta, $event)" :value="sub_menu_item.ruta">
                                                                        <span class="switch-button"></span> {{ typeof sub_menu_item.texto_alt === 'string' ? sub_menu_item.texto_alt : sub_menu_item.texto }}
                                                                    </label>
                                                                </div>

                                                            </div>
                                                            <div class="accordion-body js-accordion-body">
                                                                <div class="accordion-body__contents">

                                                                    <tabla-permisos
                                                                            :nombre="sub_menu_item.ruta"
                                                                            :padre="permisos_seleccionados[sub_menu_item.ruta]"
                                                                            :inicializacion="cargando_permisos"
                                                                            @cambiado="actualizarPermisoDesdeHijos"
                                                                    ></tabla-permisos>

                                                                </div>
                                                            </div>
                                                        </div>

                                                    </template>

                                                    <template v-if="typeof sub_menu_item.items === 'object' && sub_menu_item.items.length">
                                                        <template v-for="(sub_sub_menu_item,index3) in sub_menu_item.items">
                                                            <template v-if="(typeof sub_sub_menu_item.ruta === 'string' && sub_sub_menu_item.ruta.length) && (typeof sub_sub_menu_item.admin === 'undefined' || !sub_sub_menu_item.admin)">

                                                                <div class="accordion__item js-accordion-item" :key="'ss' + index + '_' + index2 + '_' + index3">
                                                                    <div class="accordion-header js-accordion-header alt">
    
                                                                        <div class="switch-container">
                                                                            <label class="switch">
                                                                                <!--<input type="checkbox" name="permisos[]" :value="sub_sub_menu_item.ruta" :checked="!!permisos_seleccionados[sub_sub_menu_item.ruta]" @change="actualizarPermiso(sub_sub_menu_item.ruta, $event)">-->
                                                                                <input type="checkbox" name="permisos[]" :checked="!!permisos_seleccionados[sub_sub_menu_item.ruta]" @change="actualizarPermiso(sub_sub_menu_item.ruta, $event)" :value="sub_sub_menu_item.ruta">
                                                                                <span class="switch-button"></span> {{ typeof sub_sub_menu_item.texto_alt === 'string' ? sub_sub_menu_item.texto_alt : sub_sub_menu_item.texto }}
                                                                            </label>
                                                                        </div>
    
                                                                    </div>
                                                                    <div class="accordion-body js-accordion-body">
                                                                        <div class="accordion-body__contents">

                                                                            <tabla-permisos
                                                                                    :nombre="sub_sub_menu_item.ruta"
                                                                                    :padre="permisos_seleccionados[sub_sub_menu_item.ruta]"
                                                                                    :inicializacion="cargando_permisos"
                                                                                    @cambiado="actualizarPermisoDesdeHijos"
                                                                            ></tabla-permisos>

                                                                        </div>
                                                                    </div>
                                                                </div>

                                                            </template>
                                                        </template>
                                                    </template>
                                                </template>

                                            </template>
                                        </template>

                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <a href="#" class="button ripple-effect button-sliding-icon big margin-top-30" @click.prevent="guardarPermisos">
                Guardar
                <i class="icon-feather-save"></i>
            </a>
        </section>

    </main>
</template>

<script>
    window.Vue = require('vue');
    Vue.component('tabla-permisos', require('../TablaPermisos.vue').default);

    import menu from '../../menu.json'
    import nacionalidades from '../../nacionalidades.json'

    export default {
        name: "Usuarios.vue",
        components: {},

        props: {
            urls: Object,
            avatar_defecto: String,
        },

        data: () => ({
            fuente: 'User',
            empresas: [],
            sucursales: [],
            departamentos: [],
            sub_departamentos: [],
            items: [],
            item: {
                id: 0,
                id_persona: 0,
                nombre: '',
                contrasena: '',
                contrasena_confirmar: '',
                rol: null,
                admin: false,
                //persona
                primer_nombre: '',
                segund_nombre: '',
                primer_apellido: '',
                segundo_apellido: '',
                dni: '',
                nacionalidad: 'nicaraguense',
                sexo: null,
                direccion_domicilio: '',
                fecha_nacimiento: '',
                foto: '',
                telefono: '',
                correo: '',
                //empleado
                num_control: '',
                fecha_ingreso: '',
                fecha_ingreso_inss: '',
                descripcion: '',
                grado: '',
                tipo_cargo: '',
                salario_actual: '',
                empresa: '',
                id_sucursal: '',
                id_departamento: '',
                id_sub_departamento: '',
            },
            vista_items: true,
            vista_formulario: false,
            vista_impresion_carnet: false,
            vista_permisos: false,
            nacionalidades: nacionalidades,
            texto_buscado: '',
            propiedades_buscadas: [
                'nombre',
                'nombre_persona',
                'correo',
                'empresa',
            ],
            tipo_vista: 'compacto',
            carnet_previo: '',
            carnet: {
                tipo: 1,
                nombre: '',
                num_carnet: '',
                dni: '',
                direccion: '',
                telefono: '',
                website: '',
                fecha_vencimiento: '',
            },
            cargando_carnet: false,
            menu_items: menu,
            permisos_seleccionados: {},
            cargando_permisos: true,
            usuario_es_admin: false,
            id_item: 0,
            indice: 0,
        }),

        computed: {
            clasesTipoVista() {
                if (typeof this.tipo_vista !== 'string' || !this.tipo_vista.length) return '';

                switch (this.tipo_vista) {
                    case 'compacto':
                        return 'modo-compacto';
                }

                return '';
            },

            permisosSeleccionados() {
                for (const prop in this.permisos_seleccionados) {
                    if (this.permisos_seleccionados.hasOwnProperty(prop)) {
                        if (!this.permisos_seleccionados[prop]) {
                            return false;
                        }
                    }
                }

                return true;
            },

            sucursalesFiltradas() {
                const empresas = this.item.empresa;

                if (typeof empresas !== 'object' || empresas === null || !empresas.length) {
                    return [];
                }

                let lista = [];

                for (const sucursal of this.sucursales) {
                    if (empresas.map(Number).includes(parseInt(sucursal.id_empresa))) {
                        lista.push({
                            id: sucursal.id,
                            nombre: sucursal.nombre,
                        });
                    }
                }
                
                return lista;
            },

            departamentosFiltrados() {
                const sucursal = this.item.id_sucursal;
                let lista = [];
                
                for (const departamento of this.departamentos) {
                    if (sucursal == departamento.id_sucursal) {
                        lista.push({
                            id: departamento.id,
                            nombre: departamento.nombre,
                        });
                    }
                }
                
                return lista;
            },

            subDepartamentosFiltrados() {
                const departamento = this.item.id_departamento;
                let lista = [];
                
                for (const sub_departamento of this.sub_departamentos) {
                    if (departamento == sub_departamento.id_departamento) {
                        lista.push({
                            id: sub_departamento.id,
                            nombre: sub_departamento.nombre,
                        });
                    }
                }
                
                return lista;
            },
        },

        methods: {

            changeItemsPerPage(value){
                this.items_por_pagina = value;
                this.$forceUpdate();
            },

            cargarData() {
                this.statusCargando(true);

                const self = this;

                this.$http.get(this.urls.get, {
                    params: {
                        _fuente: this.fuente,
                        _accion: 'index',
                    }
                })
                .then(response => {
                    if (response.status === 200) {
                        let data = response.data;
                        let n = 1;

                        for (const prop in data) {
                            if (data.hasOwnProperty(prop)) {
                                data[prop]['_indice'] = n;
                                n++;
                            }
                        }

                        this.items = response.data;

                        self.cargarEstructura();
                    }

                    this.statusCargando(false);
                });
            },

            mostrarFormulario() {
                this.vista_items = false;
                this.vista_impresion_carnet = false;
                this.vista_permisos = false;
                this.vista_formulario = true;
                //this.$refs.campo_nombre.$el.focus();
            },

            ocultarFormulario() {
                this.vista_formulario = false;
                this.vista_impresion_carnet = false;
                this.vista_permisos = false;
                this.vista_items = true;
            },

            mostrarImpresionCarnet() {
                this.vista_formulario = false;
                this.vista_items = false;
                this.vista_permisos = false;
                this.vista_impresion_carnet = true;
            },

            mostrarPermisos() {
                this.vista_formulario = false;
                this.vista_items = false;
                this.vista_impresion_carnet = false;
                this.vista_permisos = true;

                setTimeout(function() {
                    initAccordion();
                }, 600);
            },

            mostrarFormularioNuevo() {
                this.limpiarItem();
                this.mostrarFormulario();
            },

            limpiarItem() {
                this.item.id = 0;
                this.item.id_persona = 0;
                this.item.nombre = '';
                this.item.contrasena = '';
                this.item.contrasena_confirmar = '';
                this.item.rol = null;
                this.item.admin = false;
                //persona
                this.item.primer_nombre = '';
                this.item.segundo_nombre = '';
                this.item.primer_apellido = '';
                this.item.segundo_apellido = '';
                this.item.dni = '';
                this.item.nacionalidad = 'nicaraguense';
                this.item.sexo = null;
                this.item.direccion_domicilio = '';
                this.item.fecha_nacimiento = '';
                this.item.foto = '';
                this.item.telefono = '';
                this.item.correo = '';
                //empleado
                this.item.num_control = '';
                this.item.fecha_ingreso = '';
                this.item.fecha_ingreso_inss = '';
                this.item.descripcion = '';
                this.item.grado = '';
                this.item.tipo_cargo = '';
                this.item.salario_actual = '';
                this.item.empresa = '';
                this.item.id_sucursal = '';
                this.item.id_departamento = '';
                this.item.id_sub_departamento = '';
            },

            guardar() {
                this.statusGuardando(true);

                const params = {
                    _fuente: this.fuente,
                };
                //const frm = this.$refs.form;
                //const inputs = new FormData($(frm)[0]);
                //const inputs = $(frm).serialize();

                const form_data = new FormData(this.$refs['form']);

                /*form_data.append('_fuente', this.fuente);
                for (const prop in this.item) {
                    if (this.item.hasOwnProperty(prop)) {
                        if (prop === 'foto') continue;
                        form_data.append(prop, this.item[prop] || '');
                    }
                }
                form_data.append('foto', $(this.$refs['foto'].$el).find('input')[0].files[0]);*/

                /*this.$http.post(this.urls.post, {
                    ...params,
                    ...this.item
                })*/
                const config = { headers: { 'Content-Type': 'multipart/form-data' } };
                this.$http.post(this.urls.post, form_data, config)
                .then(response => {
                    this.debugStuff(response)
                    this.debugForm(form_data);
                    if (response.status === 200) {
                        resultadoSolicitudDefecto(response.data);

                        if (response.data.ok) {
                            this.ocultarFormulario();
                            this.cargarData();
                            this.limpiarItem();
                        }
                    }
                    else if (response.status === 500) {
                        mensajeError('Error de servidor.');
                    }

                    this.statusGuardando(false);
                });
            },

            editarItem(item) {
                for (const indice in this.items) {
                    if (this.items.hasOwnProperty(indice)) {
                        if (this.items[indice].id === item.id) {
                            this.$http.get(this.urls.get, {
                                params: {
                                    _fuente: this.fuente,
                                    id: item.id
                                }
                            })
                            .then(response => {
                                if (response.status === 200) {
                                    const data = response.data;

                                    this.item.id = data.id;
                                    this.item.id_persona = data.id_persona;
                                    this.item.nombre = data.nombre;
                                    this.item.contrasena = data.contrasena;
                                    this.item.contrasena_confirmar = data.contrasena_confirmar;
                                    this.item.rol = data.rol;
                                    this.item.admin = !!data.admin;

                                    //persona
                                    this.item.primer_nombre = data.primer_nombre;
                                    this.item.segundo_nombre = data.segundo_nombre;
                                    this.item.primer_apellido = data.primer_apellido;
                                    this.item.segundo_apellido = data.segundo_apellido;
                                    this.item.dni = data.dni;
                                    this.item.nacionalidad = data.nacionalidad;
                                    this.item.sexo = data.sexo;
                                    this.item.direccion_domicilio = data.direccion_domicilio;
                                    this.item.fecha_nacimiento = formatoFechaApp(data.fecha_nacimiento, 'fecha');
                                    this.item.foto = data.foto || '';
                                    this.item.telefono = typeof data.telefonos === 'object' && data.telefonos !== null ? data.telefonos.join(',') : '';
                                    this.item.correo = typeof data.correos === 'object' && data.correos !== null ? data.correos.join(',') : '';

                                    //empleado
                                    if (typeof  data.empleado === 'object' &&  data.empleado !== null) {
                                        const empleado = data.empleado;
                                        this.item.num_control = empleado.num_control;
                                        this.item.fecha_ingreso = formatoFechaApp(empleado.fecha_ingreso, 'fecha');
                                        this.item.fecha_ingreso_inss = formatoFechaApp(empleado.fecha_ingreso_inss, 'fecha');
                                        this.item.descripcion = empleado.descripcion;
                                        this.item.grado = empleado.grado;
                                        this.item.tipo_cargo = empleado.tipo_cargo;
                                        this.item.salario_actual = empleado.salario_actual;
                                        this.item.empresa = data.empresas; //typeof data.empresas === 'object' && data.empresas !== null ? data.empresas.join(',') : '';
                                        this.item.id_sucursal = empleado.id_sucursal;
                                        this.item.id_departamento = empleado.id_departamento;
                                        this.item.id_sub_departamento = empleado.id_sub_departamento;
                                    }

                                    this.mostrarFormulario();
                                }
                            });
                            break;
                        }
                    }
                }
            },

            removerItem(item) {
                for (const indice in this.items) {
                    if (this.items.hasOwnProperty(indice)) {
                        if (this.items[indice].id === item.id) {
                            const self = this;
                            const c_item = this.items[indice];
                            const nombre_empleado = typeof c_item.nombre_persona === 'string' && c_item.nombre_persona.length ? c_item.nombre_persona : c_item.nombre;
                            confirmar('¿Está seguro que quiere eliminar el empleado ' + nombre_empleado + '?', function() {
                                const params = {
                                    _fuente: self.fuente,
                                    _accion: 'eliminar',
                                    id: item.id,
                                };
                                self.$http.post(self.urls.post, params)
                                    .then(response => {
                                        if (response.status === 200) {
                                            resultadoSolicitudDefecto(response.data);

                                            if (response.data.ok) {
                                                self.items.splice(indice, 1);
                                                self.$forceUpdate();
                                            }
                                        }
                                        else if (response.status === 500) {
                                            mensajeError('Error de servidor.');
                                        }
                                    });
                            });
                            break;
                        }
                    }
                }
            },

            mostrarMetas(item) {
                //this.$router.push('importar');
                this.$router.push({
                    name: 'ezadigital_metas_usuario',
                    //path: '/ezadigital-metas-usuario',
                    params: {
                        id_usuario: item.id,
                        nombre_usuario: this.nombrePersona(item),
                    }
                });
            },

            mostrarUbicaciones(item) {
                this.$router.push({
                    name: 'ezadigital_ubicaciones_usuario',
                    //path: '/ezadigital-ubicaciones-usuario',
                    params: {
                        id_usuario: item.id,
                        nombre_usuario: this.nombrePersona(item),
                    }
                });
            },

            mostrarCodigoQrItem(item) {
                item.codigo_qr = `
                    <i class="icon-line-awesome-hourglass-2 fa-spin inline-block"></i>
                `;
                this.$forceUpdate();

                this.$http.get(this.urls.get, {
                    params: {
                        _fuente: this.fuente,
                        _accion: 'codigoQr',
                        id: item.id,
                    }
                })
                    .then(response => {
                        if (response.status === 200) {
                            const data = response.data;

                            if (data.ok) {
                                item.codigo_qr = data.codigo_qr;
                            }
                            else {
                                item.codigo_qr = '';

                                if (typeof data.err === 'string' && data.err.length) {
                                    mensajeError(data.err);
                                }
                            }
                        }
                        else {
                            item.codigo_qr = '';
                            mensajeError('Error de conexión.');
                        }

                        this.$forceUpdate(); //^^ no se actualiza automáticamente, por eso se llama el forceUpdate
                    });
            },

            ocultarCodigoQr(item) {
                item.codigo_qr = '';
                this.$forceUpdate();
            },

            vistaImprimirCarnet(item) {
                //this.cargarCarnet(item);
                this.id_item = item.id;
                this.mostrarImpresionCarnet();
            },

            vistaPermisos(item) {
                this.statusCargando(true);

                this.id_item = item.id;
                this.cargando_permisos = true;

                //se apagan todos los permisos
                /*for (const prop in this.permisos_seleccionados) {
                    if (this.permisos_seleccionados.hasOwnProperty(prop)) {
                        this.permisos_seleccionados[prop] = false;
                    }
                }*/

                this.$http.get(this.urls.get, {
                    params: {
                        _fuente: this.fuente,
                        _accion: 'cargarPermisos',
                        id: item.id,
                    }
                })
                    .then(response => {
                        if (response.status === 200) {
                            const data = response.data;

                            const $contenedor_permisos = $(this.$refs['contenedor_permisos']);
                            $contenedor_permisos.find('.intermediate').removeClass('intermediate');

                            if (data.ok) {
                                const permisos = data['permisos'];

                                for (const permiso of permisos) {
                                    const $input = $contenedor_permisos.find('input[name="permisos[]"][value="' + permiso.categoria + '"]');
                                    const $item = $input.closest('.switch-container');
                                    const $sub_checks = $item.closest('.accordion__item').find('.accordion-body');

                                    //this.permisos_seleccionados[permiso.categoria] = true; //

                                    let all_checked = true;
                                    let some_checked = false;
                                    let acciones = [];
                                    const $acciones_items = $sub_checks.find('input[type="checkbox"][name="permisos[]"]');

                                    $acciones_items.each(function() {
                                        const cat_accion = $(this).attr('data-accion') || '';

                                        if (cat_accion.length) {
                                            acciones.push(cat_accion);
                                        }
                                    });

                                    /*if (['todos', 'solo_propios'].includes(permiso.nombre)) {
                                        $item.find('select[name="permisos[]"]').val(permiso.nombre).trigger('change.select2');
                                    }
                                    else {*/
                                        for (const accion of acciones) {
                                            if (permiso.acciones.includes(accion)) {
                                                $sub_checks.find('input[name="permisos[]"][data-accion="' + accion + '"]').prop('checked', true);
                                                some_checked = true;
                                            }
                                            else {
                                                all_checked = false;
                                            }
                                        }

                                        if (some_checked && !all_checked) {
                                            $item.addClass('intermediate');
                                        }

                                        if (some_checked) {
                                            $input.prop('checked', true);
                                        }
                                    /*}*/

                                    for (const accion of permiso.acciones) {
                                        if (['todos', 'solo_propios'].includes(accion)) {
                                            $sub_checks.find('select[name="permisos[]"]').val(accion).trigger('change.select2');
                                            break;
                                        }
                                    }
                                }

                                this.usuario_es_admin = !!data['admin'];

                                setTimeout(() => {
                                    this.$forceUpdate();
                                    this.cargando_permisos = false;
                                }, 700);
                            }
                        }

                        this.statusCargando(false);
                    });

                this.mostrarPermisos();
            },

            cargarCarnet(item, tipo_carnet) {
                this.cargando_carnet = true;

                if (typeof item === 'undefined' || item === null) {
                    item = {
                        id: this.id_item
                    };
                }

                if (typeof tipo_carnet === 'undefined' || tipo_carnet === null) {
                    tipo_carnet = self.carnet.tipo;
                }

                for (const indice in this.items) {
                    if (this.items.hasOwnProperty(indice)) {
                        if (this.items[indice].id === item.id) {
                            const self = this;

                            const params = {
                                _fuente: 'Empleado',
                                _accion: 'cargarCarnet',
                                id: item.id,
                                tipo_carnet: tipo_carnet,
                            };

                            self.$http.get(self.urls.get, {
                                params: params
                            })
                                .then(response => {
                                    if (response.status === 200) {
                                        const data = response.data;

                                        resultadoSolicitudDefecto(data);

                                        if (response.data.ok) {
                                            self.carnet_previo = data.html;

                                            self.mostrarImpresionCarnet();

                                            self.id_item = data.id;
                                        }
                                    }
                                    else if (response.status === 500) {
                                        mensajeError('Error de servidor.');
                                    }

                                    $(this.$refs['contenedor_carnet']).on('input', '*[contenteditable]', function() {
                                        const $this = $(this);
                                        const prop = $this.attr('data-prop');

                                        if (typeof prop === 'string' && prop.length && self.carnet.hasOwnProperty(prop)) {
                                            self.carnet[prop] = $this.text();
                                        }
                                    });

                                    self.cargando_carnet = false;
                                });

                            break;
                        }
                    }
                }
            },

            tipoCarnetCambiado(tipo_carnet) {
                this.cargarCarnet(null, tipo_carnet);
            },

            inhabilitarItem(item) {
                this.$http.post(this.$url_post, {
                    _fuente: 'Empleado',
                    _accion: 'alternarHabilitar',
                    id_usuario: item.id,
                })
                    .then(response => {
                        if (response.status === 200) {
                            resultadoSolicitudDefecto(response.data);
                            item.status = response.data.status;
                        }
                        else if (response.status === 500) {
                            mensajeError('Error de servidor.');
                        }
                    });
            },

            expandir(e) {
                let $item = $(e.target);

                switch ($item.prop('tagName')) {
                    case 'BUTTON':
                    case 'A':
                        return;

                    case 'LI':
                        break;

                    default:
                        if (['BUTTON','A'].includes($item.parent().prop('tagName'))) {
                            return;
                        }
                        $item = $item.closest('li');
                }

                $item.toggleClass('abierto');
            },

            avatarUrl(item) {
                if (typeof item.foto === 'string' && item.foto.length) {
                    return this.$uploads_img_dir + 'm/' + item.foto; //Vue.prototype.$uploads_img_dir
                }
                return this.avatar_defecto;
            },

            nombrePersona(item) {
                if (typeof item.nombre_persona === 'string' && item.nombre_persona.length) {
                    return item.nombre_persona.replace('()', '');
                }
                return item.nombre;
            },

            iconoStatus(item) {
                if (item.status == 2) {
                    return `<i class="icon-feather-slash"></i> &nbsp;&nbsp;`;
                }
                return '';
            },

            nombreEmpresa(id_empresa) {
                if (typeof id_empresa !== 'undefined') {
                    for (const empresa of this.empresas) {
                        if (empresa.id == id_empresa) {
                            return empresa.nombre;
                        }
                    }
                }
                return '';
            },

            nombreSucursal(id_sucursal) {
                if (typeof id_sucursal !== 'undefined') {
                    for (const sucursal of this.sucursales) {
                        if (sucursal.id == id_sucursal) {
                            return sucursal.nombre;
                        }
                    }
                }
                return '';
            },

            nombreDepartamento(id_departamento) {
                if (typeof id_departamento !== 'undefined') {
                    for (const departamento of this.departamentos) {
                        if (departamento.id == id_departamento) {
                            return departamento.nombre;
                        }
                    }
                }
                return '';
            },

            /*esVisible(item) {
                if (!this.texto_buscado.length) return true;

                const texto = sinAcentos(this.texto_buscado);

                for (const prop of this.propiedades_buscadas) {
                    if (typeof item[prop] !== 'string') continue;
                    if (sinAcentos(item[prop]).indexOf(texto) !== -1) {
                        return true;
                    }
                }

                return false;
            },*/

            cargarEstructura() {
                this.$http.get(this.urls.get, {
                    params: {
                        _fuente: 'Empresa',
                        _accion: 'cargarEstructura'
                    }
                })
                .then(response => {
                    if (response.status === 200) {
                        const data = response.data;
                        /*let empresas = [];

                        for (const item of data['items']) {
                            empresas.push({
                                id: item.id,
                                nombre: item.nombre,
                            });
                        }

                        this.empresas = empresas;*/
                        this.empresas = data.empresas;
                        this.sucursales = data.sucursales;
                        this.departamentos = data.departamentos;
                        this.sub_departamentos = data.sub_departamentos;
                    }
                });
            },

            inicializarPermisosSeleccionados() {
                for (const menu_item of this.menu_items) {
                    if (typeof menu_item.ruta === 'string' && menu_item.ruta.length) {
                        this.permisos_seleccionados[menu_item.ruta] = false;
                    }

                    if (typeof menu_item.items === 'object' && menu_item.items.length) {

                        for (const sub_menu_item of menu_item.items) {
                            if (typeof sub_menu_item.ruta === 'string' && sub_menu_item.ruta.length) {
                                this.permisos_seleccionados[sub_menu_item.ruta] = false;
                            }

                            if (typeof sub_menu_item.items === 'object' && sub_menu_item.items.length) {

                                for (const sub_sub_menu_item of sub_menu_item.items) {
                                    if (typeof sub_sub_menu_item.ruta === 'string' && sub_sub_menu_item.ruta.length) {
                                        this.permisos_seleccionados[sub_sub_menu_item.ruta] = false;
                                    }
                                }
                            }
                        }
                    }
                }
            },

            urlSubmitImpresion() {
                return this.$url_get;
            },

            actualizarPermiso(key, e) {
                if (this.cargando_permisos) return;

                const $input = $(e.target);
                this.$set(this.permisos_seleccionados, key, $input.is(':checked'));
                $input.closest('.switch-container').removeClass('intermediate');
                this.$forceUpdate();
            },

            actualizarPermisoDesdeHijos(key, todos, algunos) {
                //console.log(key, estado);
                this.$set(this.permisos_seleccionados, key, algunos);

                const $contenedor_permisos = $(this.$refs['contenedor_permisos']);
                const $input = $contenedor_permisos.find('input[name="permisos[]"][value="' + key + '"]');
                const $item = $input.closest('.switch-container');

                if (algunos && !todos) {
                    $item.addClass('intermediate');
                } else {
                    $item.removeClass('intermediate');
                }

                this.$forceUpdate();
            },

            seleccionarTodosPermisos(seleccionar) {
                this.cargando_permisos = true;
                /*for (const prop in this.permisos_seleccionados) {
                    if (this.permisos_seleccionados.hasOwnProperty(prop)) {
                        this.permisos_seleccionados[prop] = seleccionar;
                        //this.$set(this.permisos_seleccionados, prop, seleccionar);
                    }
                }
                this.$forceUpdate();*/

                const $contenedor_permisos = $(this.$refs['contenedor_permisos']);
                const $items = $contenedor_permisos.find('input[type=checkbox]');

                $items.each(function() {
                    $(this).prop('checked', seleccionar);
                });

                $contenedor_permisos.find('.intermediate').removeClass('intermediate');

                setTimeout(() => {
                    this.cargando_permisos = false;
                    this.$forceUpdate();
                }, 500);
            },

            guardarPermisos() {
                let permisos_guardar = [];
                
                /*for (const permiso in this.permisos_seleccionados) {
                    if (this.permisos_seleccionados.hasOwnProperty(permiso)) {
                        if (this.permisos_seleccionados[permiso]) {
                            permisos_guardar.push(permiso);
                        }
                    }
                }*/

                const $contenedor_permisos = $(this.$refs['contenedor_permisos']);
                const $items = $contenedor_permisos.find('*[name="permisos[]"]');

                $items.each(function() {
                    const $item = $(this);
                    if ($item.prop('tagName') === 'INPUT') {
                        if ($item.is(':checked')) {
                            const accion = $item.data('accion') || '';
                            if (accion.length) {
                                permisos_guardar.push($item.val());
                            }
                        }
                    }
                    else {
                        const categoria = $item.closest('.seleccion-afecta').attr('data-categoria');
                        if (categoria.length) {
                            permisos_guardar.push(categoria + '|' + $item.val());
                        }
                    }
                });

                this.$http.post(this.$url_post, {
                    _fuente: this.fuente,
                    _accion: 'guardarPermisos',
                    id: this.id_item,
                    permisos: permisos_guardar,
                })
                    .then(response => {
                        if (response.status === 200) {
                            resultadoSolicitudDefecto(response.data);

                            if (response.data.ok) {
                                this.permisos_seleccionados = {};
                                this.ocultarFormulario();
                            }
                        }
                        else if (response.status === 500) {
                            mensajeError('Error de servidor.');
                        }
                    });
            },
        },

        mounted() {
            this.cargarData();
            this.inicializarPermisosSeleccionados();

            document.documentElement.scrollTop = 0;
        },

        updated: function () {
            //this.$nextTick(function () {
                initTooltips();
                initKeywords();
            //})
        }
    }
</script>

<style scoped>
    .item-inhabilitado {
        opacity: .6;
    }
</style>