<style>
    .totales {
        width: 100%;
        text-align: right;
    }

    .totales tr td {
        padding: 5px 0;
        font-weight: bold;
    }
</style>
<div class="modal-dialog" style="width: 60%">
    <div class="modal-content">
        <div class="modal-header">
            <h4>Imprimir Venta</h4>
            <input type="hidden" id="venta_id" value="<?= $venta->venta_id ?>">
        </div>
        <div class="modal-footer">

            <div class="row">
                <div class="col-md-3" style="margin: 0; text-align: left;">
                    <?php if (valueOption('FACTURACION', '0') == 1 && $facturacion_venta != null): ?>
                        <h4>Comprobante Electronico:</h4>
                        <button class="btn btn-xs btn-warning" onclick="imprimir_fe(<?= $facturacion_venta->id ?>)"
                                type="button" ><i class="fa fa-file-pdf-o"></i> A4
                        </button>

                        <button class="btn btn-xs btn-warning"
                                onclick="imprimir_ticket_fe(<?= $facturacion_venta->id ?>)"
                                type="button"
                                id="fe_imprimir_pdf"><i class="fa fa-print"></i> Ticket 
                        </button>
                    <?php endif; ?>
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
                <div class="col-md-9">
                    <?php if (ENV == 'DEV' && false): ?>
                        <button class="btn btn-primary" data-id="<?= $venta->venta_id ?>"
                                type="button"
                                id="btn_venta_imprimir_test"><i class="fa fa-print"></i> IMPRIMIR TEST
                        </button>
                    <?php endif; ?>
                    <?php if(count($traspaso)>0){ ?>
                    <button class="btn btn-primary btn_venta_imprimir"
                            type="button" data-nombre="TRASPASO"
                            id="btn_venta_imprimir_2"><i
                                class="fa fa-print"></i> Traspaso
                    </button>
                    <?php } ?>
                    <button class="btn btn-primary btn_venta_imprimir"
                            type="button" data-nombre="A4"
                            id="btn_venta_imprimir_1"><i
                                class="fa fa fa-file-pdf-o"></i> A4 NV
                    </button>
                    <button class="btn btn-primary btn_venta_imprimir"
                            type="button" data-nombre="PEDIDO"
                            id="btn_venta_imprimir_1"><i
                                class="fa fa-print"></i> (F6) Ticket NV
                    </button>
                    <button class="btn btn-primary btn_venta_imprimir_almacen imprimir"
                            type="button" data-nombre="guia"
                            id="btn_venta_imprimir_almacen_1"><i
                                class="fa fa-print"></i> Gu&iacute;a de Remisi&oacute;n
                    </button>
                    <?php $imprimir_doc = ($venta->condicion_id == 1 || $venta->condicion_id == 2); ?>
                    <?php if (($venta->factura_impresa == 0) && ($venta->documento_id == 1 || $venta->documento_id == 3) && $imprimir_doc): ?>
                        <button id="btn_venta_imprimir_almacen_3" class="btn btn-primary btn_venta_imprimir_doc imprimir"
                                data-nombre="<?= $venta->documento_id == 1 ? 'factura' : 'boleta' ?>"
                                type="button"><i
                                    class="fa fa-print"></i> <?= $venta->documento_id == 1 ? 'Factura' : 'Boleta' ?>
                        </button>
                    <?php elseif (($venta->factura_impresa != 0) && ($venta->documento_id == 1 || $venta->documento_id == 3) && $imprimir_doc): ?>
                        <button id="btn_venta_imprimir_almacen_3" class="btn btn-warning btn_venta_imprimir_doc imprimir"
                                data-nombre="<?= $venta->documento_id == 1 ? 'factura' : 'boleta' ?>"
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
                            onclick="close_previa_modal()"><i
                                class="fa fa-close"></i> Cerrar
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

                <?php if ($venta->condicion_id == '1'): ?>
                    <div class="row-fluid">
                        <div class="row">
                            <div class="col-md-2"><label class="control-label">Venta Nro:</label></div>
                            <div class="col-md-3"><?= sumCod($venta->venta_id, 6) ?></div>

                            <div class="col-md-1"></div>

                            <div class="col-md-2"><label
                                        class="control-label">Documento:</label>
                            </div>
                            <div
                                    class="col-md-3">
                                <?php
                                $doc = '';
                                if ($venta->documento_id == 1) $doc = "FA";
                                if ($venta->documento_id == 2) $doc = "NC";
                                if ($venta->documento_id == 3) $doc = "BO";
                                if ($venta->documento_id == 4) $doc = "GR";
                                if ($venta->documento_id == 5) $doc = "PCV";
                                if ($venta->documento_id == 6) $doc = "NV";
                                if ($venta->numero != '')
                                    echo $doc . ' ' . $venta->serie . '-' . sumCod($venta->numero, 6);
                                else
                                    echo '<span style="color: #0000FF">NO EMITIDO</span>';
                                ?>
                            </div>
                        </div>

                        <hr class="hr-margin-5">

                        <?php if ($venta->comprobante_id > 0): ?>
                            <div class="row">
                                <div class="col-md-2"><label class="control-label">Comprobante:</label></div>
                                <div class="col-md-3"><?= $venta->comprobante_nombre ?></div>

                                <div class="col-md-1"></div>

                                <div class="col-md-2"><label
                                            class="control-label">Comp. Nro.:</label></div>
                                <div
                                        class="col-md-3"><?= $venta->comprobante ?></div>
                            </div>

                            <hr class="hr-margin-5">
                        <?php endif; ?>

                        <div class="row">
                            <div class="col-md-2"><label class="control-label">Fecha:</label></div>
                            <div class="col-md-3"><?= date('d/m/Y H:i:s', strtotime($venta->venta_fecha)) ?></div>

                            <div class="col-md-1"></div>

                            <div class="col-md-2"><label class="control-label">Moneda:</label></div>
                            <div class="col-md-3"><?= $venta->moneda_nombre ?></div>
                        </div>

                        <hr class="hr-margin-5">

                        <div class="row">
                            <div class="col-md-2"><label class="control-label">Cliente:</label></div>
                            <div class="col-md-3"><?= $venta->cliente_nombre ?></div>

                            <div class="col-md-1"></div>

                            <div class="col-md-2"><label class="control-label">Tipo de Pago:</label></div>
                            <div class="col-md-3"><?= $venta->condicion_nombre ?></div>
                        </div>

                        <hr class="hr-margin-5">

                        <div class="row">
                            <div class="col-md-2"><label class="control-label">Vendedor:</label></div>
                            <div class="col-md-3"><?= $venta->vendedor_nombre ?></div>

                            <div class="col-md-1"></div>

                            <div class="col-md-2"><label class="control-label">Tipo de Cambio:</label></div>
                            <div class="col-md-3"><?= $venta->moneda_tasa ?></div>
                        </div>

                        <hr class="hr-margin-5">

                        <div class="row">
                            <div class="col-md-2"><label class="control-label">Estado:</label></div>
                            <div class="col-md-3"  <?=$venta->venta_estado=="ANULADO"?'style="color:red !important;"':''?>><?= $venta->venta_estado ?></div>

                            <div class="col-md-1"></div>

                            <div class="col-md-2"><label class="control-label">Venta Total:</label></div>
                            <div class="col-md-3"><?= $venta->moneda_simbolo . " " . $venta->total ?></div>
                        </div>

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
                                    <td><?= $detalle->producto_cualidad=="PESABLE"?$detalle->cantidad:number_format($detalle->cantidad,0) ?></td>
                                    <td><?= $detalle->unidad_nombre ?></td>
                                    <td style="text-align: right"><?= $detalle->precio ?></td>
                                    <td style="text-align: right"><?= $venta->moneda_simbolo . " " . $detalle->importe ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>


                <?php if ($venta->condicion_id == '2'): ?>
                    <div class="row-fluid">

                        <div class="row">
                            <div class="col-md-2"><label class="control-label">Venta Nro:</label></div>
                            <div class="col-md-3"><?= sumCod($venta->venta_id, 6) ?></div>

                            <div class="col-md-1"></div>

                            <div class="col-md-2"><label
                                        class="control-label">Documento:</label>
                            </div>
                            <div
                                    class="col-md-3">
                                <?php
                                $doc = '';
                                if ($venta->documento_id == 1) $doc = "FA";
                                if ($venta->documento_id == 2) $doc = "NC";
                                if ($venta->documento_id == 3) $doc = "BO";
                                if ($venta->documento_id == 4) $doc = "GR";
                                if ($venta->documento_id == 5) $doc = "PCV";
                                if ($venta->documento_id == 6) $doc = "NV";
                                if ($venta->numero != '')
                                    echo $doc . ' ' . $venta->serie . '-' . sumCod($venta->numero, 6);
                                else
                                    echo '<span style="color: #0000FF">NO EMITIDO</span>';
                                ?>
                            </div>
                        </div>

                        <hr class="hr-margin-5">

                        <?php if ($venta->comprobante_id > 0): ?>
                            <div class="row">
                                <div class="col-md-2"><label class="control-label">Comprobante:</label></div>
                                <div class="col-md-3"><?= $venta->comprobante_nombre ?></div>

                                <div class="col-md-1"></div>

                                <div class="col-md-2"><label
                                            class="control-label">Comp. Nro.:</label></div>
                                <div
                                        class="col-md-3"><?= $venta->comprobante ?></div>
                            </div>

                            <hr class="hr-margin-5">
                        <?php endif; ?>

                        <div class="row">
                            <div class="col-md-2"><label class="control-label">Fecha:</label></div>
                            <div class="col-md-3"><?= date('d/m/Y H:i:s', strtotime($venta->venta_fecha)) ?></div>

                            <div class="col-md-1"></div>

                            <div class="col-md-2"><label class="control-label">Moneda:</label></div>
                            <div class="col-md-3"><?= $venta->moneda_nombre ?></div>
                        </div>

                        <hr class="hr-margin-5">

                        <div class="row">
                            <div class="col-md-2"><label class="control-label">Tipo de Pago:</label></div>
                            <div class="col-md-3"><?= $venta->condicion_nombre ?></div>

                            <div class="col-md-1"></div>

                            <div class="col-md-2"><label class="control-label">Importe de Deuda:</label></div>
                            <div
                                    class="col-md-3">
                                <?= $venta->moneda_simbolo ?> <?= $venta_action == 'caja' ? $venta->total : $venta->credito_pendiente ?>
                            </div>
                        </div>

                        <hr class="hr-margin-5">
                        <div class="row">
                            <div class="col-md-2"><label class="control-label">Cliente:</label></div>
                            <div class="col-md-3"><?= $venta->cliente_nombre ?></div>

                            <div class="col-md-1"></div>

                            <div class="col-md-2"><label class="control-label">Importe Inicial:</label></div>
                            <div class="col-md-3"><?= $venta->moneda_simbolo . " " . $venta->inicial ?></div>
                        </div>

                        <hr class="hr-margin-5">

                        <div class="row">
                            <div class="col-md-2"><label class="control-label">Vendedor:</label></div>
                            <div class="col-md-3"><?= $venta->vendedor_nombre ?></div>

                            <div class="col-md-1"></div>

                            <div class="col-md-2"><label class="control-label">Tipo de Cambio:</label></div>
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
                        <div class="row">
                            <div class="col-md-12 text-center">
                                Dias de Gracia: <?= $venta->periodo_gracia ?> /
                                Numero de Cuotas: <?= count($venta->cuotas) ?> /
                                Tasa de Interes: <?= $venta->tasa_interes ?>%
                            </div>
                        </div>
                        <br>
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
                                    <td><?= number_format($detalle->cantidad, 0) ?></td>
                                    <td><?= $detalle->unidad_nombre ?></td>
                                    <td style="text-align: right"><?= $detalle->precio ?></td>
                                    <td style="text-align: right"><?= $venta->moneda_simbolo . " " . $detalle->importe ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <br>
                <div class="row">
                    <div class="col-md-8">
                        <?php if ($venta->condicion_id == '2'): ?>
                            <h4>Cuotas y Vencimientos</h4>
                            <table class="table table-condensed">
                                <thead>
                                <tr>
                                    <th>Letra</th>
                                    <th>Vence</th>
                                    <th>Monto</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($venta->cuotas as $cuota): ?>
                                    <tr>
                                        <td><?= $cuota->nro_letra ?></td>
                                        <td><?= date('d/m/Y', strtotime($cuota->fecha_vencimiento)) ?></td>
                                        <td><?= $venta->moneda_simbolo . ' ' . number_format($cuota->monto, 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>

                        <?php if (valueOption('FACTURACION', '0') == 1 && count($facturacion_notas) > 0): ?>
                            <h4>Notas Electr&oacute;nicas Asociadas:</h4>

                            <?php foreach ($facturacion_notas as $fn): ?>
                                <h5><?= $fn->documento_numero ?>
                                    <button class="btn btn-xs btn-primary" onclick="imprimir_fe(<?= $fn->id ?>)"
                                            type="button"><i class="fa fa-file-pdf-o"></i> PDF
                                    </button>

                                    <button class="btn btn-xs btn-primary"
                                            onclick="imprimir_ticket_fe(<?= $fn->id ?>)"
                                            type="button"
                                            id="fe_imprimir_pdf"><i class="fa fa-print"></i> COMPROBANTE
                                    </button>
                                </h5>

                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-4 text-right">
                        <table class="totales">
                            <tr>
                                <td>Subtotal:</td>
                                <td><?= $venta->moneda_simbolo ?> <?= number_format($venta->subtotal, 2) ?></label></td>
                            </tr>
                            <tr>
                                <td>Descuento:</td>
                                <td><?= $venta->moneda_simbolo ?> <?= number_format($venta->descuento, 2) ?></label></td>
                            </tr>
                            <tr>
                                <td>Impuesto:</td>
                                <td><?= $venta->moneda_simbolo ?> <?= number_format($venta->impuesto, 2) ?></label></td>
                            </tr>
                            <tr>
                                <td>Total:</td>
                                <td><?= $venta->moneda_simbolo ?> <?= number_format($venta->total, 2) ?></label></td>
                            </tr>
                        </table>
                    </div>
                </div>

            </div>
            <iframe style="display: block;" id="imprimir_frame" src="" frameborder="YES" height="0" width="0"
                    border="0" scrolling=no>

            </iframe>

        </div>
        <div class="modal-footer">

            <div class="row">
                <div class="col-md-12">
                    <button class="btn btn-primary btn_venta_imprimir"
                            type="button"
                            data-nombre="PEDIDO"
                            id="btn_venta_imprimir_1"><i
                                class="fa fa-print"></i> (F6) Pedido
                    </button>

                    <button class="btn btn-primary btn_venta_imprimir_almacen imprimir"
                            type="button"
                            data-nombre="guia"
                            id="btn_venta_imprimir_almacen_2"><i
                                class="fa fa-print"></i> Gu&iacute;a de Remisi&oacute;n
                    </button>

                    <?php if (($venta->factura_impresa == 0) && ($venta->documento_id == 1 || $venta->documento_id == 3) && $imprimir_doc): ?>
                        <button id="btn_venta_imprimir_almacen_4" class="btn btn-primary btn_venta_imprimir_doc imprimir"
                                data-nombre="<?= $venta->documento_id == 1 ? 'factura' : 'boleta' ?>"
                                type="button"><i
                                    class="fa fa-print"></i> <?= $venta->documento_id == 1 ? 'Factura' : 'Boleta' ?>
                        </button>
                    <?php elseif (($venta->factura_impresa != 0) && ($venta->documento_id == 1 || $venta->documento_id == 3) && $imprimir_doc): ?>
                        <button id="btn_venta_imprimir_almacen_4" class="btn btn-warning btn_venta_imprimir_doc imprimir"
                                data-nombre="<?= $venta->documento_id == 1 ? 'factura' : 'boleta' ?>"
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
                            onclick="close_previa_modal()"><i
                                class="fa fa-close"></i> Cerrar
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

    function imprimir_fe(id) {

        var win = window.open('<?= base_url()?>facturacion/imprimir/' + id, '_blank');
        win.focus();
    }

    function imprimir_ticket_fe(id) {
        $.bootstrapGrowl('<p>IMPRIMIENDO PEDIDO</p>', {
            type: 'success',
            delay: 2500,
            allow_dismiss: true
        });

        var url = '<?=base_url('facturacion/imprimir_ticket')?>/' + id;
        $("#imprimir_frame").attr('src', url);
    }

    function close_previa_modal() {
        $('#dialog_venta_imprimir').modal('hide');
        $(".modal-backdrop").remove();
    }

    $(document).ready(function () {

        $('.imprimir').on('click', function () {
            var id = $(this).attr('id');
            var nombre = $('#'+id).attr('data-nombre');
            var html = $('#'+id).html();
            $('#'+id).html('<i class="fa fa-print"></i> IMPRIMIENDO...');
            $('#'+id).attr('disabled', 'disabled');
            
            $.ajax({
                url: '<?= base_url()?>impresion/get_venta/' + $('#venta_id').val(),
                success: function (data) {
                    $.ajax({
                        url: '<?= valueOptionDB('HOST_IMPRESION', 'http://localhost:8080') ?>',
                        method: 'POST',
                        data: {
                            documento: nombre,
                            dataset: data
                        },
                        success: function (data) {
                            show_msg('success', 'La venta se esta imprimiendo');
                        },
                        error: function (data) {
                            alert('Error de impresion');
                        },
                        complete: function (data) {
//                            if (data.status == 1) {
//                                show_msg('danger', data.message);
//                            }
                            $('#'+id).removeAttr('disabled');
                            $('#'+id).html(html);
                        }
                    })
                }
            });
        });

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
            var nombre = $(this).attr('data-nombre');
            $.bootstrapGrowl('<p>IMPRIMIENDO ' + nombre + '</p>', {
                type: 'success',
                delay: 2500,
                allow_dismiss: true
            });
            var url = '<?= base_url() ?>venta_new/imprimir/<?= $venta->venta_id ?>/'+nombre;
            if(nombre == "A4"){
                var win = window.open(url, '_blank');
                win.focus();
            }else{
                $("#imprimir_frame").attr('src', url);
            }
        });
//
//        $(".btn_venta_imprimir_almacen").on('click', function () {
//            $.bootstrapGrowl('<p>IMPRIMIENDO PEDIDO ALMACEN</p>', {
//                type: 'success',
//                delay: 2500,
//                allow_dismiss: true
//            });
//
//            var url = '<?//=base_url('venta_new/imprimir/' . $venta->venta_id . '/ALMACEN')?>//';
//            $("#imprimir_frame").attr('src', url);
//
//        });
//
//        $(".btn_venta_imprimir_doc").on('click', function () {
//            $.bootstrapGrowl('<p>IMPRIMIENDO DOCUMENTO</p>', {
//                type: 'success',
//                delay: 2500,
//                allow_dismiss: true
//            });
//
//            var url = '<?//=base_url('venta_new/imprimir/' . $venta->venta_id . '/DOCUMENTO')?>//';
//            $("#imprimir_frame").attr('src', url);
//
//        });
//
//        $(".btn_venta_imprimir_sc").on('click', function () {
//            $.bootstrapGrowl('<p>IMPRIMIENDO DOCUMENTO</p>', {
//                type: 'success',
//                delay: 2500,
//                allow_dismiss: true
//            });
//
//            var url = '<?//=base_url('venta_new/imprimir/' . $venta->venta_id . '/SC')?>//';
//            $("#imprimir_frame").attr('src', url);
//
//        });
//
//        $("#edit_imprmir_sc").on('click', function (e) {
//            e.preventDefault();
//
//            $("#dialog_edit_contable").html($("#loading").html());
//            $("#dialog_edit_contable").modal('show');
//
//            $.ajax({
//                url: '<?//=base_url('venta_new/get_contable_detalle')?>//',
//                type: 'POST',
//                data: {'venta_id': <?//=$venta->venta_id?>//},
//
//                success: function (data) {
//                    $("#dialog_edit_contable").html(data);
//                }
//            });
//        });

    });

</script>