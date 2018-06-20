<?php
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=proveedor.xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<?php $term = diccionarioTermino() ?>
<h4 style="text-align: center; margin: 0;">Proveedor</h4>
<h5 style="margin: 0;">EMPRESA: <?= valueOption('EMPRESA_NOMBRE') ?></h5>
<h5 style="margin: 0;">DIRECCI&Oacute;N: <?= $local_direccion ?></h5>
<h5 style="margin: 0;">SUCURSAL: <?= $local_nombre ?></h5>
<table border="1">
    <thead>
        <tr>
            <th>ID</th>
            <th><?= $term[0]->valor.' / '.$term[1]->valor ?></th>
            <th>Razón Social</th>
            <th>Direcci&oacute;n Fiscal</th>
            <th>Tel&eacute;fono Empresa</th>
            <th>Correo</th>
            <th>Contacto</th>
            <th>Tel&eacute;fono contacto</th>
        </tr>
    </thead>
    <tbody>
    <?php if (count($lists) > 0) {
        foreach ($lists as $proveedor) {
            ?>
            <tr id=<?= $proveedor['id_proveedor'] ?>>
                <td class="center"><?= $proveedor['id_proveedor'] ?></td>
                <td><?= $proveedor['proveedor_ruc'] ?></td>
                <td><?= $proveedor['proveedor_nombre'] ?></td>
                <td><?= $proveedor['proveedor_direccion1'] ?></td>
                <td><?= $proveedor['proveedor_telefono1'] ?></td>
                <td><?= $proveedor['proveedor_email'] ?></td>
                <td><?= $proveedor['proveedor_contacto'] ?></td>
                <td><?= $proveedor['proveedor_telefono2'] ?></td>
            </tr>
        <?php }
    } ?>
    </tbody>
</table>
