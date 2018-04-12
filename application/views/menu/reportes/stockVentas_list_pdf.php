<?php $ruta = base_url(); ?>
<?php $md = get_moneda_defecto() ?>
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
