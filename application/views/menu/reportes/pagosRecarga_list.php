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
    <table class='table dataTable table-bordered no-footer tableStyle' style="overflow:scroll">
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
                <th>Fecha pago</th>
                <th>Monto pagado</th>
                <th>Pendiente pago</th>
                <th>Local</th>
                <th>Condici&oacute;n</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
        <?php $suma = 0;  ?>    
        <?php foreach ($lists as $list): ?>
        <?php
            $fechaRecarga = $list->fecha;
            $debe = 0;
            $color = 'b-default';
            $estado = 'Cancelado';
            $fechaPago = $list->fecha;
            $montoPagado = $list->total;
            if($list->condicion_pago == 2){
                $debe = $list->monto_restante;
                if($debe == 0 && !empty($debe)){ //cuando no hay deuda
                    $color = 'b-default';
                    $estado = 'Cancelado';
                    $fechaPago = $list->fecha_abono;
                    $montoPagado = $list->monto_abono;
                }elseif(empty($debe)){ //cuando no hay ningun pago
                    $debe = $list->total;
                    $color = 'b-warning';
                    $estado = 'Debe';
                    $fechaPago = '';
                    $montoPagado = 0;
                }else{ //cuando hay deuda
                    $color = 'b-warning';
                    $estado = 'Debe';
                    $fechaPago = $list->fecha_abono;
                    $montoPagado = $list->monto_abono;
                }
            }
            if(strtotime($fechaPago) == strtotime($fechaRecarga)){
                $colorFila = "#9fa8da";
            }else{
                $colorFila = "#81d4fa";
            }
        ?>
            <tr style="background-color: <?= $colorFila ?> !important">
                <td><?= $list->venta_id ?></td>
                <td><?= utf8_decode($list->razon_social) ?></td>
                <td><?= utf8_decode($list->nota) ?></td>
                <td><?= $list->valor ?></td>
                <td><?= $list->rec_nro ?></td>
                <td><?= $list->rec_trans ?></td>
                <td><?= date('d/m/Y H:i', strtotime($fechaRecarga)) ?></td>
                <td style="text-align: right;"><?= $md->simbolo ?> <?= number_format($list->total, 2) ?></td>
                <td><?php if(!empty($fechaPago)) echo date('d/m/Y H:i', strtotime($fechaPago)) ?></td>
                <td style="text-align: right;"><?= $md->simbolo.' '.number_format($montoPagado, 2); ?></td>
                <td style="text-align: right;">
                    <label style="margin-bottom: 0px;" class="control-label badge <?= $color ?>">
                        <?= $md->simbolo.' '.number_format($debe, 2); ?>
                    </label>
                </td>
                <td><?= utf8_decode($list->local_nombre) ?></td>
                <td><?= utf8_decode($list->condicion) ?></td>
                <td><?= $estado ?></td>
            </tr>     
            <?php if(!empty($list->monto_abono)){ ?>
                <?php $suma += $list->monto_abono ?>
            <?php }elseif($list->condicion_pago==1){ ?>
                <?php $suma += $list->total ?>
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
            'estado_pago': $('#estado_pago').val(),
            'poblado_id' : $('#poblado_id').val()
        };

        var win = window.open('<?= base_url()?>reporte/pagosRecarga/pdf?data=' + JSON.stringify(data), '_blank');
        win.focus();
    }

    function exportar_excel() {
        var data = {
            'local_id': $("#local_id").val(),
            'fecha': $("#fecha").val(),
            'estado_pago': $('#estado_pago').val(),
            'poblado_id' : $('#poblado_id').val()
        };

        var win = window.open('<?= base_url()?>reporte/pagosRecarga/excel?data=' + JSON.stringify(data), '_blank');
        win.focus();
    }
</script>