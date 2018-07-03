<table class="table">
    <tr>
        <th></th>
        <th>ID</th>
        <th>Fecha</th>
        <th>Documento</th>
        <th>Numero</th>
        <th>Afectado</th>
        <th>Total</th>
    </tr>
    <?php foreach ($gastos as $gasto): ?>
        <tr>
            <td><input type="checkbox" class="gastos_check" data-id="<?= $gasto->id_gastos ?>"></td>
            <td><?= $gasto->id_gastos ?></td>
            <td><?= date('d/m/Y', strtotime($gasto->fecha)) ?></td>
            <td><?= $gasto->des_doc ?></td>
            <td><?= $gasto->serie . '-' . $gasto->numero ?></td>
            <td><?php
                if ($gasto->proveedor_id != NULL) {
                    echo 'PROVEEDOR: ' . $gasto->proveedor_nombre;
                }
                elseif ($gasto->usuario_id != NULL) {
                    echo 'TRABAJADOR: ' . $gasto->nombre;
                }
                ?>
            </td>
            <td id="gasto_total_<?= $gasto->id_gastos ?>"><?= $gasto->total ?></td>
        </tr>
    <?php endforeach; ?>
</table>
<script>
    $(function () {

        for (var i = 0; i < lst_gastos.length; i++) {
            $('input[data-id="' + lst_gastos[i].id + '"]').prop('checked', true);
        }
    });
</script>