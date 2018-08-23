<?php $ruta = base_url(); ?>
<?php $md = get_moneda_defecto() ?>
<style>
    table tfoot tr td {
        font-weight: bold;
    }

    .b-default {
        background-color: #55c862;
        color: #fff;
    }

    .b-warning {
        background-color: #F78181;
        color: #fff;
    }

    .negativo {
        color: red;
    }
</style>

<table class="table dataTable table-bordered no-footer tableStyle">
    <thead>
    <tr>
        <th>Nro. Venta</th>
        <th>Fecha</th>
        <th>Documento</th>
        <th>Cliente</th>
        <th>Venta Total</th>
        <th>Emitido Total</th>
        <th>Descuento Total</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $total_venta = 0;
    $total_emitido = 0;
    $total_descuento = 0;
    ?>
    <?php foreach ($lists as $list): ?>
        <tr class="info" style="font-weight: bold;">
            <td><?= $list->venta_id ?></td>
            <td><?= date('m/d/Y', strtotime($list->fecha)) ?></td>
            <td><?php
                if ($list->id_documento == '1') echo 'FACTURA';
                if ($list->id_documento == '3') echo 'BOLETA';
                if ($list->id_documento == '6') echo 'NOTA DE VENTA';
                ?></td>
            <td style="white-space: normal;"><?= $list->cliente_nombre ?></td>
            <td style="text-align: right; white-space: nowrap;"><?= $emisor->moneda_simbolo ?> <?= number_format($list->total, 2) ?></td>
            <?php
            $total = 0;
            $descuento = 0;
            foreach ($list->comprobantes as $comprobante) {
                if ($comprobante->documento_tipo == '01' || $comprobante->documento_tipo == '03') {
                    $total += $comprobante->total;
                    $descuento += $comprobante->total * $comprobante->descuento / 100;
                } else {
                    $total -= $comprobante->total;
                    $descuento -= $comprobante->total * $comprobante->descuento / 100;
                }
            }
            $total_venta += $list->total;
            $total_emitido += $total;
            $total_descuento += $descuento;
            ?>

            <td style="text-align: right; white-space: nowrap;"><?= $emisor->moneda_simbolo ?> <?= number_format($total, 2) ?></td>
            <td style="text-align: right; white-space: nowrap;"><?= $emisor->moneda_simbolo ?> <?= number_format($descuento, 2) ?></td>

        </tr>
        <?php foreach ($list->comprobantes as $comprobante): ?>
            <tr style="font-weight: normal;"
                class="<?= $comprobante->documento_tipo == '07' || $comprobante->documento_tipo == '08' ? 'text-danger' : '' ?>">
                <td></td>
                <td><?= date('m/d/Y', strtotime($comprobante->fecha)) ?></td>
                <td><?php
                    if ($comprobante->documento_tipo == '01') echo 'FACTURA';
                    if ($comprobante->documento_tipo == '03') echo 'BOLETA';
                    if ($comprobante->documento_tipo == '07') echo 'NOTA DE CREDITO';
                    if ($comprobante->documento_tipo == '08') echo 'NOTA DE DEBITO';
                    ?></td>
                <td>
                    <?= $comprobante->documento_numero ?>
                    <?= $comprobante->documento_mod_numero != '' ? ' (DOC AFECTADO: ' . $comprobante->documento_mod_numero . ')' : '' ?>
                </td>
                <td>

                </td>
                <td style="text-align: right; white-space: nowrap;">
                    <?= $emisor->moneda_simbolo ?> <?= number_format($comprobante->total, 2) ?></td>
                <td style="text-align: right; white-space: nowrap;">
                    <?= $emisor->moneda_simbolo ?> <?= number_format($comprobante->total * $comprobante->descuento / 100, 2) ?>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endforeach; ?>
    </tbody>
    <tfoot>
    <tr>
        <td colspan="4"></td>
        <td style="text-align: right; white-space: nowrap;"><?= $emisor->moneda_simbolo ?> <?= number_format($total_venta, 2) ?></td>
        <td style="text-align: right; white-space: nowrap;"><?= $emisor->moneda_simbolo ?> <?= number_format($total_emitido, 2) ?></td>
        <td style="text-align: right; white-space: nowrap;"><?= $emisor->moneda_simbolo ?> <?= number_format($total_descuento, 2) ?></td>
    </tr>
    </tfoot>
</table>

<script type="text/javascript">


  function exportar_pdf () {
    var data = {
      'local_id': $('#local_id').val(),
      'fecha': $('#fecha').val(),
      'doc_id': $('#doc_id').val(),
      'moneda_id': $('#moneda_id').val()
    }

    var win = window.open('<?= base_url()?>reporte/creditoFiscal/pdf?data=' + JSON.stringify(data), '_blank')
    win.focus()
  }

  function exportar_excel () {
    var data = {
      'local_id': $('#local_id').val(),
      'fecha': $('#fecha').val(),
      'doc_id': $('#doc_id').val(),
      'moneda_id': $('#moneda_id').val()
    }

    var win = window.open('<?= base_url()?>reporte/creditoFiscal/excel?data=' + JSON.stringify(data), '_blank')
    win.focus()
  }
</script>