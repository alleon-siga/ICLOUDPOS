<?php $ruta = base_url(); ?>
<style>
    .table td {
        font-size: 14px !important;
    }
</style>
<input type="hidden" id="caja_imprimir" value="1">
<?php $md = get_moneda_defecto()?>
<div class="row">
    <div class="col-md-9"></div>
    <!--<div class="col-md-2">
        <label>Subtotal: <?= $md->simbolo ?> <span id="subtotal"><?=number_format($venta_totales->subtotal, 2)?></span></label>
    </div>
    <div class="col-md-2">
        <label>IGV: <?= $md->simbolo ?> <span id="impuesto"><?=number_format($venta_totales->impuesto, 2)?></span></label>
    </div>-->
        <label>Total Pendiente:
            <label style="padding: 5px; font-size: 14px; margin: 0px;" class="control-label badge label-warning panel-admin-text">
                <?= $md->simbolo ?> <span><?=number_format($venta_totales->total, 2)?></span>
            </label>
        </label>
    </div>
</div>
<div class="table-responsive">
    <table class='table table-striped dataTable table-bordered no-footer' id="tabla_caja" style="overflow:scroll">
        <thead>
        <tr>
            <th>Fecha</th>
            <th>Num Doc</th>
            <th>Cliente</th>
            <th>Vendedor</th>
            <th>Condici&oacute;n</th>
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
                    <td><?= sumCod($venta->venta_id, 4) ?></td>
                    <td><?= $venta->cliente_nombre ?></td>
                    <td><?= $venta->vendedor_nombre ?></td>
                    <td><?= $venta->condicion_nombre ?></td>
                    <td style="text-align: right; vertical-align: middle;">
                        <label style="padding: 5px; font-size: 14px; margin: 0px;" class="control-label badge label-warning panel-admin-text">
                            <?= $venta->moneda_simbolo ?> <?=$venta->condicion_id == 1 ? number_format($venta->total, 2) : number_format($venta->inicial, 2) ?>
                        </label>
                    </td>
                    <td style="text-align: center;">

                        <a class="btn btn-default" data-toggle="tooltip" style="margin-right: 5px;"
                           title="Ver" data-original-title="Ver"
                           href="#"
                           onclick="ver('<?= $venta->venta_id ?>');">
                            <i class="fa fa-search"></i>
                        </a>

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
                               onclick="anular('<?= $venta->venta_id ?>', '<?= sumCod($venta->venta_id, 6) ?>');">
                                <i class="fa fa-remove"></i>
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach ?>
        <?php endif; ?>

        </tbody>
    </table>


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

        $("#dialog_venta_imprimir").on('hidden.bs.modal', function () {
            get_ventas();
        });

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
                alert('Error');
            }
        });
    }

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
                $("#caja_venta_id").val(venta_id);
                if (data.venta.condicion_id == '1')
                    $("#vc_total_pagar").val(formatPrice(data.venta.total));
                else if (data.venta.condicion_id == '2')
                    $("#vc_total_pagar").val(formatPrice(data.venta.inicial));

                $("#vc_importe").val(formatPrice($("#vc_total_pagar").val()));
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

    function save_venta_contado(imprimir) {

        if ($("#vc_forma_pago").val() == '1' && $("#vc_vuelto").val() < 0) {
            show_msg('warning', '<h4>Error. </h4><p>El importe no puede ser menor que el total a pagar. Recomendamos una venta al Cr&eacute;dito.</p>');
            setTimeout(function () {
                $("#vc_importe").trigger('focus');
            }, 500);
            return false;
        }
        if ($("#vc_forma_pago").val() == '2' && $("#vc_num_oper").val() == '') {
            show_msg('warning', '<h4>Error. </h4><p>El campo Operaci&oacute;n # es obligatorio.</p>');
            setTimeout(function () {
                $("#vc_num_oper").trigger('focus');
            }, 500);
            return false;
        }


        var data = {
            'venta_id': $("#caja_venta_id").val(),
            'tipo_pago': $("#vc_forma_pago").val(),
            'importe': $("#vc_importe").val(),
            'vuelto': $("#vc_vuelto").val(),
            'num_oper': $("#vc_num_oper").val(),
            'tarjeta': $("#vc_tipo_tarjeta").val()
        };

        $("#loading_save_venta").modal('show');
        $("#dialog_venta_contado").modal('hide');
        $('.save_venta_contado').attr('disabled', 'disabled');

        $.ajax({
            url: '<?php echo $ruta . 'venta_new/save_venta_caja/'; ?>',
            type: 'POST',
            dataType: 'json',
            data: data,
            success: function (data) {

                if (data.success == '1') {
                    show_msg('success', '<h4>Correcto. </h4><p>La venta numero ' + data.venta.venta_id + ' se ha pagado con exito.</p>');
                    if (imprimir == '1') {
                        $("#dialog_venta_imprimir").html('');
                        $("#dialog_venta_imprimir").modal('show');

                        $.ajax({
                            url: '<?php echo $ruta . 'venta_new/get_venta_previa'; ?>',
                            type: 'POST',
                            data: {'venta_id': data.venta.venta_id},

                            success: function (data) {
                                $("#dialog_venta_imprimir").html(data);
                                $("#loading_save_venta").modal('hide');
                            }
                        });
                    } else {
                        get_ventas();
                    }
                }
                else {
                    show_msg('danger', '<h4>Error. </h4><p>Ha ocurrido un error insperado al guardar la venta.</p>');
                    $("#dialog_venta_contado").modal('show');
                    $('.save_venta_contado').removeAttr('disabled');
                }
            },
            error: function (data) {
                show_msg('danger', '<h4>Error. </h4><p>Ha ocurrido un error insperado al guardar la venta.</p>');
            },
            complete: function (data) {
                $('.save_venta_contado').removeAttr('disabled');
            }
        });
    }


    function anular(venta_id, venta) {

        $('#confirm_venta_text').attr('data-venta', venta);
        $('#confirm_venta_text').html('Estas seguro que deseas cancelar la venta ' + $('#confirm_venta_text').attr('data-venta') + '?');
        $('#confirm_venta_button').attr('onclick', 'anular_venta("' + venta_id + '");');

        $('#dialog_venta_confirm').modal('show');
    }

    function anular_venta(venta_id) {

        $("#confirm_venta_text").html($("#loading").html());

        $.ajax({
            url: '<?php echo $ruta . 'venta_new/anular_venta'; ?>',
            type: 'POST',
            data: {'venta_id': venta_id},

            success: function (data) {
                $('#dialog_venta_confirm').modal('hide');
                $.bootstrapGrowl('<h4>Correcto.</h4> <p>Venta cancelada con exito.</p>', {
                    type: 'success',
                    delay: 5000,
                    allow_dismiss: true
                });
                get_ventas();
            },
            error: function () {
                $('#confirm_venta_text').html('Estas seguro que deseas cancelar la venta ' + $('#confirm_venta_text').attr('data-venta') + '?');
                $.bootstrapGrowl('<h4>Error.</h4> <p>Ha ocurrido un error en la operaci&oacute;n</p>', {
                    type: 'danger',
                    delay: 5000,
                    allow_dismiss: true
                });
            }
        });
    }


</script>