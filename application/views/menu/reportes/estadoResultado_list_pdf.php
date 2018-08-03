<?php $ruta = base_url(); ?>
<h4 style="text-align: center;">ESTADO DE RESULTADOS</h4>
<h4 style="text-align: center;"><?= strtoupper(getMes($mes).' '.$year) ?></h4>
<h4>EMPRESA: <?= valueOption('EMPRESA_NOMBRE') ?></h4>
<h4>DIRECCI&Oacute;N: <?= $local_direccion ?></h4>
<h4>SUCURSAL: <?= $local_nombre ?></h4>
<table width="80%" cellpadding="2" cellspacing="2" align="center">
    <tr>
        <td>INGRESO VENTAS</td>
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
        if($gasto['nom_grupo_gastos']=='GASTO DE SERVICIOS' || $gasto['nom_grupo_gastos']=='PLANILLA' || $gasto['nom_grupo_gastos']=='GASTO DE VENTA' || $gasto['nom_grupo_gastos']=='GASTO ADMINISTRATIVO'){
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
        if($gasto['nom_grupo_gastos']=='GASTO FINANCIERO'){
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