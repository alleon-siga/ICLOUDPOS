<?php $ruta = base_url(); ?>
<?php $md = get_moneda_defecto() ?>
<style>
    table tfoot tr td {
        font-weight: bold;
    }
</style>
<!--<ul class="nav nav-tabs">
  <li class="active"><a data-toggle="tab" href="#data">Tabla</a></li>
  <li><a data-toggle="tab" href="#grafico">Gr&aacute;fico</a></li>
</ul>-->
<!--<div class="tab-content">
    <div id="data" class="tab-pane fade in active">-->
        <div class="table-responsive">
            <table class='table table-striped dataTable table-bordered no-footer tableStyle' style="overflow:scroll">
                <thead>
                    <tr>
                        <th># Venta</th>
                        <th>Fecha</th>
                        <th>Local</th>
                        <th>Cliente</th>
                        <th># Comprobante</th>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio unitario</th>
                        <th>Importe</th>
                    </tr>
                </thead>
                <tbody>
                <?php $suma = 0;  ?>    
                <?php foreach ($lists as $list): ?>
                    <tr>
                        <td><?= $list->venta_id ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($list->fecha)) ?></td>
                        <td><?= utf8_decode($list->local_nombre) ?></td>
                        <td><?= utf8_decode($list->razon_social) ?></td>
                        <td><?= $list->abr_doc . ' ' . $list->serie . '-' . sumCod($list->numero, 6) ?></td>
                        <td><?= utf8_decode($list->producto_nombre).' '.utf8_decode($list->nota) ?></td>
                        <td><?= $list->cantidad ?></td>
                        <td style="text-align: right;"><?= $list->simbolo ?> <?= number_format($list->precio, 2) ?></td>
                        <td style="text-align: right;"><?= $list->simbolo ?> <?= number_format($list->detalle_importe, 2) ?></td>
                    </tr>
                <?php $suma += $list->detalle_importe ?>
                <?php endforeach ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="8" style="text-align: right;">TOTAL</td>
                        <td style="text-align: right;"><?= !empty($list->simbolo)? $list->simbolo : $md->simbolo ?> <?= number_format($suma, 2) ?></td>
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
    <!--</div>
    <div id="grafico" class="tab-pane fade" style="min-width: 310px; max-width: 800px; height: 400px; margin: 0 auto"></div>
</div>-->
<script type="text/javascript">
    $(document).ready(function () {
        TablesDatatables.init(1);


        $('#exportar_excel').on('click', function () {
            exportar_excel();
        });

        $("#exportar_pdf").on('click', function () {
            exportar_pdf();
        });

        /*$('.nav-tabs a[href="#grafico"]').on('shown.bs.tab', function(event){
            $("#grafico").html($("#loading").html());
            var data = {
                'local_id': $("#local_id").val(),
                'fecha': $("#fecha").val(),
                'producto_id': $("#producto_id").val(),
                'grupo_id': $("#grupo_id").val(),
                'marca_id': $("#marca_id").val(),
                'linea_id': $("#linea_id").val(),
                'familia_id': $("#familia_id").val(),
                'limit': $(".dataTables_length select").val()
            };
        });*/
    });

    function exportar_pdf() {
        var data = {
            'local_id': $("#local_id").val(),
            'fecha': $("#fecha").val(),
            'producto_id': $("#producto_id").val(),
            'grupo_id': $("#grupo_id").val(),
            'marca_id': $("#marca_id").val(),
            'linea_id': $("#linea_id").val(),
            'familia_id': $("#familia_id").val()
        };

        var win = window.open('<?= base_url()?>reporte/hojaColecta/pdf?data=' + JSON.stringify(data), '_blank');
        win.focus();
    }

    function exportar_excel() {
        var data = {
            'local_id': $("#local_id").val(),
            'fecha': $("#fecha").val(),
            'producto_id': $("#producto_id").val(),
            'grupo_id': $("#grupo_id").val(),
            'marca_id': $("#marca_id").val(),
            'linea_id': $("#linea_id").val(),
            'familia_id': $("#familia_id").val()
        };

        var win = window.open('<?= base_url()?>reporte/hojaColecta/excel?data=' + JSON.stringify(data), '_blank');
        win.focus();
    }
</script>