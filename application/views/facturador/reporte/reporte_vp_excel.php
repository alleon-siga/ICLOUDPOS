<?php
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=producto_vendido.xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<h4 style="text-align: center; margin: 0;">Ventas Por Productos: Detalle</h4>
<h4 style="text-align: center; margin: 0;">Desde <?= date('d/m/Y', strtotime($fecha_ini)) ?>
    al <?= date('d/m/Y', strtotime($fecha_fin)) ?></h4>

<h5 style="margin: 0;">EMPRESA: <?= valueOption('EMPRESA_NOMBRE') ?></h5>
<h5 style="margin: 0;">DIRECCI&Oacute;N: <?= $local_direccion ?></h5>
<h5 style="margin: 0;">SUCURSAL: <?= $local_nombre ?></h5>
<table class="table">
    <thead>
        <tr style="border-color: transparent !important;">
            <th colspan="3" class="thvacio thblack"></th>
            <th colspan="3" class="thblack">Cant. x Tipo de Doc. </th>
            <th class="thvacio thblack"></th>
            <th colspan="3" class="thblack">Montos x Documentos</th>
            <th class="thvacio thblack"></th>
        </tr>
        <tr>
            <th class="thblack">Codigo</th>
            <th class="thblack">Producto</th>                
            <th class="thblack">Marca</th>
            <th class="CellWithComment thblack">NC  <span class="CellComment">Nota de Compra</span></th>
            <th class="CellWithComment thblack">BO  <span class="CellComment">Boleta</span></th>
            <th class="CellWithComment thblack">Fa <span class="CellComment">Factura</span></th>
            <th class="thblack">Cantidad Total</th>
            <th class="CellWithComment thblack">NC  <span class="CellComment">Nota de Compra</span></th>
            <th class="CellWithComment thblack">BO  <span class="CellComment">Boleta</span></th>
            <th class="CellWithComment thblack">Fa <span class="CellComment">Factura</span></th>
            <th class="thblack">Total</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($lists as $ingreso):
            ?>
            <tr class="trblack">
                <td ><?= $ingreso->codigo_producto ?></td>
                <td ><?= $ingreso->nombre_producto ?></td>
                <td ><?= $ingreso->marca_producto!=""?$ingreso->marca_producto:"SIN MARCA" ?></td>
                <td ><?= $ingreso->ven_nv!=0?number_format($ingreso->ven_nv,0):number_format(0,0)?></td>
                <td ><?= $ingreso->ven_bol ?></td>
                <td ><?=  $ingreso->ven_fac ?></td>
                <td ><?= $ingreso->ven_total ?> </td>
                <td ><?= $ingreso->ven_nv_t!=0?number_format($ingreso->ven_nv_t,2):number_format(0,2) ?></td>
                <td > <?= $ingreso->ven_bol_t!=0?number_format($ingreso->ven_bol_t,2):number_format($ingreso->ven_bol_t,2) ?></td>
                <td ><?= $ingreso->ven_fac_t!=0?number_format($ingreso->ven_fac_t,2):number_format($ingreso->ven_fac_t,2) ?></td>
                <td ><?= $ingreso->ven_tot_t!=0?number_format($ingreso->ven_tot_t,2):number_format($ingreso->ven_tot_t,2) ?></td>   
            </tr>
            <?php endforeach; ?>
    </tbody>

</table>
