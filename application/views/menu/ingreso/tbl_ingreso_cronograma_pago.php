<style type="text/css">
    .pad-5 .row {
        margin-bottom: 10px;
    }
</style>
<div class="modal-dialog" style="width: 60%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close cerrar_pagar_venta">&times;</button>
            <h3>Realizar Pago de Cuota - <?= $proveedor->proveedor_nombre ?></h3>
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

                                    /*para mostrar el boton de pago anticipado y validar si ya pago la deuda */
                                    $validar_si_cancelo_total = true;

                                    /*para validar si entro al menos una vez en la primera fecha menor a la de expiracion*/
                                    $validar_utimafecha = false;
                                    $flag_pago = true;

                                    foreach ($cronogramas as $pago) {
                                        $id = $pago->id;
                                        $idletra = $pago->letra;
                                        ?>

                                        <tr>
                                            <td align="center"><?php echo $idletra; ?><input type="hidden"
                                                                                             id="val<?php echo $i; ?>"
                                                                                             value="<?php echo $idletra; ?>">
                                            </td>
                                            <td align="center"><?= date("d/m/Y", strtotime($pago->fecha_vencimiento)) ?></td>
                                            <td align="center"><?= $pago->atraso ?></td>
                                            <td align="center"><?= $ingreso->simbolo . " " . number_format($pago->monto, 2) ?></td>
                                            <td align="center"><?= $ingreso->simbolo . " " . number_format($pago->monto - $pago->monto_pagado, 2) ?></td>
                                            <?php if ($pago->pagado): ?>
                                                <td align="center">
                                                    PAGADO
                                                </td>
                                            <?php else: ?>
                                                <?php if ($flag_pago): ?>
                                                    <td align="center" id="botonPagar<?php echo $i; ?>">

                                                        <a class="btn btn-xs btn-primary"
                                                           onclick="abonar(
                                                            <?= $ingreso->id_ingreso; ?>,
                                                            <?= $i; ?>,
                                                            <?= $pago->id ?>,
                                                            <?= $pago->monto ?>,
                                                            <?= $pago->monto_pagado?>,
                                                            '<?= $ingreso->simbolo ?>')">
                                                            <i class="fa fa-paypal"></i> Pagar</a>
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
                <h4 class="modal-title">Pagar Cuota</h4>
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
                                                value="<?= $metodo['id_metodo'] ?>"><?= $metodo['nombre_metodo'] ?></option>
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
                                            value="<?= $caja->id ?>" <?= $caja->principal == '1' ? 'selected' : '' ?>><?= $caja->descripcion ?></option>
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
                                            value="<?= $banco->banco_id ?>"><?= $banco->banco_nombre ?></option>
                                <?php endforeach ?>
                            </select>
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
                                <input type="hidden" id="compra_id">
                                <input type="hidden" id="id_credito_cuota">
                                <input type="number" id="cantidad_a_pagar" name="cantidad_a_pagar" value="" class="form-control">
                            </div>
                            <br>
                            <input style="cursor: pointer;" type="checkbox" id="check_all"> <label style="cursor: pointer;" for="check_all">Pagar todo</label>
                        </div>
                    </div>
                </form>
                <br>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn btn-primary" id="guardarPago_pagospendiente" onclick="guardarPago()"><i
                            class=""></i> Pagar Cuota</a>
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

        $(".cerrar_pagar_venta").on('click', function () {
            $("#pago_modal").modal('hide');
            $("#pagar_venta").modal('hide');
            buscar();
        });


    });

    function verificar_banco_cuota() {

        $("#banco_id").val("");
        $("#tipo_tarjeta").val("");
        $("#num_oper").val("");
        $("#cantidad_a_pagar").val($("#total_cuota").val());
        var tipo = $("#metodo option:selected").attr('data-tipo_metodo');
        var metodo = $("#metodo").val();

        $("#tipo_tarjeta_block").hide();
        $("#banco_block").hide();
        $("#operacion_block").show();
        $(".caja_block").hide();

        switch (tipo) {
            case 'CAJA': {
                $(".caja_block").show();
                if (metodo == '3')
                    $("#operacion_block").hide();

                if (metodo == '7')
                    $("#tipo_tarjeta_block").show();
                break;
            }
            case 'BANCO': {
                $("#banco_block").show();
                $("#operacion_block").show();
                break;
            }
        }
    }

    function guardarPago() {
        var tipo = $('#metodo option:selected').attr('data-tipo_metodo');

        if(tipo == 'BANCO' && $('#banco_id').val() == ""){
            $.bootstrapGrowl('<h4>Debe ingresar un banco</h4>', {
                type: 'warning',
                delay: 2500,
                allow_dismiss: true
            });
            return false;
        }

        if($("#metodo").val()=="7" && $("#tipo_tarjeta").val()==""){
            $.bootstrapGrowl('<h4>Debe ingresar un tipo de tarjeta</h4>', {
                type: 'warning',
                delay: 2500,
                allow_dismiss: true
            });
            return false;
        }

        if($("#metodo").val()!="3" && $("#num_oper").val()==""){
            $.bootstrapGrowl('<h4>Es necesario el numero de operacion</h4>', {
                type: 'warning',
                delay: 2500,
                allow_dismiss: true
            });
            return false;
        }

        if(tipo == 'CAJA' && $('#caja_id').val() == ""){
            $.bootstrapGrowl('<h4>Debe ingresar una cuenta</h4>', {
                type: 'warning',
                delay: 2500,
                allow_dismiss: true
            });
            return false;
        }

        var cantidad = parseFloat($("#cantidad_a_pagar").val());
        var total = parseFloat($("#total_cuota").val());

        if(isNaN(cantidad) || cantidad <= 0){
            $.bootstrapGrowl('<h4>Cantidad no valida</h4>', {
                type: 'warning',
                delay: 2500,
                allow_dismiss: true
            });
            return false;
        }

        if(cantidad > total){
            $.bootstrapGrowl('<h4>Cantidad no puede ser mayor al total de la cuota</h4>', {
                type: 'warning',
                delay: 2500,
                allow_dismiss: true
            });
            return false;
        }

        var params = {
            'correlativo_cuota':  $("#correlativo").val(),
            'ingreso_id':$("#compra_id").val(),
            'montodescontar':$("#cantidad_a_pagar").val(),
            'cuota_id': $("#id_credito_cuota").val(),
            'metodo_pago':$("#metodo").val(),
            'tipo_metodo': tipo,
            'banco':null,
            'cuenta_id':null,
            'nro_operacion':null

        };

        if($("#metodo").val()!="3")
            params['nro_operacion'] = $("#num_oper").val();

        if(tipo == 'BANCO')
            params['banco'] = $("#banco_id").val();
        else
            params['cuenta_id'] = $("#caja_id").val();

        if($("#metodo").val()=="7")
            params['banco'] = $("#tipo_tarjeta").val();


        $("#guardarPago_pagospendiente").attr('disabled','disabled');
        $('#cargando_modal').modal('show');

        $.ajax({
            url: '<?= base_url()?>ingresos/pagoCuotaCredito',
            type: 'POST',
            dataType:'json',
            data: params,
            success: function (data) {

                if(data.success==undefined){
                    $('#cargando_modal').modal('hide');
                    $.bootstrapGrowl('<h4>'+data.error+'</h4>', {
                        type: 'warning',
                        delay: 2500,
                        allow_dismiss: true
                    });

                }else{

                    $.bootstrapGrowl('<h4>El pago se ha realizado satisfactoriamente</h4>', {
                        type: 'success',
                        delay: 2500,
                        allow_dismiss: true
                    });

                    $('#pago_modal').modal('hide');

                    $.ajax({
                        url: '<?= base_url()?>ingresos/ver_deuda',
                        type: 'post',
                        data: {'id_ingreso': $("#compra_id").val()},
                        success: function (data) {
                            $("#pagar_venta").html(data);
                        },
                        complete: function(){
                            $('#cargando_modal').modal('hide');
                        }
                    });

                }

            },
            error : function(){
                $('#cargando_modal').modal('hide');
                $.bootstrapGrowl('<h4>Error al realizar la operacion</h4>', {
                    type: 'warning',
                    delay: 2500,
                    allow_dismiss: true
                });
            },
            complete: function(){
                $("#guardarPago_pagospendiente").removeAttr('disabled');
            }
        });
    }

    function abonar(id, i, credito_cuota_id, monto, pagado, moneda) {
        $('#cargando_modal').modal('show')
        $("#abrir_bancos_cuota").html('')

        /*asigno los valores a los input*/
        $("#compra_id").val(id);
        $("#correlativo").val(i);
        $("#id_credito_cuota").val(credito_cuota_id);
        $("#total_cuota").val(parseFloat(monto - pagado).toFixed(2));
        $("#cantidad_a_pagar").val('');
        $("#cantidad_a_pagar").focus();
        $('.tipo_moneda').text(moneda);
        $("#pago_modal").modal('show');
        $('#cargando_modal').modal('hide')
    }

</script>

