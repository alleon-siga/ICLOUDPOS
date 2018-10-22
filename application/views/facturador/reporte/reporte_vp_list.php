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

<table class="table">
    <thead>
        <tr style="border-color: transparent !important;">
            <th colspan="3" class="thvacio thblack"></th>
            <th colspan="3" class="thblack">Cant. x Tipo de Doc. </th>
            <th class="thvacio thblack"></th>
            <th colspan="3" class="thblack">Montos x Documentos</th>
            <th class="thvacio thblack"></th>
        </tr>
        <tr>
            <th class="thblack">Codigo</th>
            <th class="thblack">Producto</th>                
            <th class="thblack">Marca</th>
            <th class="CellWithComment thblack">NC  <span class="CellComment">Nota de Compra</span></th>
            <th class="CellWithComment thblack">BO  <span class="CellComment">Boleta</span></th>
            <th class="CellWithComment thblack">Fa <span class="CellComment">Factura</span></th>
            <th class="thblack">Cantidad Total</th>
            <th class="CellWithComment thblack">NC  <span class="CellComment">Nota de Compra</span></th>
            <th class="CellWithComment thblack">BO  <span class="CellComment">Boleta</span></th>
            <th class="CellWithComment thblack">Fa <span class="CellComment">Factura</span></th>
            <th class="thblack">Total</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($lists as $ingreso):
            ?>
            <tr class="trblack">
                <td ><?= $ingreso->codigo_producto ?></td>
                <td ><?= $ingreso->nombre_producto ?></td>
                <td ><?= $ingreso->marca_producto != "" ? $ingreso->marca_producto : "SIN MARCA" ?></td>
                <td ><?= $ingreso->ven_nv != 0 ? number_format($ingreso->ven_nv, 0) : number_format(0, 0) ?></td>
                <td ><?= $ingreso->ven_bol ?></td>
                <td ><?= $ingreso->ven_fac ?></td>
                <td ><?= $ingreso->ven_total ?> </td>
                <td ><?= $ingreso->ven_nv_t != 0 ? number_format($ingreso->ven_nv_t, 2) : number_format(0, 2) ?></td>
                <td > <?= $ingreso->ven_bol_t != 0 ? number_format($ingreso->ven_bol_t, 2) : number_format($ingreso->ven_bol_t, 2) ?></td>
                <td ><?= $ingreso->ven_fac_t != 0 ? number_format($ingreso->ven_fac_t, 2) : number_format($ingreso->ven_fac_t, 2) ?></td>
                <td ><?= $ingreso->ven_tot_t != 0 ? number_format($ingreso->ven_tot_t, 2) : number_format($ingreso->ven_tot_t, 2) ?></td>   
            </tr>
        <?php endforeach; ?>
    </tbody>

</table>

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
                'estado_cr_id': $("#estado_cr_id").val(),
                'moneda_id': $("#moneda_id").val(),
                'fecha': $("#fecha").val(),
                'tipo_reporte': $("#tipo_reporte").val(),
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
            'estado_cr_id': $("#estado_cr_id").val(),
            'fecha': $("#fecha").val(),
            'tipo_reporte': $("#tipo_reporte").val(),
            'producto_id': $("#producto_id").val(),
            'grupo_id': $("#grupo_id").val(),
            'marca_id': $("#marca_id").val(),
            'linea_id': $("#linea_id").val(),
            'familia_id': $("#familia_id").val()
        };

        var win = window.open('<?= base_url() ?>facturador/reporte/reporte_vp/pdf?data=' + JSON.stringify(data), '_blank');
        win.focus();
    }

    function exportar_excel() {
        var data = {
            'local_id': $("#local_id").val(),
            'estado_cr_id': $("#estado_cr_id").val(),
            'moneda_id': $("#moneda_id").val(),
            'fecha': $("#fecha").val(),
            'tipo_reporte': $("#tipo_reporte").val(),
            'producto_id': $("#producto_id").val(),
            'grupo_id': $("#grupo_id").val(),
            'marca_id': $("#marca_id").val(),
            'linea_id': $("#linea_id").val(),
            'familia_id': $("#familia_id").val()
        };

        var win = window.open('<?= base_url() ?>facturador/reporte/reporte_vp/excel?data=' + JSON.stringify(data), '_blank');
        win.focus();
    }
</script>