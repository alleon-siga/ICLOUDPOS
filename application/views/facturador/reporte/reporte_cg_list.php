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
                <th>Cod</th>
                <th>Producto</th>
                <th>Marca</th>
                <th>UM</th>
                <th>Costo Real S/</th>
                <th>Costo Contable S/</th>
                <th>Costo Real $</th>
                <th>Costo Contable $</th>
                <th>Tipo de Cambio</th>
                <th>% Precio</th>
                <th>Precio Comp S/</th>                
                <th>Precio Comp $</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($lists as $ingreso):
                ?>
                <tr>
                    <td><?= $ingreso->producto_codigo_interno ?></td>
                    <td><?= $ingreso->producto_nombre ?></td>
                    <td><?= $ingreso->producto_marca != NULL ? $ingreso->producto_marca : "Sin Marca" ?></td>
                    <td><?= $ingreso->nombre_unidad ?></td>
                    <td><?= number_format($ingreso->costo_real, 2) ?></td>
                    <td><?= number_format($ingreso->contable_costo, 2) ?></td>
                    <td><?= number_format($ingreso->costo_real_d, 2) ?></td>
                    <td><?= number_format($ingreso->costo_contable_d, 2) ?></td>
                    <td><?= $ingreso->tipo_cambio ?></td>
                    <td><?= number_format($ingreso->porcentaje_utilidad, 2) ?></td>
                    <td><?= number_format($ingreso->precio_compra_s, 2) ?></td>
                    <td><?= number_format($ingreso->precio_compra_d, 2) ?></td>    
                </tr>
                <?php
            endforeach;
            ?>
        </tbody>
        <!--<tfoot>
            <tr>
                <td colspan="8">TOTALES</td>
                <td style="text-align: right;"><?// number_format($totalCostoImpuesto, 2) ?></td>
                <td></td>
                <td></td>
                <td style="text-align: right;"><?// number_format($totalPrecioImpuesto, 2) ?></td>
                <td style="text-align: right;"><?// number_format($totalCostoTotal, 2) ?></td>
                <td></td>
                <td style="text-align: right;"><?// number_format($totalSubTotal, 2) ?></td>
                <td style="text-align: right;"><?// number_format($totalImpuestoV, 2) ?></td>
                <td style="text-align: right;"><?// number_format($totalVentaTotal, 2) ?></td>
                <td></td>
                <td style="text-align: right; color: green;"><?// number_format($totalUtilidadTotal, 2) ?></td>
                <td></td>
            </tr>
        </tfoot>-->
    </table>
</div>
<!--<div class="row">
    <div class="col-md-12">
        <br>
        <button type="button" id="exportar_excel" title="Exportar Excel" class="btn btn-primary">
            <i class="fa fa-file-excel-o fa-fw"></i>
        </button>
        <button type="button" id="exportar_pdf" title="Exportar Pdf" class="btn btn-primary">
            <i class="fa fa-file-pdf-o fa-fw"></i>
        </button>
    </div>
</div>-->
<script type="text/javascript">
    $(document).ready(function () {
        //TablesDatatables.init(0, 'asc');

        /*$('#exportar_excel').on('click', function () {
         exportar_excel();
         });
         
         $("#exportar_pdf").on('click', function () {
         exportar_pdf();
         });*/
    });

    /*function exportar_pdf() {
     var data = {
     'local_id': $("#local_id").val(),
     'fecha': $("#fecha").val()
     };
     
     var win = window.open('<? //base_url()?>facturador/reporte/reporte/pdf?data=' + JSON.stringify(data), '_blank');
     win.focus();
     }
     
     function exportar_excel() {
     var data = {
     'local_id': $("#local_id").val(),
     'fecha': $("#fecha").val()
     };
     
     var win = window.open('<? //base_url()?>facturador/reporte/reporte/excel?data=' + JSON.stringify(data), '_blank');
     win.focus();
     }*/
</script>