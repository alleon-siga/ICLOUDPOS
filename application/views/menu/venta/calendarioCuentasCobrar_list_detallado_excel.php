<?php
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=cuentasxcobrar_detallado.xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<h4 style="text-align: center; margin: 0;">Reporte de cuentas por cobrar detallado</h4>
<!--<h4 style="text-align: center; margin: 0;">Desde <? //date('d/m/Y', strtotime($fecha_ini)) ?>
    al <? // date('d/m/Y', strtotime($fecha_fin)) ?></h4>-->

<h5 style="margin: 0;">EMPRESA: <?= valueOption('EMPRESA_NOMBRE') ?></h5>
<h5 style="margin: 0;">DIRECCI&Oacute;N: <?= $local_direccion ?></h5>
<h5 style="margin: 0;">SUCURSAL: <?= $local_nombre ?></h5>
<table border="1">
  <thead>
    <tr>
        <th># Venta</th>
        <th>F. Venta</th>
        <th># Comprobante</th>
        <th>Cliente</th>
        <th>Importe Venta</th>
        <th>Valor Cuota</th>
        <th>Importe Abonado</th>
        <th>Pendiente Pago</th>
        <th>Nro Cuota</th>
        <th>Cuotas Atrasadas</th>
        <th>F. Vencimiento</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($tablas as $tabla): ?>
    <tr>
        <td><?= $tabla->venta_id  ?></td>
        <td><?= date("d/m/Y", strtotime($tabla->fecha)) ?></td>
        <td>
        <?php
          if ($tabla->numero != ''){
            echo $tabla->des_doc . ' ' . $tabla->serie . '-' . sumCod($tabla->numero, 6);
          }else{
            echo '<span style="color: #0000FF">NO EMITIDO</span>';
          }
        ?>                  
        </td>
        <td><?= $tabla->razon_social ?></td>
        <td><?= $tabla->simbolo.' '.$tabla->total ?></td>
        <td><?= $tabla->simbolo.' '.$tabla->monto ?></td>
        <td><?= $tabla->simbolo ?> <?= empty($tabla->monto_abono)? 0 : $tabla->monto_abono ?></td>
        <td><?= $tabla->simbolo ?> <?= empty($tabla->monto_restante)? 0 : $tabla->monto_restante ?></td>
        <td><?php $a = explode("/", $tabla->nro_letra); echo $a[0];  ?></td>
        <td>
        <?php
          $fs = strtotime(date('Y-m-d'));
          $fv = strtotime($tabla->fecha_vencimiento);
          if($fs >= $fv){
            echo '1';
          }else{
            echo '0';
          }
        ?>
        </td>
        <td><?= date("d/m/Y", strtotime($tabla->fecha_vencimiento))  ?></td>
    </tr>
    <?php endforeach ?>
  </tbody>
</table>
