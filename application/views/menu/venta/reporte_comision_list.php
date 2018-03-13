<?php $ruta = base_url(); ?>
<?php $md = get_moneda_defecto() ?>
<div class="table-responsive">
    <table class='table table-striped dataTable table-bordered no-footer tableStyle' style="overflow:scroll">
        <thead>
        <tr>
            <th>ID</th>
            <th>Vendedor</th>
            <th>Total Venta</th>
            <th>Comision</th>
            <th>Importe Comision</th>
        </tr>
        </thead>
        <tbody>

            <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td><?= $usuario->vendedor_id ?></td>
                    <td><?= $usuario->vendedor_nombre ?></td>
                    <td><?= $moneda->simbolo.' '.number_format($usuario->total_venta, 2) ?></td>
                    <td><?= number_format($usuario->comision, 2) ?></td>
                    <td><?= $moneda->simbolo.' '.number_format($usuario->importe_comision, 2) ?></td>
                </tr>
            <?php endforeach ?>

        </tbody>
    </table>
</div>

<script type="text/javascript">
    $(function () {

//        TablesDatatables.init(1);

    });
</script>