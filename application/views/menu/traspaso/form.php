<?php $ruta = base_url(); ?>
<style>
    .datepicker{
        z-index: 99999 !important;
    }
</style>
<div class="modal-dialog modal-lg" style="width: 60%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" onclick="cancelarcerrar()">&times;</button>
            <h4 class="modal-title">Traspaso de productos</h4>
        </div>
        <div class="modal-body">
            <form name="formagregar" action="<?= base_url() ?>traspaso/traspasar_productos" method="post"
                  id="formagregar">
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-4">
                            <div class="row">
                                <label class="col-md-3 panel-admin-text">Desde:</label>
                                <div class="col-md-9"><select class="form-control" id="localform1"
                                                              onchange="cambiarlocal()">

                                        <?php foreach ($locales as $local) { ?>
                                            <option
                                                value="<?= $local['int_local_id'] ?>"><?= $local['local_nombre'] ?></option>
                                        <?php } ?>

                                    </select>
                                    <input type="hidden" name="" id="valor_localform1" value="" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="row">
                                <label class="col-md-3 panel-admin-text">Hacia: </label>
                                <div class="col-md-9"><select class="form-control" id="localform2"
                                                              placeholder="Seleccione">
                                        <?php $n = 0; ?>
                                        <?php foreach ($locales as $local): ?>
                                            <?php if ($n++ != 0): ?>
                                                <option
                                                    value="<?= $local['int_local_id'] ?>"><?= $local['local_nombre'] ?></option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>

                                    </select></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="alert alert-warning alert-dismissable"
                                 style="padding: 2px; padding-right: 30px; padding-top: 7px; margin-bottom: 0;">
                                Los productos que no figuran aquí no cuentan con stock.
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-3" style="text-align: center">
                        <label class="panel-admin-text">Motivo: </label>
                    </div>
                    <div class="col-md-8">
                        <input type="text" name="motivo" id="motivo" value="" class="form-control" autocomplete="off">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-md-3" style="text-align: center">
                        <label class="control-label panel-admin-text">Buscar Producto:</label>
                    </div>
                    <div class="col-md-8">
                        <select id="select_prodc" style="width: 100%" class="form-control cho">
                            <option value="" selected>Seleccione el Producto</option>
                            <?php if (count($productos) > 0) {
                                $i = 0;
                                foreach ($productos as $producto) {
                                    ?>
                                    <option class="opciones" value="<?= $producto['producto_id'] ?>">
                                        <?php $barra = $barra_activa->activo == 1 && $producto['barra'] != "" ? "CB: ".$producto['barra'] : ""?>
                                        <?= getCodigoValue(sumCod($producto['producto_id']), $producto['producto_codigo_interno']) . ' - ' . $producto['producto_nombre'] . ' ' . $barra ?></option>
                                <?php }
                            } ?>
                        </select>
                    </div>
                </div>
                <div class="row" id="loading" style="display: none;">
                    <div class="col-md-12 text-center">
                        <div class="loading-icon"></div>
                    </div>
                </div>
                <div class="row" style="display: none" id="abrir_info" align="center">
                    <div class="form-group">
                        <div id="mostrar_nombres"></div>
                        <div id="mostrar_input"></div>
                    </div>
                    <br>
                </div>
                <div class="table-responsive" style="height: 200px; overflow-y: scroll;">
                    <table class="table dataTable" id="tablaresult">
                        <thead id="head_productos"></thead>
                        <tbody id="body_productos"></tbody>
                    </table>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" id="btn_confirmar" class="btn btn-primary" onclick="preguntar()">Confirmar</button>
            <button type="button" class="btn btn-default" onclick="cancelarcerrar()">Cancelar</button>
        </div>
    </div>
</div>
<script type="text/javascript">
    var ruta = '<?= base_url()?>';
</script>
<script src="<?php echo $ruta ?>recursos/js/traspaso_form.js"></script>