<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close"
                    aria-hidden="true" onclick="closeseleccionunidades()">&times;</button>
            <h4 class="modal-title">Existencia del producto</h4>
            <div class="row">
                <div class="col-sm-6">
                    <h5>Producto: <span style="font-weight: bold;" id="nombreproduto"></span></h5>
                </div>
                <div class="col-sm-4">
                    <h5>Ubicaci&oacute;n Actual: <span style="font-weight: bold;" id="ubicacion_actual"
                                                       data-local="0"></span></h5>
                </div>
            </div>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="form-group">
                    <div class="col-md-3"><label class="panel-admin-text">
                            Seleccion de Precio:
                            <input type="hidden" id="default_precio" class="form-control" value="1">
                        </label></div>
                    <div class="col-md-3" id="abrir_precio">
                        <div id="borrar_precio">
                            <select class="form-control" name="precio" id="precios" tabindex="0"
                                    onchange="cambiarnombreprecio()" style="width:250px"></select>
                        </div>
                    </div>

                    <div class="col-md-3" style="text-align: right;"><label class="panel-admin-text">Cantidad en
                            Stock: </label></div>
                    <div class="col-md-3 panel-admin-text" style="text-align: left; font-weight: bold;">
                        <span id="stock" style="font-size: 16px; color: #39B147 !important;"></span>
                    </div>

                </div>
            </div>
            <br>
            <div class="row">
                <div class="form-group">


                    <?php if (count($locales) > 1): ?>
                        <div class="col-md-6"></div>
                        <div class="col-md-3" style="text-align: right;"><label class="panel-admin-text">Ubicaciones: </label>
                        </div>
                        <div class="col-md-3">
                            <select name="select_local" id="select_local" class='form-control'>
                                <?php foreach ($locales as $local):
                                    if ($local_selected == $local['int_local_id']): ?>
                                        <option selected
                                                value="<?= $local['int_local_id'] ?>"><?= $local['local_nombre'] ?></option>
                                    <?php else: ?>
                                        <option
                                            value="<?= $local['int_local_id'] ?>"><?= $local['local_nombre'] ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php else: ?>
                        <div class="col-md-4" style="display: none;">
                            <select name="select_local" id="select_local" class='form-control'>
                                <option selected
                                        value="<?= $locales[0]['int_local_id'] ?>"><?= $locales[0]['local_nombre'] ?></option>
                            </select>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <br>

            <div class="row">
                <table class="table datatable table-bordered">
                    <thead>
                    <th style="text-align: left;">Unidad de Medida</th>
                    <th style="text-align: left;">Unidades</th>
                    <th style="text-align: left;" id="tituloprecio"></th>
                    </thead>
                    <tbody id="preciostbody">

                    </tbody>
                </table>
            </div>
            <br>
            <div class="row">
                <div class="form-group">

                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-6"><label class="panel-admin-text">Agregue la Cantidad: </label>
                            </div>
                            <div class="col-md-6">
                                <input type="number" readonly id="cantidad" class="form-control enter"
                                       onkeydown="return soloDecimal3(this, event);">
                            </div>

                        </div>
                        <br>
                        <div class="row">
                            <?php if (getProductoSerie() == "SI"): ?>
                                <div class="col-md-6" style="text-align: left;"><label class="panel-admin-text">Agregue
                                        por Series: </label></div>
                                <div class="col-md-6">
                                    <div class="input-prepend input-append input-group">

                                        <input type="text" id="input_serie" class="form-control enter">
                                        <a id="show_series" onclick="alert('Esta accion no ha sido implementada')"
                                           class="input-group-addon" href="#"><i class="fa fa-barcode"></i></a>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>


                    <?php if ($show_precio_new == "SI"): ?>
                        <div class="col-md-3" id="show_price_check" style="text-align: right;"><input
                                type="checkbox"
                                id="price_check" value="1">
                            <label class="panel-admin-text">Precio Personalizado: </label>
                        </div>
                        <div class="col-md-3" id="show_price_new" style="display: none;">
                            <input type="number" id="price_new" class="form-control">
                        </div>
                    <?php endif; ?>
                </div>
            </div>


        </div>
        <input type="hidden" id="poner_id_producto">
        <input type="hidden" id="poner_nombre_producto">
        <div class="modal-footer">
            <?php if (count($locales) > 1): ?>
                <span style="float: left;">
                <a href="#" class="btn btn-primary add_product" onclick="add_producto(this)" data-close="0" id="agregarproducto"
                >Agregar</a>
                    </span>
            <?php endif; ?>
            <a href="#" class="btn btn-default add_product" onclick="add_producto(this)" data-close="1" id="agregarproducto_salir"
            >Agregar & Salir</a>

            <a href="#" class="btn btn-warning " onclick="closeseleccionunidades()"
            >Salir</a>
        </div>
    </div>
</div>

<script>


</script>