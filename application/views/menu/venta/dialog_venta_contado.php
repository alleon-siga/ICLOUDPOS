<?php $md = get_moneda_defecto() ?>
<input type="hidden" id="caja_venta_id" value="">
<input type="hidden" id="contado_tipo_pago" value="">
<div class="modal-dialog" style="width: 60%">
    <div class="modal-content">
        <div class="modal-header">
            <h4>Terminar Venta</h4>
        </div>
        <div class="modal-body panel-venta-left">
            <div class="row" id="vc_forma_pago_block">
                <div class="form-group">
                    <div class="col-md-3">
                        <label for="vc_forma_pago" class="control-label panel-admin-text">Forma de Pago:</label>
                    </div>
                    <div class="col-md-9">
                        <select class="form-control" id="vc_forma_pago" name="vc_forma_pago">
                            <?php foreach ($metodos as $metodo): ?>
                                <option value="<?= $metodo['id_metodo'] ?>"><?= $metodo['nombre_metodo'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row" id="vc_moneda_tasa_block" style="display:none;">
                <div class="form-group">
                    <div class="col-md-3">
                        <label for="vc_moneda_tasa_confirm" class="control-label panel-admin-text">Tasa de
                            Cambio:</label>
                    </div>
                    <div class="col-md-9">
                        <div class="input-prepend input-append input-group">
                            <label class="input-group-addon"><?= $md->simbolo ?></label>
                            <input
                                    type="text"
                                    class='input-square input-small form-control'
                                    id="vc_moneda_tasa_confirm"
                                    readonly>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" id="vc_total_pagar_block">
                <div class="form-group">
                    <div class="col-md-3">
                        <label for="vc_total_pagar" class="control-label panel-admin-text">Total a Pagar:</label>
                    </div>
                    <div class="col-md-9">
                        <div class="input-prepend input-append input-group">
                            <label class="input-group-addon tipo_moneda"><?= $md->simbolo ?></label><input
                                    type="number"
                                    class='input-square input-small form-control'
                                    min="0.0"
                                    step="0.1"
                                    value="0.0"
                                    data-value="0.00"
                                    id="vc_total_pagar"
                                    name="vc_total_pagar"
                                    readonly
                                    onkeydown="return soloDecimal(this, event)">
                        </div>
                    </div>
                </div>
            </div>


            <div class="row" id="vc_importe_block">
                <div class="form-group">
                    <div class="col-md-3">
                        <label for="vc_importe" class="control-label panel-admin-text">Importe:</label>
                    </div>
                    <div class="col-md-9">
                        <div class="input-prepend input-append input-group">
                            <label class="input-group-addon tipo_moneda"><?= $md->simbolo ?></label><input
                                    type="number"
                                    tabindex="0"
                                    class='input-square input-small form-control'
                                    min="0.0"
                                    step="0.1"
                                    value="0.00"
                                    name="vc_importe"
                                    id="vc_importe"
                                    onkeydown="return soloDecimal(this, event)">

                        </div>
                    </div>
                </div>
            </div>
            <div class="row" id="vc_vuelto_block">
                <div class="form-group">
                    <div class="col-md-3">
                        <label for="vc_vuelto" class="control-label panel-admin-text">Vuelto:</label>
                    </div>
                    <div class="col-md-9">
                        <div class="input-prepend input-append input-group">
                            <label class="input-group-addon tipo_moneda"><?= $md->simbolo ?></label><input
                                    type="text"
                                    class='input-square input-small form-control'
                                    value="0.00"
                                    name="vc_vuelto"
                                    id="vc_vuelto"
                                    readonly>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row" id="vc_banco_block" style="display: none;">
                <div class="col-md-3">
                    <label class="control-label panel-admin-text">Banco</label>
                </div>
                <div class="col-md-9">
                    <select name="vc_banco_id" id="vc_banco_id" class="form-control">
                        <option value="">Seleccione</option>
                        <?php foreach ($bancos as $banco): ?>
                            <option
                                    value="<?= $banco->banco_id ?>"><?= $banco->banco_nombre ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
            </div>

            <div class="row" id="vc_num_oper_block" style="display:none;">
                <div class="form-group">
                    <div class="col-md-3">
                        <label for="vc_num_oper" class="control-label panel-admin-text">Operaci&oacute;n #:</label>
                    </div>
                    <div class="col-md-9">
                        <input type="text" tabindex="0" class='input-square input-small form-control' name="vc_num_oper"
                               id="vc_num_oper" autocomplete="off">
                    </div>
                </div>
            </div>

            <div class="row" id="vc_tipo_tarjeta_block" style="display:none;">
                <div class="form-group">
                    <div class="col-md-3">
                        <label for="vc_tipo_tarjeta" class="control-label panel-admin-text">Tipo de Tarjeta:</label>
                    </div>
                    <div class="col-md-9">
                        <select class="form-control" id="vc_tipo_tarjeta" name="vc_tipo_tarjeta">
                            <?php foreach ($tarjetas as $tarjeta) : ?>
                                <option value="<?php echo $tarjeta->id ?>"><?php echo $tarjeta->nombre ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>


        </div>
        <div class="modal-footer">

            <div class="row">
                <div class="col-md-12">
                    <?php
                    $boton = json_decode(valueOption("BOTONES_VENTA"));
                    $arrHtml[0] = '<button class="btn btn-default save_venta_contado" data-imprimir="0" type="button" id="btn_venta_contado"><i class="fa fa-save"></i> Guardar</button>';
                    $arrHtml[1] = '<a href="#" class="btn btn-default save_venta_contado ocultar_caja" id="btn_venta_contado_imprimir_2" data-imprimir="2" type="button"><i class="fa fa-print"></i> (F6)Guardar & Imprimir</a>';
                    $arrHtml[2] = '<a href="#" class="btn btn-default save_venta_contado ocultar_caja" id="btn_venta_contado_imprimir" data-imprimir="1" type="button"><i class="fa fa-print"></i> Guardar & Detalles</a>';
                    $arr = array('GUARDAR', 'GUARDAR & IMPRIMIR', 'GUARDAR & DETALLES');
                    foreach ($boton as $clave => $valor) {
                        if ($valor == '1') {
                            echo $arrHtml[$clave];
                        }
                    }
                    ?>
                    <button class="btn btn-danger"
                            type="button"
                            onclick="$('#dialog_venta_contado').modal('hide')"><i
                                class="fa fa-close"></i> Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
  var bancos = []
  <?php foreach ($bancos as $banco): ?>
  bancos.push({
    banco_id: '<?= $banco->banco_id ?>',
    banco_nombre: '<?= $banco->banco_nombre ?>',
    moneda_id: '<?= $banco->id_moneda ?>'
  })
  <?php endforeach ?>
  $(document).ready(function () {

    $(document).keyup(function (e) {

    })

    $('.save_venta_contado').on('click', function () {
      var tipo_pago = $('#contado_tipo_pago').val()
      if (tipo_pago == '1') {
        save_venta_contado($(this).attr('data-imprimir'))

      } else if (tipo_pago == '2') {
        if ($('#vc_forma_pago').val() == '3' && $('#vc_vuelto').val() < 0) {
          show_msg('warning', '<h4>Error. </h4><p>El importe no puede ser menor que el total a pagar. Recomendamos una venta al Cr&eacute;dito.</p>')
          setTimeout(function () {
            $('#vc_importe').trigger('focus')
          }, 500)
          return false
        }
        if ($('#vc_forma_pago').val() != '3' && $('#vc_num_oper').val() == '') {
          show_msg('warning', '<h4>Error. </h4><p>El campo Operaci&oacute;n # es obligatorio.</p>')
          setTimeout(function () {
            $('#vc_num_oper').trigger('focus')
          }, 500)
          return false
        }
        if (($('#vc_forma_pago').val() == '4' || $('#vc_forma_pago').val() == '8' || $('#vc_forma_pago').val() == '9' || $('#vc_forma_pago').val() == '7') && $('#vc_banco_id').val() == '') {
          show_msg('warning', '<h4>Error. </h4><p>Debe seleccionar un Banco</p>')
          setTimeout(function () {
            $('#vc_banco_id').trigger('focus')
          }, 500)
          return false
        }

        save_venta_credito($(this).attr('data-imprimir'))
      }

    })

    $('#vc_forma_pago').on('change', function () {
      var forma_pago = $('#vc_forma_pago').val()

      $('#vc_tipo_tarjeta_block').hide()
      $('#vc_importe_block').hide()
      $('#vc_vuelto_block').hide()
      $('#vc_num_oper_block').hide()
      $('#vc_banco_block').hide()
      $('#vc_num_oper').val('')
      $('#vc_vuelto').val('0.00')
      $('#vc_importe').val($('#vc_total_pagar').val())

      //efectivo
      if (forma_pago == '3') {
        $('#vc_importe_block').show()
        $('#vc_vuelto_block').show()
        setTimeout(function () {
          $('#vc_importe').trigger('focus')
        }, 500)
      }
      //tarjeta
      else if (forma_pago == '7') {
        $('#vc_banco_block').show()
        $('#vc_num_oper_block').show()
        $('#vc_tipo_tarjeta_block').show()
        setTimeout(function () {
          $('#vc_num_oper').trigger('focus')
        }, 500)
      }
      else if (forma_pago == '5' || forma_pago == '6' || forma_pago == '10') {
        $('#vc_num_oper_block').show()
        setTimeout(function () {
          $('#vc_num_oper').trigger('focus')
        }, 500)
      }

      else if (forma_pago == '4' || forma_pago == '8' || forma_pago == '9') {
        var banco_select = $('#vc_banco_id')
        banco_select.html('<option value="">Seleccione</option>')
        for (var i = 0; i < bancos.length; i++) {
          if (bancos[i].moneda_id == $('#moneda_id').val()) {
            banco_select.append('<option value="' + bancos[i].banco_id + '">' + bancos[i].banco_nombre + '</option>')
          }
        }
        $('#vc_num_oper_block').show()
        $('#vc_banco_block').show()
        setTimeout(function () {
          $('#vc_banco_id').trigger('focus')
        }, 500)

      }
    })

    $('#vc_importe').on('focus', function () {
      $(this).select()
    })

    $('#vc_importe').on('keyup', function () {
      var importe = isNaN(parseFloat($('#vc_importe').val())) ? 0 : parseFloat($('#vc_importe').val())
      if (importe > 0) {
        var vuelto = parseFloat(importe - parseFloat($('#vc_total_pagar').val()))
        $('#vc_vuelto').val(vuelto.toFixed(2))
      }
      else {
        $('#vc_vuelto').val('0'.toFixed(2))
      }
    })

    $('#vc_num_oper').on('focus', function () {
      $(this).select()
    })

  })

</script>