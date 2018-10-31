<?php $ruta = base_url(); ?>

<div class="modal-dialog" style="width: 90%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Kardex: <?= $local->local_nombre ?> </h4>
        </div>
        <div class="modal-body">
            <h5 class="row">

                <div class="col-md-6"><label>Descripcion: </label>
                    <?= getCodigoValue(sumCod($producto->producto_id), $producto->producto_codigo_interno) . " - " . $producto->producto_nombre ?>
                </div>

                <div class="col-md-3"><label>Unidad de Medida: </label> <?= $unidad ?></div>

                <div class="col-md-3"><label>Periodo: </label> <?= getMes($mes) ?> <?= $year ?></div>

            </h5>
            <table class="table datatable datatables_filter table-bordered table-striped tableStyle"
                   id="tabledetail">
                <thead>
                <tr>
                    <th colspan="11" style="text-align: center;">DOCUMENTO DE TRASLADO, COMPROBANTE DE PAGO, DOCUMENTO
                        INTERNO O SIMILAR
                    </th>
                </tr>
                <tr>
                    <th>Id</th>
                    <th>Fecha Registro</th>
                    <th>Tipo</th>
                    <th>Serie</th>
                    <th>Numero</th>
                    <th>Tipo de Operacion</th>
                    <th>Responsable</th>
                    <th>Referencia</th>
                    <th>Entradas</th>
                    <th>Salidas</th>
                    <th>Saldo Final</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($kardex as $k): ?>
                    <tr>
                        <td><?= $k->id ?></td>
                        <td><?= date('d/m/Y H:i:s', strtotime($k->fecha)) ?></td>
                        <?php $tipo = get_tipo_doc($k->tipo) ?>
                        <td><?= $tipo['value'] ?></td>
                        <td><?= $k->serie ?></td>
                        <td><?= $k->numero ?></td>
                        <?php $operacion = get_tipo_operacion($k->operacion) ?>
                        <td><?= $operacion['value'] ?></td>
                        <td><?= $k->nombre ?></td>
                        <td style="white-space: normal;"><?= $k->ref_val ?></td>
                        <?php if ($k->io == 1): ?>
                            <?php if($k->producto_cualidad=='MEDIBLE'){ ?>
                                <td style="text-align: right;"><?=  bcdiv($k->cantidad,1,0) ?></td>
                            <?php }else{ ?>
                                <td style="text-align: right;"><?= $k->cantidad ?></td>
                            <?php } ?>
                            <td style="text-align: right;"></td>
                        <?php elseif ($k->io == 2): ?>
                            <td style="text-align: right;"></td>
                            <?php if($k->producto_cualidad=='MEDIBLE'){ ?>
                                <td style="text-align: right;"><?= bcdiv($k->cantidad,1,0) ?></td>
                            <?php }else{ ?>
                                <td style="text-align: right;"><?= $k->cantidad ?></td>    
                            <?php } ?>
                            
                        <?php endif; ?>
                        <?php if($k->producto_cualidad=='MEDIBLE'){ ?>
                            <td style="text-align: right;"><?= bcdiv($k->cantidad_saldo,1,0) ?></td>
                        <?php }else{ ?>
                            <td style="text-align: right;"><?= $k->cantidad_saldo ?></td>    
                        <?php } ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

        </div>


        <div class="modal-footer">
            <div class="row" style="float: left;">
                <div class="col-md-12">
                    <a id="exportar_excel" href="#" class="btn btn-default btn-md" data-toggle="tooltip"
                       title="Exportar a Excel" data-original-title="fa fa-file-excel-o">
                        <i class="fa fa-file-excel-o fa-fw"></i>
                    </a>
                </div>
            </div>

            <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>

        </div>
    </div>
    <!-- /.modal-content -->
</div>

<script src="<?php echo $ruta ?>recursos/js/pages/tablesDatatables.js"></script>
<script>
    $(function () {

        //$("#tabledetail").dataTable();

        exportar('<?=$producto->producto_id?>');

    });

    function exportar(producto_id) {

        var mes, year, dia_min, dia_max;
        var local_id = $("#local_id").val();
        if ($("#mes").val() != "") {
            mes = $("#mes").val();
        }
        else
            return false;

        if ($("#year").val() != "") {
            year = $("#year").val();
        }
        else
            return false;

        if ($("#dia_min").val() != "") {
            dia_min = $("#dia_min").val();
        }
        else
            return false;

        if ($("#dia_max").val() != "") {
            dia_max = $("#dia_max").val();
        }
        else
            return false;

        $('#exportar_excel').attr('href', '<?= base_url()?>kardex/exportar_kardex/' + producto_id + '/' + local_id + '/' + mes + '/' + year + '/' + dia_min + '/' + dia_max);

    }
</script>
