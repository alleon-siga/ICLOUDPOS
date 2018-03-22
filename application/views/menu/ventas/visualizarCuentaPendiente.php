<style>

    #tabla_resumen_productos thead tr {
        border-top: 1px #000 dashed;
        border-bottom: 1px #000 dashed;
    }

    #tabla_resumen_productos thead tr th {
        border-top: 1px #000 dashed;
        border-bottom: 1px #000 dashed;
    }

    #tabla_resumen_productos tbody tr td {
        border-top: 0px #000 dashed;
        border-bottom: 0px #000 dashed;
        font-size: 85%;
    }

    #panel_documento {
        font-size: 90%;
        width: 80mm;
        margin: auto;
        border-color: #000;
        border-style: dashed;
    }

    #tabla_resumen_productos thead tr th {
        font-size: 85%;
    }

</style>
<div class="modal-dialog" style="width: 50%">
    <div class="modal-content">

        <div class="modal-header">

            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Vista Previa</h4>
        </div>
        <div class="modal-body">

            <div class="panel" class="text-center" id="panel_documento">
                <?php if (isset($cronogramas[0])) {
                    ?>
                    <div id="resumen_venta">
                            <div class="row ">
                                <div class="col-xs-12">
                                    <h3 class="text-center">
                                        <span
                                            id="titulo_emp"><?= $cliente['razon_social'] ?></span>
                                    </h3>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12">
                                    <h4 class="text-center">
                                        <span
                                            id="titulo_emp"><?= $cliente['direccion']?></span>
                                    </h4>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-12">
                                    <h5 class="text-center">
                                        Telf: <span
                                            id="titulo_emp"><?= $cliente['telefono1'] ?></span>
                                    </h5>
                                </div>
                            </div>
                            <div class="block-content-mini-padding">
                                <div class="col-xs-3">
                                    Venta Nro:
                                </div>
                                <div class="col-xs-9">
                                    <?= $cliente['documento_Serie']."-".$cliente['documento_Numero'] ?>
                                </div>


                            </div>


                            <div class="block-content-mini-padding">
                                <div class="col-xs-5">
                                    Fecha de Pago:

                                </div>
                                <div class="col-xs-7">

                                    <?php  if(!isset($id_historial)) {  echo date('d-m-Y');  }else{

                                        echo date('d-m-Y', strtotime( $cronogramas[0]['fecha_abono']));
                                    }  ?>
                                </div>
                                <div class="col-xs-5">
                                    Hora:

                                </div>
                                <div class="col-xs-7">

                                    <?php
                                    if(!isset($id_historial)) {  echo date('H:i:s') ;  }else{

                                        echo date('H:i:s', strtotime( $cronogramas[0]['fecha_abono']));
                                    } ?>
                                </div>


                            </div>

                            <div class="block-content-mini-padding">
                                <div class="col-xs-5">
                                    Tipo de Pago:

                                </div>
                                <div class="col-xs-7">

                                    <?php  echo $cronogramas[0]['nombre_metodo'];
                                      ?>
                                </div>


                            </div>


                        <br>
                        <!-- END TABLA DE PRODUCTOS -->
                        <div>

                            <div class="col-xs-12 col-lg-12">
                                <table class="table" id="totales_">
                                    <!--<tr>
                                    <td>
                                        <strong>Subtotal</strong>
                                    </td>
                                    <td>
                                        $. <span id="subtotalR"><span id="totalR"><?php //echo $ventas[0]['subTotal']?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>IMPUESTO</strong>
                                    </td>
                                    <td>
                                        <!--<label id="igvR">12</label>%-->
                                    <?php //echo $ventas[0]['impuesto']?>
                                    <!--  </td>
                                  </tr>-->
                                    <tr style="border-bottom:0px #000 dashed">
                                        <td style="border-top: 1px #000 dashed">
                                            <strong>Total de la Venta</strong>
                                        </td>
                                        <td style="border-top: 1px #000 dashed">
                                            <?php echo $detalles[0]['simbolo'] ?> <span
                                                id="totalR"><?=  $detalles[0]['total'] ?></span>
                                        </td>
                                    </tr>
                                    <tr style="border-bottom:0px #000 dashed">
                                        <td style="border-top: 0px #000 dashed">
                                            <strong>Monto a abonar</strong>
                                        </td>
                                        <td style="border-top: 0px #000 dashed">
                                            <?php echo $detalles[0]['simbolo'] ?> <span id="totalR"><?php

                                              echo $cronogramas[0]['monto_abono']; ?></span>
                                        </td>
                                    </tr>
                                    <tr style="border-bottom: 0px #000 dashed">
                                        <td style="border-bottom: 1px #000 dashed">
                                            <strong>Total Pagado</strong>
                                        </td>
                                        <td style="border-bottom: 1px #000 dashed">
                                            <?php echo $detalles[0]['simbolo']?> <span id="totalR"><?php


                                                    echo $detalles[0]['suma'];
                                                 ?></span>
                                        </td>
                                    </tr>
                                    <tr style="border-bottom:0px #000 dashed">
                                        <td style="border-top: 1px #000 dashed">
                                            <strong>Total Restante</strong>
                                        </td>
                                        <td style="border-top: 0px #000 dashed">
                                            <?php echo $detalles[0]['simbolo'] ?> <span id="totalR"><?php
                                                echo $detalles[0]['total']-$detalles[0]['inicial']-$detalles[0]['suma']; ?></span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="row text-center">
                                <div class="col-xs-12">
                                    <h6><br>GRACIAS POR SU COMPRA. VUELVA PRONTO</h6>
                                </div>
                            </div>
                        </div>

                    </div>
                <?php } ?>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#" class="btn btn-default" id="cerrar_visualizar"
                <?php if(!isset($id_historial)) { ?> onclick="cerrar_visualizar()"  <?php  }else{ ?> onclick="cerrar_detalle_historial()"   <?php  } ?>>Cerrar</a>
            <a href="#" tabindex="0" type="button" id="imprimir" class="btn btn-primary"> <i
                    class="fa fa-print"></i>Imprimir</a>
        </div>
    </div>

</div>

<script src="<?php echo base_url() ?>recursos/js/printThis.js"></script>
<script>
    $(function () {

        $("#imprimir").click(function (e) {
            e.preventDefault();
            $("#resumen_venta").printThis({
                importCSS: true,
                loadCSS: "<?= base_url()?>recursos/css/page.css"
            });
        });

        setTimeout(function(){$("#imprimir").focus();}, 500);
    })


</script>