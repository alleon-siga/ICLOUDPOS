<?php $ruta = base_url(); ?>
<?php $md = get_moneda_defecto() ?>
<div class="row">
    <div class="col-md-10"></div>
    <!--        <div class="col-md-2">-->
    <!--            <label>Subtotal: --><? //= $moneda->moneda ?><!-- <span id="subtotal">-->
    <? //=number_format($venta_totales->subtotal, 2)?><!--</span></label>-->
    <!--        </div>-->
    <!--        <div class="col-md-2">-->
    <!--            <label>IGV: --><? //= $moneda->simbolo ?><!-- <span id="impuesto">-->
    <? //=number_format($venta_totales->impuesto, 2)?><!--</span></label>-->
    <!--        </div>-->
    <div class="col-md-2">
        <label>Total: <?= $moneda->simbolo ?> <span
                    id="total"><?= number_format($venta_totales->total, 2) ?></span></label>
    </div>
</div>
<div class="table-responsive">
    <table class='table table-striped dataTable table-bordered no-footer tableStyle' style="overflow:scroll">
        <thead>
        <tr>
            <th>ID</th>
            <th>Fecha</th>
            <th>Doc</th>
            <th>Identificaci&oacute;n</th>
            <th>Cliente</th>
            <th>Vendedor</th>
            <th>Condici&oacute;n</th>
            <th>Estado</th>
            <th>Tip. Cam.</th>
            <th>Total <?= $venta_action == 'caja' ? 'a Pagar' : '' ?></th>
            <th>Acciones</th>


        </tr>
        </thead>
        <tbody>
        <?php if (count($ventas) > 0): ?>

            <?php foreach ($ventas as $venta): ?>
                <tr <?= $venta->venta_estado == 'ANULADO' ? 'style="color: red;"' : '' ?>>
                    <td><?= $venta->venta_id ?></td>
                    <td>
                        <span style="display: none;"><?= date('YmdHis', strtotime($venta->venta_fecha)) ?></span>
                        <?= date('d/m/Y H:i', strtotime($venta->venta_fecha)) ?>
                    </td>

                    <td><?php
                        $doc = '';
                        if ($venta->documento_id == 1) $doc = "FA";
                        if ($venta->documento_id == 2) $doc = "NC";
                        if ($venta->documento_id == 3) $doc = "BO";
                        if ($venta->documento_id == 4) $doc = "GR";
                        if ($venta->documento_id == 5) $doc = "PCV";
                        if ($venta->documento_id == 6) $doc = "NP";
                        if($venta->numero != '')
                        echo $doc . ' ' . $venta->serie . '-' . sumCod($venta->numero, 6);
                        else
                            echo '<span style="color: #0000FF">NO FACTURADO</span>';
                        ?>
                    </td>
                    <td><?= $venta->ruc ?></td>
                    <td><?= $venta->cliente_nombre ?></td>
                    <td><?= $venta->vendedor_nombre ?></td>
                    <td><?= $venta->condicion_nombre ?></td>
                    <td><?= $venta->venta_estado ?></td>
                    <td><?= $venta->moneda_tasa ?></td>
                    <td style="text-align: right;"><?= $venta->moneda_simbolo ?> <?= number_format($venta->total, 2) ?></td>
                    <td style="text-align: center;">
                        <a class="btn btn-default" data-toggle="tooltip" style="margin-right: 5px;"
                           title="Ver" data-original-title="Ver"
                           href="#"
                           onclick="ver('<?= $venta->venta_id ?>');">
                            <i class="fa fa-search"></i>
                        </a>

                        <?php if($venta->numero == '' && $venta_action != 'comision'):?>

                            <a class="btn btn-warning" data-toggle="tooltip" style="margin-right: 5px;"
                               title="Ver" data-original-title="Facturar"
                               href="#"
                               onclick="facturar('<?= $venta->venta_id ?>');">
                                <i class="fa fa-file-text"></i>
                            </a>
                        <?php endif;?>

                        <?php if ($venta_action != 'anular' && $venta_action != 'caja' && $venta->venta_estado != 'CERRADA' && $venta_action != 'comision'): ?>
                            <?php //if($venta_action != 'comision'):  ?>
                            <a class="btn btn-primary" data-toggle="tooltip" style="margin-right: 5px;"
                               title="Ver" data-original-title="Ver"
                               href="#"
                               onclick="previa('<?= $venta->venta_id ?>');">
                                <i class="fa fa-print"></i>
                            </a>
                            <?php //endif; ?>

                            <?php if ($venta->factura_impresa == 1 && $venta->documento_id != 6 && (validOption('ACTIVAR_SHADOW', 1) || validOption('ACTIVAR_FACTURACION_VENTA', 1))): ?>
                                <a class="btn btn-warning" data-toggle="tooltip" style="margin-right: 5px;"
                                   title="Cerrar Venta" data-original-title="Cerrar Venta"
                                   href="#"
                                   onclick="cerrar_venta('<?= $venta->venta_id ?>');">
                                    <i class="fa fa-unlock"></i>
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if ($venta_action == 'anular'): ?>
                            <?php if ($venta->condicion_id == '1'): ?>
                                <a class="btn btn-danger" data-toggle="tooltip" style="margin-right: 5px;"
                                   title="Devolver Venta" data-original-title="Devolver Venta"
                                   href="#"
                                   onclick="devolver('<?= $venta->venta_id ?>');">
                                    <i class="fa fa-arrow-circle-left"></i>
                                </a>
                            <?php endif; ?>

                            <?php if ($venta->credito_pagado == 0 || $venta->condicion_id == '1'): ?>
                                <a class="btn btn-danger" data-toggle="tooltip"
                                   title="Anular Venta" data-original-title="Anular Venta"
                                   href="#"
                                   onclick="anular('<?= $venta->venta_id ?>', '<?= sumCod($venta->venta_id, 6) ?>');">
                                    <i class="fa fa-remove"></i>
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach ?>
        <?php endif; ?>

        </tbody>
    </table>


    <a id="exportar_pdf"
       href="#"
       class="btn  btn-default btn-lg" data-toggle="tooltip" title="Exportar a PDF"
       data-original-title="fa fa-file-pdf-o"><i class="fa fa-file-pdf-o fa-fw"></i></a>

    <a id="exportar_excel"
       href="#"
       class="btn btn-default btn-lg" data-toggle="tooltip" title="Exportar a Excel"
       data-original-title="fa fa-file-excel-o"><i class="fa fa-file-excel-o fa-fw"></i></a>


    <div class="modal fade" id="dialog_venta_detalle" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false"
         aria-hidden="true">

    </div>


    <div class="modal fade" id="dialog_venta_imprimir" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false"
         aria-hidden="true">

    </div>

    <div class="modal fade" id="dialog_venta_facturar" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false"
         aria-hidden="true">

    </div>

    <div class="modal fade" id="dialog_venta_cerrar" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false"
         aria-hidden="true">

    </div>
</div>

<script type="text/javascript">
    $(function () {

        $('#exportar_excel').on('click', function (e) {
            e.preventDefault();
            exportar_excel();
        });

        $("#exportar_pdf").on('click', function (e) {
            e.preventDefault();
            exportar_pdf();
        });

        TablesDatatables.init(1);

    });

    function exportar_pdf() {

        var data = {
            'local_id': $("#venta_local").val(),
            'esatdo': $("#venta_estado").val(),
            'fecha': $("#date_range").val(),
            'moneda_id': $("#moneda_id").val(),
            'condicion_pago_id': $("#condicion_pago_id").val()
        };

        var win = window.open('<?= base_url()?>venta_new/historial_pdf?data=' + JSON.stringify(data), '_blank');
        win.focus();
    }

    function exportar_excel() {
        var data = {
            'local_id': $("#venta_local").val(),
            'esatdo': $("#venta_estado").val(),
            'fecha': $("#date_range").val(),
            'moneda_id': $("#moneda_id").val(),
            'condicion_pago_id': $("#condicion_pago_id").val()
        };

        var win = window.open('<?= base_url()?>venta_new/historial_excel?data=' + JSON.stringify(data), '_blank');
        win.focus();
    }

    function ver(venta_id) {

        $("#dialog_venta_detalle").html($("#loading").html());
        $("#dialog_venta_detalle").modal('show');

        $.ajax({
            url: '<?php echo $ruta . 'venta_new/get_venta_detalle/' . $venta_action; ?>',
            type: 'POST',
            data: {'venta_id': venta_id},

            success: function (data) {
                $("#dialog_venta_detalle").html(data);
            },
            error: function () {
                alert('asd')
            }
        });
    }    
</script>