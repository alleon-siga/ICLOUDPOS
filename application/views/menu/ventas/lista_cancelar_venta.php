<div class="table-responsive">
    <table class='table table-striped dataTable table-bordered' id="tablaresult">
        <thead>
        <tr>
            <th>Secci&oacute;n</th>
            <th>Documento</th>
            <th>Num Document.</th>
            <th>Fecha </th>
            <th>Cliente</th>
            <th>Condici&oacute;n</th>
            <th>Vendedor</th>
            <th>Moneda</th>
            <th>Tip. Camb. </th>
            <th>Total</th>
            <th>Ver</th>

        </tr>
        </thead>
        <tbody>
        <?php if (count($ventas) > 0): ?>
            <?php
            foreach ($ventas as $venta): ?>
                <tr>

                    <td class='actions_big' style="width: 5%" id="id<?php echo  $venta->venta_id; ?>">

                        <!-- <div class="btn-group">
                                                        <a onclick="anular(<?php // echo $venta->venta_id; ?>)"
                                                           class='btn btn-default'><i class="fa fa-remove"></i> Anular</a>
                                                    </div>  -->
                        <!-- <input type="hidden" name="locales[]" class="form-control check_venta" value="<?php echo  $venta->local_id; ?>">
                                                <input type="checkbox" name="venta[]" nro="<?php echo $venta->documento_Serie."-".$venta->documento_Numero; ?>" class="form-control check_venta" value="<?php echo  $venta->venta_id; ?>">-->
                        <div class="btn-group" align="center">
                            <button type="button" onclick="anular('<?php echo $venta->documento_Serie."-".$venta->documento_Numero; ?>',<?php echo $venta->venta_id; ?>)"
                                    class='btn btn-primary'>Anular</button>
                        </div>
                    </td>
                    <td style="text-align: center;">
                    <?php
                    if($venta->id_doc==1) echo "FA";
                    if($venta->id_doc==2) echo "NC";
                    if($venta->id_doc==3) echo "BO";
                    if($venta->id_doc==4) echo "GR";
                    if($venta->id_doc==5) echo "PCV";
                    if($venta->id_doc==6) echo "NV";
                    ?>
                    </td>
                    <td><?php echo $venta->documento_Serie."-".$venta->documento_Numero; ?></td>
                    <td style="text-align: center;"><span style="display: none"><?= date('YmdHis', strtotime($venta->fecha)) ?></span><?php echo date("d-m-Y H:i:s", strtotime($venta->fecha)); ?></td>
                    <td><?php echo $venta->razon_social; ?></td>
                    <td style="text-align: center;" ><?= $venta->nombre_condiciones ?></td>
                    <td style="text-align: center;"><?php echo $venta->nombre; ?></td>
                    <td style="text-align: center;"><?php echo $venta->nombre_moneda; ?></td>
                    <td style="text-align: center;"><?= $venta->tasa_cambio ?></td>
                    <td style="text-align: center;"><?php echo $venta->total; ?></td>
                    <td style="text-align: center;">

                        <?php
                        echo '<a class="btn btn-default btn-default btn-default" data-toggle="tooltip"
                                            title="Ver" data-original-title="Ver"
                                            href="#" onclick="ver(' . $venta->venta_id . ');">'; ?>
                        <i class="fa fa-search"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else : ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<script type="text/javascript">
    $(function () {

        TablesDatatables.init(3);

    });


</script>