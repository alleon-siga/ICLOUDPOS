<?php $ruta = base_url(); ?>
<style>
    #tablaresult th {
        font-size: 11px !important;
        padding: 6px 2px;
        text-align: center;
        vertical-align: middle;
    }

    #tablaresult td {
        font-size: 10px !important;
    }
</style>
<!--<script src="<?php echo $ruta; ?>recursos/js/custom.js"></script>-->

<?php if (count($lstproveedor) > 0): ?>
    <br>
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-3">
            <label>Total: <span class="tipo_moneda"></span> <span
                        id="subtotal"><?= number_format($ingreso_totales->total_monto_venta, 2) ?></span></label>
        </div>
        <div class="col-md-3">
            <label>Total Abonado: <span class="tipo_moneda"></span> <span
                        id="impuesto"><?= number_format($ingreso_totales->total_monto_debito, 2) ?></span></label>
        </div>
        <div class="col-md-3">
            <label>Deuda Actual: <span class="tipo_moneda"></span> <span
                        id="total">
                    <?= number_format($ingreso_totales->total_monto_cuota - $ingreso_totales->total_monto_debito, 2) ?></span></label>
        </div>
    </div>

    <table class='table table-striped dataTable table-bordered tableStyle' id="tablaresult" name="tablaresult">
        <thead>
            <tr>
                <th># Compra</th>
                <th># Comprobante</th>
                <th width="30%">Proveedor</th>
                <th>Fecha emisi&oacute;n</th>
                <th>Importe compra</th>
                <th>Inicial</th>
                <th>Importe abonado</th>
                <th>Pendiente de pago</th>
                <th>D&iacute;as Transcurridos</th>
                <th>Tipo</th>
                <?php if($local=="TODOS"){ ?>
                <th>Local</th>
                <?php } ?>
                <th>Accion</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($lstproveedor as $p): ?>
            <tr>
                <td><?= $p->ingreso_id ?></td>
                <td style="text-align: center;">
                <?php
                    $doc = '';
                    if ($p->documento_nombre == 'FACTURA') $doc = "FA";
                    if ($p->documento_nombre == 'NOTA CREDITO') $doc = "NC";
                    if ($p->documento_nombre == 'BOLETA DE VENTA') $doc = "BO";
                    if ($p->documento_nombre == 'GUIA DE REMISION') $doc = "GR";
                    if ($p->documento_nombre == 'PEDIDO COMPRA-VENTA') $doc = "PCV";
                    if ($p->documento_nombre == 'NOTA VENTA') $doc = "NV";

                    if($p->documento_numero != '')
                        echo $doc . ' ' . $p->documento_serie . '-' . sumCod($p->documento_numero, 6);
                    else
                        echo '<span style="color: #0000FF">NO EMITIDO</span>';
                ?>
                </td>
                <?php if(!empty($p->proveedor_nombre)){ ?>
                <td style="white-space: normal;"><?= $p->proveedor_nombre ?></td>
                <?php }else{ ?>
                <td style="white-space: normal;"><?= $p->username ?></td>
                <?php } ?>
                <td><?= date('d/m/Y', strtotime($p->fecha_emision)) ?></td>
                <td><?= $p->simbolo . ' ' . number_format($p->total_ingreso, 2) ?></td>
                <td><?= $p->simbolo . ' ' . number_format($p->inicial, 2) ?></td>
                <td><?= $p->simbolo . ' ' . number_format($p->monto_debito, 2) ?></td>
                <td><?= $p->simbolo . ' ' . number_format($p->monto_cuota - $p->monto_debito, 2) ?></td>
                <td><?= $p->dias_transcurridos ?></td>
                <td><?= $p->tipo_ingreso ?></td>
                <?php if($local=="TODOS"){ ?>
                <td><?= $p->local_nombre; ?></td>
                <?php } ?>
                <td style="white-space: nowrap;">
                    <a class='btn btn-xs btn-default tip' title="Ver Venta"
                       onclick="visualizar(<?= $p->ingreso_id ?>)"><i
                                class="fa fa-search"></i> Ver</a>

                    <a onclick="pagar_venta(<?= $p->ingreso_id ?>)" class='btn btn-xs btn-primary tip'
                       title="Pagar"><i
                                class="fa fa-paypal"></i>
                        Pagar</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <h4>No hay resultados</h4>
<?php endif; ?>

<!-- Seccion Visualizar -->
<div class="modal fade" id="visualizar_venta" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel"
     aria-hidden="true">


</div>
<!--- ----------------- -->

<!-- Pagar Visualizar -->
<div class="modal fade" id="pagar_venta" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel"
     aria-hidden="true">


</div>
<!--- ----------------- -->

<script type="text/javascript">


    $(document).ready(function () {
        TablesDatatables.init();

    });


    function pagar_venta(id) {

        $("#cargando_modal").modal('show');

        $.ajax({
            url: '<?= base_url()?>ingresos/ver_deuda',
            type: 'post',
            data: {'id_ingreso': id},
            success: function (data) {

                $("#cargando_modal").modal('hide');
                $("#pagar_venta").html(data);
                $('#pagar_venta').modal('show');
            }

        })

    }

    function cerrar_visualizar() {

        $('#visualizarPago').modal('hide');
        $('#pagar_venta').modal('hide');
        buscar();
    }
    function visualizar(id) {
        $("#cargando_modal").modal('show');
        $.ajax({
            url: '<?= base_url()?>ingresos/vertodoingreso',
            type: 'post',
            data: {'id_ingreso': id},
            success: function (data) {

                $("#cargando_modal").modal('hide');
                $("#visualizar_venta").html(data);
                $('#visualizar_venta').modal('show');
            }

        })
    }
</script>