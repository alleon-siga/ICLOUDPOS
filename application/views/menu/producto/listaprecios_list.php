<?php $ruta = base_url(); ?>
<style>
    #tabla_lista_precios_filter {
        display: none;
    }
</style>


<table class='table table-striped table-bordered' id="tabla_lista_precios">
    <thead>
    <tr>
        <th><?= getCodigoNombre() ?></th>
        <th>Nombre</th>
        <th>Imagen</th>
        <th>Detalles</th>
        <th>Embalaje</th>
        <th>Precio Unitario</th>
        <th>Precio Venta</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($lstProducto as $producto): ?>
        <tr id="tr_id_<?= $producto['producto_id'] ?>"
            class="tr_list" data-stock_min="<?= $producto['stock_min'] ?>"
            data-id="<?= $producto['producto_id'] ?>">
            <td><?= getCodigoValue(sumCod($producto['producto_id']), $producto['producto_codigo_interno']) ?></td>
            <td>
                <?= $producto['producto_nombre'] ?><br>
                <span style="font-size: 9px; color: #ADADAD; text-transform: uppercase;">
                    <?php $barra = $barra_activa->activo == 1 && $producto['barra'] != "" ? "CB: ".$producto['barra']."<br>" : ""?>
                    <?= $barra; ?>
                    <?= $producto['descripcion']?>
                </span>
            </td>
            <td>
                <?php $img = get_imagen_producto($producto['producto_id']); ?>
                <?php $src_local = $ruta . "/uploads/" . $producto['producto_id'] . "/"; ?>
                <?php if (count($img) > 0): ?>
                    <div>
                        <img src="<?= $src_local . $img[0] ?>" height="100" width="100"/><br>
                        <a href="#" onclick="ver_imagenes(<?= $producto['producto_id'] ?>)">Ver
                            mas...</a>
                    </div>
                <?php endif; ?>
            </td>
            <td id="detalle_<?= $producto['producto_id'] ?>"></td>
            <td id="embalaje_<?= $producto['producto_id'] ?>"></td>
            <td id="precio_unitario_<?= $producto['producto_id'] ?>"></td>
            <td id="precio_venta_<?= $producto['producto_id'] ?>"></td>
        </tr>
    <?php endforeach;; ?>
    </tbody>
</table>

<!--
<a href="<?= $ruta ?>producto/pdf" id="generarpdf" class="btn  btn-default btn-lg"
   data-toggle="tooltip" title="Exportar a PDF" data-original-title="fa fa-file-pdf-o"><i
        class="fa fa-file-pdf-o fa-fw"></i></a>
<a href="<?= $ruta ?>producto/excel" class="btn btn-default btn-lg"
   data-toggle="tooltip"
   title="Exportar a Excel" data-original-title="fa fa-file-excel-o"><i
        class="fa fa-file-excel-o fa-fw"></i></a>
-->

<script src="<?php echo $ruta; ?>recursos/js/Validacion.js"></script>

<script>
    function ver_imagenes(id) {
        $("#cargando_modal").modal('show');
        $("#ver_imagenes_producto").load('<?= $ruta ?>producto/ver_imagen/' + id);
        $("#cargando_modal").modal('hide');
        $('#ver_imagenes_producto').modal('show');


    }

    $(function () {


        $('#ver_imagenes_producto').on('show.bs.modal', function (e) {

            jQuery.removeData(jQuery('#img_02'), 'elevateZoom');//remove zoom instance from image
            jQuery('.zoomContainer').remove()

        });

        jQuery('#ver_imagenes_producto').on('hidden.bs.modal', function (e) {

            jQuery.removeData(jQuery('#img_02'), 'elevateZoom');//remove zoom instance from image
            jQuery('.zoomContainer').remove();// remove zoom container from DOM
        });

        



    });

</script>
