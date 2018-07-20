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
    .negativo{
        color: red;
    }
</style>
<div class="table-responsive">
    <table class='table dataTable table-bordered no-footer tableStyle' style="overflow:scroll">
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
            </tr>
        </thead>
        <tbody>
    <?php
        $totalCostoImpuesto = $totalPrecioImpuesto = $totalCostoTotal = $totalSubTotal = $totalImpuestoV = $totalVentaTotal = $totalUtilidadTotal = 0;
        foreach ($lists as $ingreso):
            $impuesto = (($ingreso->impuesto_porciento / 100) + 1);
            $cantidad = $ingreso->cantidad;
            $costoCompra = $ingreso->detalle_costo_ultimo; //Costo de compra unitario con impuesto
            
            if($ingreso->tipo_impuesto=='1'){ //incluye impuesto
            $impCompra = $costoCompra - $costoCompraSi; //Impuesto de compra
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
            $impCompra = $costoCompra - $costoCompraSi; //Impuesto de compra
            $costoTotal = $cantidad * $costoCompra; //Costo Total
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
                <td class="<?= $clase ?>" style="text-align: right;"><?= $ingreso->venta_id ?></td>
                <td class="<?= $clase ?>"><?= $ingreso->local_nombre ?></td>
                <td class="<?= $clase ?>"><?= $ingreso->fecha ?></td>
                <td class="<?= $clase ?>"><?= $ingreso->proveedor_nombre ?></td>
                <td class="<?= $clase ?>"><?= $ingreso->producto_nombre ?></td>
                <td class="<?= $clase ?>"><?= $ingreso->nombre_unidad ?></td>
                <td class="<?= $clase ?>" style="text-align: right;"><?= number_format($costoCompraSi, 2) ?></td>
                <td class="<?= $clase ?>" style="text-align: right;"><?= number_format($impCompra, 2) ?></td>
                <td class="<?= $clase ?>" style="text-align: right;"><?= number_format($costoCompra, 2) ?></td>
                <td class="<?= $clase ?>" style="text-align: right;"><?= number_format($impuesto, 2) ?></td>
                <td class="<?= $clase ?>" style="text-align: right;"><?= number_format($costoVentaSi, 2) ?></td>
                <td class="<?= $clase ?>" style="text-align: right;"><?= number_format($costoVenta, 2) ?></td>
                <td class="<?= $clase ?>" style="text-align: right;"><?= number_format($costoTotal, 2) ?></td>
                <td class="<?= $clase ?>" style="text-align: right;"><?= $cantidad ?></td>
                <td class="<?= $clase ?>" style="text-align: right;"><?= number_format($subtotal, 2) ?></td>
                <td class="<?= $clase ?>" style="text-align: right;"><?= number_format($impVenta, 2) ?></td>
                <td class="<?= $clase ?>" style="text-align: right;"><?= number_format($precioVenta, 2) ?></td>
                <td class="<?= $clase ?>" style="text-align: right;"><?= number_format($utilidadXund, 2) ?></td>
                <td class="<?= $clase ?>" style="text-align: right;"><?= number_format($utilidadTotal, 2) ?></td>
                <td class="<?= $clase ?>" style="text-align: right;"><?= number_format($porRenta, 2) ?></td>
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
                <td style="text-align: right; color: green;"><?= number_format($totalUtilidadTotal, 2) ?></td>
                <td></td>
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
        TablesDatatables.init(0, 'asc');

        $('#exportar_excel').on('click', function () {
            exportar_excel();
        });

        $("#exportar_pdf").on('click', function () {
            exportar_pdf();
        });
    });

    function exportar_pdf() {
        var data = {
            'local_id': $("#local_id").val(),
            'fecha': $("#fecha").val()
        };

        var win = window.open('<?= base_url()?>reporte/utilidadProducto/pdf?data=' + JSON.stringify(data), '_blank');
        win.focus();
    }

    function exportar_excel() {
        var data = {
            'local_id': $("#local_id").val(),
            'fecha': $("#fecha").val()
        };

        var win = window.open('<?= base_url()?>reporte/utilidadProducto/excel?data=' + JSON.stringify(data), '_blank');
        win.focus();
    }
</script>