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
    $vtasa = $v->vtasa;
    $vtotal = $v->vtotal;
    $vclien = $v->vclien;
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
                    if ($vmon == MONEDA_DEFECTO) {
                        echo 'Soles';
                    } else {
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
                            <th>T. Cliente</th>
                            <th>Doc.</th>
                            <th>Fecha</th>
                            <th>Moneda</th>
                            <th>Subtotal</th>
                            <th>Total</th>
                            <th>Acciones</th>
                            <!--<th width="10%">P. Contable</th>
                            <th width="10%">Acciones</th>-->
                        </tr>
                    </thead>
                    <tbody id="tablec">
                        <?php foreach ($venta as $detalle): ?>
                            <tr>
                                <td><?= $detalle->contador ?></td>
                                <td><?= $detalle->razon_social ?></td>
                                <td><?php
                                    if ($detalle->vclien == 0) {
                                        echo 'P. Natural';
                                    } else if ($detalle->vclien > 0) {
                                        echo 'P. Juridica';
                                    }
                                    ?></td>
                                <td><?= $detalle->abr_doc ?></td>
                                <td><?= $detalle->fecha ?></td>
                                <td><?= $detalle->moneda ?></td>
                                <td><?= $detalle->subtotal ?></td>
                                <td class="total_co"><?= $detalle->total ?></td>  
                                <td><button class="btn btn-info btn-xs" onclick="info(<?= $detalle->id_shadow ?>)"><i class="fa fa-search"></i></button>&nbsp;
                                    <button class="btn btn-danger btn-xs eliminarv" id="elishadow" data-idshadow="<?= $detalle->id_shadow ?>"><i class="fa fa-trash"></i></button>&nbsp;
                                    <button class="btn btn-default btn-xs" data-toggle="tooltip"  title="Sunat" data-original-title="Sunat" onclick="generarcomprobante(<?= $detalle->id_shadow ?>,<?= $detalle->documento_id ?>)"><i class="fa fa-mail-forward"></i></button></td>
                            </tr>
                        <?php endforeach; ?>

                    </tbody>
                </table>
            </div>
            <hr class="hr-margin-5">
            <div class="row">
                <div class="col-md-2"><label class="control-label">Total (Real):</label></div>
                <div class="col-md-3"><?= $detalle->moneda ?><span id="total_r" > <?= number_format($vtotal, 2) ?></span></div>
                <div class="col-md-1"></div>
                <div class="col-md-3"><label class="control-label">Total (Contable):</label>
                </div>
                <div class="col-md-3"><?= $detalle->moneda ?> <span id='total_c'></span>
                </div>
            </div>
            <br>
            <div class="row text-center">
                <div class="col-md-12"><label class="control-label">Total Real - Total Contable =  </label> <?= $detalle->moneda ?> <span id="total_r_c"></span></div>

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
<script type="text/javascript">
                                        $(document).ready(function () {
                                            calculartotalcontable();
                                            caltulartotal();



                                        })
                                        function calculartotalcontable() {
                                            var totalcontable = 0;
                                            $(".total_co").each(function () {
                                                totalcontable += parseFloat($(this).html()) || 0;
                                                $("#total_c").text(parseFloat(totalcontable).toFixed(2));

                                            });
                                        }
                                        function caltulartotal() {
                                            $("#total_r_c").text(parseFloat(document.getElementById("total_r").innerHTML - document.getElementById("total_c").innerHTML).toFixed(2));
                                        }

                                        $("#my-table").on('click', '.eliminarv', function () {

                                            var id = $('#elishadow').attr("data-idshadow");
                                            if ($('#tablec tr').length > 0) {
                                                var tr = $(this).closest('tr');
                                                $.ajax({
                                                    url: $('#ruta').val() + 'facturador/venta/remove_ventaconvertida_shadow/',
                                                    type: 'POST',
                                                    data: {'id_shadow': id},
                                                    success: function (data) {

                                                    },
                                                    dataType: 'json',
                                                    error: function (res1) {
                                                        tr.remove();
                                                        calculartotalcontable();
                                                        caltulartotal();
                                                    }
                                                });
                                            } else {
                                                alert('Error s');
                                            }
                                        });
                                        function generarcomprobante(id_shadow, iddoc) {

                                            $.ajax({
                                                url: $('#ruta').val() + 'facturador/venta/facturar_venta/',
                                                type: 'POST',
                                                data: {'id_shadow': id_shadow},

                                                success: function (data) {
                                                    alert(data)
                                                },
                                                error: function () {
                                                    alert('asd')
                                                }
                                            });
                                        }
</script>
