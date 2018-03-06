<?php $ruta = base_url(); ?>
<?php $md = get_moneda_defecto()?>
<div class="modal-dialog" style="width: 70%;" >
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Detalle Ingreso</h4>
        </div>
        <div class="modal-body">

            <div class="table-responsive">
                <table class="table datatable datatables_filter table-striped tableStyle" id="tabledetail">

                    <thead>
                    <tr>

                        <th>ID</th>
                        <th>Producto</th>
                        <th>UM</th>
                        <th>Cantidad</th>
                        <th>Moneda</th>
                        <th>Tipo Camb</th>
                        <th>Precio</th>
                        <th>Sub total</th>


                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if (isset($detalles)) {
                        $total = 0;
                        $simbolo = $md->simbolo;
                        foreach ($detalles as $detalle) {
                            $total += $detalle->precio*$detalle->cantidad;
                            $simbolo = $detalle->simbolo;
                            ?>
                            <tr>
                                <td align="center">
                                    <?= $detalle->id_detalle_ingreso ?>
                                </td>
                                <td align="center">
                                    <?= getCodigoValue(sumCod($detalle->id_producto),$detalle->producto_codigo_interno).' - '.$detalle->producto_nombre ?>
                                </td>
                                <td align="center">
                                    <?= $detalle->nombre_unidad ?>
                                </td>
                                <td align="center">
                                    <?= $detalle->cantidad ?>
                                </td>
                                <td align="center">
                                    <?=  $detalle->nombre ?>
                                </td>
                                <td align="center">
                                    <?=  //$detalle->tasa_cambio == '0.00' ? '-' :
                                    $detalle->tasa_cambio ?>
                                </td>
                                <td align="center">
                                    <?= $detalle->simbolo ." ".$detalle->precio?>
                                </td>

                                <td align="center">
                                    <?= $detalle->simbolo ." ".number_format($detalle->precio*$detalle->cantidad,2)?>
                                </td>

                            </tr>
                        <?php }
                    } ?>
                    </tbody>
                </table>


            </div>

            <br>
            <div class="row">
                <div class="col-md-3">
                    <?php  if(!isset($id_detalle)){ $id_detalle=0; } ?>
                    <a href="#" onclick="generar_reporte_excel(<?= $id_detalle ?>,'<?= $ingreso_tipo ?>');" class='tip btn-lg btn btn-default'
                       title="Exportar a Excel"><i class="fa fa-file-excel-o"></i> </a>


                    <a href="#" onclick="generar_reporte_pdf(<?= $id_detalle ?>,'<?= $ingreso_tipo ?>');" class='btn btn-lg btn-default tip'
                        title="Exportar a PDF"><i class="fa fa-file-pdf-o"></i></a>
                </div>
                <div class="col-md-offset-6 col-md-3">
                    <label>Total: <?= $simbolo ?> <span id="total"><?=number_format($total, 2)?></span></label>
                </div>
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
