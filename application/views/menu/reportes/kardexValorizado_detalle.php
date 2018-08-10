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
            <h5 align="center">DOCUMENTO DE TRASLADO, COMPROBANTE DE PAGO, DOCUMENTO INTERNO O SIMILAR</h5>
            <table class="table datatable datatables_filter table-bordered table-striped tableStyle" id="tabledetail">
                <thead>
                    <tr>
                        <th rowspan="2" width="5%">Id</th>
                        <th rowspan="2" width="10%">Fecha Registro</th>
                        <th rowspan="2" width="10%">Tipo</th>
                        <th rowspan="2" width="10%">Serie</th>
                        <th rowspan="2" width="10%">Numero</th>
                        <th rowspan="2" width="10%">Tipo de Operacion</th>
                        <th rowspan="2" width="10%">Usuario</th>
                        <th rowspan="2" width="10%">Referencia</th>
                        <th colspan="3">Entradas</th>
                        <th colspan="3">Salidas</th>
                        <th colspan="3">Saldo Final</th>
                    </tr>
                    <tr>
                        <th>Cantidad</th>
                        <th>Costo Unit.</th>
                        <th>Costo Total</th>
                        <th>Cantidad</th>
                        <th>Costo Unit.</th>
                        <th>Costo Total</th>
                        <th>Cantidad</th>
                        <th>Costo Unit.</th>
                        <th>Costo Total</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    if(empty($kardex_ant)){
                        $finalCant = $finalCu = $finalCt = 0;
                    }else{
                        $finalCant = $kardex_ant->cantidad_saldo;
                        $finalCu = $kardex_ant->costo;
                        $finalCt = $finalCant * $finalCu;
                    }
                    if(!empty($kardex)){
                        if($kardex[0]->simbolo!=1029){
                            $kardex[0]->simbolo = 'S/';
                        }
                ?>                    
                    <tr>
                        <td></td>
                        <td></td>
                        <td>Otros</td>
                        <td></td>
                        <td></td>
                        <td>SALDO ANTERIOR</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td style="text-align: right;"><?= $finalCant ?></td>
                        <td><?= $kardex[0]->simbolo.' '.number_format($finalCu, 2) ?></td>
                        <td><?= $kardex[0]->simbolo.' '.number_format($finalCt, 2) ?></td>
                    </tr>
                <?php
                    }
                    foreach ($kardex as $k):
                        if($k->io == 1){
                            $finalCant += $k->cantidad;
                            $finalCt += $k->cantidad * $k->costo;
                        }else{
                            $finalCant -= $k->cantidad;
                            $finalCt -= $k->cantidad * $k->costo;
                        }

                        if($finalCant==0){
                            $finalCu = 0;
                        }else{
                            $finalCu = $finalCt / $finalCant;    
                        }
                        
                        if($k->simbolo!=1029){
                            $k->simbolo = 'S/';
                        }
                ?>
                    <tr>
                        <td style="white-space: normal;"><?= $k->id ?></td>
                        <td style="white-space: normal;"><?= date('d/m/Y H:i:s', strtotime($k->fecha)) ?></td>
                        <?php $tipo = get_tipo_doc($k->tipo) ?>
                        <td style="white-space: normal;"><?= $tipo['value'] ?></td>
                        <td style="white-space: normal;"><?= $k->serie ?></td>
                        <td style="white-space: normal;"><?= $k->numero ?></td>
                        <?php $operacion = get_tipo_operacion($k->operacion) ?>
                        <td style="white-space: normal;"><?= $operacion['value'] ?></td>
                        <td style="white-space: normal;"><?= $k->username ?></td>
                        <td style="white-space: normal;"><?= $k->ref_val ?></td>
                        <?php if($k->io == 1){ ?>
                            <td style="text-align: right;"><?php if($k->producto_cualidad=='MEDIBLE'){ echo bcdiv($k->cantidad,1,0); }else{ echo $k->cantidad; } ?></td>
                            <td><?= $k->simbolo.' '.number_format($k->costo, 2) ?></td>
                            <td><?= $k->simbolo.' '.number_format($k->cantidad * $k->costo, 2) ?></td>
                        <?php }else{ ?>
                            <td></td>
                            <td></td>
                            <td></td>
                        <?php } ?>
                        <?php if($k->io == 2){ ?>
                            <td style="text-align: right;"><?php if($k->producto_cualidad=='MEDIBLE'){ echo bcdiv($k->cantidad,1,0); }else{ echo $k->cantidad; } ?></td>
                            <td><?= $k->simbolo.' '.number_format($k->costo, 2) ?></td>
                            <td><?= $k->simbolo.' '.number_format($k->cantidad * $k->costo, 2) ?></td>
                        <?php }else{ ?>
                            <td></td>
                            <td></td>
                            <td></td>
                        <?php } ?>
                        <td style="text-align: right;"><?= $finalCant ?></td>
                        <td><?= $k->simbolo.' '.number_format($finalCu, 2) ?></td>
                        <td><?= $k->simbolo.' '.number_format($finalCt, 2) ?></td>
                    </tr>
                <?php
                    endforeach;
                ?>
                </tbody>
            </table>
        </div>
        <div class="modal-footer">
            <div class="row" style="float: left;">
                <div class="col-md-12">
                    <a id="exportar_excel" href="#" class="btn btn-default" data-toggle="tooltip"
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

        $('#exportar_excel').attr('href', '<?= base_url()?>reporte/exportar_kardex/' + producto_id + '/' + local_id + '/' + mes + '/' + year + '/' + dia_min + '/' + dia_max);

    }
</script>