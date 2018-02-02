<?php $md = get_moneda_defecto() ?>
<div class="modal-dialog" style="width: 40%">
    <div class="modal-content">
        <div class="modal-header">
            <h4>Terminar Venta</h4>
        </div>
        <div class="modal-body panel-venta-left">
            <div class="row" id="forma_pago_div">
                <div class="form-group">
                    <div class="col-md-3">
                        <label for="forma_pago" class="control-label panel-admin-text">Forma de Pago:</label>
                    </div>
                    <div class="col-md-9">
                        <select class="form-control" id="forma_pago" name="forma_pago">
                            <option value="1">Efectivo</option>
                            <option value="2">Tarjeta</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row" id="moneda_tasa_div" style="display:none;">
                <div class="form-group">
                    <div class="col-md-3">
                        <label for="moneda_tasa_confirm" class="control-label panel-admin-text">Tasa de Cambio:</label>
                    </div>
                    <div class="col-md-9">
                        <div class="input-prepend input-append input-group">
                            <label class="input-group-addon"><?= $md->simbolo ?></label>
                            <input
                                type="text"
                                class='input-square input-small form-control'
                                id="moneda_tasa_confirm"
                                readonly>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" id="totApagar2div">
                <div class="form-group">
                    <div class="col-md-3">
                        <label for="totApagar2" class="control-label panel-admin-text">Total a Pagarzzzzzz:</label>
                    </div>
                    <div class="col-md-9">
                        <div class="input-prepend input-append input-group">
                            <label id="lblSim4" class="input-group-addon"><?= $md->simbolo ?></label><input
                                type="number"
                                class='input-square input-small form-control'
                                min="0.0"
                                step="0.1"
                                value="0.0"
                                id="totApagar2"
                                readonly
                                onkeydown="return soloDecimal(this, event);">
                        </div>
                    </div>
                </div>
            </div>


            <input type="hidden"
                   min="1"
                   max="10"
                   step="1"
                   class='input-square input-mini form-control'
                   name="nrocuota"
                   id="nrocuota"
                   readonly>

            <input
                type="hidden" class='input-square input-small form-control'
                name="montxcuota"
                id="montxcuota" readonly>


            <div class="row" id="importediv">
                <div class="form-group">
                    <div class="col-md-3">
                        <label for="importe" class="control-label panel-admin-text">Importe:</label>
                    </div>
                    <div class="col-md-9">
                        <div class="input-prepend input-append input-group">
                            <label id="lblSim5" class="input-group-addon"><?= $md->simbolo ?></label><input
                                type="number"
                                tabindex="0"
                                class='input-square input-small form-control'
                                min="0.0"
                                step="0.1"
                                value="0.0"
                                name="importe"
                                id="importe"
                                onkeydown="return soloDecimal(this, event);">

                        </div>
                    </div>
                </div>
            </div>
            <div class="row" id="vueltodiv">
                <div class="form-group" id="monto_vuelto">
                    <div class="col-md-3">
                        <label for="vuelto" class="control-label panel-admin-text">Vuelto:</label>
                    </div>
                    <div class="col-md-9">
                        <div class="input-prepend input-append input-group">
                            <label id="lblSim6" class="input-group-addon"><?= $md->simbolo ?></label><input
                                type="text"
                                class='input-square input-small form-control'
                                name="vuelto"
                                id="vuelto"
                                readonly>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row" id="num_oper_div" style="display:none;">
                <div class="form-group">
                    <div class="col-md-3">
                        <label for="num_oper" class="control-label panel-admin-text">Operaci&oacute;n #:</label>
                    </div>
                    <div class="col-md-9">
                        <input
                            type="text"
                            tabindex="0"
                            class='input-square input-small form-control'
                            name="num_oper"
                            id="num_oper">

                    </div>
                </div>
            </div>

            <div class="row" id="tipo_tarjeta_div" style="display:none;">
                <div class="form-group">
                    <div class="col-md-3">
                        <label for="tipo_targeta" class="control-label panel-admin-text">Tipo de Tarjeta:</label>
                    </div>
                    <div class="col-md-9">
                        <select class="form-control" id="tipo_tarjeta" name="tipo_tarjeta">
                            <?php foreach ($tarjetas as $tarjeta) : ?>
                                <option value="<?php echo $tarjeta->id ?>"><?php echo $tarjeta->nombre ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>


        </div>
        <div class="modal-footer">

            <div class="row">
                <div class="col-md-12">
                    <button class="btn btn-default" style="margin-bottom:5px" type="button"
                            id="realizarventa"
                            onclick="<?php echo !isset($cobro) ? 'hacerventa(0);' : '' ?>"><i
                            class="fa fa-save"></i>Guardar


                    </button>

                    <a href="#" class="btn btn-default" style="margin-bottom:5px"
                       id="btnRealizarVentaAndView"
                       onclick="<?php echo !isset($cobro) ? 'hacerventa(1);' : '' ?>" type="button"><i
                            class="fa fa-print"></i> (F6)Guardar e imprimir
                    </a>
                    <button class="btn btn-default" style="margin-bottom:5px"
                            type="button"
                            onclick="$('#generarventa').modal('hide');"><i
                            class="fa fa-close"></i> Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {

        $("#forma_pago").change(function () {

            $("#vuelto").val(0);
            $("#importe").val($('#totApagar2').val());
            $("#num_oper").val('');
            $("#tipo_tarjeta").val(1);

            if ($("#forma_pago").val() == 1) {
                $("#num_oper_div").hide();
                $("#tipo_tarjeta_div").hide();
                $("#vueltodiv").show();
                $("#importediv").show();
            }
            else if ($("#forma_pago").val() == 2) {

                $("#num_oper_div").show();
                $("#tipo_tarjeta_div").show();
                $("#vueltodiv").hide();
                $("#importediv").hide();
            }
        });

        /****cambios de daniel *********/
        $('#importe').on('keyup', function (event) {
            if (event.keyCode == 13) {
                $("#btnRealizarVentaAndView").focus();
            } else {
                calcular_importe();
            }
        });
    });

    function calcular_importe() {
        var totalApagar = $('#totApagar2').val();
        var importe = $('#importe').val();

        document.getElementById('vuelto').value = parseFloat(Math.ceil((parseFloat(importe - totalApagar).toFixed(2)) * 10) / 10);
    }
</script>