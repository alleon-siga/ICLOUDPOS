<?php $ruta = base_url(); ?>
<?php $md = get_moneda_defecto() ?>
<style>
    table tfoot tr td {
        font-weight: bold;
    }
</style>
<?php
    /*echo "<pre>";
    echo print_r($lists);
    echo "</pre>";*/
    //echo $tipo;
?>

<ul class="nav nav-tabs">
  <li class="active"><a data-toggle="tab" href="#data">Tabla</a></li>
  <!-- <li><a data-toggle="tab" href="#grafico">Gr&aacute;fico</a></li>-->
</ul>
<div class="tab-content">
    <div id="data" class="tab-pane fade in active">
        <div class="table-responsive">
            <table class='table table-striped dataTable table-bordered no-footer tableStyle' style="overflow:scroll; ">
                <thead>
                    <tr>
                        <th rowspan="2" style="vertical-align: middle;"><?= getCodigoNombre() ?></th>
                        <th rowspan="2" style="vertical-align: middle;">Familia</th>
                        <th rowspan="2" style="vertical-align: middle;">Nombre</th>
                        <th rowspan="2" style="vertical-align: middle;">Unidad</th>
                        <th rowspan="2" style="vertical-align: middle;">Marca</th>
                        <th rowspan="2" style="vertical-align: middle;">Linea</th>
                    <?php foreach ($locale as $x): ?>
                        <th rowspan="2" style="vertical-align: middle;"><?= $x['local_nombre']  ?></th>
                    <?php endforeach ?>
                        <th rowspan="2" style="vertical-align: middle;"><?php if($tipo==1){ echo "TOTAL<br>VNTAS"; }else{ echo "TOTAL"; } ?></th>
                        <?php foreach ($locale as $x): ?>
                        <?php if($tipo==1){ ?><th rowspan="2" style="vertical-align: middle;">STOCK<br>ACTUAL</th><?php } ?>
                            <th colspan="<?= count($periodo); ?>"><?= $x['local_nombre']  ?></th>
                        <?php if($tipo==1){ ?><th rowspan="2" style="vertical-align: middle;">VNTA<br>PROMEDIO</th><?php } ?>
                        <?php if($tipo==1){ ?><th rowspan="2" style="vertical-align: middle;">ROTACION</th><?php } ?>
                        <th rowspan="2" style="vertical-align: middle;"><?php if($tipo==1){ echo "TOTAL<br>VNTAS"; }else{ echo "TOTAL"; } ?></th>
                        <?php endforeach ?>
                    </tr>
                    <tr>
                    <?php foreach ($localId as $a){ ?>
                        <?php foreach ($periodo as $x): ?>
                        <th><?= $x  ?></th>
                        <?php endforeach ?>
                    <?php } ?>
                    </tr>
                </thead>
                <tbody>
                <?php
                    $totalLocal = 0;
                ?>
                <?php foreach ($lists as $list): ?>
                    <tr>
                        <td><?= getCodigoValue($list['producto_id'], $list['producto_codigo_interno']) ?></td>
                        <td><?= $list['nombre_familia'] ?></td>
                        <td><?= $list['producto_nombre']; ?></td>
                        <td><?= $list['nombre_unidad']; ?></td>
                        <td><?= $list['nombre_marca']; ?></td>
                        <td><?= $list['nombre_linea']; ?></td>
                    <?php
                        $totalCantV = 0;
                        foreach ($localId as $x){
                            $cantV = $list['cantVend'.$x['int_local_id']];
                            $totalCantV += $cantV;
                    ?>
                        <td style="text-align: right; background-color:#90EE7E;"><?php if($tipo==1){ echo $cantV; }else{ echo $list['simbolo'].' '.number_format($cantV, 2); } ?></td>
                    <?php
                        }
                    ?>
                        <td style="text-align: right; background-color:#90EE7E;"><?php if($tipo==1){ echo $totalCantV; }else{ echo $list['simbolo'].' '.number_format($totalCantV, 2); } ?></td>
                    <?php
                        $colors = array('#2B908F','#F45B5B');
                        $z=0;
                        foreach ($localId as $a){
                            if($z==3) $z=0;
                            $stockA = $list['stock_'.$a['int_local_id']];
                    ?>
                    <?php if($tipo==1){ ?><td style="text-align: right; background-color:<?= $colors[$z] ?>;"><?= $stockA ?></td><?php } ?>
                    <?php
                            $totalV = $vProm = 0;
                            for($x=1; $x<=count($periodo); $x++){
                                $v = $list['periodo'.$x.'_'.$a['int_local_id']];
                                $totalV += $v;
                    ?>
                        <td style="text-align: right; background-color:<?= $colors[$z] ?>;"><?php if($tipo==1){ echo $v; }else{ echo $list['simbolo'].' '.number_format($v, 2); } ?></td>
                    <?php
                            }
                            $vProm = $totalV / count($periodo);
                            if($vProm==0){
                                $rotacion = 0;
                            }else{
                                $rotacion = $stockA / $vProm;    
                            }
                    ?>
                    <?php if($tipo==1){ ?><td style="text-align: right; background-color:<?= $colors[$z] ?>;"><?= number_format($vProm,2) ?></td><?php } ?>
                    <?php if($tipo==1){ ?><td style="text-align: right; background-color:<?= $colors[$z] ?>;"><?= number_format($rotacion,2) ?></td><?php } ?>
                        <td style="text-align: right; background-color:<?= $colors[$z] ?>;"><?php if($tipo==1){ echo $totalV; }else{ echo $list['simbolo'].' '.number_format($totalV, 2); } ?></td>
                    <?php
                            $z++;
                        }
                    ?>
                    </tr>
                <?php endforeach ?>
                </tbody>
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
        TablesDatatables.init(2, 'asc');

        $('#exportar_excel').on('click', function () {
            exportar_excel();
        });

        $("#exportar_pdf").on('click', function () {
            exportar_pdf();
        });      
    });
    function exportar_pdf() {
        var th = [];

        var ini = $('#fecha_ini_value').html();
        var fin = $('#fecha_fin_value').html();

        if (ini != "" && fin != "") {
            if ($('#year').prop('checked')) {
                th = generar_rango_year(ini, fin);
            }
            else if ($('#month').prop('checked')) {
                th = generar_rango_month(ini, fin);
            }
            else if ($('#day').prop('checked')) {
                th = generar_rango_day(ini, fin);
            }
        }else{
            alert("Seleccione un rango de fecha");
            return;
        }

        if (th.length > 0) {
            var data = {
                'producto_id': $("#producto_id").val(),
                'grupo_id': $("#grupo_id").val(),
                'marca_id': $("#marca_id").val(),
                'linea_id': $("#linea_id").val(),
                'familia_id': $("#familia_id").val(),
                'tipo_periodo': $("#tipo_periodo").val(),
                'local_id' : JSON.stringify($("#local_id").val()),
                'rangos': JSON.stringify(th.slice(1)),
                'tipo': $("#tipo").val()
            };

            var win = window.open('<?= base_url()?>reporte/stockVentas/pdf?data=' + JSON.stringify(data), '_blank');
            win.focus();
        } else {
            alert("El rango de fecha no es valido");
            return;
        }
    }

    function exportar_excel() {
        var th = [];

        var ini = $('#fecha_ini_value').html();
        var fin = $('#fecha_fin_value').html();

        if (ini != "" && fin != "") {
            if ($('#year').prop('checked')) {
                th = generar_rango_year(ini, fin);
            }
            else if ($('#month').prop('checked')) {
                th = generar_rango_month(ini, fin);
            }
            else if ($('#day').prop('checked')) {
                th = generar_rango_day(ini, fin);
            }
        }else{
            alert("Seleccione un rango de fecha");
            return;
        }

        if (th.length > 0) {
            var data = {
                'producto_id': $("#producto_id").val(),
                'grupo_id': $("#grupo_id").val(),
                'marca_id': $("#marca_id").val(),
                'linea_id': $("#linea_id").val(),
                'familia_id': $("#familia_id").val(),
                'tipo_periodo': $("#tipo_periodo").val(),
                'local_id' : JSON.stringify($("#local_id").val()),
                'rangos': JSON.stringify(th.slice(1)),
                'tipo': $("#tipo").val()
            };

            var win = window.open('<?= base_url()?>reporte/stockVentas/excel?data=' + JSON.stringify(data), '_blank');
            win.focus();
        } else {
            alert("El rango de fecha no es valido");
            return;
        }
    }
</script>