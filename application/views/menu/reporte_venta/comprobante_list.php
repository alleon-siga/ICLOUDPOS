<?php $ruta = base_url(); ?>
<?php $md = get_moneda_defecto() ?>
<div class="table-responsive">
    <table class='table table-striped dataTable table-bordered no-footer tableStyle' style="overflow:scroll">
        <thead>
        <tr>
            <th>RUC</th>
            <th>Documento</th>
            <th>Cliente</th>
            <th>No. Comprobante</th>
            <th>Tipo Comprobante</th>
            <th>Impuesto</th>
            <th>Total</th>
        </tr>
        </thead>
        <tbody>

            <?php foreach ($lists as $list): ?>
                <tr>
                    <td><?= $list->ruc ?></td>
                    <?php
                        $doc = 'NP ';
                        if($list->documento_id == 1) $doc = 'FA ';
                        if($list->documento_id == 3) $doc = 'BO ';
                    ?>
                    <td><?= $doc.$list->serie.'-'.sumCod($list->numero, 6) ?></td>
                    <td><?= $list->cliente_nombre ?></td>
                    <td><?= $list->comprobante_numero ?></td>
                    <td><?= $list->comprobante_nombre ?></td>
                    <td><?= $moneda->simbolo.' '.number_format($list->impuesto, 2) ?></td>
                    <td><?= $moneda->simbolo.' '.number_format($list->total, 2) ?></td>
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