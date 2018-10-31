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
    .negativo{
        color: red;
    }
</style>
<ul class="nav nav-tabs">
  <li class="active"><a data-toggle="tab" href="#resumen">Resumen</a></li>
  <li><a data-toggle="tab" href="#detallado">Detalle Ventas</a></li>
</ul>
<div class="tab-content">
    <div id="resumen" class="tab-pane fade in active">
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <table class="table table-responsive table-bordered">
                    <tr>
                        <td colspan="2" style="background-color: #cccccc; font-weight: bold;">VENTAS</td>
                    </tr>
                    <tr>
                        <td>SUBTOTAL</td>
                        <td style="text-align: right;" id="subtotal"><?= $md->simbolo.' '.number_format(0,2) ?></td>
                    </tr>
                    <tr>
                        <td>CREDITO FISCAL (A PAGAR)</td>
                        <td style="text-align: right;" id="impuesto"><?= $md->simbolo.' '.number_format(0,2) ?></td>
                    </tr>
                    <tr>
                        <td>TOTAL</td>
                        <td style="text-align: right;" id="total"><?= $md->simbolo.' '.number_format(0,2) ?></td>
                    </tr>
                </table>

                <table class="table table-responsive table-bordered">
                    <tr>
                        <td colspan="2" style="background-color: #cccccc; font-weight: bold;">COMPRAS</td>
                    </tr>
                    <tr>
                        <td>SUBTOTAL</td>
                        <td style="text-align: right;"><?= $totalCompra->simbolo.' '.number_format($totalCompra->subtotal,2) ?></td>
                    </tr>
                    <tr>
                        <td>CREDITO FISCAL (A FAVOR)</td>
                        <td style="text-align: right;"><?= $totalCompra->simbolo.' '.number_format($totalCompra->impuesto,2) ?></td>
                    </tr>
                    <tr>
                        <td>TOTAL</td>
                        <td style="text-align: right;"><?= $totalCompra->simbolo.' '.number_format($totalCompra->total,2) ?></td>
                    </tr>
                </table>

                <table class="table table-responsive table-bordered">
                    <tr>
                        <td colspan="2" style="background-color: #cccccc; font-weight: bold;">GASTOS</td>
                    </tr>
                    <tr>
                        <td>SUBTOTAL</td>
                        <td style="text-align: right;"><?= $totalGasto->simbolo.' '.number_format($totalGasto->subtotal,2) ?></td>
                    </tr>
                    <tr>
                        <td>CREDITO FISCAL (IMPUESTO)</td>
                        <td style="text-align: right;"><?= $totalGasto->simbolo.' '.number_format($totalGasto->impuesto,2) ?></td>
                    </tr>
                    <tr>
                        <td>TOTAL</td>
                        <td style="text-align: right;"><?= $totalGasto->simbolo.' '.number_format($totalGasto->total,2) ?></td>
                    </tr>
                </table>                
            </div>
            <div class="col-md-2"></div>
        </div>
    </div>
    <div id="detallado" class="tab-pane fade">
        <div class="table-responsive">
            <table class='table dataTable table-bordered no-footer tableStyle' style="overflow:scroll">
                <thead>
                    <tr>
                        <th># Venta</th>
                        <th>Local</th>
                        <th>Fecha Registro</th>
                        <th>Fecha Venta</th>
                        <th># Comprobante</th>
                        <th>Cliente</th>
                        <th>Condici&oacute;n</th>
                        <th>Subtotal</th>
                        <th>Impuesto</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
            <?php
                $totalSubTotal = $totalImpuestoV = $totalVentaTotal = 0;
                foreach ($lists as $ingreso):
                    $cantidad = $ingreso->cantidad;
                    $impuesto = (($ingreso->impuesto_porciento / 100) + 1);
                    if($ingreso->tipo_impuesto=='1'){ //incluye impuesto
                        $precioVenta = $ingreso->detalle_importe; //precio de venta
                        $costoVentaSi = ($precioVenta / $cantidad) / $impuesto; //Costo de venta unitario sin impuesto
                        $costoVenta = $costoVentaSi * $impuesto; //Costo de venta unitario con impuesto
                    }elseif($ingreso->tipo_impuesto=='2'){ //agregar impuesto
                        $precioVenta = $ingreso->detalle_importe * $impuesto;
                        $costoVentaSi = ($precioVenta / $cantidad) / $impuesto; //Costo de venta unitario sin impuesto
                        $costoVenta = $costoVentaSi * $impuesto; //Costo de venta unitario con impuesto
                    }else{ //no incluye impuesto
                        $precioVenta = $ingreso->detalle_importe; //precio de venta
                        $costoVentaSi = ($precioVenta / $cantidad);
                        $costoVenta = $costoVentaSi;
                    }
                    $subtotal = $cantidad * $costoVentaSi;
                    $impVenta = $precioVenta - $subtotal;
                    $totalSubTotal += $subtotal;
                    $totalImpuestoV += $impVenta;
                    $totalVentaTotal += $precioVenta;
            ?>
                    <tr>
                        <td style="text-align: right;"><?= $ingreso->venta_id ?></td>
                        <td><?= $ingreso->local_nombre ?></td>
                        <td><?= date('d/m/Y', strtotime($ingreso->created_at)) ?></td>
                        <td><?= date('d/m/Y', strtotime($ingreso->fecha)) ?></td>
                        <td><?= $ingreso->abr_doc. ' ' . $ingreso->serie . '-' . sumCod($ingreso->numero, 6) ?></td>
                        <td><?= $ingreso->razon_social ?></td>
                        <td><?= $ingreso->nombre_condiciones ?></td>
                        <td style="text-align: right;"><?= $ingreso->simbolo.' '.number_format($subtotal, 2) ?></td>
                        <td style="text-align: right;"><?= $ingreso->simbolo.' '.number_format($impVenta, 2) ?></td>
                        <td style="text-align: right;"><?= $ingreso->simbolo.' '.number_format($precioVenta, 2) ?></td>
                    </tr>
            <?php
                endforeach;
            ?>
                </tbody>
            <?php if(count($lists)>0){ ?>
                <tfoot>
                    <tr>
                        <td colspan="7"></td>
                        <td id="subtotal2" style="text-align: right;"><?= $lists[0]->simbolo.' '.number_format($totalSubTotal, 2) ?></td>
                        <td id="impuesto2" style="text-align: right;"><?= $lists[0]->simbolo.' '.number_format($totalImpuestoV, 2) ?></td>
                        <td id="total2" style="text-align: right;"><?= $lists[0]->simbolo.' '.number_format($totalVentaTotal, 2) ?></td>
                    </tr>
                </tfoot>
            <?php } ?>
            </table>
        </div>
        <div class="row">
            <div class="col-md-12">
                <br>
                <button type="button" id="exportar_excel" title="Exportar Excel" class="btn btn-success btn-md">
                    <i class="fa fa-file-excel-o fa-fw"></i>
                </button>
                <button type="button" id="exportar_pdf" title="Exportar Pdf" class="btn btn-danger btn-md">
                    <i class="fa fa-file-pdf-o fa-fw"></i>
                </button>
            </div>
        </div>
    </div>
<script type="text/javascript">
    if($('#subtotal2').text()!=''){
        $('#subtotal').text($('#subtotal2').text());    
    }
    if($('#impuesto2').text()!=''){
        $('#impuesto').text($('#impuesto2').text());
    }
    if($('#total2').text()!=''){
        $('#total').text($('#total2').text());    
    }
    $(document).ready(function () {
        TablesDatatables.init(0, 'desc');

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
            'doc_id': $("#doc_id").val(),
            'moneda_id': $("#moneda_id").val()
        };

        var win = window.open('<?= base_url()?>reporte/creditoFiscal/pdf?data=' + JSON.stringify(data), '_blank');
        win.focus();
    }

    function exportar_excel() {
        var data = {
            'local_id': $("#local_id").val(),
            'fecha': $("#fecha").val(),
            'doc_id': $("#doc_id").val(),
            'moneda_id': $("#moneda_id").val()
        };

        var win = window.open('<?= base_url()?>reporte/creditoFiscal/excel?data=' + JSON.stringify(data), '_blank');
        win.focus();
    }
</script>