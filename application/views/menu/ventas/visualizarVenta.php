<style>

    #tabla_resumen_productos thead tr {
        border-top: 1px #000 dashed;
        border-bottom: 1px #000 dashed;
        background: white !important;
    }

    #tabla_resumen_productos thead tr th {
        border-top: 1px #000 dashed;
        border-bottom: 1px #000 dashed;
        background-color: white !important;
        font-weight: normal !important;
        color: #333 !important;
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
        border-top: 0px #000 dashed;
        border-bottom: 0px #000 dashed;
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
                <?php if (isset($ventas[0])) {
                    ?>
                    <div id="resumen_venta">
                        <div>
                            <div class="row ">
                                <div class="col-xs-12">
                                    <h3 class="text-center">
                                        <span
                                            id="titulo_emp"><?= isset($ventas[0]['RazonSocialEmpresa']) ? $ventas[0]['RazonSocialEmpresa'] : '' ?></span>
                                    </h3>
                                </div>
                            </div>
                            <!--
                            <div class="row">
                                <div class="col-xs-12">
                                    <h4 class="text-center">
                                        <span
                                            id="titulo_emp"><?= isset($ventas[0]['DireccionEmpresa']) ? $ventas[0]['DireccionEmpresa'] : '' ?></span>
                                    </h4>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-12">
                                    <h5 class="text-center">
                                        Telf: <span
                                            id="titulo_emp"><?= isset($ventas[0]['TelefonoEmpresa']) ? $ventas[0]['TelefonoEmpresa'] : '' ?></span>
                                    </h5>
                                </div>
                            </div>
                             -->
                            <div class="">

                                <div class="col-xs-12">
                                    <h4 class="text-center">
                                    # <?= isset($ventas[0]['serie']) ? isset($ventas[0]['serie']) : '' . "-" . isset($ventas[0]['numero']) ? $ventas[0]['numero'] : '' ?>
                                    </h4>
                                </div>


                            </div>

                            <div class="">
                                <div class="col-xs-2">
                                    Cliente:
                                </div>
                                <div class="col-xs-6">
                                    <?= $ventas[0]['cliente'] ?>
                                </div>
                                <div class="col-xs-4">
                                 &nbsp;
                                </div>

                            </div>


                            <div class="">
                                <div class="col-xs-7">
                                    Atendido por: <?= $ventas[0]['username'] ?>

                                </div>
                            </div>

                            <div class=" block-content-mini-padding">

                                <div class="col-xs-2">

                                    Pago:
                                </div>
                                <div class="col-xs-2">

                                    <?= $ventas[0]['nombre_condiciones'] ?>
                                </div>


                            </div>


                            <div class="block-content-mini-padding">
                                <div class="col-xs-7">
                                    Fecha:  <?= date('d/m/Y', strtotime($ventas[0]['fechaemision'])) ?>

                                </div>
                            </div>

                            <div class="block-content-mini-padding">
                                <div class="col-xs-4">
                                    Hora:  <?= date('H:i', strtotime($ventas[0]['fechaemision'])) ?>

                                </div>
                            </div>
                            <!-- info row -->



                            <!-- /.col -->
                        </div>
                        <!-- /.row -->
                        <!-- Table row -->
                        <div>
                            <br>
                            <div class="col-xs-12 table-responsive" style="width: 98%; margin-top: 5px">
                                <table id="tabla_resumen_productos" class="">
                                    <thead>
                                    <tr>

                                        <th style="border-bottom: 1px #000 dashed; width: 20%; font-weight: bold;"><label>Cantidad</label></th>

                                        <th style="border-bottom: 1px #000 dashed; width: 60%; font-weight: bold;"><label>Descripci&oacute;n<//label></th>

                                        <th style="border-bottom: 1px #000 dashed; width: 20%; font-weight: bold;"><label>Importe</label></th>
                                    </tr>
                                    </thead>
                                    <tbody id="detalle_contenido_producto">
                                    <?php

                                    foreach ($ventas as $venta) {
                                        $um = isset($venta['abreviatura']) ? $venta['abreviatura'] : $venta['nombre_unidad'];
                                        $cantidad_entero = intval($venta['cantidad'] / 1) > 0 ? intval($venta['cantidad'] / 1) : '';
                                        $cantidad_decimal = number_format(fmod($venta['cantidad'], 1), 3);

                                        $cantidad = $cantidad_entero;

                                        if ($cantidad_decimal > 0) {

                                            if (!empty($cantidad_entero)) {
                                                $cantidad = $venta['cantidad'];

                                            } else
                                                $cantidad = strval($cantidad_decimal);

                                            if ($cantidad_decimal == 0.25 or $cantidad_decimal == 0.250)
                                                $cantidad = $cantidad_entero . " " . '1/4';
                                            if ($cantidad_decimal == 0.5 or $cantidad_decimal == 0.50 or $cantidad_decimal == 0.500)
                                                $cantidad = $cantidad_entero . " " . '1/2';
                                            if ($cantidad_decimal == 0.75 or $cantidad_decimal == 0.750)
                                                $cantidad = $cantidad_entero . " " . '3/4';
                                            if ($cantidad_decimal == 0.125)
                                                $cantidad = $cantidad_entero . " " . '1/8';
                                        }


                                        if ($venta['producto_cualidad'] == 'MEDIBLE') {

                                            if ($venta['unidades'] == 12 or $venta['orden'] == 1) {
                                                $cantidad = floatval($venta['cantidad']);

                                            } else {
                                                $cantidad = floatval($venta['cantidad'] * $venta['unidades']);
                                                $um = $venta['unidad_minima'];
                                            }
                                        }
                                        ?>
                                        <TR style="">
                                            <td><?php echo $cantidad . " " . $um ?></td>

                                            <td style="padding-left: 2px;"><?= $venta['nombre'] ?></td>

                                            <td align="right"><?php echo number_format(ceil($venta['importe'] * 10) / 10, 2) ?></td>
                                        </TR>
                                    <?php } ?>
                                    </tbody>
                                </table>
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
                                            <strong>Total</strong>
                                        </td>
                                        <td style="border-top: 1px #000 dashed; text-align: right;">
                                            <?php echo $ventas[0]['simbolo'] ?> <span
                                                id="totalR"><?= number_format(ceil($ventas[0]['montoTotal'] * 10) / 10, 2) ?></span>
                                        </td>
                                    </tr>
                                    <tr style="border-bottom:0px #000 dashed">
                                        <td style="border-top: 0px #000 dashed">
                                            <strong><?php echo ($ventas[0]['id_condiciones']=='2') ? 'Pago a Cuenta' : 'Pagado'?></strong>
                                        </td>
                                        <td style="border-top: 0px #000 dashed; text-align: right;">
                                            <?php echo $ventas[0]['simbolo'] ?> <span
                                                id="totalR"><?= number_format(ceil($ventas[0]['pagado'] * 10) / 10, 2) ?></span>
                                        </td>
                                    </tr>
                                    <tr style="border-bottom: 0px #000 dashed">
                                        <td style="border-bottom: 1px #000 dashed">
                                            <strong>Vuelto</strong>
                                        </td>
                                        <td style="border-bottom: 1px #000 dashed; text-align: right;">
                                            <?php echo $ventas[0]['simbolo'] ?> <span
                                                id="totalR"><?= number_format(ceil($ventas[0]['vuelto'] * 10) / 10, 2) ?></span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="row text-center">
                                <div class="col-xs-12">
                                    <h6>CANJEAR POR BOLETA O FACTURA <br>GRACIAS POR ELEGIRNOS, VUELVA PRONTO</h6>
                                </div>
                            </div>
                        </div>

                    </div>
                <?php } ?>
            </div>
        </div>
        <div class="modal-footer">

            <a href="#" class="btn btn-default" data-dismiss="modal">Cerrar</a>
            <?php if ($this->session->userdata('GENERAR_FACTURACION') == "SI") { ?>
                <?php if ($hola[0]->id_documento == 1): ?>
                    <a href="<?php echo base_url() . "venta/imprimir_documento/$id_venta/factura" ?>" type="button"
                       target="_blank" id="" class="btn btn-primary" style="margin-bottom:5px;margin-left:5px;">
                        <i
                            class="fa fa-print"></i>Imprimir Factura</a>
                <?php elseif ($hola[0]->id_documento == 3): ?>
                    <a href="<?php echo base_url() . "venta/imprimir_documento/$id_venta/boleta" ?>" type="button"
                       target="_blank" id="" class="btn btn-primary" style="margin-bottom:5px;margin-left:5px;">
                        <i
                            class="fa fa-print"></i>Imprimir Boleta de Venta</a>
                <?php endif ?>
                <div class="div_nota_credito hide">
                    <a href="<?php echo base_url() . "venta/imprimir_documento/$id_venta/nota_credito" ?>" type="button"
                       target="_blank" id="" class="btn btn-primary" style="margin-bottom:5px;margin-left:5px;">
                        <i
                            class="fa fa-print"></i>Imprimir Nota de Credito</a>
                </div>
                <a href="<?php echo base_url() . "venta/imprimir_documento/$id_venta/guia_remision" ?>" type="button"
                   target="_blank" class="btn btn-primary" style="margin-bottom:5px;margin-left:5px;"> <i
                        class="fa fa-print"></i>Imprimir Guia de Remisión</a>
                <div class="clearfix"></div>
            <?php } else { ?>

                <a href="#" tabindex="0" type="button" id="imprimir" class="btn btn-primary"> <i
                        class="fa fa-print"></i>Imprimir</a>
            <?php } ?>
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

        setTimeout(function () {
            $("#imprimir").focus();
        }, 500);
    })
</script>