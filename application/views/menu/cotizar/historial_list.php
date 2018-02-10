<?php $ruta = base_url(); ?>
<?php $md = get_moneda_defecto() ?>
<div class="row">
    <div class="col-md-6"></div>
    <div class="col-md-2">
        <label>Subtotal: <?= $md->simbolo ?> <span
                    id="subtotal"><?= number_format($cotizaciones_totales->subtotal, 2) ?></span></label>
    </div>
    <div class="col-md-2">
        <label>IGV: <?= $md->simbolo ?> <span
                    id="impuesto"><?= number_format($cotizaciones_totales->impuesto, 2) ?></span></label>
    </div>
    <div class="col-md-2">
        <label>Total: <?= $md->simbolo ?> <span id="total"><?= number_format($cotizaciones_totales->total, 2) ?></span></label>
    </div>
</div>
<div class="table-responsive">
    <table class='table table-striped dataTable table-bordered no-footer tableStyle' style="overflow:scroll">
        <thead>
        <tr>
            <th>Fecha</th>
            <th>Doc</th>
            <th>Num Doc</th>
            <th>RUC - DNI</th>
            <th>Cliente</th>
            <th>Vendedor</th>
            <th>Condici&oacute;n</th>
            <th>Moneda</th>
            <th>Tip. Cam.</th>
            <th>SubTotal</th>
            <th>IGV</th>
            <th>Total</th>
            <th>Acciones</th>


        </tr>
        </thead>
        <tbody>
        <?php if (count($cotizaciones) > 0): ?>

            <?php foreach ($cotizaciones as $detalle): ?>
                <tr>
                    <td>
                        <span style="display: none;"><?= date('YmdHis', strtotime($detalle->fecha)) ?></span>
                        <?= date('d/m/Y H:i:s', strtotime($detalle->fecha)) ?>
                    </td>

                    <td style="text-align: center;"><?php
                        if ($detalle->documento_id == 1) echo "FA";
                        if ($detalle->documento_id == 2) echo "NC";
                        if ($detalle->documento_id == 3) echo "BO";
                        if ($detalle->documento_id == 4) echo "GR";
                        if ($detalle->documento_id == 5) echo "PCV";
                        if ($detalle->documento_id == 6) echo "NP";
                        ?>
                    </td>
                    <td><?= sumCod($detalle->id, 4) ?></td>
                    <td><?= $detalle->ruc ?></td>
                    <td><?= $detalle->cliente_nombre ?></td>
                    <td><?= $detalle->vendedor_nombre ?></td>
                    <td><?= $detalle->condicion_nombre ?></td>
                    <td><?= $detalle->moneda_nombre ?></td>
                    <td><?= $detalle->moneda_tasa ?></td>
                    <td style="text-align: right;"><?= $detalle->moneda_simbolo ?> <?= number_format($detalle->subtotal, 2) ?></td>
                    <td style="text-align: right;"><?= $detalle->moneda_simbolo ?> <?= number_format($detalle->impuesto, 2) ?></td>
                    <td style="text-align: right;"><?= $detalle->moneda_simbolo ?> <?= number_format($detalle->total, 2) ?></td>
                    <td style="text-align: center;">

                        <a class="btn btn-default" data-toggle="tooltip" style="margin-right: 5px;"
                           title="Ver" data-original-title="Ver"
                           href="#"
                           onclick="ver('<?= $detalle->id ?>');">
                            <i class="fa fa-search"></i>
                        </a>

                        <a class="btn btn-default" data-toggle="tooltip" style="margin-right: 5px;"
                           title="Exportar" data-original-title="Exportar"
                           href="#"
                           onclick="exportar_pdf('<?= $detalle->id ?>');">
                            <i class="fa fa-file-pdf-o"></i>
                        </a>

                        <a class="btn btn-danger" data-toggle="tooltip"
                           title="Eliminar" data-original-title="Eliminar"
                           href="#"
                           onclick="anular('<?= $detalle->id ?>', '<?= sumCod($detalle->id, 6) ?>');">
                            <i class="fa fa-remove"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach ?>
        <?php endif; ?>

        </tbody>
    </table>

</div>


<div class="modal fade" id="dialog_cotizar_detalle" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false"
     aria-hidden="true">

</div>

<script type="text/javascript">
    $(function () {

        TablesDatatables.init(2);

    });


    function ver(id) {
        $("#dialog_cotizar_detalle").html($("#loading").html());
        $("#dialog_cotizar_detalle").modal('show');

        $.ajax({
            url: '<?php echo $ruta . 'cotizar/get_cotizar_detalle/'?>',
            type: 'POST',
            data: {'id': id},

            success: function (data) {
                $("#dialog_cotizar_detalle").html(data);
            },
            error: function () {
                alert('Error inesperado')
            }
        });
    }

    function exportar_pdf(id) {

        var win = window.open('<?= base_url()?>cotizar/exportar_pdf/' + id, '_blank');
        win.focus();
    }






    function anular(id) {

        if(!window.confirm("Estas seguro de eliminar esta cotizacion"))
            return false;

        $("#confirm_venta_text").html($("#loading").html());

        $.ajax({
            url: '<?php echo $ruta . 'cotizar/eliminar'; ?>',
            type: 'POST',
            data: {'id': id},

            success: function (data) {
                $.bootstrapGrowl('<h4>Correcto.</h4> <p>Cotizacion eliminada con exito.</p>', {
                    type: 'success',
                    delay: 5000,
                    allow_dismiss: true
                });
                get_cotizaciones();
            },
            error: function () {
               alert('Error inesperado');
            }
        });
    }

</script>