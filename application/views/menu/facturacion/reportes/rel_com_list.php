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
                <th>Tipo Doc. Mod.</th>
                <th>Nro. Doc. Mod.</th>
                <th>Nro. Doc. Fact. Elec.</th>
                <th>Nro. de Venta</th>
                <th># Doc. Cliente</th>
                <th>Nom. Cliente</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($lists as $list): ?>
                <tr class="info" style="font-weight: bold;">
                    <td><?= $list->venta_id ?></td>
                    <td><?= date('m/d/Y', strtotime($list->Fec_Venta)) ?></td>
                    <td><?= date('m/d/Y', strtotime($list->FecFacturacionElectr)) ?></td>
                    <td style="white-space: normal;"><?= $list->documento ?></td>
                    <td><?= $list->documento_mod_tipo ?></td>
                    <td><?= $list->documento_mod_numero ?></td>                
                    <td style="text-align: right; white-space: nowrap;"><?= $list->documento_numero ?></td>
                    <td><?= $list->numero ?></td>
                    <td style="text-align: right; white-space: nowrap;"><?= $list->cliente_identificacion ?></td>
                    <td style="text-align: right; white-space: nowrap;"><?= $list->cliente_nombre ?></td>
                    <td style="text-align: right; white-space: nowrap;"><?= $list->Estado ?></td>

                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script type="text/javascript">

$(document).ready(function () {
        $('#datatable').DataTable( {
        "paging":   false,
        "searching": false,
        "info":false
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
            'moneda_id': $('#moneda_id').val()
        }

        var win = window.open('<?= base_url() ?>reporte/creditoFiscal/pdf?data=' + JSON.stringify(data), '_blank')
        win.focus()
    }

    function exportar_excel() {
        var data = {
            'local_id': $('#local_id').val(),
            'fecha': $('#fecha').val(),
            'doc_id': $('#doc_id').val(),
            'moneda_id': $('#moneda_id').val()
        }

        var win = window.open('<?= base_url() ?>reporte/creditoFiscal/excel?data=' + JSON.stringify(data), '_blank')
        win.focus()
    }
</script>