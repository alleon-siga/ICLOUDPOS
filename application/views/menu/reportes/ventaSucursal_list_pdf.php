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
<h4 style="text-align: center;">Desde <?= date('m/d/Y', strtotime($fecha_ini)) ?>
    al <?= date('m/d/Y', strtotime($fecha_fin)) ?></h4>

<h5>EMPRESA: <?= valueOption('EMPRESA_NOMBRE') ?></h5>
<table>
    <thead>
    <tr>
        <th rowspan="2"><?= getCodigoNombre() ?></th>
        <th rowspan="2">Nombre</th>
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
    <?php foreach ($lists as $list): ?>
        <tr>
            <td><?= getCodigoValue($list['producto_id'], $list['producto_codigo_interno']) ?></td>
            <td><?= $list['producto_nombre'] ?></td>
            <td><?= $list['nombre_unidad']; ?></td>
            <?php for($x=1; $x<=count($locales); $x++){ ?>
            <?php
                $ventas[$x] += $list['cantVend'.$x];
                $stock[$x] += $list['stock'.$x];


                $cantVend = $list['cantVend'.$x];
                $stockAct = $list['stock'.$x];
                $porcAvance = number_format(($stockAct==0)? '0':($cantVend/$stockAct)*100,2);
            ?>
            <td style="text-align: right;"><?= empty($cantVend)? '0': $cantVend; ?></td>
            <td style="text-align: right;"><?= empty($stockAct)? '0': $stockAct; ?></td>
            <td style="text-align: right;"><?= $porcAvance; ?> %</td>
            <?php } ?>
        </tr>
    <?php endforeach ?>
    </tbody>
    <tfoot>
    <tr>
        <td colspan="3">TOTALES</td>
        <?php for($x=1; $x<=count($locales); $x++){ ?>
        <td style="text-align: right;"><?= $ventas[$x] ?></td>
        <td style="text-align: right;"><?= $stock[$x] ?></td>
        <td style="text-align: right;"><?= ($stock[$x]==0)? '0.00' : number_format(($ventas[$x]/$stock[$x])*100,2) ?> %</td>
        <?php } ?>
        <td></td>
    </tr>
    </tfoot>
</table>
