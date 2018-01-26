    <?php $md = get_moneda_defecto()?>
<?php if(count($ingresos) > 0):?>
   <br>
    <div class="row">
        <div class="col-md-6"></div>
        <div class="col-md-2">
            <label>Subtotal: <?= $md->simbolo ?> <span id="subtotal"><?=number_format($ingreso_totales->subtotal, 2)?></span></label>
        </div>
        <div class="col-md-2">
            <label>IGV: <?= $md->simbolo ?> <span id="impuesto"><?=number_format($ingreso_totales->impuesto, 2)?></span></label>
        </div>
        <div class="col-md-2">
            <label>Total: <?= $md->simbolo ?> <span id="total"><?=number_format($ingreso_totales->total, 2)?></span></label>
        </div>
    </div>
     <div class="table-responsive" id="tabla">
        
   
<table class="table table-striped dataTable table-bordered tableStyle" id="tablaresultado">
    <thead>
        <tr>
            <th>ID</th>
            <th>Fecha Doc</th>
            <th>Doc</th>
            <th>Num Doc</th>
            <th>RUC Provedor</th>
            <th>Proveedor</th>
            <th>Tipo Pago</th>
            <th>Moneda</th>
            <th>Tipo Cambio</th>
            <th>SubTotal</th>
            <th>IGV</th>
            <th>Total</th>
            <th>Estado</th>
            <th>Usuario</th>
            <th>fec Registro</th>
            <th>Ver</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach($ingresos as $ingreso):?>
        <tr>
        <td><?=$ingreso->id?></td>
        <td><?=$ingreso->estado == 'COMPLETADO' ? date('d/m/Y', strtotime($ingreso->fecha_emision)) : ''?></td>
        <td><?=$ingreso->documento?></td>
        <td><?=$ingreso->documento_numero?></td>
        <td><?=$ingreso->proveedor_ruc?></td>
        <td><?=$ingreso->proveedor_nombre?></td>
        <td><?=$ingreso->tipo_pago?></td>
        <td>Soles</td>
        <td><?=$ingreso->tasa?></td>
        <td><?=number_format($ingreso->subtotal, 2)?></td>
        <td><?=number_format($ingreso->impuesto, 2)?></td>
        <td><?=number_format($ingreso->total, 2)?></td>
        <td><?=$ingreso->estado?></td>
        <td><?=$ingreso->usuario_nombre?></td>
        <td><?=date('d/m/Y', strtotime($ingreso->fecha_registro))?></td>
        <td>
        <a href="#" onclick="verCompra('<?=$ingreso->id?>');">
            <i class="fa fa-search"></i>
        </a>
        </td>
        </tr>
    <?php endforeach;?>
    </tbody>
</table>
 </div>

<script type="text/javascript">
    $(document).ready(function(){
        TablesDatatables.init();
    });

    function verCompra(id){

        $('#ver_compra').html($('#load_div').html());
        $('#ver_compra').modal('show');
        $("#ver_compra").load('<?= base_url()?>ingresos/form/' + id);
        
    }
</script>
<?php else:?>
    <h5>No se encontraron resultados</h5>
<?php endif;?>