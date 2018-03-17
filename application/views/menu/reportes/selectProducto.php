<select id="producto_id" name="producto_id" multiple="multiple">
 <?php foreach ($productos as $producto): ?>
        <option value="<?= $producto->producto_id ?>"
                data-impuesto="<?= $producto->porcentaje_impuesto ?>">
            <?php $barra = $barra_activa->activo == 1 && $producto->barra != "" ? "CB: " . $producto->barra : "" ?>
            <?= getCodigoValue($producto->producto_id, $producto->codigo) . ' - ' . $producto->producto_nombre . " " . $barra ?>
        </option>
    <?php endforeach; ?>
</select>


<!-- /.modal-dialog -->
<script type="text/javascript">
    // Filtro en select
    $("#producto_id").multipleSelect({
        filter: true,
        width: '100%'
    });
 </script>