<style type="text/css">
    table {
        width: 100%;
    }

    thead, th {
        background-color: #CED6DB;
        color: #000;
    }
    tbody tr{
        border-color: #111;
    }

    .title {
        font-weight: bold;
        text-align: center;
        font-size: 1.5em;
        color: #000;
    }
    .trtbody{
        border-color: #000044;

    }
</style>

<table>

    <tbody>
    <tr>
        <td style="font-weight: bold;text-align: left; font-size:1.5em; color: #000;"
            colspan="5"><?php echo $this->session->userdata('EMPRESA_NOMBRE'); ?>
        </td>
    </tr>
    <tr>
        <td style="font-weight: bold;text-align: center; font-size:1.5em; color: #000;"
            colspan="5">Lista de Precios </td>
    </tr>
    <tr>
        <td width="18%" style="font-weight: bold;">Fecha Emisi&oacute;n:</td>
        <td width="25%"><?php echo date("d-m-Y H:i:s"); ?></td>
        <td></td>
        <td></td>
        <td></td>

    </tr>

    </tbody>
</table>
<table border="1" bordercolor="#FFFFFF" cellpadding="1" cellspacing="0" >
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