<?php $ruta = base_url(); ?>
<style>
    .table td {
        font-size: 14px !important;
    }
</style>
<input type="hidden" id="caja_imprimir" value="1">
<?php $md = get_moneda_defecto() ?>
<div class="row">
    <?php
        $total_pendiente = 0;
        foreach ($ventas as $venta){
            $total_pendiente += $venta->condicion_id == 1 ? $venta->total : $venta->inicial;
        }
    ?>
    <div class="col-md-9"></div>
    <!--<div class="col-md-2">
        <label>Subtotal: <?= $moneda->simbolo ?> <span id="subtotal"><?= number_format($venta_totales->subtotal, 2) ?></span></label>
    </div>
    <div class="col-md-2">
        <label>IGV: <?= $moneda->simbolo ?> <span id="impuesto"><?= number_format($venta_totales->impuesto, 2) ?></span></label>
    </div>-->
    <label>Total Pendiente:
        <label style="padding: 5px; font-size: 14px; margin: 0px;"
               class="control-label badge label-warning panel-admin-text">
            <?= $moneda->simbolo ?> <span><?= number_format($total_pendiente, 2) ?></span>
        </label>
    </label>
</div>
</div>
<div class="table-responsive">
    <table class='table table-striped dataTable table-bordered no-footer' id="tabla_caja" style="overflow:scroll">
        <thead>
        <tr>
            <th>Fecha</th>
            <th>Nombre</th>
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
                    <td><?= $venta->nombre_caja ?></td>
                    <td><?= sumCod($venta->venta_id, 4) ?></td>
                    <td><?= $venta->cliente_nombre ?></td>
                    <td><?= $venta->vendedor_nombre ?></td>
                    <td><?= $venta->condicion_nombre ?></td>
                    <td style="text-align: right; vertical-align: middle;">
                        <label style="padding: 5px; font-size: 14px; margin: 0px;"
                               class="control-label badge label-warning panel-admin-text">
                            <?= $venta->moneda_simbolo ?> <?= $venta->condicion_id == 1 ? number_format($venta->total, 2) : number_format($venta->inicial, 2) ?>
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

    <div class="modal fade" id="dialog_venta_confirm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">

        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Confirmaci&oacute;n</h4>
                </div>

                <div class="modal-body ">
                    <h5 id="confirm_venta_text">Estas Seguro?</h5>

                    <div class="row">
                        <div class="col-md-4 col-md-offset-1">
                            <label>Metodos Pago</label>
                            <select id="metodo_pago" class="form-control">
                                <?php foreach ($metodos_pago as $mp): ?>
                                    <option value="<?= $mp->id_metodo ?>"><?= $mp->nombre_metodo ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label>Cuenta de Caja</label>
                            <select id="cuenta_id" class="form-control">
                                <?php foreach ($cuentas as $cuenta): ?>
                                    <option value="<?= $cuenta->id ?>"><?= $cuenta->descripcion ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-9 col-md-offset-1">
                            <?php
                            $motivos = array(
                                '01' => 'Anulaci&oacute;n de la operaci&oacute;n',
                                '02' => 'Anulaci&oacute;n por error en el RUC',
                                '03' => 'Correcci&oacute;n por error en la descripci&oacute;n',
                                '04' => 'Descuento global',
                                '05' => 'Descuento por item',
                                '06' => 'Devoluci&oacute;n total',
                                '07' => 'Devoluci&oacute;n por item',
                                '08' => 'Bonificaci&oacute;n',
                                '09' => 'Disminuci&oacute;n en el valor'
                            );
                            ?>
                            <label>Motivo</label>
                            <select id="motivo" class="form-control">
                                <option value=""></option>
                                <?php foreach ($motivos as $key => $val): ?>
                                    <option value="<?= $key ?>"><?= $val ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>


                <div class="modal-footer">
                    <button id="confirm_venta_button" type="button" class="btn btn-primary">
                        Aceptar
                    </button>

                    <button type="button" class="btn btn-danger"
                            onclick="$('#dialog_venta_confirm').modal('hide');">
                        Cancelar
                    </button>

                </div>
            </div>

            <!-- /.modal-content -->
        </div>

</div>


<script>
    $(function () {
        //CONFIGURACIONES INICIALES
        App.sidebar('close-sidebar');
        
        $("#dialog_venta_imprimir").on('hidden.bs.modal', function () {
            get_ventas();
        });



    });

    function ver(venta_id) {
        stop_get_pendientes();
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

    function cerrarDetalle(){
        $('#dialog_venta_detalle').modal('hide');
        myVar = setInterval(get_pendientes, 2000);
    }

    function cerrarDialogVenta(){
        $('#dialog_venta_contado').modal('hide');
        myVar = setInterval(get_pendientes, 2000);
    }
</script>