<?php $ruta = base_url(); ?>
<?php $md = get_moneda_defecto() ?>
<style>
    table tfoot tr td {
        font-weight: bold;
    }
</style>
<ul class="nav nav-tabs">
  <li class="active"><a data-toggle="tab" href="#data">Tabla</a></li>
  <!-- <li><a data-toggle="tab" href="#grafico">Gr&aacute;fico</a></li> -->
</ul>
<div class="tab-content">
    <div id="data" class="tab-pane fade in active">
        <div class="table-responsive">
            <table class='table dataTable table-striped  table-bordered no-footer tableStyle' style="overflow:scroll">
                <thead>
                    <tr>
                        <th><?= getCodigoNombre() ?></th>
                        <th>Nombre</th>
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
                    $totalCostoImpuesto = 0;
                    $totalPrecioImpuesto = 0;
                    $totalCostoTotal = 0;
                    $totalSubTotal = 0;
                    $totalImpuestoV = 0;
                    $totalVentaTotal = 0;
                    $totalUtilidadTotal = 0;
                ?>
                <?php foreach ($lists as $list): ?>
                    <?php
                        $porcImpuesto = $list->porcentaje_impuesto;
                        $cantidadVendida = $list->cantidad;
                        $igv = (100 + $porcImpuesto) / 100;
                        $costoImpuesto = $list->compra;
                        $costoUnitario = $costoImpuesto / $igv;
                        $impuesto = $costoImpuesto - $costoUnitario;
                        $precioImpuesto = $list->precioUnitario;
                        $precioUnitario = $precioImpuesto / $igv;
                        $costoTotal = $cantidadVendida * $costoImpuesto;
                        $subtotal = $cantidadVendida * $precioUnitario;
                        $ventaTotal = $subtotal * $igv;
                        $impuestoV = $ventaTotal - $subtotal;
                        $utilidadUnidad = $precioUnitario - $costoUnitario;
                        $utilidadTotal = $utilidadUnidad * $cantidadVendida;
                        if($list->compra==0){
                            $porcRentabilidad = 0;
                        }else{
                            $porcRentabilidad = ($utilidadUnidad / $costoUnitario) * 100; 
                        }
                        //Totales
                        $totalCostoImpuesto += $costoImpuesto;
                        $totalPrecioImpuesto += $precioImpuesto;
                        $totalCostoTotal += $costoTotal;
                        $totalSubTotal += $subtotal;
                        $totalImpuestoV += $impuestoV;
                        $totalVentaTotal += $ventaTotal;
                        $totalUtilidadTotal += $utilidadTotal;
                    ?>
                    <tr>
                        <td><?= getCodigoValue($list->id_producto, $list->producto_codigo_interno) ?></td>
                        <td><?= $list->producto_nombre ?></td>
                        <td><?= $list->nombre_unidad ?></td>
                        <td style="text-align: right;"><?= number_format($costoUnitario, 2) ?></td>
                        <td style="text-align: right;"><?= number_format($impuesto, 2) ?></td>
                        <td style="text-align: right;"><?= number_format($costoImpuesto, 2) ?></td>
                        <td style="text-align: right;"><?= number_format($porcImpuesto, 2) ?> %</td>
                        <td style="text-align: right;"><?= number_format($precioUnitario, 2) ?></td>
                        <td style="text-align: right;"><?= number_format($precioImpuesto, 2) ?></td>
                        <td style="text-align: right;"><?= number_format($costoTotal, 2) ?></td>
                        <td style="text-align: right;"><?= number_format($cantidadVendida, 0) ?></td>
                        <td style="text-align: right;"><?= number_format($subtotal, 2) ?></td>
                        <td style="text-align: right;"><?= number_format($impuestoV, 2) ?></td>
                        <td style="text-align: right;"><?= number_format($ventaTotal, 2) ?></td>
                        <td style="text-align: right;"><?= number_format($utilidadUnidad, 2) ?></td>
                        <td style="text-align: right;"><?= number_format($utilidadTotal, 2) ?></td>
                        <td style="text-align: right;"><?= number_format($porcRentabilidad, 2) ?> %</td>
                    </tr>
                <?php endforeach ?>
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="3">TOTALES</td>
                    <td></td>
                    <td></td>
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
    </div>
    <div id="grafico" class="tab-pane fade" style="min-width: 310px; max-width: 800px; height: 400px; margin: 0 auto">
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