<?php $ruta = base_url(); ?>
<?php $md = get_moneda_defecto() ?>
<div class="row">
    <div class="col-md-10"></div>
    <div class="col-md-2">

    </div>
</div>
<div class="table-responsive">
    <table class='table table-striped dataTable table-bordered no-footer tableStyle' style="overflow:scroll">
        <thead>
        <tr>
            <th>Venta ID</th>
            <th>Fecha</th>
            <th style="width: 35%;">Cliente</th>
            <th>Total</th>
            <th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        <?php if (count($ventas) > 0): ?>

            <?php foreach ($ventas as $v): ?>
                <tr>
                    <td><?= $v->venta_id ?></td>
                    <td>
                        <span style="display: none;"><?= date('YmdHis', strtotime($v->fecha)) ?></span>
                        <?= date('d/m/Y', strtotime($v->fecha)) ?>
                    </td>
                    <td style="white-space: normal;"><?= $v->razon_social ?></td>
                    <td style="text-align: right;"><?= $v->simbolo ?> <?= number_format($v->total, 2) ?></td>
                    <td>
                        <a class="btn btn-sm btn-primary" data-toggle="tooltip" style="margin-right: 5px;"
                           title="Ver Detalles" data-original-title="Ver Detalles"
                           href="#"
                           onclick="ver('<?= $v->venta_id ?>');">
                            <i class="fa fa-list"></i>
                        </a>

                        <a class="btn btn-sm btn-default" data-toggle="tooltip" style="margin-right: 5px;"
                           title="Crear Comprobante Electr&oacute;nico"
                           data-original-title="Crear Comprobante Electr&oacute;nico"
                           href="#"
                           onclick="declarar('<?= $v->venta_id ?>');">
                            <i class="fa fa-plus"></i> SUNAT
                        </a>
                    </td>
                </tr>
            <?php endforeach ?>
        <?php endif; ?>

        </tbody>
    </table>


    <div class="modal fade" id="dialog_venta_detalle" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false"
         aria-hidden="true">

    </div>

    <div class="modal fade" id="dialog_venta_declarar" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false"
         aria-hidden="true">

    </div>

</div>

<iframe style="display: block;" id="imprimir_frame" src="" frameborder="YES" height="0" width="0"
        border="0" scrolling=no>

</iframe>


<script type="text/javascript">
    $(function () {

        $('[data-toggle="tooltip"]').tooltip();
        $('[data-toggle="popover"]').popover({
            trigger: 'hover'
        });

        $('#exportar_excel').on('click', function (e) {
            e.preventDefault();
            exportar_excel();
        });

        $("#exportar_pdf").on('click', function (e) {
            e.preventDefault();
            exportar_pdf();
        });

        TablesDatatables.init(1);

    });

    //        function exportar_pdf() {
    //
    //            var data = {
    //                'local_id': $("#local_id").val(),
    //                'esatdo': $("#estado").val(),
    //                'fecha': $("#date_range").val(),
    //                'moneda_id': $("#moneda_id").val(),
    //            };
    //
    //            var win = window.open('<?//= base_url()?>//facturacion/historial_pdf?data=' + JSON.stringify(data), '_blank');
    //            win.focus();
    //        }
    //
    //        function exportar_excel() {
    //            var data = {
    //                'local_id': $("#local_id").val(),
    //                'esatdo': $("#estado").val(),
    //                'fecha': $("#date_range").val(),
    //                'moneda_id': $("#moneda_id").val(),
    //            };
    //
    //            var win = window.open('<?//= base_url()?>//facturacion/historial_excel?data=' + JSON.stringify(data), '_blank');
    //            win.focus();
    //        }


    function ver(venta_id) {

        $("#dialog_venta_detalle").html($("#loading").html());
        $("#dialog_venta_detalle").modal('show');

        $.ajax({
            url: '<?php echo $ruta . 'venta_new/get_venta_detalle'; ?>',
            type: 'POST',
            data: {'venta_id': venta_id},

            success: function (data) {
                $("#dialog_venta_detalle").html(data);
            },
            error: function () {
                alert('asd')
            }
        });
    }

    function declarar(venta_id) {
        $("#dialog_venta_declarar").html($("#loading").html());
        $("#dialog_venta_declarar").modal('show');

        $.ajax({
            url: '<?php echo $ruta . 'facturacion/notas/declarar'; ?>',
            type: 'POST',
            data: {'venta_id': venta_id},

            success: function (data) {
                $("#dialog_venta_declarar").html(data);
            },
            error: function () {
                alert('asd')
            }
        });
    }
</script>