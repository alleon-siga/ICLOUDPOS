<div class="modal-dialog" >
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Detalle Ajuste</h4>
        </div>
        <div class="modal-body">

            <div class="table-responsive">
                <table class="table datatable datatables_filter table-striped" id="tabledetail">

                    <thead>
                    <tr>

                        <th style="text-align: center"><?php echo getCodigoNombre() ?></th>
                        <th style="text-align: center">Nombre</th>
                        <th style="text-align: center">UM</th>
                        <th style="text-align: center">Cantidad</th>
                        <th style="text-align: center">Fraccion</th>


                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if (isset($detalles)) {
                        foreach ($detalles as $detalle) {

                            ?>
                            <tr>
                                <td style="text-align: center">
                                    <?php echo getCodigoValue(sumCod($detalle->producto_id),$detalle->producto_codigo_interno) ?>

                                </td>
                                <td style="text-align: center">
                                    <?= $detalle->producto_nombre ?>
                                </td>
                                <td style="text-align: center">
                                    <?= $detalle->nombre_unidad ?>
                                </td>
                                <td style="text-align: center">
                                    <?= $detalle->cantidad_detalle ?>
                                </td>
                                <td style="text-align: center">
                                    <?= $detalle->fraccion_detalle ?>
                                </td>

                            </tr>
                        <?php }
                    } ?>
                    </tbody>
                </table>


            </div>

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>

        </div>
    </div>
    <!-- /.modal-content -->
</div>

<script src="<?php echo $ruta ?>recursos/js/pages/tablesDatatables.js"></script>
<script>
    $(function () {

        $("#tabledetail").dataTable();

    });
</script>
