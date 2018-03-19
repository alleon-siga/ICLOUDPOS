<?php $ruta = base_url(); ?>
<?php $md = get_moneda_defecto() ?>
<style>
    .totales {
        width: 100%;
        text-align: right;
    }

    .totales tr td {
        padding: 5px 0;
        font-weight: bold;
    }
</style>
<div class="modal-dialog" style="width: 70%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <?php
            $doc = 'NP ';
            if ($ingreso->tipo_documento == 'FACTURA') $doc = 'FA ';
            if ($ingreso->tipo_documento == 'BOLETA DE VENTA') $doc = 'BO ';
            ?>
            <h4 class="modal-title">Detalle
                Ingreso <?= $doc . $ingreso->documento_serie . '-' . $ingreso->documento_numero ?></h4>
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
                            $total += $detalle->precio * $detalle->cantidad;
                            $simbolo = $detalle->simbolo;
                            ?>
                            <tr>
                                <td align="center">
                                    <?= $detalle->id_detalle_ingreso ?>
                                </td>
                                <td align="center">
                                    <?= getCodigoValue(sumCod($detalle->id_producto), $detalle->producto_codigo_interno) . ' - ' . $detalle->producto_nombre ?>
                                </td>
                                <td align="center">
                                    <?= $detalle->nombre_unidad ?>
                                </td>
                                <td align="center">
                                    <?= $detalle->cantidad ?>
                                </td>
                                <td align="center">
                                    <?= $detalle->nombre ?>
                                </td>
                                <td align="center">
                                    <?= //$detalle->tasa_cambio == '0.00' ? '-' :
                                    $detalle->tasa_cambio ?>
                                </td>
                                <td align="center">
                                    <?= $detalle->simbolo . " " . $detalle->precio ?>
                                </td>

                                <td align="center">
                                    <?= $detalle->simbolo . " " . number_format($detalle->precio * $detalle->cantidad, 2) ?>
                                </td>

                            </tr>
                        <?php }
                    } ?>
                    </tbody>
                </table>


            </div>

            <br>
            <div class="row">
                <div class="col-md-8 ">
                    <h5>Estado: <?= $ingreso->ingreso_status ?></h5>
                    <?php if ($ingreso->ingreso_status == 'ANULADO'): ?>
                        Nota de Credito: <?= $kardex->serie . ' - ' . $kardex->numero ?>
                    <?php endif; ?>
                </div>
                <div class="col-md-4 text-right">
                    <table class="totales">
                        <tr>
                            <td>Subtotal:</td>
                            <td><?= $simbolo ?> <?= number_format($ingreso->sub_total_ingreso, 2) ?></label></td>
                        </tr>
                        <tr>
                            <td>Impuesto:</td>
                            <td><?= $simbolo ?> <?= number_format($ingreso->impuesto_ingreso, 2) ?></label></td>
                        </tr>
                        <tr>
                            <td>Total:</td>
                            <td><?= $simbolo ?> <?= number_format($ingreso->total_ingreso, 2) ?></label></td>
                        </tr>
                    </table>
                </div>
            </div>


        </div>
        <div class="modal-footer">
            <div class="row">
                <div class="col-md-6 text-left">
                    <?php if (!isset($id_detalle)) {
                        $id_detalle = 0;
                    } ?>
                    <a href="#" onclick="generar_reporte_excel(<?= $id_detalle ?>,'<?= $ingreso_tipo ?>');"
                       class='btn btn-default'
                       title="Exportar a Excel"><i class="fa fa-file-excel-o"></i> </a>


                    <a href="#" onclick="generar_reporte_pdf(<?= $id_detalle ?>,'<?= $ingreso_tipo ?>');"
                       class='btn btn-default'
                       title="Exportar a PDF"><i class="fa fa-file-pdf-o"></i></a>
                </div>

                <div class="col-md-6 text-right">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                </div>
            </div>

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
