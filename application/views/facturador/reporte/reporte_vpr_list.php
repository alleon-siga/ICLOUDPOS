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
<?php foreach ($lists as $ingreso) {
    $nombre_p=$ingreso->producto_nombre; 
} ?>
<table class="table">

    <thead>
        <tr style="border-color: transparent !important;">
            <th colspan="5" class="thblack text-left"> <?= !empty($nombre_p) ? 'PRODUCTO:'.$nombre_p:'PRODUCTO: NO HAY REGISTRO' ?></th>
            <th colspan="5" class="thvacio"></th>
        </tr>
        <tr>
            <th class="thblack">Ruc / Dni</th>
            <th class="thblack">Cliente</th>                
            <th class="thblack">Tipo Doc</th>
            <th class="thblack"># Doc.</th>
            <th class="thblack">Cant.</th>
            <th class="thblack">Costo Unitario</th>
            <th class="thblack">IGV</th>
            <th class="thblack">Total</th>
            <th class="thblack">Fecha</th>
            <th class="thblack">Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($lists as $ingreso):
            ?>
            <tr class="trblack">
                <td ><?= $ingreso->cliente_identificacion > 0 ? $ingreso->cliente_identificacion :"SIN DOCUMENTO" ?></td>
                <td ><?= $ingreso->cliente_nombre ?></td>
                <td ><?= $ingreso->des_doc ?></td>
                <td ><?= $ingreso->documento_numero ?></td>
                <td ><?= $ingreso->cant .' UND' ?></td>
                <td ><?= $ingreso->costo_unitario ?></td>
                <td ><?= $ingreso->igv ?> </td>
                <td ><?= $ingreso->total ?></td>
                <td ><?= $ingreso->fecha ?></td>
                <td class="text-center"><button class="btn btn-sm btn-info"><i class="fa fa-search"></i></button></td>
            </tr>

<?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            
        </tr>
    </tfoot>
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