<?php $ruta = base_url(); ?>
<link href='<?= $ruta ?>recursos/css/fullcalendar.min.css' rel='stylesheet'/>
<link href='<?= $ruta ?>recursos/css/fullcalendar.print.min.css' rel='stylesheet' media='print'/>
<style>
    h2 {
        font-size: 1.5em !important;
        font-weight: bold !important;
    }

    #cuerpoCalendar {
        padding: 0;
        font-family: "Lucida Grande", Helvetica, Arial, Verdana, sans-serif;
        font-size: 14px;
        text-transform: capitalize;
    }

    #calendar {
        max-width: 900px;
        margin: 0 auto;
    }

    .fc-button {
        line-height: 0 !important;
    }

    .fc-button .fc-icon {
        font-size: 1em !important;
    }
</style>
<ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#calendario">Calendario</a></li>
    <li><a data-toggle="tab" href="#lista">Lista</a></li>
</ul>
<div class="tab-content">
    <div id="calendario" class="tab-pane fade in active">
        <div class="row" id="cuerpoCalendar">
            <div class="col-md-12">
                <div id='calendar'></div>
            </div>
        </div>
    </div>
    <div id="lista" class="tab-pane fade">
        <div class="table-responsive">
            <table class='table table-striped dataTable table-bordered no-footer tableStyle' style="overflow:scroll">
                <thead>
                <tr>
                    <th># Venta</th>
                    <th>F. Venta</th>
                    <th># Comprobante</th>
                    <th>Cliente</th>
                    <th>Importe Venta</th>
                    <th>Valor Cuota</th>
                    <th>Importe Abonado</th>
                    <th>Pendiente Pago</th>
                    <th>Nro Cuota</th>
                    <th>Cuotas Atrasadas</th>
                    <th>F. Pago</th>
                    <th>F. Vencimiento</th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($tablas as $tabla):
                    if ($tabla->var_credito_estado == 'PagoPendiente') {
                        ?>
                        <tr>
                            <td><?= $tabla->venta_id ?></td>
                            <td><?= date("d/m/Y", strtotime($tabla->fecha)) ?></td>
                            <td>
                                <?php
                                if ($tabla->numero != '') {
                                    echo $tabla->des_doc . ' ' . $tabla->serie . '-' . sumCod($tabla->numero, 6);
                                } else {
                                    echo '<span style="color: #0000FF">NO EMITIDO</span>';
                                }
                                ?>
                            </td>
                            <td><?= $tabla->razon_social ?></td>
                            <td style="text-align: right;"><?= $tabla->simbolo . ' ' . $tabla->total ?></td>
                            <td style="text-align: right;"><?= $tabla->simbolo . ' ' . $tabla->monto ?></td>
                            <td style="text-align: right;"><?= $tabla->simbolo ?> <?= empty($tabla->monto_abono) ? 0 : $tabla->monto_abono ?></td>
                            <td style="text-align: right;"><?= $tabla->simbolo ?> <?= empty($tabla->monto_restante) ? 0 : $tabla->monto_restante ?></td>
                            <td><?php $a = explode("/", $tabla->nro_letra);
                                echo $a[0]; ?></td>
                            <td>
                                <?php
                                $fs = strtotime(date('Y-m-d'));
                                $fv = strtotime($tabla->fecha_vencimiento);
                                if ($fs >= $fv) {
                                    echo '1';
                                } else {
                                    echo '0';
                                }
                                ?>
                            </td>
                            <td>
                                <?
                                if (!empty($tabla->ultimo_pago)) {
                                    echo date("d/m/Y", strtotime($tabla->ultimo_pago));
                                }
                                ?>
                            </td>
                            <td><?= date("d/m/Y", strtotime($tabla->fecha_vencimiento)); ?></td>
                        </tr>
                        <?php
                    }
                endforeach;
                ?>
                </tbody>
            </table>
        </div>
        <div class="row">
            <div class="col-md-12">
                <br>
                <button type="button" id="exportar_excel_lista" title="Exportar Excel" class="btn btn-success btn-md">
                    <i class="fa fa-file-excel-o fa-fw"></i>
                </button>
                <button type="button" id="exportar_pdf_lista" title="Exportar Pdf" class="btn btn-danger btn-md">
                    <i class="fa fa-file-pdf-o fa-fw"></i>
                </button>
            </div>
        </div>
    </div>
</div>
<hr>

<script type="text/javascript">
  var eventos = []
  <?php foreach ($lists as $list):?>
  eventos.push({
    venta_id: '<?= $list->venta_id ?>',
    cliente: '<?= $list->razon_social ?>',
    fecha_venc: '<?= $list->fecha_vencimiento ?>',
    pago_pendiente: '<?= $list->pago_pendiente ?>',
    nro_letra: '<?= $list->nro_letra ?>',
    simbolo: '<?= $list->simbolo ?>'
  })
  <?php endforeach;?>

  $(document).ready(function () {
    var options = {
      defaultView: 'month',
      header: {
        left: 'prev,next today',
        center: 'title',
        right: 'month'
      },
      views: {
        month: {
          titleFormat: 'MMMM YYYY',
          columnFormat: 'dddd'
        }
      },
      events: []
    }

    for (var i = 0; i < eventos.length; i++) {
      var datos = []
      datos['id'] = eventos[i].venta_id
      datos['title'] = eventos[i].cliente + '\n' + eventos[i].simbolo + ' ' + eventos[i].pago_pendiente + '\n' + eventos[i].nro_letra
      datos['start'] = eventos[i].fecha_venc
      options.events.push(datos)
    }
    $('#calendar').fullCalendar(options)

    $('#exportar_excel_lista').on('click', function () {
      exportar_excel_lista()
    })

    $('#exportar_pdf_lista').on('click', function () {
      exportar_pdf_lista()
    })

    TablesDatatables.init(0)
  })

  function exportar_pdf_lista () {
    var data = {
      'local_id': $('#local_id').val(),
      'opcion': 'lista'
    }

    var win = window.open('<?= base_url()?>venta/calendarioCuentasCobrar/pdf?data=' + JSON.stringify(data), '_blank')
    win.focus()
  }

  function exportar_excel_lista () {
    var data = {
      'local_id': $('#local_id').val(),
      'opcion': 'lista'
    }

    var win = window.open('<?= base_url()?>venta/calendarioCuentasCobrar/excel?data=' + JSON.stringify(data), '_blank')
    win.focus()
  }
</script>