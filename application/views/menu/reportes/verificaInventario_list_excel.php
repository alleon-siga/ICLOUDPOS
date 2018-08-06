<?php
    header("Content-type: application/octet-stream");
    header("Content-Disposition: attachment; filename=credito_fiscal.xls");
    header("Pragma: no-cache");
    header("Expires: 0");
?>
<?php $md = get_moneda_defecto() ?>
<h4 style="text-align: center; margin: 0;">Reporte de Cr&eacute;dito Fiscal</h4>
<?php if(isset($fecha_ini) && isset($fecha_fin)): ?>
<h4 style="text-align: center; margin: 0;">
    Desde <?= date('d/m/Y', strtotime($fecha_ini)) ?> al <?= date('d/m/Y', strtotime($fecha_fin)) ?>
    Hora: <?= date('H:i:s') ?>
</h4>
<?php endif; ?>
<h5 style="margin: 0;">EMPRESA: <?= valueOption('EMPRESA_NOMBRE') ?></h5>
<h5 style="margin: 0;">DIRECCI&Oacute;N: <?= $local_direccion ?></h5>
<h5 style="margin: 0;">SUCURSAL: <?= $local_nombre ?></h5>
<table border="1">
    <thead>
        <tr>
            <th># Venta</th>
            <th>Local</th>
            <th>Fecha Registro</th>
            <th>Fecha Venta</th>
            <th># Comprobante</th>
            <th>Cliente</th>
            <th>Condici&oacute;n</th>
            <th>Subtotal</th>
            <th>Impuesto</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
<?php
    $totalSubTotal = $totalImpuestoV = $totalVentaTotal = 0;
    foreach ($lists as $ingreso):
        $cantidad = $ingreso->cantidad;
        $impuesto = (($ingreso->impuesto_porciento / 100) + 1);
        if($ingreso->tipo_impuesto=='1'){ //incluye impuesto
            $precioVenta = $ingreso->detalle_importe; //precio de venta
            $costoVentaSi = ($precioVenta / $cantidad) / $impuesto; //Costo de venta unitario sin impuesto
            $costoVenta = $costoVentaSi * $impuesto; //Costo de venta unitario con impuesto
        }elseif($ingreso->tipo_impuesto=='2'){ //agregar impuesto
            $precioVenta = $ingreso->detalle_importe * $impuesto;
            $costoVentaSi = ($precioVenta / $cantidad) / $impuesto; //Costo de venta unitario sin impuesto
            $costoVenta = $costoVentaSi * $impuesto; //Costo de venta unitario con impuesto
        }else{ //no incluye impuesto
            $precioVenta = $ingreso->detalle_importe; //precio de venta
            $costoVentaSi = ($precioVenta / $cantidad);
            $costoVenta = $costoVentaSi;
        }
        $subtotal = $cantidad * $costoVentaSi;
        $impVenta = $precioVenta - $subtotal;
        $totalSubTotal += $subtotal;
        $totalImpuestoV += $impVenta;
        $totalVentaTotal += $precioVenta;
?>
        <tr>
            <td style="text-align: right;"><?= $ingreso->venta_id ?></td>
            <td><?= $ingreso->local_nombre ?></td>
            <td><?= date('d/m/Y', strtotime($ingreso->created_at)) ?></td>
            <td><?= date('d/m/Y', strtotime($ingreso->fecha)) ?></td>
            <td><?= $ingreso->abr_doc. ' ' . $ingreso->serie . '-' . sumCod($ingreso->numero, 6) ?></td>
            <td><?= $ingreso->razon_social ?></td>
            <td><?= utf8_decode($ingreso->nombre_condiciones) ?></td>
            <td style="text-align: right;"><?= $ingreso->simbolo.' '.number_format($subtotal, 2) ?></td>
            <td style="text-align: right;"><?= $ingreso->simbolo.' '.number_format($impVenta, 2) ?></td>
            <td style="text-align: right;"><?= $ingreso->simbolo.' '.number_format($precioVenta, 2) ?></td>
        </tr>
<?php
    endforeach;
?>
    </tbody>
<?php if(count($lists)>0){ ?>
    <tfoot>
        <tr>
            <td colspan="7"></td>
            <td style="text-align: right;"><?= $lists[0]->simbolo.' '.number_format($totalSubTotal, 2) ?></td>
            <td style="text-align: right;"><?= $lists[0]->simbolo.' '.number_format($totalImpuestoV, 2) ?></td>
            <td style="text-align: right;"><?= $lists[0]->simbolo.' '.number_format($totalVentaTotal, 2) ?></td>
        </tr>
    </tfoot>
<?php } ?>
</table>
