<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">Crear nota de credito</h4>
        </div>

        <div class="modal-body">

            <input type="hidden" id="venta_id_anular" value="<?= $venta->venta_id ?>">
            <div class="row">
                <div class="col-md-4">
                    <label>Metodos Pago</label>
                    <select id="metodo_pago" class="form-control">
                        <?php foreach ($metodos_pago as $mp): ?>
                            <option value="<?= $mp->id_metodo ?>"><?= $mp->nombre_metodo ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Cuenta de Caja</label>
                    <select id="cuenta_id" class="form-control">
                        <?php foreach ($cuentas as $cuenta): ?>
                            <option value="<?= $cuenta->id ?>"><?= $cuenta->descripcion ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <?php
                    $motivos = array(
                        '01' => 'Anulaci&oacute;n de la operaci&oacute;n',
                        '02' => 'Anulaci&oacute;n por error en el RUC',
                        '03' => 'Correcci&oacute;n por error en la descripci&oacute;n',
                        //'04' => 'Descuento global',
                        //'05' => 'Descuento por item',
                        '06' => 'Devoluci&oacute;n total',
                        '07' => 'Devoluci&oacute;n por item',
                        //'08' => 'Bonificaci&oacute;n',
                        //'09' => 'Disminuci&oacute;n en el valor'
                    );
                    ?>
                    <label>Motivo</label>
                    <select id="motivo" class="form-control">
                        <option></option>
                        <?php foreach ($motivos as $key => $val): ?>
                            <?php if ($venta->condicion_id == 2 && $key == '07' && $total_pagado > 0) continue; ?>
                            <option value="<?= $key ?>"><?= $val ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-6">
                    <label>Nota de Credito:</label>
                    <?= $nota_credito_numero ?>
                </div>
                <div class="col-md-6 text-right">
                    <label>Total Nota de Credito:</label>
                    <?= $venta->moneda_simbolo ?> <span id="total_nc">0.00</span>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-12">
                    <table id="table_nc" class="table table-bordered">
                        <thead>
                        <tr>
                            <th><?= getCodigoNombre() ?></th>
                            <th>Producto</th>
                            <th>Cantidad a Devolver</th>
                            <th>UM</th>
                            <th>Precio</th>
                            <th>Subtotal</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $row = 0; ?>
                        <?php foreach ($venta->detalles as $detalle): ?>
                            <tr class="producto_detalles_list" id="nc_row_<?= $row ?>"
                                data-detalle_id="<?= $detalle->detalle_id ?>">
                                <td><?= getCodigoValue($detalle->producto_id, $detalle->producto_codigo_interno) ?></td>
                                <td><?= $detalle->producto_nombre ?></td>
                                <?php $cantidad = $detalle->cantidad - $detalle->cantidad_devuelta; ?>
                                <?php $cantidad_formateada = $detalle->producto_cualidad == "PESABLE" ? $cantidad : number_format($cantidad, 0); ?>
                                <td style="width: 120px;">
                                    <input type="number" value="0" data-row="<?= $row ?>"
                                           data-cantidad="<?= $cantidad ?>"
                                           class="form-control cantidad_input">
                                </td>
                                <td><?= $detalle->unidad_nombre ?></td>
                                <td style="text-align: right; white-space: nowrap; width: 120px;">
                                    <div class="input-group">
                                        <span style="min-width: 0px;"
                                              class="input-group-addon"><?= $venta->moneda_simbolo ?></span>
                                        <input type="text" class="form-control"
                                               value="<?= number_format($detalle->precio, 2) ?>" readonly>
                                    </div>
                                </td>
                                <td style="text-align: right; white-space: nowrap; width: 140px;">
                                    <div class="input-group">
                                        <span style="min-width: 0px;"
                                              class="input-group-addon"><?= $venta->moneda_simbolo ?></span>
                                        <input type="text" class="form-control subtotal_input" value="0.00"
                                               readonly>
                                    </div>
                                </td>
                            </tr>
                            <?php $row++; ?>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <br>
            <h3>Detalles de la Venta que desea crear la Nota de credito</h3>
            <div class="row">
                <div class="col-md-3">
                    <label>Venta:</label>
                    <?= $venta->venta_id ?>
                    <a class="badge label-success">Facturado</a>
                </div>
                <div class="col-md-3">
                    <label>Documento:</label>
                    <?php
                    $doc = 'B';
                    if ($venta->documento_id == 1)
                        $doc = 'F';
                    ?>
                    <?= $doc . $venta->serie . '-' . sumCod($venta->numero, 8) ?>
                </div>
                <div class="col-md-3">
                    <label>Condicion:</label>
                    <?= $venta->condicion_nombre ?>
                </div>
                <div class="col-md-3 text-right">
                    <label>Total venta:</label>
                    <?= $venta->moneda_simbolo ?><span id="total_venta"><?= $venta->total ?></span>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-12">
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
                        <?php $row = 0; ?>
                        <?php foreach ($venta->detalles as $detalle): ?>
                            <tr class="producto_detalles_list" id="doc_row_<?= $row ?>"
                                data-id="<?= $detalle->detalle_id ?>">
                                <td><?= getCodigoValue($detalle->producto_id, $detalle->producto_codigo_interno) ?></td>
                                <td><?= $detalle->producto_nombre ?></td>
                                <?php $cantidad = $detalle->cantidad - $detalle->cantidad_devuelta; ?>
                                <?php $cantidad_formateada = $detalle->producto_cualidad == "PESABLE" ? $cantidad : number_format($cantidad, 0); ?>
                                <td data-cantidad="<?= $cantidad_formateada ?>">
                                    <?= $cantidad_formateada ?>
                                </td>
                                <td><?= $detalle->unidad_nombre ?></td>
                                <td style="text-align: right; white-space: nowrap;">
                                    <?= $venta->moneda_simbolo ?> <?= $detalle->precio ?>
                                </td>
                                <td style="text-align: right; white-space: nowrap;">
                                    <?= $venta->moneda_simbolo ?> <?= $detalle->importe ?>
                                </td>
                            </tr>
                            <?php $row++; ?>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php if($venta->condicion_id == 2):?>
                    <h5 class="text-warning">Nota: Las ventas al credito solo aceptan devoluciones por item si no ha pagado ningun monto ya sea inicial o cobranza.</h5>
                    <?php endif;?>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button id="anular_venta_btn" type="button" class="btn btn-primary">
                Aceptar
            </button>

            <button type="button" class="btn btn-danger" data-dismiss="modal">
                Cancelar
            </button>

        </div>
    </div>
    <!-- /.modal-content -->
</div>

<script>
  $(function () {

    $('#motivo').trigger('change')
    $('#motivo').trigger('click')

    $('#table_nc input.cantidad_input').on('input', function () {
      var row_id = $(this).attr('data-row')
      var cantidad_input = $('#nc_row_' + row_id + ' > td:nth-child(3) > input:nth-child(1)')
      var precio_input = $('#nc_row_' + row_id + ' > td:nth-child(5) > div:nth-child(1) > input:nth-child(2)')
      var subtotal_input = $('#nc_row_' + row_id + ' > td:nth-child(6) > div:nth-child(1) > input:nth-child(2)')

      var cantidad = cantidad_input.val().trim() != '' ? parseFloat(cantidad_input.val()) : 0
      var cantidad_max = parseFloat($('#doc_row_' + row_id + ' > td:nth-child(3)').html())

      if (cantidad > cantidad_max) {
        show_msg('warning', 'No puede devolver una cantidad mayor a la venta original')
        return false
      }

      subtotal_input.val(roundPrice(cantidad * precio_input.val()))

      var subtotal = 0
      $('.subtotal_input').each(function () {
        var input = $(this)
        subtotal += parseFloat(input.val())
      })

      $('#total_nc').html(roundPrice(subtotal))
    })

    $('#table_nc input.cantidad_input').on('focus', function () {
      $(this).select()
    })

    $('#motivo').on('change', function () {
      var select = $(this)

      if (select.val() == '' || select.val() == '07') {
        $('#table_nc input.cantidad_input').each(function () {
          var input = $(this)
          input.removeAttr('readonly', 'readonly')
          input.val('0')
          input.trigger('input')
        })

        $('#table_nc input.cantidad_input:first').focus()
      }
      else {
        $('#table_nc input.cantidad_input').each(function () {
          var input = $(this)
          input.attr('readonly', 'readonly')
          input.val(input.attr('data-cantidad'))
          input.trigger('input')
        })
      }

    })

    $('#anular_venta_btn').on('click', function () {

      if (isNaN($('#venta_id_anular').val())) {
        show_msg('warning', 'No se ha podido recuperar el id de la venta')
        return false
      }

      if (isNaN($('#cuenta_id').val())) {
        show_msg('warning', 'Debe seleccionar una cuenta valida')
        return false
      }

      if (isNaN($('#metodo_pago').val())) {
        show_msg('warning', 'Debe seleccionar un metodo de pago valido')
        return false
      }

      if ($('#motivo').val().trim() == '') {
        show_msg('warning', 'Ingrese el motivo de la anulacion')
        return false
      }

      var nc_detalles = []

      $('#table_nc > tbody tr').each(function () {
        var tr = $(this)
        var cantidad_input = tr.find('td:nth-child(3) > input:nth-child(1)')
        var cantidad = parseFloat(cantidad_input.val())
        var cantidad_old = parseFloat(cantidad_input.attr('data-cantidad'))
        if (cantidad > 0) {
          nc_detalles.push({
            detalle_id: tr.attr('data-detalle_id'),
            cantidad: cantidad,
            cantidad_old: cantidad_old
          })
        }
      })

      if (nc_detalles.length == 0) {
        show_msg('warning', 'Debe devolver al menos un producto para crear la nota de credito')
        return false
      }

      var flag = true
      for (var i = 0; i < nc_detalles.length; i++) {
        if (nc_detalles[i].cantidad > nc_detalles[i].cantidad_old) {
          flag = false
        }
      }

      if (flag == false) {
        show_msg('warning', 'No puede devolver una cantidad mayor a la venta original')
        return false
      }

      $('#barloadermodal').modal('show')
      $.ajax({
        url: '<?= base_url()?>venta_new/nota_credito_venta',
        method: 'POST',
        data: {
          venta_id: $('#venta_id_anular').val(),
          cuenta_id: $('#cuenta_id').val(),
          metodo_pago: $('#metodo_pago').val(),
          motivo: $('#motivo').val(),
          nc_detalles: JSON.stringify(nc_detalles)
        },
        success: function (data) {
          show_msg(data.success == 1 ? 'success' : 'warning', data.msg)
        },
        error: function () {
          show_msg('danger', 'Ha ocurrido un error inesperado')
        },
        complete: function () {
          $('#dialog_anular').modal('hide')
          $('#barloadermodal').modal('hide')
          $('.modal-backdrop').remove()
          get_ventas()
        }
      })
    })
  })
</script>