<style>
    /**
     * Fix for elevateZoom with jQuery modal
     */

    /*set a border on the images to prevent shifting*/

    /*Change the colour*/
    #gal1 div a img {
        border: 2px solid white;
        width: 96px;
    }

    .active img {
        border: 2px solid #333 !important;
    }
</style>
<div class="modal-dialog modal-lg">
    <?php
    $ruta = base_url();
    $nombre_producto = json_decode(valueOption("NOMBRE_PRODUCTO", 2));
    $numero_decimales = valueOption("NUMERO_DECIMALES", 2);
    ?>
    <script src="<?= $ruta ?>recursos/js/helpers/excanvas.min.js"></script>
    <script src="<?= $ruta ?>recursos/js/pages/readyInboxCompose.js"></script>

    <?= form_open_multipart(base_url() . 'producto/registrar', array('id' => 'formguardar', 'method' => 'post')); ?>

    <div class="modal-content">

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Datos del
                producto: <?php if (isset($producto['producto_nombre']) and $duplicar != 1) echo getCodigoValue($producto['producto_id'], $producto['producto_codigo_interno']) . ' - ' . $producto['producto_nombre'] ?></h4>
        </div>


        <div class="modal-body">


            <div id="mensaje"></div>


            <ul class="nav nav-tabs" role="tablist">
                <li class='active' role="presentation">
                    <a href="#lista" data-toggle="tab">Datos Generales</a>
                </li>

                <li role="presentation">
                    <a href="#precios" data-toggle="tab">Unidades y Precios</a>
                </li>
                <li role="imagenes">
                    <a href="#imagenes" data-toggle="tab">Imagenes</a>
                </li>
                <!--<li role="presentation">
                    <a href="#imagenes" data-toggle="tab">Im&aacute;genes</a>
                </li>-->
            </ul>

            <div class="tab-content row" style="height: auto">

                <input type="hidden" name="id" id="iddos" maxlength="15"
                       value="<?php if (isset($producto['producto_id']) and empty($duplicar)) echo $producto['producto_id'] ?>"/>

                <div class="tab-pane active" role="tabpanel" id="lista" role="tabpanel">
                    <?php foreach ($columnas as $columna): ?>

                        <?php if ($columna->nombre_columna == 'producto_id' && isset($producto['producto_id']) and ! isset($duplicar)) { ?>
                            <div class="form-group">
                                <div class="col-md-3"><label class="control-label">C&oacute;digo:</label></div>
                                <div class="col-md-8">

                                    <input type="text" name="codigo" id="codigo"
                                           class='form-control' autofocus="autofocus" maxlength="25"
                                           value="<?php if (isset($producto['producto_id']) and ! isset($duplicar)) echo $producto['producto_id'] ?>"
                                           readonly>

                                </div>
                            </div>
                        <?php } ?>
                        <?php if ($columna->nombre_columna == 'producto_codigo_interno' && getCodigo() == "INTERNO") { ?>
                            <div class="form-group">
                                <div class="col-md-3"><label class="control-label">C&oacute;digo Interno:</label></div>
                                <div class="col-md-8">
                                    <input type="text" name="producto_codigo_interno" id="codigo_interno"
                                           class='form-control cbo_desc' autofocus="autofocus" maxlength="25"
                                           value="<?php if (isset($producto['producto_codigo_interno'])) echo $producto['producto_codigo_interno'] ?>" autocomplete="off">

                                </div>
                                <div class="col-md-1">
                                    <input type="checkbox" name="chk" class="desc_chk" id="chk_codigo" <?= ($nombre_producto[5] == '1') ? 'checked' : ''; ?>>
                                </div>
                            </div>
                        <?php } ?>
                        <?php if ($columna->nombre_columna == 'producto_codigo_barra' and $columna->activo == 1) { ?>
                            <div class="form-group">
                                <div class="col-md-3"><label class="control-label">C&oacute;digo de barra:</label></div>
                                <div class="col-md-8">

                                    <input type="text" name="producto_codigo_barra" id="codigodebarra"
                                           class='form-control' autofocus="autofocus" maxlength="25"
                                           value="<?php if (isset($producto['producto_codigo_barra'])) echo $producto['producto_codigo_barra'] ?>">

                                </div>
                            </div>
                        <?php } ?>

                        <?php if ($columna->nombre_columna == 'producto_nombre') { ?>
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label class="control-label">Nombre:</label>
                                </div>

                                <div class="col-md-8">
                                    <input type="text" name="producto_nombre_unico" required="true" id="producto_nombre_unico"
                                           class='form-control cbo_desc'
                                           maxlength="100"
                                           value="<?php if (isset($producto['producto_nombre_original'])) echo $producto['producto_nombre_original'] ?>" autocomplete="off">
                                    <br>
                                    <label id="label_producto_nombre" class="control-label">
                                        <?php if (isset($producto['producto_nombre'])) echo $producto['producto_nombre'] ?>
                                    </label>
                                    <input type="hidden" name="producto_nombre" id="producto_nombre" value="<?php if (isset($producto['producto_nombre'])) echo $producto['producto_nombre'] ?>">
                                </div>
                            </div>

                        <?php } ?>
                        <?php if ($columna->nombre_columna == 'producto_descripcion' and $columna->activo == 1) { ?>
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label class="control-label">Descripcion:</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" name="producto_descripcion" id="producto_descripcion"
                                           class='form-control'
                                           maxlength="500"
                                           value="<?php if (isset($producto['producto_descripcion'])) echo $producto['producto_descripcion'] ?>">
                                </div>
                            </div>
                        <?php } ?>
                        <?php if ($columna->nombre_columna == 'producto_marca' and $columna->activo == 1) { ?>
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="linea" class="control-label">Marca:</label>
                                </div>
                                <div class="col-md-7">
                                    <select name="producto_marca" id="producto_marca" class='cho form-control cbo_desc'>
                                        <option value="">Seleccione</option>
                                        <?php if (count($marcas) > 0): ?>
                                            <?php foreach ($marcas as $marca): ?>
                                                <option
                                                    value="<?php echo $marca['id_marca']; ?>" <?php if (isset($producto['producto_marca']) && $producto['producto_marca'] == $marca['id_marca']) echo 'selected' ?> ><?php echo $marca['nombre_marca']; ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                    </select>
                                </div>
                                <div class="col-md-1">
                                    <a class="btn btn-default" data-toggle="tooltip"
                                       title="Agregar Marca" data-original-title="Agregar Marca"
                                       href="#" onclick="agregarmarca()">
                                        <i class="hi hi-plus-sign"></i>
                                    </a>
                                </div>
                                <div class="col-md-1">
                                    <input type="checkbox" name="chk" class="desc_chk" id="chk_marca" <?= ($nombre_producto[4] == '1') ? 'checked' : ''; ?>>
                                </div>
                            </div>
                        <?php } ?>
                        <?php if ($columna->nombre_columna == 'produto_grupo' and $columna->activo == 1) { ?>
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="grupo" class="control-label">Grupo:</label>
                                </div>
                                <div class="col-md-7">
                                    <select name="produto_grupo" id="produto_grupo" class='cho form-control cbo_desc'>
                                        <option value="">Seleccione</option>
                                        <?php if (count($grupos) > 0): ?>
                                            <?php foreach ($grupos as $grupo): ?>
                                                <option
                                                    value="<?php echo $grupo['id_grupo']; ?>" <?php if (isset($producto['produto_grupo']) && $producto['produto_grupo'] == $grupo['id_grupo']) echo 'selected' ?>><?php echo $grupo['nombre_grupo']; ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                    </select>
                                </div>
                                <div class="col-md-1">
                                    <a class="btn btn-default" data-toggle="tooltip"
                                       title="Agregar Grupo" data-original-title="Agregar Grupo"
                                       href="#" onclick="agregargrupo()">
                                        <i class="hi hi-plus-sign"></i>
                                    </a>
                                </div>
                                <div class="col-md-1">
                                    <input type="checkbox" name="chk" class="desc_chk" id="chk_grupo" <?= ($nombre_producto[0] == '1') ? 'checked' : ''; ?>>
                                </div>
                            </div>
                        <?php } ?>
                        <?php if ($columna->nombre_columna == 'producto_familia' and $columna->activo == 1) { ?>
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="producto_familia" class="control-label">Familia:</label>
                                </div>
                                <div class="col-md-7">
                                    <select name="producto_familia" id="producto_familia" class='cho form-control cbo_desc'>
                                        <option value="">Seleccione</option>
                                        <?php if (count($familias) > 0): ?>
                                            <?php foreach ($familias as $familia): ?>
                                                <option
                                                    value="<?php echo $familia['id_familia']; ?>" <?php if (isset($producto['producto_familia']) && $producto['producto_familia'] == $familia['id_familia']) echo 'selected' ?>><?php echo $familia['nombre_familia']; ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                    </select>
                                </div>
                                <div class="col-md-1">
                                    <a class="btn btn-default" data-toggle="tooltip"
                                       title="Agregar Familia" data-original-title="Agregar Familia"
                                       href="#" onclick="agregarfamilia()">
                                        <i class="hi hi-plus-sign"></i>
                                    </a>
                                </div>
                                <div class="col-md-1">
                                    <input type="checkbox" name="chk" class="desc_chk" id="chk_familia" <?= ($nombre_producto[1] == '1') ? 'checked' : ''; ?>>
                                </div>
                            </div>
                        <?php } ?>
                        <?php if ($columna->nombre_columna == 'producto_linea' and $columna->activo == 1) { ?>
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="producto_linea" class="control-label">L&iacute;nea:</label>
                                </div>
                                <div class="col-md-7">
                                    <select name="producto_linea" id="producto_linea" class='cho form-control cbo_desc'>
                                        <option value="">Seleccione</option>
                                        <?php if (count($lineas) > 0): ?>
                                            <?php foreach ($lineas as $linea): ?>
                                                <option
                                                    value="<?php echo $linea['id_linea']; ?>" <?php if (isset($producto['producto_linea']) && $producto['producto_linea'] == $linea['id_linea']) echo 'selected' ?>><?php echo $linea['nombre_linea']; ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                    </select>
                                </div>
                                <div class="col-md-1">
                                    <a class="btn btn-default" data-toggle="tooltip"
                                       title="Agregar Linea" data-original-title="Agregar Linea"
                                       href="#" onclick="agregarlinea()">
                                        <i class="hi hi-plus-sign"></i>
                                    </a>
                                </div>
                                <div class="col-md-1">
                                    <input type="checkbox" name="chk" class="desc_chk" id="chk_linea" <?= ($nombre_producto[2] == '1') ? 'checked' : ''; ?>>
                                </div>
                            </div>
                        <?php } ?>

                        <?php if ($columna->nombre_columna == 'producto_modelo' and $columna->activo == 1) { ?>
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="producto_modelo" class="control-label">Modelo:</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" name="producto_modelo" required="true" id="producto_modelo"
                                           class='form-control cbo_desc'
                                           maxlength="100"
                                           value="<?php if (isset($producto['producto_modelo'])) echo $producto['producto_modelo'] ?>" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-1">
                                <input type="checkbox" name="chk" class="desc_chk" id="chk_modelo" <?= ($nombre_producto[3] == '1') ? 'checked' : ''; ?>>
                            </div>
                        <?php } ?>


                        <?php if ($columna->nombre_columna == 'producto_proveedor' and $columna->activo == 1) { ?>
                            <div class="form-group">
                                <div class="col-md-3"><label class="control-label">Proveedor:</label></div>
                                <div class="col-md-7">
                                    <select name="producto_proveedor" id="producto_proveedor" class='cho form-control'>
                                        <option value="">Seleccione</option>
                                        <?php if (count($proveedores) > 0): ?>
                                            <?php foreach ($proveedores as $proveedor): ?>
                                                <option
                                                    value="<?php echo $proveedor->id_proveedor; ?>" <?php if (isset($producto['producto_proveedor']) && $producto['producto_proveedor'] == $proveedor->id_proveedor) echo 'selected' ?>><?php echo $proveedor->proveedor_nombre; ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                    </select>

                                </div>
                                <div class="col-md-1">
                                    <a class="btn btn-default" data-toggle="tooltip"
                                       title="Agregar Proveedor" data-original-title="Agregar Proveedor"
                                       href="#" onclick="agregarproveedor()">
                                        <i class="hi hi-plus-sign"></i>
                                    </a>
                                </div>
                            </div>
                        <?php } ?>
                        <?php if ($columna->nombre_columna == 'producto_stockminimo' and $columna->activo == 1) { ?>
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="stockmin" class="control-label">Stock M&iacute;nimo:</label>
                                </div>

                                <div class="col-md-8">


                                    <div class="input-prepend input-append input-group">
                                        <span class="input-group-addon">cant.</span>
                                        <input type="text" class='input-small input-square form-control'
                                               name="producto_stockminimo"
                                               id="producto_stockminimo" maxlength="11"
                                               onkeydown="return soloDecimal(this, event);"
                                               value="<?php
                                               if (isset($producto['producto_stockminimo']))
                                                   echo $producto['producto_stockminimo'];
                                               else
                                                   echo "0";
                                               ?>">
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

    <?php if ($columna->nombre_columna == 'producto_vencimiento' and $columna->activo == 1) { ?>
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="producto_vencimiento" class="control-label">Fecha de
                                        Vencimiento:</label>
                                </div>

                                <div class="col-md-8">
                                    <input type="text" class='input-small input-square form-control my_datepicker'
                                           name="producto_vencimiento"
                                           id="producto_vencimiento"
                                           readonly
                                           value="<?php if (isset($producto['producto_vencimiento'])) echo date('d-m-Y', strtotime($producto['producto_vencimiento'])) ?>">
                                </div>
                            </div>
                        <?php } ?>

    <?php if ($columna->nombre_columna == 'producto_afectacion_impuesto'): ?>
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="afectacion_impuesto" class="control-label">Afectacion del
                                        Impuesto:</label>
                                </div>
                                <div class="col-md-8">
                                    <select name="afectacion_impuesto" id="afectacion_impuesto"
                                            class='cho form-control'>
                                        <option value="">Seleccione</option>

                                        <option value="<?= OP_GRAVABLE ?>"
        <?= isset($producto['producto_afectacion_impuesto']) && $producto['producto_afectacion_impuesto'] == OP_GRAVABLE ? 'selected' : '' ?>>
                                            GRAVABLE
                                        </option>

                                        <option value="<?= OP_EXONERADA ?>"
        <?= isset($producto['producto_afectacion_impuesto']) && $producto['producto_afectacion_impuesto'] == OP_EXONERADA ? 'selected' : '' ?>>
                                            EXONERADA
                                        </option>

                                        <option value="<?= OP_INAFECTA ?>"
        <?= isset($producto['producto_afectacion_impuesto']) && $producto['producto_afectacion_impuesto'] == OP_INAFECTA ? 'selected' : '' ?>>
                                            INAFECTA
                                        </option>
                                    </select>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php
                        if ($columna->nombre_columna == 'producto_impuesto') {
                            $impuesto_ivg = "";
                            if (count($impuestos) > 0):
                                foreach ($impuestos as $impuesto):
                                    if ((strtoupper($impuesto['nombre_impuesto']) == "IGV")) {
                                        $impuesto_ivg = $impuesto['id_impuesto'];
                                    }
                                endforeach;
                            endif;
                            ?>
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="impuesto" class="control-label">Impuesto:</label>
                                </div>
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <select name="producto_impuesto" id="producto_impuesto"
                                                    class='cho form-control'>
                                                <option value="">Seleccione</option>
                                                <?php if (count($impuestos) > 0): ?>
            <?php foreach ($impuestos as $impuesto): ?>
                                                        <option
                                                            value="<?php echo $impuesto['id_impuesto']; ?>"
                                                            data-impuesto="<?= $impuesto['porcentaje_impuesto'] ?>"
                                                                <?php if (isset($producto['producto_impuesto']) && $producto['producto_impuesto'] == $impuesto['id_impuesto']) echo 'selected'; ?>>
                                                        <?php echo $impuesto['nombre_impuesto']; ?>
                                                        </option>
                                                    <?php endforeach; ?>
        <?php endif; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="input-group">
                                                <input
                                                    type="text"
                                                    style="text-align: right;"
                                                    class="form-control"
                                                    name="valor_importe"
                                                    id="valor_importe"
                                                    readonly>
                                                <div class="input-group-addon tipo_moneda">%</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
    <?php if ($columna->nombre_columna == 'producto_largo' and $columna->activo == 1) { ?>
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="impuesto" class="control-label">Largo:</label>
                                </div>
                                <div class="col-md-8">

                                    <div class="input-prepend input-append input-group">
                                        <span class="input-group-addon">Cm.</span>
                                        <input type="number" name="producto_largo" id="producto_largo"
                                               class='cho form-control'
                                               value="<?php if (isset($producto['producto_largo'])) echo $producto['producto_largo'] ?>"/>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
    <?php if ($columna->nombre_columna == 'producto_ancho' and $columna->activo == 1) { ?>
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="impuesto" class="control-label">Ancho:</label>
                                </div>
                                <div class="col-md-8">

                                    <div class="input-prepend input-append input-group">
                                        <span class="input-group-addon">Cm.</span>
                                        <input type="number" name="producto_ancho" id="producto_ancho"
                                               class='cho form-control'
                                               value="<?php if (isset($producto['producto_ancho'])) echo $producto['producto_ancho'] ?>"/>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
    <?php if ($columna->nombre_columna == 'producto_alto' and $columna->activo == 1) { ?>
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="impuesto" class="control-label">Alto:</label>
                                </div>
                                <div class="col-md-8">


                                    <div class="input-prepend input-append input-group">
                                        <span class="input-group-addon">Cm.</span>
                                        <input type="number" name="producto_alto" id="producto_alto"
                                               class='cho form-control'
                                               value="<?php if (isset($producto['producto_alto'])) echo $producto['producto_alto'] ?>"/>
                                    </div>
                                </div>


                            </div>
                        <?php } ?>
    <?php if ($columna->nombre_columna == 'producto_peso' and $columna->activo == 1) { ?>
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="impuesto" class="control-label">Peso:</label>
                                </div>
                                <div class="col-md-8">


                                    <div class="input-prepend input-append input-group">
                                        <span class="input-group-addon">Kg.</span>
                                        <input type="number" name="producto_peso" id="producto_peso"
                                               class='cho form-control'
                                               value="<?php if (isset($producto['producto_peso'])) echo $producto['producto_peso'] ?>"/>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
    <?php if ($columna->nombre_columna == 'producto_nota' and $columna->activo == 1) { ?>
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="impuesto" class="control-label">Nota:</label>
                                </div>
                                <div class="col-md-8">
                                    <textarea name="producto_nota" id="producto_nota"
                                              class='cho form-control'><?php if (isset($producto['producto_nota'])) echo $producto['producto_nota'] ?></textarea>
                                </div>
                            </div>
                        <?php } ?>
    <?php if ($columna->nombre_columna == 'producto_cualidad') { ?>
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="impuesto" class="control-label">Cualidad:</label>
                                </div>
                                <div class="col-md-8">

                                    <select class="form-control" id="producto_cualidad" name="producto_cualidad">
                                        <option value="">Seleccione</option>
                                        <option
                                            value="<?= MEDIBLE ?>" <?php echo 'selected'; ?>><?= MEDIBLE ?></option>
                                        <option
                                            value="<?= PESABLE ?>" <?php if (isset($producto['producto_id']) and $producto['producto_cualidad'] == PESABLE) echo 'selected' ?>><?= PESABLE ?></option>

                                    </select>
                                </div>
                            </div>
                        <?php } ?>

    <?php if ($columna->nombre_columna == 'producto_titulo_imagen' and $columna->activo == 1) { ?>
                            <div class="form-group ">

                                <div class="col-md-3">
                                    <label for="" class="control-label">Detalle Imagen:</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" name="titulo_imagen" id="titulo_imagen"
                                           class='cho form-control' placeholder="Titulo"
                                           value="<?php
                                           if (isset($producto['producto_titulo_imagen'])) {
                                               echo $producto['producto_titulo_imagen'];
                                           }
                                           ?>"/>
                                </div>
                                <div class="col-md-3">
                                    <label for="" class="control-label">&nbsp;<br></label>
                                </div>
                                <div class="col-md-8">
                                    <textarea id="compose-message" name="descripcion_imagen" rows="5"
                                              class="form-control textarea-editor" placeholder="Su descripcion">
                                                  <?php
                                                  if (isset($producto['producto_descripcion_img'])) {
                                                      echo $producto['producto_descripcion_img'];
                                                  }
                                                  ?>
                                    </textarea>
                                </div>
                            </div>
                        <?php } ?>
<?php endforeach ?>


                    <div class="form-group">

                        <div class="col-md-3">
                            <label for="" class="control-label">Estado:</label>
                        </div>


                        <div class="col-md-3">
                            <input type="radio" value=1 name="estado" <?php
                            if (isset($producto['producto_estado'])
                                    and $producto['producto_estado'] == 1
                            ) {
                                echo "checked";
                            } else {
                                echo "checked";
                            }
                            ?>> Activo

                            <input type="radio" value=0 name="estado" <?php
                            if (isset($producto['producto_estado'])
                                    and $producto['producto_estado'] == 0
                            )
                                echo "checked";
                            ?>> Inactivo
                        </div>
                        <div class="col-md-5">
                            <label for="" class="control-label">&nbsp;<br></label>
                        </div>
                        <div class="col-md-11">
                            <label for="" class="control-label"><br></label>
                        </div>

                    </div>
                    <br>

                </div>

<?php $md = get_moneda_defecto() ?>
                <div class="tab-pane" role="tabpanel" id="precios" role="tabpanel">

<?php //if ($this->session->userdata('PRECIO_DE_VENTA') == "MANUAL") {    ?>
                    <div class="panel" style="margin-bottom: 0px;">

                        <div class="row" style="display: <?= valueOption('ACTIVAR_SHADOW') == 1 ? 'block' : 'none'; ?>">
                            <div class="col-md-3">

                            </div>


                            <div class="col-md-<?php echo count($costos_unitario) == 1 ? '5' : '3' ?>"
                                 style="text-align: right;"><label class="panel-admin-text">Costo
                                    Contable Compra: </label></div>

                            <div class="col-md-2"
                                 style="display: <?php echo count($costos_unitario) == 1 ? 'none' : 'block' ?>">
                                <select id="cu_moneda_contable" name="cu_moneda_contable" class="form-control">
<?php foreach ($costos_unitario as $cu): ?>
                                        <option
                                            data-costo="<?php echo $cu['cu_contable_costo'] ?>"
                                            data-simbolo="<?php echo $cu['moneda_simbolo'] ?>"
                                            data-tasa="<?php echo $cu['moneda_tasa'] ?>"
                                            value="<?php echo $cu['moneda_id'] ?>" <?php echo $cu['cu_contable_activo'] == '1' ? 'selected="selected"' : '' ?>>
                                        <?php echo $cu['moneda_nombre'] ?>
                                        </option>
                                        <?php
                                        if ($cu['cu_contable_activo'] == '1') {
                                            $contable_costo = $cu['cu_contable_costo'];
                                            $simbolo = $cu['moneda_simbolo'];
                                            $tasa = $cu['moneda_tasa'];
                                        }
                                        ?>
<?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="hidden" name="tasa_convert_contable" id="tasa_convert_contable"
                                       data-value-s="<?php echo isset($contable_costo) ? number_format($contable_costo, $numero_decimales, '.', '') : '0.00' ?>"
                                       value="<?php echo isset($tasa) ? $tasa : '0.00' ?>">
                                <div class="input-prepend input-append">
                                    <div class="input-group">
                                        <div
                                            class="input-group-addon tipo_tasa_contable"><?php echo isset($simbolo) ? $simbolo : $md->simbolo ?></div>
                                        <input type="number" name="contable_costo" id="contable_costo"
                                               class='cho form-control'
                                               value="<?php echo (isset($contable_costo)) ? number_format($contable_costo, $numero_decimales, '.', '') : '0.00' ?>"/>
                                    </div>
                                </div>

                            </div>
                            <div class="col-md-1">
                                <label class="panel-admin-text" style="text-align: left;">
<?php echo isset($um_minimo) ? $um_minimo : '' ?>
                                </label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-2">
                            </div>
                            <div class="col-md-<?php echo count($costos_unitario) == 1 ? '5' : '3' ?>"
                                 style="text-align: right;">
                                <label class="panel-admin-text">Costo
                                    Unitario: </label></div>

                            <div class="col-md-2"
                                 style="display: <?php echo count($costos_unitario) == 1 ? 'none' : 'block' ?>">
                                <select id="cu_moneda" name="cu_moneda" class="form-control">
<?php foreach ($costos_unitario as $cu): ?>
                                        <option
                                            data-costo="<?php echo $cu['cu_costo'] ?>"
                                            data-simbolo="<?php echo $cu['moneda_simbolo'] ?>"
                                            data-tasa="<?php echo $cu['moneda_tasa'] ?>"
                                            value="<?php echo $cu['moneda_id'] ?>" <?php echo $cu['cu_activo'] == '1' ? 'selected="selected"' : '' ?>>
                                        <?php echo $cu['moneda_nombre'] ?>
                                        </option>
                                        <?php
                                        if ($cu['cu_activo'] == '1') {
                                            $simbolo = $cu['moneda_simbolo'];
                                            $tasa = $cu['moneda_tasa'];
                                            $producto['producto_costo_unitario'] = $cu['cu_costo'];
                                        }
                                        ?>
                                    <?php endforeach;
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="hidden" name="tasa_convert" id="tasa_convert"
                                       data-value-s="<?php echo isset($producto['producto_costo_unitario']) ? number_format($producto['producto_costo_unitario'], 2, '.', '') : '' ?>"
                                       value="<?php echo $tasa ?>">
                                <div class="input-prepend input-append">
                                    <div class="input-group">
                                        <div class="input-group-addon tipo_tasa"><?php echo $simbolo ?></div>
                                        <input type="number" name="costo_unitario" id="costo_unitario"
                                               class='cho form-control'
                                               value="<?php echo isset($producto['producto_costo_unitario']) ? number_format($producto['producto_costo_unitario'], 2, '.', '') : '0.00' ?>"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label class="panel-admin-text" style="text-align: left;">                                 
                                    <a href="#" title="Este costo incluye impuesto"><i class="far fa-question-circle"></i></a>
<?php echo isset($um_minimo) ? $um_minimo : '' ?>
                                </label>
                            </div>

                        </div>


                    </div>

<?php //}   ?>

                    <div class="panel" style="margin-bottom: 0px;">

                        <?php
                        if ($this->session->userdata('PRECIO_DE_VENTA') == "CALCULADO") {
                            /* aqui va a entrar cuando la opcion de precio de venta sea CALCULADO */


                            $simbolo_cambio_tasa = "";
                            $cambio_soles_tasa = "";
                            $moneda_local = "";
                            $moneda_local_simbolo = "";
                            $nombre = "";
                            $tasa = "";
                            $simbolo = "";

                            foreach ($monedas as $mon) {
                                if ($mon['id_moneda'] == MONEDA_DEFECTO) {
                                    $moneda_local_simbolo = $mon['simbolo'];
                                    $moneda_local = $mon['id_moneda'];
                                    $nombre = $mon['nombre'];
                                    $tasa = $mon['tasa_soles'];
                                    $simbolo = $mon['simbolo'];
                                } else {
                                    $simbolo_cambio_tasa = $mon['simbolo'];
                                    $cambio_soles_tasa = $mon['tasa_soles'];
                                }
                            }
                            $i = 0;
                            arsort($costos_unitario);
                            ?>
                            <div class="section-border">
                                <?php foreach ($costos_unitario as $cu):
                                    ?>
                                    <div class="row">
                                        <div class="control-group">


                                            <div class="col-md-2 text-right">
                                                <label for="" class="control-label panel-admin-text">Precio de
                                                    Compra:</label>
                                            </div>
                                            <div class="col-md-3 text-right ">
                                                <div class="input-group">
                                                    <span
                                                        class="input-group-addon"><? echo $cu['moneda_simbolo'] ?></span>
                                                    <input min="0.00" type="number" name="" id="precio_compra<?= $i ?>"
                                                           onchange="calcularpreciocompra(<?= $i ?>)"
                                                           onKeyUp="calcularpreciocompra(<?= $i ?>)"
                                                           onkeydown="return soloDecimal(this, event);"
                                                           value="<?php echo $cu['cu_costo'] ?>"
                                                           class='form-control precio_compra text-right'
                                                           data-nombre="<? echo $cu['moneda_nombre'] ?>"
                                                           data-tasa="<? echo $cu['moneda_tasa'] ?>"
                                                           data-simbolo="<? echo $cu['moneda_simbolo'] ?>"
                                                           data-operacion="<? echo $cu['moneda_oper'] ?>">
                                                </div>
                                            </div>

        <?php if ($i == 0) { ?>


                                                <div class="col-md-1 text-right">
                                                    <label for="" class="control-label panel-admin-text">Fecha:</label>
                                                </div>
                                                <div class="col-md-2 text-right">
                                                    <input type="text" name="" id="fecha_producto"
                                                           value="<?= date('d-m-Y') ?>" readonly
                                                           class='form-control text-right'>
                                                </div>

                                                <div class="col-md-1 text-right"><label for=""
                                                                                        class="control-label panel-admin-text">TC:</label>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="input-group">
                                                        <span
                                                            class="input-group-addon"><? echo $moneda_local_simbolo ?></span>
                                                        <input type="number" onKeyUp="calculo_inicial()"
                                                               onchange="calculo_inicial()" name="tasa_id" min="0.00"
                                                               id="tasa_id" onkeydown="return soloDecimal(this, event);"
                                                               value="<?= $cambio_soles_tasa ?>"
                                                               class='form-control text-right'>
                                                    </div>

                                                </div>


        <?php } ?>


                                            <?php
                                            //$producto['producto_costo_unitario'] = "";
                                            if ($cu['cu_activo'] == '1') {
                                                $producto['producto_costo_unitario'] = $cu['cu_costo'];
                                            }
                                            $i++;
                                            ?>


                                        </div>
                                    </div>
                                    <br>
    <?php endforeach; ?>
                            </div>


                            <div class="section-border">

                                <div class="row ">
                                    <div class="control-group">

                                        <input type="hidden" name="calculado_costo_unitario"
                                               id="calculado_costo_unitario"
                                               value="<?= $producto['producto_costo_unitario'] ?>">
                                        <input type="hidden" id="calculado_costo_u_nombre" value="<?= $nombre ?>">
                                        <input type="hidden" name="moneda_id_calculo" id="moneda_id_calculo"
                                               value="<?= $moneda_local ?>">
                                        <div class="col-md-2 text-right">
                                            <label for="" class="control-label panel-admin-text">Flete:</label>
                                        </div>
                                        <div class="col-md-3 ">
                                            <div class="input-group">
                                                <span class="input-group-addon"><? echo $simbolo ?></span>
                                                <input type="number" onKeyUp="calculo_inicial()"
                                                       onchange="calculo_inicial()" name="flete_producto"
                                                       id="flete_producto" onkeydown="return soloDecimal(this, event);"
                                                       value="" class='form-control text-right'>
                                            </div>
                                        </div>

                                        <div class="col-md-3 text-right">

                                        </div>
                                        <div class="col-md-3 text-right"></div>


                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="control-group">

                                        <div class="col-md-2">
                                            <label for="" class="control-label panel-admin-text">Nuevo Precio de
                                                Venta:</label>
                                        </div>
                                        <div class="col-md-3 ">
                                            <div class="input-group">
                                                <span class="input-group-addon"><? echo $simbolo ?></span>
                                                <input type="number" name="nuevo_precio_venta" id="nuevo_precio_venta"
                                                       readonly onkeydown="return soloDecimal(this, event);"
                                                       value="" class='form-control text-right'>
                                            </div>
                                        </div>

                                        <div class="col-md-3 text-right">
                                            <label for="" class="control-label panel-admin-text">Margen de
                                                Utilidad:</label>
                                        </div>
                                        <div class="col-md-3 text-right">
                                            <div class="input-group">
                                                <span class="input-group-addon"><? echo $simbolo ?></span>
                                                <input type="number" onKeyUp="calculo_inicial()"
                                                       onchange="calculo_inicial()" name="margen_utilidad_producto"
                                                       id="margen_utilidad_producto"
                                                       onkeydown="return soloDecimal(this, event);"
                                                       value="0" class='form-control text-right'>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        <?php } ?>

<?php //if ($this->session->userdata('PRECIO_DE_VENTA') == "MANUAL" || !isset($producto['producto_nombre'])) {    ?>
                        <div class="row">
                            <div class="col-md-3">
                                <a class="btn btn-default" onclick="agregarprecio();">
                                    <i class="fa fa-plus "></i> Nuevo Precio (F7)
                                </a>
                            </div>
                        </div>
<?php //}   ?>
                        <br>
                        <div class="table-responsive ">


                            <!-- Block -->

                            <table class="table block table-striped dataTable table-bordered">
                                <thead>
                                <th>Presentación</th>
                                <th>Cantidad</th>
                                <?php
                                foreach ($precios as $precio):
                                    if ($precio['mostrar_precio']):
                                        ?>
                                        <th><?= $precio['nombre_precio'] ?></th>
                                    <?php endif ?>
<?php endforeach ?>
                                <th></th>
                                </thead>
                                <tbody id="unidadescontainer" class="draggable-tbody">

                                    <?php
                                    $countunidad = 0;
                                    if (isset($unidades_producto) and count($unidades_producto)):


                                        foreach ($unidades_producto as $unidad) {
                                            ?>
                                            <tr id="trunidad<?= $countunidad ?>" class="trdrag">
                                                <td>
                                                    <select name='medida[<?= $countunidad ?>]'
                                                            id='medida<?= $countunidad ?>'
                                                            class='form-control unidad_select'
                                                            data-row="<?= $countunidad ?>"
                                                            >
                                                                <?php foreach ($unidades as $unidad2):
                                                                    ?>
                                                            <option
                                                                value='<?= $unidad2['id_unidad'] ?>' <?php if ($unidad2['id_unidad'] == $unidad['id_unidad']) echo 'selected' ?>><?= $unidad2['nombre_unidad'] ?></option>"

        <?php endforeach ?></select>

                                                </td>
                                                <td><input type="number" class="form-control unidades" required
                                                           min="1"
                                                           onkeydown="return soloNumeros(event);"
                                                           value='<?= $unidad['unidades'] ?>'
                                                           data-row="<?php echo $countunidad ?>"
                                                           name="unidad[<?= $countunidad ?>]"
                                                           id="unidad[<?= $countunidad ?>]"
                                                           >
                                                </td>
                                                <?php
                                                $countproducto = 0;

                                                $precio_venta_producto = 0;

                                                foreach ($precios as $precioo) {

                                                    if ($precioo['mostrar_precio']) {
                                                        $blanco = true;
                                                        foreach ($precios_producto[$countunidad] as $precio) {


                                                            if ($precioo['id_precio'] == 3)
                                                                $precio_class = 'precio_unitario';
                                                            elseif ($precioo['id_precio'] == 1)
                                                                $precio_class = 'precio_venta';
                                                            else
                                                                $precio_class = 'precio_descuento';


                                                            if ($precio['nombre_unidad'] == "UNIDAD" and $precio_class == "precio_venta") {
                                                                $precio_venta_producto = $precio['precio'];
                                                            }
                                                            if ($precio['id_precio'] == $precioo['id_precio']) {
                                                                $blanco = false;
                                                                ?>
                                                                <td><input type="hidden" value='<?= $precio['id_precio'] ?>'
                                                                           name='precio_id_<?= $countunidad ?>[<?= $countproducto ?>]'/>
                                                                    <input id="<?php echo $precio_class . $countunidad ?>"
                                                                           min="1"
                                                                           data-row="<?php echo $countunidad ?>"
                                                                           data-nombre_precio="<?php echo $precio_class ?>"
                                                                           type="number"
                                                                           class="form-control <?php echo $precio_class ?>"
                                                                           required
                                                                           value="<?= number_format($precio['precio'], $numero_decimales, '.', '') ?>"
                                                                           name="precio_valor_<?= $countunidad ?>[<?= $countproducto ?>]">

                                                                </td>

                                                                <?php
                                                            }
                                                        }
                                                        if ($blanco) {
                                                            ?>
                                                            <td><input type="hidden" value='<?= $precioo['id_precio'] ?>'
                                                                       name='precio_id_<?= $countunidad ?>[<?= $countproducto ?>]'/>
                                                                <input id="<?php echo $precio_class . $countunidad ?>"
                                                                       data-row="<?php echo $countunidad ?>"
                                                                       min="1"
                                                                       data-nombre_precio="<?php echo $precio_class ?>"
                                                                       type="number"
                                                                       class="form-control  <?php echo $precio_class ?>"
                                                                       required
                                                                       value='0.00'
                                                                       name="precio_valor_<?= $countunidad ?>[<?= $countproducto ?>]">

                                                            </td>
                                                        <?php }
                                                        ?>


                                                        <?php
                                                    }
                                                    $countproducto++;
                                                }
                                                ?>

                                                <td width='13%'>
                                                    <a href="#" class='btn btn-default'
                                                       id="eliminar<?= $countunidad ?>"
                                                       onclick="eliminarunidad(<?= $countunidad ?>);"><i
                                                            class="fa fa-remove"></i> </a>
                                                    <a class='btn btn-default'
                                                       data-toggle='tooltip'
                                                       title='Mover'
                                                       data-original-title='Mover'
                                                       href='#'
                                                       style="cursor: move"><i
                                                            class='fa fa-arrows-v'></i> </a></td>
                                            </tr>
                                            <?php
                                            $countunidad++;
                                        } endif;
                                    ?>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="tab-pane" role="tabpanel" id="imagenes" role="tabpanel">

                    <div class="form-group" id="row1">
                        <div class="row">
                            <div class="col-md-6">

                                <div class="input-prepend input-append input-group">
                                    <span class="input-group-addon"><i class="fa fa-folder"></i> </span>
                                    <input type="file" onchange="asignar_imagen(0)" class="form-control input_imagen"
                                           data-count="0" name="userfile[]" accept="image/*"
                                           id="input_imagen0">

                                </div>


                            </div>

                            <div class="col-md-2">
                                <img id="imgSalida0" data-count="0" src="" height="100" width="100">

                            </div>
                        </div>


                    </div>
                    <div>
                        <button id="agregar_img" align="center" class="btn btn-default">Agregar otra imagen</button>
                    </div>
                    <br>
                    <div class="form-group">
                        <div class="row">
                            <div class="text-right">
                                <div class="col-md-12">

                                    <?php if (isset($producto['producto_id'])): ?>
    <?php $ruta_imagen = "uploads/" . $producto['producto_id'] . "/" ?>


                                        <div class="row">
                                            <?php
                                            if (isset($images) and count($images) > 0) {
                                                $con_image = 0;
                                                foreach ($images as $img):
                                                    ?>
                                                    <div class="col-sm-4"
                                                         style="text-align: center; margin-bottom: 20px;"
                                                         id="div_imagen_producto<?= $con_image ?>">

                                                        <a href="#" class="img_show"
                                                           data-src="<?php echo $ruta . $ruta_imagen . $img; ?>">
                                                            <img width="200" height="200"
                                                                 src="<?php echo $ruta . $ruta_imagen . $img; ?>">
                                                        </a>
                                                        <br>
                                                        <a href="#"
                                                           onclick="borrar_img('<?= $producto['producto_id'] ?>', '<?= $img ?>', '<?= $con_image ?>')"
                                                           style="width: 200px; margin: 0;" id="eliminar"
                                                           class="btn btn-danger"><i

                                                                class="fa fa-trash-o"></i> Eliminar</a>
                                                    </div>


                                                    <?php
                                                    $con_image++;

                                                endforeach;
                                            }
                                            ?>
                                        </div>


<?php endif; ?>


                                </div>
                            </div>
                        </div>

                    </div>


                </div>


            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-md-offset-8 col-md-2">
                        <button class="btn btn-default" type="button" onclick="confirm_save('producto')"
                                id="btnGuardar"><i
                                class="fa fa-save"></i> Guardar
                        </button>
                    </div>
                    <div class="col-md-2">
                        <input type="reset" class='btn btn-danger' value="Cancelar" data-dismiss="modal">
                    </div>

                </div>
            </div>

        </div>
<?= form_close() ?>
    </div>


    <div class="modal fade" id="confirmarcerrar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <form name="" id="" method="post">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Confirmar</h4>
                    </div>
                    <div class="modal-body">
                        <p>Est&aacute; seguro que desea cerrar la ventana ?</p>
                        <input type="hidden" name="id" id="id_borrar">

                    </div>
                    <div class="modal-footer">
                        <button type="button" id="botoneliminar" class="btn btn-primary" onclick="confirmarcerrar()">
                            Confirmar
                        </button>
                        <button type="button" class="btn btn-default" onclick="cancelarcerrar()">Cancelar</button>

                    </div>
                </div>
                <!-- /.modal-content -->
            </div>

    </div>

    <div class="modal fade conf" id="confirm_cu" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" onclick="$('#confirm_cu').modal('hide');" data-dismiss="conf"
                            aria-hidden="true">&times;
                    </button>
                    <h4 class="modal-title">Confirmacion del costo unitario</h4>
                </div>
                <div class="modal-body">

                    <p style="text-align: justify;">Usted usara en este producto como costo unitario por defecto una
                        moneda
                        diferente a la por defecto del sistema.
                        La tasa de esta moneda es de <span id="tasa_text"></span>. Si desea puede cambiarla o dejar
                        esta.
                        La conversion de las otras monedas usaran su tasa por defecto para calcular su costo unitario.
                    </p>

                    <div class="input-prepend input-append">
                        <div class="input-group">
                            <div class="input-group-addon tipo_tasa">$</div>
                            <input type="number" name="tasa_input" id="tasa_input"
                                   class='cho form-control'
                                   value=""/>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" id="ok_cu" class="btn btn-primary" onclick="guardarproducto()">
                        Confirmar
                    </button>
                    <button type="button" id="ok_cu" class="btn btn-warning" onclick="$('#confirm_cu').modal('hide');">
                        Cancelar
                    </button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
    </div>


    <script>

        var nombre_producto = $('#producto_nombre').val()
        var codigo_interno = $('#codigo_interno').val()
<?php if ($columna->nombre_columna == 'producto_vencimiento' and $columna->activo == 1): ?>
            $(".my_datepicker").datepicker({format: 'dd-mm-yyyy'});
<?php endif; ?>

        $(function () {
            $('.desc_chk, .cbo_desc').on('click change', function () {
                var cadena = new Array();
                if ($('#producto_nombre_unico').val() != '') {
                    cadena[0] = $.trim($('#producto_nombre_unico').val());
                }
                $('.desc_chk').each(function () {
                    if ($(this).prop('checked') == true) {
                        var id = $(this).attr('id');
                        if (id == 'chk_grupo') {
                            if ($('#produto_grupo').val() != '') {
                                cadena[1] = $.trim($('#produto_grupo option:selected').text());
                            }
                        } else if (id == 'chk_familia') {
                            if ($('#producto_familia').val() != '') {
                                cadena[2] = $.trim($('#producto_familia option:selected').text());
                            }
                        } else if (id == 'chk_linea') {
                            if ($('#producto_linea').val() != '') {
                                cadena[3] = $.trim($('#producto_linea option:selected').text());
                            }
                        } else if (id == 'chk_modelo') {
                            if ($('#producto_modelo').val() != '') {
                                cadena[4] = $.trim($('#producto_modelo').val());
                            }
                        } else if (id == 'chk_marca') {
                            if ($('#producto_marca').val() != '') {
                                cadena[5] = $.trim($('#producto_marca option:selected').text());
                            }
                        } else {
                            if ($('#codigo_interno').val() != '') {
                                cadena[6] = $.trim($('#codigo_interno').val());
                            }
                        }
                    }
                });

                var newCadena = new Array();
                var i = 0;
                cadena.forEach(function (valor, indice, array) {
                    newCadena[i] = valor;
                    i++;
                });

                $('#label_producto_nombre').text(newCadena.join(' '));
                $('#producto_nombre').val(newCadena.join(' '));
            });

            $('#producto_impuesto').on('change', function () {
                if ($(this).val() == '') {
                    $('#valor_importe').val("");
                } else {
                    $('#valor_importe').val($('#producto_impuesto option:selected').attr('data-impuesto'));
                }
            });

            $('#producto_impuesto').trigger('change');


        });


        $("#cu_moneda").change(function (e) {
            $("#costo_unitario").val(parseFloat($("#cu_moneda option:selected").attr('data-costo')).toFixed(<?= $numero_decimales ?>));
            $(".tipo_tasa").html($("#cu_moneda option:selected").attr('data-simbolo'));
            $("#tasa_convert").val($("#cu_moneda option:selected").attr('data-tasa'));
        });

        $("#cu_moneda_contable").change(function (e) {
            $("#contable_costo").val(parseFloat($("#cu_moneda_contable option:selected").attr('data-costo')).toFixed(<?= $numero_decimales ?>));
            $(".tipo_tasa_contable").html($("#cu_moneda_contable option:selected").attr('data-simbolo'));
            $("#tasa_convert_contable").val($("#cu_moneda_contable option:selected").attr('data-tasa'));
        });

        /*estos metodos lo que hacen es actualizar los select de los campos dinamicos, marca, familia, etc*/
        function update_proveedor(id, nombre) {

            $('#producto_proveedor').append('<option value="' + id + '">' + nombre + '</option>');
            $('#producto_proveedor').val(id)
            $("#producto_proveedor").trigger('chosen:updated');
        }

        function update_linea(id, nombre) {

            $('#producto_linea').append('<option value="' + id + '">' + nombre + '</option>');
            $('#producto_linea').val(id)
            $("#producto_linea").trigger('chosen:updated');
        }

        function update_familia(id, nombre) {

            $('#producto_familia').append('<option value="' + id + '">' + nombre + '</option>');
            $('#producto_familia').val(id)
            $("#producto_familia").trigger('chosen:updated');


        }

        function update_marca(id, nombre) {

            $('#producto_marca').append('<option value="' + id + '">' + nombre + '</option>');
            $('#producto_marca').val(id)
            $("#producto_marca").trigger('chosen:updated');

        }
        function update_grupo(id, nombre) {

            $('#produto_grupo').append('<option value="' + id + '">' + nombre + '</option>');
            $('#produto_grupo').val(id)
            $("#produto_grupo").trigger('chosen:updated');

        }


        //$("select").chosen();
        var ruta = '<?php echo $ruta; ?>';


        function confirm_save(retorno) {

            var tasa = $("#tasa_convert");
            var tasa_contable = $("#tasa_convert_contable");

            //alert(tasa.attr('data-value-s'));

            guardarproducto(retorno);

            /*if (tasa.val() == '0.00' && tasa_contable.val() == '0.00') {
             
             }
             else {
             
             if ($("#tasa_id").val() != undefined) {
             guardarproducto();
             } else {
             $("#tasa_text").html(tasa.val());
             if (tasa.val() != '0.00')
             $("#tasa_input").val(tasa.val());
             else
             $("#tasa_input").val(tasa_contable.val());
             
             $("#confirm_cu").modal('show');
             }
             }*/


        }

        function guardarproducto(retorno) {
//            if ($("#tasa_convert").val() != '0.00' || $("#tasa_convert_contable").val() != '0.00') {
//                $("#tasa_convert").val($("#tasa_input").val());
//                $("#tasa_convert_contable").val($("#tasa_input").val());
//            }
            var unidad_min_index = $('.unidades').length - 1;
            if ($("#unidad\\[" + unidad_min_index + "\\]").val() > 1) {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>La unidad minima tiene que ser 1</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $("#unidad\\[" + unidad_min_index + "\\]").trigger('focus');

                return false;
            }


//            $("#confirm_cu").modal('hide');

            var nombre = $("#producto_nombre");
            var producto_impuesto = $("#producto_impuesto");

            var cod = $("#codigo_interno").val();
            if (cod != undefined && cod == '') {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe ingresar el Codigo Interno del producto</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }


            if (nombre.val() == '' || nombre.val() == 0) {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe ingresar el nombre del producto</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }

            if ($('#afectacion_impuesto').val() == '') {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe seleccionar la afectacion del impuesto</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }

            if (producto_impuesto.val() == '') {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe seleccionar el impuesto</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }

            if ($("#producto_cualidad").val() == '') {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe seleccionar la cualidad del producto</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }


            if ($("#unidadescontainer tr").length == 0) {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe Seleccionar al menos una unidad de medida</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }

            var menor_cero = false;
            var vacios = false;
            var nan = false;
            var negativo = false;
            $("#unidadescontainer input[type='number']").each(function () {
                var txt = $(this).val();

                //console.log(txt);
                if (txt == '') {
                    vacios = true;
                }
                /// console.log(isNaN(txt));
                if (!isNaN(txt)) {
                    nan = true;
                }

                if (parseInt(txt) < 0) {
                    negativo = true;
                }


            });
            var verificar = false;
            $(".unidades").each(function () {
                var txt = $(this).val();

                //console.log(txt);
                if (txt < 1) {
                    verificar = true;
                }


            });
            // alert(verificar)
            if (verificar == true) {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Las unidades deben ser mayor a 0 </h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }

            if (vacios) {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Los campos precios y unidades no pueden estar vac&iacute;os y deben contener solo n&uacute;meros</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }
            if (negativo) {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Los campos precios y unidades no pueden estar vac&iacute;os y deben contener solo n&uacute;meros positivos</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }


            var repetidas = false;

            var seen = {};
            $("#unidadescontainer select[id^='medida']").each(function () {
                var txt = $(this).val();
                //console.log(txt);
                if (seen[txt]) {
                    repetidas = true;
                } else {
                    seen[txt] = true;
                }
            });

            if (repetidas) {

                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Las unidades de medida no deben repetirse!</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;

            }
            var length = $("#unidadescontainer input[id^='unidad']").length;
            var is_last_item;
            var item_actual = null;
            var orden_incorrecto = false;
            $("#unidadescontainer input[id^='unidad']").each(function (index) {

                if (item_actual != null) {
                    if (parseFloat($(this).val()) >= parseFloat(item_actual)) {
                        orden_incorrecto = true;
                    }
                }
                item_actual = $(this).val();


                if ((index == (length - 1))) {


                    is_last_item = $(this).val();

                }


            });

            if (orden_incorrecto) {
                $.bootstrapGrowl('<h4>Por favor verifique la cantidad de unidades, no puede haber una unidad mayor con cantidades menores !</h4>', {
                    type: 'warning',
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;

            }

            /*if(is_last_item!='1'){
             $.bootstrapGrowl('<h4>La unidad minima no puede ser mayor  a uno(1) !</h4>', {
             type: 'warning',
             delay: 2500,
             allow_dismiss: true
             });
             
             $(this).prop('disabled', true);
             
             return false;
             
             }*/

            var last_precio = $('.precio_unitario').length - 1;
            var precio_unitario = parseFloat($('#precio_unitario' + last_precio).val());
            var costo_unitario = parseFloat($('#costo_unitario').val());

            if (costo_unitario >= precio_unitario)
                show_msg('warning', '<h3><strong>El precio unitario de venta es menor o igual a su costo</strong></h3>');



            $('#productomodal').modal('hide');
            $("#barloadermodal").modal('show');
            var formData = new FormData($("#formguardar")[0]);
            $.ajax({
                url: ruta + 'producto/registrar',
                type: "post",
                dataType: "json",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    var callback = getproductosbylocal;
                    var modal = "productomodal";
                    if (data.error == undefined) {
                        if (retorno != 'producto') {
                            update_producto(data.id, data.nombre, data.impuesto, data.producto_id);
                        }
                        $("#barloadermodal").modal('hide');
                        $(".modal-backdrop").remove();
                        if (retorno == 'producto') {
                            $.ajax({
                                url: ruta + 'producto',
                                success: function (data) {
                                    $('#page-content').html(data);
                                }
                            });
                        }

                        var growlType = 'success';

                        $.bootstrapGrowl('<h4>' + data.success + '</h4>', {
                            type: growlType,
                            delay: 2500,
                            allow_dismiss: true
                        });
                    } else {
                        $("#barloadermodal").modal('hide');
                        $('#productomodal').modal('show');
                        if ($('.modal-backdrop').is(':visible')) {
                            $('body').removeClass('modal-open');
                            $('.modal-backdrop').remove();
                        }
                        $.ajax({
                            url: ruta + 'producto',
                            success: function (data) {
                                $('#page-content').html(data);
                            }
                        });
                        var growlType = 'warning';

                        $.bootstrapGrowl('<h4>' + data.error + '</h4>', {
                            type: growlType,
                            delay: 2500,
                            allow_dismiss: true
                        });

                        $(this).prop('disabled', true);

                        /*$("#errorspan").text(data.error);
                         $("#error").css('display','block');*/

                    }


                    setTimeout(function () {
                        //$(".alert-danger").css('display','none');
                        $(".alert-success").css('display', 'none');
                    }, 3000)


                },
                error: function (response) {
                    $('#barloadermodal').modal('hide');
                    $('#productomodal').modal('show');
                    var growlType = 'warning';
                    $.bootstrapGrowl('<h4>Ha ocurrido un error al realizar la operacion</h4>', {
                        type: growlType,
                        delay: 2500,
                        allow_dismiss: true
                    });

                    $(this).prop('disabled', true);

                }

            });


            //App.formSubmitAjax($("#formguardar").attr('action'), getproductosbylocal, 'productomodal', 'formguardar' );

        }
        var unidadcount = <?= $countunidad ?>;
        function agregarprecio() {


            $("#unidadescontainer").append("<tr id='trunidad" + unidadcount + "'>" +
                    "<td><select name='medida[" + unidadcount + "]' data-row='" + unidadcount + "' id='medida" + unidadcount + "' class='form-control unidad_select'>" +
<?php foreach ($unidades as $unidad): ?>
                "<option value='<?= $unidad['id_unidad'] ?>' ><?= $unidad['nombre_unidad'] ?></option>" +
<?php endforeach ?>"</select></td>" +
            "<td><input type='number' class='form-control unidades'" +
                    " data-row='" + unidadcount + "' required name='unidad[" + unidadcount + "]' id='unidad[" + unidadcount + "]'></td>" +
<?php
$preciocount = 0;
foreach ($precios as $precio):
    if ($precio['mostrar_precio']):
        ?>

        <?php
        if ($precio['id_precio'] == 3)
            $precio_class = 'precio_unitario';
        elseif ($precio['id_precio'] == 1)
            $precio_class = 'precio_venta';
        else
            $precio_class = 'precio_descuento';
        ?>

                    "<td><input class='form-control' type='hidden' value='<?= $precio['id_precio'] ?>' name='precio_id_" + unidadcount + "[<?= $preciocount ?>]' id='precio_id" + unidadcount + "'>" +
                            "<input value='0.00' id='<?php echo $precio_class ?>" + unidadcount + "' class='form-control <?php echo $precio_class ?>' data-row='" + unidadcount + "' data-nombre_precio='<?php echo $precio_class ?>' type='number' required name='precio_valor_" + unidadcount + "[<?= $preciocount ?>]' id='precio_valor" + unidadcount + "'></td>" +
    <?php endif ?>

    <?php
    $preciocount++;
endforeach
?>
            "<td width='13%'><a class='btn btn-default' href='#' id='eliminar" + unidadcount + "' onclick='eliminarunidad(" + unidadcount + ");'><i class='fa fa-remove'></i> </a> <a style='cursor: move' class='btn btn-default' href='#' data-toggle='tooltip'" +
                    " title='Mover' data-original-title='Mover' ><i class='fa fa-arrows-v'></i> </a>  </td>" +
                    "</tr>"
                    );

//            $('.unidad_select').off('change');
//            $('.unidad_select').on('change', function () {
//                var nombre = $(this).find('option:selected').text();
//                var id = $(this).attr('data-row');
//                $('#unidad[' + id + ']').val(1);
//                if (nombre == 'UNIDAD')
//                    $('input[id="unidad[' + id + ']"]').val(1);
//                if (nombre == 'DOCENA')
//                    $('input[id="unidad[' + id + ']"]').val(12);
//
//            });
//
//            $('.unidad_select').trigger('change');


            unidadcount++;


            $(".precio_unitario").keyup(function () {
                var row = $(this).attr('data-row');
                var precio_venta = $("#precio_venta" + row);
                var unidades = $("#unidad\\[" + row + "\\]").val();

                precio_venta.val(parseFloat(unidades * $(this).val()).toFixed(<?= $numero_decimales ?>));
            });

            $(".precio_venta").keyup(function () {
                var row = $(this).attr('data-row');
                var precio_unitario = $("#precio_unitario" + row);
                var unidades = $("#unidad\\[" + row + "\\]").val();


                precio_unitario.val(parseFloat($(this).val() / unidades).toFixed(<?= $numero_decimales ?>));
            });

            $('.unidades').keyup(function () {
                var row = $(this).attr('data-row');
                var unidades = parseFloat($(this).val());
                var precio_unitario = parseFloat($("#precio_unitario" + row).val());
                var precio_venta = $("#precio_venta" + row);

                if (precio_unitario != 0 && !isNaN(precio_unitario) && unidades != 0)
                    precio_venta.val(parseFloat(unidades * precio_unitario).toFixed(<?= $numero_decimales ?>));


            });
        }

        function actualizartabla(nuevo_precio) {

            var clase = "";
            for (var i = 0; i < $(".unidades").length; i++) {
                $("#unidad\\[" + i + "\\]").attr('onchange', 'nuevocostoporunidad(' + i + ')')
                $("#unidad\\[" + i + "\\]").attr('onKeyUp', 'nuevocostoporunidad(' + i + ')')

<?php
foreach ($precios as $precioo) {

    if ($precioo['mostrar_precio']) {

        if ($precioo['id_precio'] == 3) {
            ?>
                            clase = 'precio_unitario';
        <?php } elseif ($precioo['id_precio'] == 1) {
            ?>
                            clase = 'precio_venta';
        <?php } else {
            ?>
                            clase = 'precio_descuento';
        <?php } ?>
                        for (var j = 0; j < $("." + clase).length; j++) {

                            var valor = $("#unidad\\[" + i + "\\]").val() * nuevo_precio
                            $("#" + clase + i).val(Math.round(parseFloat(valor)))

                        }

        <?php
    }
}
?>
            }


//            $('.unidad_select').off('change');
//            $('.unidad_select').on('change', function () {
//                var nombre = $(this).find('option:selected').text();
//                var id = $(this).attr('data-row');
//                $('#unidad[' + id + ']').val(1);
//                if (nombre == 'UNIDAD')
//                    $('input[id="unidad[' + id + ']"]').val(1);
//                if (nombre == 'DOCENA')
//                    $('input[id="unidad[' + id + ']"]').val(12);
//
//
//            });

            $('.unidad_select').trigger('change');

            $(".precio_unitario").keyup(function () {
                var row = $(this).attr('data-row');
                var precio_venta = $("#precio_venta" + row);
                var unidades = $("#unidad\\[" + row + "\\]").val();

                precio_venta.val(parseFloat(unidades * $(this).val()).toFixed(<?= $numero_decimales ?>));
            });

            $(".precio_venta").keyup(function () {
                var row = $(this).attr('data-row');
                var precio_unitario = $("#precio_unitario" + row);
                var unidades = $("#unidad\\[" + row + "\\]").val();


                precio_unitario.val(parseFloat($(this).val() / unidades).toFixed(<?= $numero_decimales ?>));
            });

            $('.unidades').keyup(function () {
                var row = $(this).attr('data-row');
                var unidades = parseFloat($(this).val());
                var precio_unitario = parseFloat($("#precio_unitario" + row).val());
                var precio_venta = $("#precio_venta" + row);

                if (precio_unitario != 0 && !isNaN(precio_unitario) && unidades != 0)
                    precio_venta.val(parseFloat(unidades * precio_unitario).toFixed(<?= $numero_decimales ?>));


            });
        }

        function eliminarunidad(unidadcount) {
            // console.log(unidadcount);
            $("#trunidad" + unidadcount).remove();
            var count = 0;

            $("tr[id^='trunidad']").each(function () {
                $(this).attr('id', 'trunidad' + count);

                $("#trunidad" + count + " select[name^='medida']").attr('name', 'medida[' + count + ']');
                $("#trunidad" + count + " select[name^='medida']").attr('id', 'medida' + count + '');

                $("#trunidad" + count + " input[name^='unidad']").attr('name', 'unidad[' + count + ']');
                $("#trunidad" + count + " input[name^='unidad']").attr('id', 'unidad[' + count + ']');
                $("#trunidad" + count + " input[name^='unidad']").attr('data-row', count);

                var countprecio = 0;
                $("#trunidad" + count + " input[name^='precio_id_']").each(function () {
                    $(this).attr('name', 'precio_id_' + count + '[' + countprecio + ']');
                    countprecio++;
                });
                $("#trunidad" + count + " input[name^='precio_id_']").attr('id', 'precio_id' + count);

                var countprecio = 0;
                $("#trunidad" + count + " input[name^='precio_valor_']").each(function () {

                    var id_precios = $(this).attr('data-nombre_precio')

                    $(this).attr('id', id_precios + count);
                    $(this).attr('name', 'precio_valor_' + count + '[' + countprecio + ']');
                    countprecio++;
                });

                if ($('#trunidad' + count).find('.precio_unitario').length) {
                    $('#trunidad' + count).find('.precio_unitario').attr('id', 'precio_unitario' + count);
                } else if ($('#trunidad' + count).find('.precio_venta').length) {
                    $('#trunidad' + count).find('.precio_venta').attr('id', 'precio_venta' + count);
                } else if ($('#trunidad' + count).find('.precio_descuento').length) {
                    $('#trunidad' + count).find('.precio_descuento').attr('id', 'precio_descuento' + count);
                }

                $("#trunidad" + count + " input[name^='precio_valor_']").attr('data-row', count);


                $("#trunidad" + count + " a[id^='eliminar']").attr('id', 'eliminar' + count);
                $("#trunidad" + count + " a[id^='eliminar']").attr('onclick', 'eliminarunidad(' + count + ')');

                count++;
            })


//            $('.unidad_select').off('change');
//            $('.unidad_select').on('change', function () {
//                var nombre = $(this).find('option:selected').text();
//                var id = $(this).attr('data-row');
//                $('#unidad[' + id + ']').val(1);
//                if (nombre == 'UNIDAD')
//                    $('input[id="unidad[' + id + ']"]').val(1);
//                if (nombre == 'DOCENA')
//                    $('input[id="unidad[' + id + ']"]').val(12);
//
//
//            });
//
//            $('.unidad_select').trigger('change');

            $(".precio_unitario").keyup(function () {
                var row = $(this).attr('data-row');
                var precio_venta = $("#precio_venta" + row);
                var unidades = $("#unidad\\[" + row + "\\]").val();

                precio_venta.val(parseFloat(unidades * $(this).val()).toFixed(<?= $numero_decimales ?>));
            });

            $(".precio_venta").keyup(function () {
                var row = $(this).attr('data-row');
                var precio_unitario = $("#precio_unitario" + row);
                var unidades = $("#unidad\\[" + row + "\\]").val();


                precio_unitario.val(parseFloat($(this).val() / unidades).toFixed(<?= $numero_decimales ?>));
            });

            $('.unidades').keyup(function () {
                var row = $(this).attr('data-row');
                var unidades = parseFloat($(this).val());
                var precio_unitario = parseFloat($("#precio_unitario" + row).val());
                var precio_venta = $("#precio_venta" + row);

                if (precio_unitario != 0 && !isNaN(precio_unitario) && unidades != 0)
                    precio_venta.val(parseFloat(unidades * precio_unitario).toFixed(<?= $numero_decimales ?>));


            });
        }


    </script>
    <script src="<?php echo base_url() ?>recursos/js/pages/uiDraggable.js"></script>
    <script>

        var contador_img = 0
        var identificador = 0

        function asignar_identificador(identif) {
            identificador = identif;
        }

        function fileOnload(e) {
            var result = e.target.result;
            $('#imgSalida' + identificador).attr("src", result);

        }

        function asignar_imagen(con) {
            var input = $("#input_imagen" + con)
            if (input[0].files[0] && input[0].files[0]) {

                asignar_identificador(con)
                var reader = new FileReader();
                reader.onload = fileOnload;

                reader.readAsDataURL(input[0].files[0]);
            }

        }
        var primera_carga = false;
        var actualizar_columna_primero = false;


        $(document).ready(function () {


            $('.textarea-editor').wysihtml5({
                "font-styles": true, //Font styling, e.g. h1, h2, etc. Default true
                "emphasis": true, //Italics, bold, etc. Default true
                "lists": true, //(Un)ordered lists, e.g. Bullets, Numbers. Default true
                "html": false, //Button which allows you to edit the generated HTML. Default false
                "link": false, //Button to insert a link. Default true
                "image": false, //Button to insert an image. Default true,
                "color": false //Button to change color of font
            });

//            $('.unidad_select').off('change');
//            $('.unidad_select').on('change', function () {
//                var nombre = $(this).find('option:selected').text();
//                var id = $(this).attr('data-row');
//                $('#unidad[' + id + ']').val(1);
//                if (nombre == 'UNIDAD')
//                    $('input[id="unidad[' + id + ']"]').val(1);
//                if (nombre == 'DOCENA')
//                    $('input[id="unidad[' + id + ']"]').val(12);
//
//
//            });
//
//            $('.unidad_select').trigger('change');

            $(".precio_unitario").keyup(function () {
                var row = $(this).attr('data-row');
                var precio_venta = $("#precio_venta" + row);
                var unidades = $("#unidad\\[" + row + "\\]").val();

                precio_venta.val(parseFloat(unidades * $(this).val()).toFixed(<?= $numero_decimales ?>));
            });

            $(".precio_venta").keyup(function () {
                var row = $(this).attr('data-row');
                var precio_unitario = $("#precio_unitario" + row);
                var unidades = $("#unidad\\[" + row + "\\]").val();


                precio_unitario.val(parseFloat($(this).val() / unidades).toFixed(<?= $numero_decimales ?>));
            });

            $('.unidades').keyup(function () {
                var row = $(this).attr('data-row');
                var unidades = parseFloat($(this).val());
                var precio_unitario = parseFloat($("#precio_unitario" + row).val());
                var precio_venta = $("#precio_venta" + row);

                if (precio_unitario != 0 && !isNaN(precio_unitario) && unidades != 0)
                    precio_venta.val(parseFloat(unidades * precio_unitario).toFixed(<?= $numero_decimales ?>));

            });


            /////


            $("#producto_marca").chosen();
            $("#producto_linea").chosen();
            $("#producto_familia").chosen();
            $("#produto_grupo").chosen();
            $("#producto_proveedor").chosen();


            $("#agregar_img").on('click', function (e) {

                contador_img++;
                $("#row1").append('<div class="row"><div class="col-md-6"><div class="input-prepend input-append input-group">' +
                        '<span class="input-group-addon"><i class="fa fa-folder"></i> </span>' +
                        '<input type="file" onchange="asignar_imagen(' + contador_img + ')" class="form-control input_imagen" data-count="1" name="userfile[]" accept="image/*" id="input_imagen' + contador_img + '"></div>' +
                        '</div> <div class="col-md-2"><img id="imgSalida' + contador_img + '" src="" height="100" width="100"></div> </div>');
                e.preventDefault()
            })

            /* $("input[type=file]").live('change', function () {
             alert($(this).attr('data-count'))
             if (this.files && this.files[0]) {
             
             asignar_identificador($(this).attr('data-count'))
             var reader = new FileReader();
             reader.onload = fileOnload;
             
             reader.readAsDataURL(this.files[0]);
             }
             });*/
            $("#eliminar").click(function () {
<?php $prod_id = isset($producto['producto_id']) ? $producto['producto_id'] : null ?>
                $.ajax({
                    url: '<?php echo $ruta . 'producto/borrarimagen?id=' . $prod_id; ?>',
                    success: function (data) {
                        if (data == 'success') {

                            console.log('elimino');
                            $('#imgSalida').attr('src', '');
                        }
                    }
                })

            });

            UiDraggable.init();
            //$("select[id^='medida']").chosen({ allow_single_deselect: true, disable_search_threshold: 5, width:"100%" });




<?php if ($this->session->userdata('PRECIO_DE_VENTA') == "CALCULADO") { ?>
                calculo_inicial();

<?php } ?>

        });

        /*este borra la imagen de los productos, pero de la pestana IMAGEN*/
        function borrar_img(producto_id, nombre, id_div) {

            $.ajax({
                url: ruta + 'producto/eliminarimg',
                type: "post",
                dataType: "json",
                data: {'producto_id': producto_id, 'nombre': nombre},
                success: function (data) {

                    if (data.error == undefined) {

                        $("#div_imagen_producto" + id_div).remove()

                        var growlType = 'success';

                        $.bootstrapGrowl('<h4>' + data.success + '</h4>', {
                            type: growlType,
                            delay: 2500,
                            allow_dismiss: true
                        });
                    } else {
                        var growlType = 'warning';

                        $.bootstrapGrowl('<h4>' + data.error + '</h4>', {
                            type: growlType,
                            delay: 2500,
                            allow_dismiss: true
                        });

                    }

                },
                error: function () {

                    var growlType = 'warning';

                    $.bootstrapGrowl('<h4>Por favor comuniquese con soporte</h4>', {
                        type: growlType,
                        delay: 2500,
                        allow_dismiss: true
                    });
                }
            })

        }

        function buscar_flete() {
            /*busco el valor del flete*/
            var flete = 0;
            if ($("#flete_producto").val() != "") {
                return flete = $("#flete_producto").val();
            } else {
                return flete = 0;
            }
        }

        function calcularpreciocompra(i) {

            /*este metodo es invocado cada vez que se presiona sobre una tecla en los campos con clase precio_compra*/
            var inputseleccionado = $("#precio_compra" + i);
            var tasa = $("#tasa_id");
            var calculo = 0.00;
            /*este es un campo hiden en el que ya fue colocado el costo unitario al momento de cargar el documento*/
            var costounitario = $("#calculado_costo_unitario").val();
            var flete = 0;

            if (inputseleccionado.val() != "" && tasa.val() != "") {
                for (var j = 0; j < $('.precio_compra').length; j++) {

                    /*voy a entrar solo cuando el input sobre el que presione la tecla, es distinta del que estoy recorriendo en estos
                     * momentos*/
                    if ((inputseleccionado.attr('data-nombre') != $("#precio_compra" + j).attr('data-nombre'))) {

                        /*recuerden que aqui va a entrar sobre el que es distinto del que acabas de presionar la tecla, si presionaste sobre soles,
                         * va a entrar aqui solamente sobre el input de Dolares*/
                        if (inputseleccionado.attr('data-nombre') == 'Soles') {
                            /*si el input sobre el que presione la tecla, es igual a Soles hago el calculo*/
                            calculo = parseFloat(inputseleccionado.val() / tasa.val()).toFixed(<?= $numero_decimales ?>)
                            costounitario = inputseleccionado.val();

                            /*le asigno el valor del nuevo costo unitario a este campo hidden*/
                            $("#calculado_costo_unitario").val(costounitario)
                        } else if (inputseleccionado.attr('data-nombre') == 'Dolares') {
                            /*si es Dolares, busco el costo unitario actual*/
                            costounitario = $('input[type=number].precio_compra[data-nombre="Soles"]').val()

                            /*hago el calculo*/
                            calculo = parseFloat(inputseleccionado.val() * tasa.val()).toFixed(<?= $numero_decimales ?>)

                            /*le asigno el valor del calculo a este campo hidden*/
                            $("#calculado_costo_unitario").val(calculo)

                        }
                        $("#precio_compra" + j).val(calculo)

                        /*busco el valor del flete*/
                        flete = buscar_flete();
                        /*calculo el nuevo precio de venta*/
                        var nuevo_precio = calcular_nuevo_precio(costounitario, flete)
                    }
                }
                actualizartabla(nuevo_precio)

            }
        }

        function calcular_nuevo_precio(costounitario, flete) {
            var nuevo_precio = 0;
            /*busco el margen de utilidad y calculo el nuevo precio de venta*/
            if ($("#margen_utilidad_producto").val() != "" && $("#margen_utilidad_producto").val() > 0.00) {

                $("#nuevo_precio_venta").val(Math.round(parseFloat((costounitario / $("#margen_utilidad_producto").val()) + parseFloat(flete))))
            } else {

                $("#nuevo_precio_venta").val(Math.round(parseFloat(costounitario) + parseFloat(flete)))

            }
            return nuevo_precio = $("#nuevo_precio_venta").val()
        }


        function calculo_inicial() {

            var tasa = $("#tasa_id");
            var calculo = 0.00;
            var costounitario = $("#calculado_costo_unitario").val();


            var flete = 0;
            if (tasa.val() != "" && costounitario != "") {
                for (var j = 0; j < $('.precio_compra').length; j++) {

                    if ($("#precio_compra" + j).attr('data-nombre') == 'Soles') {
                        costounitario = $("#precio_compra" + j).val();

                        calculo = parseFloat(costounitario / tasa.val()).toFixed(<?= $numero_decimales ?>)
                        if (primera_carga == true) {

                        } else {

                            setTimeout(function () {

                                $('input[type=number].precio_compra[data-nombre="Dolares"]').val(calculo)
                            }, 1);
                        }
                        $("#calculado_costo_unitario").val(costounitario)

                    } else if ($("#precio_compra" + j).attr('data-nombre') == 'Dolares') {

                        calculo = parseFloat(costounitario / tasa.val()).toFixed(<?= $numero_decimales ?>)
                        $("#precio_compra" + j).val(calculo)
                    }

                    flete = buscar_flete();

                    if (primera_carga == false) {

                    } else {

                        var nuevo_precio = calcular_nuevo_precio(costounitario, flete)

                    }

                }

                if (primera_carga == false) {
                    $("#nuevo_precio_venta").val('<?= isset($precio_venta_producto) ? $precio_venta_producto : 0.00 ?>');
                    primera_carga = true;

                }


                if (actualizar_columna_primero == true) {


                    actualizartabla(nuevo_precio)
                }
                actualizar_columna_primero = true;

            }

        }
    </script>
