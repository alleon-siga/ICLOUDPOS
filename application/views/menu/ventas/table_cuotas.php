<div class="col-md-4" style="text-align: center">
                                    <fieldset>
                                       <legend>Proyeciones de pago por cuotas</legend>
                                       <table id="tbproy_cuotas" class='table dataTable dataTables_filter table-striped' id="tbCuotas">
                                       <thead>
                                       <tr>
                                          <td>Nro Cuotas</td>
                                          <td>Monto</td>
                                       <tr>
                                       <tbody>
                                       <?php foreach ($proyeccion as $key=> $value) {?>
                                       <tr>
                                          <td><?php echo $key; ?></td>
                                          <td><?php echo $value; ?></td>
                                       <tr>
                                       <?php } ?>
                                       </tbody>
                                       </thead>
                                       </table>
                                    </fieldset>
                               </div>
                               <div class="col-md-8" style="text-align: center">
                                    <fieldset>
                                       <legend>Cronograma de Pagos</legend>
                                       <table id="tbcrono_pagos" class='table dataTable dataTables_filter table-striped'>
                                       <thead>
                                       <tr>
                                          <td>Nro Letra</td>
                                          <td>Fecha Giro</td>
                                          <td>Fecha Vencimiento</td>
                                          <td>Monto</td>
                                        
                                       <tr>
                                       <tbody>
                                          <?php $monto_sumado = 0; ?>
                                       <?php if (!$cronogramas): ?>
                                           <?php foreach ($cuotas as $cuota) {?>
                                          <tr>
                                             <td><?php echo $cuota["nro_letra"]; ?></td>
                                             <td><?php echo $cuota["fecha_giro"]; ?></td>
                                             <td><?php echo $cuota["fecha_vencimiento"]; ?></td>
                                             <?php $monto = str_replace(',','',$cuota["monto"]); ?>
                                             <?php $monto = (float)$monto; ?>
                                             <td><?php echo "S/. ".number_format($monto,2); ?></td>
                                             <?php $monto_sumado =  $monto_sumado + $monto; ?>
                                        
                                          <tr>
                                          <?php } ?>
                                       <?php else: ?>

                                          <?php foreach ($cronogramas as $cro) {?>
                                          <tr>
                                             <td><?php echo $cro["nro_letra"]; ?></td>
                                             <td><?php echo $cro["fecha_giro"]; ?></td>
                                             <td><?php echo $cro["fecha_vencimiento"]; ?></td>
                                             <?php $monto = str_replace(',','',$cro["monto"]); ?>
                                             <?php $monto = str_replace('S/. ','',$monto); ?>
                                             
                                             <td><?php echo $cro["monto"]; ?></td>
                                             <?php $monto_sumado =  (float)$monto_sumado + (float)$monto; ?>
                                     
                                          <tr>
                                          <?php } ?>
                                       <?php endif ?>
                                       <input type="hidden" value="<?php echo ($monto_sumado+$inicial) ?>" id="monto_sumado_precio_credito">
                                       </tbody>
                                       </thead>
                                       </table>
                                    </fieldset>
                               </div>
                            </div>

                                       
                                  
                                       
                        
