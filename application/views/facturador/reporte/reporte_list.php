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
</style>
<div class="table-responsive">
    <table class='table dataTable table-bordered no-footer tableStyle' style="overflow:scroll">
        <thead>
            <tr>
                <th>Precio Venta</th>
                <th>Costo Compra</th>
                <th>Cantidad Vendida</th>
                <th>Total PV</th>
                <th>Total CC</th>
                <th>Precio Venta</th>
                <th>Costo Compra</th>
                <th>Cantidad Vendida</th>
                <th>Total PV</th>
                <th>Total CC</th>
            </tr>
        </thead>
        <tbody>
    <?php
        foreach ($lists as $ingreso):

    ?>
            <tr>
                <td><?= number_format($ingreso->precio_venta, 2) ?></td>
                <td><?= number_format($ingreso->detalle_costo_ultimo, 2) ?></td>
                <td><?= number_format($ingreso->cantidad, 2) ?></td>
                <td><?= number_format($ingreso->detalle_importe, 2) ?></td>
                <td><?= number_format($ingreso->detalle_importe_cc, 2) ?></td>
                <td><?= number_format($ingreso->precio_venta2, 2) ?></td>
                <td><?= number_format($ingreso->detalle_costo_ultimo2, 2) ?></td>
                <td><?= number_format($ingreso->cantidad2, 2) ?></td>
                <td><?= number_format($ingreso->detalle_importe2, 2) ?></td>
                <td><?= number_format($ingreso->detalle_importe_cc2, 2) ?></td>
            </tr>
    <?php
        endforeach;
    ?>
        </tbody>
        <!--<tfoot>
            <tr>
                <td colspan="8">TOTALES</td>
                <td style="text-align: right;"><?// number_format($totalCostoImpuesto, 2) ?></td>
                <td></td>
                <td></td>
                <td style="text-align: right;"><?// number_format($totalPrecioImpuesto, 2) ?></td>
                <td style="text-align: right;"><?// number_format($totalCostoTotal, 2) ?></td>
                <td></td>
                <td style="text-align: right;"><?// number_format($totalSubTotal, 2) ?></td>
                <td style="text-align: right;"><?// number_format($totalImpuestoV, 2) ?></td>
                <td style="text-align: right;"><?// number_format($totalVentaTotal, 2) ?></td>
                <td></td>
                <td style="text-align: right; color: green;"><?// number_format($totalUtilidadTotal, 2) ?></td>
                <td></td>
            </tr>
        </tfoot>-->
    </table>
</div>
<!--<div class="row">
    <div class="col-md-12">
        <br>
        <button type="button" id="exportar_excel" title="Exportar Excel" class="btn btn-primary">
            <i class="fa fa-file-excel-o fa-fw"></i>
        </button>
        <button type="button" id="exportar_pdf" title="Exportar Pdf" class="btn btn-primary">
            <i class="fa fa-file-pdf-o fa-fw"></i>
        </button>
    </div>
</div>-->
<script type="text/javascript">
    $(document).ready(function () {
        //TablesDatatables.init(0, 'asc');

        /*$('#exportar_excel').on('click', function () {
            exportar_excel();
        });

        $("#exportar_pdf").on('click', function () {
            exportar_pdf();
        });*/
    });

    /*function exportar_pdf() {
        var data = {
            'local_id': $("#local_id").val(),
            'fecha': $("#fecha").val()
        };

        var win = window.open('<? //base_url()?>facturador/reporte/reporte/pdf?data=' + JSON.stringify(data), '_blank');
        win.focus();
    }

    function exportar_excel() {
        var data = {
            'local_id': $("#local_id").val(),
            'fecha': $("#fecha").val()
        };

        var win = window.open('<? //base_url()?>facturador/reporte/reporte/excel?data=' + JSON.stringify(data), '_blank');
        win.focus();
    }*/
</script>