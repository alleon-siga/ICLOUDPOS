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
 <tr height=20 style='height:15.0pt'>
  <td height=20 class=xl67 width=108 style='height:15.0pt;width:81pt'>FECHA</td>
  <td class=xl67 width=250 style='border-left:none;width:188pt'>CLIENTE</td>
  <td class=xl67 width=163 style='border-left:none;width:122pt'>NOMBRE DE LA
  TIENDA</td>
  <td class=xl67 width=96 style='border-left:none;width:72pt'>NRO RECARGA</td>
  <td class=xl67 width=97 style='border-left:none;width:73pt'>TRANSACCION</td>
  <td class=xl67 width=56 style='border-left:none;width:42pt'>MONTO</td>
  <td class=xl67 width=80 style='border-left:none;width:60pt'>TIENDA</td>
    <td class=xl68 width=80 style='border-left:none;width:60pt'>CONDICION</td>
 </tr>
        </thead>
        <tbody>
<tr height=20 style='height:15.0pt'>
  <td height=20 class=xl66 align=right style='height:15.0pt;border-top:none'>2018-04-11
  18:37</td>
  <td class=xl65 style='border-top:none;border-left:none'>LENIN WINSTON BAILON
  HUERTA</td>
  <td class=xl65 style='border-top:none;border-left:none'>BODEGA LLANGANUCO</td>
  <td class=xl65 align=right style='border-top:none;border-left:none'>931051658</td>
  <td class=xl65 align=right style='border-top:none;border-left:none'>87961725</td>
  <td class=xl65 align=right style='border-top:none;border-left:none'>94</td>
  <td class=xl65 style='border-top:none;border-left:none'>TIENDA 1</td>
  <td class=xl65 style='border-top:none;border-left:none'>CREDITO</td>
 </tr>
 <tr height=20 style='height:15.0pt'>
  <td height=20 class=xl66 align=right style='height:15.0pt;border-top:none'>2018-04-20
  19:30</td>
  <td class=xl65 style='border-top:none;border-left:none'>ANTONINO MARCELINO
  MILLA CASTRO</td>
  <td class=xl65 style='border-top:none;border-left:none'>MILTISERVICIOS MILLA</td>
  <td class=xl65 align=right style='border-top:none;border-left:none'>935461053</td>
  <td class=xl65 align=right style='border-top:none;border-left:none'>87933013</td>
  <td class=xl65 align=right style='border-top:none;border-left:none'>100</td>
  <td class=xl65 style='border-top:none;border-left:none'>TIENDA 1</td>
  <td class=xl65 style='border-top:none;border-left:none'>CREDITO</td>
 </tr>
 <tr height=20 style='height:15.0pt'>
  <td height=20 class=xl66 align=right style='height:15.0pt;border-top:none'>2018-04-20
  19:33</td>
  <td class=xl65 style='border-top:none;border-left:none'>ROSALIA GONZALES
  HUERTA</td>
  <td class=xl65 style='border-top:none;border-left:none'>MULT. 7 ROSAS</td>
  <td class=xl65 align=right style='border-top:none;border-left:none'>930806034</td>
  <td class=xl65 align=right style='border-top:none;border-left:none'>87913027</td>
  <td class=xl65 align=right style='border-top:none;border-left:none'>279</td>
  <td class=xl65 style='border-top:none;border-left:none'>TIENDA 1</td>
  <td class=xl65 style='border-top:none;border-left:none'>CREDITO</td>
 </tr>
 <tr height=20 style='height:15.0pt'>
  <td height=20 class=xl66 align=right style='height:15.0pt;border-top:none'>2018-04-11
  19:34</td>
  <td class=xl65 style='border-top:none;border-left:none'>JOSAIN FLORENCIO
  MILLA DIONICIO</td>
  <td class=xl65 style='border-top:none;border-left:none'>MULTISERVICIOS
  &quot;MILLA&quot;</td>
  <td class=xl65 align=right style='border-top:none;border-left:none'>927303052</td>
  <td class=xl65 align=right style='border-top:none;border-left:none'>87936920</td>
  <td class=xl65 align=right style='border-top:none;border-left:none'>100</td>
  <td class=xl65 style='border-top:none;border-left:none'>TIENDA 1</td>
  <td class=xl65 style='border-top:none;border-left:none'>CREDITO</td>
 </tr>
 <tr height=20 style='height:15.0pt'>
  <td height=20 class=xl66 align=right style='height:15.0pt;border-top:none'>2018-04-11
  19:36</td>
  <td class=xl65 style='border-top:none;border-left:none'>LIDA MARIELA NIÑO
  MORALES</td>
  <td class=xl65 style='border-top:none;border-left:none'>PDV MARIELA - MUSHO</td>
  <td class=xl65 align=right style='border-top:none;border-left:none'>921651008</td>
  <td class=xl65 align=right style='border-top:none;border-left:none'>87949724</td>
  <td class=xl65 align=right style='border-top:none;border-left:none'>200</td>
  <td class=xl65 style='border-top:none;border-left:none'>TIENDA 1</td>
  <td class=xl65 style='border-top:none;border-left:none'>CREDITO</td>
 </tr>
 <tr height=20 style='height:15.0pt'>
  <td height=20 class=xl66 align=right style='height:15.0pt;border-top:none'>2018-04-11
  19:38</td>
  <td class=xl65 style='border-top:none;border-left:none'>GLADYS SABINA
  TARAZONA CUEVA</td>
  <td class=xl65 style='border-top:none;border-left:none'>PDV TARAZONA</td>
  <td class=xl65 align=right style='border-top:none;border-left:none'>931440778</td>
  <td class=xl65 align=right style='border-top:none;border-left:none'>87944275</td>
  <td class=xl65 align=right style='border-top:none;border-left:none'>100</td>
  <td class=xl65 style='border-top:none;border-left:none'>TIENDA 1</td>
  <td class=xl65 style='border-top:none;border-left:none'>CREDITO</td>
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