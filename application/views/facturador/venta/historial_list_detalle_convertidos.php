<style>
    .totales {
        width: 100%;
        text-align: right;
    }

    .totales tr td {
        padding: 5px 0;
        font-weight: bold;
    }
</style>
<?php
foreach ($venta as $v) {
    $vdoc = $v->vdoc;
    $vnom = $v->vnom;
    $vmon = $v->vmon;
    $vcon = $v->vcon;
    $vser = $v->vser;
    $vnum = $v->vnum;
    $vven = $v->vven;
    $vfecha = $v->vfecha;
    $vcon = $v->vcon;
    $vtasa = $v->vtasa;
    $vtotal=$v->vtotal;
}
?>
<div class="modal-dialog" style="width: 60%">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Detalles de la Venta Convertido</h3>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-2"><label class="control-label">Venta Nro:</label></div>
                <div class="col-md-3"><?php
                    if (count($vnum) > 0) {
                        echo '000' . $vnum;
                    } else {
                        echo '<span style="color: #0000FF">Sin Numero</span>';
                    }
                    ?></div>
                <div class="col-md-1"></div>
                <div class="col-md-2"><label class="control-label">Documento:</label>
                </div>
                <div class="col-md-3">
                    <?php
                    if (count($vnum) > 0) {
                        echo $vdoc;
                    } else {
                        echo '<span style="color: #0000FF">NO EMITIDO</span>';
                    }
                    ?>
                </div>
            </div>
            <hr class="hr-margin-5">
            <div class="row">
                <div class="col-md-2"><label class="control-label">Fecha:</label></div>
                <div class="col-md-3"><?= $vfecha ?></div>
                <div class="col-md-1"></div>
                <div class="col-md-2"><label class="control-label">Moneda:</label>
                </div>
                <div class="col-md-3">
                    <?php
                    if ($vmon == '1029') {
                        echo 'Soles';
                    } elseif ($vmon == '1030') {
                        echo 'Dolares';
                    }
                    ?>
                </div>
            </div>
            <hr class="hr-margin-5">
            <div class="row">
                <div class="col-md-2"><label class="control-label">Cliente:</label></div>
                <div class="col-md-3"><?= $vnom ?></div>
                <div class="col-md-1"></div>
                <div class="col-md-2"><label class="control-label">Tipo de Pago:</label>
                </div>
                <div class="col-md-3">
                    <?= $vcon ?>
                </div>
            </div>
            <hr class="hr-margin-5">
            <div class="row">
                <div class="col-md-2"><label class="control-label">Vendedor:</label></div>
                <div class="col-md-3"><?= $vfecha ?></div>
                <div class="col-md-1"></div>
                <div class="col-md-2"><label class="control-label">Tipo de Cambio:</label>
                </div>
                <div class="col-md-3"><?= $vtasa ?>
                </div>
            </div>
            <hr class="hr-margin-5">
            <div class="row">
                <div class="col-md-2"><label class="control-label">Estado:</label></div>
                <div class="col-md-3"><?= $vven ?></div>
                <div class="col-md-1"></div>
            </div>
            <hr class="hr-margin-5">
            <div class="row-fluid force-margin">
                <table class="table table-bordered" id="my-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Cliente</th>
                            <th>Documento</th>
                            <th>Fecha</th>
                            <th>Moneda</th>
                            <th>Subtotal</th>
                            <th>Total</th>
                            <th>Acciones</th>
                            <!--<th width="10%">P. Contable</th>
                            <th width="10%">Acciones</th>-->
                        </tr>
                    </thead>
                    <tbody>
                        <?php  $total_c=0; ?>
                        <?php foreach ($venta as $detalle): ?>
                            <tr>
                                <td><?= $detalle->contador ?></td>
                                <td><?= $detalle->razon_social ?></td>
                                <td><?= $detalle->des_doc ?></td>
                                <td><?= $detalle->fecha ?></td>
                                <td><?= $detalle->moneda ?></td>
                                <td><?= $detalle->subtotal ?></td>
                                <td><?= $detalle->total ?> <?php   $total_c+=$detalle->total;?></td>  
                                <td><button class="btn btn-info btn-xs"><i class="fa fa-search"></i></button>&nbsp;
                                    <button class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></button>&nbsp;
                                    <button class="btn btn-success btn-xs"><i class="fa fa-check"></i></button></td>
                            </tr>
                        <?php endforeach; ?>

                    </tbody>
                </table>
            </div>
            <hr class="hr-margin-5">
            <div class="row">
                <div class="col-md-2"><label class="control-label">Total (Real):</label></div>
                <div class="col-md-3"><?= $vtotal ?></div>
                <div class="col-md-1"></div>
                <div class="col-md-3"><label class="control-label">Total (Contable):</label>
                </div>
                <div class="col-md-3"><?= $total_c ?>
                </div>
            </div>
            <br>
            <div class="row text-center">
                <div class="col-md-12"><label class="control-label">Total Real - Total Contable =  </label> <?= $vtotal-$total_c ?></div>
                
            </div>
        </div>
        <div class="modal-footer" align="right">
            <div class="row">
                <div class="text-right">
                    <div class="col-md-12">
                        <input type="button" class='btn btn-danger' value="Cerrar" data-dismiss="modal">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?= base_url() ?>recursos/js/facturador_historial_list_detalle.js"></script>