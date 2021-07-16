<template>
    <!-- Wrapper -->
    <div id="wrapper">

        <!-- Header Container
        ================================================== -->
        <header id="header-container" class="fullwidth dashboard-header not-sticky">

            <!-- Header -->
            <div id="header">
                <div class="container">

                    <!-- Left Side Content -->
                    <div class="left-side">

                        <!-- Logo -->
                        <div id="logo">
                            <span class="menu-trigger cursor-active" @click="toggleMenu" >
                                <button class="hamburger hamburger-desktop hamburger--collapse" type="button">
                                    <span class="hamburger-box menu-active" id="menu-hamburger-button">
                                        <span class="hamburger-inner"></span>
                                    </span>
                                </button>
                            </span>
                            <transition
                                mode="out-in"
                                enter-active-class="animated fadeIn"
                            >
                                <template v-if="$empresa.logo">
                                    <router-link to="/">
                                        <img :src="$empresa.logo" alt="" v-show="usuario.cargado">
                                    </router-link>
                                </template>

                                <template v-else>
                                    <router-link to="/">
                                        <span v-show="usuario.cargado">EZA DIGITAL</span>
                                    </router-link>
                                </template>
                            </transition>

                            <!--<h1 @click="modal_empresas = true">FTLOG</h1>-->
                        </div>

                        <!-- Main Navigation -->
                        <nav id="navigation" v-show="false">
                            <ul id="responsive">

                                <li><a href="#">Home</a>
                                    <ul class="dropdown-nav">
                                        <li><a href="index.html">Home 1</a></li>
                                        <li><a href="index-2.html">Home 2</a></li>
                                        <li><a href="index-3.html">Home 3</a></li>
                                    </ul>
                                </li>

                                <li><a href="#">Find Work</a>
                                    <ul class="dropdown-nav">
                                        <li><a href="#">Browse Jobs</a>
                                            <ul class="dropdown-nav">
                                                <li><a href="jobs-list-layout-full-page-map.html">Full Page List + Map</a></li>
                                                <li><a href="jobs-grid-layout-full-page-map.html">Full Page Grid + Map</a></li>
                                                <li><a href="jobs-grid-layout-full-page.html">Full Page Grid</a></li>
                                                <li><a href="jobs-list-layout-1.html">List Layout 1</a></li>
                                                <li><a href="jobs-list-layout-2.html">List Layout 2</a></li>
                                                <li><a href="jobs-grid-layout.html">Grid Layout</a></li>
                                            </ul>
                                        </li>
                                        <li><a href="#">Browse Tasks</a>
                                            <ul class="dropdown-nav">
                                                <li><a href="tasks-list-layout-1.html">List Layout 1</a></li>
                                                <li><a href="tasks-list-layout-2.html">List Layout 2</a></li>
                                                <li><a href="tasks-grid-layout.html">Grid Layout</a></li>
                                                <li><a href="tasks-grid-layout-full-page.html">Full Page Grid</a></li>
                                            </ul>
                                        </li>
                                        <li><a href="browse-companies.html">Browse Companies</a></li>
                                        <li><a href="single-job-page.html">Job Page</a></li>
                                        <li><a href="single-task-page.html">Task Page</a></li>
                                        <li><a href="single-company-profile.html">Company Profile</a></li>
                                    </ul>
                                </li>

                                <li><a href="#">For Employers</a>
                                    <ul class="dropdown-nav">
                                        <li><a href="#">Find a Freelancer</a>
                                            <ul class="dropdown-nav">
                                                <li><a href="freelancers-grid-layout-full-page.html">Full Page Grid</a></li>
                                                <li><a href="freelancers-grid-layout.html">Grid Layout</a></li>
                                                <li><a href="freelancers-list-layout-1.html">List Layout 1</a></li>
                                                <li><a href="freelancers-list-layout-2.html">List Layout 2</a></li>
                                            </ul>
                                        </li>
                                        <li><a href="single-freelancer-profile.html">Freelancer Profile</a></li>
                                        <li><a href="dashboard-post-a-job.html">Post a Job</a></li>
                                        <li><a href="dashboard-post-a-task.html">Post a Task</a></li>
                                    </ul>
                                </li>

                                <li><a href="#" class="current">Dashboard</a>
                                    <ul class="dropdown-nav">
                                        <li><a href="dashboard.html">Dashboard</a></li>
                                        <li><a href="dashboard-messages.html">Messages</a></li>
                                        <li><a href="dashboard-bookmarks.html">Bookmarks</a></li>
                                        <li><a href="dashboard-reviews.html">Reviews</a></li>
                                        <li><a href="dashboard-manage-jobs.html">Jobs</a>
                                            <ul class="dropdown-nav">
                                                <li><a href="dashboard-manage-jobs.html">Manage Jobs</a></li>
                                                <li><a href="dashboard-manage-candidates.html">Manage Candidates</a></li>
                                                <li><a href="dashboard-post-a-job.html">Post a Job</a></li>
                                            </ul>
                                        </li>
                                        <li><a href="dashboard-manage-tasks.html">Tasks</a>
                                            <ul class="dropdown-nav">
                                                <li><a href="dashboard-manage-tasks.html">Manage Tasks</a></li>
                                                <li><a href="dashboard-manage-bidders.html">Manage Bidders</a></li>
                                                <li><a href="dashboard-my-active-bids.html">My Active Bids</a></li>
                                                <li><a href="dashboard-post-a-task.html">Post a Task</a></li>
                                            </ul>
                                        </li>
                                        <li><a href="dashboard-settings.html">Settings</a></li>
                                    </ul>
                                </li>

                                <li><a href="#">Pages</a>
                                    <ul class="dropdown-nav">
                                        <li><a href="pages-blog.html">Blog</a></li>
                                        <li><a href="pages-pricing-plans.html">Pricing Plans</a></li>
                                        <li><a href="pages-checkout-page.html">Checkout Page</a></li>
                                        <li><a href="pages-invoice-template.html">Invoice Template</a></li>
                                        <li><a href="pages-user-interface-elements.html">User Interface Elements</a></li>
                                        <li><a href="pages-icons-cheatsheet.html">Icons Cheatsheet</a></li>
                                        <li><a href="pages-login.html">Login & Register</a></li>
                                        <li><a href="pages-404.html">404 Page</a></li>
                                        <li><a href="pages-contact.html">Contact</a></li>
                                    </ul>
                                </li>

                            </ul>
                        </nav>
                        <div class="clearfix"></div>
                        <!-- Main Navigation / End -->

                    </div>
                    <!-- Left Side Content / End -->


                    <!-- Right Side Content / End -->
                    <div class="right-side">

                        <!--  User Notifications -->
                        <div class="header-widget hide-on-mobile" v-if="false">

                            <!-- Notifications -->
                            <div class="header-notifications">

                                <!-- Trigger -->
                                <div class="header-notifications-trigger">
                                    <a href="#"><i class="icon-feather-bell"></i><span>4</span></a>
                                </div>

                                <!-- Dropdown -->
                                <div class="header-notifications-dropdown">

                                    <div class="header-notifications-headline">
                                        <h4>Notifications</h4>
                                        <button class="mark-as-read ripple-effect-dark" title="Mark all as read" data-tippy-placement="left">
                                            <i class="icon-feather-check-square"></i>
                                        </button>
                                    </div>

                                    <div class="header-notifications-content">
                                        <div class="header-notifications-scroll" data-simplebar>
                                            <ul>
                                                <!-- Notification -->
                                                <li class="notifications-not-read">
                                                    <a href="dashboard-manage-candidates.html">
                                                        <span class="notification-icon"><i class="icon-material-outline-group"></i></span>
                                                        <span class="notification-text">
                                                            <strong>Michael Shannah</strong> applied for a job <span class="color">Full Stack Software Engineer</span>
                                                        </span>
                                                    </a>
                                                </li>

                                                <!-- Notification -->
                                                <li>
                                                    <a href="dashboard-manage-bidders.html">
                                                        <span class="notification-icon"><i class=" icon-material-outline-gavel"></i></span>
                                                        <span class="notification-text">
                                                            <strong>Gilbert Allanis</strong> placed a bid on your <span class="color">iOS App Development</span> project
                                                        </span>
                                                    </a>
                                                </li>

                                                <!-- Notification -->
                                                <li>
                                                    <a href="dashboard-manage-jobs.html">
                                                        <span class="notification-icon"><i class="icon-material-outline-autorenew"></i></span>
                                                        <span class="notification-text">
                                                            Your job listing <span class="color">Full Stack PHP Developer</span> is expiring.
                                                        </span>
                                                    </a>
                                                </li>

                                                <!-- Notification -->
                                                <li>
                                                    <a href="dashboard-manage-candidates.html">
                                                        <span class="notification-icon"><i class="icon-material-outline-group"></i></span>
                                                        <span class="notification-text">
                                                            <strong>Sindy Forrest</strong> applied for a job <span class="color">Full Stack Software Engineer</span>
                                                        </span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                </div>

                            </div>

                            <!-- Messages -->
                            <div class="header-notifications">
                                <div class="header-notifications-trigger">
                                    <a href="#"><i class="icon-feather-mail"></i><span>3</span></a>
                                </div>

                                <!-- Dropdown -->
                                <div class="header-notifications-dropdown">

                                    <div class="header-notifications-headline">
                                        <h4>Messages</h4>
                                        <button class="mark-as-read ripple-effect-dark" title="Mark all as read" data-tippy-placement="left">
                                            <i class="icon-feather-check-square"></i>
                                        </button>
                                    </div>

                                    <div class="header-notifications-content">
                                        <div class="header-notifications-scroll" data-simplebar>
                                            <ul>
                                                <!-- Notification -->
                                                <li class="notifications-not-read">
                                                    <a href="dashboard-messages.html">
                                                        <span class="notification-avatar status-online"><img src="images/user-avatar-small-03.jpg" alt=""></span>
                                                        <div class="notification-text">
                                                            <strong>David Peterson</strong>
                                                            <p class="notification-msg-text">Thanks for reaching out. I'm quite busy right now on many...</p>
                                                            <span class="color">4 hours ago</span>
                                                        </div>
                                                    </a>
                                                </li>

                                                <!-- Notification -->
                                                <li class="notifications-not-read">
                                                    <a href="dashboard-messages.html">
                                                        <span class="notification-avatar status-offline"><img src="images/user-avatar-small-02.jpg" alt=""></span>
                                                        <div class="notification-text">
                                                            <strong>Sindy Forest</strong>
                                                            <p class="notification-msg-text">Hi Tom! Hate to break it to you, but I'm actually on vacation until...</p>
                                                            <span class="color">Yesterday</span>
                                                        </div>
                                                    </a>
                                                </li>

                                                <!-- Notification -->
                                                <li class="notifications-not-read">
                                                    <a href="dashboard-messages.html">
                                                        <span class="notification-avatar status-online"><img src="images/user-avatar-placeholder.png" alt=""></span>
                                                        <div class="notification-text">
                                                            <strong>Marcin Kowalski</strong>
                                                            <p class="notification-msg-text">I received payment. Thanks for cooperation!</p>
                                                            <span class="color">Yesterday</span>
                                                        </div>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    <a href="dashboard-messages.html" class="header-notifications-button ripple-effect button-sliding-icon">View All Messages<i class="icon-material-outline-arrow-right-alt"></i></a>
                                </div>
                            </div>

                        </div>
                        <!--  User Notifications / End -->

                        <!-- User Menu -->
                        <div class="header-widget">

                            <!-- Messages -->
                            <div class="header-notifications user-menu">
                                <div class="header-notifications-trigger">
                                    <a href="#"><div class="user-avatar status-online"><img :src="urlAvatar" alt=""></div></a>
                                </div>

                                <!-- Dropdown -->
                                <div class="header-notifications-dropdown">

                                    <!-- User Status -->
                                    <div class="user-status">

                                        <!-- User Name / Avatar -->
                                        <div class="user-details">
                                            <div class="user-avatar status-online"><img :src="urlAvatar" alt=""></div>
                                            <div class="user-name">
                                                {{ nombrePersona }} <span>{{ usuario.nombre }}</span>
                                            </div>
                                        </div>

                                        <!-- User Status Switcher -->
                                        <div class="status-switch" id="snackbar-user-status" v-show="false">
                                            <label class="user-online current-status">En línea</label>
                                            <label class="user-invisible">No disponible</label>
                                            <!-- Status Indicator -->
                                            <span class="status-indicator" aria-hidden="true"></span>
                                        </div>
                                </div>

                                <ul class="user-menu-small-nav">
                                    <!--<li><a href="dashboard.html"><i class="icon-material-outline-dashboard"></i> Dashboard</a></li>-->
                                    <li>
                                        <router-link to="mi-cuenta">
                                            <i class="icon-material-outline-settings"></i> Mi cuenta
                                        </router-link>
                                    </li>
                                    <li><a :href="urls.cerrar_sesion"><i class="icon-material-outline-power-settings-new"></i> Cerrar sesión</a></li>
                                </ul>

                                </div>
                            </div>

                        </div>
                        <!-- User Menu / End -->

                        <!-- Mobile Navigation Button -->
                        <span class="mmenu-trigger" v-show="false">
                            <button class="hamburger hamburger--collapse" type="button">
                                <span class="hamburger-box">
                                    <span class="hamburger-inner"></span>
                                </span>
                            </button>
                        </span>

                    </div>
                    <!-- Right Side Content / End -->

                </div>
            </div>
            <!-- Header / End -->

        </header>

        <div class="clearfix"></div>
        <!-- Header Container / End -->

        <!-- Dashboard Container -->
        <div class="dashboard-container" >

            <!-- Dashboard Sidebar
            ================================================== -->
            <div class="dashboard-sidebar collapsable" id ="·sidebar-container" >
                <div class="dashboard-sidebar-inner" data-simplebar>
                    <div class="dashboard-nav-container">

                        <!-- Responsive Navigation Trigger -->
                        <a href="#" class="dashboard-responsive-nav-trigger">
                            <span class="hamburger hamburger--collapse" >
                                <span class="hamburger-box">
                                    <span class="hamburger-inner"></span>
                                </span>
                            </span>
                            <span class="trigger-title">Navegación</span>
                        </a>

                        <!-- Navigation -->
                        <div class="dashboard-nav">
                            <div class="dashboard-nav-inner" ref="navegacion">

                                <input type="text" class="input-buscar" placeholder="Buscar..." title="" v-model="filtro_menu" @input="buscarEnMenu" @dblclick="restablecerMenu" @keyup.enter="seleccionarMenuItem" autofocus>

                                <template v-for="categoria in menu_items">
                                    <template v-if="typeof categoria.visible !== 'boolean' || categoria.visible">
                                        <ul class="menu-item-nivel-1" :data-submenu-title="categoria.texto">
                                            <li class="menu-icono" v-if="typeof categoria.icono === 'string' && categoria.icono.length">
                                                <i :class="categoria.icono"></i>
                                            </li>

                                            <template v-for="item in categoria.items" v-if="typeof item.visible !== 'boolean' || item.visible">
                                                <li class="menu-item-nivel-2">

                                                    <template v-if="typeof item.ruta !== 'string' || !item.ruta.length">
                                                        <a href="#">
                                                            <i :class="item.icono"></i> {{ item.texto }}
                                                        </a>
                                                    </template>

                                                    <template v-else-if="tienePermiso(item.ruta)">
                                                        <router-link :to="item.ruta">
                                                            <i :class="item.icono"></i> {{ item.texto }}
                                                        </router-link>
                                                    </template>

                                                    <template v-if="typeof item.items === 'object' && item.items.length">
                                                        <ul>
                                                            <template v-for="subitem in item.items" v-if="(typeof subitem.visible !== 'boolean' || subitem.visible) && tienePermiso(subitem.ruta)">
                                                                <li class="menu-item-nivel-3">
                                                                    <router-link :to="typeof subitem.ruta === 'string' ? subitem.ruta : '#'">
                                                                        <i :class="subitem.icono"></i> {{ subitem.texto }}
                                                                    </router-link>
                                                                </li>
                                                            </template>
                                                        </ul>
                                                    </template>
                                                </li>
                                            </template>
                                        </ul>
                                    </template>
                                </template>

                            </div>
                        </div>
                        <!-- Navigation / End -->

                    </div>
                </div>
            </div>
            <!-- Sidebar / End -->


            <!-- Content
            ================================================== -->
            <div class="dashboard-content-container"> <!--data-simplebar-->
                <div class="dashboard-content-inner">
                    <router-view
                            :usuario="usuario"
                            :urls="urls"
                            :avatar_defecto="avatar_defecto"
                            :key="$route.fullPath"
                            :empresas="empresas"
                    ></router-view>

                    <div class="dashboard-footer-spacer"></div>
                </div>

                <div class="dashboard-content-inner" v-if="false">

                    <!-- Dashboard Headline -->
                    <div class="dashboard-headline">
                        <h3>Post a Task</h3>

                        <!-- Breadcrumbs -->
                        <nav id="breadcrumbs" class="dark">
                            <ul>
                                <li><a href="#">Home</a></li>
                                <li><a href="#">Dashboard</a></li>
                                <li>Post a Task</li>
                            </ul>
                        </nav>
                    </div>

                    <!-- Row -->
                    <div class="row">

                        <!-- Dashboard Box -->
                        <div class="col-xl-12">
                            <div class="dashboard-box margin-top-0">

                                <!-- Headline -->
                                <div class="headline">
                                    <h3><i class="icon-feather-folder-plus"></i> Task Submission Form</h3>
                                </div>

                                <div class="content with-padding padding-bottom-10">
                                    <div class="row">

                                        <div class="col-xl-4">
                                            <div class="submit-field">
                                                <h5>Project Name</h5>
                                                <input type="text" class="with-border" placeholder="e.g. build me a website">
                                            </div>
                                        </div>

                                        <div class="col-xl-4">
                                            <div class="submit-field">
                                                <h5>Category</h5>
                                                <select class="selectpicker with-border" data-size="7" title="Select Category">
                                                    <option>Admin Support</option>
                                                    <option>Customer Service</option>
                                                    <option>Data Analytics</option>
                                                    <option>Design & Creative</option>
                                                    <option>Legal</option>
                                                    <option>Software Developing</option>
                                                    <option>IT & Networking</option>
                                                    <option>Writing</option>
                                                    <option>Translation</option>
                                                    <option>Sales & Marketing</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-xl-4">
                                            <div class="submit-field">
                                                <h5>Location  <i class="help-icon" data-tippy-placement="right" title="Leave blank if it's an online job"></i></h5>
                                                <div class="input-with-icon">
                                                    <div id="autocomplete-container">
                                                        <input id="autocomplete-input" class="with-border" type="text" placeholder="Anywhere">
                                                    </div>
                                                    <i class="icon-material-outline-location-on"></i>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-xl-6">
                                            <div class="submit-field">
                                                <h5>What is your estimated budget?</h5>
                                                <div class="row">
                                                    <div class="col-xl-6">
                                                        <div class="input-with-icon">
                                                            <input class="with-border" type="text" placeholder="Minimum">
                                                            <i class="currency">USD</i>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-6">
                                                        <div class="input-with-icon">
                                                            <input class="with-border" type="text" placeholder="Maximum">
                                                            <i class="currency">USD</i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="feedback-yes-no margin-top-0">
                                                    <div class="radio">
                                                        <input id="radio-1" name="radio" type="radio" checked>
                                                        <label for="radio-1"><span class="radio-label"></span> Fixed Price Project</label>
                                                    </div>

                                                    <div class="radio">
                                                        <input id="radio-2" name="radio" type="radio">
                                                        <label for="radio-2"><span class="radio-label"></span> Hourly Project</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-xl-6">
                                            <div class="submit-field">
                                                <h5>What skills are required? <i class="help-icon" data-tippy-placement="right" title="Up to 5 skills that best describe your project"></i></h5>
                                                <div class="keywords-container">
                                                    <div class="keyword-input-container">
                                                        <input type="text" class="keyword-input with-border" placeholder="Add Skills"/>
                                                        <button class="keyword-input-button ripple-effect"><i class="icon-material-outline-add"></i></button>
                                                    </div>
                                                    <div class="keywords-list"><!-- keywords go here --></div>
                                                    <div class="clearfix"></div>
                                                </div>

                                            </div>
                                        </div>

                                        <div class="col-xl-12">
                                            <div class="submit-field">
                                                <h5>Describe Your Project</h5>
                                                <textarea cols="30" rows="5" class="with-border"></textarea>
                                                <div class="uploadButton margin-top-30">
                                                    <input class="uploadButton-input" type="file" accept="image/*, application/pdf" id="upload" multiple/>
                                                    <label class="uploadButton-button ripple-effect" for="upload">Upload Files</label>
                                                    <span class="uploadButton-file-name">Images or documents that might be helpful in describing your project</span>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-12">
                            <a href="#" class="button ripple-effect big margin-top-30"><i class="icon-feather-plus"></i> Post a Task</a>
                        </div>

                    </div>
                    <!-- Row / End -->

                    <!-- Footer -->
                    <div class="dashboard-footer-spacer"></div>
                    <div class="small-footer margin-top-15">
                        <div class="small-footer-copyrights" v-if="false">
                            © 2019 <strong>JAMIT</strong>
                        </div>
                        <ul class="footer-social-links">
                            <li>
                                <a href="#" title="Facebook" data-tippy-placement="top">
                                    <i class="icon-brand-facebook-f"></i>
                                </a>
                            </li>
                            <li>
                                <a href="#" title="Twitter" data-tippy-placement="top">
                                    <i class="icon-brand-twitter"></i>
                                </a>
                            </li>
                            <li>
                                <a href="#" title="Google Plus" data-tippy-placement="top">
                                    <i class="icon-brand-google-plus-g"></i>
                                </a>
                            </li>
                            <li>
                                <a href="#" title="LinkedIn" data-tippy-placement="top">
                                    <i class="icon-brand-linkedin-in"></i>
                                </a>
                            </li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <!-- Footer / End -->

                </div>
            </div>
            <!-- Dashboard Content / End -->

        </div>
        <!-- Dashboard Container / End -->

        <!--<modal
                v-model="modal_empresas"
        >
            <h1>Welcome from the Main VUE</h1>
        </modal>-->
    </div>
    <!-- Wrapper / End -->
</template>

<script>
    import menu from '../../menu.json'

    export default {
        name: "Main.vue",

        props: {
            avatar_defecto: String,
            img_placeholder: String,
            urls: Object,
            usuario: Object,
            persona: Object,
            empresas: Array,
            opciones: Object,
        },

        data: () => ({
            menu_items: menu,
            filtro_menu: '',
            /*usuario: {
                nombre: '',
                avatar: '',
                persona: {},
                admin: false,
                cargado: false,
            },*/
            /*empresa: {
                id: 0,
                nombre: '',
                color_fondo: '#fff',
                color: '#000',
                logo: null,
            },*/
            modal_empresas: false,
        }),

        computed: {
            urlAvatar() {
                if (typeof this.persona !== 'object' || this.persona === null) {
                    return this.avatar_defecto;
                }
                const foto = this.persona.foto;
                if (typeof foto !== 'string' || !foto.length) {
                    return this.avatar_defecto;
                }
                return this.$uploads_img_dir + foto;
            },

            nombrePersona() {
                if (typeof this.persona !== 'object' || this.persona === null) {
                    return '';
                }
                const persona = this.persona;
                let nombres = [];
                if (typeof persona['primer_nombre'] === 'string' && persona['primer_nombre'].length) nombres.push(persona['primer_nombre']);
                if (typeof persona['segundo_nombre'] === 'string' && persona['segundo_nombre'].length) nombres.push(persona['segundo_nombre'].substr(0, 1));
                if (typeof persona['primer_apellido'] === 'string' && persona['primer_apellido'].length) nombres.push(persona['primer_apellido']);
                if (typeof persona['segundo_apellido'] === 'string' && persona['segundo_apellido'].length) nombres.push(persona['segundo_apellido'].substr(0, 1));

                return nombres.join(' ');
            },
        },

        methods: {
            cargarUsuario() {
                this.$http.get(this.urls.get, {
                    params: {
                        _fuente: 'User',
                        _accion: 'loggedUser',
                    }
                })
                .then(response => {
                    this.usuario.nombre = '';
                    this.usuario.avatar = this.avatar_defecto;
                    this.usuario.admin = false;

                    if (response.status === 200) {
                        const data = response.data;
                        if (data['ok']) {
                            this.usuario.nombre = data['nombre'];
                            this.usuario.admin = !!data['admin'];

                            if (typeof data['persona'] === 'object') {
                                const persona = data['persona'];

                                this.usuario.persona = persona;

                                if (typeof persona['foto'] === 'string' && persona['foto'].length) {
                                    this.usuario.avatar = this.$uploads_img_dir + persona.foto;
                                }
                            }

                            if (typeof data['empresas'] === 'object') {
                                const empresas = data['empresas'];
                                const empresa = empresas[0];

                                this.empresa = {
                                    id: empresa.id,
                                    nombre: empresa.nombre,
                                    color_fondo: '#' + empresa.color_fondo,
                                    color: '#' + empresa.color,
                                    logo: typeof empresa.logo === 'string' && empresa.logo.length ? (this.$uploads_img_dir + empresa.logo) : null,
                                };
                            }
                        }

                        this.usuario.cargado = true;
                    }
                });
            },

            toggleMenu(){
                
                $("#·sidebar-container").toggleClass("collapse-sidebar");
                $("#menu-hamburger-button").toggleClass("menu-active");

            },
            
            /*nombrePersona(persona) {
                let nombres = [];
                if (typeof persona['primer_nombre'] === 'string' && persona['primer_nombre'].length) nombres.push(persona['primer_nombre']);
                if (typeof persona['segundo_nombre'] === 'string' && persona['segundo_nombre'].length) nombres.push(persona['segundo_nombre'].substr(0, 1));
                if (typeof persona['primer_apellido'] === 'string' && persona['primer_apellido'].length) nombres.push(persona['primer_apellido']);
                if (typeof persona['segundo_apellido'] === 'string' && persona['segundo_apellido'].length) nombres.push(persona['segundo_apellido'].substr(0, 1));

                return nombres.join(' ');
            }*/

            actualizarFondo() {
                if (typeof this.opciones['color_fondo'] === 'string' && this.opciones['color_fondo'].length) {
                    const color_fondo = this.opciones['color_fondo'];

                    actualizarColorFondo(color_fondo);

                }
            },

            buscarEnMenu() {
                const val = this.filtro_menu.toLowerCase();
                const $navegacion = $(this.$refs['navegacion']);
                const $uls = $navegacion.find('ul');
                const $as = $navegacion.find('a');
                const filtrado = val.length;

                $uls.removeClass('hidden abierto');

                if (!filtrado) {
                    $as.removeClass('hidden');
                    return;
                }

                $as.each(function() {
                    const $a = $(this);

                    if ($a.text().toLowerCase().indexOf(val) !== -1) {
                        $a.removeClass('hidden');
                        $a.closest('ul.menu-item-nivel-1').addClass('abierto');

                        if ($a.closest('li').hasClass('menu-item-nivel-3')) {

                            $a.closest('li.menu-item-nivel-2')
                                .addClass('active-submenu')
                                .find('> a').eq(0)
                                .removeClass('hidden');
                        }
                    } else {
                        $a.addClass('hidden');
                    }
                });

                $uls.each(function() {
                    const $ul = $(this);
                    if (!$ul.find('a').not('.hidden').length) {
                        $ul.addClass('hidden');
                    }
                });
            },

            restablecerMenu() {
                this.filtro_menu = '';
                this.buscarEnMenu();
            },

            seleccionarMenuItem() {
                if (this.filtro_menu === '') return;
                const $navegacion = $(this.$refs['navegacion']);
                const $as = $navegacion.find('a').not('.hidden');

                $as.each(function() {
                    const $a = $(this);
                    const href = $a.attr('href');

                    if (href !== '#') {
                        window.location = href;

                        setTimeout(function() {
                            $(document).find('.input-crud-buscar').focus();
                        }, 800);

                        return false;
                    }
                });
            }
        },

        mounted() {
            /*if (!this.usuario.cargado) {
                this.cargarUsuario();
            }*/

            //se quitan las categorias del menu si no tienen items
            {
                const $navegacion = $(this.$refs['navegacion']);
                const $uls = $navegacion.find('ul');

                $uls.each(function() {
                    const $ul = $(this);
                    if (!$ul.length) return;
                    if (!$ul.find('a').not('[href="#"]').length) {
                        $ul.remove();
                    }
                });
            }

            this.actualizarFondo();
        },

        created() {
            /*if (!this.usuario.cargado) {
                this.cargarUsuario();
            }*/

            //inicializa las rutas para imagenes y documentos
            Vue.prototype.$uploads_img_dir = this.urls['uploads_img_dir'];
            Vue.prototype.$uploads_doc_dir = this.urls['uploads_doc_dir'];
            Vue.prototype.$public_dir = this.urls['public_dir'];
            Vue.prototype.$avatar_defecto = this.avatar_defecto;
            Vue.prototype.$img_placeholder = this.img_placeholder;
            Vue.prototype.$url_get = this.urls.get;
            Vue.prototype.$url_post = this.urls.post;

            Vue.prototype.$usuario = this.usuario;
            Vue.prototype.$usuario['persona'] = typeof this.persona !== 'undefined' ? this.persona : {};
            Vue.prototype.$usuario['empresas'] = typeof this.empresas !== 'undefined' ? this.empresas : [];

            if (typeof this.empresas === 'object') {
                const empresas = this.empresas;
                const empresa = empresas[0];

                if (typeof empresa === 'object' && empresa !== null) {
                    Vue.prototype.$empresa = {
                        id: empresa.id,
                        nombre: empresa.nombre,
                        color_fondo: '#' + empresa.color_fondo,
                        color: '#' + empresa.color,
                        logo: typeof empresa.logo === 'string' && empresa.logo.length ? (this.$uploads_img_dir + empresa.logo) : null,
                    };
                }
                else {
                    Vue.prototype.$empresa = {
                        id: 0,
                        nombre: '',
                        color_fondo: '#fff',
                        color: '#000',
                        logo: null,
                    };
                }
            }

            Vue.prototype.$usuario['cargado'] = true;

            //moment lang
            moment.locale('es');
        },
    }
</script>

<style scoped>

    .hamburger-desktop{
        top: 6px;
        left: -30px;
    }



    .menu-active span{
        background-color: #2a41e8;
    }

    .menu-active span::before,
    .menu-active span::after{
        background-color: #2a41e8;
    }

    .collapsable{
        transition: flex-basis 500ms ease;
    }

    .collapse-sidebar{
        flex: 0 0 0;
    }

    .cursor-active{
        cursor: pointer;
    }
</style>