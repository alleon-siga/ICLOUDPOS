<?php $ruta = base_url(); ?>
<!--<script src="<?php echo $ruta; ?>recursos/js/custom.js"></script>-->
<table class='table table-striped dataTable table-bordered no-footer' id="lstPagP" name="lstPagP">
    <thead>
    <tr>
        <th><?php echo getCodigoNombre() ?></th>
        <th>Nombre Prod.</th>
        <th>UM</th>
        <th>Cantidad</th>
        <th>Almacen Origen</th>
        <th>Almacen Destino</th>
        <th>Usuario</th>
        <th>Fecha</th>
        <?php //if($local=="TODOS"){?>
            <!--<th>Local</th>-->
        <?php //} ?>
    </tr>
    </thead>
    <tbody id="columnas">

    <?php

    foreach ($movimientos as $arreglo): ?>
        <tr>
            <td style="text-align: center"><span
                    style="display: none"><?= date('YmdHi', strtotime($arreglo->date)) ?></span><?= $arreglo->id ?></span></td>
            <td style="text-align: center"><?= $arreglo->producto_nombre ?></td>
            <td style="text-align: center"><?= $arreglo->um ?></td>
            <td style="text-align: center"><?= $arreglo->cantidad ?></td>
            <td style="text-align: center"><?php  if($arreglo->io=="2"){ echo $arreglo->local_nombre; }else{ echo $arreglo->ref_val; } ?></td>
            <td style="text-align: center"><?php  if($arreglo->io=="1"){ echo $arreglo->local_nombre; }else{ echo $arreglo->ref_val; } ?></td>
            <td style="text-align: center"><?= $arreglo->nombre ?></td>
            <td style="text-align: center"><?= date('d-m-Y H:i', strtotime($arreglo->fecha)) ?></td>
            <?php //if($local=="TODOS"){?>
                <!--<td style="text-align: center;"><?php //echo $arreglo->localuno; ?></td>-->
            <?php //} ?> </tr>
    <?php endforeach; ?>


    </tbody>
</table>
<!--- ----------------- -->
<script src="<?php echo $ruta ?>recursos/js/pages/tablesDatatables.js"></script>
<script type="text/javascript">


    $(document).ready(function () {
        $('#cargando_modal').modal('hide')
        TablesDatatables.init(5);

        $("#cerrar_pago_modal").on('click', function (){

            $("#pago_modal").modal('hide')
        })

    });




</script>