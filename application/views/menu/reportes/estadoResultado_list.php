<?php $ruta = base_url(); ?>
<?
    /*echo "<pre>";
    echo print_r($lists);
    echo "</pre>";*/
?>
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <table class="table table-responsive table-bordered" >
            <tr>
                <td>VENTAS</td>
                <td style="text-align: right;"><?= $lists['simbolo'].' '.number_format($lists['ventas'], 2) ?></td>
            </tr>
            <tr>
                <td>COSTO DE VENTAS</td>
                <td style="text-align: right;"><?= $lists['simbolo'].' '.number_format($lists['costo'], 2) ?></td>
            </tr>
            <tr>
                <td style="background-color: #cccccc; font-weight: bold;">MARGEN BRUTO</td>
                <td style="background-color: #cccccc; font-weight: bold; text-align: right;"><?= $lists['simbolo'].' '.number_format($lists['margen_bruto'], 2) ?></td>
            </tr>
        <?php
            $x=1;
            foreach ($lists['gastos'] as $gasto) {
                if($x>2) break;
        ?>
            <tr>
                <td style="background-color: #e6e6e6; text-align: center;"><?= $gasto['nom_grupo_gastos'] ?></td>
                <td style="background-color: #e6e6e6; text-align: right;"><?= $lists['simbolo'].' '.number_format($gasto['suma'], 2) ?></td>
            </tr>
        <?php
                foreach ($gasto['nom'] as $tipo) {
        ?>
            <tr>
                <td style="text-align: right;"><?= strtoupper($tipo['nombre_tipos_gasto']) ?></td>
                <td style="text-align: right;"><?= $lists['simbolo'].' '.number_format($tipo['suma'], 2) ?></td>
            </tr>
        <?php
                }
                $x++;
            }
        ?>
            <tr>
                <td style="background-color: #cccccc; font-weight: bold;">UTILIDAD OPERATIVA</td>
                <td style="background-color: #cccccc; font-weight: bold; text-align: right;"><?= $lists['simbolo'].' '.number_format($lists['utilidad'], 2) ?></td>
            </tr>
        <?php
            $x = 1;
            foreach ($lists['gastos'] as $gasto) {
                if($x>2){
        ?>
            <tr>
                <td style="background-color: #e6e6e6; text-align: center;"><?= strtoupper($gasto['nom_grupo_gastos']) ?></td>
                <td style="background-color: #e6e6e6; text-align: right;"><?= $lists['simbolo'].' '.number_format($gasto['suma'], 2) ?></td>
            </tr>
        <?php
                    foreach ($gasto['nom'] as $tipo) {
        ?>
            <tr>
                <td style="text-align: right;"><?= $tipo['nombre_tipos_gasto'] ?></td>
                <td style="text-align: right;"><?= $lists['simbolo'].' '.number_format($tipo['suma'], 2) ?></td>
            </tr>
        <?php
                    }
                }
                $x++;
            }
        ?>
            <tr>
                <td style="background-color: #cccccc; font-weight: bold;">UTILIDAD ANTES DE IMPUESTOS</td>
                <td style="background-color: #cccccc; font-weight: bold; text-align: right;"><?= $lists['simbolo'].' '.number_format($lists['utilidad_si'], 2) ?></td>
            </tr>
            <tr>
                <td style="background-color: #e6e6e6; text-align: center;">IMPUESTO A LA RENTA </td>
                <td style="background-color: #e6e6e6; font-weight: bold; text-align: right;"><?= $lists['simbolo'].' '.number_format($lists['impuesto'], 2) ?></td>
            </tr>
            <tr>
                <td style="background-color: #cccccc; font-weight: bold;">UTILIDAD NETA</td>
                <td style="background-color: #cccccc; font-weight: bold; text-align: right;"><?= $lists['simbolo'].' '.number_format($lists['utilidad_neta'], 2) ?></td>
            </tr>
        </table>
    </div>
    <div class="col-md-2"></div>
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
        <br><br>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
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
            'year': $("#year").val(),
            'mes': $("#mes").val(),
            'moneda_id': $("#moneda_id").val()
        };

        var win = window.open('<?= base_url()?>reporte/estadoResultado/pdf?data=' + JSON.stringify(data), '_blank');
        win.focus();
    }

    function exportar_excel() {
        var data = {
            'local_id': $("#local_id").val(),
            'year': $("#year").val(),
            'mes': $("#mes").val(),
            'moneda_id': $("#moneda_id").val()
        };

        var win = window.open('<?= base_url()?>reporte/estadoResultado/excel?data=' + JSON.stringify(data), '_blank');
        win.focus();
    }
</script>