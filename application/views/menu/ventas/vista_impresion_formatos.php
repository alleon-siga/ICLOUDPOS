<div class="modal-dialog" style="width: 60%">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Formatos de Venta</h3>
        </div>
        <div class="modal-body">
            <div class="row-fluid force-margin">

                <div class="row-fluid">
                    <div class="block">

                        <div class="box-content box-nomargin">


                            <?php if ($id_documento==1): ?>
                                <a href="<?php echo base_url().'venta/imprimir_documento/'.$id_venta.'/factura' ?>" TARGET="_blank" tabindex="0" venta="<?php echo $id_venta ?>" type="button"  class="btn btn-primary  imprimir_predido_compra_venta"> <i class="fa fa-print"></i>Factura</a>
                            <?php endif ?>
                            <?php if ($id_documento==3): ?>
                                <a href="<?php echo base_url().'venta/imprimir_documento/'.$id_venta.'/boleta' ?>" TARGET="_blank" tabindex="0" venta="<?php echo $id_venta ?>" type="button"  class="btn btn-primary  imprimir_predido_compra_venta"> <i class="fa fa-print"></i>Boleta</a>
                            <?php endif ?>
                            <?php if ($condicion_pago == 2): ?>
                                <a href="<?php echo base_url().'venta/imprimir_documento/'.$id_venta.'/pedido_compra' ?>" TARGET="_blank" tabindex="0" venta="<?php echo $id_venta ?>" type="button"  class="btn btn-primary  imprimir_predido_compra_venta"> <i
                                        class="fa fa-print"></i>Pedido Compra-Venta</a>
                                <div class="btn-group" role="group" aria-label="">


                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-default  dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Letras
                                            <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <?php


                                            ?>
                                            <?php if (isset($credito_cuotas)): ?>
                                                <?php for($i=0;$i < count($credito_cuotas); $i++): ?>


                                                    <li><a href="<?php echo base_url()."venta/imprimir_documento/$id_venta/letra_cambio/".$credito_cuotas[$i]['nro_letra'] ?>" TARGET="_blank" tabindex="0" type="button"  cuota="<?php echo $credito_cuotas[$i]['nro_letra'] ?>" venta="<?php echo $id_venta ?>" class="btn btn-success  imp987"> <i
                                                                class="fa fa-print"></i><?php echo $i+1 ?>/<?php echo count($credito_cuotas) ?> <?php echo $numero_creditos[0]['cuenta'] ?></a></li>

                                                <?php endfor; ?>
                                            <?php endif ?>
                                        </ul>
                                    </div>
                                </div>
                            <?php endif ?>
                            <a href="<?php echo base_url().'venta/imprimir_documento/'.$id_venta.'/guia_remision' ?>" TARGET="_blank" tabindex="0" venta="<?php echo $id_venta ?>" type="button" class="btn btn-primary  imprimir_guia_remision"> <i
                                    class="fa fa-print"></i>Guia Remision</a>
                            <?php if ($estatus == "DEVUELTO" or $estatus == "ANULADO"): ?>
                                <a href="<?php echo base_url().'venta/imprimir_documento/'.$id_venta.'/nota_credito' ?>" TARGET="_blank" tabindex="0" venta="<?php echo $id_venta ?>" type="button"  class="btn btn-primary "> <i
                                        class="fa fa-print"></i>Nota de Crédito</a>
                            <?php endif ?>



                        </div>
                    </div>
                </div>

            </div>

        </div>

        <div class="modal-footer" align="right">
            <div class="row">
                <div class="text-right">
                    <div class="col-md-10" >

                    </div>
                    <div class="col-md-2" >
                        <input align="right" type="reset" class='btn btn-default' value="Cerrar" data-dismiss="modal">
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>