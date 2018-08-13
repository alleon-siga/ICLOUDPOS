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
<h4 style="text-align: center;">Reporte de cuentas por cobrar detallado</h4>
<!--<h4 style="text-align: center;">Desde <? //date('d/m/Y', strtotime($fecha_ini)) ?>
    al <? //date('d/m/Y', strtotime($fecha_fin)) ?></h4>-->

<h5>EMPRESA: <?= valueOption('EMPRESA_NOMBRE') ?></h5>
<h5>DIRECCI&Oacute;N: <?= $local_direccion ?></h5>
<h5>SUCURSAL: <?= $local_nombre ?></h5>
<table border="0">
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
