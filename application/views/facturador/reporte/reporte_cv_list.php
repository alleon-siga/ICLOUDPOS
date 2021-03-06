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
    table tr th, table tr td{
        border: 1px solid !important; 
    }
    [data-toggle="tooltip"]{
        cursor: pointer;
    }
    
</style>
<?php
foreach ($lists as $ingreso):
    echo '<h4 class="text-center">' . $ingreso->producto_nombre . '</h4>';
endforeach;
?>
<?php
foreach ($lists as $ingreso):
    ?>
    <table class="table">
        <thead>
            <tr style="border-color: transparent !important;">
                <th colspan="8" class="thvacio thblack"></th>
                <th colspan="3" class="thblack">Compras</th>
                <th colspan="4" class="thblack">Ventas</th>
                <th class="thvacio thblack"></th>
                <th class="thvacio thblack"></th>
            </tr>
            <tr>
                <th class="thvacio thblack"></th>
                <th class="thblack">Fecha</th>
                <th class="thblack">Ruc / Dni</th>
                <th class="thblack">Nombre</th>
                <th class="CellWithComment thblack">Cli / Pro  <span class="CellComment">Cliente / Proveedor</span></th>
                <th class="CellWithComment thblack">E / S  <span class="CellComment">Entradas / Salidas</span></th>
                <th class="thblack">Tipo Doc.</th>
                <th class="thblack">Nro Doc.</th>
                <th class="CellWithComment thblack">NC <span class="CellComment">Nota de Compra</span></th>
                <th class="CellWithComment thblack">BO <span class="CellComment">Boleta</span></th>
                <th class="CellWithComment thblack">FA <span class="CellComment">Factura</span></th>
                <th class="CellWithComment thblack">NV <span class="CellComment">Nota de Venta</span></th>
                <th class="CellWithComment thblack">BO <span class="CellComment">Boleta</span></th>
                <th class="CellWithComment thblack">FA <span class="CellComment">Factura</span></th>
                <th class="CellWithComment thblack">GR <span class="CellComment">Guia de Remision</span></th>
                <th class="thblack">Costo Total</th>
                <th class="thblack">Existencia</th>
            </tr>
        </thead>
        <tbody>
            <tr class="trblack">
                <th class="thblack">Contable</th>
                <td >05-09-2018</td>
                <td>354535453</td>
                <td>Alfonso</td>
                <td>Cliente</td>
                <td>Salida</td>
                <td>Boleta</td>
                <td>001</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td >3.00</td>
                <td >-</td>
                <td >-</td>
                <td >300.00</td>
                <td >-3.00</td>
            </tr>
            <tr class="trblack">
                <th class="thblack">Contable</th>
                <td >05-09-2018</td>
                <td>354535453</td>
                <td>Alfonso</td>
                <td>Cliente</td>
                <td>Salida</td>
                <td>Boleta</td>
                <td>001</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td >3.00</td>
                <td >-</td>
                <td >-</td>
                <td >300.00</td>
                <td >-3.00</td>
            </tr>
            <tr class="trblack">
                <th class="thblack">Contable</th>
                <td >05-09-2018</td>
                <td>354535453</td>
                <td>Alfonso</td>
                <td>Cliente</td>
                <td>Salida</td>
                <td>Boleta</td>
                <td>001</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td >3.00</td>
                <td >-</td>
                <td >-</td>
                <td >300.00</td>
                <td >-3.00</td>
            </tr>
        </tbody>

    </table>
    <?php
endforeach;
foreach ($lists as $ingreso):
    ?>
    <table class="table" >
        <thead>
            <tr>
                <th colspan="8" class="thvacio thblack"></th>
                <th colspan="3" class="thblack">Compras</th>
                <th colspan="4" class="thblack">Ventas</th>
                <th class="thvacio thblack"></th>
                <th class="thvacio thblack"></th>
            </tr>
            <tr>
                <th class="thvacio thblack"></th>
                <th class="thblack">Fecha</th>
                <th class="thblack">Ruc / Dni</th>
                <th class="thblack">Nombre</th>
                <th class="CellWithComment thblack">Cli / Pro  <span class="CellComment">Cliente / Proveedor</span></th>
                <th class="CellWithComment thblack">E / S  <span class="CellComment">Entradas / Salidas</span></th>
                <th class="thblack">Tipo Doc.</th>
                <th class="thblack">Nro Doc.</th>
                <th class="CellWithComment thblack ">NC <span class="CellComment">Nota de Compra</span></th>
                <th class="CellWithComment thblack">BO <span class="CellComment">Boleta</span></th>
                <th class="CellWithComment thblack">FA <span class="CellComment">Factura</span></th>
                <th class="CellWithComment thblack">NV <span class="CellComment">Nota de Venta</span></th>
                <th class="CellWithComment thblack">BO <span class="CellComment">Boleta</span></th>
                <th class="CellWithComment thblack">FA <span class="CellComment">Factura</span></th>
                <th class="CellWithComment thblack">GR <span class="CellComment">Guia de Remision</span></th>
                <th class="thblack">Costo Total</th>
                <th class="thblack">Existencia</th>
            </tr>
        </thead>
        <tbody>
            <tr class="trblack">
                <th class="thblack">Reales</th>
                <td >05-09-2018</td>
                <td>354535453</td>
                <td>Alfonso</td>
                <td>Cliente</td>
                <td>Salida</td>
                <td>Boleta</td>
                <td>001</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td >3.00</td>
                <td >-</td>
                <td >-</td>
                <td >300.00</td>
                <td >-3.00</td>
            </tr>
            <tr class="trblack">
                <th class="thblack">Reales</th>
                <td >05-09-2018</td>
                <td>354535453</td>
                <td>Alfonso</td>
                <td>Cliente</td>
                <td>Salida</td>
                <td>Boleta</td>
                <td>001</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td >3.00</td>
                <td >-</td>
                <td >-</td>
                <td >300.00</td>
                <td >-3.00</td>
            </tr>
            <tr class="trblack">
                <th class="thblack">Reales</th>
                <td >05-09-2018</td>
                <td>354535453</td>
                <td>Alfonso</td>
                <td>Cliente</td>
                <td>Salida</td>
                <td>Boleta</td>
                <td>001</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td >3.00</td>
                <td >-</td>
                <td >-</td>
                <td >300.00</td>
                <td >-3.00</td>
            </tr>
        </tbody>

    </table>
    <?php
endforeach;
foreach ($lists as $ingreso):
    ?>
    <table class="table" >
        <thead>
            <tr>
                <th colspan="5" class="thblack">Resume Comparativo de Compra/Venta para el producto <?= $ingreso->producto_nombre ?></th>
            </tr>
            <tr>
                <th colspan="2" class="thblack">Tipo Doc</th>
                <th class="thblack">Compras</th>
                <th class="thblack">Ventas</th>
                <th class="thblack">Totales</th>
            </tr>
        </thead>
        <tbody>
            <tr class="trblack">
                <td  colspan="2">Notas de Compras-Ventas</td>
                <td>0.00</td>
                <td>2.00</td>
                <td>-2.00</td>
            </tr>
            <tr class="trblack">
                <td  colspan="2">Boletas</td>
                <td>0.00</td>
                <td>2.00</td>
                <td>-2.00</td>
            </tr>
            <tr class="trblack">
                <td  colspan="2">Facturas</td>
                <td>0.00</td>
                <td>2.00</td>
                <td>-2.00</td>
            </tr>
            <tr class="trblack">
                <td  colspan="2">Guias de Remision</td>
                <td>0.00</td>
                <td>2.00</td>
                <td>-2.00</td>
            </tr>
        </tbody>

    </table>
    <?php
endforeach;
?>
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

        $('.nav-tabs a[href="#grafico"]').on('shown.bs.tab', function (event) {
            var data = {
                'local_id': $("#local_id").val(),
                'fecha': $("#fecha").val(),
                'producto_id': $("#producto_id").val(),
                'grupo_id': $("#grupo_id").val(),
                'marca_id': $("#marca_id").val(),
                'linea_id': $("#linea_id").val(),
                'familia_id': $("#familia_id").val()
            };
        });
    });

    function exportar_pdf() {
        var data = {
            'local_id': $("#local_id").val(),
            'fecha': $("#fecha").val(),
            'moneda_id': $("#moneda_id").val(),
            'producto_id': $("#producto_id").val(),
            'grupo_id': $("#grupo_id").val(),
            'marca_id': $("#marca_id").val(),
            'linea_id': $("#linea_id").val(),
            'familia_id': $("#familia_id").val()
        };

        var win = window.open('<?= base_url() ?>reporte_ventas/margenUtilidad/pdf?data=' + JSON.stringify(data), '_blank');
        win.focus();
    }

    function exportar_excel() {
        var data = {
            'local_id': $("#local_id").val(),
            'fecha': $("#fecha").val(),
            'moneda_id': $("#moneda_id").val(),
            'producto_id': $("#producto_id").val(),
            'grupo_id': $("#grupo_id").val(),
            'marca_id': $("#marca_id").val(),
            'linea_id': $("#linea_id").val(),
            'familia_id': $("#familia_id").val()
        };

        var win = window.open('<?= base_url() ?>reporte_ventas/margenUtilidad/excel?data=' + JSON.stringify(data), '_blank');
        win.focus();
    }
</script>