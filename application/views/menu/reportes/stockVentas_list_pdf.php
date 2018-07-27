<?php $ruta = base_url(); ?>
<?php $md = get_moneda_defecto() ?>
<?
    /*echo "<pre>";
    echo print_r($rangos);
    echo "</pre>";*/
?>
<style type="text/css">
    table td {
        width: 100%;
        border: #e1e1e1 1px solid;
        font-size: 9px;
    }

    thead, th {
        background: #585858;
        border: #111 1px solid;
        color: #fff;
        font-size: 10px;
    }

    h4, h5 {
        margin: 0px;
    }

    table tfoot tr td {
        font-weight: bold;
    }
</style>
<h4 style="text-align: center;">Reporte de Stock y Ventas</h4>
<h4 style="text-align: center;">Desde <?= date('d/m/Y', strtotime($fecha_ini)) ?>
    al <?= date('d/m/Y', strtotime($fecha_fin)) ?></h4>

<h5>EMPRESA: <?= valueOption('EMPRESA_NOMBRE') ?></h5>
<table>
    <thead>
        <tr>
            <th rowspan="2"><?= getCodigoNombre() ?></th>
            <th rowspan="2">Familia</th>
            <th rowspan="2">Nombre</th>
            <th rowspan="2">Unidad</th>
            <th rowspan="2">Marca</th>
            <th rowspan="2">Linea</th>
    <?php foreach ($locale as $local): ?>
            <th rowspan="2"><?= ucfirst(strtolower($local['local_nombre'])); ?></th>
    <?php endforeach ?>
            <th rowspan="2"><?= ($tipo==1)? "Total<br>Vntas" : "Total"; ?></th>
    <?php foreach ($locale as $local): ?>
        <?php if($tipo==1){ ?>
            <th rowspan="2">Stock<br>Actual</th>
        <?php } ?>
            <th  colspan="<?= count($rangos); ?>"><?= ucfirst(strtolower($local['local_nombre']))  ?></th>
        <?php if($tipo==1){ ?>
            <th rowspan="2">Vnta<br>Promedio</th>
            <th rowspan="2">Rotacion</th>
        <?php } ?>
            <th rowspan="2"><?= ($tipo==1)? "Total<br>Vntas" : "Total"; ?></th>
    <?php endforeach ?>
        </tr>
        <tr>
        <?php foreach ($locale as $local): ?>
            <?php foreach ($rangos as $rango): ?>
            <th><?= $rango  ?></th>
            <?php endforeach ?>
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
</table>
