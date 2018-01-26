<div class="modal-dialog" style="width: 40%">
    <div class="modal-content">
        <div class="modal-header">
            <h4>Confirmar Pedido de Venta</h4>
        </div>
        <div class="modal-body">
            <h5>Total a Pagar: <span id="total_pagar_caja"></span></h5>
            <h5>Productos: </h5>
            <div id="" class="table-responsive" style="height: 400px;    overflow-y: auto;">
                <table class="table dataTable dataTables_filter table-bordered">
                    <thead id="thead_caja">
                    <tr style="background-color: #B1AEAE;color:#fff !important;">
                        <th>#</th>
                        <th>Nombre</th>
                        <th>UM</th>
                        <th>Cantidad</th>
                        <th>Precio</th>
                        <th>Subtotal</th>
                    </tr>
                    </thead>
                    <tbody id="tbody_caja">


                    </tbody>
                </table>
            </div>


        </div>
        <div class="modal-footer">

            <div class="row">
                <div class="col-md-12">
                    <button class="btn btn-default" type="button" id="guardar_venta_caja"
                            onclick="hacerventa(0)"><i
                            class="fa fa-save"></i> Confirmar


                    </button>
                    <button class="btn btn-default" onclick="$('#generarventa_caja').modal('hide');" type="button"><i
                            class="fa fa-close"></i> Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>