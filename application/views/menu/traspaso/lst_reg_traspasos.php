<?php $ruta = base_url(); ?>
<!--<script src="<?php echo $ruta; ?>recursos/js/custom.js"></script>-->
<table class='table table-striped dataTable table-bordered no-footer tableStyle' id="lstPagP" name="lstPagP">
    <thead>
    <tr>
        <th><?php echo getCodigoNombre() ?></th>
        <th>Tipo</th>
        <th>Almacen Origen</th>
        <th>Almacen Destino</th>
        <th>Usuario</th>
        <th>Fecha</th>
        <th>Acciones</th>
    </tr>
    </thead>
    <tbody id="columnas">
    <?php
    foreach ($movimientos as $arreglo): ?>
        <tr>
            <td style="text-align: center"><?= $arreglo->id ?></td>
            <td style="text-align: center"><?= $arreglo->ref_id; ?></td>
            <td style="text-align: center"><?= $arreglo->origen; ?></td>
            <td style="text-align: center"><?= $arreglo->destino; ?></td>
            <td style="text-align: center"><?= $arreglo->username ?></td>
            <td style="text-align: center"><?= date('d-m-Y H:i', strtotime($arreglo->fecha)) ?></td>
            <td style="text-align: center">
                <a class="btn btn-default" data-toggle="tooltip" style="margin-right: 5px;" title="Ver" data-original-title="Ver" href="#" onclick="ver('<?= $arreglo->id ?>');"><i class="fa fa-search"></i></a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<div class="modal fade" id="verModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"></div>
<iframe style="display: block;" id="imprimir_frame" src="" frameborder="YES" height="0" width="0" border="0" scrolling="no"></iframe>
<!--- ----------------- -->
<script type="text/javascript">
    var url = '<?= base_url() ?>';
</script>
<script src="<?php echo $ruta ?>recursos/js/pages/tablesDatatables.js"></script>
<script src="<?php echo $ruta ?>recursos/js/lst_reg_traspasos.js"></script>