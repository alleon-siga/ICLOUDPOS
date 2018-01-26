<form name="formagregar" id="agregarSeries"  action="<?= base_url() ?>seriescalzado/guardar" method="post" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Nueva Serie Calzado</h4>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-4">
                            <label class="control-label panel-admin-text">Letra de la Serie</label>
                        </div>
                        <div class="col-md-7">
                            <select name="serie" id="serie" class="form-control">
                                <option value="">Seleccione</option>
                            <?php for ($i=65;$i<=90;$i++):?>
                                <option value="<?=chr($i)?>" 
                                    <?=@$serie->serie == chr($i) ? 'selected' : '' ?>><?=chr($i)?></option>
                            <?php endfor;?>
                            </select>
                        </div>
                    </div>
                </div>
                <br>

                <div class="row">
                    <div class="col-md-4">
                        <label class="control-label panel-admin-text">Rango de Tallas</label>
                    </div>
                    <div class="col-md-7">
                        <input type="text" name="rango" id="rango" class="form-control"
                            value="<?=str_replace("|", ", ", @$serie->rango)?>">
                        <div style="text-align: right;">
                            <i style="color: #e74c3c;">* Inserte los rangos separados por coma.</i>
                            <br><i style="color: #e74c3c;">Ej: 16,17,18,19,20,21</i>
                        </div>
                    </div>
                </div>



            </div>
            <div class="modal-footer">
                <button type="button" id="btn_save" class="btn btn-primary" >Guardar</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
</form>


<script type="text/javascript">
    $(function(){

        setTimeout(function(){
            $("select").chosen();
        }, 500);

        $("#btn_save").on('click', function(){

            if($("#serie").val() == '') {
                show_msg('warning', '<h4>Error. </h4><p>Inserte una serie.</p>');
                return false;
            }

            if($("#rango").val() == '') {
                show_msg('warning', '<h4>Error. </h4><p>Inserte una serie.</p>');
                return false;
            }


            
            var rango = $("#rango").val();
            var params = {
                'serie': $("#serie").val(), 
                'rango': rango.split(/[(, )(,)( )]+/).join("|"),
            };

            $("#btn_save").attr('disabled','disabled');
            $('#cargando_modal').modal('show');


            $.ajax({
            url: '<?= base_url()?>seriescalzado/save',
            type: 'POST',
            dataType:'json',
            data: params,
            success: function (data) {

                if(data.success=='1'){
                    show_msg('success', '<h4>Serie Guardada con exito</h4>');
                    $.ajax({
                        url: '<?= base_url()?>seriescalzado',
                        type: 'post',
                        success: function (data) {
                            $('#page-content').html(data);
                            $("#cargando_modal").modal('hide');
                            $(".modal-backdrop").remove();
                        }
                    });
                }else{
                    show_msg('warning', '<h4>Error al realizar la operacion</h4>');
                }

             },
            error : function(){
                show_msg('danger', '<h4>Error al realizar la operacion</h4>');
            },
            complete : function(){
                $("#btn_save").removeAttr('disabled');
                $("#cargando_modal").modal('hide');
            }
        });

        });
        

    });

     function validar_rango(valor) { 
        if(valor.split(",").length == 1)
            return true;

        var reg = /[(, )(,)( )]+/;
        if(reg.test(valor))
        {
            return true;
        }else{
            return false;
        }
    }

    
</script>