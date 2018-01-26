<div class="modal-dialog" style="width: 30%">
    <div class="modal-content">

        <div class="modal-header">

            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Imprimir Documentos:</h4>
        </div>
        <div class="modal-body">
             <div class="row">
                <div class="form-group" style="text-align: center;">
                <div class="col-md-6">
                    <?php if (isset($hola) and $hola[0]->id_documento == 1): ?>
                      
                    <label for="contado" class="control-label">Factura</label> 
                  <?php elseif(isset($hola) and $hola[0]->id_documento == 3): ?>
                    <label for="contado" class="control-label">Boleta de Venta:</label> 

                    <?php endif; ?>
                </div>
                <div class="col-md-2">
                <?php if (isset($hola) and $hola[0]->id_documento == 1): ?>
                   <label for="contado" class="control-label"> 
                       <a href="<?php echo base_url()."venta/imprimir_documento/$id_ventas/factura" ?>" TARGET="_blank" tabindex="0" venta="<?php echo $id_ventas ?>" type="button"  class="btn btn-primary imprimir_predido_compra_venta"> <i
                                    class="fa fa-print"></i>Imprimir</a>
                <?php elseif(isset($hola) and $hola[0]->id_documento == 3): ?>
                   <label for="contado" class="control-label"> 
                       <a href="<?php echo base_url()."venta/imprimir_documento/$id_ventas/boleta" ?>" TARGET="_blank" tabindex="0" venta="<?php echo $id_ventas ?>" type="button"  class="btn btn-primary imprimir_predido_compra_venta"> <i
                                    class="fa fa-print"></i>Imprimir</a>
                <?php endif; ?>
                   </label>
                </div>
            </div>
           </div>
             <div class="row">
                <div class="form-group" style="text-align: center;">
                <div class="col-md-6">
                    <label for="contado" class="control-label">Pedido Compra-Venta:</label> 
                </div>
                <div class="col-md-2">
                   <label for="contado" class="control-label"> 
                       <a href="<?php echo base_url()."venta/imprimir_documento/$id_ventas/pedido_compra" ?>" TARGET="_blank" tabindex="0" venta="<?php echo $id_ventas ?>" type="button"  class="btn btn-primary imprimir_predido_compra_venta"> <i
                                    class="fa fa-print"></i>Imprimir</a>
                   </label>
                </div>
            </div>
           </div>
           <div class="row">
                <div class="form-group" style="text-align: center;">
                <div class="col-md-6">
                    <label for="contado" class="control-label">Guia de Remision:</label> 
                </div>
                <div class="col-md-2">
                   <label for="contado" class="control-label"> 
                       <a href="<?php echo base_url()."venta/imprimir_documento/$id_ventas/guia_remision" ?>" TARGET="_blank" tabindex="0" venta="<?php echo $id_ventas ?>" type="button" class="btn btn-primary imprimir_guia_remision"> <i
                                    class="fa fa-print"></i>Imprimir</a>
                   </label>
                </div>
            </div>
           </div>
           <?php if (isset($credito_cuotas)): ?>
             <?php for($i=0;$i < count($credito_cuotas); $i++): ?>
               <div class="row">
                    <div class="form-group" style="text-align: center;">
                    <div class="col-md-6">
                        <label for="contado" class="control-label">Letra <?php echo $i+1 ?>/<?php echo count($credito_cuotas) ?> <?php echo $numero_creditos[0]['cuenta'] ?>:</label> 
                    </div>
                    <div class="col-md-2">
                       <label for="contado" class="control-label"> 
                           <a href="<?php echo base_url()."venta/imprimir_documento/$id_ventas/letra_cambio/".$credito_cuotas[$i]['nro_letra'] ?>" TARGET="_blank" tabindex="0" type="button"  cuota="<?php echo $credito_cuotas[$i]['nro_letra'] ?>" venta="<?php echo $id_ventas ?>" class="btn btn-primary imp987"> <i
                                        class="fa fa-print"></i>Imprimir</a>
                       </label>
                    </div>
                </div>
               </div>
             <?php endfor; ?>
           <?php endif ?>
       </div>
        <div class="modal-footer">
            <a href="#" class="btn btn-default" onclick="cerrarmodal_ventana_imp_cre()" data-dismiss="modal">Cerrar</a>
        </div>
    </div>

</div>