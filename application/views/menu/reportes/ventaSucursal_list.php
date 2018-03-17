<?php $ruta = base_url(); ?>
<?php $md = get_moneda_defecto() ?>
<style>
    table tfoot tr td {
        font-weight: bold;
    }
</style>
<ul class="nav nav-tabs">
  <li class="active"><a data-toggle="tab" href="#data">Tabla</a></li>
  <li><a data-toggle="tab" href="#grafico">Gr&aacute;fico</a></li>
</ul>
<div class="tab-content">
    <div id="data" class="tab-pane fade in active">
        <div class="table-responsive">
            <table class='table table-striped dataTable table-bordered no-footer tableStyle' style="overflow:scroll">
                <thead>
                <tr>
                    <th rowspan="2" style="vertical-align: middle;"><?= getCodigoNombre() ?></th>
                    <th rowspan="2" style="vertical-align: middle;">Nombre</th>
                    <th rowspan="2" style="vertical-align: middle;">Unidad</th>
                    <?php foreach ($locales as $local): ?>
                    <th colspan="3"><?= $local['local_nombre'] ?></th>
                    <?php endforeach ?>    
                </tr>
                <tr>
                <?php for($x=1; $x<=count($locales); $x++){ ?>
                    <th>Vendida</th>
                    <th>Stock actual</th>
                    <th>% de avance</th>
                <?php } ?>
                </tr>
                </thead>
                <tbody>
                <?php $ventas = array(); $stock = array(); ?>
                <?php for($x=1; $x<=count($locales); $x++){ ?>
                <?php
                    $ventas[$x] = 0;
                    $stock[$x] = 0;
                ?>
                <?php } ?>
                <?php
                    $cantVend = $atockAct = 0;
                ?>
                <?php $colors = array('#ffcccc','#ffff99', '#ffcc99'); ?>
                <?php foreach ($lists as $list): ?>
                    <?php $z=0; ?>
                    <tr>
                        <td><?= getCodigoValue($list['producto_id'], $list['producto_codigo_interno']) ?></td>
                        <td><?= $list['producto_nombre'] ?></td>
                        <td><?= $list['nombre_unidad']; ?></td>
                        <?php for($x=1; $x<=count($locales); $x++){ ?>
                            <?php if($z==3) $z=0; ?>    
                        <?php
                            $ventas[$x] += $list['cantVend'.$x];
                            $stock[$x] += $list['stock'.$x];

                            $cantVend = $list['cantVend'.$x];
                            $stockAct = $list['stock'.$x];
                            $porcAvance = number_format(($stockAct==0)? '0':($cantVend/$stockAct)*100,2);
                        ?>
                        <td style="text-align: right; background-color:<?= $colors[$z] ?>;"><?= empty($cantVend)? '0': $cantVend; ?></td>
                        <td style="text-align: right; background-color:<?= $colors[$z] ?>;"><?= empty($stockAct)? '0': $stockAct; ?></td>
                        <td style="text-align: right; background-color:<?= $colors[$z] ?>;"><?= $porcAvance; ?> %</td>
                            <?php $z++; ?>
                        <?php } ?>
                    </tr>
                <?php endforeach ?>
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="3">TOTALES</td>
                    <?php $z=0; ?>
                    <?php for($x=1; $x<=count($locales); $x++){ ?>
                    <?php if($z==3) $z=0; ?>  
                    <td style="text-align: right; background-color:<?= $colors[$z] ?> !important;"><?= $ventas[$x] ?></td>
                    <td style="text-align: right; background-color:<?= $colors[$z] ?> !important;"><?= $stock[$x] ?></td>
                    <td style="text-align: right; background-color:<?= $colors[$z] ?> !important;"><?= ($stock[$x]==0)? '0.00' : number_format(($ventas[$x]/$stock[$x])*100,2) ?> %</td>
                    <?php $z++; ?>
                    <?php } ?>
                    <td></td>
                </tr>
                </tfoot>
            </table>
        </div>

        <div class="row">
            <div class="col-md-12">
                <br>
                <button type="button" id="exportar_excel" title="Exportar Excel" class="btn btn-primary">
                    <i class="fa fa-file-excel-o fa-fw"></i>
                </button>
                <button type="button" id="exportar_pdf" title="Exportar Pdf" class="btn btn-primary">
                    <i class="fa fa-file-pdf-o fa-fw"></i>
                </button>
            </div>
        </div>
    </div>
    <div id="grafico" class="tab-pane fade" style="min-width: 310px; max-width: 800px; height: 400px; margin: 0 auto">

    </div>
</div>
<script type="text/javascript">
     $(function () {
        $('#exportar_excel').on('click', function () {
            exportar_excel();
        });

        $("#exportar_pdf").on('click', function () {
            exportar_pdf();
        });
        $('.nav-tabs a[href="#grafico"]').on('shown.bs.tab', function(event){
            var data = {
                'local_id': $("#local_id").val(),
                'fecha': $("#fecha").val(),
                'producto_id': $("#producto_id").val(),
                'grupo_id': $("#grupo_id").val(),
                'marca_id': $("#marca_id").val(),
                'linea_id': $("#linea_id").val(),
                'familia_id': $("#familia_id").val()
            };

            $.post("<?= base_url()?>reporte/ventaSucursal/grafico/", data, function(respuesta){
                //Usar primero esto
                var data_estadistica = eval("("+respuesta+")"); // Obtenemos la informacion del JSON
                var options = {
                    chart: {
                        type: 'bar'
                    },
                    title: {
                        text: "Ventas por sucursal en unidades"
                    },
                    subtitle: {
                        text: ''
                    },
                    xAxis: {
                        categories: [],
                        title: {
                            text: ""
                        }
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: 'Unidades',
                            align: 'high'
                        },
                        labels: {
                            overflow: 'justify'
                        }
                    },
                    tooltip: {
                        valueSuffix: ' unidades'
                    },
                    plotOptions: {
                        bar: {
                            dataLabels: {
                                enabled: true
                            }
                        }
                    },
                    legend: {
                        layout: 'vertical',
                        align: 'right',
                        verticalAlign: 'top',
                        x: -40,
                        y: 80,
                        floating: true,
                        borderWidth: 1,
                        backgroundColor: ((Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'),
                        shadow: true
                    },
                    credits: {
                        enabled: false
                    },
                    series: [{
                        name: 'Cantidad vendida',
                        data: []
                    }, {
                        name: 'Stock actual',
                        data: []
                    }]
                };

                var vendida = new Array();
                var stock = new Array();

                for(var i = 0; i < data_estadistica['locales'].length; i++){
                    options.xAxis.categories.push(data_estadistica['locales'][i]['local_nombre']);

                    vendida[i] = 0;
                    stock[i] = 0;

                    for(var x = 0; x < data_estadistica['lists'].length; x++){
                        vendida[i] += parseInt(data_estadistica['lists'][x]['cantVend' + (i+1)]);
                        stock[i] += parseInt(data_estadistica['lists'][x]['stock' + (i+1)]);
                    }

                    options.series[0].data.push(parseInt(vendida[i]));
                    options.series[1].data.push(parseInt(stock[i]));
                }

                Highcharts.chart('grafico', options);
            });
        });        
    });

    function exportar_pdf() {
        var data = {
            'fecha': $("#fecha").val(),
            'producto_id': $("#producto_id").val(),
            'grupo_id': $("#grupo_id").val(),
            'marca_id': $("#marca_id").val(),
            'linea_id': $("#linea_id").val(),
            'familia_id': $("#familia_id").val()
        };

        var win = window.open('<?= base_url()?>reporte/ventaSucursal/pdf?data=' + JSON.stringify(data), '_blank');
        win.focus();
    }

    function exportar_excel() {
        var data = {
            'fecha': $("#fecha").val(),
            'producto_id': $("#producto_id").val(),
            'grupo_id': $("#grupo_id").val(),
            'marca_id': $("#marca_id").val(),
            'linea_id': $("#linea_id").val(),
            'familia_id': $("#familia_id").val()
        };

        var win = window.open('<?= base_url()?>reporte/ventaSucursal/excel?data=' + JSON.stringify(data), '_blank');
        win.focus();
    }
</script>