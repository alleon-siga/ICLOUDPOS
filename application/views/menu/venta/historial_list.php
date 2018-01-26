<?php $ruta = base_url(); ?>
<?php $md = get_moneda_defecto()?>
    <div class="row">
        <div class="col-md-6"></div>
        <div class="col-md-2">
            <label>Subtotal: <?= $md->simbolo ?> <span id="subtotal"><?=number_format($venta_totales->subtotal, 2)?></span></label>
        </div>
        <div class="col-md-2">
            <label>IGV: <?= $md->simbolo ?> <span id="impuesto"><?=number_format($venta_totales->impuesto, 2)?></span></label>
        </div>
        <div class="col-md-2">
            <label>Total: <?= $md->simbolo ?> <span id="total"><?=number_format($venta_totales->total, 2)?></span></label>
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
        <th>Total <?= $venta_action == 'caja' ? 'a Pagar' : '' ?></th>
        <th>Acciones</th>


    </tr>
    </thead>
    <tbody>
    <?php if (count($ventas) > 0): ?>

        <?php foreach ($ventas as $venta): ?>
            <tr>
                <td>
                    <span style="display: none;"><?= date('YmdHis', strtotime($venta->venta_fecha)) ?></span>
                    <?= date('d/m/Y H:i:s', strtotime($venta->venta_fecha)) ?>
                </td>

                <td style="text-align: center;"><?php
                    if ($venta->documento_id == 1) echo "FA";
                    if ($venta->documento_id == 2) echo "NC";
                    if ($venta->documento_id == 3) echo "BO";
                    if ($venta->documento_id == 4) echo "GR";
                    if ($venta->documento_id == 5) echo "PCV";
                    if ($venta->documento_id == 6) echo "NP";
                    ?>
                </td>
                <td><?= sumCod($venta->venta_id, 4) ?></td>
                <td><?= $venta->ruc?></td>
                <td><?= $venta->cliente_nombre ?></td>
                <td><?= $venta->vendedor_nombre ?></td>
                <td><?= $venta->condicion_nombre ?></td>
                <td><?= $venta->moneda_nombre ?></td>
                <td><?= $venta->moneda_tasa ?></td>
                <td style="text-align: right;"><?= $venta->moneda_simbolo ?> <?= number_format($venta->subtotal, 2) ?></td>
                <td style="text-align: right;"><?= $venta->moneda_simbolo ?> <?= number_format($venta->impuesto, 2) ?></td>
                <td style="text-align: right;"><?= $venta->moneda_simbolo ?> <?=number_format( $venta->total, 2) ?></td>
                <td style="text-align: center;">

                    <a class="btn btn-default" data-toggle="tooltip" style="margin-right: 5px;"
                       title="Ver" data-original-title="Ver"
                       href="#"
                       onclick="ver('<?= $venta->venta_id ?>');">
                        <i class="fa fa-search"></i>
                    </a>

                    <?php if ($venta_action != 'anular' && $venta_action != 'caja' && $venta->venta_estado != 'CERRADA'): ?>
                        <a class="btn btn-primary" data-toggle="tooltip" style="margin-right: 5px;"
                           title="Ver" data-original-title="Ver"
                           href="#"
                           onclick="previa('<?= $venta->venta_id ?>');">
                            <i class="fa fa-print"></i>
                        </a>

                        <?php if ($venta->factura_impresa == 1 && $venta->documento_id != 6 && (validOption('ACTIVAR_SHADOW', 1) || validOption('ACTIVAR_FACTURACION_VENTA', 1))): ?>
                            <a class="btn btn-warning" data-toggle="tooltip" style="margin-right: 5px;"
                               title="Cerrar Venta" data-original-title="Cerrar Venta"
                               href="#"
                               onclick="cerrar_venta('<?= $venta->venta_id ?>');">
                                <i class="fa fa-unlock"></i>
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if ($venta_action == 'caja'): ?>
                        <a class="btn btn-primary" data-toggle="tooltip" style="margin-right: 5px;"
                           title="Cobrar" data-original-title="Cobrar"
                           href="#"
                           onclick="cobrar('<?= $venta->venta_id ?>');">
                            <i class="fa fa-money"></i>
                        </a>

                        <a class="btn btn-danger" data-toggle="tooltip"
                           title="Cancelar Venta" data-original-title="Cancelar Venta"
                           href="#"
                           onclick="eliminar_venta('<?= $venta->venta_id ?>', '<?= sumCod($venta->venta_id, 6) ?>');">
                            <i class="fa fa-remove"></i>
                        </a>
                    <?php endif; ?>

                    <?php if ($venta_action == 'anular'): ?>
                        <?php if ($venta->condicion_id == '1'): ?>
                            <a class="btn btn-warning" data-toggle="tooltip" style="margin-right: 5px;"
                               title="Devolver Venta" data-original-title="Devolver Venta"
                               href="#"
                               onclick="devolver('<?= $venta->venta_id ?>');">
                                <i class="fa fa-arrow-circle-left"></i>
                            </a>
                        <?php endif; ?>

                        <?php if ($venta->credito_pagado == 0 || $venta->condicion_id == '1'): ?>
                            <a class="btn btn-danger" data-toggle="tooltip"
                               title="Anular Venta" data-original-title="Anular Venta"
                               href="#"
                               onclick="anular('<?= $venta->venta_id ?>', '<?= sumCod($venta->venta_id, 6) ?>');">
                                <i class="fa fa-remove"></i>
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach ?>
    <?php endif; ?>

    </tbody>
</table>


<a id="exportar_pdf" target="_blank"
   href="#"
   data-href="<?= $ruta ?>venta_new/historial_pdf/"
   class="btn  btn-default btn-lg" data-toggle="tooltip" title="Exportar a PDF"
   data-original-title="fa fa-file-pdf-o"><i class="fa fa-file-pdf-o fa-fw"></i></a>

<a id="exportar_excel" target="_blank"
   href="#"
   data-href="<?= $ruta ?>venta_new/historial_excel/"
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

<div class="modal fade" id="dialog_venta_cerrar" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false"
     aria-hidden="true">

</div>
   </div>

<script type="text/javascript">
    $(function () {

        TablesDatatables.init(2);

    });


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

    function previa(venta_id) {

        $("#dialog_venta_imprimir").html($("#loading").html());
        $("#dialog_venta_imprimir").modal('show');

        $.ajax({
            url: '<?php echo $ruta . 'venta_new/get_venta_previa'; ?>',
            type: 'POST',
            data: {'venta_id': venta_id},

            success: function (data) {
                $("#dialog_venta_imprimir").html(data);
            }
        });
    }

    function cerrar_venta(venta_id) {

        $("#dialog_venta_cerrar").html($("#loading").html());
        $("#dialog_venta_cerrar").modal('show');

        $.ajax({
            url: '<?php echo $ruta . 'venta_new/cerrar_venta'; ?>',
            type: 'POST',
            data: {'venta_id': venta_id},

            success: function (data) {
                $("#dialog_venta_cerrar").html(data);
            }
        });
    }

    <?php if ($venta_action == 'caja'): ?>
    function cobrar(venta_id) {

        $("#dialog_venta_detalle").html($("#loading").html());
        $("#dialog_venta_detalle").modal('show');

        $.ajax({
            url: '<?php echo $ruta . 'venta_new/get_venta_cobro/' . $venta_action; ?>',
            type: 'POST',
            data: {'venta_id': venta_id},
            headers: {
                Accept: 'application/json'
            },

            success: function (data) {

                if (data.venta.condicion_id == '1')
                    $("#vc_total_pagar").val(formatPrice(data.venta.total));
                else if (data.venta.condicion_id == '2')
                    $("#vc_total_pagar").val(formatPrice(data.venta.inicial));

                $("#vc_importe").val($("#vc_total_pagar").val());
                $("#vc_vuelto").val(0);
                $("#vc_num_oper").val('');

                $("#dialog_venta_detalle").modal('hide');
                $("#dialog_venta_contado").modal('show');

                setTimeout(function () {
                    $("#vc_forma_pago").val('1').trigger("chosen:updated");
                    $("#vc_forma_pago").change();
                }, 500);


            }
        });
    }
    <?php endif;?>

    <?php if ($venta_action == 'anular'): ?>
    function anular(venta_id, venta) {

        $('#confirm_venta_text').attr('data-venta', venta);
        $('#confirm_venta_text').html('Estas seguro que deseas anular la venta ' + $('#confirm_venta_text').attr('data-venta') + '?');
        $('#confirm_venta_button').attr('onclick', 'anular_venta("' + venta_id + '");');

        $("#documento_serie").val("");
        $("#documento_numero").val("");

        $('#dialog_venta_confirm').modal('show');
    }

    function anular_venta(venta_id) {

        if($("#documento_serie").val() == "" || $("#documento_numero").val() == ""){
            show_msg('warning', 'Complete la serie y numero del documento');
            return false;
        }

        $("#confirm_venta_text").html($("#loading").html());

        $.ajax({
            url: '<?php echo $ruta . 'venta_new/anular_venta'; ?>',
            type: 'POST',
            data: {'venta_id': venta_id, 'serie': $("#documento_serie").val(), 'numero': $("#documento_numero").val()},

            success: function (data) {
                $('#dialog_venta_confirm').modal('hide');
                $.bootstrapGrowl('<h4>Correcto.</h4> <p>Venta anulada con exito.</p>', {
                    type: 'success',
                    delay: 5000,
                    allow_dismiss: true
                });
                get_ventas();
            },
            error: function () {
                $('#confirm_venta_text').html('Estas seguro que deseas anular la venta ' + $('#confirm_venta_text').attr('data-venta') + '?');
                $.bootstrapGrowl('<h4>Error.</h4> <p>Ha ocurrido un error en la operaci&oacute;n</p>', {
                    type: 'danger',
                    delay: 5000,
                    allow_dismiss: true
                });
            }
        });
    }

    function devolver(venta_id) {

        $("#dialog_venta_detalle").html($("#loading").html());
        $("#dialog_venta_detalle").modal('show');

        $.ajax({
            url: '<?php echo $ruta . 'venta_new/devolver_detalle'; ?>',
            type: 'POST',
            data: {'venta_id': venta_id},

            success: function (data) {
                $("#dialog_venta_detalle").html(data);
            }
        });
    }
    <?php endif;?>
</script>