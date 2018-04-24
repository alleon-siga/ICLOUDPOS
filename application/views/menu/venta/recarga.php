<ul class="breadcrumb breadcrumb-top">
    <li>Ventas</li>
    <li><a href="<?= base_url('venta_new/recarga') ?>">Recarga</a></li>
</ul>
<link rel="stylesheet" href="<?= base_url('recursos/css/plugins.css') ?>">
<link rel="stylesheet" href="<?= base_url('recursos/js/datepicker-range/daterangepicker.css') ?>">
<div class="block">
    <div class="row">
        <!-- SECCION IZQUIERDA -->
        <div class="col-md-12 block-section">
            <form id="frmRecarga" method="post">
                <input type="hidden" name="vc_importe2" value="">
                <input type="hidden" name="vc_vuelto2">
                <!-- SELECCION DEL LOCAL DE LA VENTA -->
                <div class="col-md-12 block block-section">
                    <div class="row">
                        <div class="col-md-2">
                            <label class="control-label panel-admin-text">Local:</label>
                        </div>
                        <div class="col-md-4">
                            <select name="local_venta_id" id="local_venta_id" class='form-control'>
                                <?php foreach ($locales as $local): ?>
                                    <option <?= $local->local_id == $local->local_defecto ? 'selected="selected"' : '' ?>
                                            value="<?= $local->local_id ?>"><?= $local->local_nombre ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="control-label panel-admin-text">Cliente:</label>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <select name="cliente_id" id="cliente_id" class='form-control'>
                                    <?php foreach ($clientes as $cliente): ?>
                                        <option
                                                value="<?php echo $cliente['id_cliente']; ?>"
                                                data-ruc="<?= $cliente['ruc'] ?>"
                                            <?= $cliente['id_cliente'] == 1 ? 'selected' : '' ?>
                                        ><?php echo $cliente['razon_social']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <a id="cliente_new" href="#" class="input-group-addon btn-default">
                                    <i class="fa fa-plus-circle"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-2">
                            <label class="control-label">Fecha:</label>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control date-picker" name="fecha_venta" id="fecha_venta" value="<?= date('d/m/Y') ?>" readonly="" style="cursor: pointer;">
                        </div>
                        <div class="col-md-2">
                            <label class="control-label">Pago:</label>
                        </div>
                        <div class="col-md-4">
                            <select name="tipo_pago" id="tipo_pago" class="form-control">
                            <?php foreach($condPagos as $condPago): ?>
                                <option value="<?= $condPago['id_condiciones'] ?>"><?= $condPago['nombre_condiciones'] ?></option>
                            <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-2">
                            <label class="control-label">Moneda:</label>
                        </div>
                        <div class="col-md-4">
                            <select name="moneda_id" id="moneda_id" class="form-control">
                            <?php foreach($monedas as $moneda): ?>
                                <option value="<?= $moneda->id_moneda ?>"><?= $moneda->nombre ?></option>
                            <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="control-label">Total:</label>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="total_importe" id="total_importe">
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-2">
                            <label class="control-label panel-admin-text">Operador:</label>
                        </div>
                        <div class="col-md-4">
                            <select name="operador_id" id="operador_id" class='form-control'>
                                <?php foreach ($operadore as $operador): ?>
                                    <option <?= $operador->id == $operador->valor ? 'selected="selected"' : '' ?>
                                            value="<?= $operador->id ?>"><?= $operador->valor ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="control-label panel-admin-text">N&uacute;mero de recarga:</label>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="nro_recarga" id="nro_recarga" value="">
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-2">
                            <label class="control-label panel-admin-text">C&oacute;digo de transacci&oacute;n:</label>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="cod_tran" id="cod_tran" value="">
                        </div>
                    </div>
                    <br>
                </div>
                <div class="col-md-12 block block-section">
                    <button class="btn" id="terminar_venta" type="button"><i class="fa fa-save fa-3x text-info fa-fw"></i> <br>F6 Guardar</button>
                    <!--<button type="button" class="btn" id="reiniciar_venta"><i class="fa fa-refresh fa-3x text-info fa-fw"></i><br>Reiniciar</button>
                    <button class="btn" type="button" id="cancelar_venta"><i class="fa fa-remove fa-3x text-warning fa-fw"></i><br>Cancelar</button>-->
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="dialog_new_cliente" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false" aria-hidden="true"></div>
<div class="modal fade" id="dialog_venta_contado" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false" aria-hidden="true"></div>
<script src="<?= base_url('recursos/js/recarga.js') ?>"></script>