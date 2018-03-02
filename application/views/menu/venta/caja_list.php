<?php $ruta = base_url(); ?>
<style>
    .table td {
        font-size: 14px !important;
    }
</style>
<input type="hidden" id="caja_imprimir" value="1">
<?php $md = get_moneda_defecto() ?>
<div class="row">
    <div class="col-md-9"></div>
    <!--<div class="col-md-2">
        <label>Subtotal: <?= $md->simbolo ?> <span id="subtotal"><?= number_format($venta_totales->subtotal, 2) ?></span></label>
    </div>
    <div class="col-md-2">
        <label>IGV: <?= $md->simbolo ?> <span id="impuesto"><?= number_format($venta_totales->impuesto, 2) ?></span></label>
    </div>-->
    <label>Total Pendiente:
        <label style="padding: 5px; font-size: 14px; margin: 0px;"
               class="control-label badge label-warning panel-admin-text">
            <?= $md->simbolo ?> <span><?= number_format($venta_totales->total, 2) ?></span>
        </label>
    </label>
</div>
</div>
<div class="table-responsive">
    <table class='table table-striped dataTable table-bordered no-footer' id="tabla_caja" style="overflow:scroll">
        <thead>
        <tr>
            <th>Fecha</th>
            <th>Num Doc</th>
            <th>Cliente</th>
            <th>Vendedor</th>
            <th>Condici&oacute;n</th>
            <th>Total <?= $venta_action == 'caja' ? 'a Pagar' : '' ?></th>
            <th>Acciones</th>


        </tr>
        </thead>
        <tbody>
        <?php if (count($ventas) > 0): ?>

            <?php foreach ($ventas as $venta): ?>
                <tr>
                    <td>
                        <span style="display: none;"><?= date('YmdHis', strtotime($venta->venta_fecha)) ?></span>
                        <?= date('d/m/Y H:i:s', strtotime($venta->venta_fecha)) ?>
                    </td>
                    <td><?= sumCod($venta->venta_id, 4) ?></td>
                    <td><?= $venta->cliente_nombre ?></td>
                    <td><?= $venta->vendedor_nombre ?></td>
                    <td><?= $venta->condicion_nombre ?></td>
                    <td style="text-align: right; vertical-align: middle;">
                        <label style="padding: 5px; font-size: 14px; margin: 0px;"
                               class="control-label badge label-warning panel-admin-text">
                            <?= $venta->moneda_simbolo ?> <?= $venta->condicion_id == 1 ? number_format($venta->total, 2) : number_format($venta->inicial, 2) ?>
                        </label>
                    </td>
                    <td style="text-align: center;">

                        <a class="btn btn-default" data-toggle="tooltip" style="margin-right: 5px;"
                           title="Ver" data-original-title="Ver"
                           href="#"
                           onclick="ver('<?= $venta->venta_id ?>');">
                            <i class="fa fa-search"></i>
                        </a>

                        <?php if ($venta_action == 'caja'): ?>
                            <a class="btn btn-primary" data-toggle="tooltip" style="margin-right: 5px;"
                               title="Cobrar" data-original-title="Cobrar"
                               href="#"
                               onclick="cobrar('<?= $venta->venta_id ?>');">
                                <i class="fa fa-money"></i>
                            </a>

                            <a class="btn btn-danger" data-toggle="tooltip"
                               title="Cancelar Venta" data-original-title="Cancelar Venta"
                               href="#"
                               onclick="anular('<?= $venta->venta_id ?>', '<?= sumCod($venta->venta_id, 6) ?>');">
                                <i class="fa fa-remove"></i>
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach ?>
        <?php endif; ?>

        </tbody>
    </table>


    <div class="modal fade" id="dialog_venta_detalle" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false"
         aria-hidden="true">

    </div>


    <div class="modal fade" id="dialog_venta_imprimir" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false"
         aria-hidden="true">

    </div>

    <div class="modal fade" id="dialog_venta_cerrar" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false"
         aria-hidden="true">

    </div>
</div>


<script>
    $(function () {

        $("#dialog_venta_imprimir").on('hidden.bs.modal', function () {
            get_ventas();
        });

    });


</script>