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
<div class="table-responsive">
    <table class='table dataTable table-bordered no-footer tableStyle' style="overflow:scroll">
        <thead>
            <tr>
                <th rowspan="2" style="vertical-align: middle;">Local</th>
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
        $totCostoCompraSi = $totImpCompra = $totCostoCompraImp = $totCostoVentaSi = $totImpVenta = $totCostoVenta = $totCostoCompraCantSi = $totImpCompraCant = $totalCostoTotal = $totCostoVentaCantSi = $totImpVentaCant = $totCostoTotalCant = $totUtilidadXund = $totUtilidadTotal = $totPorRenta = $totCantidad = 0;
        foreach ($lists as $ingreso):
            $impuesto = (($ingreso->impuesto_porciento / 100) + 1);
            $cantidad = $ingreso->cantidad;
            //compras x unidad
            $costoCompraSi = $ingreso->costoCompraSi; //Costo de compra unitario sin impuesto
            $impCompra = $ingreso->impCompra; //Impuesto de compra
            $costoCompraImp = $ingreso->costoCompraImp; //Costo de compra unitario con impuesto
            //compras x cantidad
            $costoCompraCantSi = $costoCompraSi * $cantidad; //subtotal
            $impCompraCant = $impCompra * $cantidad; //impuesto
            $costoTotal = $costoCompraCantSi + $impCompraCant; //Costo total
            //ventas x unidad
            $costoVentaSi = $ingreso->costoVentaSi; //Precio unitario sin impuesto
            $impVenta = $ingreso->impVenta; //Impuesto de venta
            $costoVenta = $ingreso->costoVenta; //Precio unitario con impuesto
            //ventas x cantidad
            $costoVentaCantSi = $costoVentaSi * $cantidad; //subtotal
            $impVentaCant = $costoVentaCantSi * $impuesto; //impuesto
            $costoTotalCant = $costoVentaCantSi + $impVentaCant; //venta total
            //Resultado operativo
            $utilidadXund = $costoVentaSi - $costoCompraSi;
            $utilidadTotal = $costoVentaCantSi - $costoCompraCantSi;

            if($costoVentaCantSi>0){
                $porRenta = 1 - ($costoCompraCantSi / $costoVentaCantSi); //Porcentaje de rentabilidad
            }else{
                $porRenta = 0;
            }

            $clase = "";
            if($porRenta<0){
                $clase = "negativo";
            }            
            //Totales
            $totCantidad += $cantidad;
            $totCostoCompraSi += $costoCompraSi; 
            $totImpCompra += $impCompra;
            $totCostoCompraImp += $costoCompraImp;
            $totCostoVentaSi += $costoVentaSi;
            $totImpVenta += $impVenta;
            $totCostoVenta += $costoVenta;
            $totCostoCompraCantSi += $costoCompraCantSi;
            $totImpCompraCant += $impCompraCant;
            $totalCostoTotal += $costoTotal;
            $totCostoVentaCantSi += $costoVentaCantSi;
            $totImpVentaCant += $impVentaCant;
            $totCostoTotalCant += $costoTotalCant;
            $totUtilidadXund += $utilidadXund;
            $totUtilidadTotal += $utilidadTotal;
            $totPorRenta += $porRenta;
        ?>
            <tr>
                <td><?= $ingreso->local_nombre ?></td>
                <td><?= $ingreso->producto_nombre ?></td>
                <td><?= $ingreso->nombre_unidad ?></td>
                <td style="text-align: right;"><?= $cantidad ?></td>

                <td class="<?= $clase ?> compraxunidad" style="text-align: right;"><?= number_format($costoCompraSi, 2) ?></td>
                <td class="<?= $clase ?> compraxunidad" style="text-align: right;"><?= number_format($impCompra, 2) ?></td>
                <td class="<?= $clase ?> compraxunidad" style="text-align: right;"><?= number_format($costoCompraImp, 2) ?></td>

                <td class="<?= $clase ?> compraxcantidad" style="text-align: right;"><?= number_format($costoCompraCantSi, 2) ?></td>
                <td class="<?= $clase ?> compraxcantidad" style="text-align: right;"><?= number_format($impCompraCant, 2) ?></td>
                <td class="<?= $clase ?> compraxcantidad" style="text-align: right;"><?= number_format($costoTotal, 2) ?></td>

                <td class="<?= $clase ?> ventaxunidad" style="text-align: right;"><?= number_format($costoVentaSi, 2) ?></td>
                <td class="<?= $clase ?> ventaxunidad" style="text-align: right;"><?= number_format($impVenta, 2) ?></td>
                <td class="<?= $clase ?> ventaxunidad" style="text-align: right;"><?= number_format($costoVenta, 2) ?></td>

                <td class="<?= $clase ?> ventaxcantidad" style="text-align: right;"><?= number_format($costoVentaCantSi, 2) ?></td>
                <td class="<?= $clase ?> ventaxcantidad" style="text-align: right;"><?= number_format($impVentaCant, 2) ?></td>
                <td class="<?= $clase ?> ventaxcantidad" style="text-align: right;"><?= number_format($costoTotalCant, 2) ?></td>

                <td class="<?= $clase ?> resulOperativo" style="text-align: right;"><?= number_format($utilidadXund, 2) ?></td>
                <td class="<?= $clase ?> resulOperativo" style="text-align: right;"><?= number_format($utilidadTotal, 2) ?></td>
                <td class="<?= $clase ?> resulOperativo" style="text-align: right;"><?= number_format($porRenta, 2) ?></td>
            </tr>
    <?php
        endforeach;
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
                <td colspan="3">TOTALES</td>
                <td style="text-align: right;"><?= $totCantidad ?></td>

                <td style="text-align: right;"><?= number_format($totCostoCompraSi, 2); ?></td>
                <td style="text-align: right;"><?= number_format($totImpCompra, 2); ?></td>
                <td style="text-align: right;"><?= number_format($totCostoCompraImp, 2); ?></td>

                <td style="text-align: right;"><?= number_format($totCostoCompraCantSi, 2) ?></td>
                <td style="text-align: right;"><?= number_format($totImpCompraCant, 2) ?></td>
                <td style="text-align: right;"><?= number_format($totalCostoTotal, 2) ?></td>

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
        TablesDatatables.init(0);

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

        var win = window.open('<?= base_url()?>reporte_ventas/margenUtilidad/pdf?data=' + JSON.stringify(data), '_blank');
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

        var win = window.open('<?= base_url()?>reporte_ventas/margenUtilidad/excel?data=' + JSON.stringify(data), '_blank');
        win.focus();
    }
</script>