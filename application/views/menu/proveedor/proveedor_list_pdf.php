<?php $term = diccionarioTermino() ?>
<style type="text/css">
    table td {
        width: 100%;
        border: #e1e1e1 1px solid;
        font-size: 9px;
    }

    thead, th {
        background: #585858;
        border: #111 1px solid;
        color: #fff;
        font-size: 10px;
    }

    h4, h5 {
        margin: 0px;
    }

    table tfoot tr td {
        font-weight: bold;
    }
</style>
<h4 style="text-align: center;">Proveedor</h4>
<h5>EMPRESA: <?= valueOption('EMPRESA_NOMBRE') ?></h5>
<h5>DIRECCI&Oacute;N: <?= $local_direccion ?></h5>
<h5>SUCURSAL: <?= $local_nombre ?></h5>
<table>
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
