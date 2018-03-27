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
<h4 style="text-align: center;">Reporte de ventas por sucursal</h4>
<h4 style="text-align: center;">Desde <?= date('d/m/Y', strtotime($fecha_ini)) ?>
    al <?= date('d/m/Y', strtotime($fecha_fin)) ?></h4>

<h5>EMPRESA: <?= valueOption('EMPRESA_NOMBRE') ?></h5>
<table>
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
            <th>Importe total</th>
        <?php } ?>
        </tr>
    </thead>
    <?php $ventas = array(); $stock = array(); ?>
    <?php for($x=1; $x<=count($locales); $x++){ ?>
    <?php
        $ventas[$x] = 0;
        $stock[$x] = 0;
        $total[$x] = 0;
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
                $total[$x] += $list['total'.$x];

                $cantVend = $list['cantVend'.$x];
                $stockAct = $list['stock'.$x];
                $importe = $list['total'.$x];
                //$porcAvance = number_format(($stockAct==0)? '0':($cantVend/$stockAct)*100,2);
                //($stock[$x]==0)? '0.00' : number_format(($ventas[$x]/$stock[$x])*100,2)
            ?>
            <td style="text-align: right; background-color:<?= $colors[$z] ?>;"><?= empty($cantVend)? '0': $cantVend; ?></td>
            <td style="text-align: right; background-color:<?= $colors[$z] ?>;"><?= empty($stockAct)? '0': $stockAct; ?></td>
            <td style="text-align: right; background-color:<?= $colors[$z] ?>;"><?= $moneda->simbolo . ' ' . number_format($importe, 2); ?></td>
                <?php $z++; ?>
            <?php } ?>
        </tr>
    <?php endforeach ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3" style="font-size: 9px; font-weight: bold;">Totales</td>
            <?php $z=0; ?>
            <?php for($x=1; $x<=count($locales); $x++){ ?>
                <?php if($z==3) $z=0; ?>  
            <td style="text-align: right; background-color:<?= $colors[$z] ?> !important; font-size: 9px; font-weight: bold;"><?= $ventas[$x] ?></td>
            <td style="text-align: right; background-color:<?= $colors[$z] ?> !important; font-size: 9px; font-weight: bold;"><?= $stock[$x] ?></td>
            <td style="text-align: right; background-color:<?= $colors[$z] ?> !important; font-size: 9px; font-weight: bold;"><?= $moneda->simbolo . ' ' . number_format($total[$x], 2) ?></td>
            <?php $z++; ?>
            <?php } ?>
        </tr>
    </tfoot>
</table>
