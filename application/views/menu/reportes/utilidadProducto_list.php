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
                <th># Venta </th>
                <th>Local</th>
                <th>Fecha</th>
                <th>Proveedor</th>
                <th>Producto</th>
                <th>Unidad</th>
                <th>Cantidad</th>
                <th>Compra</th>
                <th>Total 1</th>
                <th>Venta</th>
                <th>Total 2</th>
                <th>Utilidad</th>
            </tr>
        </thead>
        <tbody>
        <?php
            $total1 = $total2 = $Utilidad = $sumTotal1 = $sumTotal2 = $sumUtilidad = 0;
            foreach ($lists as $ingreso):
                $total1 = $ingreso->cantidad * $ingreso->detalle_costo_promedio;
                $total2 = $ingreso->cantidad * $ingreso->precio;
                $Utilidad = $total2 - $total1;
        ?>
            <tr>
                <td><?= $ingreso->venta_id ?></td>
                <td><?= $ingreso->local_nombre ?></td>
                <td><?= $ingreso->fecha ?></td>
                <td><?= $ingreso->proveedor_nombre ?></td>
                <td><?= $ingreso->producto_nombre ?></td>
                <td><?= $ingreso->nombre_unidad ?></td>
                <td><?= $ingreso->cantidad ?></td>
                <td><?= $ingreso->detalle_costo_promedio ?></td>
                <td><?= number_format($total1, 2) ?></td>
                <td><?= $ingreso->precio ?></td>
                <td><?= number_format($total2, 2) ?></td>
                <td><?= $Utilidad ?></td>
            </tr>
        <?php
            $sumTotal1 += $total1;
            $sumTotal2 += $total2;
            $sumUtilidad += $Utilidad;
            endforeach;
        ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="8" align="right" style="font-weight: bold;">Total:</td>
                <td><?= number_format($sumTotal1, 2) ?></td>
                <td></td>
                <td><?= number_format($sumTotal2, 2) ?></td>
                <td><?= number_format($sumUtilidad, 2) ?></td>
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