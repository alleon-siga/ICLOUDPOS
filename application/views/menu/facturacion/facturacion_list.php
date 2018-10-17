<?php if ($emisor == NULL): ?>
    <h4 class="alert alert-danger text-center">Emisor no configurado</h4>
<?php else: ?>
    <?php $ruta = base_url(); ?>
    <?php $md = get_moneda_defecto() ?>
    <div class="row">
        <div class="col-md-10">
            <div class="label <?= $emisor->env == 'PROD' ? 'label-success' : 'label-warning' ?>">
                <?= $emisor->env == 'PROD' ? 'EMISION A SUNAT' : 'EMISION DE PRUEBA' ?>
            </div>
        </div>
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
                <th style="width: 35%;">Cliente</th>
                <th>Estado</th>
                <th>Condicion</th>
                <th>Total</th>
                <th>Acciones</th>
                <th>Impresi&oacute;n</th>
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
                        <td style="white-space: normal;"><?= $f->cliente_nombre ?></td>
                        <td style="white-space: nowrap;">

                            <?php
                            $estado = '';
                            $estado_class = '';
                            if ($f->estado == 0) {
                                $estado_class = 'label-warning';
                                $estado = 'NO GENERADO';
                            } elseif ($f->estado == 1) {
                                $estado_class = 'label-info';
                                $estado = 'GENERADO';
                            } elseif ($f->estado == 2) {
                                $estado_class = 'label-warning';
                                $estado = 'ENVIADO';
                            } elseif ($f->estado == 3) {
                                $estado_class = 'label-success';
                                $estado = 'ACEPTADO';
                            } elseif ($f->estado == 4) {
                                $estado_class = 'label-danger';
                                $estado = 'RECHAZADO';
                            }

                            ?>
                            <div
                                    title="Descripci&oacute;n del Estado" data-content="<?= $f->nota ?>"
                                    data-toggle="popover"
                                    class="label <?= $estado_class ?>"
                                    data-placement="top"
                                    style="font-size: 1em; padding: 2px; cursor: pointer; white-space: nowrap;">
                                <?= $estado ?>
                            </div>
                        </td>
                        <td>

                            <?php
                            $estado = '';
                            $estado_class = '';
                            if ($f->estado_comprobante == 1) {
                                $estado_class = 'label-success';
                                $estado = 'NUEVO';
                            } elseif ($f->estado_comprobante == 2) {
                                $estado_class = 'label-warning';
                                $estado = 'MODIFICADO';
                            } elseif ($f->estado_comprobante == 3) {
                                $estado_class = 'label-danger';
                                $estado = 'ANULADO';
                            }
                            ?>

                            <div
                                    class="label <?= $estado_class ?>"
                                    style="font-size: 1em; padding: 2px; white-space: nowrap;">
                                <?= $estado ?>
                            </div>
                        </td>
                        <td style="text-align: right;"><?= $emisor->moneda_simbolo ?> <?= number_format($f->total, 2) ?></td>
                        <td style="text-align: center;">
                            <?php if ($f->estado == 0): ?>
                                <a class="btn btn-sm btn-warning" data-toggle="tooltip" style="margin-right: 5px;"
                                   title="Actualizar estado" data-original-title="Actualizar estado"
                                   href="#"
                                   onclick="generarComprobante('<?= $f->id ?>')">
                                    <i class="fa fa-refresh"></i>
                                </a>
                            <?php endif; ?>

                            <?php if ($f->estado == 1 || $f->estado == 2 || $f->estado == 3): ?>
                                <a class="btn btn-sm btn-info" data-toggle="tooltip" style="margin-right: 5px;"
                                   title="Descargar comprobante XML" data-original-title="Descargar comprobante XML"
                                   href="#"
                                   onclick="descargar('<?= $f->id ?>')">
                                    <i class="fa fa-download"></i>
                                </a>
                            <?php endif; ?>


                            <?php if ($f->estado == 4): ?>

                            <?php endif; ?>

                        </td>
                        <td>
                            <a class="btn btn-sm btn-primary" data-toggle="tooltip" style="margin-right: 5px;"
                               title="Ver Detalles" data-original-title="Ver Detalles"
                               href="#"
                               onclick="ver('<?= $f->id ?>')">
                                <i class="fa fa-list"></i>
                            </a>

                            <?php if ($f->estado != 0 && $f->estado != 4): ?>
                                <a class="btn btn-sm btn-primary" data-toggle="tooltip" style="margin-right: 5px;"
                                   title="Imprimir PDF (A4)" data-original-title="Imprimir PDF (A4)"
                                   href="#"
                                   onclick="imprimir('<?= $f->id ?>')">
                                    <i class="fa fa-file-pdf-o"></i>
                                </a>

                                <a class="btn btn-sm btn-primary" data-toggle="tooltip" style="margin-right: 5px;"
                                   title="Imprimir Ticket" data-original-title="Imprimir Ticket"
                                   href="#"
                                   onclick="imprimir_ticket('<?= $f->id ?>')">
                                    <i class="fa fa-print"></i>
                                </a>
                            <?php endif; ?>
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

        $('[data-toggle="tooltip"]').tooltip()
        $('[data-toggle="popover"]').popover({
          trigger: 'hover'
        })

        $('#exportar_excel').on('click', function (e) {
          e.preventDefault()
          exportar_excel()
        })

        $('#exportar_pdf').on('click', function (e) {
          e.preventDefault()
          exportar_pdf()
        })

        TablesDatatables.init(2)

      })

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

      function descargar (id) {

        var win = window.open('<?= base_url()?>facturacion/descargar_xml/' + id, '_blank')
        win.focus()
      }

      function ver (id) {

        $('#dialog_venta_detalle').html($('#loading').html())
        $('#dialog_venta_detalle').modal('show')

        $.ajax({
          url: '<?php echo $ruta . 'facturacion/get_facturacion_detalle'; ?>',
          type: 'POST',
          data: {'id': id},

          success: function (data) {
            $('#dialog_venta_detalle').html(data)
          },
          error: function () {
            alert('Error inesperado')
          }
        })
      }

      function generarComprobante (id) {

        $('#barloadermodal').modal('show')

        $.ajax({
          url: '<?php echo $ruta . 'facturacion/generar_comprobante'; ?>',
          type: 'POST',
          data: {'id': id},

          success: function (data) {

            if (data.facturacion.estado == 1) {
              show_msg('success', '<h4>Facturacion Electronica:</h4> ' + data.facturacion.nota)
            }
            else {
              show_msg('danger', '<h4>Facturacion Electronica:</h4> ' + data.facturacion.nota)
            }

            $('#barloadermodal').modal('hide')
            get_facturacion()
          },
          error: function () {
            alert('Error inesperado')
            $('#barloadermodal').modal('hide')
          }
        })
      }

      function reemitir (id) {

        $('#barloadermodal').modal('show')

        $.ajax({
          url: '<?php echo $ruta . 'facturacion/reemitir_comprobante'; ?>',
          type: 'POST',
          data: {'id': id},

          success: function (data) {

            if (data.facturacion.estado == 3) {
              show_msg('success', '<h4>Facturacion Electronica:</h4> ' + data.facturacion.nota)
            }
            else {
              show_msg('danger', '<h4>Facturacion Electronica:</h4> ' + data.facturacion.nota)
            }

            $('#barloadermodal').modal('hide')
            get_facturacion()
          },
          error: function () {
            alert('Error inesperado')
            $('#barloadermodal').modal('hide')
          }
        })
      }

      function imprimir_ticket (id) {
        $.bootstrapGrowl('<p>IMPRIMIENDO PEDIDO</p>', {
          type: 'success',
          delay: 2500,
          allow_dismiss: true
        })

        var url = '<?=base_url('facturacion/imprimir_ticket')?>/' + id
        $('#imprimir_frame').attr('src', url)
      }

      function imprimir (id) {

        var win = window.open('<?= base_url()?>facturacion/imprimir/' + id, '_blank')
        win.focus()
      }

    </script>
<?php endif; ?>