<?php $ruta = base_url(); ?>
<?php $md = get_moneda_defecto() ?>
<?php $term = diccionarioTermino() ?>
<div class="row">
    <div class="col-md-10"></div>

    <div class="col-md-2">
        <label>Total: <?= $moneda->simbolo ?> <span id="total"><?= number_format($cotizaciones_totales->total, 2) ?></span></label>
    </div>
</div>
<div class="table-responsive">
    <table class='table table-striped dataTable table-bordered no-footer tableStyle' style="overflow:scroll">
        <thead>
        <tr>
            <th>ID</th>
            <th>Fecha Emisi&oacute;n</th>
            <th>Fecha Vencimiento</th>
            <th>Documento</th>
            <th>Num Documento</th>
            <th>Cliente</th>
            <th>Vendedor</th>
            <th>Tip. Cam.</th>
            <th>Total</th>
            <th>Acciones</th>


        </tr>
        </thead>
        <tbody>
        <?php if (count($cotizaciones) > 0): ?>

            <?php foreach ($cotizaciones as $detalle): ?>
                <tr>
                    <td><?= $detalle->id ?></td>
                    <td>
                        <span style="display: none;"><?= date('YmdHis', strtotime($detalle->created)) ?></span>
                        <?= date('d/m/Y', strtotime($detalle->created)) ?>
                    </td>
                    <td>
                        <span style="display: none;"><?= date('YmdHis', strtotime($detalle->fecha)) ?></span>
                        <?= date('d/m/Y', strtotime($detalle->fecha)) ?>
                    </td>

                    <td style="text-align: center;"><?= $detalle->tipo_cliente == '2' ? $term[1]->valor : $term[0]->valor ?></td>
                    <td><?= $detalle->ruc ?></td>
                    <td><?= $detalle->cliente_nombre ?></td>
                    <td><?= $detalle->vendedor_nombre ?></td>
                    <td><?= $detalle->moneda_tasa ?></td>
                    <td style="text-align: right;"><?= $detalle->moneda_simbolo ?> <?= number_format($detalle->total, 2) ?></td>
                    <td style="text-align: center;">
                        <?php if ($detalle->estado == 'PENDIENTE'): ?>
                            <a class="btn btn-sm btn-default" data-toggle="tooltip" style="margin-right: 5px;"
                               title="Ver" data-original-title="Ver"
                               href="#"
                               onclick="cotizar('<?= $detalle->id ?>');">
                                <i class="fa fa-dollar"></i>
                            </a>
                        <?php endif; ?>

                        <a class="btn btn-sm btn-default" data-toggle="tooltip" style="margin-right: 5px;"
                           title="Ver" data-original-title="Ver"
                           href="#"
                           onclick="ver('<?= $detalle->id ?>');">
                            <i class="fa fa-search"></i>
                        </a>

                        <a class="btn btn-sm btn-default" data-toggle="tooltip" style="margin-right: 5px;"
                           title="Exportar" data-original-title="Exportar"
                           href="#"
                           onclick="exportar_pdf('<?= $detalle->id ?>','<?= $detalle->tipo_cliente ?>');">
                            <i class="fa fa-file-pdf-o"></i>
                        </a>

                        <?php if ($detalle->estado == 'PENDIENTE'): ?>
                            <a class="btn btn-sm btn-danger" data-toggle="tooltip"
                               title="Eliminar" data-original-title="Eliminar"
                               href="#"
                               onclick="anular('<?= $detalle->id ?>', '<?= sumCod($detalle->id, 6) ?>');">
                                <i class="fa fa-remove"></i>
                            </a>
                        <?php endif; ?>
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

        TablesDatatables.init(1);

        $("#dialog_cotizar_detalle").on("hide.bs.modal", function(){
            get_cotizaciones();
        })

    });

    function cotizar(id) {
        $("#dialog_cotizar_detalle").html($("#loading").html());
        $("#dialog_cotizar_detalle").modal('show');

        $.ajax({
            url: '<?php echo $ruta . 'cotizar/get_cotizar_validar/'?>',
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

    function exportar_pdf(id, tp) {

        var win = window.open('<?= base_url()?>cotizar/exportar_pdf/' + id + '/' + tp, '_blank');
        win.focus();
    }


    function anular(id) {

        if (!window.confirm("Estas seguro de eliminar esta cotizacion"))
            return false;

        $("#confirm_venta_text").html($("#loading").html());

        $.ajax({
            url: '<?php echo $ruta . 'cotizar/eliminar'; ?>',
            type: 'POST',
            data: {'id': id},

            success: function (data) {
                $.bootstrapGrowl('<h4>Correcto.</h4> <p>Cotizacion anulada con exito.</p>', {
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