<div class="modal-dialog" style="width: 40%;">
    <div class="modal-content">
        <div class="modal-header " style="background-color: red !important;">
            <h3>Eliminar</h3>
        </div>
        <div class="modal-body">
            <h3>Deseas Eliminar?</h3>
        </div>
        <div class="modal-footer" align="right">
            <div class="row">
                <div class="text-right">
                    <div class="col-md-12">
                        <?php foreach ($ventacon as $vc): ?>
                        <input type="button" class='btn btn-danger' id="elishadow" onclick="eliminar(<?= $vc->id ?>,<?= $vc->venta_id ?>)" value="Si">
                        
                        <input type="button" class='btn btn-success' value="Cerrar" onclick="cerrarmodal(<?= $vc->venta_id ?>)">
                        <?php endforeach; ?>
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