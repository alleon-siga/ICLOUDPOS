<?php $ruta = base_url(); ?>
<?php $md = get_moneda_defecto() ?>
<div class="row">
    <div class="col-md-10"></div>
    <div class="col-md-2">
        <label>Total: <?= $moneda->simbolo ?> <span
                    id="total"><?= number_format($venta_totales->total, 2) ?></span></label>
    </div>
</div>
<div class="table-responsive">
    <table class='table table-striped dataTable tableStyle'>
        <thead>
        <tr>
            <th width="5%"># Venta</th>
            <th width="5%">Fecha Registro</th>
            <th width="5%">Fecha Venta</th>
            <th width="5%"># Comprobante</th>
            <th width="10%">Identificaci&oacute;n</th>
            <th width="20%">Cliente</th>
            <th width="10%">Vendedor</th>
            <th width="5%">Condici&oacute;n</th>
            <th width="5%">Estado</th>
            <th width="5%">Tip. Cam.</th>
            <th width="5%">Total <?= $venta_action == 'caja' ? 'a Pagar' : '' ?></th>
            <th width="20%">Acciones</th>
        </tr>
        </thead>
        <tbody>
        <?php if (count($ventas) > 0): ?>

            <?php foreach ($ventas as $venta): ?>
                <tr <?= $venta->venta_estado == 'ANULADO' ? 'style="color: red;"' : '' ?>>
                    <td style="white-space: normal;"><?= $venta->venta_id ?></td>
                    <td style="white-space: normal;">
                        <span style="display: none;"><?= date('YmdHis', strtotime($venta->venta_creado)) ?></span>
                        <?= date('d/m/Y H:i', strtotime($venta->venta_creado)) ?>
                    </td>
                    <td style="white-space: normal;">
                        <span style="display: none;"><?= date('YmdHis', strtotime($venta->venta_fecha)) ?></span>
                        <?= date('d/m/Y H:i', strtotime($venta->venta_fecha)) ?>
                    </td>

                    <td style="white-space: normal;"><?php
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
                    </td>
                    <td style="white-space: normal;"><?= $venta->ruc ?></td>
                    <td style="white-space: normal;"><?= $venta->cliente_nombre ?></td>
                    <td style="white-space: normal;"><?= $venta->vendedor_nombre ?></td>
                    <td style="white-space: normal;"><?= $venta->condicion_nombre ?></td>
                    <td style="white-space: normal;"><?= $venta->venta_estado ?></td>
                    <td style="white-space: normal;"><?= $venta->moneda_tasa ?></td>
                    <td style="text-align: right;"><?= $venta->moneda_simbolo ?> <?= number_format($venta->total, 2) ?></td>
                    <td style="text-align: center; white-space: nowrap;">
                        <a class="btn btn-default" data-toggle="tooltip" style="margin-right: 5px;"
                           title="Ver" data-original-title="Ver"
                           href="#"
                           onclick="ver('<?= $venta->venta_id ?>')">
                            <i class="fa fa-search"></i>
                        </a>

                        <?php if ($venta->numero == '' && $venta_action != 'comision' && $venta_action != 'anular' && $venta->venta_estado == 'COMPLETADO'): ?>

                            <a class="btn btn-warning" data-toggle="tooltip" style="margin-right: 5px;"
                               title="Facturar" data-original-title="Facturar"
                               href="#"
                               onclick="facturar('<?= $venta->venta_id ?>')">
                                <i class="fa fa-file-text"></i>
                            </a>
                        <?php endif; ?>

                        <?php if ($venta_action != 'anular' && $venta_action != 'caja' && $venta->venta_estado != 'CERRADA' && $venta_action != 'comision'): ?>
                            <?php //if($venta_action != 'comision'):  ?>
                            <a class="btn btn-primary" data-toggle="tooltip" style="margin-right: 5px;"
                               title="Ver" data-original-title="Ver"
                               href="#"
                               onclick="previa('<?= $venta->venta_id ?>')">
                                <i class="fa fa-print"></i>
                            </a>
                            <?php //endif; ?>

                            <?php if ($venta->factura_impresa == 1 && $venta->documento_id != 6 && (validOption('ACTIVAR_SHADOW', 1) || validOption('ACTIVAR_FACTURACION_VENTA', 1))): ?>
                                <a class="btn btn-warning" data-toggle="tooltip" style="margin-right: 5px;"
                                   title="Cerrar Venta" data-original-title="Cerrar Venta"
                                   href="#"
                                   onclick="cerrar_venta('<?= $venta->venta_id ?>')">
                                    <i class="fa fa-unlock"></i>
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if ($venta_action == 'anular'): ?>
                            <a class="btn btn-danger" data-toggle="tooltip"
                               title="Anular Venta" data-original-title="Anular Venta"
                               href="#"
                               onclick="anularModal(<?= $venta->venta_id ?>)">
                                <i class="fa fa-remove"></i>
                            </a>
                        <?php else: ?>
                        <a class="btn btn-sm btn-warning" data-toggle="tooltip" style="margin-right: 5px;"
                           title="Exportar" data-original-title="Exportar"
                           href="#"
                           onclick="enviar_correo('<?= $venta->venta_id ?>', '<?= $venta->tipo_cliente ?>')">
                            <i class="fa fa-envelope" aria-hidden="true"></i>
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

    <div class="modal fade" id="dialog_venta_cerrar" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false"
         aria-hidden="true">

    </div>
</div>
<div class="modal fade" id="nc_modal" tabindex="-1" role="dialog" style="z-index: 999999;"
     aria-labelledby="myModalLabel"
     aria-hidden="true"
     data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" style="width: 60%">
        <input type="hidden" name="hd_venta_id" id="hd_venta_id" value="">
        <input type="hidden" name="hd_serie" id="hd_serie" value="">
        <input type="hidden" name="hd_credito" id="hd_credito" value="">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" onclick="cerrarNotaCredito()" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title">Nota de cr&eacute;dito</h4>
            </div>
            <div id="nc_modal_body" class="modal-body">

            </div>
            <div class="modal-footer">
                <button class="btn btn-primary btn_venta_imprimir imprimir" type="button" data-nombre="nota_credito">
                    <i class="fa fa-print"></i> Imprimir
                </button>
                <a href="#" class="btn btn-danger" id="cerrar_pago_modal"
                   onclick="cerrarNotaCredito()">Cerrar</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="dialog_anular" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

</div>

<div class="modal fade" id="correoModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"
     data-backdrop="static"></div>
<script type="text/javascript">
  $(function () {
    $('.imprimir').on('click', function () {
      var input = $('.btn_venta_imprimir')
      var nombre = $(this).attr('data-nombre')

      input.html('<i class="fa fa-print"></i> IMPRIMIENDO...')
      input.attr('disabled', 'disabled')

      var data = {
        'venta_id': $('#hd_venta_id').val(),
        'serie': $('#hd_serie').val(),
        'numero': $('#hd_credito').val()
      }

      $.ajax({
        url: '<?= base_url()?>impresion/get_nota_credito',
        data: data,
        type: 'POST',
        success: function (data) {
          $.ajax({
            url: '<?= valueOptionDB('HOST_IMPRESION', 'http://localhost:8080') ?>',
            method: 'POST',
            data: {
              documento: nombre,
              dataset: data
            },
            success: function (data) {
              show_msg('success', 'La nota de credito se esta imprimiendo')
            },
            error: function (data) {
              alert('Error de impresion')
            },
            complete: function (data) {
              input.removeAttr('disabled')
              input.html('<i class="fa fa-print"></i> Nota de credito')
            }

          })
        }
      })
    })

    $('#exportar_excel').on('click', function (e) {
      e.preventDefault()
      exportar_excel()
    })

    $('#exportar_pdf').on('click', function (e) {
      e.preventDefault()
      exportar_pdf()
    })

    TablesDatatables.init(1)

  })

  function anularModal (id) {

    $('#barloadermodal').modal('show')
    $.ajax({
      url: 'venta_new/anular_modal',
      method: 'POST',
      data: {
        venta_id: id,
        local_id: $('#venta_local').val(),
        moneda_id: $('#moneda_id').val()
      },
      success: function (data) {
        $('#barloadermodal').modal('hide')
        $('#dialog_anular').html(data)
        $('#dialog_anular').modal('show')
      },
      error: function () {
        show_msg('danger', 'Ha ocurrido un error inesperado')
      }

    })
  }

  function exportar_pdf () {

    var data = {
      'local_id': $('#venta_local').val(),
      'esatdo': $('#venta_estado').val(),
      'fecha': $('#date_range').val(),
      'moneda_id': $('#moneda_id').val(),
      'condicion_pago_id': $('#condicion_pago_id').val()
    }

    var win = window.open('<?= base_url()?>venta_new/historial_pdf?data=' + JSON.stringify(data), '_blank')
    win.focus()
  }

  function exportar_excel () {
    var data = {
      'local_id': $('#venta_local').val(),
      'esatdo': $('#venta_estado').val(),
      'fecha': $('#date_range').val(),
      'moneda_id': $('#moneda_id').val(),
      'condicion_pago_id': $('#condicion_pago_id').val()
    }

    var win = window.open('<?= base_url()?>venta_new/historial_excel?data=' + JSON.stringify(data), '_blank')
    win.focus()
  }

  function ver (venta_id) {

    $('#dialog_venta_detalle').html($('#loading').html())
    $('#dialog_venta_detalle').modal('show')

    $.ajax({
      url: '<?php echo $ruta . 'venta_new/get_venta_detalle/' . $venta_action; ?>',
      type: 'POST',
      data: {'venta_id': venta_id},

      success: function (data) {
        $('#dialog_venta_detalle').html(data)
      },
      error: function () {
        alert('asd')
      }
    })
  }

  function ver_nc (venta_id, serie, numero) {
    $('#dialog_venta_detalle').modal('hide')
    $('#nc_modal').modal('show')
    $('#hd_venta_id').attr('value', venta_id)
    $('#hd_serie').attr('value', serie)
    $('#hd_credito').attr('value', numero)
    $('#nc_modal_body').html($('#loading').html())
    $.ajax({
      url: '<?php echo $ruta ?>venta/get_nota_credito/',
      type: 'POST',
      data: {'venta_id': venta_id, 'serie': serie, 'numero': numero},
      success: function (data) {
        $('#nc_modal_body').html(data)
      },
      error: function () {
        alert('ups')
      }
    })
  }

  function enviar_correo (idVenta, tipo_cliente) {
    $('#correoModal').html($('#loading').html())
    $('#correoModal').load('<?php echo $ruta ?>' + 'venta/modalEnviarVenta/' + idVenta + '/' + tipo_cliente)
    $('#correoModal').modal('show')
  }

  function cerrarNotaCredito () {
    $('#nc_modal').modal('hide')
    $('#dialog_venta_detalle').modal('show')
  }

  function cerrarDetalle () {
    $('#dialog_venta_detalle').modal('hide')
  }
</script>