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
<h4 style="text-align: center;">Reporte hoja de colecta</h4>
<h4 style="text-align: center;">Desde <?= date('d/m/Y', strtotime($fecha_ini)) ?> al <?= date('d/m/Y', strtotime($fecha_fin)) ?> 
    Hora: <?= date('H:i:s') ?></h4>

<h5>EMPRESA: <?= valueOption('EMPRESA_NOMBRE') ?></h5>
<h5>DIRECCI&Oacute;N: <?= $local_direccion ?></h5>
<h5>SUCURSAL: <?= $local_nombre ?></h5>
<table>
    <thead>
        <tr>
            <th># Venta</th>
            <th>Fecha</th>
            <th>Local</th>
            <th>Cliente</th>
            <th># Comprobante</th>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Precio unitario</th>
            <th>Importe</th>
        </tr>
    </thead>
    <tbody>
    <?php
        $venta_id_temp = '';
        $suma = $total = 0;
        foreach ($lists as $list):
            if($venta_id_temp!=$list->local_nombre && !empty($venta_id_temp)){
    ?>
        <tr>
            <td style="text-align: right; font-weight: bold;" colspan="8">TOTAL <?= $venta_id_temp ?></td>
            <td style="text-align: right; font-weight: bold;"><?= $list->simbolo ?> <?= number_format($suma, 2) ?></td>
        </tr>
    <?php
                $suma = 0;
            }
    ?>
        <tr>
            <td><?= $list->venta_id ?></td>
            <td><?= date('d/m/Y H:i', strtotime($list->fecha)) ?></td>
            <td><?= $list->local_nombre ?></td>
            <td><?= $list->razon_social ?></td>
            <td><?= $list->abr_doc . ' ' . $list->serie . '-' . sumCod($list->numero, 6) ?></td>
            <td><?= $list->producto_nombre ?></td>
            <td><?= $list->cantidad ?></td>
            <td style="text-align: right;"><?= $list->simbolo ?> <?= number_format($list->precio, 2) ?></td>
            <td style="text-align: right;"><?= $list->simbolo ?> <?= number_format($list->detalle_importe, 2) ?></td>
        </tr>
    <?php
            $suma += $list->detalle_importe;
            $venta_id_temp = $list->local_nombre;
            $total += $list->detalle_importe;
        endforeach;
    ?>
        <tr>
            <td style="text-align: right; font-weight: bold;" colspan="8">TOTAL <?= $list->local_nombre ?></td>
            <td style="text-align: right; font-weight: bold;"><?= !empty($list->simbolo)? $list->simbolo : $md->simbolo ?> <?= number_format($suma, 2) ?></td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <td style="text-align: right; font-weight: bold;" colspan="8">TOTAL GENERAL</td>
            <td style="text-align: right; font-weight: bold;"><?= !empty($list->simbolo)? $list->simbolo : $md->simbolo ?> <?= number_format($total, 2) ?></td>
        </tr>
    </tfoot>
</table>
