<?php $md = get_moneda_defecto() ?>
<div class="modal-dialog" style="width: 40%">
    <div class="modal-content">
        <div class="modal-header">
            <h4>Terminar Cotizacion</h4>
        </div>
        <div class="modal-body panel-venta-left">

            <div class="row" id="vc_total_pagar_block">
                <div class="form-group">
                    <div class="col-md-3">
                        <label for="vc_total_pagar" class="control-label panel-admin-text">Total a Pagar:</label>
                    </div>
                    <div class="col-md-9">
                        <div class="input-prepend input-append input-group">
                            <label class="input-group-addon tipo_moneda"><?= $md->simbolo ?></label><input
                                    type="number"
                                    class='input-square input-small form-control'
                                    min="0.0"
                                    step="0.1"
                                    value="0.0"
                                    data-value="0.00"
                                    id="vc_total_pagar"
                                    name="vc_total_pagar"
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
                    <button class="btn btn-default save_venta_contado" data-imprimir="0"
                            type="button"
                            id="btn_venta_contado"><i
                                class="fa fa-save"></i> Guardar


                    </button>

                    <a href="#" class="btn btn-default save_venta_contado ocultar_caja"
                       id="btn_venta_contado_imprimir" data-imprimir="1" type="button"><i
                                class="fa fa-print"></i> (F6)Guardar e imprimir
                    </a>
                    <button class="btn btn-danger"
                            type="button"
                            onclick="$('#dialog_cotizar').modal('hide');"><i
                                class="fa fa-close"></i> Cancelar
                    </button>
                    <div style="display: none">

                        <input type="button" id="impr" value="imprimir">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {

        $(".save_venta_contado").on('click', function () {
            save_cotizar($(this).attr('data-imprimir'));
        });

        $('#impr').on('click', function(){
            alert('<?= base_url()?>' + '/cotizar/exportar_pdf/' + $(this).attr('data-id'))

        })

    });

</script>