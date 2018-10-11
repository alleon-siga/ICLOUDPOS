<?php
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=utilidad_venta.xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<style>
    .compraxunidad{
        background-color: #67908c !important;
    }
    .ventaxunidad{
        background-color: #71bc78 !important;
    }
    .compraxcantidad{
        background-color: #e6cca5 !important;
    }
    .ventaxcantidad{
        background-color: #c2b1b5 !important;
    }
    .resulOperativo{
        background-color: #6ec4c6 !important;
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
    $result = array();
    $totCantidad = $totCostoCompraSi = $totImpCompra = $totCostoCompraImp = $totCostoCompraCantSi = $totImpCompraCant = $totCostoTotal  = $totCostoVentaSi = $totCostoVenta = $totImpVenta = $totCostoVentaCantSi = $totImpVentaCant = $totCostoTotalCant = $totUtilidadXund = $totUtilidadTotal = $totPorRenta = 0;
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
        if($costoVentaCantSi>0){
            $porRenta = 1 - ($costoCompraCantSi / $costoVentaCantSi); //Porcentaje de rentabilidad
        }else{
            $porRenta = 0;
        }

        $clase = "";
        if($porRenta<0){
            $clase = "color: red;";
        }

        //Totales
        $totCantidad += $cantidad;
        $totCostoCompraSi += $costoCompraSi;
        $totImpCompra += $impCompra;
        $totCostoCompraImp += $costoCompraImp;
        $totCostoCompraCantSi += $costoCompraCantSi;
        $totImpCompraCant += $impCompraCant;
        $totCostoTotal += $costoTotal;
        $totCostoVentaSi += $costoVentaSi;
        $totImpVenta += $impVenta;
        $totCostoVenta += $costoVenta;
        $totCostoVentaCantSi += $costoVentaCantSi;
        $totImpVentaCant += $impVentaCant;
        $totCostoTotalCant += $costoTotalCant;
        $totUtilidadXund += $utilidadXund;
        $totUtilidadTotal += $utilidadTotal;
        $totPorRenta += $porRenta;

        $index = $ingreso->producto_id . '_' . $ingreso->unidad_medida;
        if (!isset($result[$index])) {
            $result[$ingreso->producto_id . '_' . $ingreso->unidad_medida] = array(
                'counter' => 1,
                'producto_nombre' => $ingreso->producto_nombre,
                'nombre_unidad' => $ingreso->nombre_unidad,
                'cantidad' => $cantidad,

                'costoCompraSi' => $costoCompraSi,
                'impCompra' => $impCompra,
                'costoCompraImp' => $costoCompraImp,

                'costoCompraCantSi' => $costoCompraCantSi,
                'impCompraCant' => $impCompraCant,
                'costoTotal' => $costoTotal,

                'costoVentaSi' => $costoVentaSi,
                'impVenta' => $impVenta,
                'costoVenta' => $costoVenta,

                'costoVentaCantSi' => $costoVentaCantSi,
                'impVentaCant' => $impVentaCant,
                'costoTotalCant' => $costoTotalCant,

                'utilidadXund' => $utilidadXund,
                'utilidadTotal' => $utilidadTotal,
                'porRenta' => $porRenta,
            );
        } else {
            $result[$index]['counter']++;
            $result[$index]['cantidad'] += $cantidad;

            $result[$index]['costoCompraSi'] += $costoCompraSi;
            $result[$index]['impCompra'] += $impCompra;
            $result[$index]['costoCompraImp'] += $costoCompraImp;

            $result[$index]['costoCompraCantSi'] += $costoCompraCantSi;
            $result[$index]['impCompraCant'] += $impCompraCant;
            $result[$index]['costoTotal'] += $costoTotal;

            $result[$index]['costoVentaSi'] += $costoVentaSi;
            $result[$index]['impVenta'] += $impVenta;
            $result[$index]['costoVenta'] += $costoVenta;

            $result[$index]['costoVentaCantSi'] += $costoVentaCantSi;
            $result[$index]['impVentaCant'] += $impVentaCant;
            $result[$index]['costoTotalCant'] += $costoTotalCant;

            $result[$index]['utilidadXund'] += $utilidadXund;
            $result[$index]['utilidadTotal'] += $utilidadTotal;
            $result[$index]['porRenta'] += $porRenta;
        }

    endforeach;

        ?>

    <?php foreach ($result as $r): ?>
        <tr>
            <td style="<?= $clase ?>"><?= $r['producto_nombre'] ?></td>
            <td style="<?= $clase ?>"><?= $r['nombre_unidad'] ?></td>
            <td style="text-align: right; <?= $clase ?>"><?= number_format($r['cantidad'], 0) ?></td>

            <td class="compraxunidad" style="text-align: right; <?= $clase ?>"><?= number_format($r['costoCompraSi'] / $r['counter'], 2) ?></td>
            <td class="compraxunidad" style="text-align: right; <?= $clase ?>"><?= number_format($r['impCompra'] / $r['counter'], 2) ?></td>
            <td class="compraxunidad" style="text-align: right; <?= $clase ?>"><?= number_format($r['costoCompraImp'] / $r['counter'], 2) ?></td>

            <td class="compraxcantidad" style="text-align: right; <?= $clase ?>"><?= number_format($r['costoCompraCantSi'], 2) ?></td>
            <td class="compraxcantidad" style="text-align: right; <?= $clase ?>"><?= number_format($r['impCompraCant'], 2) ?></td>
            <td class="compraxcantidad" style="text-align: right; <?= $clase ?>"><?= number_format($r['costoTotal'], 2) ?></td>

            <td class="ventaxunidad" style="text-align: right; <?= $clase ?>"><?= number_format($r['costoVentaSi'] / $r['counter'], 2) ?></td>
            <td class="ventaxunidad" style="text-align: right; <?= $clase ?>"><?= number_format($r['impVenta'] / $r['counter'], 2) ?></td>
            <td class="ventaxunidad" style="text-align: right; <?= $clase ?>"><?= number_format($r['costoVenta'] / $r['counter'], 2) ?></td>

            <td class="ventaxcantidad" style="text-align: right; <?= $clase ?>"><?= number_format($r['costoVentaCantSi'], 2) ?></td>
            <td class="ventaxcantidad" style="text-align: right; <?= $clase ?>"><?= number_format($r['impVentaCant'], 2) ?></td>
            <td class="ventaxcantidad" style="text-align: right; <?= $clase ?>"><?= number_format($r['costoTotalCant'], 2) ?></td>

            <td class="resulOperativo" style="text-align: right; <?= $clase ?>"><?= number_format($r['utilidadXund'] / $r['counter'], 2) ?></td>
            <td class="resulOperativo" style="text-align: right; <?= $clase ?>"><?= number_format($r['utilidadTotal'], 2) ?></td>
            <td class="resulOperativo" style="text-align: right; <?= $clase ?>"><?= number_format($r['porRenta'] / $r['counter'], 2) ?></td>
        </tr>
    <?php endforeach; ?>
        <?php

    if(count($lists)>0){
        $totCostoCompraSi = $totCostoCompraSi / count($lists);
        $totImpCompra = $totImpCompra / count($lists);
        $totCostoCompraImp = $totCostoCompraImp / count($lists);
        $totCostoVentaSi = $totCostoVentaSi / count($lists);
        $totImpVenta = $totImpVenta / count($lists);
        $totCostoVenta = $totCostoVenta / count($lists);
        $totUtilidadXund = $totUtilidadXund / count($lists);
        $totPorRenta = $totPorRenta / count($lists);
    }
    ?>
    </tbody>
    <tfoot>
    <tr>
        <td colspan="2">Totales:</td>
        <td style="text-align: right;"><?= $totCantidad ?></td>

        <td style="text-align: right;"><?= number_format($totCostoCompraSi, 2) ?></td>
        <td style="text-align: right;"><?= number_format($totImpCompra, 2) ?></td>
        <td style="text-align: right;"><?= number_format($totCostoCompraImp, 2) ?></td>

        <td style="text-align: right;"><?= number_format($totCostoCompraCantSi, 2) ?></td>
        <td style="text-align: right;"><?= number_format($totImpCompraCant, 2) ?></td>
        <td style="text-align: right;"><?= number_format($totCostoTotal, 2) ?></td>

        <td style="text-align: right;"><?= number_format($totCostoVentaSi, 2) ?></td>
        <td style="text-align: right;"><?= number_format($totImpVenta, 2) ?></td>
        <td style="text-align: right;"><?= number_format($totCostoVenta, 2) ?></td>

        <td style="text-align: right;"><?= number_format($totCostoVentaCantSi, 2) ?></td>
        <td style="text-align: right;"><?= number_format($totImpVentaCant, 2) ?></td>
        <td style="text-align: right;"><?= number_format($totCostoTotalCant, 2) ?></td>

        <td style="text-align: right;"><?= number_format($totUtilidadXund, 2) ?></td>
        <td style="text-align: right;"><?= number_format($totUtilidadTotal, 2) ?></td>
        <td style="text-align: right;"><?= number_format($totPorRenta, 2) ?></td>
    </tr>
    </tfoot>
</table>
