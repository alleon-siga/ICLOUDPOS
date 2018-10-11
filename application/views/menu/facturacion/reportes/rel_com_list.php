<?php $ruta = base_url(); ?>
<?php $md = get_moneda_defecto() ?>
<style>
    table tfoot tr td {
        font-weight: bold;
    }

    .b-default {
        background-color: #55c862;
        color: #fff;
    }

    .b-warning {
        background-color: #F78181;
        color: #fff;
    }

    .negativo {
        color: red;
    }
</style>
<div class="table-responsive">
    <table id="datatable" class="table dataTable table-bordered tableStyle">
        <thead>
            <tr>
                <th># Venta</th>
                <th>Fec. Venta</th>
                <th>Fec. Fact.</th>
                <th>Documento</th>
                <th ><span data-toggle="tooltip" data-placement="top" title="Tipo Documento que Modifica">Doc. Mod.</span></th>
                <th ><span data-toggle="tooltip" data-placement="top" title="Numero de Documento que Modifica">Nro. Doc.</span></th>
                <th><span data-toggle="tooltip" data-placement="top" title="Numero de Documento de Facturacion Electronica">Doc.</span></th>
                <th>Nro. de Venta</th>
                <th>SubTotal</th>
                <th>Impuesto</th>
                <th>Total</th>
                <th># Doc. Cliente</th>
                <th>Nom. Cliente</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php
        $total_subtotal = 0;
        $total_impuesto = 0;
        $total_total = 0;
        ?>
            <?php foreach ($lists as $list): ?>
                <tr class="info" style="font-weight: bold;">
                    <td><?= $list->venta_id ?></td>
                    <td><?= date('d/m/Y', strtotime($list->Fec_Venta)) ?></td>
                    <td><?= date('d/m/Y', strtotime($list->FecFacturacionElectr)) ?></td>
                    <td><?= $list->documento ?></td>
                    <td><?= $list->documento_mod_tipo ?></td>
                    <td><?= $list->documento_mod_numero ?></td>                
                    <td><?= $list->documento_numero ?></td>
                    <td><?= $list->numero ?></td>
                    <td><?= $list->subtotal ?></td>
                    <td><?= $list->impuesto ?></td>
                    <td><?= $list->total ?></td>
                    <td><?= $list->cliente_identificacion ?></td>
                    <td  style="white-space: normal;"><?= $list->cliente_nombre ?></td>
                    <?php 
                    $total_subtotal+=$list->subtotal;
                    $total_impuesto+=$list->impuesto;
                    $total_total+=$list->total;
                    ?>
                    <td>
                        <?php
                            $estado = '';
                            $estado_class = '';
                            if ($list->Estado=="NO GENERADO") {
                                $estado_class = 'label-warning';
                                $estado = 'NO GENERADO';
                            } elseif ($list->Estado=="GENERADO") {
                                $estado_class = 'label-info';
                                $estado = 'GENERADO';
                            } elseif ($list->Estado=="ENVIADO") {
                                $estado_class = 'label-warning';
                                $estado = 'ENVIADO';
                            } elseif ($list->Estado=="ACEPTADO") {
                                $estado_class = 'label-success';
                                $estado = 'ACEPTADO';
                            } elseif ($list->Estado=="RECHAZADO") {
                                $estado_class = 'label-danger';
                                $estado = 'RECHAZADO';
                            }

                            ?>
                            <div title="Descripci&oacute;n del Estado" data-content="<?= $list->nota ?>"
                                    data-toggle="popover"
                                    class="label <?= $estado_class ?>"
                                    data-placement="top"
                                    style="font-size: 1em; padding: 2px; cursor: pointer; white-space: nowrap;">
                                <?= $estado ?>
                            </div>
                      
                    </td>

                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td >Totales:</td>
                <td colspan="7"></td>
                <td style="text-align: right; white-space: nowrap;"><?= number_format($total_subtotal,2) ?></td>
                <td style="text-align: right; white-space: nowrap;"><?= number_format($total_impuesto,2) ?></td>
                <td style="text-align: right; white-space: nowrap;"><?= number_format($total_total,2) ?></td>
                <td colspan="3"></td>
            </tr>
        </tfoot>
    </table>
</div>
<div class="row">
            <div class="col-md-12">
                <br>
                <button type="button" id="exportar_excel" title="Exportar Excel" class="btn btn-primary">
                    <i class="fa fa-file-excel-o fa-fw"></i>
                </button>
                <button type="button" id="exportar_pdf" title="Exportar Pdf" class="btn btn-primary">
                    <i class="fa fa-file-pdf-o fa-fw"></i>
                </button>
            </div>
        </div>
<script type="text/javascript">

$(document).ready(function () {
    $('[data-toggle="tooltip"]').tooltip();
            $('[data-toggle="popover"]').popover({
                trigger: 'hover'
            });
        
    $('#datatable').removeAttr('width').DataTable( {
        "paging":   false,
        "searching": false
    } );
        $('#exportar_excel').on('click', function () {
            exportar_excel();
        });

        $("#exportar_pdf").on('click', function () {
            exportar_pdf();
        });

        $('.nav-tabs a[href="#grafico"]').on('shown.bs.tab', function(event){
            var data = {
                'local_id': $("#local_id").val(),
                'producto_id': $("#producto_id").val(),
                'grupo_id': $("#grupo_id").val(),
                'marca_id': $("#marca_id").val(),
                'linea_id': $("#linea_id").val(),
                'familia_id': $("#familia_id").val()
            };
        });
    });
    function exportar_pdf() {
        var data = {
            'local_id': $('#local_id').val(),
            'fecha': $('#fecha').val(),
            'doc_id': $('#doc_id').val(),
            'estado_id': $('#estado_id').val()
        }
        if ($('#bloqueofecha').prop('checked')) {
                                data.fecha_flag = 1;
                            } else {
                                data.fecha_flag = 0;
                            }
        var win = window.open('<?= base_url() ?>facturacion/relacion_comprobante/pdf?data=' + JSON.stringify(data), '_blank')
        win.focus()
    }

    function exportar_excel() {
        var data = {
            'local_id': $('#local_id').val(),
            'fecha': $('#fecha').val(),
            'doc_id': $('#doc_id').val(),
            'estado_id': $('#estado_id').val()
        }
        if ($('#bloqueofecha').prop('checked')) {
                                data.fecha_flag = 1;
                            } else {
                                data.fecha_flag = 0;
                            }
        var win = window.open('<?= base_url() ?>facturacion/relacion_comprobante/excel?data=' + JSON.stringify(data), '_blank')
        win.focus()
    }
</script>