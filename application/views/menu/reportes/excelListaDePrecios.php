<?php
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=ExcelListaDePrecios.xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<table >
    <tr>
        <td style="font-weight: bold;text-align: left; font-size:1.5em; color: #000;"
            colspan="5"><?php echo $this->session->userdata('EMPRESA_NOMBRE'); ?>
        </td>
    </tr>
    <tr>
        <td width="12%" colspan="10" style="font-weight: bold;text-align: center; font-size:1.5em; color: #000;"
            >Lista de Precios</td>
        <td width="12%">&nbsp;&nbsp;</td>

        <td width="12%">&nbsp;&nbsp;</td>
        <td width="12%">&nbsp;&nbsp;</td>
        <td width="12%">&nbsp;&nbsp;</td>
        <td width="12%">&nbsp;&nbsp;</td>
        <td width="12%">&nbsp;&nbsp;</td>
        <td width="12%">&nbsp;&nbsp;</td>
        <td width="12%">&nbsp;&nbsp;</td>
    </tr>

    <tr>
        <td width="18%" colspan="5" style="font-weight: bold;">Fecha Emisi&oacute;n: <?php echo date("d-m-Y H:i:s"); ?></td>
        <td width="25%"></td>
        <td width="12%">&nbsp;&nbsp;</td>
        <td width="12%">&nbsp;&nbsp;</td>
        <td width="12%">&nbsp;&nbsp;</td>
        <td width="7%">&nbsp;&nbsp;</td>
        <td width="5%">&nbsp;&nbsp;</td>
        <td width="5%">&nbsp;&nbsp;</td>
        <td width="5%">&nbsp;&nbsp;</td>


    </tr>
    <tr>
        <td colspan="8"></td>
    </tr>
</table>
<table border="1" bordercolor="#000" cellpadding="1" cellspacing="0">
    <thead>
    <tr>

        <th>ID Producto</th>
        <th>Nombre</th>
        <th>Grupo</th>
        <?php
        $bandera="LISTA_PRECIOS";
        foreach($precios as $precio){ ?>

            <th class=""><?= $precio['nombre_precio'] ?></th>
        <?php } ?>
    </tr>
    </thead>
    <tbody>
    <?php

    if(count($lstProducto)>0) {
        foreach ($lstProducto as $row) {
            ?>
            <tr>
                <td><?= $row['producto_id'] ?></td>
                <td><?= $row['producto_nombre'] ?></td>
                <td><?= $row['nombre_grupo'] ?></td>
                <?php

                foreach ($precios as $precio) {
                    echo "<td>";
                    foreach ($productos as $producto) {

                        if ($row['producto_id'] == $producto['id_producto']) {

                            if ($producto['id_precio'] == $precio['id_precio'] and $producto['id_grupo']==$row['id_grupo']) {

                                echo $producto['nombre_unidad'] . ": " . number_format($producto['precio'], 2);
                                echo "<br>";

                            }

                        }

                    }

                    echo "</td>";
                }

                ?>
            </tr>
        <?php


        }
    }
    ?>
    </tbody>
</table>