<?php
    header("Content-type: application/octet-stream");
    header("Content-Disposition: attachment; filename=utilidad_venta.xls");
    header("Pragma: no-cache");
    header("Expires: 0");
?>
<?php $md = get_moneda_defecto() ?>
<h4 style="text-align: center; margin: 0;">Reporte de utilidades por venta</h4>
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
            <th># Venta </th>
            <th>Local</th>
            <th>Fecha</th>
            <th>Proveedor</th>
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
            <th>% rentabilidad</th>
            <th>Moneda</th>
            <th>Tipo_impuesto_compra</th>
        </tr>
    </thead>
    <tbody>
<?php
    $totalCostoImpuesto = $totalPrecioImpuesto = $totalCostoTotal = $totalSubTotal = $totalImpuestoV = $totalVentaTotal = $totalUtilidadTotal = 0;
    foreach ($lists as $ingreso):
        $impuesto = (($ingreso->impuesto_porciento / 100) + 1);
        $cantidad = $ingreso->cantidad;
        $costoCompra = $ingreso->detalle_costo_ultimo; //Costo de compra unitario con impuesto
        //Ventas
        if($ingreso->tipo_impuesto=='1' || empty($ingreso->tipo_impuesto)){ //incluye impuesto
            $precioVenta = $ingreso->detalle_importe; //precio de venta
            $costoVentaSi = ($precioVenta / $cantidad) / $impuesto; //Costo de venta unitario sin impuesto
            $costoVenta = $costoVentaSi * $impuesto; //Costo de venta unitario con impuesto
            $costoCompraSi = $ingreso->detalle_costo_ultimo / $impuesto;
        }elseif($ingreso->tipo_impuesto=='2'){ //agregar impuesto
            $precioVenta = $ingreso->detalle_importe * $impuesto;
            $costoVentaSi = ($precioVenta / $cantidad) / $impuesto; //Costo de venta unitario sin impuesto
            $costoVenta = $costoVentaSi * $impuesto; //Costo de venta unitario con impuesto
            $costoCompraSi = $ingreso->detalle_costo_ultimo * $impuesto;
        }else{ //no incluye impuesto
            $precioVenta = $ingreso->detalle_importe; //precio de venta
            $costoVentaSi = ($precioVenta / $cantidad);
            $costoVenta = $costoVentaSi;
            $costoCompraSi = $ingreso->detalle_costo_ultimo;
        }
        //Compras
        if($ingreso->tipo_impuesto_compra=='1'){ //incluye impuesto
            $costoCompraSi = $costoCompra / $impuesto; //costo de compra sin impuesto
            $impCompra = $costoCompra - $costoCompraSi; //Impuesto de compra
            $costoCompraImp = $costoCompraSi + $impCompra; //Costo Total
        }elseif($ingreso->tipo_impuesto_compra=='2'){ //agrega impuesto
            $costoCompraSi = $costoCompra; //costo de compra sin impuesto
            $costoCompraImp = $costoCompraSi * $impuesto; //Costo Total
            $impCompra = $costoCompraImp - $costoCompraSi; //Impuesto de compra
        }else{
            $costoCompraSi = $costoCompra; //costo de compra sin impuesto
            $impCompra = 0; //Impuesto de compra
            $costoCompraImp = $costoCompraSi + $impCompra; //Costo Total
        }
        
        $costoTotal = $cantidad * $costoCompraImp; //Costo Total
        $subtotal = $cantidad * $costoVentaSi;
        $impVenta = $precioVenta - $subtotal;
        $utilidadXund = $costoVentaSi - $costoCompraSi;
        $utilidadTotal = $utilidadXund * $cantidad;
        if($costoCompraSi>0){
            $porRenta = ($utilidadXund / $costoCompraSi) * 100; //Porcentaje de rentabilidad    
        }else{
            $porRenta = 0;
        }
        //Totales
        $totalCostoImpuesto += $costoCompra;
        $totalPrecioImpuesto += $costoVenta;
        $totalCostoTotal += $costoTotal;
        $totalSubTotal += $subtotal;
        $totalImpuestoV += $impVenta;
        $totalVentaTotal += $precioVenta;
        $totalUtilidadTotal += $utilidadTotal;

        $clase = "";
        if($utilidadTotal<0){
            $clase = "negativo";
        }
?>
        <tr>
            <td style="text-align: right;"><?= $ingreso->venta_id ?></td>
            <td><?= $ingreso->local_nombre ?></td>
            <td><?= $ingreso->fecha ?></td>
            <td><?= $ingreso->proveedor_nombre ?></td>
            <td><?= $ingreso->producto_nombre ?></td>
            <td><?= $ingreso->nombre_unidad ?></td>
            <td style="text-align: right;"><?= number_format($costoCompraSi, 2) ?></td>
            <td style="text-align: right;"><?= number_format($impCompra, 2) ?></td>
            <td style="text-align: right;"><?= number_format($costoCompraImp, 2) ?></td>
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
            <td style="text-align: right;"><?= number_format($porRenta, 2) ?></td>
            <td><?= $ingreso->id_moneda ?></td>
            <td><?= $ingreso->tipo_impuesto_compra ?></td>
        </tr>
<?php
    endforeach;
?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="8">TOTALES</td>
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
            <td style="text-align: right;"><?= number_format($totalUtilidadTotal, 2) ?></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </tfoot>
</table>
