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
<?
    /*echo "<pre>";
    echo print_r($lists);
    echo "</pre>";*/
?>
<div class="table-responsive">
    <table class='table dataTable table-bordered no-footer tableStyle' style="overflow:scroll">
        <thead>
            <tr>
                <th>Id</th>
                <th>Nombre</th>
                <th>Compras</th>
                <th>Entrada</th>
                <th>Traspaso</th>
                <th>Total entrada</th>
                <th>Kardex</th>

                <th>Ventas</th>
                <th>Salida</th>
                <th>Traspaso</th>
                <th>Total salida</th>
                <th>Kardex</th>

                <th>E - S</th>
                <th>Stock</th>
                <th>KE - KS</th>
                <th>Diferencia</th>
                <th>Observaci&oacute;n</th>
            </tr>
        </thead>
        <tbody>
    <?php
        foreach ($lists as $dato):
            $totalEntrada = $dato->compra + $dato->entrada + $dato->traspasoE;
            $totalSalida = $dato->venta + $dato->salida + $dato->traspasoS;
            $entradaSalida = $totalEntrada - $totalSalida;
            $kardex = $dato->kardexE - $dato->kardexS;
            $diferencia = $entradaSalida - $dato->stock;

            $mensaje = $color = '';
            if($entradaSalida!=$dato->stock){
                $mensaje = "Inconsistente";
                $color = "red";
            }

            if($entradaSalida==$dato->stock && $inconsistencia=='1'){
                continue;
            }
    ?>
            <tr>
                <td style="text-align: right; color:<?= $color ?>"><?= $dato->producto_id ?></td>
                <td style="text-align: left; color:<?= $color ?>"><?= $dato->producto_nombre ?></td>

                <td style="text-align: right; color:<?= $color ?>"><?= number_format($dato->compra, 0) ?></td>
                <td style="text-align: right; color:<?= $color ?>"><?= number_format($dato->entrada, 0) ?></td>
                <td style="text-align: right; color:<?= $color ?>"><?= number_format($dato->traspasoE, 0) ?></td>
                <td style="text-align: right; color:<?= $color ?>"><?= number_format($totalEntrada, 0) ?></td>
                <td style="text-align: right; color:<?= $color ?>"><?= number_format($dato->kardexE, 0) ?></td>

                <td style="text-align: right; color:<?= $color ?>"><?= number_format($dato->venta, 0) ?></td>
                <td style="text-align: right; color:<?= $color ?>"><?= number_format($dato->salida, 0) ?></td>
                <td style="text-align: right; color:<?= $color ?>"><?= number_format($dato->traspasoS, 0) ?></td>
                <td style="text-align: right; color:<?= $color ?>"><?= number_format($totalSalida, 0) ?></td>
                <td style="text-align: right; color:<?= $color ?>"><?= number_format($dato->kardexS, 0) ?></td>
                
                <td style="text-align: right; color:<?= $color ?>"><?= number_format($entradaSalida, 0) ?></td>
                <td style="text-align: right; color:<?= $color ?>"><?= number_format($dato->stock, 0) ?></td>
                <td style="text-align: right; color:<?= $color ?>"><?= number_format($kardex, 0) ?></td>
                <td style="text-align: left; color:<?= $color ?>"><?= $diferencia ?></td>
                <td style="text-align: left; color:<?= $color ?>"><?= $mensaje ?></td>
            </tr>
    <?php
        endforeach;
    ?>
        </tbody>
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
<script type="text/javascript">
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
        var inconsistencia = 0;
        if($("#inconsistencia").prop('checked')){
            inconsistencia = 1;
        }

        var data = {
            'local_id': $("#local_id").val(),
            'producto_id': $("#producto_id").val(),
            'inconsistencia': inconsistencia
        };

        var win = window.open('<?= base_url()?>reporte_inventario/verificaInventario/pdf?data=' + JSON.stringify(data), '_blank');
        win.focus();
    }

    function exportar_excel() {
        var inconsistencia = 0;
        if($("#inconsistencia").prop('checked')){
            inconsistencia = 1;
        }

        var data = {
            'local_id': $("#local_id").val(),
            'producto_id': $("#producto_id").val(),
            'inconsistencia': inconsistencia
        };

        var win = window.open('<?= base_url()?>reporte_inventario/verificaInventario/excel?data=' + JSON.stringify(data), '_blank');
        win.focus();
    }
</script>