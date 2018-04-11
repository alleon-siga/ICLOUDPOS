<?php $ruta = base_url(); ?>
<script src="<?php echo $ruta; ?>recursos/js/Validacion.js"></script>
<script type="text/javascript">


    $(document).ready(function () {
        $("#pp_excel").hide();
        $("#pp_pdf").hide();

        $("#btnBuscar").on('click', function () {
            buscar();

        });

        $("#local, #cliente_id, #vence_deuda, #moneda").on('change', function () {
            $("#lstTabla").html('');
        });

        buscar();
    });


    function buscar() {

        $('#cargando_modal').modal('show');
        $.ajax({
            type: 'POST',
            data: $('#frmBuscar').serialize(),
            url: '<?php echo base_url();?>' + 'venta/lst_reg_pagospendientes',
            success: function (data) {

                $("#lstTabla").html(data);
                $(".modal-backdrop").remove();
            },
            complete: function () {
                $('#cargando_modal').modal('hide');
            }
        });
    }

    function visualizar_monto_abonado(id_credito_cuota, id_venta, id_cuota) {
        $('#cargando_modal').modal('show')
        $.ajax({
            type: 'POST',
            data: {'id_cuota': id_cuota, 'id_venta': id_venta, 'id_credito_cuota': id_credito_cuota},
            url: '<?php echo base_url();?>' + 'venta/imprimir_pago_pendiente',
            success: function (data) {
                $('#cargando_modal').modal('hide')
                $("#visualizar_cada_historial").html(data);
                $('#visualizar_cada_historial').modal('show');
            }
        });
    }

    function generar_reporte_excel() {
        $("#abrir_local_excel").append('<input type"hidden" name="local" value="' + $("#local").val() + '">');

        document.getElementById("frmExcel").submit();
    }

    function generar_reporte_pdf() {
        $("#abrir_local_pdf").append('<input type"hidden" name="local" value="' + $("#local").val() + '">');
        document.getElementById("frmPDF").submit();
    }

    function pagoadelantado(idVenta) {
        $('#cargando_modal').modal('show')
        /*pongo el blanco el html para que no se repitan los metodos de pagos cada vez que abran el modal*/
        $("#form_group").html('');

        /*lleno los metodos de pago*/
        var optiones = ''
        optiones += '<div class="col-md-3">Metodo de Pago:</div>' +
            '<div class="col-md-3"><select class="form-control" name="metodo" id="metodo_anticipado" onchange="verificar_banco_anticipado()" ' +
            'style="width:250px">' +
            '<option value="">Seleccione</option>'
        <?php
        if(count($metodos) > 0){foreach ($metodos as $metodo) {
        if ($metodo['nombre_metodo'] == "Efectivo") {
            $selected = " selected ";
        } else {
            $selected = '';
        }
        ?>
        optiones += '<option <?= $selected; ?> '

        optiones += 'value="<?= $metodo['id_metodo'] ?>"><?= $metodo['nombre_metodo'] ?></option>'
        <?php }
        } ?>
        optiones += '</select></div><div id="abrir_banco"></div><input type="hidden" id="id_venta_anticipado">'


        $("#form_group").append(optiones)
        /*le asigno el valor de la venta al input*/
        $("#id_venta_anticipado").val(idVenta)
        $('#cargando_modal').modal('hide')
        $('#pagoadelantado').modal('show');

    }

    function verificar_banco_anticipado() {


    }

    function cerrar_pagar_venta() {

        $("#cerrar_pagar_venta").prop('disabled', 'disabled')
        $('#pagar_venta').hide();

        setTimeout(function () {
            $("#cerrar_pagar_venta").prop('disabled', 'disabled')
        }, 1000);

    }


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

        $('#banco_id, #caja_id').trigger('change');
    }


</script>
<ul class="breadcrumb breadcrumb-top">
    <li>Clientes</li>
    <li><a href="">Cuentas por cobrar</a></li>
</ul>
<div class="row-fluid">
    <div class="span12">
        <div class="block">
            <form id="frmBuscar">

                <div class="row">

                    <div class="col-md-1">
                        <label>Ubicacion:</label>
                    </div>
                    <div class="col-md-3">
                        <select name="local" id="local" class='cho form-control'>
                        <?php if(count($locales)>1): ?>
                            <option value="TODOS">TODOS</option>
                        <?php endif; ?>
                            <?php if (count($locales) > 0): ?>
                                <?php foreach ($locales as $local): ?>
                                    <option
                                            value="<?php echo $local['int_local_id']; ?>" <?= $local['int_local_id'] == $this->session->userdata('id_local') ? 'selected' : ''?>><?php echo $local['local_nombre']; ?></option>
                                <?php endforeach; ?>
                            <?php else : ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="col-md-1">
                        <label>Cliente</label>
                    </div>
                    <div class="col-md-3">

                        <select name="cliente_id" id="cliente_id" class='cho form-control'>
                            <option value="-1">TODOS</option>
                            <?php if (count($lstCliente) > 0): ?>
                                <?php foreach ($lstCliente as $cl): ?>
                                    <option
                                            value="<?php echo $cl['id_cliente']; ?>"><?php echo $cl['razon_social']; ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="col-md-1">
                        <label class="control-label panel-admin-text">Moneda:</label>
                    </div>
                    <div class="col-md-2">

                        <select name="moneda" id="moneda" class='cho form-control'>
                            <?php foreach ($monedas as $moneda): ?>
                                <option value="<?= $moneda->id_moneda ?>"
                                        data-simbolo="<?= $moneda->simbolo ?>"><?= $moneda->nombre ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-1">
                        <button type="button" id="btnBuscar" value="Buscar" class="btn btn-default">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>


                    <div class="col-md-3" style="display: none;">
                        <input type="checkbox" id="vence_deuda" name="vence_deuda" value="0">
                        <label for="vence_deuda" style="cursor: pointer;">Ver deudas que vencen hoy</label>
                        </button>
                    </div>


            </form>

        </div>
        <div class="row-fluid">
            <div class="span12">
                <div class="block">
                    <div class="block-title">

                    </div>


                    <div id="lstTabla"></div>
                </div>

                <div class="block-section" style="display: none;">


                    <div id="pp_excel">
                        <form action="<?php echo $ruta; ?>exportar/toExcel_pagoPendiente" name="frmExcel"
                              id="frmExcel" method="post">
                            <input type="hidden" name="fecIni1" id="fecIni1" class='input-small'>
                            <input type="hidden" name="fecFin1" id="fecFin1" class='input-small'>
                            <input type="hidden" name="cboCliente1" id="cboCliente1" class='input-small'>
                            <div id="abrir_local_excel"></div>
                        </form>
                    </div>
                    <a href="#" onclick="generar_reporte_excel();" class=' btn btn-lg btn-default'
                       title="Exportar a Excel"><i class="fa fa-file-excel-o"></i></a>

                    <div id="pp_pdf">
                        <form name="frmPDF" id="frmPDF"
                              action="<?php echo $ruta; ?>exportar/toPDF_pagoPendiente" target="_blank"
                              method="post">
                            <input type="hidden" name="fecIni2" id="fecIni2" class='input-small'>
                            <input type="hidden" name="fecFin2" id="fecFin2" class='input-small'>
                            <input type="hidden" name="cboCliente2" id="cboCliente2" class='input-small'>
                            <div id="abrir_local_pdf"></div>
                        </form>
                    </div>
                    <a href="#" onclick="generar_reporte_pdf();" class='btn btn-lg btn-default'
                       title="Exportar a PDF"><i class="fa fa-file-pdf-o"></i> </a>

                </div>
            </div>
        </div>

        <div class="modal fade" id="pagoadelantado" tabindex="-1" role="dialog"
             aria-labelledby="myModalLabel"
             aria-hidden="true">
            <div class="modal-dialog" style="width: 50%">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Pago Anticipado</h4>
                    </div>
                    <div class="modal-body">
                        <form id="">
                            <div class="row">
                                <div class="col-md-12"><h4>Est&aacute; seguro de realizar este pago anticipado?</h4>
                                    <div class="form-group" id="form_group">


                                    </div>
                                </div>
                        </form>
                        <br>
                    </div>
                    <div class="modal-footer">
                        <a href="#" class="btn btn-default" id="guardar_anticipado" onclick="guardar_anticipado()"><i
                                    class=""></i> Pagar</a>
                        <a href="#" class="btn btn-default" id="cerrar" data-dismiss="modal"
                        >Cancelar</a>
                    </div>
                </div>
            </div>
        </div>


        <script>
            $(document).ready(function () {
                $('select').chosen();
                //$(".input-datepicker").datepicker({format: 'dd-mm-yyyy'});
            })
        </script>