<style type="text/css">
    .pad-5 .row {
        margin-bottom: 10px;
    }
</style>
<div class="modal-dialog" style="width: 60%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close cerrar_pagar_venta">&times;</button>
            <h3>Realizar Pago de Cuota - <?= $cliente->razon_social ?></h3>
        </div>
        <div class="modal-body">
            <div class="row-fluid force-margin">
                <div class="row-fluid">
                    <div class="box">
                        <div class="box-content box-nomargin">
                            <div id="lstTabla" class="table-responsive">
                                <table class="table dataTable table-bordered table-striped tableStyle">
                                    <thead>
                                    <th>N° Cuota</th>
                                    <th># Unico</th>
                                    <th>Vencimiento</th>
                                    <th>Dias atraso</th>
                                    <th>Total</th>
                                    <th>Pago Pendiente</th>
                                    <th></th>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $i = 1;

                                    /*esta variable para guardarla y utilizarla en caso de que se haga un pago anticipado*/
                                    $id_venta = '';

                                    /*para mostrar el boton de pago anticipado y validar si ya pago la deuda */
                                    $validar_si_cancelo_total = true;

                                    /*para validar si entro al menos una vez en la primera fecha menor a la de expiracion*/
                                    $validar_utimafecha = false;
                                    $flag_pago = true;

                                    foreach ($cronogramas as $pago) {
                                        $id_venta = $pago->id_venta;
                                        $idletra = $pago->nro_letra;
                                        ?>

                                        <tr>
                                            <td align="center"><?php echo $idletra; ?><input type="hidden"
                                                                                             id="val<?php echo $i; ?>"
                                                                                             value="<?php echo $idletra; ?>">
                                            </td>
                                            <td align="center"><?= $pago->numero_unico ?></td>
                                            <td align="center"><?= date("d-m-Y", strtotime($pago->fecha_vencimiento)) ?></td>
                                            <td align="center"><?= $pago->atraso ?></td>
                                            <td align="center"><?= $moneda[0]['simbolo'] . " " . number_format($pago->monto, 2) ?></td>
                                            <td align="center"><?php if ($pago->monto_restante == null) {
                                                    echo $moneda[0]['simbolo'] . " " . number_format($pago->monto, 2);
                                                    $restante = number_format($pago->monto, 2);
                                                } else {
                                                    $restante = number_format($pago->monto_restante, 2);
                                                    echo $moneda[0]['simbolo'] . " " . number_format($pago->monto_restante, 2);
                                                } ?></td>
                                            <?php if ($pago->ispagado): ?>
                                                <td align="center">
                                                    PAGADO
                                                </td>
                                            <?php else: ?>
                                                <?php if ($flag_pago): ?>
                                                    <td align="center" id="botonPagar<?php echo $i; ?>">

                                                        <a class="btn btn-xs btn-primary"
                                                           onclick="abonar(<?= $pago->id_venta; ?>,<?= $i; ?>,'<?= str_replace(',', '', $pago->monto) ?>',<?= $pago->id_credito_cuota ?>,'<?= str_replace(',', '', $restante) ?>','<?= $moneda[0]['simbolo'] ?>')">
                                                            <i class="fa fa-paypal"></i> Cobrar</a>
                                                    </td>
                                                    <?php $flag_pago = false; ?>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </tr>
                                        <?php
                                        $i++;
                                    } ?>
                                    </tbody>
                                </table>

                            </div>
                        </div>

                        <?php if ($this->session->userdata('PAGOS_ANTICIPADOS') == "SI" and $validar_si_cancelo_total == false) { ?>
                            <div class="form-group">
                                <button id="pago_anticipado" class="btn btn-default"
                                        onclick="pagoadelantado(<?= $id_venta ?>)">Pago Anticipado
                                </button>

                            </div>
                        <?php } ?>

                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-danger cerrar_pagar_venta" id="cerrar_pagar_venta">Salir</button>
        </div>
    </div>
</div>


<div class="modal fade" id="pago_modal" tabindex="-1" role="dialog" style="z-index: 999999;"
     aria-labelledby="myModalLabel"
     aria-hidden="true"
     data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" style="width: 40%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" onclick="$('#pago_modal').modal('hide');" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title">Cobrar Cuota</h4>
            </div>
            <div class="modal-body">
                <form id="form" class="pad-5">
                    <div class="row">
                        <div class="col-md-5">
                            <label class="control-label panel-admin-text">Importe de la Cuota</label>
                        </div>
                        <div class="col-md-7">
                            <div class="input-group">
                                <input type="hidden" id="correlativo">
                                <div class="input-group-addon tipo_moneda"></div>
                                <input style="text-align: right;" type="text" id="total_cuota" value="" class="form-control" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-5">
                            <label class="control-label panel-admin-text">Metodo de Pago</label>
                        </div>
                        <div class="col-md-7">
                            <select class="form-control" name="metodo" id="metodo" onchange="verificar_banco_cuota()">
                                <?php
                                if (count($metodos) > 0) {
                                    foreach ($metodos as $metodo) { ?>
                                        <option <?php if ($metodo['id_metodo'] == "3") echo "selected"; ?>
                                                data-tipo_metodo="<?= $metodo['tipo_metodo'] ?>"
                                                value="<?= $metodo['id_metodo'] ?>">
                                            <?= $metodo['nombre_metodo'] ?>
                                        </option>
                                    <?php }
                                } ?>
                            </select>
                        </div>
                    </div>

                    <div class="row caja_block">
                        <div class="col-md-5">
                            <label class="control-label panel-admin-text">Seleccione la Cuenta</label>
                        </div>
                        <div class="col-md-7">
                            <select name="caja_id" id="caja_id" class="form-control">
                                <option value="">Seleccione</option>
                                <?php foreach ($cajas as $caja): ?>
                                    <option
                                            value="<?= $caja->id ?>"
                                            data-tasa="<?= $caja->tasa_soles ?>"
                                            data-moneda_id="<?= $caja->id_moneda ?>"
                                            data-moneda_nombre="<?= $caja->nombre ?>"
                                        <?= $caja->principal == '1' && $venta->id_moneda == $caja->id_moneda ? 'selected' : '' ?>>
                                        <?= $caja->nombre ?> |
                                        <?= $caja->descripcion ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>


                    <div class="row" id="banco_block" style="display: none;">
                        <div class="col-md-5">
                            <label class="control-label panel-admin-text">Seleccione el Banco</label>
                        </div>
                        <div class="col-md-7">
                            <select name="banco_id" id="banco_id" class="form-control">
                                <option value="">Seleccione</option>
                                <?php foreach ($bancos as $banco): ?>
                                    <option
                                            value="<?= $banco->banco_id ?>"
                                            data-tasa="<?= $banco->tasa_soles ?>"
                                            data-moneda_id="<?= $banco->moneda_id ?>"
                                            data-moneda_nombre="<?= $banco->nombre ?>"
                                    ><?= $banco->banco_nombre ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>

                    <div class="row" id="tipo_cambio_block" style="display: none;">
                        <div class="col-md-5">
                            <label for="tipo_cambio" class="control-label panel-admin-text">Cantidad en <span
                                        id="moneda_nombre"></span></label>
                        </div>
                        <div class="col-md-4">
                            <label>Tipo de cambio</label>
                            <input type="text" id="tipo_cambio" name="tipo_cambio"
                                   class="form-control" autocomplete="off"
                                   value="">
                        </div>
                        <div class="col-md-3">
                            <label>Importe</label>
                            <input type="text" id="moneda_saldo" name="moneda_saldo"
                                   class="form-control" autocomplete="off"
                                   value="">
                        </div>
                    </div>


                    <div class="row" id="tipo_tarjeta_block" style="display:none;">
                        <div class="col-md-5">
                            <label for="tipo_tarjeta" class="control-label panel-admin-text">Tipo de Tarjeta:</label>
                        </div>
                        <div class="col-md-7">
                            <select class="form-control" id="tipo_tarjeta" name="tipo_tarjeta">
                                <option value="">Seleccione</option>
                                <?php foreach ($tarjetas as $tarjeta) : ?>
                                    <option value="<?php echo $tarjeta->id ?>"><?php echo $tarjeta->nombre ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>


                    <div class="row" id="operacion_block" style="display: none;">
                        <div class="col-md-5">
                            <label id="num_oper_label" class="control-label panel-admin-text">Nro de
                                Operaci&oacute;n</label>
                        </div>
                        <div class="col-md-7">
                            <input type="text" id="num_oper" name="num_oper"
                                   class="form-control" autocomplete="off"
                                   value="">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-5">
                            <label class="control-label panel-admin-text">Cantidad a Abonar</label>
                        </div>
                        <div class="col-md-7">
                            <div class="input-group">
                                <div class="input-group-addon tipo_moneda"></div>
                                <input type="hidden" id="correlativo">
                                <input type="hidden" id="venta_id">
                                <input type="hidden" id="id_credito_cuota">
                                <input type="number" id="cantidad_a_pagar" name="cantidad_a_pagar" value="" class="form-control">
                            </div>
                            <br>
                            <input style="cursor: pointer;" type="checkbox" id="check_all"> <label style="cursor: pointer;" for="check_all">Cobrar todo</label>
                        </div>
                    </div>
                </form>
                <br>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn btn-primary" id="guardarPago_pagospendiente" onclick="guardarPago()"><i
                            class=""></i> Cobrar Cuota</a>
                <a href="#" class="btn btn-danger" id="cerrar_pago_modal" onclick="$('#pago_modal').modal('hide');">Salir</a>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="visualizarPago" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel"
     aria-hidden="true">
</div>
<script>
    $(document).ready(function () {
        $('#check_all').on('click', function(){
            if($(this).prop('checked')){
                $('#cantidad_a_pagar').val($('#total_cuota').val());
            }else{
                $('#cantidad_a_pagar').val('');
            }
        });
        $('#banco_id, #caja_id').on('change', function () {
            var moneda_id = $(this).find('option:selected').first().attr('data-moneda_id');

            if ($(this).val() != '' && moneda_id != $('#MONEDA_DEFECTO_ID').val()) {
                var tasa = $(this).find('option:selected').first().attr('data-tasa');
                $('#tipo_cambio').val(tasa);
                $('#moneda_nombre').html($(this).find('option:selected').first().attr('data-moneda_nombre'));
                $('#tipo_cambio_block').show();
                $('#cantidad_a_pagar').attr('readonly', 'readonly');
            }
            else {
                $('#tipo_cambio').val('');
                $('#cantidad_a_pagar').removeAttr('readonly');
                $('#tipo_cambio_block').css('display', 'none');
            }
        });

        $('#moneda_saldo, #tipo_cambio').on('keyup', function () {
            var tasa = isNaN(parseFloat($('#tipo_cambio').val())) ? 1 : parseFloat($('#tipo_cambio').val());
            var moneda_saldo = isNaN(parseFloat($('#moneda_saldo').val())) ? 1 : parseFloat($('#moneda_saldo').val());

            $('#cantidad_a_pagar').val(formatPrice(parseFloat(moneda_saldo * tasa)));

        });

        $('#banco_id, #caja_id').trigger('change');

        $(".cerrar_pagar_venta").on('click', function () {
            $("#pago_modal").modal('hide');
            $("#pagar_venta").modal('hide');
            buscar();
        });

    });


</script>

