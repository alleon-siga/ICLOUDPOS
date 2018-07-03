<link rel="stylesheet" href="<?= base_url() ?>recursos/js/datepicker-range/daterangepicker.css">
<div class="modal-dialog" style="width: 60%">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Agregar gasto a la compra</h3>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-3">
                    <label class="control-label panel-admin-text">Fecha del Gasto</label>
                    <input type="text" id="date_range" class="form-control" readonly style="cursor: pointer;"
                           name="daterange" value="<?= date('01/m/Y') ?> - <?= date('d/m/Y') ?>"/>
                </div>

                <div class="col-md-3">
                    <label class="control-label panel-admin-text">Documento</label>
                    <select id="gasto_tipo_documento" class="form-control">
                        <option value="">TODOS</option>
                        <?php foreach ($documentos as $d): ?>
                            <option value="<?= $d->id_doc ?>"><?= $d->des_doc ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6 text-right">
                    <label class="control-label panel-admin-text" style="color: #f0f0f0;">.</label><br>
                    <button type="button" id="filter_gasto" class="btn btn-default">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
            </div>
            <br>
            <div class="row" id="table_list">

                <?= isset($table_list) ? $table_list : '' ?>

            </div>

        </div>

        <div class="modal-footer" align="right">
            <div class="row">
                <div class="text-right">
                    <div class="col-md-12">
                        <input id="btn_add_gasto" type="button" class='btn btn-default' value="Agregar Gastos">
                        <input type="button" class='btn btn-danger' value="Cerrar"
                               data-dismiss="modal">
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>recursos/js/datepicker-range/moment.min.js"></script>
<script src="<?php echo base_url(); ?>recursos/js/datepicker-range/daterangepicker.js"></script>
<script>
    $(function () {

        $('input[name="daterange"]').daterangepicker({
            "locale": {
                "format": "DD/MM/YYYY",
                "separator": " - ",
                "applyLabel": "Aplicar",
                "cancelLabel": "Cancelar",
                "fromLabel": "De",
                "toLabel": "A",
                "customRangeLabel": "Personalizado",
                "daysOfWeek": [
                    "Do",
                    "Lu",
                    "Ma",
                    "Mi",
                    "Ju",
                    "Vi",
                    "Sa"
                ],
                "monthNames": [
                    "Enero",
                    "Febrero",
                    "Marzo",
                    "Abril",
                    "Mayo",
                    "Junio",
                    "Julio",
                    "Agosto",
                    "Septiembre",
                    "Octubre",
                    "Noviembre",
                    "Diciembre"
                ],
                "firstDay": 1
            }
        });

        $('#filter_gasto').on('click', function () {

            $.ajax({
                url: '<?= base_url()?>ingresos/get_gastos/filter',
                type: 'POST',
                data: {
                    local_id: $('#local').val(),
                    moneda_id: $('#monedas').val(),
                    documento_id: $('#gasto_tipo_documento').val(),
                    fecha: $('#date_range').val()
                },
                success: function (data) {
                    $("#table_list").html(data);
                },
                error: function () {
                    alert('Error inesperado')
                }
            });
        });

        $('#btn_add_gasto').on('click', function () {

            lst_gastos = [];
            $('.gastos_check').each(function (key, elem) {
                var item = $(elem);
                if (item.prop('checked')) {
                    lst_gastos.push({
                        id: item.attr('data-id'),
                        total: $('#gasto_total_' + item.attr('data-id')).html(),
                    });
                }
            });

            updateView(get_type_view());
            $('#dialog_gastos_modal').modal('hide');

        });

    });
</script>