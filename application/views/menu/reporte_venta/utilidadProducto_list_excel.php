<?php
    header("Content-type: application/octet-stream");
    header("Content-Disposition: attachment; filename=utilidad_venta.xls");
    header("Pragma: no-cache");
    header("Expires: 0");
?>
<style>
    .negativo{
        color: red;
    }
    .compraxunidad{
        background-color: #46a3cb !important;
    }
    .ventaxunidad{
        background-color: #71bc78 !important;
    }
    .compraxcantidad{
        background-color: #ef71cd !important;
    }
    .ventaxcantidad{
        background-color: #2fc4a6 !important;
    }
    .resulOperativo{
        background-color: #dd7e7e !important;
    }
</style>
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
            <th rowspan="2" style="vertical-align: middle;"># Venta</th>
            <th rowspan="2" style="vertical-align: middle;">Local</th>
            <th rowspan="2" style="vertical-align: middle;">Fecha</th>
            <th rowspan="2" style="vertical-align: middle;">Proveedor</th>
            <th rowspan="2" style="vertical-align: middle;">Producto</th>
            <th rowspan="2" style="vertical-align: middle;">Unidad</th>
            <th rowspan="2" style="vertical-align: middle;">Cantidad</th>
            <th colspan="3" class="compraxunidad">Compras x Unidad</th>
            <th colspan="3" class="compraxcantidad">Compras x Cantidad</th>
            <th colspan="3" class="ventaxunidad">Ventas x Unidad</th>
            <th colspan="3" class="ventaxcantidad">Ventas x Cantidad</th>
            <th colspan="3" class="resulOperativo">Resultado Operativo</th>
        </tr>
        <tr>
            <th class="compraxunidad">Costo unitario</th>
            <th class="compraxunidad">Impuesto</th>
            <th class="compraxunidad">Costo + Impuesto</th>

            <th class="compraxcantidad">Subtotal</th>
            <th class="compraxcantidad">Impuesto</th>
            <th class="compraxcantidad">Costo Total</th>

            <th class="ventaxunidad">Precio unitario</th>
            <th class="ventaxunidad">Impuesto</th>
            <th class="ventaxunidad">Precio + Impuesto</th>

            <th class="ventaxcantidad">Subtotal</th>
            <th class="ventaxcantidad">Impuesto</th>
            <th class="ventaxcantidad">Venta total</th>

            <th class="resulOperativo">Utilidad x unidad</th>
            <th class="resulOperativo">Utilidad total</th>
            <th class="resulOperativo">% Rentabilidad</th>
        </tr>
    </thead>
    <tbody>
    <?php
    $totCostoCompraSi = $totImpCompra = $totCostoCompraImp = $totCostoVentaSi = $totImpVenta = $totCostoVenta = $totCostoCompraCantSi = $totImpCompraCant = $totalCostoTotal = $totCostoVentaCantSi = $totImpVentaCant = $totCostoTotalCant = $totUtilidadXund = $totUtilidadTotal = 0;
    foreach ($lists as $ingreso):
        $impuesto = (($ingreso->impuesto_porciento / 100) + 1);
        $cantidad = $ingreso->cantidad;
        $costoCompra = $ingreso->detalle_costo_ultimo; //Costo de compra unitario con impuesto
        $precioVenta = $ingreso->precio; //precio de venta

        //Ventas
        if($ingreso->tipo_impuesto=='1'){ //incluye impuesto
            $costoVentaSi = $precioVenta / $impuesto; //Costo de venta unitario sin impuesto
            $impVenta = $precioVenta - $costoVentaSi;
            $costoVenta = $costoVentaSi * $impuesto; //Costo de venta unitario con impuesto
        }elseif($ingreso->tipo_impuesto=='2'){ //agregar impuesto
            $costoVentaSi = $precioVenta; //Costo de venta unitario sin impuesto
            $costoVenta = $costoVentaSi * $impuesto; //Costo de venta unitario con impuesto
            $impVenta = $costoVenta - $costoVentaSi;
        }else{ //no incluye impuesto
            $costoVentaSi = $precioVenta;
            $impVenta = 0;
            $costoVenta = $costoVentaSi + $impVenta;
        }
        //Compras x unidad
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
        //Compras x cantidad
        $costoCompraCantSi = $costoCompraSi * $cantidad; //subtotal
        $impCompraCant = $impCompra * $cantidad; //impuesto
        $costoTotal = $costoCompraImp * $cantidad; //Costo total
        //ventas x cantidad
        $costoVentaCantSi = $costoVentaSi * $cantidad; //subtotal
        $impVentaCant = $impVenta * $cantidad; //impuesto
        $costoTotalCant = $costoVenta * $cantidad; //venta total
    
        $utilidadXund = $costoVentaSi - $costoCompraSi;
        $utilidadTotal = $costoVentaCantSi - $costoCompraCantSi;
        if($costoCompraSi>0){
            $porRenta = $utilidadTotal / (($costoVentaCantSi) / 100); //Porcentaje de rentabilidad
        }else{
            $porRenta = 0;
        }

        $clase = "";
        if($utilidadTotal<0){
            $clase = "negativo";
        }
    ?>
        <tr>
            <td class="" style="text-align: right;"><?= $ingreso->venta_id ?></td>
            <td class=""><?= $ingreso->local_nombre ?></td>
            <td class=""><?= $ingreso->fecha ?></td>
            <td class=""><?= $ingreso->proveedor_nombre ?></td>
            <td class=""><?= $ingreso->producto_nombre ?></td>
            <td class=""><?= $ingreso->nombre_unidad ?></td>
            <td class="" style="text-align: right;"><?= number_format($cantidad, 0) ?></td>

            <td class="compraxunidad" style="text-align: right;"><?= number_format($costoCompraSi, 2) ?></td>
            <td class="compraxunidad" style="text-align: right;"><?= number_format($impCompra, 2) ?></td>
            <td class="compraxunidad" style="text-align: right;"><?= number_format($costoCompraImp, 2) ?></td>

            <td class="compraxcantidad" style="text-align: right;"><?= number_format($costoCompraCantSi, 2) ?></td>
            <td class="compraxcantidad" style="text-align: right;"><?= number_format($impCompraCant, 2) ?></td>
            <td class="compraxcantidad" style="text-align: right;"><?= number_format($costoTotal, 2) ?></td>

            <td class="ventaxunidad" style="text-align: right;"><?= number_format($costoVentaSi, 2) ?></td>
            <td class="ventaxunidad" style="text-align: right;"><?= number_format($impVenta, 2) ?></td>
            <td class="ventaxunidad" style="text-align: right;"><?= number_format($costoVenta, 2) ?></td>

            <td class="ventaxcantidad" style="text-align: right;"><?= number_format($costoVentaCantSi, 2) ?></td>
            <td class="ventaxcantidad" style="text-align: right;"><?= number_format($impVentaCant, 2) ?></td>
            <td class="ventaxcantidad" style="text-align: right;"><?= number_format($costoTotalCant, 2) ?></td>

            <td class="resulOperativo" style="text-align: right;"><?= number_format($utilidadXund, 2) ?></td>
            <td class="resulOperativo" style="text-align: right;"><?= number_format($utilidadTotal, 2) ?></td>
            <td class="resulOperativo" style="text-align: right;"><?= number_format($porRenta, 2) ?></td>
        </tr>
    <?php
    endforeach;
    ?>
    </tbody>
</table>
