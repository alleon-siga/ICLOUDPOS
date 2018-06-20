<?php $ruta = base_url(); ?>
<link rel="stylesheet" href="<?= $ruta ?>recursos/css/plugins.css">
<link rel="stylesheet" href="<?= $ruta ?>recursos/js/datepicker-range/daterangepicker.css">
<link rel="stylesheet" href="<?= $ruta ?>recursos/css/tcharm.css">
<ul class="breadcrumb breadcrumb-top">
    <li>Gastos</li>
    <li><a href="">Agregar y editar Gastos</a></li>
</ul>
<div class="row">
    <div class="col-xs-12">
        <div class="alert alert-success alert-dismissable" id="success"
             style="display:<?php echo isset($success) ? 'block' : 'none' ?>">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">X</button>
            <h4><i class="icon fa fa-check"></i> Operaci&oacute;n realizada</h4>
            <span id="successspan"><?php echo isset($success) ? $success : '' ?></div>
        </span>
    </div>
</div>
<?php
echo validation_errors('<div class="alert alert-danger alert-dismissable"">', "</div>");
?>
<div class="block">
    <div class="row">
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
                    <label class="control-label">Moneda</label>
                    <select name="moneda_id" id="moneda_id" class='cho form-control'>
                        <?php foreach ($monedas as $moneda): ?>
                            <option value="<?= $moneda->id_moneda ?>"
                                    data-simbolo="<?= $moneda->simbolo ?>"
                                <?= $moneda->id_moneda == MONEDA_DEFECTO ? 'selected' : '' ?>><?= $moneda->nombre ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="row">
                    <label class="control-label">Estado</label>
                    <select name="estado_id" id="estado_id" class='cho form-control'>
                       <option value="">Todos</option>
                       <option value="1">Pendientes</option>
                       <option value="0">Confirmados</option>
                    </select>
                </div>
                <div class="row">
                    <label class="control-label">Persona Afectada:</label>
                    <select name="persona_gasto_filter" id="persona_gasto_filter" required="true" class="select_chosen form-control">
                        <option value="1">Proveedor</option>
                        <option value="2">Trabajador</option>
                    </select>
                </div>
                <div class="row" id="proveedor_block_filter">
                    <label class="control-label">Proveedor:</label>
                    <select name="proveedor_filter" id="proveedor_filter" required="true" class="select_chosen form-control">
                        <option value="-">TODOS</option>
                        <?php foreach ($proveedores as $proveedor): ?>
                            <option
                                    value="<?php echo $proveedor->id_proveedor ?>"
                                <?php if (isset($gastos['proveedor_id']) and $gastos['proveedor_id'] == $proveedor->id_proveedor) echo 'selected' ?>>
                                <?= $proveedor->proveedor_nombre ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div class="row" id="usuario_block_filter" style="display: none;">
                    <label class="control-label">Usuario:</label>
                    <select name="usuario_filter" id="usuario_filter" required="true" class="select_chosen form-control">
                        <option value="-">TODOS</option>
                        <?php foreach ($usuarios as $usuario): ?>
                            <option
                                    value="<?php echo $usuario->nUsuCodigo ?>"
                                <?php if (isset($gastos['usuario_id']) and $gastos['usuario_id'] == $usuario->nUsuCodigo) echo 'selected' ?>>
                                <?= $usuario->nombre ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div class="row">
                    <label class="control-label">Tipo de Gasto:</label>
                    <select id="tipo_gasto_id" class="form-control select_chosen" name="tipo_gasto_id">
                        <option value="-">TODOS</option>
                        <?php foreach ($tipos_gastos as $gasto): ?>
                            <option
                                    value="<?php echo $gasto['id_tipos_gasto'] ?>" <?php if (isset($gastos['tipo_gasto']) and $gastos['tipo_gasto'] == $gasto['id_tipos_gasto']) echo 'selected' ?>><?= $gasto['nombre_tipos_gasto'] ?></option>
                        <?php endforeach ?>
                    </select>
                </div>                
            </div>
        </div>
        <div class="col-md-1">
            <div style="padding-top: 30px;"></div>
            <button id="btn_buscar" class="btn btn-primary" onclick="agregar();">
                <i class="fa fa-plus "> Nuevo</i>
            </button>
        </div>
        <div class="col-md-3">
            <label class="control-label panel-admin-text">Ubicaci&oacute;n:</label>
            <?php if (isset($locales)): ?>
                <select id="local_id" class="form-control select_chosen">
                    <option value="0">TODOS</option>
                    <?php foreach ($locales as $local): ?>
                        <option <?php if ($this->session->userdata('id_local') == $local->local_id) echo "selected"; ?>
                                value="<?= $local->local_id; ?>"> <?= $local->local_nombre ?> </option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
        </div>
        <div class="col-md-3">
            <label class="control-label panel-admin-text">Fecha</label>
            <input type="text" id="date_range" class="form-control" readonly style="cursor: pointer;"
                   name="daterange" value="<?= date('01/m/Y') ?> - <?= date('d/m/Y') ?>"/>
        </div>
        <div class="col-md-2">
            <div style="padding-top: 30px;"></div>
            <button id="btn_buscar" class="btn btn-default">
                <i class="fa fa-search"></i>
            </button>
        </div>
        <div class="col-md-2"></div>
        <div class="col-md-1">
            <div style="padding-top: 30px;"></div>
            <button type="button" class="btn btn-primary tcharm-trigger form-control">
                <i class="fa fa-plus"></i>
            </button>
        </div>
    </div>
    <div id="load_div" style="display: none;">
        <div class="row" id="loading" style="position: relative; top: 50px; z-index: 500000;">
            <div class="col-md-12 text-center">
                <div class="loading-icon"></div>
            </div>
        </div>
    </div>
    <div id="tabla_lista"></div>
</div>
<!-- Modales for Messages -->
<div class="modal hide" id="mOK">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" onclick="javascript:window.location.reload();">
        </button>
        <h3>Notificaci&oacute;n</h3>
    </div>
    <div class="modal-body">
        <p>Registro Exitosa</p>
    </div>
    <div class="modal-footer">
        <a href="#" class="btn btn-primary" data-dismiss="modal"
           onclick="javascript:window.location.reload();">Close</a>
    </div>
</div>
<div class="modal fade" id="agregar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-keyboard="false">
</div>
<div class="modal fade" id="borrar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <form name="formeliminar" id="formeliminar" method="post" action="<?= $ruta ?>gastos/eliminar">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Eliminar Gasto</h4>
                </div>
                <div class="modal-body">
                    <p>Est&aacute; seguro que desea eliminar el Gasto seleccionado?</p>
                    <input type="hidden" name="id" id="id_borrar">
                    <div class="row">
                        <div class="col-md-2">
                            <label class="control-label panel-admin-text">Motivo: </label>
                        </div>

                        <div class="col-md-8">
                            <input type="text" name="motivo" id="motivo" required="true" class="form-control"
                                   value="">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="confirmar" class="btn btn-primary" onclick="eliminar()">Confirmar</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
</div>
<!-- /.modal-dialog -->
</div>
<div class="modal fade" id="agregarproveedor" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"></div>
<div class="modal fade" id="tipoGastoModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"></div>
<script src="<?php echo $ruta; ?>recursos/js/datepicker-range/moment.min.js"></script>
<script src="<?php echo $ruta; ?>recursos/js/datepicker-range/daterangepicker.js"></script>
<script src="<?php echo $ruta; ?>recursos/js/Validacion.js"></script>
<script src="<?php echo $ruta ?>recursos/js/pages/tablesDatatables.js"></script>
<script src="<?= base_url('recursos/js/tcharm.js') ?>"></script>
<script type="text/javascript">
    var ruta = '<?= base_url()?>';
</script>
<script src="<?php echo $ruta ?>recursos/js/gastos.js"></script>