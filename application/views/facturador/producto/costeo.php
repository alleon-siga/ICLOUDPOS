<?php $ruta = base_url(); ?>
<ul class="breadcrumb breadcrumb-top">
    <li>Producto</li>
    <li><a href="">Costeo de Productos</a></li>
</ul>
<link rel="stylesheet" href="<?= $ruta ?>recursos/css/plugins.css">
<link rel="stylesheet" href="<?= $ruta ?>recursos/js/datepicker-range/daterangepicker.css">
<link rel="stylesheet" href="<?= $ruta ?>recursos/css/multiple-select.css" />
<link rel="stylesheet" href="<?= $ruta ?>recursos/css/tcharm.css" />
<div class="row-fluid">
    <div class="span12">
        <div class="block">
            <!-- Progress Bars Wizard Title -->
            <div id="charm" class="tcharm">
                    <div class="tcharm-header">
                        <h3><a href="#" class="fa fa-arrow-right tcharm-close"></a> <span>Filtros Avanzados</span></h3>
                    </div>
                    <div class="tcharm-body">
                        <div class="row">
                            <div class="col-md-4" style="text-align: center;">
                                <button type="button" class="btn btn-default btn_buscar">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                            <div class="col-md-4" style="text-align: center;">
                                <button id="btn_filter_reset" type="button" class="btn btn-warning">
                                    <i class="fa fa-refresh"></i>
                                </button>
                            </div>
                            <div class="col-md-4" style="text-align: center;">
                                <button type="button" class="btn btn-danger tcharm-trigger">
                                    <i class="fa fa-remove"></i>
                                </button>
                            </div>
                        </div>
                        <div class="row">
                            <label class="control-label">Marca:</label>
                            <select id="marca_id" name="marca_id" class="form-control ctrl">
                                <option value="0">Todos</option>
                                <?php foreach ($marcas as $marca): ?>
                                    <option value="<?= $marca->id_marca ?>"><?= $marca->nombre_marca ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="row">
                            <label class="control-label">Grupo:</label>
                            <select id="grupo_id" name="grupo_id" class="form-control ctrl">
                                <option value="0">Todos</option>
                                <?php foreach ($grupos as $grupo): ?>
                                    <option value="<?= $grupo->id_grupo ?>"><?= $grupo->nombre_grupo ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="row">
                            <label class="control-label">Familia:</label>
                            <select id="familia_id" name="familia_id" class="form-control ctrl">
                                <option value="0">Todos</option>
                                <?php foreach ($familias as $familia): ?>
                                    <option value="<?= $familia->id_familia ?>"><?= $familia->nombre_familia ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="row">
                            <label class="control-label">Linea:</label>
                            <select id="linea_id" name="linea_id" class="form-control ctrl">
                                <option value="0">Todos</option>
                                <?php foreach ($lineas as $linea): ?>
                                    <option value="<?= $linea->id_linea ?>"><?= $linea->nombre_linea ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="row">
                            <label class="control-label">Producto:</label>
                            <div id="divSelect">
                                <select id="producto_id" name="producto_id" multiple="multiple">
                                <?php foreach ($productos as $producto): ?>
                                    <option value="<?= $producto->producto_id ?>"
                                            data-impuesto="<?= $producto->porcentaje_impuesto ?>">
                                        <?php $barra = $barra_activa->activo == 1 && $producto->barra != "" ? "CB: " . $producto->barra : "" ?>
                                        <?= getCodigoValue($producto->producto_id, $producto->codigo) . ' - ' . $producto->producto_nombre . " " . $barra ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            </div>
                        </div>
                    </div>
                </div>
            <div class="row">
                
                <div class="col-md-2">
                    <button type="button" class="btn btn-default form-control" id="btnSave">
                        <i class="fa fa-save"></i> Guardar cambios
                    </button>
                </div>
                <div class="col-md-2">
                </div>
                <div class="col-md-3">
                </div>
                <div class="col-md-2">
                </div>
                <div class="col-md-2">
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-primary tcharm-trigger form-control">
                        <i class="fa fa-plus"></i>
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
            <script src="<?= base_url('recursos/js/tcharm.js') ?>"></script>
            <script src="<?php echo $ruta; ?>recursos/js/multiple-select.js"></script>
            <script type="text/javascript">
                var ruta = "<?= $ruta ?>";
            </script>
            <script src="<?php echo $ruta; ?>recursos/js/costeo.js"></script>