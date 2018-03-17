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
                    <th>Id</th>
                    <th>Nombre</th>
                    <?php if(isset($lists[0]->tipo)){ ?>
                    <th><?= ($lists[0]->tipo=='1')? 'Cantidad': 'Total' ?></th>
                    <?php } ?>
                    <th>Anulado</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $cant = 0;
                $total = 0;
                $anulado = 0;
                ?>
                <?php foreach ($lists as $list): ?>
                    <?php
                    $cant += $list->cantidad;
                    $total += $list->total;
                    $anulado += $list->anulado;
                    ?>
                    <tr>
                        <td><?= $list->id_vendedor ?></td>
                        <td><?= $list->nombre ?></td>
                        <?php if(isset($lists[0]->tipo)){ ?>
                            <?php if($list->tipo=='1'){ ?>
                                <td><?= $list->cantidad ?></td>
                            <?php }elseif($list->tipo=='2') { ?>
                                <td><?= $moneda->simbolo . ' ' . number_format($list->total,2) ?></td>
                            <?php } ?>
                        <?php } ?>
                        <td><?= $list->anulado ?></td>
                    </tr>
                <?php endforeach ?>
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="2">TOTALES</td>
                    <?php if(isset($lists[0]->tipo)){ ?>
                        <?php if($lists[0]->tipo=='1'){ ?>
                            <td><?= $cant ?></td>   
                        <?php }elseif($lists[0]->tipo=='2'){ ?>
                            <td><?= $moneda->simbolo . ' ' . number_format($total, 2) ?></td>
                        <?php } ?>
                    <?php } ?>
                    <td><?= $anulado ?></td>
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
                'moneda_id': $("#moneda_id").val(),
                'producto_id': $("#producto_id").val(),
                'grupo_id': $("#grupo_id").val(),
                'marca_id': $("#marca_id").val(),
                'linea_id': $("#linea_id").val(),
                'familia_id': $("#familia_id").val(),
                'tipo': $("#tipo").val(),
            };

            $.post("<?= base_url()?>reporte/ventaEmpleado/grafico/", data, function(respuesta){
                //Usar primero esto
                var data_estadistica = eval("("+respuesta+")"); // Obtenemos la informacion del JSON
                var options = {
                    chart: {
                        type: 'bar'
                    },
                    title: {
                        text: "Ventas por empleado"
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
                            text: '',
                            align: 'high'
                        },
                        labels: {
                            overflow: 'justify'
                        }
                    },
                    tooltip: {
                        valueSuffix: ''
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
                        name: '',
                        data: []
                    }, {
                        name: 'Anulado',
                        data: []
                    }]
                };

                for(var i = 0; i < data_estadistica['lists'].length; i++){
                    options.xAxis.categories.push(data_estadistica['lists'][i]['nombre']);
                    if(parseInt(data_estadistica['lists'][i]['tipo'])=='1'){ //cantidad
                        options.series[0].name = 'Cantidad';
                        options.yAxis.title.text = 'Unidades';
                        options.tooltip.valueSuffix = ' unidades';
                        options.series[0].data.push(parseInt(data_estadistica['lists'][i]['cantidad']));
                    }else{
                        options.series[0].name = 'Importe';
                        options.series[0].data.push(parseInt(data_estadistica['lists'][i]['total']));
                        options.yAxis.title.text = 'Importes';
                        options.tooltip.valueSuffix = ' importes';
                    }
                    options.series[1].data.push(parseInt(data_estadistica['lists'][i]['anulado']));
                }

                Highcharts.chart('grafico', options);
            });
        });
    });

    function exportar_pdf() {
        var data = {
            'local_id': $("#local_id").val(),
            'fecha': $("#fecha").val(),
            'moneda_id': $("#moneda_id").val(),
            'producto_id': $("#producto_id").val(),
            'grupo_id': $("#grupo_id").val(),
            'marca_id': $("#marca_id").val(),
            'linea_id': $("#linea_id").val(),
            'familia_id': $("#familia_id").val(),
            'tipo': $("#tipo").val()
        };

        var win = window.open('<?= base_url()?>reporte/ventaEmpleado/pdf?data=' + JSON.stringify(data), '_blank');
        win.focus();
    }

    function exportar_excel() {
        var data = {
            'local_id': $("#local_id").val(),
            'fecha': $("#fecha").val(),
            'moneda_id': $("#moneda_id").val(),
            'producto_id': $("#producto_id").val(),
            'grupo_id': $("#grupo_id").val(),
            'marca_id': $("#marca_id").val(),
            'linea_id': $("#linea_id").val(),
            'familia_id': $("#familia_id").val(),
            'tipo': $("#tipo").val()
        };

        var win = window.open('<?= base_url()?>reporte/ventaEmpleado/excel?data=' + JSON.stringify(data), '_blank');
        win.focus();
    }
</script>