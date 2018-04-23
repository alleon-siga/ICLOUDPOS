<?php $ruta = base_url(); ?>
<?php $md = get_moneda_defecto() ?>
<div class="row">
    <div class="col-md-10"></div>
    <!--        <div class="col-md-2">-->
    <!--            <label>Subtotal: --><? //= $moneda->moneda ?><!-- <span id="subtotal">-->
    <? //=number_format($venta_totales->subtotal, 2)?><!--</span></label>-->
    <!--        </div>-->
    <!--        <div class="col-md-2">-->
    <!--            <label>IGV: --><? //= $moneda->simbolo ?><!-- <span id="impuesto">-->
    <? //=number_format($venta_totales->impuesto, 2)?><!--</span></label>-->
    <!--        </div>-->
    <div class="col-md-2">
        <label>Total: <?= $moneda->simbolo ?> <span
                    id="total"><?= number_format($venta_totales->total, 2) ?></span></label>
    </div>
</div>
<div class="table-responsive">
    <table class='table table-striped dataTable table-bordered no-footer tableStyle' style="overflow:scroll">
        <thead>
        <tr>
            <th>CLIENTE</th>
            <th>NOMBRE DE LA TIENDA </th>
            <th>NRO RECARGA </th>
            <th>TRANSACCION </th>
            <th>FECHA </th>
            <th>RECARGA </th>
            <th>MONTO   </th>
            <th>FECHA PAGO  </th>
            <th>IMPORTE </th>
            <th>PAGADO  </th>
            <th>TIENDA  </th>
            <th>CONDICION</th>
        </tr>
        </thead>
        <tbody>
<tr height=20 style='height:15.0pt'>
  <td height=20  style='height:15.0pt;border-top:none'>YBAN HIPOLITO
  PAREDES VEGA</td>
  <td  >BODEGA
  &quot;SHERA&quot;</td>
  <td  align=right >926482827</td>
  <td  align=right >123456789</td>
  <td class=xl66 align=right >2018-04-20
  02:12</td>
  <td  align=right >94</td>
  <td class=xl66 align=right >2018-04-20
  17:23</td>
  <td  align=right >94</td>
  <td  >TIENDA 1</td>
  <td>CONTADO</td>
 </tr>
 <tr height=20 style='height:15.0pt'>
  <td height=20  style='height:15.0pt;border-top:none'>ALICIA
  ANGELICA YUCYUC AGURTO</td>
  <td  >BODEGA ALICIA -PUNYA<span
  style='display:none'>N</span></td>
  <td  align=right >935169138</td>
  <td class=xl69 >00000</td>
  <td class=xl66 align=right >2018-04-10
  17:47</td>
  <td  align=right >94</td>
  <td class=xl66 align=right >2018-04-11
  17:52</td>
  <td  align=right >94</td>
  <td  >TIENDA 1</td>
  <td>CREDITO</td>
 </tr>
 <tr height=20 style='height:15.0pt'>
  <td height=20  style='height:15.0pt;border-top:none'>ALICIA
  ANGELICA YUCYUC AGURTO</td>
  <td  >BODEGA ALICIA -PUNYA<span
  style='display:none'>N</span></td>
  <td  align=right >935169138</td>
  <td  align=right >86625379</td>
  <td class=xl66 align=right >2018-03-29
  18:09</td>
  <td  align=right >94</td>
  <td class=xl66 align=right >2018-04-11
  18:09</td>
  <td  align=right >94</td>
  <td  >TIENDA 1</td>
  <td>CREDITO</td>
 </tr>
 <tr height=20 style='height:15.0pt'>
  <td height=20  style='height:15.0pt;border-top:none'>DELMA ENEDINA
  SOLORZANO FLORENTINO</td>
  <td  >BODEGA DELMA</td>
  <td  align=right >931440918</td>
  <td  align=right >87939682</td>
  <td class=xl66 align=right >2018-04-11
  18:19</td>
  <td  align=right >200</td>
  <td class=xl66 align=right >2018-04-11
  18:19</td>
  <td  align=right >200</td>
  <td  >TIENDA 1</td>
  <td>CONTADO</td>
 </tr>
 <tr height=20 style='height:15.0pt'>
  <td height=20  style='height:15.0pt;border-top:none'>LUCIO GARCIA
  ROMERO</td>
  <td  >BODEGA LUCIO</td>
  <td  align=right >930805663</td>
  <td  align=right >87957949</td>
  <td class=xl66 align=right >2018-04-11
  19:53</td>
  <td  align=right >94</td>
  <td class=xl66 align=right >2018-04-11
  19:53</td>
  <td  align=right >94</td>
  <td  >TIENDA 1</td>
  <td>CONTADO</td>
 </tr>
 <tr height=20 style='height:15.0pt'>
  <td height=20  style='height:15.0pt;border-top:none'>IRENE BLANCA
  IBARRA JARA</td>
  <td  >BODEGA MILAGRITOS</td>
  <td  align=right >935887929</td>
  <td  align=right >87960071</td>
  <td class=xl66 align=right >2018-04-11
  19:54</td>
  <td  align=right >94</td>
  <td class=xl66 align=right >2018-04-11
  19:54</td>
  <td  align=right >94</td>
  <td  >TIENDA 1</td>
  <td>CONTADO</td>
 </tr>
 <tr height=20 style='height:15.0pt'>
  <td height=20  style='height:15.0pt;border-top:none'>ROSARIO
  PAREDES BARROSO</td>
  <td  >BOTICA CAMILA</td>
  <td  align=right >87929508</td>
  <td  align=right >930806070</td>
  <td class=xl66 align=right >2018-04-11
  19:56</td>
  <td  align=right >94</td>
  <td class=xl66 align=right >2018-04-11
  19:56</td>
  <td  align=right >94</td>
  <td  >TIENDA 1</td>
  <td>CONTADO</td>
 </tr>            
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
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" onclick="$('#nc_modal').modal('hide');" aria-hidden="true">
                        &times;
                    </button>
                    <h4 class="modal-title">Nota de cr&eacute;dito</h4>
                </div>
                <div id="nc_modal_body" class="modal-body">
                
                </div>
                <div class="modal-footer">
                    <a href="#" class="btn btn-danger" id="cerrar_pago_modal" onclick="$('#nc_modal').modal('hide');">Cerrar</a>
                </div>
            </div>
        </div>
    </div>
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

        TablesDatatables.init(1);

    });

    function exportar_pdf() {

        var data = {
            'local_id': $("#venta_local").val(),
            'esatdo': $("#venta_estado").val(),
            'fecha': $("#date_range").val(),
            'moneda_id': $("#moneda_id").val(),
            'condicion_pago_id': $("#condicion_pago_id").val()
        };

        var win = window.open('<?= base_url()?>venta_new/historial_pdf?data=' + JSON.stringify(data), '_blank');
        win.focus();
    }

    function exportar_excel() {
        var data = {
            'local_id': $("#venta_local").val(),
            'esatdo': $("#venta_estado").val(),
            'fecha': $("#date_range").val(),
            'moneda_id': $("#moneda_id").val(),
            'condicion_pago_id': $("#condicion_pago_id").val()
        };

        var win = window.open('<?= base_url()?>venta_new/historial_excel?data=' + JSON.stringify(data), '_blank');
        win.focus();
    }

    function ver(venta_id) {

        $("#dialog_venta_detalle").html($("#loading").html());
        $("#dialog_venta_detalle").modal('show');

        $.ajax({
            url: '<?php echo $ruta . 'venta_new/get_venta_detalle/' . $venta_action; ?>',
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

    function ver_nc(venta_id, serie, numero) {
        $("#nc_modal").modal('show');
        $.ajax({
            url: '<?php echo $ruta ?>venta/get_nota_credito/',
            type: 'POST',
            data: {'venta_id': venta_id, 'serie': serie, 'numero': numero},
            success: function (data) {
                $("#nc_modal_body").html(data);
            },
            error: function () {
                alert('ups')
            }
        });
    }
</script>