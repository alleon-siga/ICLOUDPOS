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
<?php
foreach ($lists as $ingreso):
    ?>
    <div class="table-responsive">
        <table class='table dataTable table-bordered no-footer tableStyle' style="overflow:scroll">
            <thead>
                <tr>
                    <th colspan="7">Producto : <?= $ingreso->producto_nombre ?></th>
                    <th colspan="3">Compras</th>
                    <th colspan="4">Ventas</th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= $ingreso->local_nombre ?></td>
                    <td><?= $ingreso->producto_nombre ?></td>
                    <td><?= $ingreso->nombre_unidad ?></td>
                    <td style="text-align: right;"><?= number_format($costoCompraSi, 2) ?></td>
                    <td style="text-align: right;"><?= number_format($impCompra, 2) ?></td>
                    <td style="text-align: right;"><?= number_format($costoCompraImp, 2) ?></td>
                    <td style="text-align: right;"><?= number_format($impuesto, 2) ?></td>
                    <td style="text-align: right;"><?= number_format($costoVentaSi, 2) ?></td>
                    <td style="text-align: right;"><?= number_format($costoVenta, 2) ?></td>
                    <td style="text-align: right;"><?= number_format($costoTotal, 2) ?></td>
                    <td style="text-align: right;"><?= $cantidad ?></td>
                    <td style="text-align: right;"><?= number_format($subtotal, 2) ?></td>
                    <td style="text-align: right;"><?= number_format($impVenta, 2) ?></td>
                    <td style="text-align: right;"><?= number_format($precioVenta, 2) ?></td>
                    <td style="text-align: right;"><?= number_format($utilidadXund, 2) ?></td>
                    <td style="text-align: right;"><?= number_format($utilidadTotal, 2) ?></td>
                </tr>

            </tbody>
        </table>
    </div>
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