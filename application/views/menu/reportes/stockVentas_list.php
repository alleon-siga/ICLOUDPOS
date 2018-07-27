<?php $ruta = base_url(); ?>
<style>
    table tfoot tr td {
        font-weight: bold;
    }
</style>
<?
    /*echo "<pre>";
    echo print_r($lists);
    echo "</pre>";*/
?>
<div class="table-responsive">
    <table class='table dataTable table-bordered no-footer tableStyle' style="overflow:scroll;">
        <thead>
            <tr>
                <th width="5%"><?= getCodigoNombre() ?></th>
                <th width="5%">Familia</th>
                <th width="5%">Nombre</th>
                <th width="5%">Unidad</th>
                <th width="5%">Marca</th>
                <th width="5%" >Linea</th>
        <?php foreach ($locale as $local): ?>
                <th width="5%"><?= ucfirst(strtolower($local['local_nombre'])); ?></th>
        <?php endforeach ?>
                <th width="5%"><?= ($tipo==1)? "Total<br>Vntas" : "Total"; ?></th>
        <?php foreach ($locale as $local): ?>
            <?php if($tipo==1){ ?>
                <th width="5%">Stock<br>Actual</th>
            <?php } ?>
                <th width="5%" colspan="<?= count($rangos); ?>"><?= ucfirst(strtolower($local['local_nombre']))  ?></th>
            <?php if($tipo==1){ ?>
                <th width="5%">Vnta<br>Promedio</th>
                <th width="5%">Rotacion</th>
            <?php } ?>
                <th width="5%"><?= ($tipo==1)? "Total<br>Vntas" : "Total"; ?></th>
        <?php endforeach ?>
            </tr>
        </thead>
        <tbody>
    <?php foreach ($lists as $list): ?>
            <tr>
                <td style="white-space: normal;"><?= getCodigoValue($list['producto_id'], $list['producto_codigo_interno']) ?></td>
                <td style="white-space: normal;"><?= $list['nombre_familia'] ?></td>
                <td style="white-space: normal;"><?= $list['producto_nombre']; ?></td>
                <td style="white-space: normal;"><?= $list['nombre_unidad']; ?></td>
                <td style="white-space: normal;"><?= $list['nombre_marca']; ?></td>
                <td style="white-space: normal;"><?= $list['nombre_linea']; ?></td>
        <?php
            $totalCantV = $totalxLocal = 0;
            $moneda = '';
            foreach ($locale as $local):
                if(!empty($list['detalle'])){
                    if(isset($list['detalle']['local'][$local['int_local_id']])){
                        $totalxLocal = $list['detalle']['local'][$local['int_local_id']];
                    }else{
                        $totalxLocal = '0';
                    }
                    $totalCantV += $totalxLocal;
                    $moneda = '';
                    if(isset($list['detalle']['moneda'])){
                        $moneda = $list['detalle']['moneda'];
                    }
                }
        ?>
                <td style="white-space: normal; text-align: right; background-color:#90EE7E;"><?= ($tipo==1)? $totalxLocal : $moneda.' '.number_format($totalxLocal, 2); ?></td>
        <?php endforeach; ?>
                <td style="white-space: normal; text-align: right; background-color:#90EE7E;"><?= ($tipo==1)? $totalCantV : $moneda.' '.number_format($totalCantV, 2); ?></td>
        <?php 
            $colors = array('#2B908F','#F45B5B');
            $z=0;
        ?>
        <?php foreach ($locale as $local): ?>
            <?php if($z==2) $z=0; ?>
            <?php if($tipo==1){ ?>
                <td style="white-space: normal; text-align: right; background-color:<?= $colors[$z] ?>;"><?= number_format($list['detalle']['stock'][$local['int_local_id'].'_'.$list['producto_id']],0); ?></td>
            <?php } ?>
            <?php
                $LocalyFecha = $totalxLocalyFecha = 0;
                $moneda = '';
                foreach ($rangos as $rango => $id):
                    //echo $id;
                    $idlocal = $local['int_local_id'];
                    if($tipo_periodo=='1'){
                        $fecha = date('Y-m-d', strtotime(str_replace("/", "-", $id)));
                    }elseif($tipo_periodo=='2'){
                        $parte = explode("/", $id);
                        $fecha = $parte[1].'-'.$parte[0];
                    }else{
                        $fecha = $id;
                    }

                    if(!empty($list['detalle'])){
                        if(isset($list['detalle']['fecha'][$idlocal.'_'.$fecha])){
                            $LocalyFecha = $list['detalle']['fecha'][$idlocal.'_'.$fecha];
                        }else{
                            $LocalyFecha = '0';
                        }

                        $moneda = '';
                        if(isset($list['detalle']['moneda'])){
                            $moneda = $list['detalle']['moneda'];
                        }
                        
                        $totalxLocalyFecha += $LocalyFecha;
                    }
            ?>
                <td style="white-space: normal; text-align: right; background-color:<?= $colors[$z] ?>;"><?= ($tipo==1)? $LocalyFecha : $moneda.' '.number_format($LocalyFecha, 2); ?></td>
            <?php endforeach ?>
            <?php 
                if($tipo==1){
                    $vProm = $totalxLocalyFecha / count($rangos);
                    if($vProm==0){
                        $rotacion = 0;
                    }else{
                        $rotacion = ($list['detalle']['stock'][$local['int_local_id'].'_'.$list['producto_id']]) / $vProm; //stock / venta promedio
                    }
            ?>
                <td style="white-space: normal; text-align: right; background-color:<?= $colors[$z] ?>;"><?= number_format($vProm, 2) ?></td>
                <td style="white-space: normal; text-align: right; background-color:<?= $colors[$z] ?>;"><?= number_format($rotacion, 2) ?></td>
            <?php } ?>
                <td style="white-space: normal; text-align: right; background-color:<?= $colors[$z] ?>;"><?= ($tipo==1)? $totalxLocalyFecha : $moneda.' '.number_format($totalxLocalyFecha, 2); ?></td>
        <?php $z++; ?>
        <?php endforeach ?>
            </tr>
    <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td style="white-space: normal;"></td>
                <td style="white-space: normal;"></td>
                <td style="white-space: normal;"></td>
                <td style="white-space: normal;"></td>
                <td style="white-space: normal;"></td>
                <td style="white-space: normal;"></td>
        <?php foreach ($locale as $local): ?>
                <td style="white-space: normal;"></td>
        <?php endforeach ?>
                <td style="white-space: normal;"></td>
        <?php foreach ($locale as $local): ?>
            <?php if($tipo==1){ ?>
                <td style="white-space: normal;"></td>
            <?php } ?>
            <?php foreach ($rangos as $rango): ?>
                <td style="white-space: normal; text-align: center; color:blue !important; font-size: 10px !important;"><?= $rango; ?></td>
            <?php endforeach ?>
        <?php if($tipo==1){ ?>
                <td style="white-space: normal;"></td>
                <td style="white-space: normal;"></td>
        <?php } ?>
                <td style="white-space: normal;"></td>
        <?php endforeach ?>
            </tr>
        </tfoot>
    </table>
</div>
<br>
<script type="text/javascript">
     $(document).ready(function () {
        //TablesDatatables.init(0);

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