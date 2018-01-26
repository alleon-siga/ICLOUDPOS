<style type="text/css">
    .tableStyle td {
        font-size: 9px !important;
    }

    .table > tbody > tr:hover {
        color: #333 !important;
    }

    .tableStyle th {
        font-size: 10px !important;
    }

    .input_table, .input_table_readonly {
        width: 100%;
        height: 25px;
        font-size: 10px !important;
        border: 1px solid #DEDEDE;
        text-align: center;
    }

    .input_table_readonly {
        background-color: #DEDEDE;
    }

    .table > tbody > tr > td {
        padding: 1px !important;
    }

    .table > thead > tr > th {
        padding: 4px 1px !important;
    }

    .panel_button {
        padding: 3px 5px;
    }

    .panel_button:hover {
        border: 1px solid #DEDEDE;
    }
</style>

<?php if (count($productos) > 0): ?>
    <?php foreach ($productos as $producto): ?>
        <?php if(!isset($producto->nombre)) continue;?>
        <?php foreach ($producto->series as $key => $rangos): ?>
            <table class="table table-striped table-bordered tableStyle">
                <thead>
                <tr>
                    <th>Plantilla</th>
                    <th>Serie</th>
                    <?php foreach ($rangos as $key_r => $val): ?>
                        <th><?= $key_r ?></th>
                    <?php endforeach; ?>
                    <th>Precio Min.</th>
                    <th>Precio Max.</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td><?= $producto->nombre ?></td>
                    <td><?= $key ?></td>
                    <?php foreach ($rangos as $val): ?>
                        <td><?= $val ?></td>
                    <?php endforeach; ?>
                    <td><?= number_format($producto->precio_min, 2) ?></td>
                    <td><?= $producto->precio_max ?></td>
                </tr>
                </tbody>
            </table>
        <?php endforeach; ?>
    <?php endforeach; ?>
<?php else: ?>
    <h3>No se encontro ningun resultado.</h3>
<?php endif; ?>




