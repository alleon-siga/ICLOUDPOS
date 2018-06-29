<style>
    .totales {
        width: 100%;
        text-align: right;
    }

    .totales tr td {
        padding: 5px 0;
        font-weight: bold;
    }
</style>
<div class="modal-dialog" style="width: 45%">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Crear Comprobante electr&oacute;nico</h3>
        </div>
        <div class="modal-body">
            <input type="hidden" id="venta_id" value="<?= $venta->venta_id ?>">
            <div class="row">
                <label class="control-label col-md-offset-1 col-md-5">Fecha de Facturaci&oacute;n</label>
                <div class="col-md-5">
                    <input type="text" id="fecha_facturacion" value="<?= date('d/m/Y') ?>" class="form-control"
                           readonly style="cursor: pointer;">
                </div>
            </div>
            <br>
            <div class="row">
                <label class="control-label col-md-offset-1 col-md-5">Tipo de Comprobante</label>
                <div class="col-md-5">
                    <select id="tipo_documento" class="form-control">
                        <option></option>
                        <option value="03">BOLETA UNICA</option>
                        <option value="01">FACTURA</option>
                        <option value="99">MULTIPLES BOLETAS (< 700)</option>
                    </select>
                </div>
            </div>
            <br>
            <div class="row" id="descuento_block" style="display: block;">
                <label class="control-label col-md-offset-1 col-md-5">Aplicar Descuento</label>
                <div class="col-md-5">
                    <div class="input-group">
                        <input type="number" class="form-control" id="descuento" value="0" min="0" max="100">
                        <span class="input-group-addon">%</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer" align="right">
            <div class="row">
                <div class="text-right">
                    <div class="col-md-12">
                        <input id="crear_comprobante" type="button" class='btn btn-default' value="Crear Comprobante">

                        <input type="button" class='btn btn-danger' value="Cerrar"
                               data-dismiss="modal">
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    $(function () {

//        $('#tipo_documento').on('change', function () {
//
//            if ($(this).val() == '03' || $(this).val() == '99') {
//                $('#descuento_block').show();
//            }
//            else {
//                $('#descuento_block').hide();
//            }
//        });

//        $('#fecha_facturacion').datepicker({format: 'dd/mm/yyyy'});

        $('#crear_comprobante').on('click', function () {

            if ($('#tipo_documento').val() == '') {
                show_msg('warning', 'El tipo de documento es requerido');
                return false;
            }


            $("#dialog_venta_declarar").modal('hide');
            $("#barloadermodal").modal('show');

            $.ajax({
                url: '<?php echo base_url() . 'facturacion/notas/crear_comprobante'; ?>',
                type: 'POST',
                data: {
                    'venta_id': $('#venta_id').val(),
                    'tipo_documento': $('#tipo_documento').val(),
                    'descuento': $('#descuento').val(),
                    'fecha_facturacion': $('#fecha_facturacion').val()
                },
                success: function (data) {

                    if (data.facturacion.estado == 1) {
                        show_msg('success', '<h4>Facturacion Electronica:</h4> ' + data.facturacion.nota);
                    }
                    else {
                        show_msg('danger', '<h4>Facturacion Electronica:</h4> ' + data.facturacion.nota);
                    }

                    $("#barloadermodal").modal('hide');
                    get_notas();
                },
                error: function () {
                    alert('Error inesperado');
                    $("#barloadermodal").modal('hide');
                }
            });

        });
    });
</script>