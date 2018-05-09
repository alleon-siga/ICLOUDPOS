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
<h4 style="text-align: center;">Reporte de utilidades por producto</h4>
<h4 style="text-align: center;">
<?php if(isset($fecha_ini) && isset($fecha_fin)): ?>    
    Desde <?= date('d/m/Y', strtotime($fecha_ini)) ?> al <?= date('d/m/Y', strtotime($fecha_fin)) ?> 
    Hora: <?= date('H:i:s') ?>
<?php endif; ?>
</h4>

<h5>EMPRESA: <?= valueOption('EMPRESA_NOMBRE') ?></h5>
<h5>DIRECCI&Oacute;N: <?= $local_direccion ?></h5>
<h5>SUCURSAL: <?= $local_nombre ?></h5>
<table>
    <thead>
        <tr>
            <th># Venta </th>
            <th>Local</th>
            <th>Fecha</th>
            <th>Proveedor</th>
            <th>Producto</th>
            <th>Unidad</th>
            <th>Cantidad</th>
            <th>Compra</th>
            <th>Total 1</th>
            <th>Venta</th>
            <th>Total 2</th>
            <th>Utilidad</th>
        </tr>
    </thead>
    <tbody>
    <?php
        $total1 = $total2 = $Utilidad = $sumTotal1 = $sumTotal2 = $sumUtilidad = 0;
        foreach ($lists as $ingreso):
            $total1 = $ingreso->cantidad * $ingreso->detalle_costo_promedio;
            $total2 = $ingreso->cantidad * $ingreso->precio;
            $Utilidad = $total2 - $total1;
    ?>
        <tr>
            <td><?= $ingreso->venta_id ?></td>
            <td><?= $ingreso->local_nombre ?></td>
            <td><?= $ingreso->fecha ?></td>
            <td><?= $ingreso->proveedor_nombre ?></td>
            <td><?= $ingreso->producto_nombre ?></td>
            <td><?= $ingreso->nombre_unidad ?></td>
            <td><?= $ingreso->cantidad ?></td>
            <td><?= $ingreso->detalle_costo_promedio ?></td>
            <td><?= number_format($total1, 2) ?></td>
            <td><?= $ingreso->precio ?></td>
            <td><?= number_format($total2, 2) ?></td>
            <td><?= $Utilidad ?></td>
        </tr>
    <?php
        $sumTotal1 += $total1;
        $sumTotal2 += $total2;
        $sumUtilidad += $Utilidad;
        endforeach;
    ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="8" align="right" style="font-weight: bold;">Total:</td>
            <td><?= number_format($sumTotal1, 2) ?></td>
            <td></td>
            <td><?= number_format($sumTotal2, 2) ?></td>
            <td><?= number_format($sumUtilidad, 2) ?></td>
        </tr>
    </tfoot>
</table>
