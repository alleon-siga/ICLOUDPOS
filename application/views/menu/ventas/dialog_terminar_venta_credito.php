<?php if (validOption("VISTA_CREDITO", 'AVANZADO', 'SIMPLE')): ?>
    <div class="modal-dialog" style="width: 75%;">
        <div class="modal-content">
            <div class="modal-header">
                <h4>Venta a Credito</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group" style="text-align: center;">
                        <div class="col-md-2">
                            <label for="contado" class="control-label">Precio contado:</label>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <input type="number" name="precio_contado" id="precio_contado"
                                       class='form-control input-sm' readonly="readonly" maxlength="8"
                                       value="">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label for="credito" class="control-label">Precio credito:</label>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <input type="number" name="precio_credito" id="precio_credito"
                                       class='form-control input-sm' readonly="readonly" maxlength="8"
                                       value="">
                            </div>
                        </div>
                        <div class="col-md-1">
                            <label for="fecha" class="control-label">Fecha actual:</label>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="text" name="fecha_actual" id="fecha_actual"
                                       class='form-control input-sm datepicker' readonly="readonly"
                                       maxlength="8"
                                >
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group" style="text-align: center;">
                        <div class="col-md-8">
                            <fieldset>
                                <legend>Datos del credito</legend>

                                <div class="row">
                                    <div class="form-group" style="text-align: center;">
                                        <div class="col-md-3">
                                            <label for="inicial" class="control-label">Inicial:</label>
                                        </div>
                                        <div class="col-md-2">
                                            <input type="text" name="inicial" id="inicial"
                                                   class='form-control input-sm' autofocus="autofocus"
                                                   maxlength="8"
                                                   value="">
                                        </div>

                                        <div class="col-md-3 checkox">
                                            <label id="mtoporcen">
                                                <input type="checkbox" name="porcentaje" id="porcentaje"
                                                       style="" checked>
                                                <?php echo $inicial_por[0]["config_value"] ?> %
                                            </label>
                                        </div>
                                        <div class="col-md-2">
                                            <label for="dias_pago" class="control-label">Dias de
                                                pago:</label>
                                        </div>
                                        <div class="col-md-2">
                                            <input type="number" name="dias_pago" id="dias_pago"
                                                   class='form-control' autofocus="autofocus"
                                                   maxlength="2"
                                                   value="">
                                        </div>
                                    </div>
                                </div>
                                <br>

                                <div class="row">
                                    <!-- <div class="form-group" style="text-align: center;"> -->
                                    <div class="col-md-3">
                                        <label for="num_cuota" class="control-label">Numero de
                                            cuota:</label>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" name="num_cuota" id="num_cuota"
                                               class='form-control input-sm' min="0"
                                               max="<?php echo $this->session->userdata("MAXIMO_CUOTAS_CREDITO") ?>"
                                               maxlength="2"
                                               value="<?php echo $this->session->userdata("MAXIMO_CUOTAS_CREDITO") ?>">
                                        <p class="alert alert-danger hide"> El número de cuotas no puede
                                            exceder del número condigurado en opciones</p>
                                    </div>
                                    <div class="col-md-1"></div>
                                    <div class="col-md-2"></div>
                                    <div class="col-md-4">

                                        <button type="button" id="btnGenerarCuotas"
                                                class="btn btn-primary btn-block"
                                                onclick="GenerarCuotas()">
                                            Generar Cuotas
                                        </button>

                                    </div>
                                    <!--   <div class="col-md-2">
                                          <label for="num_cuota" class="control-label">&nbsp;</label>
                                      </div> -->

                                    <!-- </div> -->
                                </div>


                            </fieldset>
                        </div>
                        <div class="col-md-4">
                            <fieldset>
                                <legend>Adicionales</legend>
                                <div class="row">
                                    <div class="form-group">
                                        <div class="col-md-4" style="text-align: center;">
                                            <label for="intereses"
                                                   class="control-label">Intereses</label>
                                        </div>
                                        <div class="col-md-5" style="text-align: center;">
                                            <input type="number" name="intereses" id="intereses"
                                                   class='form-control input-sm' autofocus="autofocus"
                                                   value="<?php echo $tasa_interes[0]["config_value"]; ?>">
                                        </div>
                                        <div class="col-md-3">
                                            <div class="pull-left">%</div>
                                            <div class="clearfix"></div>
                                        </div>

                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="form-group">
                                        <div class="col-md-6" style="text-align: center;">
                                            <label for="cliente_nuevo" class="cliente_nuevo">Cliente
                                                Nuevo</label>
                                        </div>
                                        <div class="col-md-6" style="text-align: center;">
                                            <div class="checbox pull-left">
                                                <label>
                                                    <input type="checkbox" name="cliente_nuevo"
                                                           id="cliente_nuevo" value="">&nbsp;Si
                                                </label>
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group">
                                        <div id="divcantpagar" class="col-md-6 hide"
                                             style="text-align: center;">
                                            <label for="cant_pagar" class="cant_pagar">Cant a
                                                Pagar:</label>
                                        </div>
                                        <div id="divmto_ini_res" class="col-md-6 hide"
                                             style="text-align: center;">
                                            <input type="number" name="mto_ini_res" id="mto_ini_res"
                                                   class='form-control input-sm'
                                                   value="">
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-12 hide " id="garante_menesaje">

                    </div>
                </div>
                <div class="row" style="width: 90%; padding-left: 10%">
                    <fieldset>
                        <legend>Garante</legend>
                        <div class="col-md-2">
                            <label for="cant_pagar" class="cant_pagar">Garante:</label>
                        </div>
                        <div id="divgarante" class="col-md-4" class='form-control'>
                            <select class="form-control" id="garante" name="garante">
                                <?php foreach ($garantes as $gara) { ?>
                                    <option
                                        value="<?php echo $gara["dni"] ?>"><?php echo $gara["nombre_full"] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <a href="#" class="btn btn-default" id="btnNuevoGarante"
                               onclick="LoadFormGarante();" type="button"><i
                                    class="fa fa-save"></i> Nuevo Garante
                            </a>
                        </div>
                    </fieldset>
                </div>
                <br>
                <div class="row">
                    <center>
                        <div class="col-md-12 hide" id="procesando">
                            <b>Procesando...</b>
                        </div>
                    </center>
                </div>
                <div class="row" id="tbCroCuotas">
                    <div class="col-md-4">
                        <fieldset>
                            <legend>Proyeciones de pago por cuotas</legend>
                            <table id="tbproy_cuotas"
                                   class='table table-striped'
                                   id="tbCuotas">
                                <thead>
                                <tr>
                                    <td>Nro Cuota</td>
                                    <td>Monto</td>
                                <tr>
                                </thead>
                            </table>
                        </fieldset>
                    </div>
                    <div class="col-md-8" style="text-align: center">
                        <fieldset>
                            <legend>Cronograma de Pagos</legend>
                            <table id="tbcrono_pagos"
                                   class='table table-striped'>
                                <thead>
                                <tr>
                                    <td>Nro Letra</td>
                                    <td>Fecha Giro</td>
                                    <td>Fecha Vencimiento</td>
                                    <td>Monto</td>
                                <tr>
                                <tbody>
                                </tbody>
                                </thead>
                            </table>
                        </fieldset>
                    </div>
                </div>
                <br><br>
            </div>
            <div class="modal-footer">

                <div class="row">
                    <div class="col-md-12">

                        <a href="#" class="btn btn-default" id="btnRealizarVentaAndViewCr"
                           onclick="<?php echo !isset($cobro) ? 'hacerventa(1);' : '' ?>" type="button"><i
                                class="fa fa-print"></i> (F6) Grabar e imprimir
                        </a>
                        <button class="btn btn-default closegenerarventa" type="button"
                                onclick="$('#generarventa1').modal('hide');"><i
                                class="fa fa-close"></i> Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php elseif (validOption("VISTA_CREDITO", 'SIMPLE', 'SIMPLE')): ?>
    <div class="modal-dialog" style="width: 40%">
        <div class="modal-content">
            <div class="modal-header">
                <h4>Venta a Credito</h4>
            </div>
            <div class="modal-body panel-venta-left">

<?php $md = get_moneda_defecto()?>
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label for="totApagar2" class="control-label panel-admin-text">Total a Pagar:</label>
                        </div>
                        <div class="col-md-9">
                            <div class="input-prepend input-append input-group">
                                <label id="lblSim10" class="input-group-addon"><?= $md->simbolo ?></label><input
                                    type="number"
                                    class='input-square input-small form-control'
                                    min="0.0"
                                    step="0.1"
                                    value="0.0"
                                    id="precio_contado"
                                    name="precio_contado"
                                    readonly
                                    onkeydown="return soloDecimal(this, event);">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-3">
                            <label for="totApagar2" class="control-label panel-admin-text">Pago a cuenta:</label>
                        </div>
                        <div class="col-md-9">
                            <div class="input-prepend input-append input-group">
                                <label id="lblSim11" class="input-group-addon"><?= $md->simbolo ?></label><input
                                    type="number"
                                    class='input-square input-small form-control'
                                    min="0.0"
                                    step="0.1"
                                    value="0.0"
                                    id="pago_cuenta"
                                    name="pago_cuenta"
                                    onkeydown="return soloDecimal(this, event);">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-3">
                            <label for="totApagar2" class="control-label panel-admin-text">Monto Restante:</label>
                        </div>
                        <div class="col-md-9">
                            <div class="input-prepend input-append input-group">
                                <label id="lblSim12" class="input-group-addon"><?= $md->simbolo ?></label><input
                                    type="number"
                                    class='input-square input-small form-control'
                                    min="0.0"
                                    step="0.1"
                                    value="0.0"
                                    id="deuda_restante"
                                    name="deuda_restante"
                                    readonly
                                    onkeydown="return soloDecimal(this, event);">
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">

                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-default" style="margin-bottom:5px" type="button"
                                id="realizarventa_credito_simple"
                                onclick="<?php echo !isset($cobro) ? 'insert_venta(0);' : '' ?>"><i
                                class="fa fa-save"></i>Guardar


                        </button>

                        <a href="#" class="btn btn-default" style="margin-bottom:5px"
                           id="btnRealizarVentaAndView_credito_simple"
                           onclick="<?php echo !isset($cobro) ? 'insert_venta(1);' : '' ?>" type="button"><i
                                class="fa fa-print"></i> (F6)Guardar e imprimir
                        </a>
                        <button class="btn btn-default" style="margin-bottom:5px"
                                type="button"
                                onclick="$('#generarventa1').modal('hide');"><i
                                class="fa fa-close"></i> Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>


<div class="modal fade" id="advertencia" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false"
     aria-hidden="true">

    <div class="modal-dialog" style="width: 40%">
        <div class="modal-content">
            <div class="modal-header">
                <h4>Advertencia</h4>
            </div>
            <div class="modal-body panel-venta-left">

                <div class="row">
                    <div class="col-md-12">
                <h4>La cuenta a pagar es igual o mayor que el total del importe. Le recomendamos realizar una venta al contado.</h4>
                    </div>
                </div>
                <div class="modal-footer">

                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" id="pago_cuenta_saved" value="">
                            <button class="btn btn-primary" style="margin-bottom:5px" type="button"
                                    id="realizarventa_exec"
                                    onclick="cambiar_contado();">Hacer Venta al Contado</button>

                            <button class="btn btn-danger" style="margin-bottom:5px"
                                    type="button"
                                    onclick="$('#advertencia').modal('hide');">Cancelar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>


<?php endif; ?>
<div style="display: none;" id="credito_vista" data-vista="<?php echo validOption("VISTA_CREDITO", 'SIMPLE', 'SIMPLE') ? 'SIMPLE' : 'AVANZADO'?>"></div>
<script>
    var lst_cuotas = [];

    var currentDate = new Date();
    $('.datepicker').datepicker("setDate", currentDate);

    function insert_venta(type){
        if(parseFloat($("#pago_cuenta").val()) >= parseFloat($("#precio_contado").val())){
            $("#pago_cuenta_saved").val($("#pago_cuenta").val());
            $("#advertencia").modal('show');
        }
        else{
            hacerventa(type);
        }
    }

    function cambiar_contado(){
        $("#advertencia").modal('hide');
        $("#generarventa1").modal('hide');
        $("#generarventa").modal('show');

        $("#forma_pago").change().trigger("chosen:updated");
        $("#cboModPag").val("1").trigger("chosen:updated");
        $("#cboModPag").change();


        
    }

    $('#advertencia').on('hidden.bs.modal', function (e) {
        $("#pago_cuenta").val($("#pago_cuenta_saved").val());
        $("#deuda_restante").val(parseFloat($("#precio_contado").val()) - parseFloat($("#pago_cuenta").val()));
    });

    function addCuotasToArray(nro_letra, fecha_giro, fecha_vencimiento, monto, isgiro) {
        var cuotas = {};

        cuotas.nro_letra = nro_letra;
        cuotas.fecha_giro = fecha_giro;

        cuotas.fecha_vencimiento = fecha_vencimiento;
        cuotas.monto = monto;
        cuotas.isgiro = isgiro;

        lst_cuotas.push(cuotas);

    }


    function LoadCuotas() {
        //lst_cuotas
        lst_cuotas = [];

        var table = document.getElementById('tbproy_cuotas'),
            rows = table.getElementsByTagName('tr'),
            i, j, cells, customerId;


        for (i = 1, j = rows.length; i < j; ++i) {
            cells = rows[i].getElementsByTagName('td');
            if (!cells.length) {
                continue;
            }
            customerId = cells[0].innerHTML;
            addCuotasToArray(cells[0].innerHTML, null, null, cells[1].innerHTML, 0);
        }

        table = document.getElementById('tbcrono_pagos'),
            rows = table.getElementsByTagName('tr'),
            i, j, cells, customerId;

        for (i = 1, j = rows.length; i < j; ++i) {
            cells = rows[i].getElementsByTagName('td');
            //console.log(cells);
            if (!cells.length) {
                continue;
            }
            customerId = cells[0].innerHTML;
            addCuotasToArray(cells[0].innerHTML, cells[1].innerHTML, cells[2].innerHTML, cells[3].innerHTML, 1);
        }
    }

    function LoadFormGarante() {
        $("#formgarante").modal('show');
    }

    function GenerarCuotas() {

        if (parseInt($("#num_cuota").val()) > parseInt($("#num_cuota").attr("max"))) {
            // $("#generarventa1").modal("hide");

            $.bootstrapGrowl('<h4>Error en los datos:</h4> <p>El número de cuotas configurado es menor al colocado en número de cuota, por favor, reconfigure el valor de Cuotas máximas o coloque un valor menor o igual al configurado.</p>', {
                type: 'danger',
                delay: 2500,
                allow_dismiss: true
            });
            // setTimeout(mostrar_error("El número de cuotas configurado es menor al colocado en número de cuota, por favor, reconfigure el valor de Cuotas máximas o coloque un valor menor o igual al configurado."),1500);

            return;
        }
        $("#procesando").removeClass("hide");
        var esnuevo = "NO";
        if ($("#cliente_nuevo").is(":checked")) {
            esnuevo = "SI";
        }

        $.ajax({
            type: 'POST',
            data: {
                'precio_contado': $("#precio_contado").val(),
                'precio_credito': $("#precio_credito").val(),
                'inicial': $("#inicial").val(),
                'tasa_interes': $("#intereses").val(),
                'numero_cuotas': $("#num_cuota").val(),
                'cliente_nuevo': esnuevo,
                'res_pago_inicial': $("#mto_ini_res").val(),
                'dias_pago': $("#dias_pago").val()
            },
            //dataType: "json",
            url: '<?= base_url()?>venta/generarcuota',
            success: function (data) {
                $("#tbCroCuotas").html(data);

                $("#procesando").addClass("hide");
                $("#precio_credito").val(parseFloat($("#monto_sumado_precio_credito").val()));

            },
            error: function (data) {
                alert(data.responseText);
            }
        });
    }

</script>
