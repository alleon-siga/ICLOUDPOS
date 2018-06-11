<?php $ruta = base_url(); ?>
<!--<script src="<?php echo $ruta; ?>recursos/js/custom.js"></script>-->
<?php $md = get_moneda_defecto() ?>
<?php $term = diccionarioTermino() ?>
<input type="hidden" id="facturacion_electronica" value="<?= valueOptionDB('FACTURACION', 0) ?>">
<br>
<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-3">
        <label>Total Venta Credito: <?= $moneda->simbolo ?> <span
                    id="total_venta"><?= number_format($credito_totales->total_venta, 2) ?></span></label>
    </div>
    <div class="col-md-3">
        <label>Total Abonado: <?= $moneda->simbolo ?> <span
                    id="total_abonado"><?= number_format($credito_totales->total_abonado, 2) ?></span></label>
    </div>
    <div class="col-md-3">
        <label>Total Deuda: <?= $moneda->simbolo ?> <span
                    id="total_deuda"><?= number_format($credito_totales->total_deuda, 2) ?></span></label>
    </div>
</div>
<div class="table-responsive">

    <table class='table table-striped dataTable table-bordered no-footer tableStyle' id="lstPagP" name="lstPagP">
        <thead>
        <tr>
            <th># Venta</th>
            <th class='tip' title="Fecha Venta">Fecha Venta</th>
            <th># Comprobante</th>
            <th>Cliente</th>
            <th class='tip' title="Monto Credito Solicitado">Importe Venta</th>
            <th class='tip' title="Monto Cancelado">Importe Abonado</th>
            <th class='tip' title="Monto Cancelado">Pendiente de pago</th>
            <th class='tip' title="Monto Cancelado">Cuotas</th>

            <th class='tip' title="Total" tool># Cuotas Atrasado</th>
            <?php if ($local == "TODOS") { ?>
                <th>Local</th>
            <?php } ?>
            <th>Accion</th>
        </tr>
        </thead>
        <tbody>
        <?php if (count($lstVenta) > 0): ?>
            <?php foreach ($lstVenta as $v): ?>
                <tr>
                    <td><?php echo $v->Venta_id; ?></td>
                    <td style="text-align: center;"><span
                                style="display: none"><?= date('YmdHis', strtotime($v->FechaReg)) ?></span><?php echo date("d/m/Y", strtotime($v->FechaReg)) ?>
                    </td>
                    <td style="text-align: center;">
                        <?php
                        $doc = '';
                        if ($v->TipoDocumento == 1) $doc = "FA";
                        if ($v->TipoDocumento == 2) $doc = "NC";
                        if ($v->TipoDocumento == 3) $doc = "BO";
                        if ($v->TipoDocumento == 4) $doc = "GR";
                        if ($v->TipoDocumento == 5) $doc = "PCV";
                        if ($v->TipoDocumento == 6) $doc = "NP";

                        if ($v->correlativo != '')
                            echo $doc . ' ' . $v->serie . '-' . sumCod($v->correlativo, 6);
                        else
                            echo '<span style="color: #0000FF">NO FACTURADO</span>';
                        ?>
                    </td>
                    <td><?php echo $v->Cliente; ?></td>
                    <td style="text-align: right;"><?php echo $v->Simbolo . ' ' . number_format($v->MontoTotal, 2) ?></td>
                    <td style="text-align: right;"><?php echo $v->Simbolo . ' ' . number_format($v->MontoCancelado, 2) ?></td>
                    <td style="text-align: right;"><?php echo $v->Simbolo . ' ' . number_format($v->MontoTotal - $v->MontoCancelado, 2) ?></td>
                    <td style="text-align: center;"><?= $v->nro_cuotas ?></td>
                    <td style="text-align: center;"><?= $v->cuotas_atrasadas ?></td>
                    <?php if ($local == "TODOS") { ?>
                        <td style="text-align: center;"><?php echo $v->local; ?></td>
                    <?php } ?>
                    <td class='actions_big'>
                        <div class="btn-group">
                            <a class='btn btn-xs btn-default tip' title="Ver Venta"
                               onclick="visualizar(<?= $v->Venta_id; ?>)"><i
                                        class="fa fa-search"></i> Ver</a>
                            <a onclick="pagar_venta(<?= $v->Venta_id; ?>)" class='btn btn-xs btn-primary tip'
                               title="Pagar"><i
                                        class="fa fa-paypal"></i>
                                Cobrar</a>

                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else : ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Seccion Visualizar -->
<div class="modal fade" id="visualizar_venta" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel"
     aria-hidden="true">


</div>
<!--- ----------------- -->

<!-- Pagar Visualizar -->
<div class="modal fade" id="pagar_venta" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel"
     aria-hidden="true" data-backdrop="static" data-keyboard="false">

</div>
<!--- ----------------- -->
<script src="<?php echo $ruta ?>recursos/js/pages/tablesDatatables.js"></script>
<script type="text/javascript">


    $(document).ready(function () {
        TablesDatatables.init(0);

        $("#cerrar_pago_modal").on('click', function () {

            $("#pago_modal").modal('hide')
        })

    });

    function guardar_anticipado() {
        /*llama al metodo que actualiza la venta a credito*/

        $("#guardar_anticipado").attr('disabled', 'disabled')

        if ($("#metodo_anticipado").val() == "") {
            $("#guardar_anticipado").attr('disabled', false)
            var growlType = 'warning';
            $.bootstrapGrowl('<h4>Debe ingresar un metodo de pago</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

            $(this).prop('disabled', true);

            return false;
        }

        /*valido el campo banco y el campo numero de operacion*/
        var banco = ''
        if ($("#banco_anticipado").val() != undefined) {

            if ($("#banco_anticipado").val() == "") {
                $("#guardar_anticipado").attr('disabled', false)
                var growlType = 'warning';
                $.bootstrapGrowl('<h4>Debe seleccionar un banco</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }
            banco = $("#banco_anticipado").val()
        }

        var nro_operacion = ''
        if ($("#num_operacion_anticipado").val() != undefined) {

            if ($("#num_operacion_anticipado").val() == "") {
                $("#guardar_anticipado").attr('disabled', false)
                var growlType = 'warning';
                $.bootstrapGrowl('<h4>Ingrese un numero de operacion</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }
            nro_operacion = $("#num_operacion_anticipado").val()
        }
        $('#cargando_modal').modal('show')
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {
                'idVenta': $("#id_venta_anticipado").val(),
                'metodo_pago': $("#metodo_anticipado").val(),
                'nro_operacion': nro_operacion,
                'banco': banco
            },
            url: '<?php echo base_url();?>' + 'venta/pagoCuotaCredito',
            success: function (data) {
                $('#cargando_modal').modal('hide')
                if (data.success == undefined) {
                    $("#guardar_anticipado").attr('disabled', false)
                    var growlType = 'warning';

                    $.bootstrapGrowl('<h4>' + data.error + '</h4>', {
                        type: growlType,
                        delay: 2500,
                        allow_dismiss: true
                    });

                    $(this).prop('disabled', true);

                    return false;


                } else {
                    $("#guardar_anticipado").attr('disabled', 'disabled')
                    var growlType = 'success';

                    $.bootstrapGrowl('<h4>El pago se ha realizado satisfactoriamente</h4>', {
                        type: growlType,
                        delay: 2500,
                        allow_dismiss: true
                    });

                    $('#pagoadelantado').modal('hide');
                    $('#pagar_venta').modal('hide');
                    pagar_venta($("#id_venta_anticipado").val())

                }

            },
            error: function () {
                $('#cargando_modal').modal('hide')
                $("#guardar_anticipado").attr('disabled', false)
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Error al realizar la operacion</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }
        });
    }

    function pagar_venta(id) {
        $('#cargando_modal').modal('show')
        $.ajax({
            url: '<?= base_url()?>venta/vercronograma',
            type: 'post',
            data: {'idventa': id},
            success: function (data) {
                $('#cargando_modal').modal('hide')
                $("#pagar_venta").html(data);
                $('#pagar_venta').modal('show');
            }
        })
    }

    function visualizar(id) {
        $('#cargando_modal').modal('show')
        $.ajax({
            url: '<?= base_url()?>venta/verVentaCredito',
            type: 'post',
            data: {'idventa': id},
            success: function (data) {
                $('#cargando_modal').modal('hide')
                $("#visualizar_venta").html(data);
                $('#visualizar_venta').modal('show');
            }

        })
    }
    /*function pagarCuotaCredito() {
     var id= $('#val'+i).val();
     //alert("ida: "+id+" i: "+i+" id venta: "+idVenta+" monto: "+montodescontar)
     $.ajax({
     url: 'venta/pagoCuotaCredito',
     type: 'post',
     data: {'idCuota': id, 'idVenta':idVenta, 'montodescontar':montodescontar},
     success: function (data) {
     $('#botonListo'+i).show();
     $('#botonPagar'+i).hide();

     var suma=  (parseInt(i)+parseInt(1))

     $('#botonPagar'+suma).show();
     }
     })
     }*/

    function guardarPago() {
        var tipo = $('#metodo option:selected').attr('data-tipo_metodo');

        if (tipo == 'BANCO' && $('#banco_id').val() == "") {
            $.bootstrapGrowl('<h4>Debe ingresar un banco</h4>', {
                type: 'warning',
                delay: 2500,
                allow_dismiss: true
            });
            return false;
        }

        if ($("#metodo").val() == "7" && $("#tipo_tarjeta").val() == "") {
            $.bootstrapGrowl('<h4>Debe ingresar un tipo de tarjeta</h4>', {
                type: 'warning',
                delay: 2500,
                allow_dismiss: true
            });
            return false;
        }

        if ($("#metodo").val() != "3" && $("#num_oper").val() == "") {
            $.bootstrapGrowl('<h4>Es necesario el numero de operacion</h4>', {
                type: 'warning',
                delay: 2500,
                allow_dismiss: true
            });
            return false;
        }

        if (tipo == 'CAJA' && $('#caja_id').val() == "") {
            $.bootstrapGrowl('<h4>Debe ingresar una cuenta</h4>', {
                type: 'warning',
                delay: 2500,
                allow_dismiss: true
            });
            return false;
        }

        var cantidad = parseFloat($("#cantidad_a_pagar").val());
        var total = parseFloat($("#total_cuota").val());

        if (isNaN(cantidad) || cantidad <= 0) {
            $.bootstrapGrowl('<h4>Cantidad no valida</h4>', {
                type: 'warning',
                delay: 2500,
                allow_dismiss: true
            });
            return false;
        }

        if (cantidad > total) {
            $.bootstrapGrowl('<h4>Cantidad no puede ser mayor al total de la cuota</h4>', {
                type: 'warning',
                delay: 2500,
                allow_dismiss: true
            });
            return false;
        }

        var params = {
            'correlativo_cuota': $("#correlativo").val(),
            'idVenta': $("#venta_id").val(),
            'montodescontar': $("#cantidad_a_pagar").val(),
            'moneda_saldo': $("#moneda_saldo").val(),
            'idCuota': $("#id_credito_cuota").val(),
            'metodo_pago': $("#metodo").val(),
            'tipo_metodo': tipo,
            'banco': null,
            'cuenta_id': null,
            'nro_operacion': null

        };

        if ($("#metodo").val() != "3")
            params['nro_operacion'] = $("#num_oper").val();

        if (tipo == 'BANCO')
            params['banco'] = $("#banco_id").val();
        else
            params['cuenta_id'] = $("#caja_id").val();

        //if ($("#metodo").val() == "7")
            //params['banco'] = $("#tipo_tarjeta").val();


        $("#guardarPago_pagospendiente").attr('disabled', 'disabled');
        $('#cargando_modal').modal('show');

        $.ajax({
            url: '<?= base_url()?>venta/pagoCuotaCredito',
            type: 'POST',
            dataType: 'json',
            data: params,
            success: function (data) {

                if (data.success == undefined) {
                    $('#cargando_modal').modal('hide');
                    $.bootstrapGrowl('<h4>' + data.error + '</h4>', {
                        type: 'warning',
                        delay: 2500,
                        allow_dismiss: true
                    });

                } else {

                    $.bootstrapGrowl('<h4>El pago se ha realizado satisfactoriamente</h4>', {
                        type: 'success',
                        delay: 2500,
                        allow_dismiss: true
                    });
                    if ($('#facturacion_electronica').val() == 1) {
                        if (data.venta != undefined) {
                            if (data.venta.facturacion == 1) {
                                show_msg('success', '<h4>Facturacion Electronica: </h4> ' + data.venta.facturacion_nota);
                            }
                            else {
                                show_msg('success', '<h4>Facturacion Electronica: </h4> ' + data.venta.facturacion_nota);
                            }
                        }
                    }

                    $('#pago_modal').modal('hide');

                    $.ajax({
                        url: '<?= base_url()?>venta/vercronograma',
                        type: 'post',
                        data: {'idventa': $("#venta_id").val()},
                        success: function (data) {
                            $("#pagar_venta").html(data);
                        },
                        complete: function () {
                            $('#cargando_modal').modal('hide');
                        }
                    });

                }

            },
            error: function () {
                $('#cargando_modal').modal('hide');
                $.bootstrapGrowl('<h4>Error al realizar la operacion</h4>', {
                    type: 'warning',
                    delay: 2500,
                    allow_dismiss: true
                });
            },
            complete: function () {
                $("#guardarPago_pagospendiente").removeAttr('disabled');
            }
        });
    }

    function abonar(idVenta, i, montodescontar, id_credito_cuota, restante, moneda) {
        $('#cargando_modal').modal('show')
        $("#abrir_bancos_cuota").html('')

        /*asigno los valores a los input*/
        $("#venta_id").val(idVenta);
        $("#correlativo").val(i);
        $("#id_credito_cuota").val(id_credito_cuota);
        $("#total_cuota").val(restante);
        $("#cantidad_a_pagar").val('');
        $('.tipo_moneda').text(moneda);
        $("#cantidad_a_pagar").focus();
        $("#pago_modal").modal('show');
        $('#cargando_modal').modal('hide')
    }


</script>