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
            <th>Id</th>
            <th>Fecha</th>
            <th>Movimiento</th>
            <th>Operaci&oacute;n</th>
            <th>Documento</th>
            <th>Num. Documento</th>
            <th>Ubicaci&oacute;n</th>
            <th>Estado</th>
            <th>Usuario</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($lists as $list): ?>
            <tr>
                <td><?= $list->id ?></td>
                <td><?= $list->fecha ?></td>
                <td><?= $list->io == 1 ? 'ENTRADA' : 'SALIDA' ?></td>
                <td><?= get_sunat_operacion($list->operacion) ?></td>
                <td><?= get_sunat_documento($list->documento) ?></td>
                <td><?= $list->serie . ' - ' . $list->numero ?></td>
                <td><?= $list->local_nombre ?></td>
                <td><?= $list->estado ?></td>
                <td><?= $list->nombre ?></td>
                <td>
                    <a href="#" onclick="verAjuste(<?= $list->id ?>)" style="margin-right: 5px;">
                        <i class="fa fa-search"></i>
                    </a>
                </td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="historial_detalle_modal" style="width: 85%; overflow: auto;
  margin: auto;" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">


</div>

<div class="row">
    <div class="col-md-12">
        <br>
        <button type="button" id="exportar_excel" title="Exportar Excel" class="btn btn-success btn-md">
            <i class="fa fa-file-excel-o fa-fw"></i>
        </button>
        <button type="button" id="exportar_pdf" title="Exportar Pdf" class="btn btn-danger btn-md">
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

    function verAjuste(id){
        $('#historial_detalle_modal').load('<?= base_url('ajuste/ver_ajuste_detalle')?>/' + id, function(){
            $('#historial_detalle_modal').modal('show');
        })
    }

    function exportar_pdf() {
        var data = {
            local_id: $("#venta_local").val(),
            fecha: $("#date_range").val(),
            moneda_id: $("#moneda_id").val(),
            io: $("#io").val()
        };

        var win = window.open('<?= base_url()?>ajuste/historial/pdf?data=' + JSON.stringify(data), '_blank');
        win.focus();
    }

    function exportar_excel() {
        var data = {
            local_id: $("#venta_local").val(),
            fecha: $("#date_range").val(),
            moneda_id: $("#moneda_id").val(),
            io: $("#io").val()
        };

        var win = window.open('<?= base_url()?>ajuste/historial/excel?data=' + JSON.stringify(data), '_blank');
        win.focus();
    }
</script>