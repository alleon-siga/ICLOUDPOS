
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header" style="background-color: red !important;">
            <h3>Venta Eliminada</h3>
        </div>
        <div class="modal-body">
            
        </div>
        <div class="modal-footer" align="right">
            <div class="row">
                <div class="text-right">
                    <div class="col-md-12">
                        <input type="button" class='btn btn-info' value="Aceptar" id="cerrarmodal">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
$(function(){
    $("#cerrarmodal").on("click", function(){
        $('#remove_ventaconvertida_shadow').modal('hide');
        $('#dialog_venta_detalle_convertidos').show();
    })
})
</script>