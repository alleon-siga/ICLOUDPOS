<?php $ruta = base_url(); ?>
<?php $md = get_moneda_defecto() ?>
<style type="text/css">
    table td {
        width: 100%;
        border: #e1e1e1 1px solid;
        font-size: 9px;
    }

    thead, th {
        background: #585858;
        border: #111 1px solid;
        color: #fff;
        font-size: 10px;
    }

    h4, h5 {
        margin: 0px;
    }

    table tfoot tr td {
        font-weight: bold;
    }
</style>
<h4 style="text-align: center;">Reporte hoja de colecta</h4>
<h4 style="text-align: center;">Desde <?= date('d/m/Y', strtotime($fecha_ini)) ?> al <?= date('d/m/Y', strtotime($fecha_fin)) ?> 
    Hora: <?= date('H:i:s') ?></h4>

<h5>EMPRESA: <?= valueOption('EMPRESA_NOMBRE') ?></h5>
<h5>DIRECCI&Oacute;N: <?= $local_direccion ?></h5>
<h5>SUCURSAL: <?= $local_nombre ?></h5>
<table>
    <thead>
        <tr>
            <th># Venta</th>
            <th>Fecha</th>
            <th>Local</th>
            <th>Usuario</th>
            <th>Cliente</th>
            <th># Comprobante</th>
            <th>Producto</th>
            <th>Estado</th>
            <th>Operador</th>
            <th>Condici&oacute;n</th>
            <th>Precio unitario</th>
            <th>Importe</th>
        </tr>
    </thead>
    <tbody>
    <?php
        $suma = 0;
        foreach ($countLists as $count) {
            $suma += $count->detalle_importe;
        }
    ?>
    <?php
        $venta_id_ant = '';
        foreach ($lists as $list):
    ?>
    <?php
        $debe = 0;
        $estado = 'Cancelado';
        if($list->condicion_pago == 2){
            $debe = $list->monto_restante;
            if($debe == 0 && !empty($debe)){ //cuando no hay deuda
                $estado = 'Cancelado';
            }elseif(empty($debe)){ //cuando no hay ningun pago
                $debe = $list->total;
                $estado = 'Debe';
            }else{ //cuando hay deuda
                $estado = 'Debe';
            }
        }
    ?>
        <tr>
            <td><?= $list->venta_id ?></td>
            <td><?= date('d/m/Y H:i', strtotime($list->fecha)) ?></td>
            <td><?= $list->local_nombre ?></td>
            <td><?= $list->nombre ?></td>
            <td><?= $list->razon_social ?></td>
            <td><?= $list->abr_doc . ' ' . $list->serie . '-' . sumCod($list->numero, 6) ?></td>
        <?php if($venta_id_ant != $list->venta_id){ ?>
            <td><?= $list->producto_nombre.' '.$list->nota ?></td>
        <?php }else{ ?>
            <td><?= $list->producto_nombre ?></td>
        <?php } ?>
            <td><?= $estado ?></td>
            <td><?= $list->valor ?></td>
            <td><?= $list->condicion ?></td>
            <td style="text-align: right;"><?= $list->simbolo ?> <?= number_format($list->precio, 2) ?></td>
            <td style="text-align: right;"><?= $list->simbolo ?> <?= number_format($list->detalle_importe, 2) ?></td>
        </tr>
    <?php
        $venta_id_ant = $list->venta_id;
        endforeach;
    ?>
    </tbody>
    <tfoot>
        <?php
            $totalEfectivo = $totalBanco = 0;
            if(!empty($totalesCon)){
                foreach($totalesCon as $totalCon){
                    if($totalCon->medio_pago==3){ //efectivo
                        $totalEfectivo += $totalCon->saldo;
                    }else{
                        $totalBanco += $totalCon->saldo;
                    }
                }
            }
            foreach ($totalesCre as $totalCre) {
                if($totalCre->medio_pago==3){ //efectivo
                    $totalEfectivo += $totalCre->saldo;    
                }else{
                    $totalBanco += $totalCre->saldo;
                }
            }
        ?>
        <tr>
            <td colspan="11" style="text-align: right;"><b>TOTAL EFECTIVO</b></td>
            <td style="text-align: right;"><?= $md->simbolo.' '.number_format($totalEfectivo, 2) ?></td>
        </tr>
        <tr>                      
            <td colspan="11" style="text-align: right;"><b>TOTAL BANCARIZADO</b></td>
            <td style="text-align: right;"><?= $md->simbolo.' '.number_format($totalBanco, 2) ?></td>
        </tr>
        <tr>
            <td colspan="11" style="text-align: right;"><b>TOTAL CREDITO</b></td>
            <td style="text-align: right;">
            <?php
                echo $md->simbolo.' '.number_format($suma - $totalEfectivo - $totalBanco,2);
                /*if(isset($totalesCre->saldo)){
                    echo $md->simbolo.' '.number_format($totalesCre->saldo, 2);
                }else{
                    echo $md->simbolo.' '.number_format(0, 2);
                }*/
            ?>
            </td>
        </tr>
        <tr>
            <td colspan="11" style="text-align: right;"><b>TOTAL VENTAS</b></td>
            <td style="text-align: right;"><?= !empty($list->simbolo)? $list->simbolo : $md->simbolo ?> <?= number_format($suma, 2) ?></td>
        </tr>
    </tfoot>
</table>
