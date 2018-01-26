<div class="modal-dialog" style="width: 60%">
    <div class="modal-content">
        <div class="modal-header">
            <h4>Imprimir Venta</h4>
        </div>
        <div class="modal-footer">

            <div class="row">
                <div class="col-md-4" style="margin: 0; text-align: left;">
                    <?php if (validOption('ACTIVAR_SHADOW', 1) && $venta->documento_id != 6): ?>
                        <button class="btn btn-default btn_venta_imprimir_sc"
                                type="button"
                                id="btn_venta_imprimir_sc"><i
                                class="fa fa-print"></i>
                            Imprimir Contable
                        </button>

                        <button class="btn btn-default"
                                type="button"
                                id="edit_imprmir_sc"><i
                                class="fa fa-edit"></i>
                        </button>
                    <?php endif; ?>
                </div>
                <div class="col-md-12">
                    <button class="btn btn-primary btn_venta_imprimir"
                            type="button"
                            id="btn_venta_imprimir_1"><i
                            class="fa fa-print"></i> (F6) Pedido
                    </button>
                    <button class="btn btn-primary btn_venta_imprimir_almacen"
                            type="button"
                            id="btn_venta_imprimir_almacen_1"><i
                                class="fa fa-print"></i> Almacen
                    </button>
                    <?php $imprimir_doc = ($venta->condicion_id == 1 || $venta->condicion_id == 2 && $venta->credito_estado == 'PagoCancelado');?>
                    <?php if (($venta->factura_impresa == 0) && ($venta->documento_id == 1 || $venta->documento_id == 3) && $imprimir_doc): ?>
                        <button class="btn btn-primary btn_venta_imprimir_doc"
                                type="button"><i
                                class="fa fa-print"></i> <?= $venta->documento_id == 1 ? 'Factura' : 'Boleta' ?>
                        </button>
                    <?php elseif (($venta->factura_impresa != 0) && ($venta->documento_id == 1 || $venta->documento_id == 3) && $imprimir_doc): ?>
                        <button class="btn btn-warning btn_venta_imprimir_doc"
                                type="button"><i
                                class="fa fa-print"></i> <?= $venta->documento_id == 1 ? 'Factura' : 'Boleta' ?>
                        </button>
                    <?php endif; ?>

                    <!--<button class="btn btn-default btn_venta_email_doc"
                            type="button"><i
                            class="fa fa-mail-forward"></i> Enviar por Email
                    </button>-->

                    <button class="btn btn-danger"
                            type="button"
                            onclick="$('#dialog_venta_imprimir').modal('hide');"><i
                            class="fa fa-close"></i> Cancelar
                    </button>
                </div>
            </div>
        </div>
        <div class="modal-body">
            <form id="form_imprimir"
                  target="_blank"
                  method="post"
                  action="<?php echo base_url('venta_new/imprimir'); ?>">
                <input type="hidden" id="venta_id" name="venta_id" value="<?= $venta->venta_id ?>">
                <input type="hidden" id="tipo_impresion" name="tipo_impresion" value="">
            </form>
            <div class="row-fluid force-margin">

                <div class="row-fluid">
                    <div class="row">
                        <div class="col-md-2"><label class="control-label">Documento:</label></div>
                        <div class="col-md-3"><?= $venta->documento_nombre ?></div>

                        <div class="col-md-1"></div>

                        <div class="col-md-2"><label
                                class="control-label"><?= 'Venta Nro' ?>
                                :</label></div>
                        <div
                            class="col-md-3"><?= sumCod($venta->venta_id, 6) ?></div>
                    </div>

                    <hr class="hr-margin-5">

                    <div class="row">
                        <div class="col-md-2"><label class="control-label">Fecha:</label></div>
                        <div class="col-md-3"><?= date('d/m/Y H:i:s', strtotime($venta->venta_fecha)) ?></div>

                        <div class="col-md-1"></div>

                        <div class="col-md-2"><label class="control-label">Tipo de Pago:</label></div>
                        <div class="col-md-3"><?= $venta->condicion_nombre ?></div>
                    </div>


                    <?php if ($venta->condicion_id == '2'): ?>
                        <hr class="hr-margin-5">
                        <div class="row">
                            <div class="col-md-2"><label class="control-label">Cr&eacute;dito Deuda:</label></div>
                            <div
                                class="col-md-3">
                                <?= $venta->moneda_simbolo ?> <?= $venta_action == 'caja' ? $venta->total : $venta->credito_pendiente ?>
                            </div>

                            <div class="col-md-1"></div>

                            <div class="col-md-2"><label class="control-label">Cr&eacute;dito Pagado:</label></div>
                            <div class="col-md-3"><?= $venta->moneda_simbolo . " " . $venta->credito_pagado ?></div>
                        </div>
                    <?php endif; ?>

                    <hr class="hr-margin-5">

                    <div class="row">
                        <div class="col-md-2"><label class="control-label">Cliente:</label></div>
                        <div class="col-md-3"><?= $venta->cliente_nombre ?></div>

                        <div class="col-md-1"></div>

                        <div class="col-md-2"><label class="control-label">Vendedor:</label></div>
                        <div class="col-md-3"><?= $venta->vendedor_nombre ?></div>
                    </div>

                    <hr class="hr-margin-5">

                    <div class="row">
                        <div class="col-md-2"><label class="control-label">Moneda:</label></div>
                        <div class="col-md-3"><?= $venta->moneda_nombre ?></div>

                        <div class="col-md-1"></div>

                        <div class="col-md-2"><label class="control-label">Moneda Tasa:</label></div>
                        <div class="col-md-3"><?= $venta->moneda_tasa ?></div>
                    </div>

                    <hr class="hr-margin-5">

                    <div class="row">
                        <div class="col-md-2"><label class="control-label">Estado:</label></div>
                        <div class="col-md-3"><?= $venta->venta_estado ?></div>

                        <div class="col-md-1"></div>

                        <div class="col-md-2"><label class="control-label">Venta Total:</label></div>
                        <div class="col-md-3"><?= $venta->moneda_simbolo . " " . $venta->total ?></div>
                    </div>

                    <hr class="hr-margin-5">

                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th><?= getCodigoNombre() ?></th>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>UM</th>
                            <th>Precio</th>
                            <th>Subtotal</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($venta->detalles as $detalle): ?>
                            <tr>
                                <td><?= getCodigoValue($detalle->producto_id, $detalle->producto_codigo_interno) ?></td>
                                <td><?= $detalle->producto_nombre ?></td>
                                <td><?= $detalle->cantidad ?></td>
                                <td><?= $detalle->unidad_nombre ?></td>
                                <td style="text-align: right"><?= $detalle->precio ?></td>
                                <td style="text-align: right"><?= $venta->moneda_simbolo . " " . $detalle->importe ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            </div>
            <iframe style="display: none;" id="imprimir_frame" src="" frameborder="YES" height="200" width="100%"
                    border="0" scrolling=no>

            </iframe>
        </div>
        <div class="modal-footer">

            <div class="row">
                <div class="col-md-12">
                    <button class="btn btn-primary btn_venta_imprimir"
                            type="button"
                            id="btn_venta_imprimir_1"><i
                            class="fa fa-print"></i> (F6) Pedido
                    </button>

                    <button class="btn btn-primary btn_venta_imprimir_almacen"
                            type="button"
                            id="btn_venta_imprimir_almacen_2"><i
                                class="fa fa-print"></i> Almacen
                    </button>

                    <?php if (($venta->factura_impresa == 0) && ($venta->documento_id == 1 || $venta->documento_id == 3) && $imprimir_doc): ?>
                        <button class="btn btn-primary btn_venta_imprimir_doc"
                                type="button"><i
                                class="fa fa-print"></i> <?= $venta->documento_id == 1 ? 'Factura' : 'Boleta' ?>
                        </button>
                    <?php elseif (($venta->factura_impresa != 0) && ($venta->documento_id == 1 || $venta->documento_id == 3) && $imprimir_doc): ?>
                       <button class="btn btn-warning btn_venta_imprimir_doc"
                                type="button"><i
                                class="fa fa-print"></i> <?= $venta->documento_id == 1 ? 'Factura' : 'Boleta' ?>
                        </button>
                    <?php endif; ?>

                    <button class="btn btn-default btn_venta_email_doc"
                            type="button"><i
                            class="fa fa-mail-forward"></i> Enviar por Email
                    </button>

                    <button class="btn btn-danger"
                            type="button"
                            onclick="$('#dialog_venta_imprimir').modal('hide');"><i
                            class="fa fa-close"></i> Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="dialog_edit_contable" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false"
     aria-hidden="true">

</div>


<script>
    $(document).ready(function () {

        $(document).keydown(function (e) {

            if (e.keyCode == 117 && $("#dialog_venta_imprimir").is(":visible") == true) {
                e.preventDefault();
                e.stopImmediatePropagation();
            }
        });

        $(document).keyup(function (e) {

            if (e.keyCode == 117 && $("#dialog_venta_imprimir").is(":visible") == true) {
                e.preventDefault();
                e.stopImmediatePropagation();
                $("#btn_venta_imprimir_1").click();
            }
        });

        $(".btn_venta_imprimir").on('click', function () {
            $.bootstrapGrowl('<p>IMPRIMIENDO PEDIDO</p>', {
                type: 'success',
                delay: 2500,
                allow_dismiss: true
            });

            var url = '<?=base_url('venta_new/imprimir/' . $venta->venta_id . '/PEDIDO')?>';
            $("#imprimir_frame").attr('src', url);

        });

        $(".btn_venta_imprimir_almacen").on('click', function () {
            $.bootstrapGrowl('<p>IMPRIMIENDO PEDIDO ALMACEN</p>', {
                type: 'success',
                delay: 2500,
                allow_dismiss: true
            });

            var url = '<?=base_url('venta_new/imprimir/' . $venta->venta_id . '/ALMACEN')?>';
            $("#imprimir_frame").attr('src', url);

        });

        $(".btn_venta_imprimir_doc").on('click', function () {
            $.bootstrapGrowl('<p>IMPRIMIENDO DOCUMENTO</p>', {
                type: 'success',
                delay: 2500,
                allow_dismiss: true
            });

            var url = '<?=base_url('venta_new/imprimir/' . $venta->venta_id . '/DOCUMENTO')?>';
            $("#imprimir_frame").attr('src', url);

        });

        $(".btn_venta_imprimir_sc").on('click', function () {
            $.bootstrapGrowl('<p>IMPRIMIENDO DOCUMENTO</p>', {
                type: 'success',
                delay: 2500,
                allow_dismiss: true
            });

            var url = '<?=base_url('venta_new/imprimir/' . $venta->venta_id . '/SC')?>';
            $("#imprimir_frame").attr('src', url);

        });

        $("#edit_imprmir_sc").on('click', function (e) {
            e.preventDefault();

            $("#dialog_edit_contable").html($("#loading").html());
            $("#dialog_edit_contable").modal('show');

            $.ajax({
                url: '<?=base_url('venta_new/get_contable_detalle')?>',
                type: 'POST',
                data: {'venta_id': <?=$venta->venta_id?>},

                success: function (data) {
                    $("#dialog_edit_contable").html(data);
                }
            });
        });

    });

</script>