<?php if ($flag == 0): ?>
    <div class="modal-dialog" style="width: 60%">
    <div class="modal-content">
    <div class="modal-header">
        <h3>Visualizar Venta</h3>
    </div>
    <div class="modal-body">
    <div class="row-fluid force-margin">

    <div class="row-fluid">
    <div class="block">

    <div class="box-content box-nomargin">
    <div id="lstTabla" class="table-responsive">

<?php endif; ?>
    <table id="table" class="table dataTable dataTables_filter table-striped">
        <thead>
        <th>Cod. Producto</th>
        <th>Producto</th>
        <th>UM</th>
        <th>Cantidad</th>
        <th>Moneda</th>
        <th>Precio</th>
        <th>Subtotal</th>
        </thead>

        <tbody>
        <?php foreach ($ventas as $venta): ?>
            <tr>
                <td align="center"><?= $venta['id_producto'] ?></td>
                <td align="center"><?= $venta['producto_nombre'] ?></td>
                <td align="center"><?= $venta['nombre_unidad'] ?></td>
                <td align="center"><?= number_format($venta['cantidad'], 0) ?></td>
                <td align="center"><?= $venta['nombre_moneda'] ?></td>
                <td align="center"><?= $venta['simbolo'] . " " . number_format($venta['precio'], 2) ?></td>
                <td align="center"><?= $venta['simbolo'] . " " . number_format($venta['detalle_importe'], 2) ?></td>
            </tr>
        <?php endforeach ?>


        </tbody>
    </table>
<?php if ($flag == 0): ?>
    </div>
    </div>
    </div>
    </div>

    </div>

    </div>

    <div class="modal-footer">
        <div class="row">
            <div class="text-right">
                <div class="col-md-10">

                </div>
                <div class="col-md-2">
                    <input type="reset" class='btn btn-default' value="Cerrar" data-dismiss="modal">
                </div>
            </div>

        </div>
    </div>
    </div>
    </div>
<?php endif; ?>