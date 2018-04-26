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
</style>
<div class="table-responsive">
    <table class='table table-striped dataTable table-bordered no-footer tableStyle' style="overflow:scroll">
        <thead>
            <tr>
                <th># Venta</th>
                <th>Cliente</th>
                <th>Tienda</th>
                <th>Operador</th>
                <th># Recarga</th>
                <th># Transacci&oacute;n</th>
                <th>Fecha recarga</th>
                <th>Monto Recarga</th>
                <?php if($condicion_pago==2 || $condicion_pago==0){ ?><th>Fecha pago</th><?php } ?>
                <?php if($condicion_pago==2 || $condicion_pago==0){ ?><th>Monto pagado</th><?php } ?>
                <th>Pendiente pago</th>
                <th>Local</th>
                <th>Condici&oacute;n</th>
                <?php if($estado_pago==0){ ?><th>Estado</th><?php } ?>
            </tr>
        </thead>
        <tbody>
        <?php $suma = 0;  ?>    
        <?php foreach ($lists as $list): ?>
            <tr>
                <td><?= $list->venta_id ?></td>
                <td><?= utf8_decode($list->razon_social) ?></td>
                <td><?= utf8_decode($list->nota) ?></td>
                <td><?= $list->valor ?></td>
                <td><?= $list->rec_nro ?></td>
                <td><?= $list->rec_trans ?></td>
                <td><?= date('d/m/Y H:i', strtotime($list->fecha)) ?></td>
                <td style="text-align: right;">
                    <?= $md->simbolo ?> <?= number_format($list->total, 2) ?>
                </td>
                <?php if($condicion_pago==2 || $condicion_pago==0){ ?>
                    <td>
                    <?php if(!empty($list->fecha_abono)){ ?>
                        <?php echo date('d/m/Y H:i', strtotime($list->fecha_abono)); ?>
                    <?php }else{ ?>
                        <?php echo date('d/m/Y H:i', strtotime($list->fecha)); ?>
                    <?php } ?>
                    </td>
                <?php } ?>
                <?php if($condicion_pago==2 || $condicion_pago==0){ ?>
                    <td style="text-align: right;">
                        <?php if(!empty($list->monto_abono)){ ?>
                            <?php echo $md->simbolo.' '.number_format($list->monto_abono, 2); ?>
                        <?php }else{ ?>
                            <?php echo $md->simbolo.' '.number_format($list->total, 2); ?>
                        <?php } ?>
                    </td>
                <?php } ?>
                <td style="text-align: right;">
                    <label style="margin-bottom: 0px;" class="control-label badge <?php if($list->ispagado == 1 OR $list->ispagado==''){ echo 'b-default'; }elseif($list->ispagado == 0){ echo 'b-warning'; } ?>">
                    <?php if(!empty($list->monto_abono)){ ?>
                        <?= number_format($list->total - $list->monto_abono, 2) ?>
                    <?php }else{ ?>
                        <?= number_format($list->total - $list->total, 2) ?>
                    <?php } ?>
                    </label>
                </td>
                <td><?= utf8_decode($list->local_nombre) ?></td>
                <td><?= utf8_decode($list->condicion) ?></td>
                <?php if($estado_pago==0){ ?>
                    <?php if($list->ispagado == 1 OR $list->ispagado==''){ ?>
                        <td>Cancelado</td>
                    <?php }elseif($list->ispagado == 0) { ?>
                        <td>Debe</td>
                    <?php } ?>
                <?php } ?>
            </tr>     
            <?php if(!empty($list->monto_abono)){ ?>
                <?= $suma += $list->monto_abono ?>
            <?php }else{ ?>
                <?= $suma += $list->total ?>
            <?php } ?>
        <?php endforeach ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="9" style="text-align: right;">TOTAL</td>
                <td style="text-align: right;"><?= $md->simbolo ?> <?= number_format($suma, 2) ?></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>
<div class="row">
    <div class="col-md-12">
        <br>
        <button type="button" id="exportar_excel" title="Exportar Excel" class="btn btn-primary">
            <i class="fa fa-file-excel-o fa-fw"></i>
        </button>
        <button type="button" id="exportar_pdf" title="Exportar Pdf" class="btn btn-primary">
            <i class="fa fa-file-pdf-o fa-fw"></i>
        </button>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        TablesDatatables.init(0);

        $('#exportar_excel').on('click', function () {
            exportar_excel();
        });

        $("#exportar_pdf").on('click', function () {
            exportar_pdf();
        });
    });

    function exportar_pdf() {
        var data = {
            'local_id': $("#local_id").val(),
            'fecha': $("#fecha").val(),
            'condicion_pago': $("#condicion_pago").val(),
            'estado_pago': $('#estado_pago').val()
        };

        var win = window.open('<?= base_url()?>reporte/pagosRecarga/pdf?data=' + JSON.stringify(data), '_blank');
        win.focus();
    }

    function exportar_excel() {
        var data = {
            'local_id': $("#local_id").val(),
            'fecha': $("#fecha").val(),
            'condicion_pago': $("#condicion_pago").val(),
            'estado_pago': $('#estado_pago').val()
        };

        var win = window.open('<?= base_url()?>reporte/pagosRecarga/excel?data=' + JSON.stringify(data), '_blank');
        win.focus();
    }
</script>