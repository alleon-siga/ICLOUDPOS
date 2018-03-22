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
<h4 style="text-align: center;">Desde <?= date('m/d/Y', strtotime($fecha_ini)) ?>
    al <?= date('m/d/Y', strtotime($fecha_fin)) ?></h4>

<h5>EMPRESA: <?= valueOption('EMPRESA_NOMBRE') ?></h5>
<table>
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
            <th rowspan="2" style="vertical-align: middle;">Total</th>
            <?php foreach ($locale as $x): ?>
            <th colspan="<?= count($periodo); ?>"><?= $x['local_nombre']  ?></th>
            <th rowspan="2" style="vertical-align: middle;">Total</th>
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
            <td style="text-align: right;"><?= $cantV; ?></td>
        <?php
            }
        ?>
            <td style="text-align: right;"><?= $totalCantV; ?></td>
        <?php
            foreach ($localId as $a){
                $totalV = 0;
                for($x=1; $x<=count($periodo); $x++){
                    $v = $list['periodo'.$x.'_'.$a['int_local_id']];
                    $totalV += $v;
        ?>
            <td style="text-align: right;"><?= $v; ?></td>
        <?php
                }
        ?>
            <td style="text-align: right;"><?= $totalV; ?></td>
        <?php
            }
        ?>
        </tr>
    <?php endforeach ?>
    </tbody>
</table>
