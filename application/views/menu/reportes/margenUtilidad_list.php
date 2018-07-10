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
        $('#exportar_excel').on('click', function () {
            exportar_excel();
        });

        $("#exportar_pdf").on('click', function () {
            exportar_pdf();
        });

        $('.nav-tabs a[href="#grafico"]').on('shown.bs.tab', function(event){
            var data = {
                'local_id': $("#local_id").val(),
                'fecha': $("#fecha").val(),
                'producto_id': $("#producto_id").val(),
                'grupo_id': $("#grupo_id").val(),
                'marca_id': $("#marca_id").val(),
                'linea_id': $("#linea_id").val(),
                'familia_id': $("#familia_id").val()
            };
        });
    });

    function exportar_pdf() {
        var data = {
            'local_id': $("#local_id").val(),
            'fecha': $("#fecha").val(),
            'moneda_id': $("#moneda_id").val(),
            'producto_id': $("#producto_id").val(),
            'grupo_id': $("#grupo_id").val(),
            'marca_id': $("#marca_id").val(),
            'linea_id': $("#linea_id").val(),
            'familia_id': $("#familia_id").val()
        };

        var win = window.open('<?= base_url()?>reporte/margenUtilidad/pdf?data=' + JSON.stringify(data), '_blank');
        win.focus();
    }

    function exportar_excel() {
        var data = {
            'local_id': $("#local_id").val(),
            'fecha': $("#fecha").val(),
            'moneda_id': $("#moneda_id").val(),
            'producto_id': $("#producto_id").val(),
            'grupo_id': $("#grupo_id").val(),
            'marca_id': $("#marca_id").val(),
            'linea_id': $("#linea_id").val(),
            'familia_id': $("#familia_id").val()
        };

        var win = window.open('<?= base_url()?>reporte/margenUtilidad/excel?data=' + JSON.stringify(data), '_blank');
        win.focus();
    }
</script>