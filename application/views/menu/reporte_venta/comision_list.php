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

            <?php foreach ($lists as $list): ?>
                <tr>
                    <td><?= $list->vendedor_id ?></td>
                    <td><?= $list->vendedor_nombre ?></td>
                    <td><?= $moneda->simbolo.' '.number_format($list->total_venta, 2) ?></td>
                    <td><?= number_format($list->comision, 2) ?></td>
                    <td><?= $moneda->simbolo.' '.number_format($list->importe_comision, 2) ?></td>
                </tr>
            <?php endforeach ?>

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
    $(function () {
        TablesDatatables.init(0);
        $('#exportar_excel').on('click', function () {
            exportar_excel();
        });

        $("#exportar_pdf").on('click', function () {
            exportar_pdf();
        });

    });

    function exportar_pdf() {
        var data = {
            local_id: $("#venta_local").val(),
            fecha: $("#date_range").val(),
            moneda_id: $("#moneda_id").val()
        };

        var win = window.open('<?= base_url()?>reporte_ventas/comision/pdf?data=' + JSON.stringify(data), '_blank');
        win.focus();
    }

    function exportar_excel() {
        var data = {
            local_id: $("#venta_local").val(),
            fecha: $("#date_range").val(),
            moneda_id: $("#moneda_id").val()
        };

        var win = window.open('<?= base_url()?>reporte_ventas/comision/excel?data=' + JSON.stringify(data), '_blank');
        win.focus();
    }
</script>