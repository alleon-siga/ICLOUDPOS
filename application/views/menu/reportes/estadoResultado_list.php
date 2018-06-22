<?php $ruta = base_url(); ?>
<?
    /*echo "<pre>";
    echo print_r($lists);
    echo "</pre>";*/
?>
<div class="row">
    <div class="col-md-12">
        <table class="table-responsive table">
            <tr>
                <td>VENTAS</td>
                <td><?= $lists['simbolo'].' '.number_format($lists['ventas'], 2) ?></td>
            </tr>
            <tr>
                <td>COSTO DE VENTAS</td>
                <td><?= $lists['simbolo'].' '.number_format($lists['costo'], 2) ?></td>
            </tr>
            <tr>
                <td style="background-color: #ccc; font-weight: bold;">MARGEN BRUTO</td>
                <td style="background-color: #ccc; font-weight: bold;"><?= $lists['simbolo'].' '.number_format($lists['margen_bruto'], 2) ?></td>
            </tr>
        </table>
    </div>
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
    });

    function exportar_pdf() {
        var data = {
            'local_id': $("#local_id").val(),
            'year': $("#year").val(),
            'mes': $("#mes").val(),
            'moneda_id': $("#moneda_id").val()
        };

        var win = window.open('<?= base_url()?>reporte/estadoResultado/pdf?data=' + JSON.stringify(data), '_blank');
        win.focus();
    }

    function exportar_excel() {
        var data = {
            'local_id': $("#local_id").val(),
            'year': $("#year").val(),
            'mes': $("#mes").val(),
            'moneda_id': $("#moneda_id").val()
        };

        var win = window.open('<?= base_url()?>reporte/estadoResultado/excel?data=' + JSON.stringify(data), '_blank');
        win.focus();
    }
</script>