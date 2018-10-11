<?php $ruta = base_url(); ?>
<?php $md = get_moneda_defecto() ?>
<style>
    table tfoot tr td {
        font-weight: bold;
    }

    .b-default {
        background-color: #55c862;
        color: #fff;
    }

    .b-warning {
        background-color: #F78181;
        color: #fff;
    }

    .negativo {
        color: red;
    }

    .compraxunidad {
        background-color: #67908c !important;
    }

    .ventaxunidad {
        background-color: #71bc78 !important;
    }

    .compraxcantidad {
        background-color: #e6cca5 !important;
    }

    .ventaxcantidad {
        background-color: #c2b1b5 !important;
    }

    .resulOperativo {
        background-color: #6ec4c6 !important;
    }
</style>
<div class="table-responsive">
    <table class='table dataTable table-bordered no-footer tableStyle' style="overflow:scroll">
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
        $totCantidad = $totCostoCompraSi = $totImpCompra = $totCostoCompraImp = $totCostoCompraCantSi = $totImpCompraCant = $totCostoTotal = $totCostoVentaSi = $totCostoVenta = $totImpVenta = $totCostoVentaCantSi = $totImpVentaCant = $totCostoTotalCant = $totUtilidadXund = $totUtilidadTotal = $totPorRenta = 0;
        foreach ($lists as $ingreso):
            $impuesto = (($ingreso->impuesto_porciento / 100) + 1);
            $cantidad = $ingreso->cantidad;
            $costoCompra = $ingreso->detalle_costo_ultimo; //Costo de compra unitario con impuesto
            $precioVenta = $ingreso->precio; //precio de venta

            //Ventas
            if ($ingreso->tipo_impuesto == '1') { //incluye impuesto
                $costoVentaSi = $precioVenta / $impuesto; //Costo de venta unitario sin impuesto
                $impVenta = $precioVenta - $costoVentaSi;
                $costoVenta = $costoVentaSi * $impuesto; //Costo de venta unitario con impuesto
            } elseif ($ingreso->tipo_impuesto == '2') { //agregar impuesto
                $costoVentaSi = $precioVenta; //Costo de venta unitario sin impuesto
                $costoVenta = $costoVentaSi * $impuesto; //Costo de venta unitario con impuesto
                $impVenta = $costoVenta - $costoVentaSi;
            } else { //no incluye impuesto
                $costoVentaSi = $precioVenta;
                $impVenta = 0;
                $costoVenta = $costoVentaSi + $impVenta;
            }
            //Compras x unidad
            if ($ingreso->tipo_impuesto_compra == '1') { //incluye impuesto
                $costoCompraSi = $costoCompra / $impuesto; //costo de compra sin impuesto
                $impCompra = $costoCompra - $costoCompraSi; //Impuesto de compra
                $costoCompraImp = $costoCompraSi + $impCompra; //Costo Total
            } elseif ($ingreso->tipo_impuesto_compra == '2') { //agrega impuesto
                $costoCompraSi = $costoCompra; //costo de compra sin impuesto
                $costoCompraImp = $costoCompraSi * $impuesto; //Costo Total
                $impCompra = $costoCompraImp - $costoCompraSi; //Impuesto de compra
            } else {
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
            if ($costoVentaCantSi > 0) {
                $porRenta = 1 - ($costoCompraCantSi / $costoVentaCantSi); //Porcentaje de rentabilidad
            } else {
                $porRenta = 0;
            }

            $clase = "";
            if ($porRenta < 0) {
                $clase = "negativo";
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


        endforeach; ?>

        <?php foreach ($result as $r): ?>
            <tr>
                <td class="<?= $clase ?>"><?= $r['producto_nombre'] ?></td>
                <td class="<?= $clase ?>"><?= $r['nombre_unidad'] ?></td>
                <td class="<?= $clase ?>" style="text-align: right;"><?= number_format($r['cantidad'], 0) ?></td>

                <td class="<?= $clase ?> compraxunidad"
                    style="text-align: right;"><?= number_format($r['costoCompraSi'] / $r['counter'], 2) ?></td>
                <td class="<?= $clase ?> compraxunidad"
                    style="text-align: right;"><?= number_format($r['impCompra'] / $r['counter'], 2) ?></td>
                <td class="<?= $clase ?> compraxunidad"
                    style="text-align: right;"><?= number_format($r['costoCompraImp'] / $r['counter'], 2) ?></td>

                <td class="<?= $clase ?> compraxcantidad"
                    style="text-align: right;"><?= number_format($r['costoCompraCantSi'], 2) ?></td>
                <td class="<?= $clase ?> compraxcantidad"
                    style="text-align: right;"><?= number_format($r['impCompraCant'], 2) ?></td>
                <td class="<?= $clase ?> compraxcantidad"
                    style="text-align: right;"><?= number_format($r['costoTotal'], 2) ?></td>

                <td class="<?= $clase ?> ventaxunidad"
                    style="text-align: right;"><?= number_format($r['costoVentaSi'] / $r['counter'], 2) ?></td>
                <td class="<?= $clase ?> ventaxunidad"
                    style="text-align: right;"><?= number_format($r['impVenta'] / $r['counter'], 2) ?></td>
                <td class="<?= $clase ?> ventaxunidad"
                    style="text-align: right;"><?= number_format($r['costoVenta'] / $r['counter'], 2) ?></td>

                <td class="<?= $clase ?> ventaxcantidad"
                    style="text-align: right;"><?= number_format($r['costoVentaCantSi'], 2) ?></td>
                <td class="<?= $clase ?> ventaxcantidad"
                    style="text-align: right;"><?= number_format($r['impVentaCant'], 2) ?></td>
                <td class="<?= $clase ?> ventaxcantidad"
                    style="text-align: right;"><?= number_format($r['costoTotalCant'], 2) ?></td>

                <td class="<?= $clase ?> resulOperativo"
                    style="text-align: right;"><?= number_format($r['utilidadXund'] / $r['counter'], 2) ?></td>
                <td class="<?= $clase ?> resulOperativo"
                    style="text-align: right;"><?= number_format($r['utilidadTotal'], 2) ?></td>
                <td class="<?= $clase ?> resulOperativo"
                    style="text-align: right;"><?= number_format($r['porRenta'] / $r['counter'], 2) ?></td>
            </tr>
        <?php endforeach; ?>

        <?php
        if (count($lists) > 0) {
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
</div>
<div class="row">
    <div class="col-md-12">
        <br>
        <button type="button" id="exportar_excel" title="Exportar Excel" class="btn btn-primary">
            <i class="fa fa-file-excel-o fa-fw"></i>
        </button>
        <button type="button" id="exportar_pdf" title="Exportar Pdf" class="btn btn-primary">
            <i class="fa fa-file-pdf-o fa-fw"></i>
        </button>
    </div>
</div>
<script type="text/javascript">
  $(document).ready(function () {
    TablesDatatables.init(0, 'asc')

    $('#exportar_excel').on('click', function () {
      exportar_excel()
    })

    $('#exportar_pdf').on('click', function () {
      exportar_pdf()
    })
  })

  function exportar_pdf () {
    var data = {
      'local_id': $('#local_id').val(),
      'fecha': $('#fecha').val(),
      'moneda_id': $('#moneda_id').val()
    }

    var win = window.open('<?= base_url()?>reporte_ventas/utilidadProducto/pdf?data=' + JSON.stringify(data), '_blank')
    win.focus()
  }

  function exportar_excel () {
    var data = {
      'local_id': $('#local_id').val(),
      'fecha': $('#fecha').val(),
      'moneda_id': $('#moneda_id').val()
    }

    var win = window.open('<?= base_url()?>reporte_ventas/utilidadProducto/excel?data=' + JSON.stringify(data), '_blank')
    win.focus()
  }
</script>