<?php $ruta = base_url(); ?>
<input type="hidden" name="ruta" id="ruta" value="<?= base_url() ?>">
<ul class="breadcrumb breadcrumb-top">
    <li>Venta</li>
    <li><a href="">Historial de Ventas</a></li>
</ul>
<link rel="stylesheet" href="<?= $ruta ?>recursos/css/plugins.css">
<link rel="stylesheet" href="<?= $ruta ?>recursos/js/datepicker-range/daterangepicker.css">
<div class="row-fluid">
    <div class="span12">
        <div class="block">
            <div class="row">
                <div class="form-group">
                    <div class="col-md-2">
                        <?php if (isset($locales)): ?>
                            <label class="control-label panel-admin-text">Ubicaci&oacute;n</label>
                            <select id="venta_local" class="form-control filter-input">
                                <?php foreach ($locales as $local): ?>
                                    <option <?php if ($this->session->userdata('id_local') == $local['int_local_id']) echo "selected"; ?>
                                            value="<?= $local['int_local_id']; ?>"> <?= $local['local_nombre'] ?> </option>
                                <?php endforeach; ?>
                            </select>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-2">
                        <label class="control-label panel-admin-text">Fecha Venta</label>
                        <input type="text" id="date_range" class="form-control" readonly style="cursor: pointer;"
                               name="daterange" value="<?= date('d/m/Y') ?> - <?= date('d/m/Y') ?>"/>
                    </div>
                    <div class="col-md-2">
                        <label class="control-label panel-admin-text">Tipo de Pago</label>
                        <select name="condicion_pago_id" id="condicion_pago_id" class='cho form-control'>
                            <option value="">Todos</option>
                            <?php foreach ($condiciones_pagos as $condicion): ?>
                                <option value="<?= $condicion->id_condiciones ?>">
                                    <?= $condicion->nombre_condiciones ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="control-label panel-admin-text">Moneda</label>
                        <select name="moneda_id" id="moneda_id" class='cho form-control'>
                            <?php foreach ($monedas as $moneda): ?>
                                <option value="<?= $moneda->id_moneda ?>"
                                        data-simbolo="<?= $moneda->simbolo ?>"
                                    <?= $moneda->id_moneda == MONEDA_DEFECTO ? 'selected' : '' ?>><?= $moneda->nombre ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="control-label panel-admin-text">Tipo Documento</label>
                        <select name="id_documento" id="id_documento" class='cho form-control'>
                            <option value="">Todos</option>
                            <?php foreach ($documentos as $documento): ?>
                                <option value="<?= $documento->id_doc ?>"><?= $documento->des_doc ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="control-label panel-admin-text" style="color: #fff;">.</label><br>
                        <button id="btn_buscar" class="btn btn-default">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
            <br>
            <div class="row-fluid">
                <div class="span12">
                    <div id="historial_list" class="block">
                    </div>
                </div>
            </div>
            <div class="row" id="loading" style="display: none;">
                <div class="col-md-12 text-center">
                    <div class="loading-icon"></div>
                </div>
            </div>
            <script src="<?php echo $ruta; ?>recursos/js/datepicker-range/moment.min.js"></script>
            <script src="<?php echo $ruta; ?>recursos/js/datepicker-range/daterangepicker.js"></script>
            <script src="<?php echo $ruta; ?>recursos/js/Validacion.js"></script>
            <script src="<?php echo $ruta; ?>recursos/js/facturador_historial.js"></script>
