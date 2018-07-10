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
<h4 style="text-align: center;">Reporte de m&aacute;rgen de utilidad</h4>
<h4 style="text-align: center;">Desde <?= date('d/m/Y', strtotime($fecha_ini)) ?>
    al <?= date('d/m/Y', strtotime($fecha_fin)) ?></h4>

<h5>EMPRESA: <?= valueOption('EMPRESA_NOMBRE') ?></h5>
<h5>DIRECCI&Oacute;N: <?= $local_direccion ?></h5>
<h5>SUCURSAL: <?= $local_nombre ?></h5>
<table>
    <thead>
        <tr>
            <th>Producto</th>
            <th>Unidad</th>
            <th>Costo unitario</th>
            <th>Impuesto</th>
            <th>Costo + Impuesto</th>
            <th>Impuesto</th>
            <th>Precio unitario</th>
            <th>Precio + Impuesto</th>
            <th>Costo Total</th>
            <th>Cantidad vendida</th>
            <th>Subtotal</th>
            <th>Impuesto</th>
            <th>Venta total</th>
            <th>Utilidad x unidad</th>
            <th>Utilidad total</th>
        </tr>
    </thead>
    <tbody>
<?php
    $totalCostoImpuesto = $totalPrecioImpuesto = $totalCostoTotal = $totalSubTotal = $totalImpuestoV = $totalVentaTotal = $totalUtilidadTotal = 0;
    foreach ($lists as $ingreso):
        $impuesto = (($ingreso->impuesto_porciento / 100) + 1);
        $cantidad = $ingreso->cantidad;
        $costoCompraSi = $ingreso->costoCompraSi; //Costo de compra unitario sin impuesto
        $costoCompra = $ingreso->detalle_costo_ultimo; //Costo de compra unitario con impuesto
        $impCompra = $costoCompra - $costoCompraSi; //Impuesto de compra
        $precioVenta = $ingreso->detalle_importe; //precio de venta
        $costoVentaSi = $ingreso->costoVentaSi;
        $costoVenta = $ingreso->costoVenta;
        $costoTotal = $ingreso->costoTotal;
        $subtotal = $ingreso->subtotal;
        $impVenta = $precioVenta - $subtotal;
        //$utilidadXund = $costoVentaSi - $costoCompraSi;
        $utilidadXund = $ingreso->utilidadXund;
        //$utilidadTotal = $utilidadXund * $cantidad;
        $utilidadTotal = $ingreso->utilidadTotal;
        /*if($costoCompraSi>0){
            //$porRenta = ($utilidadXund / $costoCompraSi) * 100; //Porcentaje de rentabilidad
            $porRenta = $ingreso->porRenta;
        }else{
            $porRenta = 0;
        }*/
        //Totales
        $totalCostoImpuesto += $costoCompra;
        $totalPrecioImpuesto += $costoVenta;
        $totalCostoTotal += $costoTotal;
        $totalSubTotal += $subtotal;
        $totalImpuestoV += $impVenta;
        $totalVentaTotal += $precioVenta;
        $totalUtilidadTotal += $utilidadTotal;
?>
        <tr>
            <td><?= $ingreso->producto_nombre ?></td>
            <td><?= $ingreso->nombre_unidad ?></td>
            <td style="text-align: right;"><?= number_format($costoCompraSi, 2) ?></td>
            <td style="text-align: right;"><?= number_format($impCompra, 2) ?></td>
            <td style="text-align: right;"><?= number_format($costoCompra, 2) ?></td>
            <td style="text-align: right;"><?= number_format($impuesto, 2) ?></td>
            <td style="text-align: right;"><?= number_format($costoVentaSi, 2) ?></td>
            <td style="text-align: right;"><?= number_format($costoVenta, 2) ?></td>
            <td style="text-align: right;"><?= number_format($costoTotal, 2) ?></td>
            <td style="text-align: right;"><?= $cantidad ?></td>
            <td style="text-align: right;"><?= number_format($subtotal, 2) ?></td>
            <td style="text-align: right;"><?= number_format($impVenta, 2) ?></td>
            <td style="text-align: right;"><?= number_format($precioVenta, 2) ?></td>
            <td style="text-align: right;"><?= number_format($utilidadXund, 2) ?></td>
            <td style="text-align: right;"><?= number_format($utilidadTotal, 2) ?></td>
        </tr>
<?php
    endforeach;
?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4">TOTALES</td>
            <td style="text-align: right;"><?= number_format($totalCostoImpuesto, 2) ?></td>
            <td></td>
            <td></td>
            <td style="text-align: right;"><?= number_format($totalPrecioImpuesto, 2) ?></td>
            <td style="text-align: right;"><?= number_format($totalCostoTotal, 2) ?></td>
            <td></td>
            <td style="text-align: right;"><?= number_format($totalSubTotal, 2) ?></td>
            <td style="text-align: right;"><?= number_format($totalImpuestoV, 2) ?></td>
            <td style="text-align: right;"><?= number_format($totalVentaTotal, 2) ?></td>
            <td></td>
            <td style="text-align: right; color: green;"><?= number_format($totalUtilidadTotal, 2) ?></td>
        </tr>
    </tfoot>
</table>
