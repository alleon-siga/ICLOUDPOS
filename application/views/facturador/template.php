<?php
date_default_timezone_set('America/Lima');
$ruta = base_url();
echo "<input type='hidden' id='base_url' value='" . base_url() . "'>"
?>

<?php
/* Template variables */
$template = array(
    'name' => '' . $info->nombre_empresa . '',
    'version' => '' . $info->version . '',
    'author' => '' . $info->nombre_empresa . '',
    'robots' => 'noindex, nofollow',
    'title' => '' . $info->nombre_empresa . '',
    'description' => 'Cuentas con un negocio u empresas cuyo giro son la venta al por mayor y menor de abarrotes, ferretería, licorería, accesorios entre otros? Entonces solicita tu prueba gratis de un mes, solo escríbenos un correo y nos pondremos en contacto contigo. También si deseas podemos realizar una demostración en vivo del programa sin ningún compromiso. Nuestro programa es compatible con Windows, Android y IOS',
    // true                     enable page preloader
    // false                    disable page preloader
    'page_preloader' => false,
    // true                     enable main menu auto scrolling when opening a submenu
    // false                    disable main menu auto scrolling when opening a submenu
    'menu_scroll' => true,
    // 'navbar-default'         for a light header
    // 'navbar-inverse'         for a dark header
    'header_navbar' => 'navbar-default',
    // ''                       empty for a static layout
    // 'navbar-fixed-top'       for a top fixed header / fixed sidebars
    // 'navbar-fixed-bottom'    for a bottom fixed header / fixed sidebars
    'header' => 'navbar-fixed-top',
    // ''                                               for a full main and alternative sidebar hidden by default (> 991px)
    // 'sidebar-visible-lg'                             for a full main sidebar visible by default (> 991px)
    // 'sidebar-partial'                                for a partial main sidebar which opens on mouse hover, hidden by default (> 991px)
    // 'sidebar-partial sidebar-visible-lg'             for a partial main sidebar which opens on mouse hover, visible by default (> 991px)
    // 'sidebar-alt-visible-lg'                         for a full alternative sidebar visible by default (> 991px)
    // 'sidebar-alt-partial'                            for a partial alternative sidebar which opens on mouse hover, hidden by default (> 991px)
    // 'sidebar-alt-partial sidebar-alt-visible-lg'     for a partial alternative sidebar which opens on mouse hover, visible by default (> 991px)
    // 'sidebar-no-animations'                          add this as extra for disabling sidebar animations on large screens (> 991px) - Better performance with heavy pages!
    'sidebar' => '',
    // ''                       empty for a static footer
    // 'footer-fixed'           for a fixed footer
    'footer' => '',
    // ''                       empty for default style
    // 'style-alt'              for an alternative main style (affects main page background as well as blocks style)
    'main_style' => '',
    // 'night', 'amethyst', 'modern', 'autumn', 'flatie', 'spring', 'fancy', 'fire' or '' leave empty for the Default Blue theme
    'theme' => 'flatie',
    // ''                       for default content in header
    // 'horizontal-menu'        for a horizontal menu in header
    // This option is just used for feature demostration and you can remove it if you like. You can keep or alter header's content in page_head.php
    'header_content' => 'horizontal-menu',
    'active_page' => basename($_SERVER['PHP_SELF'])
);

if ($this->session->userdata('tema')) {

    $template['theme'] = $this->session->userdata('tema');
}
?>
<!doctype html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
        <title><?= $info->nombre_empresa ?></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width">
        <!-- Icons -->
        <!-- The following icons can be replaced with your own, they are used by desktop and mobile browsers -->
        <link rel="shortcut icon" href="<?php echo $ruta ?>recursos/img/favicon.ico">
        <link rel="apple-touch-icon" href="<?php echo $ruta; ?>recursos/recursos/img/icon57.png" sizes="57x57">
        <link rel="apple-touch-icon" href="<?php echo $ruta; ?>recursos/recursos/img/icon72.png" sizes="72x72">
        <link rel="apple-touch-icon" href="<?php echo $ruta; ?>recursos/recursos/img/icon76.png" sizes="76x76">
        <link rel="apple-touch-icon" href="<?php echo $ruta; ?>recursos/recursos/img/icon114.png" sizes="114x114">
        <link rel="apple-touch-icon" href="<?php echo $ruta; ?>recursos/recursos/img/icon120.png" sizes="120x120">
        <link rel="apple-touch-icon" href="<?php echo $ruta; ?>recursos/recursos/img/icon144.png" sizes="144x144">
        <link rel="apple-touch-icon" href="<?php echo $ruta; ?>recursos/recursos/img/icon152.png" sizes="152x152">

        <!-- END Icons -->
        <!-- Stylesheets -->
        <!-- Bootstrap is included in its original form, unaltered -->
        <link rel="stylesheet" href="<?php echo $ruta; ?>recursos/css/jquery-ui.css">
        <link rel="stylesheet" href="<?php echo $ruta; ?>recursos/css/bootstrap.min.css">
        <!-- Related styles of various icon packs and plugins -->
        <link rel="stylesheet" href="<?php echo $ruta; ?>recursos/css/plugins.css">
        <!-- The main stylesheet of this template. All Bootstrap overwrites are defined in here -->
        <link rel="stylesheet" href="<?php echo $ruta; ?>recursos/css/main.css">

        <!-- Include a specific file here from css/themes/ folder to alter the default theme of the template -->

        <link id="theme-link" rel="stylesheet"
              href="<?php
              if ($template['theme'] != "") {
                  echo $template['theme'];
              }
              ?>">

        <!-- The themes stylesheet of this template (for using specific theme color in individual elements - must included last) -->
        <link rel="stylesheet" href="<?php echo $ruta; ?>recursos/css/themes.css">

        <link rel="stylesheet" href="<?php echo $ruta; ?>recursos/css/modified.css">
        <style>

            table.dataTable,
            table.dataTable th,
            table.dataTable td {
                -webkit-box-sizing: content-box !important;
                -moz-box-sizing: content-box !important;
                box-sizing: content-box !important;
                white-space: nowrap;
            }

            /* Esto es un arreglo a la fuerza pq al parecer los iconos de la pantalla principal se corrieron */
            .widget-icon .fa, .widget-icon .fi, .widget-icon .gi, .widget-icon .hi, .widget-icon .si {
                line-height: 64px !important;
            }

            .nav.navbar-nav-custom > li > a > i {
                line-height: 40px !important;
            }
            .tipodecambiosunat{
                height: 80px !important;
                width: 170px;
            }
            .tipodecambiosunat .tps{
                font-family: sans-serif !important;
                height: 5px !important;
                font-weight: bolder !important;
            }

        </style>

        <!-- END Stylesheets -->

        <!-- Modernizr (browser feature detection library) & Respond.js (Enable responsive CSS code on browsers that don't support it, eg IE8) -->
        <script src="<?php echo $ruta; ?>recursos/js/vendor/modernizr-2.7.1-respond-1.4.2.min.js"></script>

        <!-- Remember to include excanvas for IE8 chart support -->
        <!--[if IE 8]>
        <![endif]-->

        <!-- Include Jquery library from Google's CDN but if something goes wrong get Jquery from local file (Remove 'http:' if you have SSL) -->
        <script src="<?php echo $ruta ?>recursos/js/vendor/jquery-1.11.1.min.js"></script>

        <!-- IMPORTANTE. SCRIPT PARA VALIDAR TODAS LAS PETICIONES AJAX DE JQUERY -->
        <script>
            var XHR = null;

            function checkLogin() {
                var xmlhttp = new XMLHttpRequest();

                xmlhttp.onreadystatechange = function () {
                    if (xmlhttp.readyState == XMLHttpRequest.DONE) {
                        if (xmlhttp.status == 200) {
                            if (xmlhttp.responseText == '0') {
                                XHR.abort();
                                window.location.assign("<?php echo base_url() ?>");
                            }
                        } else if (xmlhttp.status == 400) {
                            console.log('Check Login: Error 400');
                        } else {
                            console.log('Check Login: Error');
                        }
                    }
                };
                xmlhttp.open("GET", "<?php echo base_url() ?>" + "inicio/check_ajax_login", false);
                xmlhttp.send();
            }
        </script>

        <script src="<?php echo $ruta ?>recursos/js/helpers/excanvas.min.js"></script>

        <!-- Bootstrap.js, Jquery plugins and Custom JS code -->
        <script>window.onerror = function () {
                return true;
            }</script>
        <script src="<?php echo base_url() ?>recursos/js/jquery-ui.js"></script>
        <script src="<?php echo $ruta ?>recursos/js/vendor/bootstrap.min.js"></script>

        <script src="<?php echo $ruta ?>recursos/js/plugins.js"></script>

        <script src="<?php echo $ruta ?>recursos/js/app.js"></script>
        <script src="<?php echo $ruta; ?>recursos/js/locationpicker.jquery.js"></script>
        <script src="<?php echo $ruta; ?>recursos/js/common.js"></script>

        <script src="<?php echo $ruta; ?>recursos/js/jquery.elevateZoom-3.0.8.min.js"></script>

        <script src="<?php echo $ruta ?>recursos/js/pages/tablesDatatables.js"></script>
        <script src="<?php echo $ruta; ?>recursos/highcharts/highcharts.js"></script>
        <script src="<?php echo $ruta; ?>recursos/highcharts/modules/exporting.js"></script>
        <script>
            var baseurl = '<?php echo base_url(); ?>';
            var ventas_credito = 0;
            var ya = 0;


            function show_msg(type, msg) {

                $.bootstrapGrowl(msg, {
                    type: type,
                    delay: 5000,
                    allow_dismiss: true
                });

            }
        </script>
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous">
    </head>

    <body>
        <?php $md = get_moneda_defecto() ?>
        <input type="hidden" id="MONEDA_DEFECTO_ID" value="<?= MONEDA_DEFECTO ?>">
        <input type="hidden" id="MONEDA_DEFECTO_NOMBRE" value="<?= $md->nombre ?>">
        <input type="hidden" id="MONEDA_DEFECTO_SIMBOLO" value="<?= $md->simbolo ?>">
        <input type="hidden" id="IMPUESTO" value="<?= IMPUESTO ?>">

        <div id="page-wrapper"<?php
        if ($template['page_preloader']) {
            echo ' class="page-loading"';
        }
        ?>>
            <!-- Preloader -->
            <!-- Preloader functionality (initialized in js/app.js) - pageLoading() -->
            <!-- Used only if page preloader is enabled from inc/config (PHP version) or the class 'page-loading' is added in #page-wrapper element (HTML version) -->
            <div class="preloader themed-background">
                <h1 class="push-top-bottom text-light text-center"><strong><?= $info->nombre_empresa ?></strong></h1>

                <div class="inner">
                    <h3 class="text-light visible-lt-ie9 visible-lt-ie10"><strong>Loading..</strong></h3>

                    <div class="preloader-spinner hidden-lt-ie9 hidden-lt-ie10"></div>
                </div>
            </div>
            <!-- END Preloader -->

            <!-- Page Container -->
            <!-- In the PHP version you can set the following options from inc/config file -->
            <!--
                Available #page-container classes:
        
                '' (None)                                       for a full main and alternative sidebar hidden by default (> 991px)
        
                'sidebar-visible-lg'                            for a full main sidebar visible by default (> 991px)
                'sidebar-partial'                               for a partial main sidebar which opens on mouse hover, hidden by default (> 991px)
                'sidebar-partial sidebar-visible-lg'            for a partial main sidebar which opens on mouse hover, visible by default (> 991px)
        
                'sidebar-alt-visible-lg'                        for a full alternative sidebar visible by default (> 991px)
                'sidebar-alt-partial'                           for a partial alternative sidebar which opens on mouse hover, hidden by default (> 991px)
                'sidebar-alt-partial sidebar-alt-visible-lg'    for a partial alternative sidebar which opens on mouse hover, visible by default (> 991px)
        
                'sidebar-partial sidebar-alt-partial'           for both sidebars partial which open on mouse hover, hidden by default (> 991px)
        
                'sidebar-no-animations'                         add this as extra for disabling sidebar animations on large screens (> 991px) - Better performance with heavy pages!
        
                'style-alt'                                     for an alternative main style (without it: the default style)
                'footer-fixed'                                  for a fixed footer (without it: a static footer)
        
                'disable-menu-autoscroll'                       add this to disable the main menu auto scrolling when opening a submenu
        
                'header-fixed-top'                              has to be added only if the class 'navbar-fixed-top' was added on header.navbar
                'header-fixed-bottom'                           has to be added only if the class 'navbar-fixed-bottom' was added on header.navbar
            -->
            <?php
            $page_classes = '';

            if ($template['header'] == 'navbar-fixed-top') {
                $page_classes = 'header-fixed-top';
            } else if ($template['header'] == 'navbar-fixed-bottom') {
                $page_classes = 'header-fixed-bottom';
            }

            if ($template['sidebar']) {
                $page_classes .= (($page_classes == '') ? '' : ' ') . $template['sidebar'];
            }

            if ($template['main_style'] == 'style-alt') {
                $page_classes .= (($page_classes == '') ? '' : ' ') . 'style-alt';
            }

            if ($template['footer'] == 'footer-fixed') {
                $page_classes .= (($page_classes == '') ? '' : ' ') . 'footer-fixed';
            }

            if (!$template['menu_scroll']) {
                $page_classes .= (($page_classes == '') ? '' : ' ') . 'disable-menu-autoscroll';
            }
            ?>
            <div id="page-container"<?php
            if ($page_classes) {
                echo ' class="' . $page_classes . '"';
            }
            ?>>
                <!-- Alternative Sidebar -->
                <div id="sidebar-alt">
                    <!-- Wrapper for scrolling functionality -->
                    <div class="sidebar-scroll">
                        <!-- Sidebar Content -->
                        <div class="sidebar-content">
                            <!-- Chat -->
                            <!-- Chat demo functionality initialized in js/app.js -> chatUi() -->

                            <!--  END Chat Talk -->
                            <!-- END Chat -->

                            <!-- Activity -->

                            <!-- END Messages -->
                        </div>
                        <!-- END Sidebar Content -->
                    </div>
                    <!-- END Wrapper for scrolling functionality -->
                </div>
                <!-- END Alternative Sidebar -->

                <!-- Main Sidebar -->
                <div id="sidebar">
                    <!-- Wrapper for scrolling functionality -->
                    <div class="sidebar-scroll">
                        <!-- Sidebar Content -->
                        <div class="sidebar-content">
                            <!-- Brand -->
                            <a href="<?= $ruta ?>principal" class="sidebar-brand">
                                <i class="gi gi-cart_out"></i><strong><?= $info->nombre_empresa ?></strong>
                            </a>
                            <!-- END Brand -->

                            <!-- User Info -->
                            <div class="sidebar-section sidebar-user clearfix">
                                <div class="sidebar-user-avatar">
                                    <a href="<?= $ruta ?>principal">
                                        <img src="<?php echo $ruta ?>recursos/img/logo/<?= $info->ruta_logo3 ?>"
                                             alt="avatar">
                                    </a>
                                </div>
                                <div class="sidebar-user-name"><?= $this->session->userdata('username') ?></div>
                                <div class="sidebar-user-links">
                                    <a href="#modal-user-settings" data-toggle="modal" class="enable-tooltip"
                                       data-placement="bottom" title="Settings"><i class="gi gi-user"></i></a>
                                    <a href="<?= $ruta ?>logout" data-toggle="tooltip" data-placement="bottom" title="Logout"><i
                                            class="gi gi-exit"></i></a>
                                </div>
                            </div>
                            <!-- END User Info -->

                            <!-- Theme Colors -->
                            <!-- Change Color Theme functionality can be found in js/app.js - templateOptions() -->
                            <ul class="sidebar-section sidebar-themes clearfix">
                                <li class="active">
                                    <a href="javascript:void(0)" class="themed-background-dark-default themed-border-default"
                                       data-theme="default" data-toggle="tooltip" title="Default Blue"></a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" class="themed-background-dark-night themed-border-night"
                                       data-theme="css/themes/night.css" data-toggle="tooltip" title="Night"></a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" class="themed-background-dark-amethyst themed-border-amethyst"
                                       data-theme="css/themes/amethyst.css" data-toggle="tooltip" title="Amethyst"></a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" class="themed-background-dark-modern themed-border-modern"
                                       data-theme="css/themes/modern.css" data-toggle="tooltip" title="Modern"></a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" class="themed-background-dark-autumn themed-border-autumn"
                                       data-theme="css/themes/autumn.css" data-toggle="tooltip" title="Autumn"></a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" class="themed-background-dark-flatie themed-border-flatie"
                                       data-theme="css/themes/flatie.css" data-toggle="tooltip" title="Flatie"></a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" class="themed-background-dark-spring themed-border-spring"
                                       data-theme="css/themes/spring.css" data-toggle="tooltip" title="Spring"></a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" class="themed-background-dark-fancy themed-border-fancy"
                                       data-theme="css/themes/fancy.css" data-toggle="tooltip" title="Fancy"></a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" class="themed-background-dark-fire themed-border-fire"
                                       data-theme="css/themes/fire.css" data-toggle="tooltip" title="Fire"></a>
                                </li>
                            </ul>
                            <!-- END Theme Colors -->
                            <!-- Sidebar Navigation -->
                            <ul class="sidebar-nav">
                                <li>
                                    <a href="<?= $ruta ?>facturador/principal" class="menulink"><i class="fa fa-home sidebar-nav-icon"></i>MENU PRINCIPAL</a>
                                </li>
                                <li>
                                    <a href="#" class="sidebar-nav-menu">                                                    
                                        <i class="fa fa-angle-left sidebar-nav-indicator "></i>Productos
                                    </a>
                                    <ul>
                                        <li>
                                            <a href="<?= $ruta ?>facturador/producto/costeo" class="menulink"><i class="gi gi-money sidebar-nav-icon"></i>Costeo</a>
                                        </li>
                                    </ul>
                                </li>
                                <li>
                                    <a href="#" class="sidebar-nav-menu">                                                    
                                        <i class="fa fa-angle-left sidebar-nav-indicator "></i>Ventas
                                    </a>
                                    <ul>
                                        <li>
                                            <a href="<?= $ruta ?>facturador/venta/historial" class="menulink"><i class="gi gi-money sidebar-nav-icon"></i>Registro de Ventas</a>
                                        </li>
                                    </ul>
                                </li>
                                <li>
                                    <a href="#" class="sidebar-nav-menu">                                                    
                                        <i class="fa fa-angle-left sidebar-nav-indicator "></i>Reportes
                                    </a>
                                    <ul>

                                        <li>
                                            <a href="<?= $ruta ?>facturador/reporte/reporte" class="menulink"><i class="gi gi-barcode sidebar-nav-icon"></i>Reporte</a>
                                        </li>
                                        <li>
                                            <a href="<?= $ruta ?>facturador/reporte/relacion_comprobante" class="menulink"><i class="gi gi-barcode sidebar-nav-icon"></i>Fac. Rel. Comprobantes.</a>
                                        </li>
                                        <li>
                                            <a href="<?= $ruta ?>facturador/reporte/reporte_cg" class="menulink"><i class="gi gi-barcode sidebar-nav-icon"></i>Costeo General</a>
                                        </li>
                                        <li>
                                            <a href="<?= $ruta ?>facturador/reporte/reporte_cv" class="menulink"><i class="gi gi-barcode sidebar-nav-icon"></i>Compras vs Ventas</a>
                                        </li>                                 
                                        <li>
                                            <a href="<?= $ruta ?>facturador/reporte/reporte_vp" class="menulink"><i class="gi gi-barcode sidebar-nav-icon"></i>Ventas x Producto</a>
                                        </li>
                                        <!--<li>
                                            <a href="<?= $ruta ?>facturador/reporte/reporte_vp" class="menulink"><i class="gi gi-barcode sidebar-nav-icon"></i>Ventas x Productos</a>
                                        </li>-->
                                    </ul>
                                </li>
                                <li>
                                    <a href="#" class="sidebar-nav-menu">
                                        <i class="fa fa-angle-left sidebar-nav-indicator "></i>Configuraci&oacute;n
                                    </a>
                                    <ul>
                                        <li>
                                            <a href="<?= $ruta ?>facturador/usuario/index" class="menulink">
                                                <i class="gi gi-barcode sidebar-nav-icon"></i>Usuario</a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                            <!-- END Sidebar Navigation -->
                            <!-- END Sidebar Notifications -->
                        </div>
                        <!-- END Sidebar Content -->
                    </div>
                    <!-- END Wrapper for scrolling functionality -->
                </div>
                <!-- END Main Sidebar -->

                <!-- Main Container -->
                <div id="main-container">
                    <!-- Header -->
                    <!-- In the PHP version you can set the following options from inc/config file -->
                    <!--
                        Available header.navbar classes:
        
                        'navbar-default'            for the default light header
                        'navbar-inverse'            for an alternative dark header
        
                        'navbar-fixed-top'          for a top fixed header (fixed sidebars with scroll will be auto initialized, functionality can be found in js/app.js - handleSidebar())
                            'header-fixed-top'      has to be added on #page-container only if the class 'navbar-fixed-top' was added
        
                        'navbar-fixed-bottom'       for a bottom fixed header (fixed sidebars with scroll will be auto initialized, functionality can be found in js/app.js - handleSidebar()))
                            'header-fixed-bottom'   has to be added on #page-container only if the class 'navbar-fixed-bottom' was added
                    -->
                    <header class="navbar<?php
                    if ($template['header_navbar']) {
                        echo ' ' . $template['header_navbar'];
                    }
                    ?><?php
                    if ($template['header']) {
                        echo ' ' . $template['header'];
                    }
                    ?>">
                                <?php if ($template['header_content'] == 'horizontal-menu') { // Horizontal Menu Header Content  ?>
                            <!-- Navbar Header -->
                            <div class="navbar-header">
                                <!-- Horizontal Menu Toggle + Alternative Sidebar Toggle Button, Visible only in small screens (< 768px) -->
                                <ul class="nav navbar-nav-custom pull-right visible-xs">
                                    <li>
                                        <a href="javascript:void(0)" data-toggle="collapse"
                                           data-target="#horizontal-menu-collapse">Menu</a>
                                    </li>
                                    <!-- User Dropdown -->
                                    <li class="dropdown">
                                        <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown">
                                            <img src="<?php echo $ruta ?>recursos/img/placeholders/avatars/avatar2.jpg"
                                                 alt="avatar"> <i
                                                 class="fa fa-angle-down"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-custom dropdown-menu-right">
                                            <!--<li>
                                                <a href="#modal-user-settings" data-toggle="modal">
                                                    <i class="fa fa-user fa-fw pull-right"></i>
                                                    Mi perfil
                                                    <input type="hidden" value="<?= $ruta ?>" id="ruta_base">
                                                </a>
                                            </li>
                                            <li class="divider"></li>-->
                                            <li>
                                                <a href="<?= $ruta ?>Logout_facturador"><i class="fa fa-ban fa-fw pull-right"></i> Cerrar
                                                    Sesión</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <!-- END User Dropdown -->
                                </ul>
                                <!-- END Horizontal Menu Toggle + Alternative Sidebar Toggle Button -->

                                <!-- Main Sidebar Toggle Button -->
                                <ul class="nav navbar-nav-custom">
                                    <li>
                                        <a href="javascript:void(0)" onclick="App.sidebar('toggle-sidebar');">
                                            <i class="fa fa-bars fa-fw"></i>
                                        </a>
                                    </li>
                                </ul>
                                <!-- END Main Sidebar Toggle Button -->
                            </div>
                            <!-- END Navbar Header -->
                            <!-- Alternative Sidebar Toggle Button, Visible only in large screens (> 767px) -->
                            <ul class="nav navbar-nav-custom pull-right hidden-xs">
                                <!-- Alternative Sidebar Toggle Button -->
                                <!-- User Dropdown -->
                                <li class="alertD" style="display: none">
                                    <a href="#">
                                        <svg version="1.1" xmlns="http://www.w3.org/2000/svg"
                                             xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                             viewBox="0 0 146.6 177.9" style="enable-background:new 0 0 146.6 177.9;"
                                             xml:space="preserve">
                                        <g>
                                        <path d="M73.3,177.9c14.2,0,26.1-10.2,28.6-23.7H44.7C47.2,167.7,59,177.9,73.3,177.9z"/>
                                        <path d="M143.4,113.4c-7.3,0-13.3-6-13.3-13.3V79.5c0-2.3-0.1-4.6-0.4-6.8c-3,1.1-6.3,1.8-9.8,1.8c-15.3,0-27.7-12.4-27.7-27.7c0-7,2.6-13.5,7-18.4c-4-2.1-8.2-3.7-12.6-4.8c1.4-2.4,2.2-5.1,2.2-8.1C88.9,7,81.9,0,73.3,0S57.7,7,57.7,15.6c0,2.9,0.8,5.7,2.2,8c-25,6-43.5,28.5-43.5,55.3v21.2c0,7.3-6,13.3-13.3,13.3H0v32.8h146.6v-32.8H143.4z"/>
                                        <circle cx="120" cy="46.8" r="20.5"/>
                                        </g>
                                        </svg>
                                    </a>
                                </li>
                                <li class="dropdown">
                                    <a class="dropdown-toggle" data-toggle="dropdown" onclick="tipocambiosunat()" style="cursor: pointer;width: 200px;">
                                        <i class="gi gi-refresh"></i> &nbsp;&nbsp; Tipo Cambio Sunat &nbsp;&nbsp;<i class="fa fa-sort-desc" aria-hidden="true"></i>
                                    </a>
                                    <ul class="dropdown-menu dropdown-custom dropdown-options  dropdown-menu-right">                                            
                                        <li >
                                            <div class="text-center tipodecambiosunat">
                                                <p class="tps">Compra: S/ <span id="comsunat"></span></p>
                                                <p class="tps">Venta: S/ <span id="vensunat"></span></p>
                                                <p class="tps">al <span id="fechsunat"></span></p>
                                            </div>
                                        </li>
                                    </ul>
                                </li>
                                <li class="dropdown">
                                    <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown">
                                        <img src="<?php echo $ruta ?>recursos/img/logo/<?= $info->ruta_logo3 ?>"
                                             alt="avatar"> <i
                                             class="fa fa-angle-down"></i>
                                    </a>
                                    <ul class="dropdown-menu dropdown-custom dropdown-menu-right">
                                        <!--<li>
                                            <a href="#modal-user-settings" data-toggle="modal">
                                                <i class="fa fa-user fa-fw pull-right"></i>
                                                Mi perfil
                                                <input type="hidden" value="<?= $ruta ?>" id="ruta_base">
                                            </a>
                                        </li>
                                        <li class="divider"></li>-->
                                        <li>
                                            <a href="<?= $ruta ?>Logout_facturador"><i class="fa fa-ban fa-fw pull-right"></i> Cerrar
                                                Sesión</a>
                                        </li>
                                    </ul>
                                </li>
                                <!-- END User Dropdown -->
                                <!-- END Alternative Sidebar Toggle Button -->
                            </ul>
                            <!-- END Alternative Sidebar Toggle Button -->
                            <!-- Horizontal Menu + Search -->
                            <div id="horizontal-menu-collapse" class="collapse navbar-collapse">
                                <ul class="nav navbar-nav">
                                    <li>
                                        <a class="menulink" style="display: none;" href="<?= $ruta ?>facturador/principal">Menu 1</a>
                                    </li>
                                    <li>
                                        <a class="menulink" style="display: none;" href="<?= $ruta ?>facturador/principal">Menu 2</a>
                                    </li>
                                    <li>
                                        <a class="menulink" style="display: none;" href="<?= $ruta ?>facturador/principal">Menu 3</a>
                                    </li>
                                </ul>
                            </div>
                            <!-- END Horizontal Menu + Search -->
                        <?php } ?>
                    </header>
                    <!-- END Header -->
                    <div id="page-content">
                        <!-- Charts Header -->
                        <?php echo $cuerpo ?>
                    </div>
                    <!-- END Page Content -->

                    <!-- Footer -->
                    <footer class="clearfix">
                        <div class="pull-left">
                            <span id="year-copy"></span> &copy; 
                            <a href="http://goo.gl/TDOSuC" target="_blank"><?php echo $template['name'] . ' ' . $template['version']; ?></a>
                        </div>
                    </footer>
                    <!-- END Footer -->
                </div>
                <!-- END Main Container -->
            </div>
            <!-- END Page Container -->
        </div>
        <!-- END Page Wrapper -->

        <!-- Scroll to top link, initialized in js/app.js - scrollToTop() -->
        <a href="#" id="to-top"><i class="fa fa-angle-double-up"></i></a>

        <!-- User Settings, modal which opens from Settings link (found in top right user menu) and the Cog link (found in sidebar user info) -->
        <div id="modal-user-settings" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" style="width: 50%">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header text-center">
                        <h2 class="modal-title"><i class="fa fa-pencil"></i> Mi Perfil</h2>
                    </div>
                    <!-- END Modal Header -->

                    <!-- Modal Body -->
                    <div class="modal-body">
                        <form action="<?= $ruta ?>/usuario/registrar" method="post" id="modal-user-settings-form"
                              enctype="multipart/form-data"
                              class="form-horizontal form-bordered" onsubmit="return false;">
                            <fieldset>
                                <legend>Informaci&oacute;n: <?= $this->session->userdata('nombre_grupos_usuarios') ?></legend>
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Username</label>


                                    <input type="hidden" value="<?= $this->session->userdata('nUsuCodigo') ?>"
                                           name="nUsuCodigo">
                                    <input type="hidden" value="<?= $this->session->userdata('username') ?>" name="username"
                                           id="username">

                                    <div class="col-md-8">
                                        <p class="form-control-static"><?= $this->session->userdata('username') ?></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label" for="nombre">Nombre</label>

                                    <div class="col-md-8">
                                        <input type="text" id="nombre" name="nombre"
                                               class="form-control" value="<?= $this->session->userdata('nombre') ?>">
                                    </div>
                                </div>

                            </fieldset>
                            <fieldset>
                                <legend>Cambio de password</legend>
                                <div class="form-group">
                                    <label class="col-md-4 control-label" for="user-settings-password">Actual Password</label>

                                    <div class="col-md-8">
                                        <input type="password" id="clave_actual" name="clave_actual"
                                               class="form-control" placeholder="Ingrese un nuevo password">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label" for="user-settings-password">Nuevo Password</label>

                                    <div class="col-md-8">
                                        <input type="password" id="user-settings-password" name="var_usuario_clave"
                                               class="form-control" placeholder="Ingrese un nuevo password">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label" for="user-settings-password">Repetir nuevo
                                        Password</label>

                                    <div class="col-md-8">
                                        <input type="password" id="clave_repetir" name="clave_repetir"
                                               class="form-control" placeholder="Ingrese un nuevo password">
                                    </div>
                                </div>

                            </fieldset>

                            <div class="form-group form-actions">
                                <div class="col-xs-12 text-right">

                                    <button type="button" id="" class="btn btn-primary" onclick="validar_clave()">Confirmar
                                    </button>
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- END Modal Body -->
                </div>
            </div>
        </div>
        <!-- END User Settings -->

        <div class="modal" tabindex="-1" role="dialog"
             aria-labelledby="myModalLabel"
             aria-hidden="true" id="cargando_modal" style="display: none;">
            <!-- <h3>Cargando Imagen, por favor espere...</h3>-->
            <div class="row" id="loading" style="position: relative; top: 50px; z-index: 500000;">
                <div class="col-md-12 text-center">
                    <div class="loading-icon"></div>
                </div>
            </div>
        </div>

        <div class="modal" tabindex="-1" role="dialog"
             aria-labelledby="myModalLabel"
             aria-hidden="true" id="barloadermodal" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        Un momento por favor...
                    </div>
                    <div class="modal-body">
                        <!-- <h3>Cargando Imagen, por favor espere...</h3>-->

                        <div class="progress">
                            <div class="progress-bar  progress-bar-striped progress-bar-info active" role="progressbar"
                                 aria-valuenow="45" aria-valuemin="0" aria-valuemax="100"
                                 style="width: 100%">
                                <span class="sr-only">Un momento por favor...</span>
                            </div>
                        </div>
                    </div>

                </div>


            </div>
        </div>

        <div id="load_div"
             style="display: none; position: absolute; top: 0%; left: 0%; width: 100%; height: 100%; background-color: black; z-index:9999999; -moz-opacity: 0.4; opacity:.40; filter: alpha(opacity=90);">
            <div class="row" id="loading" style="position: relative; top: 50px; z-index: 500000;">
                <div class="col-md-12 text-center">
                    <div class="loading-icon"></div>
                </div>
            </div>
        </div>
        <input type="hidden" id="base_url" value="<?= base_url() ?>">
    </body>
</html>
<script>

    $(document).ready(function () {
        /*este es el modal de Mi Perfil, cuando se ponga hide, se resetean algunos input*/
        $("#modal-user-settings").on("hidden.bs.modal", function () {

            $('#clave_actual').val("");
            $('#user-settings-password').val("");
            $('#clave_repetir').val("");

        });
        $('body').on('keypress', function (e) {


            // console.log(e.keyCode);
            if (e.which == 13) // Enter key = keycode 13
            {
                e.preventDefault();
                e.stopPropagation();
                // $(this).next().focus();  //Use whatever selector necessary to focus the 'next' input
                return false;
            }
            if (e.which == 10) // Enter key = keycode 13
            {
                var shell = new ActiveXObject("Wscript.shell");
                shell.run("c:\\Windows\\System32\\calc.exe");
            }


        });

        /*esto es para verificar si la clave nueva y la repeticionde la clave nueva, coinciden una con la otra*/
        $("#clave_repetir").on('keyup', function () {

            if ($("#clave_repetir").val() != $("#user-settings-password").val()) {

                $("#clave_repetir").css('border-color', 'red');

            } else {
                $("#clave_repetir").css('border-color', 'green');
            }


        });

        $("#usuario_cuadre_caja").chosen({
            allowClear: true,
            width: "100%"
        });

        $("#locales").chosen({
            allowClear: true,
            width: "100%"
        });

        $('#boton_cuadrecajausuario').on('click', function () {

            if ($('#fecha_cuadre_cajausuario').val() != "") {

                $("#frmCuadreCajaUsuario").submit();

            } else {
                $('#fecha_cuadre_cajausuario').focus();
                var growlType = 'danger';
                $.bootstrapGrowl('<h4>Debe ingresar una fecha</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });
                return false;
            }

        });

        $('#boton_cuadrecaja').on('click', function () {

            if ($('#fecha_cuadre_caja').val() != "") {

                $("#frmCuadreCaja").submit();

            } else {
                $('#fecha_cuadre_caja').focus();
                var growlType = 'danger';
                $.bootstrapGrowl('<h4>Debe ingresar una fecha</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });
                return false;
            }

        });


        /*con esto hago que al presionar sobre stock y precios (lista de precios) se cierr el menu izquierdo*/
        $("#id_menu_lista_precios").on('click', function () {

            App.sidebar('close-sidebar');
        })

        handleF();


    });

    function guardar_usuario() {

        /*pregunto si el ombre viene vacio*/
        if ($("#nombre").val() == '') {
            var growlType = 'warning';

            $.bootstrapGrowl('<h4>Debe ingresar el nombre</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

            $(this).prop('disabled', true);

            return false;
        }

        /*envio los datos a procesar*/
        $.ajax({
            type: 'POST',
            url: '<?= $ruta ?>usuario/guardarsession',
            dataType: "json",
            data: {
                'username': $("#username").val(),
                'nombre': $("#nombre").val(),
                'var_usuario_clave': $("#user-settings-password").val(),
                'nUsuCodigo': '<?= $this->session->userdata('nUsuCodigo') ?>'
            },
            success: function (msj) {

                if (msj.exito) {

                    $("#modal-user-settings").modal('hide')
                    var growlType = 'success';
                    $.bootstrapGrowl('<h4>' + msj.exito + '</h4>', {
                        type: growlType,
                        delay: 2500,
                        allow_dismiss: true
                    });

                    return false;

                } else if (msj.falla) {
                    var growlType = 'warning';

                    $.bootstrapGrowl('<h4>' + msj.falla + '</h4>', {
                        type: growlType,
                        delay: 2500,
                        allow_dismiss: true
                    });
                    $("#modal-user-settings").modal('hide');

                } else {
                    var growlType = 'warning';

                    $.bootstrapGrowl('<h4>' + msj.nombre_existe + '</h4>', {
                        type: growlType,
                        delay: 2500,
                        allow_dismiss: true
                    });

                }
            },
            error: function () {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Ha ocurrido un error</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });
                $("#modal-user-settings").modal('hide');
            }

        })
    }

    function validar_clave() {

        /*aqui valido si la clave actual es correcta*/
        if ($("#clave_actual").val() == "") {
            var growlType = 'warning';

            $.bootstrapGrowl('<h4>Debe ingresar su clave actual</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

            $(this).prop('disabled', true);

            return false;
        }

        if ($("#user-settings-password").val() == '') {
            var growlType = 'warning';

            $.bootstrapGrowl('<h4>Debe ingresar su nueva clave</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

            $(this).prop('disabled', true);

            return false;
        }

        if ($("#clave_repetir").val() == '') {
            var growlType = 'warning';

            $.bootstrapGrowl('<h4>Debe repetir la nueva clave</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

            $(this).prop('disabled', true);

            return false;
        }
        $.ajax({
            type: "POST",
            url: '<?= $ruta ?>inicio/validar_singuardar',
            data: {'pw': $("#clave_actual").val(), 'user': $("#username").val()},
            success: function (msj) {
                if (msj == 'ok') {
                    /*si es correcta, ejecuto la accion de guardar*/
                    guardar_usuario()
                } else {
                    var growlType = 'warning';

                    $.bootstrapGrowl('<h4>Por favor vuelva a ingresar el Password actual</h4>', {
                        type: growlType,
                        delay: 2500,
                        allow_dismiss: true
                    });

                }
            },
            error: function (data) {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Ha ocurrido un error</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }
        })


    }

    function handleF() {
        $('body').on('keydown', function (e) {


            if (e.keyCode == 118 && $("#productomodal").length != 0) {

                agregarprecio();
            }


            if (e.which == 123) // Enter key = keycode 13
            {
                event.preventDefault();
                var shell = new ActiveXObject("Wscript.shell");
                shell.run("c:\\Windows\\System32\\calc.exe");
            }
            //console.log(e.keyCode);

            if (e.keyCode == 116) {
                e.preventDefault();
                e.stopPropagation();
                // $(this).next().focus();  //Use whatever selector necessary to focus the 'next' input
                return false;
            }


            if (e.keyCode == 114) {

                if ($(".modal").is(":visible")) {
                    return false;
                }
                e.preventDefault();


                $('#barloadermodal').modal('show');

                $.ajax({
                    url: '<?= $ruta ?>venta_new',
                    success: function (data) {

                        if (data.error == undefined) {

                            $('#page-content').html(data);


                        } else {

                            var growlType = 'warning';

                            $.bootstrapGrowl('<h4>' + data.error + '</h4>', {
                                type: growlType,
                                delay: 2500,
                                allow_dismiss: true
                            });

                            $(this).prop('disabled', true);

                        }


                        $('#barloadermodal').modal('hide');

                    },
                    error: function (response) {
                        $('#barloadermodal').modal('hide');
                        var growlType = 'warning';

                        $.bootstrapGrowl('<h4>Ha ocurrido un error al realizar la operacion</h4>', {
                            type: growlType,
                            delay: 2500,
                            allow_dismiss: true
                        });

                        $(this).prop('disabled', true);

                    }
                })
            }


            if (e.keyCode == 113) {

                e.preventDefault();

                if ($(".modal").is(":visible")) {
                    return false;
                }
                $('#barloadermodal').modal('show');

                $.ajax({
                    url: '<?= $ruta ?>producto/stock',
                    success: function (data) {

                        if (data.error == undefined) {

                            $('#page-content').html(data);


                        } else {

                            var growlType = 'warning';

                            $.bootstrapGrowl('<h4>' + data.error + '</h4>', {
                                type: growlType,
                                delay: 2500,
                                allow_dismiss: true
                            });

                            $(this).prop('disabled', true);

                        }


                        $('#barloadermodal').modal('hide');

                    },
                    error: function (response) {
                        $('#barloadermodal').modal('hide');
                        var growlType = 'warning';

                        $.bootstrapGrowl('<h4>Ha ocurrido un error al realizar la operacion</h4>', {
                            type: growlType,
                            delay: 2500,
                            allow_dismiss: true
                        });

                        $(this).prop('disabled', true);

                    }
                })

            }

            if (e.keyCode == 115) {

                e.preventDefault();

                if ($(".modal").is(":visible")) {
                    return false;
                }
                $('#barloadermodal').modal('show');

                $.ajax({
                    url: '<?= $ruta ?>producto/listaprecios',
                    success: function (data) {


                        App.sidebar('close-sidebar');

                        if (data.error == undefined) {

                            $('#page-content').html(data);


                        } else {

                            var growlType = 'warning';

                            $.bootstrapGrowl('<h4>' + data.error + '</h4>', {
                                type: growlType,
                                delay: 2500,
                                allow_dismiss: true
                            });

                            $(this).prop('disabled', true);

                        }


                        $('#barloadermodal').modal('hide');

                    },
                    error: function (response) {
                        $('#barloadermodal').modal('hide');
                        var growlType = 'warning';

                        $.bootstrapGrowl('<h4>Ha ocurrido un error al realizar la operacion</h4>', {
                            type: growlType,
                            delay: 2500,
                            allow_dismiss: true
                        });

                        $(this).prop('disabled', true);

                    }
                })

            }
        });


    }


    var miperfil = {

        guardar: function () {
            if ($("#nombre").val() == '') {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe ingresar el nombre</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }

            $.ajax({
                type: 'POST',
                url: '<?= $ruta ?>usuario/guardarsession',
                data: $("#modal-user-settings-form").serialize(),
                success: function (msj) {

                    console.log(data)
                    if (msj == "no guardo") {

                        var growlType = 'warning';

                        $.bootstrapGrowl('<h4>Ocurrio un error durante el registro</h4>', {
                            type: growlType,
                            delay: 2500,
                            allow_dismiss: true
                        });

                        return false;

                    } else if (msj == "guardo") {
                        var growlType = 'success';

                        $.bootstrapGrowl('<h4>Se han guardado los cambios</h4>', {
                            type: growlType,
                            delay: 2500,
                            allow_dismiss: true
                        });
                        $("#modal-user-settings").modal('hide');

                    } else {
                        var growlType = 'success';

                        $.bootstrapGrowl('<h4>El username ingresado ya existe</h4>', {
                            type: growlType,
                            delay: 2500,
                            allow_dismiss: true
                        });

                    }
                },
                error: function () {
                    alert("error")
                }

            })


        }
    }
    function tipocambiosunat() {
        $.ajax({
            url: '<?= base_url() ?>monedas/get_tipocambio',
            dataType: 'json',
            success: function (data) {
                console.log(data)
                if (data.cambio != null) {
                    $("#comsunat").text(data.cambio.compra);
                    $("#vensunat").text(data.cambio.venta);
                    $("#fechsunat").text(data.cambio.fecha);
                }
            }
        })
    }
    $(document).click(function (e) {
        var target = e.target;
        if ($(target).is('li.alertD>a')) {
            e.preventDefault();
            $('li.alertD>ul').slideToggle(50);
        } else {
            $('li.alertD>ul').slideUp(50);
        }
    })
</script>
