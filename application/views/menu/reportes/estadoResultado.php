<?php $ruta = base_url(); ?>
<ul class="breadcrumb breadcrumb-top">
    <li>Reporte</li>
    <li>Caja</li>
    <li><a href="">Estado de Resultados</a></li>
</ul>
<link rel="stylesheet" href="<?= $ruta ?>recursos/css/plugins.css">
<link rel="stylesheet" href="<?= $ruta ?>recursos/js/datepicker-range/daterangepicker.css">
<link rel="stylesheet" href="<?= $ruta ?>recursos/css/multiple-select.css" />
<div class="row-fluid">
    <div class="span12">
        <div class="block">
            <!-- Progress Bars Wizard Title -->
            <div class="row">
                <div class="col-md-3">
                    <label class="control-label panel-admin-text">Ubicaci&oacute;n:</label>
                    <?php if (isset($locales)): ?>
                        <select id="local_id" class="ctrl form-control">
                            <option value="0">TODOS</option>
                            <?php foreach ($locales as $local): ?>
                                <option <?php if ($this->session->userdata('id_local') == $local->local_id) echo "selected"; ?>
                                        value="<?= $local->local_id; ?>"> <?= $local->local_nombre ?> </option>
                            <?php endforeach; ?>
                        </select>
                    <?php endif; ?>
                </div>
                <div class="col-md-2">
                    <label class="control-label panel-admin-text">A&ntilde;o:</label>
                    <input type="number" id="year" name="year" value="<?= date('Y') ?>" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="control-label panel-admin-text">Mes</label>
                    <select id="mes" class="form-control filter-input" name="mes">
                        <option value="1" <?= date('m')=='01' ? 'selected' : ''?>>Enero</option>
                        <option value="2" <?= date('m')=='02' ? 'selected' : ''?>>Febrero</option>
                        <option value="3" <?= date('m')=='03' ? 'selected' : ''?>>Marzo</option>
                        <option value="4" <?= date('m')=='04' ? 'selected' : ''?>>Abril</option>
                        <option value="5" <?= date('m')=='05' ? 'selected' : ''?>>Mayo</option>
                        <option value="6" <?= date('m')=='06' ? 'selected' : ''?>>Junio</option>
                        <option value="7" <?= date('m')=='07' ? 'selected' : ''?>>Julio</option>
                        <option value="8" <?= date('m')=='08' ? 'selected' : ''?>>Agosto</option>
                        <option value="9" <?= date('m')=='09' ? 'selected' : ''?>>Septiembre</option>
                        <option value="10" <?= date('m')=='10' ? 'selected' : ''?>>Octubre</option>
                        <option value="11" <?= date('m')=='11' ? 'selected' : ''?>>Noviembre</option>
                        <option value="12" <?= date('m')=='12' ? 'selected' : ''?>>Diciembre</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="control-label panel-admin-text">Moneda:</label>
                    <select name="moneda_id" id="moneda_id" class='ctrl form-control'>
                        <?php foreach ($monedas as $moneda): ?>
                            <option value="<?= $moneda->id_moneda ?>"
                                    data-simbolo="<?= $moneda->simbolo ?>"
                                <?= $moneda->id_moneda == MONEDA_DEFECTO ? 'selected' : '' ?>><?= $moneda->nombre ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-1">
                </div>
                <div class="col-md-2">
                    <label class="control-label panel-admin-text">&nbsp;</label><br>
                    <button id="btn_buscar" class="btn btn-default">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
            </div>
            <br>
            <div class="row" id="loading" style="display: none;">
                <div class="col-md-12 text-center">
                    <div class="loading-icon"></div>
                </div>
            </div>
            <div class="row-fluid">
                <div class="span12">
                    <div id="historial_list">

                    </div>
                </div>
            </div>
            <!-- /.modal-dialog -->
            <script type="text/javascript">
                $(document).ready(function () {
                    $('.ctrl').chosen();
                    $("#btn_buscar, .btn_buscar").on("click", function () {
                        getReporte();
                    });
                });

                function getReporte() {
                    $("#historial_list").html($("#loading").html());

                    var data = {
                        'local_id': $("#local_id").val(),
                        'year': $("#year").val(),
                        'mes': $("#mes").val(),
                        'moneda_id': $("#moneda_id").val()
                    };

                    $.ajax({
                        url: '<?= base_url()?>reporte/estadoResultado/filter',
                        data: data,
                        type: 'POST',
                        success: function (data) {
                            $("#historial_list").html(data);
                        },
                        error: function () {
                            $.bootstrapGrowl('<h4>Error.</h4> <p>Ha ocurrido un error en la operaci&oacute;n</p>', {
                                type: 'danger',
                                delay: 5000,
                                allow_dismiss: true
                            });
                            $("#historial_list").html('');
                        }
                    });
                }
            </script>
