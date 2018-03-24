<?php
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=stock_ventas.xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<h4 style="text-align: center; margin: 0;">Reporte de Stock y Ventas</h4>
<h4 style="text-align: center; margin: 0;">Desde <?= date('d/m/Y', strtotime($fecha_ini)) ?>
    al <?= date('d/m/Y', strtotime($fecha_fin)) ?></h4>

<h5 style="margin: 0;">EMPRESA: <?= valueOption('EMPRESA_NOMBRE') ?></h5>
<table border="1">
    <thead>
        <tr>
            <th rowspan="2" style="vertical-align: middle;"><?= getCodigoNombre() ?></th>
            <th rowspan="2" style="vertical-align: middle;">Familia</th>
            <th rowspan="2" style="vertical-align: middle;">Nombre</th>
            <th rowspan="2" style="vertical-align: middle;">Marca</th>
            <th rowspan="2" style="vertical-align: middle;">Linea</th>
        <?php foreach ($locale as $x): ?>
            <th rowspan="2" style="vertical-align: middle;"><?= $x['local_nombre']  ?></th>
        <?php endforeach ?>
            <th rowspan="2" style="vertical-align: middle;"><?php if($tipo==1){ echo "Cantidad"; }else{ echo "Total"; } ?></th>
            <?php foreach ($locale as $x): ?>
            <th colspan="<?= count($periodo); ?>"><?= $x['local_nombre']  ?></th>
            <th rowspan="2" style="vertical-align: middle;"><?php if($tipo==1){ echo "Cantidad"; }else{ echo "Total"; } ?></th>
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
            <td><?= $list['nombre_marca']; ?></td>
            <td><?= $list['nombre_linea']; ?></td>
        <?php
            $totalCantV = 0;
            foreach ($localId as $x){
                $cantV = $list['cantVend'.$x['int_local_id']];
                $totalCantV += $cantV;
        ?>
            <td style="text-align: right;"><?php if($tipo==1){ echo $cantV; }else{ echo number_format($cantV, 2); } ?></td>
        <?php
            }
        ?>
            <td style="text-align: right;"><?php if($tipo==1){ echo $totalCantV; }else{ echo number_format($totalCantV, 2); } ?></td>
        <?php
            foreach ($localId as $a){
                $totalV = 0;
                for($x=1; $x<=count($periodo); $x++){
                    $v = $list['periodo'.$x.'_'.$a['int_local_id']];
                    $totalV += $v;
        ?>
            <td style="text-align: right;"><?php if($tipo==1){ echo $v; }else{ echo number_format($v, 2); } ?></td>
        <?php
                }
        ?>
            <td style="text-align: right;"><?php if($tipo==1){ echo $totalV; }else{ echo number_format($totalV, 2); } ?></td>
        <?php
            }
        ?>
        </tr>
    <?php endforeach ?>
    </tbody>
</table>
