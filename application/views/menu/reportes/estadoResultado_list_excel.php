<?php
    header("Content-type: application/octet-stream");
    header("Content-Disposition: attachment; filename=estado_resultados.xls");
    header("Pragma: no-cache");
    header("Expires: 0");
?>
<h4 style="text-align: center; margin: 0;">Estado de Resultados</h4>
<h4 style="text-align: center; margin: 0;"><?= getMes($mes).' '.$year ?></h4>
<h5 style="margin: 0;">EMPRESA: <?= valueOption('EMPRESA_NOMBRE') ?></h5>
<h5 style="margin: 0;">DIRECCI&Oacute;N: <?= $local_direccion ?></h5>
<h5 style="margin: 0;">SUCURSAL: <?= $local_nombre ?></h5>
<table width="60%" cellpadding="0" cellspacing="0" align="center">
    <tbody>
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
    </tbody>
</table>
