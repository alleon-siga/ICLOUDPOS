<input type="hidden" id="tasa_interes" value="0">
<input type="hidden" id="saldo_porciento" value="0">
<input type="hidden" id="max_cuotas" value="50">
<input type="hidden" id="numero_cuotas" value="1">
<input type="hidden" id="periodo_pago" value="4">
<input type="hidden" id="proyeccion_rango" value="1">
<input type="hidden" name="c_saldo_inicial" id="c_saldo_inicial" value="0">
<input type="hidden" id="c_saldo_inicial_por" value="0">
<input type="hidden" name="c_periodo_gracia" id="c_periodo_gracia" value="0">
<input type="hidden" name="tipo_pago" value="2">
<input type="hidden" name="gravable" value="0">
<input type="hidden" name="cboDocumento" value="10">
<input type="hidden" name="doc_serie" value="">
<input type="hidden" name="doc_numero" value="">
<?php $md = get_moneda_defecto() ?>
<div class="modal-dialog" style="width: 80%;">
    <div class="modal-content">
        <div class="modal-header">
            <h4>Compra al Cr&eacute;dito</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="block block-section">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="control-label panel-admin-text">Proveedor:</label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" class='form-control' name="c_proveedor" id="c_proveedor" value="" readonly="">
                            </div>
                        </div>
                        <hr class="hr-margin-10">
                        <div class="row">
                            <div class="col-md-12">
                                <h4>Cronograma de Pagos</h4>
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="control-label panel-admin-text">Fecha de Giro:</label>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="input-group">
                                            <input type="text" style="font-weight: bold;" class='form-control date-picker' name="c_fecha_giro" id="c_fecha_giro" value="<?= date('d/m/Y') ?>" readonly>
                                            <input type="hidden" id="last_fecha_giro" value="<?= date('d/m/Y') ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="control-label panel-admin-text">Moneda: <span class="tipo_moneda"></span></label>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-12" style="padding-right: 0;">
                                        <table class="table table-bordered table-cuotas">
                                            <thead>
                                                <tr>
                                                    <th># Letra</th>
                                                    <th>Fecha Venc.</th>
                                                    <th>Saldo</th>
                                                    <th>Capital</th>
                                                    <th>Inter&eacute;s</th>
                                                    <th>Comisi&oacute;n</th>
                                                    <th>Total cuota</th>
                                                </tr>
                                            </thead>
                                        <?php
                                            if(empty($body_cuotas)){
                                                $body_cuotas = "body_cuotas";
                                            }else{
                                                $body_cuotas = "";
                                            }
                                        ?>
                                            <tbody id="<?= $body_cuotas ?>">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="block block-section venta-right venta_input">
                        <div class="row">
                            <div class="col-md-5 label-title">
                                <label class="control-label">Capital:</label>
                            </div>
                            <div class="col-md-7">
                                <div class="input-group">
                                    <div class="input-group-addon tipo_moneda"></div>
                                    <input onkeydown="return soloDecimal(this, event);" type="text" style="text-align: right; font-weight: bold;" class='form-control' name="c_precio_contado" id="c_precio_contado" autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-5 label-title">
                                <label class="control-label">Inter&eacute;s:</label>
                            </div>
                            <div class="col-md-7">
                                <div class="input-group">
                                    <div class="input-group-addon tipo_moneda"></div>
                                    <input onkeydown="return soloDecimal(this, event);" type="text" style="text-align: right; font-weight: bold;" class='form-control' name="c_tasa_interes" id="c_tasa_interes" autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-5 label-title">
                                <label class="control-label">Comisi&oacute;n:</label>
                            </div>
                            <div class="col-md-7">
                                <div class="input-group">
                                    <div class="input-group-addon tipo_moneda"></div>
                                    <input onkeydown="return soloDecimal(this, event);" type="text" style="text-align: right; font-weight: bold;" class='form-control' name="c_comision" id="c_comision" autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-5 label-title">
                                <label class="control-label">Total cuota:</label>
                            </div>
                            <div class="col-md-7">
                                <div class="input-group">
                                    <div class="input-group-addon tipo_moneda"></div>
                                    <input type="text" style="text-align: right; font-weight: bold;" class='form-control' name="c_precio_credito" id="c_precio_credito" autocomplete="off" readonly="">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-5 label-title">
                                <label class="control-label">Nro Cuotas:</label>
                            </div>
                            <div class="col-md-7">
                                <div class="input-group">
                                    <input type="number" max="50" min="1" class='form-control' name="c_numero_cuotas" id="c_numero_cuotas" value="1">
                                    <div class="input-group-addon">MAX: 50</div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-5 label-title">
                                <label class="control-label">Periodo de Pago:</label>
                            </div>

                            <div class="col-md-7">
                                <select id="c_pago_periodo" name="c_pago_periodo" class="form-control">
                                    <option value="1">Diario</option>
                                    <option value="2">Interdiario</option>
                                    <option value="3">Semanal</option>
                                    <option value="4">Mensual</option>
                                    <option value="5">Personalizado</option>
                                    <option value="6">Rango Variados</option>
                                </select>
                            </div>
                        </div>
                        <div id="c_dia_pago_block" class="row">
                            <div class="col-md-5 label-title">
                                <label id="c_dia_pago_letra" class="control-label">D&iacute;as de Pago:</label>
                            </div>
                            <div class="col-md-7">
                                <input type="text" autocomplete="off" class='form-control' name="c_dia_pago" id="c_dia_pago" value="" onkeydown="return soloDecimal(this, event);">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <div class="row">
                <div class="col-md-7">
                    <div class="row" style="text-align: left;">
                        <div class="col-md-6">
                            <h4>Total Importe: <span class="tipo_moneda"></span> <span id="c_total_deuda">0</span></h4>
                        </div>
                        <div class="col-md-6">
                            <h4>Total Cronograma: <span class="tipo_moneda"></span> <span id="c_total_cronograma">0</span></h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <button class="btn btn-primary save_compra_credito" data-imprimir="0" type="button" id="btn_compra_credito"><i class="fa fa-save"></i> (F6) Guardar</button>
                    <button type="button" class="btn btn-danger" onclick="$('#dialog_gasto_prestamo').modal('hide');"><i class="fa fa-close"></i> Cancelar</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?= base_url() ?>recursos/js/dialog_gasto_prestamo.js"></script>