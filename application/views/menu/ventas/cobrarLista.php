<div class="table-responsive">
    <table class='table table-striped dataTable table-bordered' id="tablaresult">
        <thead>
        <tr>
            <th>Fecha</th>
            <th>Nro. Venta</th>
            <th>Cliente</th>
            <th>Vendedor</th>
            <th>Moneda</th>
            <th>Monto</th>
            <th>Estado</th>
            <th>Opciones</th>

        </tr>
        </thead>
        <tbody>
        <?php if (count($ventas) > 0): ?>
            <?php
            foreach ($ventas as $venta): ?>
                <tr id="id<?php echo $venta->venta_id; ?>" style="text-align: center;">
                    <td style="text-align: center;"><span
                            style="display: none;"><?php echo date("YmdHis", strtotime($venta->fecha)); ?></span><?php echo date("d-m-Y H:i:s", strtotime($venta->fecha)); ?>
                    </td>
                    <td><?php echo $venta->documento_Serie . "-" . $venta->documento_Numero; ?></td>
                    <td><?php echo $venta->razon_social; ?></td>
                    <td style="text-align: center;"><?php echo $venta->nombre; ?></td>
                    <td style="text-align: center;"><?php echo $venta->nombre_moneda; ?></td>
                    <td id="total_pagar_<?php echo $venta->venta_id; ?>"
                        style="text-align: center;"><?php echo $venta->total; ?></td>
                    <td style="text-align: center;"><?php echo $venta->venta_status == "COBRO" ? "PENDIENTE A COBRAR" : $venta->venta_status; ?></td>
                    <td class="row" style=" width: 175px; text-align: center;">
                        <div class="col-sm-6">
                            <div class="dropdown">
                                <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                    Cobrar
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                    <li><a class="cobrar_contado"
                                           data-id="<?php echo $venta->venta_id; ?>"
                                           data-moneda-id="<?php echo $venta->id_moneda; ?>"
                                           data-moneda-tasa="<?php echo $venta->tasa_cambio; ?>"
                                           data-moneda-simbolo="<?php echo $venta->simbolo; ?>"
                                           href="#">Contado</a></li>

                                    <li><a class="cobrar_credito" data-id="<?php echo $venta->venta_id; ?>" href="#">Credito</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <a class="btn btn-default btn-default btn-default" data-toggle="tooltip"
                               title="Ver" data-original-title="Ver"
                               href="#" onclick="ver('<?php echo $venta->venta_id ?>');">
                                Ver
                            </a>
                        </div>
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

        TablesDatatables.init(0);


        $(".cobrar_contado").click(function () {

            var venta_id = $(this).attr('data-id');
            var moneda_id = $(this).attr('data-moneda-id');
            var moneda_tasa = $(this).attr('data-moneda-tasa');
            var moneda_simbolo = $(this).attr('data-moneda-simbolo');
            var total_input = $("#totApagar2");
            var total = $("#total_pagar_" + venta_id).html().trim();

            total_input.val(total);
            $("#importe").val(total);
            $("#moneda_tasa_confirm").val(moneda_tasa);
            $("#lblSim4").html(moneda_simbolo);
            $("#lblSim5").html(moneda_simbolo);
            $("#lblSim6").html(moneda_simbolo);

            if (moneda_tasa != 0) {
                $("#moneda_tasa_div").show();
            }
            else {
                $("#moneda_tasa_div").hide();
            }
            $("#realizarventa").attr("onclick", 'completar_cobro(' + venta_id + ', "1");');
            $("#btnRealizarVentaAndView").attr("onclick", 'completar_cobro(' + venta_id + ', "1");');

            $("#generarventa").modal("show");
        });


        $(".cobrar_credito").click(function () {

            var venta_id = $(this).attr('data-id');
            var moneda_id = $(this).attr('data-moneda-id');
            var moneda_tasa = $(this).attr('data-moneda-tasa');
            var moneda_simbolo = $(this).attr('data-moneda-simbolo');
            var total = $("#total_pagar_" + venta_id).html().trim();


            $("#precio_contado").val(total);
            var mto = parseFloat($("#mtoporcen").text().split('%')[0]);
            $("#inicial").val(parseFloat(total * mto / 100));

            $("#precio_credito").val('0');
            $("#inicial").attr('disabled', 'disabled');
            $("#mto_ini_res").val($("#inicial").val());


            $("#btnRealizarVentaAndViewCr").attr("onclick", 'completar_cobro(' + venta_id + ', "2");');
            $("#realizarventa_credito_simple").attr("onclick", 'completar_cobro(' + venta_id + ', "2");');
            $("#generarventa1").modal("show");
        });
    });


</script>