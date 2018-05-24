<?php $ruta = base_url(); ?>
<?php $md = get_moneda_defecto() ?>
<div class="row">
    <div class="col-md-10"></div>
    <div class="col-md-2">
        <?php
        $total = 0;
        foreach ($facturaciones as $f) {
            $total += $f->total;
        } ?>
        <label>Total: <?= $emisor->moneda_simbolo ?> <span
                    id="total"><?= number_format($total, 2) ?></span></label>
    </div>
</div>
<div class="table-responsive">
    <table class='table table-striped dataTable table-bordered no-footer tableStyle' style="overflow:scroll">
        <thead>
        <tr>
            <th>ID</th>
            <th>Venta Ref.</th>
            <th>Fecha</th>
            <th>Documento</th>
            <th>Numero</th>
            <th>Cliente</th>
            <th>Documento Afect.</th>
            <th>Estado</th>
            <th>Total</th>
            <th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        <?php if (count($facturaciones) > 0): ?>

            <?php foreach ($facturaciones as $f): ?>
                <tr>
                    <td><?= $f->id ?></td>
                    <td><?= $f->ref_id ?></td>
                    <td>
                        <span style="display: none;"><?= date('YmdHis', strtotime($f->fecha)) ?></span>
                        <?= date('d/m/Y', strtotime($f->fecha)) ?>
                    </td>

                    <td><?php
                        if ($f->documento_tipo == '01') echo 'FACTURA';
                        if ($f->documento_tipo == '03') echo 'BOLETA';
                        if ($f->documento_tipo == '07') echo 'NOTA DE CREDITO';
                        if ($f->documento_tipo == '08') echo 'NOTA DE DEBITO';
                        ?></td>
                    <td><?= $f->documento_numero ?>
                    </td>
                    <td><?= $f->cliente_nombre ?></td>
                    <td><?= ($f->documento_tipo == '07' || $f->documento_tipo == '08') ? $f->documento_mod_numero : '-'
                        ?></td>
                    <td><?= $f->estado == 1 ? 'ACEPTADA' : 'PENDIENTE' ?></td>
                    <td style="text-align: right;"><?= $emisor->moneda_simbolo ?> <?= number_format($f->total, 2) ?></td>
                    <td style="text-align: center;">
                        <a class="btn btn-xs btn-default" data-toggle="tooltip" style="margin-right: 5px;"
                           title="Ver" data-original-title="Ver"
                           href="#"
                           onclick="ver('<?= $f->id ?>');">
                            <i class="fa fa-search"></i> Detalles
                        </a>

                        <?php if ($f->estado == 0): ?>
                            <a class="btn btn-xs btn-warning" data-toggle="tooltip" style="margin-right: 5px;"
                               title="Emitir Comprobante" data-original-title="Emitir Comprobante"
                               href="#"
                               onclick="emitir('<?= $f->id ?>');">
                                <i class="fa fa-mail-forward"></i> Enviar
                            </a>
                        <?php endif; ?>

                        <?php if ($f->estado == 1): ?>
                        <a class="btn btn-xs btn-primary" data-toggle="tooltip" style="margin-right: 5px;"
                           title="Exportar Pdf" data-original-title="Exportar Pdf"
                           href="#"
                           onclick="imprimir('<?= $f->id ?>');">
                            <i class="fa fa-file-pdf-o"></i> PDF

                            <a class="btn btn-sm btn-primary" data-toggle="tooltip" style="margin-right: 5px;"
                               title="Imprimir" data-original-title="Imprimir"
                               href="#"
                               onclick="imprimir_ticket('<?= $f->id ?>');">
                                <i class="fa fa-print"></i>
                                <?php endif; ?>
                            </a>
                    </td>
                </tr>
            <?php endforeach ?>
        <?php endif; ?>

        </tbody>
    </table>


    <a id="exportar_pdf"
       href="#"
       class="btn  btn-default btn-lg" data-toggle="tooltip" title="Exportar a PDF"
       data-original-title="fa fa-file-pdf-o"><i class="fa fa-file-pdf-o fa-fw"></i></a>

    <a id="exportar_excel"
       href="#"
       class="btn btn-default btn-lg" data-toggle="tooltip" title="Exportar a Excel"
       data-original-title="fa fa-file-excel-o"><i class="fa fa-file-excel-o fa-fw"></i></a>


    <div class="modal fade" id="dialog_venta_detalle" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false"
         aria-hidden="true">

    </div>


    <div class="modal fade" id="dialog_venta_imprimir" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false"
         aria-hidden="true">

    </div>

    <div class="modal fade" id="dialog_venta_facturar" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false"
         aria-hidden="true">

    </div>

</div>

<iframe style="display: block;" id="imprimir_frame" src="" frameborder="YES" height="0" width="0"
        border="0" scrolling=no>

</iframe>


<script type="text/javascript">
    $(function () {

        $('#exportar_excel').on('click', function (e) {
            e.preventDefault();
            exportar_excel();
        });

        $("#exportar_pdf").on('click', function (e) {
            e.preventDefault();
            exportar_pdf();
        });

        TablesDatatables.init(2);

    });

    function exportar_pdf() {

        var data = {
            'local_id': $("#local_id").val(),
            'esatdo': $("#estado").val(),
            'fecha': $("#date_range").val(),
            'moneda_id': $("#moneda_id").val(),
        };

        var win = window.open('<?= base_url()?>facturacion/historial_pdf?data=' + JSON.stringify(data), '_blank');
        win.focus();
    }

    function exportar_excel() {
        var data = {
            'local_id': $("#local_id").val(),
            'esatdo': $("#estado").val(),
            'fecha': $("#date_range").val(),
            'moneda_id': $("#moneda_id").val(),
        };

        var win = window.open('<?= base_url()?>facturacion/historial_excel?data=' + JSON.stringify(data), '_blank');
        win.focus();
    }

    function ver(id) {

        $("#dialog_venta_detalle").html($("#loading").html());
        $("#dialog_venta_detalle").modal('show');

        $.ajax({
            url: '<?php echo $ruta . 'facturacion/get_facturacion_detalle'; ?>',
            type: 'POST',
            data: {'id': id},

            success: function (data) {
                $("#dialog_venta_detalle").html(data);
            },
            error: function () {
                alert('Error inesperado')
            }
        });
    }



    function emitir(id) {

        $("#barloadermodal").modal('show');

        $.ajax({
            url: '<?php echo $ruta . 'facturacion/emitir_comprobante'; ?>',
            type: 'POST',
            data: {'id': id},

            success: function (data) {

                if (data.facturacion.estado == 1) {
                    show_msg('success', '<h4>Facturacion Electronica:</h4> ' + data.facturacion.nota);
                }
                else {
                    show_msg('danger', '<h4>Facturacion Electronica:</h4> ' + data.facturacion.nota);
                }

                $("#barloadermodal").modal('hide');
                get_facturacion();
            },
            error: function () {
                alert('Error inesperado')
                $("#barloadermodal").modal('hide');
            }
        });
    }

    function imprimir_ticket(id) {
        $.bootstrapGrowl('<p>IMPRIMIENDO PEDIDO</p>', {
            type: 'success',
            delay: 2500,
            allow_dismiss: true
        });

        var url = '<?=base_url('facturacion/imprimir_ticket')?>/' + id;
        $("#imprimir_frame").attr('src', url);
    }

    function imprimir(id) {

        var win = window.open('<?= base_url()?>facturacion/imprimir/' + id, '_blank');
        win.focus();
    }

</script>