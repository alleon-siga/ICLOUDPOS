<?php $ruta = base_url(); ?>
<?php $md = get_moneda_defecto() ?>
<div class="row">
    <div class="col-md-10"></div>
    <div class="col-md-2">
        <label>Total: <?= $moneda->simbolo ?> <span id="total"><?= number_format($venta_totales->total, 2) ?></span></label>
    </div>
</div>
<div class="table-responsive">
    <table class='table table-striped dataTable tableStyle'>
        <thead>
            <tr>
                <th width="5%"># Venta</th>
                <th width="5%">Fecha Registro</th>
                <th width="5%">Fecha Venta</th>
                <th width="5%"># Comprobante</th>
                <th width="10%">Identificaci&oacute;n</th>
                <th width="20%">Cliente</th>
                <th width="10%">Vendedor</th>
                <th width="5%">Condici&oacute;n</th>
                <th width="5%">Estado</th>
                <th width="5%">Tip. Cam.</th>
                <th width="5%">Total</th>
                <th width="10%">Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php if (count($ventas) > 0): ?>

            <?php foreach ($ventas as $venta): ?>
                <tr <?= $venta->venta_estado == 'ANULADO' ? 'style="color: red;"' : '' ?>>
                    <td style="white-space: normal;"><?= $venta->venta_id ?></td>
                    <td style="white-space: normal;">
                        <span style="display: none;"><?= date('YmdHis', strtotime($venta->venta_creado)) ?></span>
                        <?= date('d/m/Y H:i', strtotime($venta->venta_creado)) ?>
                    </td>
                    <td style="white-space: normal;">
                        <span style="display: none;"><?= date('YmdHis', strtotime($venta->venta_fecha)) ?></span>
                        <?= date('d/m/Y H:i', strtotime($venta->venta_fecha)) ?>
                    </td>
                    <td style="white-space: normal;">
                    <?php
                        if ($venta->numero != ''){
                            echo $venta->documento_abr . ' ' . $venta->serie . '-' . sumCod($venta->numero, 6);
                        }else{
                            echo '<span style="color: #0000FF">NO EMITIDO</span>';
                        }
                    ?>
                    </td>
                    <td style="white-space: normal;"><?= $venta->ruc ?></td>
                    <td style="white-space: normal;"><?= $venta->cliente_nombre ?></td>
                    <td style="white-space: normal;"><?= $venta->vendedor_nombre ?></td>
                    <td style="white-space: normal;"><?= $venta->condicion_nombre ?></td>
                    <td style="white-space: normal;"><?= $venta->venta_estado ?></td>
                    <td style="white-space: normal;"><?= $venta->moneda_tasa ?></td>
                    <td style="text-align: right;"><?= $venta->moneda_simbolo ?> <?= number_format($venta->total, 2) ?></td>
                    <td style="text-align: center; white-space: normal;">
                        <a class="btn btn-default" data-toggle="tooltip" style="margin-right: 5px;" title="Ver" data-original-title="Ver" href="#" onclick="ver('<?= $venta->venta_id ?>');">
                            <i class="fa fa-search"></i>
                        </a>
                        <a class="btn btn-primary" data-toggle="tooltip" style="margin-right: 5px;" title="Ver" data-original-title="Ver" href="#" onclick="shadow('<?= $venta->venta_id ?>');">
                            <i class="fa fa-dollar"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach ?>
        <?php endif; ?>

        </tbody>
    </table>
    <!--<a id="exportar_pdf"
       href="#"
       class="btn  btn-default btn-lg" data-toggle="tooltip" title="Exportar a PDF"
       data-original-title="fa fa-file-pdf-o"><i class="fa fa-file-pdf-o fa-fw"></i></a>
    <a id="exportar_excel"
       href="#"
       class="btn btn-default btn-lg" data-toggle="tooltip" title="Exportar a Excel"
       data-original-title="fa fa-file-excel-o"><i class="fa fa-file-excel-o fa-fw"></i></a>-->
    <div class="modal fade" id="dialog_venta_detalle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false" aria-hidden="true"></div>
</div>
<script src="<?= $ruta; ?>recursos/js/facturador_historial_list.js"></script>