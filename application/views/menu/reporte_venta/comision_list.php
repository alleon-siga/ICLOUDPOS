<?php $ruta = base_url(); ?>
<?php $md = get_moneda_defecto() ?>
<div class="table-responsive">
    <table class='table table-striped dataTable table-bordered no-footer tableStyle' style="overflow:scroll">
        <thead>
            <tr>
                <th>ID</th>
                <th>Vendedor</th>
                <th>Total Venta</th>
                <th>Comision</th>
                <th>Importe Comision</th>
            </tr>
        </thead>
        <tbody>
            <?php $total_venta = $imp_com = 0; ?>
            <?php foreach ($lists as $list): ?>
                <tr>
                    <td><?= $list->vendedor_id ?></td>
                    <td><?= $list->vendedor_nombre ?></td>
                    <td><?= $moneda->simbolo.' '.number_format($list->total_venta, 2) ?></td>
                    <td><?= number_format($list->comision, 2) ?></td>
                    <td><?= $moneda->simbolo.' '.number_format($list->importe_comision, 2) ?></td>
                </tr>
            <?php $total_venta += $list->total_venta; ?>
            <?php $imp_com += $list->importe_comision; ?>
            <?php endforeach ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2">Total:</td>
                <td><?= $moneda->simbolo.' '.number_format($total_venta, 2); ?></td>
                <td></td>
                <td><?= $moneda->simbolo.' '.number_format($imp_com, 2); ?></td>
            </tr>
        </tfoot>
    </table>
</div>
<div class="row">
    <div class="col-md-12">
        <br>
        <button type="button" id="exportar_excel2" title="Exportar Excel" class="btn btn-success btn-md">
            <i class="fa fa-file-excel-o fa-fw"></i>
        </button>
        <button type="button" id="exportar_pdf2" title="Exportar Pdf" class="btn btn-danger btn-md">
            <i class="fa fa-file-pdf-o fa-fw"></i>
        </button>
    </div>
</div>

<script type="text/javascript">
    $(function () {
        //TablesDatatables.init(0);
        $('#exportar_excel2').on('click', function () {
            exportar_excel2();
        });

        $("#exportar_pdf2").on('click', function () {
            exportar_pdf2();
        });
    });

    function exportar_pdf2() {
        var data = {
            local_id: $("#venta_local").val(),
            fecha: $("#date_range").val(),
            moneda_id: $("#moneda_id").val(),
            usuarios_id: $('#usuarios_id').val()
        };

        var win = window.open('<?= base_url()?>reporte_ventas/comision/pdf?data=' + JSON.stringify(data), '_blank');
        win.focus();
    }

    function exportar_excel2() {
        var data = {
            local_id: $("#venta_local").val(),
            fecha: $("#date_range").val(),
            moneda_id: $("#moneda_id").val(),
            usuarios_id: $('#usuarios_id').val()
        };

        var win = window.open('<?= base_url()?>reporte_ventas/comision/excel?data=' + JSON.stringify(data), '_blank');
        win.focus();
    }
</script>