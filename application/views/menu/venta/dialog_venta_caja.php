<div class="modal-dialog" style="width: 40%">
    <div class="modal-content">
        <div class="modal-header">
            <h4>Cobrar en caja</h4>
        </div>
        <div class="modal-body panel-venta-left">
            <div class="row">
                <div class="form-group">
                    <div class="col-md-3">
                        <label for="caja_numero_venta" class="control-label panel-admin-text">Venta Nro:</label>
                    </div>
                    <div class="col-md-9">
                        <input type="text"
                               class='input-square input-small form-control'
                               value="<?= $next_id?>"
                               id="caja_numero_venta"
                               name="caja_numero_venta"
                               readonly>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="form-group">
                    <div class="col-md-3">
                        <label for="caja_cliente" class="control-label panel-admin-text">Cliente:</label>
                    </div>
                    <div class="col-md-9">
                        <input type="text"
                               class='input-square input-small form-control'
                               id="caja_cliente"
                               name="caja_cliente"
                               readonly>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="form-group">
                    <div class="col-md-3">
                        <label for="caja_total_pagar" class="control-label panel-admin-text">Total a Pagar:</label>
                    </div>
                    <div class="col-md-9">
                        <div class="input-prepend input-append input-group">
                            <?php $md = get_moneda_defecto()?>
                            <label class="input-group-addon tipo_moneda"><?= $md->simbolo ?></label><input
                                type="number"
                                class='input-square input-small form-control'
                                min="0.0"
                                step="0.1"
                                value="0.0"
                                data-value="0.00"
                                id="caja_total_pagar"
                                name="caja_total_pagar"
                                readonly
                                onkeydown="return soloDecimal(this, event);">
                        </div>
                    </div>
                </div>
            </div>


        </div>
        <div class="modal-footer">

            <div class="row">
                <div class="col-md-12">
                    <button class="btn btn-default save_venta_caja" data-imprimir="0"
                            type="button"
                            id="btn_venta_caja"><i
                            class="fa fa-save"></i> (F6)Guardar
                    </button>

                    <a href="#" class="btn btn-default save_venta_caja ocultar_caja"
                       id="btn_venta_caja_imprimir" data-imprimir="1" type="button"><i
                            class="fa fa-print"></i> (F6)Guardar e imprimir
                    </a>
                    <button class="btn btn-danger"
                            type="button"
                            onclick="$('#dialog_venta_caja').modal('hide');"><i
                            class="fa fa-close"></i> Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {


        $(".save_venta_caja").on('click', function () {
            save_venta_caja($(this).attr('data-imprimir'));
        });

    });

    function caja_init(total){

        $("#caja_cliente").val($("#cliente_id option:selected").text().trim());
        $("#caja_total_pagar").val(total);
        $("#dialog_venta_caja").modal('show');
    }

</script>