<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">Anulacion de Venta</h4>
        </div>

        <div class="modal-body ">
            <h5>Estas seguro que deseas anular esta venta</h5>

            <input type="hidden" id="venta_id_anular" value="<?= $venta->venta_id ?>">
            <div class="row">
                <div class="col-md-4 col-md-offset-1">
                    <label>Metodos Pago</label>
                    <select id="metodo_pago" class="form-control">
                        <?php foreach ($metodos_pago as $mp): ?>
                            <option value="<?= $mp->id_metodo ?>"><?= $mp->nombre_metodo ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-5">
                    <label>Cuenta de Caja</label>
                    <select id="cuenta_id" class="form-control">
                        <?php foreach ($cuentas as $cuenta): ?>
                            <option value="<?= $cuenta->id ?>"><?= $cuenta->descripcion ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-9 col-md-offset-1">
                    <?php
                    $motivos = array(
                        '01' => 'Anulaci&oacute;n de la operaci&oacute;n',
                        '02' => 'Anulaci&oacute;n por error en el RUC',
                        '03' => 'Correcci&oacute;n por error en la descripci&oacute;n',
                        '04' => 'Descuento global',
                        '05' => 'Descuento por item',
                        '06' => 'Devoluci&oacute;n total',
                        '07' => 'Devoluci&oacute;n por item',
                        '08' => 'Bonificaci&oacute;n',
                        '09' => 'Disminuci&oacute;n en el valor'
                    );
                    ?>
                    <label>Motivo</label>
                    <input type="text" id="motivo" class="form-control">
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

      $('#barloadermodal').modal('show')
      $.ajax({
        url: '<?= base_url() ?>venta_new/anular_venta',
        method: 'POST',
        data: {
          venta_id: $('#venta_id_anular').val(),
          cuenta_id: $('#cuenta_id').val(),
          metodo_pago: $('#metodo_pago').val(),
          motivo: $('#motivo').val()
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