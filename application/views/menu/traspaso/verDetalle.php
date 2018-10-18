<div class="modal-dialog" style="width: 60%">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Detalle del traspaso</h3>
        </div>
        <div class="modal-body">
            <div class="row-fluid force-margin">
                <div class="row-fluid">
                    <div class="row">
                        <div class="col-md-2"><label class="control-label">Nro:</label></div>
                        <div class="col-md-3"><?= sumCod($data[0]->id, 6) ?></div>
                        <div class="col-md-1"></div>
                        <div class="col-md-2"><label class="control-label">Fecha:</label></div>
                        <div class="col-md-3"><?= $data[0]->fecha ?></div>
                    </div>
                    <hr class="hr-margin-5">
                    <div class="row">
                        <div class="col-md-2"><label class="control-label">Destino:</label></div>
                        <div class="col-md-3"><?= $data[0]->destino ?></div>
                        <div class="col-md-1"></div>
                        <div class="col-md-2"><label class="control-label">Usuario:</label></div>
                        <div class="col-md-3"><?= $data[0]->username ?></div>
                    </div>
                    <hr class="hr-margin-5">
                    <div class="row">
                        <div class="col-md-2"><label class="control-label">Motivo:</label></div>
                        <div class="col-md-10"><?= $data[0]->motivo ?></div>
                    </div>
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th><?= getCodigoNombre() ?></th>
                            <th>Producto</th>
                            <th>Origen</th>
                            <th>UM</th>
                            <th>Cantidad</th>
                            <th>Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($data as $dato): ?>
                            <tr>
                                <td><?= getCodigoValue($dato->producto_id, $dato->producto_codigo_interno) ?></td>
                                <td><?= $dato->producto_nombre ?></td>
                                <td><?= $dato->origen ?></td>
                                <td><?= $dato->um ?></td>
                                <td><?= $dato->cantidad ?></td>
                                <td>
                                    <a class="btn btn-primary" title="Imprimir" href="#" onclick="imprimir('<?= $dato->id ?>', '<?= $dato->local_origen ?>')">
                                        <i class="fa fa-print"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="modal-footer" align="right">
            <div class="row">
                <div class="text-right">
                    <div class="col-md-12">
                        <input type="button" class='btn btn-danger' value="Cerrar" onclick="cerrarmodal()">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    //Funcion cerrar modal mas busqueda de los traslados (18-10-2018) Carlos Camargo
function cerrarmodal(){
    $('#verModal').modal('hide');
    buscar();
}
</script>