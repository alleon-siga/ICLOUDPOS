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
<h4 style="text-align: center;">Reporte de ventas por empleado</h4>
<h4 style="text-align: center;">Desde <?= date('m/d/Y', strtotime($fecha_ini)) ?>
    al <?= date('m/d/Y', strtotime($fecha_fin)) ?></h4>

<h5>EMPRESA: <?= valueOption('EMPRESA_NOMBRE') ?></h5>
<h5>DIRECCI&Oacute;N: <?= $local_direccion ?></h5>
<h5>SUCURSAL: <?= $local_nombre ?></h5>
<table>
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
        <td></td>
        <td>TOTALES</td>
        <?php if(isset($lists[0]->tipo)){ ?>
            <?php if($lists[0]->tipo=='1'){ ?>
                <td><?= $cant ?></td>
            <?php }elseif($lists[0]->tipo=='2') { ?>
                <td><?= $moneda->simbolo . ' ' . number_format($total, 2) ?></td>
            <?php } ?>
        <?php } ?>
        <td><?= $anulado ?></td>
    </tr>
    </tfoot>
</table>
