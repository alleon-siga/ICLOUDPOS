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
<div class="table-responsive">
    <table class='table dataTable table-bordered no-footer tableStyle' style="overflow:scroll">
        <thead>
            <tr>
                <th rowspan="2" style="vertical-align: middle;"># Venta</th>
                <th rowspan="2" style="vertical-align: middle;">Local</th>
                <th rowspan="2" style="vertical-align: middle;">Fecha</th>
                <th rowspan="2" style="vertical-align: middle;">Proveedor</th>
                <th rowspan="2" style="vertical-align: middle;">Producto</th>
                <th rowspan="2" style="vertical-align: middle;">Unidad</th>

                <th colspan="3" class="compraxunidad">Compras x Unidad</th>
                <th colspan="3" class="ventaxunidad">Ventas x Unidad</th>
                <th colspan="3" class="compraxcantidad">Compras x Cantidad</th>
                <th rowspan="2" style="vertical-align: middle;">Cantidad</th>
                <th colspan="3" class="ventaxcantidad">Ventas x Cantidad</th>
                <th colspan="3" class="resulOperativo">Resultado Operativo</th>
            </tr>
            <tr>
                <th class="compraxunidad">Costo unitario</th>
                <th class="compraxunidad">Impuesto</th>
                <th class="compraxunidad">Costo + Impuesto</th>

                <th class="ventaxunidad">Precio unitario</th>
                <th class="ventaxunidad">Impuesto</th>
                <th class="ventaxunidad">Precio + Impuesto</th>

                <th class="compraxcantidad">Subtotal</th>
                <th class="compraxcantidad">Impuesto</th>
                <th class="compraxcantidad">Costo Total</th>

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
        //Ventas
        if($ingreso->tipo_impuesto=='1'){ //incluye impuesto
            $precioVenta = $ingreso->detalle_importe; //precio de venta
            $costoVentaSi = ($precioVenta / $cantidad) / $impuesto; //Costo de venta unitario sin impuesto
            $impVenta = ($precioVenta / $cantidad) - $costoVentaSi;
            $costoVenta = $costoVentaSi * $impuesto; //Costo de venta unitario con impuesto
        }elseif($ingreso->tipo_impuesto=='2'){ //agregar impuesto
            $precioVenta = $ingreso->detalle_importe * $impuesto;
            $costoVentaSi = ($precioVenta / $cantidad) / $impuesto; //Costo de venta unitario sin impuesto
            $impVenta = ($precioVenta / $cantidad) - $costoVentaSi;
            $costoVenta = $costoVentaSi * $impuesto; //Costo de venta unitario con impuesto
        }else{ //no incluye impuesto
            $precioVenta = $ingreso->detalle_importe; //precio de venta
            $costoVentaSi = ($precioVenta / $cantidad);
            $impVenta = ($precioVenta / $cantidad) - $costoVentaSi;
            $costoVenta = $costoVentaSi;
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
        $impCompraCant = $costoCompraCantSi * $impuesto; //impuesto
        $costoTotal = $costoCompraCantSi + $impCompraCant; //Costo total
        //ventas x cantidad
        $costoVentaCantSi = $costoVentaSi * $cantidad; //subtotal
        $impVentaCant = $costoVentaCantSi * $impuesto; //impuesto
        $costoTotalCant = $costoVentaCantSi + $impVentaCant; //venta total
    
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
                <td class="<?= $clase ?>" style="text-align: right;"><?= $ingreso->venta_id ?></td>
                <td class="<?= $clase ?>"><?= $ingreso->local_nombre ?></td>
                <td class="<?= $clase ?>"><?= $ingreso->fecha ?></td>
                <td class="<?= $clase ?>"><?= $ingreso->proveedor_nombre ?></td>
                <td class="<?= $clase ?>"><?= $ingreso->producto_nombre ?></td>
                <td class="<?= $clase ?>"><?= $ingreso->nombre_unidad ?></td>

                <td class="<?= $clase ?> compraxunidad" style="text-align: right;"><?= number_format($costoCompraSi, 2) ?></td>
                <td class="<?= $clase ?> compraxunidad" style="text-align: right;"><?= number_format($impCompra, 2) ?></td>
                <td class="<?= $clase ?> compraxunidad" style="text-align: right;"><?= number_format($costoCompraImp, 2) ?></td>

                <td class="<?= $clase ?> ventaxunidad" style="text-align: right;"><?= number_format($costoVentaSi, 2) ?></td>
                <td class="<?= $clase ?> ventaxunidad" style="text-align: right;"><?= number_format($impVenta, 2) ?></td>
                <td class="<?= $clase ?> ventaxunidad" style="text-align: right;"><?= number_format($costoVenta, 2) ?></td>

                <td class="<?= $clase ?> compraxcantidad" style="text-align: right;"><?= number_format($costoCompraCantSi, 2) ?></td>
                <td class="<?= $clase ?> compraxcantidad" style="text-align: right;"><?= number_format($impCompraCant, 2) ?></td>
                <td class="<?= $clase ?> compraxcantidad" style="text-align: right;"><?= number_format($costoTotal, 2) ?></td>

                <td class="<?= $clase ?>" style="text-align: right;"><?= number_format($cantidad, 2) ?></td>

                <td class="<?= $clase ?> ventaxcantidad" style="text-align: right;"><?= number_format($costoVentaCantSi, 2) ?></td>
                <td class="<?= $clase ?> ventaxcantidad" style="text-align: right;"><?= number_format($impVentaCant, 2) ?></td>
                <td class="<?= $clase ?> ventaxcantidad" style="text-align: right;"><?= number_format($costoTotalCant, 2) ?></td>

                <td class="<?= $clase ?> resulOperativo" style="text-align: right;"><?= number_format($utilidadXund, 2) ?></td>
                <td class="<?= $clase ?> resulOperativo" style="text-align: right;"><?= number_format($utilidadTotal, 2) ?></td>
                <td class="<?= $clase ?> resulOperativo" style="text-align: right;"><?= number_format($porRenta, 2) ?></td>
            </tr>
    <?php
        endforeach;
    ?>
        </tbody>
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

        var win = window.open('<?= base_url()?>reporte_ventas/utilidadProducto/pdf?data=' + JSON.stringify(data), '_blank');
        win.focus();
    }

    function exportar_excel() {
        var data = {
            'local_id': $("#local_id").val(),
            'fecha': $("#fecha").val()
        };

        var win = window.open('<?= base_url()?>reporte_ventas/utilidadProducto/excel?data=' + JSON.stringify(data), '_blank');
        win.focus();
    }
</script>