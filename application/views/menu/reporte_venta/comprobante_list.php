<?php $ruta = base_url(); ?>
<?php $md = get_moneda_defecto() ?>
<style>
    table tfoot tr td {
        font-weight: bold;
    }
</style>
<div class="table-responsive">
    <table class='table table-striped dataTable table-bordered no-footer tableStyle' style="overflow:scroll">
        <thead>
        <tr>
            <th>Identificaci&oacute;n</th>
            <th>Documento</th>
            <th>Cliente</th>
            <th>No. Comprobante</th>
            <th>Tipo Comprobante</th>
            <th>Impuesto</th>
            <th>Total</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $total = 0;
        $total_impuesto = 0;
        ?>
        <?php foreach ($lists as $list): ?>
            <?php
            $total += $list->total;
            $total_impuesto += $list->impuesto;
            ?>
            <tr>
                <td><?= $list->identificacion ?></td>
                <?php
                $doc = 'NP ';
                if ($list->documento_id == 1) $doc = 'FA ';
                if ($list->documento_id == 3) $doc = 'BO ';
                ?>
                <td><?= $doc . $list->serie . '-' . sumCod($list->numero, 6) ?></td>
                <td><?= $list->cliente_nombre ?></td>
                <td><?= $list->comprobante_numero ?></td>
                <td><?= $list->comprobante_nombre ?></td>
                <td><?= $moneda->simbolo . ' ' . number_format($list->impuesto, 2) ?></td>
                <td><?= $moneda->simbolo . ' ' . number_format($list->total, 2) ?></td>
            </tr>
        <?php endforeach ?>
        </tbody>
        <tfoot>
        <tr>
            <td colspan="5">TOTALES</td>
            <td><?= $moneda->simbolo . ' ' . number_format($total_impuesto, 2) ?></td>
            <td><?= $moneda->simbolo . ' ' . number_format($total, 2) ?></td>
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
    $(function () {

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
            moneda_id: $("#moneda_id").val(),
            comprobante_id: $("#comprobante_id").val()
        };

        var win = window.open('<?= base_url()?>reporte_ventas/comprobante/pdf?data=' + JSON.stringify(data), '_blank');
        win.focus();
    }

    function exportar_excel() {
        var data = {
            local_id: $("#venta_local").val(),
            fecha: $("#date_range").val(),
            moneda_id: $("#moneda_id").val(),
            comprobante_id: $("#comprobante_id").val()
        };

        var win = window.open('<?= base_url()?>reporte_ventas/comprobante/excel?data=' + JSON.stringify(data), '_blank');
        win.focus();
    }
</script>