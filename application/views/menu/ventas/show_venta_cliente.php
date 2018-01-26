<?php $ruta = base_url(); ?>
<form name="formagregar" action="<?= base_url() ?>grupo/guardar" method="post">
    <div class="modal-dialog" style="width: 80%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Venta por Cliente</h4>
            </div>

            <div class="modal-body">
                <div class="table-responsive">
                <table class="table table-striped dataTable table-bordered" id="tablaresult" >
                    <thead>
                    <tr>

                        <th>N&uacute;mero de Venta</th>
                        <th>Fecha</th>
                        <th>Vendedor</th>
                        <th>Razon Social</th>
                        <th>Condiciones de Pago</th>
                        <th>Estado</th>
                        <th>Local</th>
                        <th>Sub total</th>
                        <th>Impuesto</th>
                        <th>Total</th>

                    </tr>
                    </thead>
                    <tbody>
                    <?php $cliente; if (count($ventas) > 0) {

                        foreach ($ventas as $venta) {
                            ?>
                            <tr>

                                <td><?= $venta->venta_id ?> <?php  $cliente=$venta->id_cliente ?></td>
                                <td><?= date('d-m-Y H:i:s', strtotime($venta->fecha)) ?></td>
                                <td><?= $venta->username ?></td>
                                <td><?= $venta->razon_social ?></td>
                                <td><?= $venta->nombre_condiciones ?></td>
                                <td><?= $venta->venta_status ?></td>
                                <td><?= $venta->local_nombre ?></td>
                                <td><?=  $venta->simbolo." ".$venta->subtotal ?></td>
                                <td><?=  $venta->simbolo." ".$venta->total_impuesto ?></td>
                                <td><?=  $venta->simbolo." ".$venta->total ?></td>
                            </tr>

                        <?php }
                    }?>



                    </tbody>
                </table>
            </div>
                <br>
                <a target="_blank" href="<?= $ruta?>venta/pdfVentasporCliente/<?= $cliente?>" id="generarpdf" class="btn  btn-default btn-lg" data-toggle="tooltip" title="Exportar a PDF" data-original-title="fa fa-file-pdf-o"><i class="fa fa-file-pdf-o fa-fw"></i></a>
                <a target="_blank" href="<?= $ruta?>venta/excelVentasporCliente/<?= $cliente?>" class="btn btn-default btn-lg" data-toggle="tooltip" title="Exportar a Excel" data-original-title="fa fa-file-excel-o"><i class="fa fa-file-excel-o fa-fw"></i></a>

            </div>
            <!-- /.modal-content -->
        </div>
</form>