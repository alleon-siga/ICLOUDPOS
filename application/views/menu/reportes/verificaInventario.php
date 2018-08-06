<?php $ruta = base_url(); ?>
<ul class="breadcrumb breadcrumb-top">
    <li>Reporte</li>
    <li>Inventario</li>
    <li><a href="">Verificaci&oacute;n de Inventario</a></li>
</ul>
<link rel="stylesheet" href="<?= $ruta ?>recursos/css/plugins.css">
<link rel="stylesheet" href="<?= $ruta ?>recursos/css/multiple-select.css" />
<div class="row-fluid">
    <div class="span12">
        <div class="block">
            <!-- Progress Bars Wizard Title -->
            <div class="row">
                <div class="col-md-2">
                    <?php if (isset($locales)): ?>
                        <label class="control-label panel-admin-text">Ubicación</label>
                        <select id="local_id" multiple="multiple">
                            <?php foreach ($locales as $local): ?>
                                <option <?php if ($this->session->userdata('id_local') == $local['int_local_id']) echo "selected"; ?>
                                        value="<?= $local['int_local_id']; ?>"> <?= $local['local_nombre'] ?> </option>
                            <?php endforeach; ?>
                        </select>
                    <?php endif; ?>
                </div>
                <div class="col-md-3">
                    <?php if (isset($productos)): ?>
                        <label class="control-label panel-admin-text">Producto</label>
                        <select id="producto_id" name="producto_id" multiple="multiple">
                         <?php foreach ($productos as $producto): ?>
                                <option value="<?= $producto->producto_id ?>"
                                        data-impuesto="<?= $producto->porcentaje_impuesto ?>">
                                    <?php $barra = $barra_activa->activo == 1 && $producto->barra != "" ? "CB: " . $producto->barra : "" ?>
                                    <?= getCodigoValue($producto->producto_id, $producto->codigo) . ' - ' . $producto->producto_nombre . " " . $barra ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php endif; ?>
                </div>
                <div class="col-md-2">

                </div>
                <div class="col-md-2">

                </div>
                <div class="col-md-1">
                    <div style="padding-top: 30px;"></div>
                    <button id="btn_buscar" class="btn btn-default">
                        <i class="fa fa-search"></i> Buscar
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
            <script src="<?php echo $ruta; ?>recursos/js/Validacion.js"></script>
            <script src="<?= base_url('recursos/js/multiple-select.js') ?>"></script>
            <!-- /.modal-dialog -->
            <script type="text/javascript">
                // Filtro en select
                $("#producto_id, #local_id").multipleSelect({
                    filter: true,
                    width: '100%'
                });

                $(document).ready(function () {
                    $("#btn_buscar, .btn_buscar").on("click", function () {
                        getReporte();
                    });
                });

                function getReporte() {
                    $("#historial_list").html($("#loading").html());

                    var data = {
                        'local_id': $("#local_id").val(),
                        'producto_id': $("#producto_id").val()
                    };

                    $.ajax({
                        url: '<?= base_url()?>reporte/verificaInventario/filter',
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
