<?php $md = get_moneda_defecto() ?>
<?php if (count($ingresos) > 0): ?>
    <br>
    <div class="row">
        <div class="col-md-6"></div>
        <div class="col-md-2">
            <label>Subtotal: <?= $moneda->simbolo ?> <span
                        id="subtotal"><?= number_format($ingreso_totales->subtotal, 2) ?></span></label>
        </div>
        <div class="col-md-2">
            <label>Impuesto: <?= $moneda->simbolo ?> <span
                        id="impuesto"><?= number_format($ingreso_totales->impuesto, 2) ?></span></label>
        </div>
        <div class="col-md-2">
            <label>Total: <?= $moneda->simbolo ?> <span
                        id="total"><?= number_format($ingreso_totales->total, 2) ?></span></label>
        </div>
    </div>
    <div class="table-responsive" id="tabla">


        <table class="table table-striped dataTable table-bordered tableStyle" id="tablaresultado">
            <thead>
            <tr>
                <th>ID</th>
                <th>Fecha Doc</th>
                <th>Doc</th>
                <th>Num Doc</th>
                <th>RUC Provedor</th>
                <th>Proveedor</th>
                <th>Tipo Pago</th>
                <?php if ($md->id_moneda != $moneda->id_moneda): ?>
                    <th>Tipo Cambio</th>
                <?php endif; ?>
                <th>SubTotal</th>
                <th>Impuesto</th>
                <th>Total</th>
                <th>Estado</th>
                <th>Usuario</th>
                <th>fec Registro</th>
                <th>Ver</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($ingresos as $ingreso): ?>
                <tr>
                    <td><?= $ingreso->id ?></td>
                    <td><?= $ingreso->estado == 'COMPLETADO' ? date('d/m/Y', strtotime($ingreso->fecha_emision)) : '' ?></td>
                    <td><?= $ingreso->documento ?></td>
                    <td><?= $ingreso->documento_numero ?></td>
                    <td><?= $ingreso->proveedor_ruc ?></td>
                    <td><?= $ingreso->proveedor_nombre ?></td>
                    <td><?= $ingreso->tipo_pago ?></td>
                    <?php if ($md->id_moneda != $moneda->id_moneda): ?>
                        <td><?= $ingreso->tasa ?></td>
                    <?php endif; ?>
                    <td><?= number_format($ingreso->subtotal, 2) ?></td>
                    <td><?= number_format($ingreso->impuesto, 2) ?></td>
                    <td><?= number_format($ingreso->total, 2) ?></td>
                    <td><?= $ingreso->estado ?></td>
                    <td><?= $ingreso->usuario_nombre ?></td>
                    <td><?= date('d/m/Y', strtotime($ingreso->fecha_registro)) ?></td>
                    <td>
                        <a href="#" onclick="verCompra('<?= $ingreso->id ?>');" style="margin-right: 5px;">
                            <i class="fa fa-search"></i>
                        </a>

                        <?php if ($ingreso->estado == "PENDIENTE"): ?>
                            <a href="#" onclick="editaringreso('<?= $ingreso->id ?>');">
                                <i class="fa fa-money"></i>
                            </a>
                        <?php endif ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="modal fade" id="ingresomodal" style="width: 85%; overflow: auto;
  margin: auto;" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">


    </div>

    <div id="valorizar_ingreso" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-close"></i>
                </button>

                <h3></h3>
            </div>
            <div class="modal-body" id="ingresomodalbody">

            </div>

        </div>
    </div>

    <div id="load_div" style="display: none;">
        <div class="row" id="loading" style="position: relative; top: 50px; z-index: 500000;">
            <div class="col-md-12 text-center">
                <div class="loading-icon"></div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function () {
            TablesDatatables.init();
        });

        function verCompra(id) {

            $('#ver_compra').html($('#load_div').html());
            $('#ver_compra').modal('show');
            $("#ver_compra").load('<?= base_url()?>ingresos/form/' + id);

        }

        function editaringreso(id, facturar) {

            $("#load_div").show();
            $("#ingresomodalbody").html('');
            /*este metodo llamado editaringreso, es usado tanto para facturar ingreso, como para valorizar el documento,
             * solo que uno envia el parametro,   y el otro no*/
            //$("#load_div").modal('show');
            if (facturar != undefined) {
                facturar = "SI";
            } else {
                facturar = "NO";
            }
            $.ajax({
                url: '<?php echo base_url()?>ingresos',
                data: {'idingreso': id, 'editar': 1, 'costos': 'true', 'facturar': facturar},
                type: 'post',
                success: function (data) {
                    $('#ingresomodal').html($("#valorizar_ingreso").html());
                    $("#ingresomodalbody").html(data);
                },
                complete: function () {
                    $("#load_div").hide();

                }

            });

            $('#ingresomodal').html($("#load_div").html());
            $("#ingresomodal").modal('show');

        }
    </script>
<?php else: ?>
    <h5>No se encontraron resultados</h5>
<?php endif; ?>