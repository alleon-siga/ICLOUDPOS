<?php $ruta = base_url(); ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?= $info->nombre_empresa ?></title>
    <meta name="description" content="">

    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1">


    <script src="<?php echo $ruta; ?>recursos/js/vendor/jquery-1.11.1.min.js"></script>

    <meta name="description"
          content="<?= $info->nombre_empresa ?>, sistema de inventario, ventas y gastos web y movil">
    <meta name="author" content="pixelcave">
    <meta name="robots" content="noindex, nofollow">

    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1.0">

    <!-- Icons -->
    <!-- The following icons can be replaced with your own, they are used by desktop and mobile browsers -->
    <link rel="shortcut icon" href="<?php echo $ruta; ?>recursos/img/favicon.ico">
    <link rel="apple-touch-icon" href="<?php echo $ruta; ?>recursos/img/icon57.png" sizes="57x57">
    <link rel="apple-touch-icon" href="<?php echo $ruta; ?>recursos/img/icon72.png" sizes="72x72">
    <link rel="apple-touch-icon" href="<?php echo $ruta; ?>recursos/img/icon76.png" sizes="76x76">
    <link rel="apple-touch-icon" href="<?php echo $ruta; ?>recursos/img/icon114.png" sizes="114x114">
    <link rel="apple-touch-icon" href="<?php echo $ruta; ?>recursos/img/icon120.png" sizes="120x120">
    <link rel="apple-touch-icon" href="<?php echo $ruta; ?>recursos/img/icon144.png" sizes="144x144">
    <link rel="apple-touch-icon" href="<?php echo $ruta; ?>recursos/img/icon152.png" sizes="152x152">
    <!-- END Icons -->

    <!-- Stylesheets -->
    <!-- Bootstrap is included in its original form, unaltered -->
    <link rel="stylesheet" href="<?php echo $ruta; ?>recursos/css/bootstrap.min.css">

    <!-- Related styles of various icon packs and plugins -->
    <link rel="stylesheet" href="<?php echo $ruta; ?>recursos/css/plugins.css">

    <!-- The main stylesheet of this template. All Bootstrap overwrites are defined in here -->
    <link rel="stylesheet" href="<?php echo $ruta; ?>recursos/css/main.css">

    <!-- Include a specific file here from css/themes/ folder to alter the default theme of the template -->

    <!-- The themes stylesheet of this template (for using specific theme color in individual elements - must included last) -->
    <link rel="stylesheet" href="<?php echo $ruta; ?>recursos/css/themes.css">
    <!-- END Stylesheets -->

    <!-- Modernizr (browser feature detection library) & Respond.js (Enable responsive CSS code on browsers that don't support it, eg IE8) -->
    <script src="<?php echo $ruta; ?>recursos/js/vendor/modernizr-2.7.1-respond-1.4.2.min.js"></script>

    <script type="text/javascript">
        $(document).on('submit','form#frmLogin',function(e){
            <?php $mensaje = "<a ></a>";?>
            e.preventDefault();
            $.ajax({
                type: "POST",
                data: $(this).serialize(),
                url: "<?php echo $ruta;?>" + "inicio/validar_login",
                success: function (msj) {
                    if (msj == 'ok') {
                        window.location.href = "<?php echo $ruta;?>" + "principal/";
                    } else {
                        $("#msg").html('<div class="alert alert-warning alert-dismissible fade in" role="alert"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button><strong>Usuario o clave incorrecta, por favor vuelva a intentar</strong></div>')
                        $("#msg").delay("slow").fadeIn().delay(2000).fadeOut();
                    }
                },
                error: function (msj) {
                    $("#msg").html('<div class="alert alert-warning alert-dismissible fade in" role="alert"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button><strong>Usuario o clave incorrecta, por favor vuelva a intentar</strong></div>')
                    $("#msg").delay("slow").fadeIn().delay(2000).fadeOut();
                },
            });
        });
    </script>

    <style type="text/css">
        .nopadding{
            padding: 0px !important;
        }
        .vpadding{
            padding-top: 15px !important;
            padding-bottom: 15px !important;
        }
        .noHorizontalPadding{
            padding-left: 0px;
            padding-right: 0px;
        }
        .nomargin{
            margin: 0px !important;
        }
        .centered{
            margin: 0px auto;
            float: none !important;
            display: inline-block;
        }
        .vertical{
            display: -ms-flexbox;
            display: -webkit-flex;
            display: flex;
            -webkit-flex-direction: column;
            -ms-flex-direction: column;
            flex-direction: column;
            -webkit-align-items: flex-start;
            -ms-flex-align: start;
            align-items: flex-start;
            -webkit-justify-content: center;
            -ms-flex-pack: center;
            justify-content: center;
            position: absolute;
            width: 100%;
            height: 100%;
            left: 0;
            right: 0;
            top: 0;
            bottom: 0;
        }
        body.login_{
            background-color: #FFFFFF;
            overflow-x: hidden;
        }
        body.login_ div.full-height{
            height: 100vh;
            min-height: 470px;
        }
        body.login_ div.full-height.back_img div.lined>span{
            height: 1px;
            background-color: #ffcf00;
        }

        body.login_ div.full-height.darkness{
            background-color: <?= $info->color_fondo ?>;
        }

        body.login_ div.full-height.darkness>div.logo_white{
            padding-top: 10vh;
        }

        body.login_ div.full-height>div.vertical>div.container_form{
            width: 350px;
            padding-top: 30vh;
        }
        body.login_ div.full-height h2{
        color: #FFFFFF;
            font-size: 25px;
        }
        body.login_ div.full-height form div.form-group>label{
            position: relative;
            width: 100%;
            text-align: left;
            color: #FFFFFF;
            font-weight: 100;
        }
        body.login_ div.full-height form div.form-group>div.input-group>div.input-group-addon{
            background-color: #ffffff;
            border-color: transparent;
            box-shadow: none;
        }
        body.login_ div.full-height form div.form-group>div.input-group>div.input-group-addon>i{
            color: #394263;
            font-size: 20px;
        }
        body.login_ div.full-height form div.form-group>div.input-group>input.form-control{
            border-left-color: transparent;
            height: 45px;
        }
        body.login_ div.full-height form div.form-group button{
            display: inline-block;
            position: relative;
            width: 140px;
            text-align: center;
            padding: 10px 0px;
            font-size: 16px;
            font-weight: 500;
            background-color: <?= $info->color_boton ?>;
            border: 1px solid <?= $info->color_boton ?>;
            color: #394263;
            -webkit-transition: all .25s ease;
               -moz-transition: all .25s ease;
                -ms-transition: all .25s ease;
                 -o-transition: all .25s ease;
                    transition: all .25s ease;
        }
        body.login_ div.full-height form div.form-group button:hover{
            background-color: transparent;
            color: <?= $info->color_boton ?>;
        }
        @media (max-width : 400px) {
            body.login_ div.full-height>div.vertical>div.container_form {
                width: 95%;
            }
        }
        .empresa_nombre{
            color: #fff;
            font-size: 2em;
        }
    </style>
</head>
<body class='login_'>
    <div class="container-full">
        <div class="row">
            <div class="col-xs-12 col-md-6 col-lg-4 full-height darkness">
                <div class="col-xs-12 noHorizontalPadding logo_white text-center">
                    <img src="recursos/img/logo/<?= $info->ruta_logo2 ?>" alt="<?= $info->nombre_empresa ?>" class="col-xs-12 noHorizontalPadding centered" style="width: 175px;">
                </div>
                <div class="vertical text-center">
                    <div class="col-xs-12 noHorizontalPadding centered container_form">
                        <div class="col-xs-12 noHorizontalPadding text-center empresa_nombre">
                            <?= valueOptionDB('EMPRESA_NOMBRE',"$info->nombre_empresa") ?>
                        </div>
                        <h2 class="col-xs-12 text-center noHorizontalPadding nomargin vpadding">Ingreso al Área de Cliente</h2>
                        <form class="col-xs-12" id="frmLogin">
                            <div class="form-group col-xs-12 noHorizontalPadding">
                              <label for="user">Correo Electrónico</label>
                              <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-user" aria-hidden="true"></i>
                                </div>
                                <input type="text" class="form-control" id="user" name="user">
                              </div>
                            </div>
                            <div class="form-group col-xs-12 noHorizontalPadding">
                              <label for="pw">Contraseña</label>
                              <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-unlock-alt" aria-hidden="true"></i>
                                </div>
                                <input type="password" class="form-control" id="pw" name="pw">
                              </div>
                            </div>
                            <div class="col-xs-12 noHorizontalPadding vpadding" id="msg"></div>
                            <div class="form-group col-xs-12 noHorizontalPadding text-center">
                                <button type="submit" class="centered">Acceder</button>
                            </div>
                        </form>
                    </div>
                </div>
                
            </div>
            <div class="col-xs-12 col-md-6 col-lg-8 full-height back_img hidden-xs hidden-sm">
                <div class="vertical">
                    <div class="col-xs-12 noHorizontalPadding text-center">
                        <div class="col-xs-12 noHorizontalPadding text-center">
                            <img src="recursos/img/logo/<?= $info->ruta_logo1 ?>" alt="" class="col-xs-12 noHorizontalPadding centered" style="width: 150px;">
                        </div>
                        <div class="col-xs-12 noHorizontalPadding vpadding lined text-center">
                            <span class="col-xs-10 centered"></span>
                        </div>
                        <h1 class="col-xs-12 noHorizontalPadding nomargin">SISTEMA DE ADMINISTRACIÓN INTERNA</h1>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</body>

</html>