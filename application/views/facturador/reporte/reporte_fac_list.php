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
    <table id="datatable" class="table  dataTable table-bordered tableStyle" style="overflow:scroll">
        <thead>
            <tr>
                <th class="thblack"># Venta</th>
                <th class="thblack">Local</th>
                <th class="thblack">Tipo Venta</th>
                <th class="thblack">Fec. Venta</th>
                <th class="thblack">Fec. Fact.</th>
                <th class="thblack">Documento</th>
                <th class="thblack"><span data-toggle="tooltip" data-placement="top" title="Tipo Documento que Modifica">Doc. Mod.</span></th>
                <th class="thblack"><span data-toggle="tooltip" data-placement="top" title="Numero de Documento que Modifica">Nro. Doc.</span></th>
                <th class="thblack"><span data-toggle="tooltip" data-placement="top" title="Numero de Documento de Facturacion Electronica">Doc.</span></th>
                <th class="thblack">Nro. de Venta</th>
                <th class="thblack">SubTotal</th>
                <th class="thblack">Impuesto</th>
                <th class="thblack">Total</th>
                <th class="thblack"># Doc. Cliente</th>
                <th class="thblack">Nom. Cliente</th>
                <th class="thblack">Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $total_subtotal = 0;
            $total_impuesto = 0;
            $total_total = 0;
            ?>
            <?php foreach ($lists as $list): ?>
                <tr class="info trblack" style="font-weight: bold;">
                    <td><?= $list->venta_id ?></td>
                    <td><?= $list->local_nombre ?></td>
                    <td><?= $list->vfac ?></td>
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
                    $total_subtotal += $list->subtotal;
                    $total_impuesto += $list->impuesto;
                    $total_total += $list->total;
                    ?>
                    <td>
                        <?php
                        $estado = '';
                        $estado_class = '';
                        if ($list->Estado == "NO GENERADO") {
                            $estado_class = 'label-warning';
                            $estado = 'NO GENERADO';
                        } elseif ($list->Estado == "GENERADO") {
                            $estado_class = 'label-info';
                            $estado = 'GENERADO';
                        } elseif ($list->Estado == "ENVIADO") {
                            $estado_class = 'label-warning';
                            $estado = 'ENVIADO';
                        } elseif ($list->Estado == "ACEPTADO") {
                            $estado_class = 'label-success';
                            $estado = 'ACEPTADO';
                        } elseif ($list->Estado == "RECHAZADO") {
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
                <td colspan="9"></td>
                <td style="text-align: right; white-space: nowrap;"><?= number_format($total_subtotal, 2) ?></td>
                <td style="text-align: right; white-space: nowrap;"><?= number_format($total_impuesto, 2) ?></td>
                <td style="text-align: right; white-space: nowrap;"><?= number_format($total_total, 2) ?></td>
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
        $("#datatable").dataTable({
            'paging': false,
            'searching': true,
            'language': {
                'emptyTable': 'No se encontraron registros',
                'info': 'Mostrando _START_ a _END_ de _TOTAL_ resultados',
                'infoEmpty': 'Mostrando 0 a 0 de 0 resultados',
                'infoFiltered': '(filtrado de _MAX_ total resultados)',
                'infoPostFix': '',
                'thousands': ',',
                'lengthMenu': 'Mostrar _MENU_ resultados',
                'loadingRecords': 'Cargando...',
                'processing': 'Procesando...',
                'search': "Buscar:",
                'zeroRecords': 'No se encontraron resultados'}
        });
        $('.dataTables_filter').find('input[type="search"]').each(function () {
            $('input[type="search"]').attr("placeholder", "Buscar");
            $('input[type="search"]').addClass('form-control');
        });
        $('[data-toggle="tooltip"]').tooltip();
        $('[data-toggle="popover"]').popover({
            trigger: 'hover'
        });

        $('#exportar_excel').on('click', function () {
            exportar_excel();
        });

        $("#exportar_pdf").on('click', function () {
            exportar_pdf();
        });

        $('.nav-tabs a[href="#grafico"]').on('shown.bs.tab', function (event) {
            var data = {
                'tipo_ven': $("#tpven").val(),
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
            'tipo_ven': $("#tpven").val(),
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
        var win = window.open('<?= base_url() ?>facturador/reporte/relacion_comprobante/pdf?data=' + JSON.stringify(data), '_blank')
        win.focus()
    }

    function exportar_excel() {
        var data = {
            'tipo_ven': $("#tpven").val(),
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
        var win = window.open('<?= base_url() ?>facturador/reporte/relacion_comprobante/excel?data=' + JSON.stringify(data), '_blank')
        win.focus()
    }
</script>