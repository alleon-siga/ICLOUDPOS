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
<h4 style="text-align: center;">Reporte de Cr&eacute;dito Fiscal</h4>
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
            <td><?= $ingreso->nombre_condiciones ?></td>
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
