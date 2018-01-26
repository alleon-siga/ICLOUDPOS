<?php $ruta = base_url(); ?>


<div id="inentariocontainer" style="display: none;"></div>
<div id="stock_actual" style="display: none;"></div>
<input type="hidden" id="producto_cualidad" value="">
<input type="hidden" id="devolver" name="devolver" value="<?php echo isset($devolver) ? 'true' : 'false' ?>">
<input type="hidden" id="idlocal" name="idlocal" value="<?= $this->session->userdata('id_local'); ?>">
<input type="text" id="hdTasaSoles" name="hdTasaSoles" style="visibility: hidden">


<script>
    var countproducto = 0;

</script>
<ul class="breadcrumb breadcrumb-top">
    <li>Ventas</li>
    <li><a href="">Realizar venta</a></li>
</ul>
<!-- END Datatables Header -->
<div class="block">

    <!-- Progress Bars Wizard Title -->
    <div class="block-title">
        <h2>Realizar Venta</h2>
    </div>


    <form method="post" id="frmVenta" action="#" class=''>

        <input type="hidden" name="diascondicionpagoinput" id="diascondicionpagoinput"
               value="<?php if (isset($venta[0]['id_condiciones'])) echo $venta[0]['id_condiciones'] ?>">
        <input type="hidden" name="idventa" id="idventa"
               value="<?php if (isset($venta[0]['venta_id'])) echo $venta[0]['venta_id'] ?>">

        <div class="row">
            <?php if (count($locales) == 1): ?>
                <div style="display: none;">
                    <select name="id_local" id="id_local" class='form-control'>
                        <option selected
                                value="<?= $locales[0]['int_local_id'] ?>"><?= $locales[0]['local_nombre'] ?></option>
                    </select>
                </div>
            <?php else: ?>
                <div class="form-group">
                    <div class="col-md-2">
                        <label for="cboPersonal" class="control-label panel-admin-text">Ubicaci&oacute;n:</label>

                    </div>
                    <div class="col-md-6">
                        <select name="id_local" id="id_local" class='form-control'>
                            <?php foreach ($locales as $local):
                                if ($local_selected == $local['int_local_id']): ?>
                                    <option selected
                                            value="<?= $local['int_local_id'] ?>"><?= $local['local_nombre'] ?></option>
                                <?php else: ?>
                                    <option value="<?= $local['int_local_id'] ?>"><?= $local['local_nombre'] ?></option>
                                <?php endif; ?>
                            <?php endforeach;; ?>
                        </select>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="row">
            <div class="form-group">
                <div class="col-md-2">

                    <label for="cliente" class="control-label panel-admin-text">Cliente:</label>
                </div>
                <div class="col-md-6">
                    <input type="hidden" id="devolver_activo" data-cantidad="0"
                           value="<?php echo isset($devolver) ? "1" : "0" ?>">
                    <?php if (isset($devolver)) { ?>
                        <input type="hidden" name="id_cliente" value="<?php echo $venta[0]['cliente_id'] ?>">
                    <?php } ?>
                    <select name="id_cliente" id="id_cliente" class='form-control'
                            required="true" <?php if (isset($devolver)) echo 'disabled' ?>>
                        <option value="">Seleccione</option>
                        <?php if (count($clientes) > 0): ?>
                            <?php foreach ($clientes as $cl): ?>
                                <option
                                    value="<?php echo $cl['id_cliente']; ?>" <?php if ((isset($venta[0]['cliente_id']) and $venta[0]['cliente_id'] == $cl['id_cliente']) or (!isset($venta[0]['cliente_id']) && $cl['razon_social'] == 'Cliente Frecuente'))
                                    echo 'selected' ?>><?php echo $cl['razon_social']; ?></option>
                            <?php endforeach; ?>
                        <?php else : ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="col-md-1">
                    <a class="btn btn-default" href="#" onclick="agregarCliente()"><i class="fa fa-plus-circle"></i>
                        Nuevo Cliente</a>
                </div>
            </div>
        </div>


        <div class="row panel">
            <div class="form-group">
                <div class="col-md-2">

                    <label for="cboTipDoc" class="control-label panel-admin-text">Producto:</label>
                </div>

                <?php if ($codigo_barra_activo): ?>
                    <div class="col-md-2">
                        <input type="text" placeholder="C&oacute;digo de Barra:" name="barra" id="barra" class="form-control enter" enter-id="1"
                            >
                    </div>
                <?php endif; ?>
                <div class="col-md-<?php if ($codigo_barra_activo){ echo 4; }else{ echo 6; }?>  ">

                    <input type="hidden" id="serie_activa" value="<?php echo getProductoSerie() ?>">
                    <select class="form-control prueba enter"
                            enter-id="2" <?php echo isset($devolver) ? "disabled" : NULL ?>
                            id="selectproductos">
                        <option value="">Seleccione</option>
                        <?php foreach ($productos as $producto) { ?>
                            <option
                                data-producto="<?php echo getCodigoValue(sumCod($producto['producto_id']), $producto['producto_codigo_interno']) . " - " . $producto['producto_nombre'] ?>"
                                value="<?= $producto['producto_id'] ?>">
                                <?php echo getCodigoValue(sumCod($producto['producto_id']), $producto['producto_codigo_interno']) . " - " ; echo valueOption('BUSCAR_PRODUCTOS_VENTA','NOMBRE')=="NOMBRE"? $producto['producto_nombre'] : $producto['producto_descripcion']==null ? $producto['producto_nombre'] : $producto['producto_descripcion'] ?>
                            </option>
                        <?php } ?>
                    </select>

                </div>
                <div class="col-md-2">


                    <button type="button" id="refrescarstock" class="btn btn-default"><i class="fa fa-refresh"></i>
                        Refrescar
                    </button>


                </div>



            </div>


        </div>




