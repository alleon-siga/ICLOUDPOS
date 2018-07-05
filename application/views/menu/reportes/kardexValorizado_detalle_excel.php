<?php
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=kardex.xls");
header("Content-Language: es");
header("Pragma: no-cache");
header("Expires: 0");
?>

<table>
    <tr>
        <td colspan="17" style="text-align: center;">FORMATO 12.1: "REGISTRO DEL INVENTARIO PERMANENTE EN UNIDADES F&Iacute;SICAS- DETALLE DEL INVENTARIO PERMANENTE EN UNIDADES F&Iacute;SICAS</td>
    </tr>
    <tr>
        <td colspan="4">PER&Iacute;ODO:</td>
        <td colspan="13" align="left"><?=getMes($mes)?> <?=$year?></td>
    </tr>
    <tr>
        <td colspan="4">EMPRESA:</td>
        <td colspan="13"><?=valueOption('EMPRESA_NOMBRE')?></td>
    </tr>
    <tr>
        <td colspan="4">ESTABLECIMIENTO:</td>
        <td colspan="13"><?=$local->local_nombre?></td>
    </tr>
    <tr>
        <td colspan="4">CR&Oacute;DIGO DE LA EXISTENCIA:</td>
        <td colspan="13">CODIGO INTERNO DEL PRODUCTO</td>
    </tr>
    <tr>
        <td colspan="4">TIPO:</td>
        <td colspan="13">MERCADERIA</td>
    </tr>
    <tr>
        <td colspan="4">DESCRIPCI&Oacute;N::</td>
        <td colspan="13"><?=getCodigoValue(sumCod($producto->producto_id), $producto->producto_codigo_interno)." - ".$producto->producto_nombre?></td>
    </tr>
    <tr>
        <td colspan="4">C&Oacute;DIGO DE LA UNIDAD DE MEDIDA:</td>
        <td colspan="13"><?=$unidad?></td>
    </tr>
    <tr>
        <th colspan="17" style="text-align: center;">DOCUMENTO DE TRASLADO, COMPROBANTE DE PAGO, DOCUMENTO INTERNO O SIMILAR</th>
    </tr>
</table>
<table>
    <thead>
        <tr>
            <th rowspan="2" width="5%">Id</th>
            <th rowspan="2" width="10%">Fecha Registro</th>
            <th rowspan="2" width="10%">Tipo</th>
            <th rowspan="2" width="10%">Serie</th>
            <th rowspan="2" width="10%">Numero</th>
            <th rowspan="2" width="10%">Tipo de Operacion</th>
            <th rowspan="2" width="10%">Usuario</th>
            <th rowspan="2" width="10%">Referencia</th>
            <th colspan="3">Entradas</th>
            <th colspan="3">Salidas</th>
            <th colspan="3">Saldo Final</th>
        </tr>
        <tr>
            <th>Cantidad</th>
            <th>Costo Unit.</th>
            <th>Costo Total</th>
            <th>Cantidad</th>
            <th>Costo Unit.</th>
            <th>Costo Total</th>
            <th>Cantidad</th>
            <th>Costo Unit.</th>
            <th>Costo Total</th>
        </tr>
    </thead>
    <tbody>
    <?php
        if(empty($kardex_ant)){
            $finalCant = $finalCu = $finalCt = 0;
        }else{
            $finalCant = $kardex_ant->cantidad_saldo;
            $finalCu = $kardex_ant->costo;
            $finalCt = $finalCant * $finalCu;
        }
        if(!empty($kardex)){
    ?>                    
        <tr>
            <td></td>
            <td></td>
            <td>Otros</td>
            <td></td>
            <td></td>
            <td>SALDO ANTERIOR</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td style="text-align: right;"><?= $finalCant ?></td>
            <td><?= $kardex[0]->simbolo.' '.number_format($finalCu, 2) ?></td>
            <td><?= $kardex[0]->simbolo.' '.number_format($finalCt, 2) ?></td>
        </tr>
    <?php
        }
        foreach ($kardex as $k):
            if($k->io == 1){
                $finalCant += $k->cantidad;
                $finalCt += $k->cantidad * $k->costo;
            }else{
                $finalCant -= $k->cantidad;
                $finalCt -= $k->cantidad * $k->costo;
            }
            $finalCu = $finalCt / $finalCant;
    ?>
        <tr>
            <td style="white-space: normal;"><?= $k->id ?></td>
            <td style="white-space: normal;"><?= date('d/m/Y H:i:s', strtotime($k->fecha)) ?></td>
            <?php $tipo = get_tipo_doc($k->tipo) ?>
            <td style="white-space: normal;"><?= $tipo['value'] ?></td>
            <td style="white-space: normal;"><?= $k->serie ?></td>
            <td style="white-space: normal;"><?= $k->numero ?></td>
            <?php $operacion = get_tipo_operacion($k->operacion) ?>
            <td style="white-space: normal;"><?= $operacion['value'] ?></td>
            <td style="white-space: normal;"><?= $k->username ?></td>
            <td style="white-space: normal;"><?= $k->ref_val ?></td>
            <?php if($k->io == 1){ ?>
                <td style="text-align: right;"><?php if($k->producto_cualidad=='MEDIBLE'){ echo bcdiv($k->cantidad,1,0); }else{ echo $k->cantidad; } ?></td>
                <td><?= $k->simbolo.' '.number_format($k->costo, 2) ?></td>
                <td><?= $k->simbolo.' '.number_format($k->cantidad * $k->costo, 2) ?></td>
            <?php }else{ ?>
                <td></td>
                <td></td>
                <td></td>
            <?php } ?>
            <?php if($k->io == 2){ ?>
                <td style="text-align: right;"><?php if($k->producto_cualidad=='MEDIBLE'){ echo bcdiv($k->cantidad,1,0); }else{ echo $k->cantidad; } ?></td>
                <td><?= $k->simbolo.' '.number_format($k->costo, 2) ?></td>
                <td><?= $k->simbolo.' '.number_format($k->cantidad * $k->costo, 2) ?></td>
            <?php }else{ ?>
                <td></td>
                <td></td>
                <td></td>
            <?php } ?>
            <td style="text-align: right;"><?= $finalCant ?></td>
            <td><?= $k->simbolo.' '.number_format($finalCu, 2) ?></td>
            <td><?= $k->simbolo.' '.number_format($finalCt, 2) ?></td>
        </tr>
    <?php
        endforeach;
    ?>
    </tbody>
</table>
