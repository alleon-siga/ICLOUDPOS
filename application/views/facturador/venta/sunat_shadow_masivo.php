<div class="modal-dialog" style="width: 40%;">
    <div class="modal-content">
        <div class="modal-header " >
            <h3>FACTURAR</h3>
        </div>
        <div class="modal-body">
            <h3>Deseas Facturar todos los comprobantes?</h3>
        </div>
        <div class="modal-footer" align="right">
            <div class="row">
                <div class="text-right">
                    <div class="col-md-12">
                        <input type="button" class='btn btn-success'  value="Si">
                        
                        <input type="button" class='btn btn-danger' value="Cerrar" >
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<script>
    function cerrarmodal(id_venta) {
        $('#remove_ventaconvertida_shadow').modal('hide');
        detalle(id_venta);
    }
    
</script>