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
<div class="modal-dialog" style="width: 80%">
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
                    <?= $vmon ?>
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
                            <th># Factura</th>
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
                                <td><?= $detalle->serie_fac > 0 ? $detalle->serie_fac . '-' . $detalle->numero_fac : 'Sin Facturar' ?></td>
                                <td><?= $detalle->fecha ?></td>
                                <td><?= $detalle->moneda ?></td>
                                <td><?= $detalle->subtotal ?></td>
                                <td class="total_co"><?= $detalle->total ?></td>  
                                <td class="text-center"><button class="btn btn-info btn-xs" onclick="info(<?= $detalle->id_shadow ?>)"><i class="fa fa-search"></i></button>
                                    <?php
                                    if ($detalle->id_factura > 0) {
                                        ?>
                                        <a class="btn btn-xs btn-info" data-toggle="tooltip" 
                                           title="Imprimir PDF (A4)" data-original-title="Imprimir PDF (A4)"
                                           href="#"
                                           onclick="imprimir('<?= $detalle->id_factura ?>');">
                                            <i class="fa fa-file-pdf-o"></i>
                                        </a>

                                        <a class="btn btn-xs btn-info" data-toggle="tooltip" 
                                           title="Imprimir Ticket" data-original-title="Imprimir Ticket"
                                           href="#"
                                           onclick="imprimir_ticket('<?= $detalle->id_factura ?>');">
                                            <i class="fa fa-print"></i>
                                        </a>
                                        <?php
                                    } else {
                                        ?>
                                        <button class="btn btn-danger btn-xs eliminarv" onclick="mostrar(<?= $detalle->id_shadow ?>)" ><i class="fa fa-trash"></i></button>&nbsp;
                                        <?php
                                        if (valueOptionDB('FACTURACION', 0) == 1) {
                                            ?>
                                            <button class="btn btn-default btn-xs" data-toggle="tooltip"  title="Sunat" data-original-title="Sunat"  onclick="generarcomprobante(<?= $detalle->id_shadow ?>,<?= $detalle->venta_id ?>)"><i class="fa fa-mail-forward"></i></button>
                                            <?php
                                        }
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                    </tbody>
                </table>
            </div>
            <hr class="hr-margin-5">
            <div class="row">
                <div class="col-md-2"><label class="control-label">Total (Real):</label></div>
                <div class="col-md-3"><?= $detalle->moneda ?><span id="total_r" > <?= number_format($vtotal, 2, '.', '') ?></span></div>
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
                        <a onclick="regresar()" class="btn btn-danger">Cancelar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<iframe style="display: block;" id="imprimir_frame" src="" frameborder="YES" height="0" width="0"
        border="0" scrolling=no>

</iframe>
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
                            function regresar() {
                                $('#dialog_venta_detalle_convertidos').modal('hide');
                                $(".modal-backdrop").hide();
                                get_ventas();
                            }

                            function eliminar(id, id_venta) {

                                if ($('#my-table #tablec tr').length > 0) {
                                    var tr = $(this).closest('tr');
                                    $.ajax({
                                        url: $('#ruta').val() + 'facturador/venta/remove_ventaconvertida_shadow/',
                                        type: 'POST',
                                        data: {'id': id},
                                        success: function (data) {
                                            if ($('#my-table #tablec tr').length == 1) {
                                                $('#remove_ventaconvertida_shadow').modal('hide');
                                                tr.remove();
                                                $('#dialog_venta_detalle_convertidos').modal('hide');
                                                $(".modal-backdrop").hide();
                                                get_ventas();
                                            } else {
                                                $('#remove_ventaconvertida_shadow').modal('hide');
                                                tr.remove();
                                                calculartotalcontable();
                                                caltulartotal();
                                                detalle(id_venta);
                                            }
                                        },
                                        error: function (res1) {
                                            alert("error 2");
                                        }
                                    });
                                }
                            }

                            function generarcomprobante(id_shadow, venta_id) {
                                $.ajax({
                                    url: $('#ruta').val() + 'facturador/venta/facturar_venta/',
                                    type: 'POST',
                                    data: {'id_shadow': id_shadow},

                                    success: function (data) {
                                        detalle(venta_id);
                                    },
                                    error: function (resp) {
                                        alert(resp)
                                    }
                                });
                            }
                            function imprimir_ticket(id_shadow) {

                                $.bootstrapGrowl('<p>IMPRIMIENDO PEDIDO</p>', {
                                    type: 'success',
                                    delay: 2500,
                                    allow_dismiss: true
                                });

                                var url = '<?= base_url('facturacion/imprimir_ticket') ?>/' + id_shadow;
                                $("#imprimir_frame").attr('src', url);
                            }

                            function imprimir(id_shadow) {

                                var win = window.open('<?= base_url() ?>facturacion/imprimir/' + id_shadow, '_blank');
                                win.focus();
                            }
</script>
