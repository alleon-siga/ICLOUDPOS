<?php $ruta = base_url(); ?>
<!-- Load and execute javascript code used only in this page -->


<script type="text/javascript">
    /*
     function cancelar(id) {
     document.getElementById('venta_id').value = id;
     $("#cancVent").modal('show');
     }

     function update() {
     var id = $('#venta_id').val();
     var observacion = $('#observacion').val();
     var usuario_id = $('#usuario_id').val();
     $.ajax({
     url: '' + 'venta/anular_venta',
     type: 'POST',
     data: 'venta_id=' + id + '&observacion=' + observacion + "&usuario_id=" + usuario_id,
     success: function (msj) {
     if (msj == 'actualizo') {
     window.location.reload();
     }
     }
     });
     return false;
     }
     */

</script>


<ul class="breadcrumb breadcrumb-top">
    <li>Ventas</li>
    <li><a href="">Devolver Ventas</a></li>
</ul>

<!-- END Datatables Header -->
<div class="block">
    <div class="row-fluid">
        <div class="span12">
            <div class="box">
                <div class="block-title">
                    <h2><strong>Devolver</strong> Ventas</h2>
                </div>

                <div class="box-content box-nomargin">

                    <div class="tab-content">
                        <div class="table-responsive">
                            <?php $md = get_moneda_defecto()?>
                            <table class='table table-striped dataTable table-bordered' id="table">
                                <thead>
                                <tr>

                                    <th>ID</th>
                                    <th>T.Documento</th>
                                    <th>Nro. Doc</th>
                                    <th>Cliente</th>
                                    <th>Fecha Reg</th>
                                    <th>Monto Total <?php echo $md->simbolo ?></th>
                                    <th>Estado</th>
                                    <th>Accion</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if (count($ventas) > 0): ?>
                                    <?php foreach ($ventas as $venta): ?>
                                        <?php $detalles = $venta_model->get_detalle_by_venta_id($venta->venta_id) ?>
                                        <tr>
                                            <td><?php echo $venta->venta_id; ?></td>
                                            <td><?php echo $venta->des_doc ?></td>
                                            <td><?php echo $venta->documento_Serie."-".$venta->documento_Numero; ?></td>
                                            <td><?php echo $venta->razon_social; ?></td>
                                            <td style="text-align: center;"><?php echo date("d-m-Y H:i:s", strtotime($venta->fecha)); ?></td>
                                            <td style="text-align: center;"><?php echo $venta->total; ?></td>
                                            <td style="text-align: center;"><?php echo $venta->venta_status == "COBRO" ? "COBRAR EN CAJA" : $venta->venta_status; ?></td>
                                            <td class='actions_big'>

                                                <div class="btn-group">
                                                    <a onclick="devolverventa(<?php echo $venta->venta_id; ?>)"
                                                       class='btn btn-default'><i class="fa fa-remove"></i> Devolver</a>
                                                </div>

                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="ventamodal"  style="width: 85%; overflow: auto;
  margin: auto;" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
             aria-hidden="true">
            <div class="modal-content"  >
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-close"></i> </button>

                    <h3>Devolver venta</h3>
                </div>
                <div class="modal-body" id="ventamodalbody">

                </div>

            </div>

        </div>

    </div>
</div>
<script src="<?php echo $ruta?>recursos/js/pages/tablesDatatables.js"></script>
<script>
    $(function(){

        TablesDatatables.init();


        $("#fecha").datepicker({format: 'dd-mm-yyyy'});

    });

    function devolverventa(id) {

        $.ajax({
            url:'<?php echo base_url()?>venta',
            data:{'idventa':id, 'devolver':1},
            type:'post',
            success: function (data) {
                $("#ventamodalbody").html(data);


            },
            error: function (data){
                alert(data.responseText);
            }
        });
        $("#ventamodal").modal({show:true,  keyboard: false,  backdrop:'static'});

    }



</script>