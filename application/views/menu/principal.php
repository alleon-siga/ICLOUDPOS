<?php $ruta = base_url(); ?>
<?php $md = get_moneda_defecto() ?>
<div class="content-header content-header-media" style="height: 110px;">
    <div class="header-section">
        <div class="row">
            <!-- Main Title (hidden on small devices for the statistics to fit) -->
            <div class="col-md-4 col-lg-6 hidden-xs hidden-sm">
                <h1>Bienvenido <strong><?= $this->session->userdata('nombre')?></strong><br><small><?= $this->session->userdata('local_nombre')?></small></h1>
            </div>
            <!-- END Main Title -->

            <!-- Top Stats -->
          <div class="col-md-8 col-lg-6">
                <div class="row text-center">
                    <!--  <div class="col-xs-4 col-sm-3">
                        <h2 class="animation-hatch">
                            $<strong>93.7k</strong><br>
                            <small><i class="fa fa-thumbs-o-up"></i> Great</small>
                        </h2>
                    </div>
                    <div class="col-xs-4 col-sm-3">
                        <h2 class="animation-hatch">
                            <strong>167k</strong><br>
                            <small><i class="fa fa-heart-o"></i> Likes</small>
                        </h2>
                    </div>
                    <div class="col-xs-4 col-sm-3">
                        <h2 class="animation-hatch">
                            <strong>101</strong><br>
                            <small><i class="fa fa-calendar-o"></i> Events</small>
                        </h2>
                    </div>
                    <!-- We hide the last stat to fit the other 3 on small devices -->
                    <!--<div class="col-sm-3 hidden-xs">
                        <h2 class="animation-hatch">
                            <strong>27&deg; C</strong><br>
                            <small><i class="fa fa-map-marker"></i> Sydney</small>
                        </h2>
                    </div>-->
                </div>
            </div>
            <!-- END Top Stats -->
        </div>
    </div>
    <!-- For best results use an image with a resolution of 2560x248 pixels (You can also use a blurred image with ratio 10:1 - eg: 1000x100 pixels - it will adjust and look great!) -->
    <!--<img src="<?php //echo $ruta; ?>recursos/img/placeholders/headers/dashboard_header.jpg" alt="header image" class="animation-pulseSlow">-->
</div>
<!-- END Dashboard Header -->
<div class="row">
    <div class="col-md-3">
        <a href="<?=$ruta?>ingresos?costos=true" class="widget widget-hover-effect1 menulink">
            <div class="widget-simple">
                <div class="widget-icon pull-left themed-background-autumn animation-fadeIn">
                    <i class="gi gi-cart_in sidebar-nav-icon" ></i>
                </div>
                <h3 class="widget-content text-right animation-pullDown">
                    Registrar Compras
                </h3>
            </div>
        </a>
        <!-- END Widget -->
    </div>

    <div class="col-md-3">
        <a href="<?=$ruta?>venta_new" class="widget widget-hover-effect1 menulink">
            <div class="widget-simple">
                <div class="widget-icon pull-left themed-background-autumn animation-fadeIn">
                    <i class="fa fa-share sidebar-nav-icon" ></i>
                </div>
                <h3 class="widget-content text-right animation-pullDown">
                    Registrar Ventas
                </h3>
            </div>
        </a>
        <!-- END Widget -->
    </div>

    <div class="col-md-3">
        <a href="<?=$ruta?>reporte/productoVendido" class="widget widget-hover-effect1 menulink">
            <div class="widget-simple">
                <div class="widget-icon pull-left themed-background-autumn animation-fadeIn">
                    <i class="gi gi-list sidebar-nav-icon" ></i>
                </div>
                <h3 class="widget-content text-right animation-pullDown">
                    Productos de alta rotaci&oacute;n
                </h3>
            </div>
        </a>
        <!-- END Widget -->
    </div>

    <div class="col-md-3">
        <a href="<?=$ruta?>reporte/productoVendido" class="widget widget-hover-effect1 menulink">
            <div class="widget-simple">
                <div class="widget-icon pull-left themed-background-autumn animation-fadeIn">
                    <i class="fa fa-history sidebar-nav-icon" ></i>
                </div>
                <h3 class="widget-content text-right animation-pullDown">
                    Productos sin rotaci&oacute;n
                </h3>
            </div>
        </a>
        <!-- END Widget -->
    </div>
</div>
<!-- Mini Top Stats Row -->
<div class="row">
    <?php if($this->usuarios_grupos_model->user_has_perm($this->session->userdata('nUsuCodigo'), 'nuevoproducto')) {?>
    <div class="col-sm-6 col-lg-3">
        <!-- Widget -->
        <a href="<?=$ruta?>producto" class="widget widget-hover-effect1 menulink">
            <div class="widget-simple">
                <div class="widget-icon pull-left themed-background-autumn animation-fadeIn">
                    <i class="fa fa-file-text" ></i>
                </div>
                <h3 class="widget-content text-right animation-pullDown">
                    Nuevo <strong>Producto</strong><br>
                    <small></small>
                </h3>
            </div>
        </a>
        <!-- END Widget -->
    </div>
    <?php } ?>
    <?php if($this->usuarios_grupos_model->user_has_perm($this->session->userdata('nUsuCodigo'), 'ventasdehoy')) {?>
    <div class="col-sm-6 col-lg-3">
        <!-- Widget -->
        <a href="#" class="widget widget-hover-effect1">
            <div class="widget-simple">
                <div class="widget-icon pull-left themed-background-spring animation-fadeIn">
                    <i class="gi gi-cart_out"></i>
                </div>
                <h3 class="widget-content text-right animation-pullDown">
                    + <strong><?= $ventashoy?></strong><br>
                    <small>Ventas de Hoy</small>
                </h3>
            </div>
        </a>
        <!-- END Widget -->
    </div>
    <?php }?>
    <?php if($this->usuarios_grupos_model->user_has_perm($this->session->userdata('nUsuCodigo'), 'comprasdehoy')) {?>
    <div class="col-sm-6 col-lg-3">
        <!-- Widget -->
        <a href="#" class="widget widget-hover-effect1">
            <div class="widget-simple">
                <div class="widget-icon pull-left themed-background-fire animation-fadeIn">
                    <i class="gi gi-cart_in"></i>
                </div>
                <h3 class="widget-content text-right animation-pullDown">
                   + <strong><?=$comprashoy?></strong>
                    <small>Compras de hoy</small>
                </h3>
            </div>
        </a>
        <!-- END Widget -->
    </div>
    <?php } ?>

    <?php if($this->usuarios_grupos_model->user_has_perm($this->session->userdata('nUsuCodigo'), 'ventasdeldia')) {?>
    <div class="col-sm-6 col-lg-3">
        <!-- Widget -->
        <a href="#" class="widget widget-hover-effect1">
            <div class="widget-simple">
                <div class="widget-icon pull-left themed-background-amethyst animation-fadeIn">
                    <i class="gi gi-usd"></i>
                </div>
                <h3 class="widget-content text-right animation-pullDown">
                   Ventas del dia <?= date('d/m/y')?>

                    <small> S/. <?= $ventastotalhoy['suma'] ?>

                    </small>
                </h3>
            </div>
        </a>
        <!-- END Widget -->
    </div>
    <?php } ?>

   <!-- <div class="col-sm-6">

        <a href="page_widgets_stats.php" class="widget widget-hover-effect1">
            <div class="widget-simple">
                <div class="widget-icon pull-left themed-background animation-fadeIn">
                    <i class="gi gi-crown"></i>
                </div>
                <div class="pull-right">

                    <span id="mini-chart-brand"></span>
                </div>
                <h3 class="widget-content animation-pullDown visible-lg">
                    Our <strong>Brand</strong>
                    <small>Popularity over time</small>
                </h3>
            </div>
        </a>

    </div>-->
</div>
<!-- END Mini Top Stats Row -->
<div class="row">
    <?php if ($this->usuarios_grupos_model->user_has_perm($this->session->userdata('nUsuCodigo'), 'reporteVentas')) { ?>
    <div class="col-md-6">
        <div id="containerRv" style="min-width: 310px; height: 400px; margin: 0 auto"></div>        
    </div>    
    <?php } ?>
    
    <?php if ($this->usuarios_grupos_model->user_has_perm($this->session->userdata('nUsuCodigo'), 'reporteCompras')) { ?>
    <div class="col-md-6">
        <div id="containerRc" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
    </div>
    <?php } ?>
</div>
<!-- Load and execute javascript code used only in this page -->
<script src="<?php echo $ruta; ?>recursos/js/pages/index.js"></script>
<script>
$(function(){
    Index.init(); 
        //Grafico de reporte de ventas
        $.post('<?php echo $ruta; ?>principal/reporteVentas', {}, function(result){
            var data_estadistica = eval("("+result+")"); // Obtenemos la informacion del JSON
            var options = {
                chart: {
                    type: 'column'
                },
                title: {
                    text: 'Reporte de ventas semanal'
                },
                colors: ['green'],
                subtitle: {
                    text: ''
                },
                xAxis: {
                    categories: [],
                    crosshair: true
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: '<?= $md->nombre ?> (<?= $md->simbolo ?>)'
                    }
                },
                tooltip: {
                    headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                    pointFormat: '<tr><td style="color:{series.color};padding:0" nowrap>{series.name}: </td>' +
                        '<td style="padding:0" nowrap><b> {point.y:.1f} <?= $md->simbolo ?></b></td></tr>',
                    footerFormat: '</table>',
                    shared: true,
                    useHTML: true
                },
                plotOptions: {
                    column: {
                        pointPadding: 0.2,
                        borderWidth: 0
                    }
                },
                series: [{
                    name: 'Ventas',
                    data: []

                }]
            };

            var arrMes = ['Dic', 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Set', 'Oct', 'Nov'];
            for(var i = 0; i < data_estadistica['venta'].length; i++){
                if(data_estadistica['venta'][i]['2'].length>0){
                    let fechaYhora = data_estadistica['venta'][i]['2'].split(' ');
                    let arrFecha = fechaYhora[0].split('-');
                    let fecha = new Date(arrFecha[0],arrFecha[1],arrFecha[2]);
                    let mes = fecha.getMonth();

                    options.xAxis.categories.push(arrFecha[2] + ' ' + arrMes[mes]);
                    options.series[0].data.push(parseInt(data_estadistica['venta'][i]['1']));
                    //options.series[1].data.push(parseInt(data_estadistica['utilidad'][i]['1']));
                }
            }            
            Highcharts.chart('containerRv', options);
        });
        //Grafico de reporte de compras
        $.post('<?php echo $ruta; ?>principal/reporteCompras', {}, function(result){
            var data_estadistica = eval("("+result+")"); // Obtenemos la informacion del JSON
            var options = {
                chart: {
                    type: 'column'
                },
                title: {
                    text: 'Reporte de compras semanal'
                },
                colors: ['brown'],
                subtitle: {
                    text: ''
                },
                xAxis: {
                    categories: [],
                    crosshair: true
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: '<?= $md->nombre ?> (<?= $md->simbolo ?>)'
                    }
                },
                tooltip: {
                    headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                    pointFormat: '<tr><td style="color:{series.color};padding:0" nowrap>{series.name}: </td>' +
                        '<td style="padding:0" nowrap><b> {point.y:.1f} <?= $md->simbolo ?></b></td></tr>',
                    footerFormat: '</table>',
                    shared: true,
                    useHTML: true
                },
                plotOptions: {
                    column: {
                        pointPadding: 0.2,
                        borderWidth: 0
                    }
                },
                series: [{
                    name: 'Compras',
                    data: []

                }]
            };
            //var arrDia = ['Dom','Lun','Mar','Mie','Jue','Vie','Sab'];
            var arrMes = ['Dic', 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Set', 'Oct', 'Nov'];
            for(var i = 0; i < data_estadistica['ingresos'].length; i++){
                let fechaYhora = data_estadistica['ingresos'][i]['fecha_emision'].split(' ');
                let arrFecha = fechaYhora[0].split('-');
                let fecha = new Date(arrFecha[0],arrFecha[1],arrFecha[2]);
                //let dia = fecha.getDay(); 
                let mes = fecha.getMonth();

                //options.xAxis.categories.push(arrDia[dia] + ' ' + arrFecha[2] + ' ' + arrMes[mes]);
                options.xAxis.categories.push(arrFecha[2] + ' ' + arrMes[mes]);
                options.series[0].data.push(parseInt(data_estadistica['ingresos'][i]['total']));
            }            
            Highcharts.chart('containerRc', options);
        });    
});
</script>

