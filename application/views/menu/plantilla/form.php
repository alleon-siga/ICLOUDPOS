<form name="frm_plantilla" id="frm_plantilla"  action="<?= base_url() ?>plantilla/save" method="post">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Nueva Plantilla</h4>
                <input type="hidden" id="producto_id" value="<?=isset($producto)? $producto->id : ''?>">
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label class="control-label panel-admin-text">Nombre</label>
                        </div>
                        <div class="col-md-9">
                            <input type="text" 
                                name="nombre" 
                                id="nombre"  
                                class="form-control"
                                value="<?=isset($producto)? $producto->nombre : ''?>">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label class="control-label panel-admin-text">Descripcion</label>
                        </div>
                        <div class="col-md-9">
                            <input type="text" 
                                name="descripcion" 
                                id="descripcion"  
                                class="form-control"
                                value="<?=isset($producto)? $producto->descripcion : ''?>">
                        </div>
                    </div>
                </div>

            <?php if(!isset($producto)):?>
                <?php foreach($productos_header as $ph):?>
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label class="control-label panel-admin-text"><?=$ph->tipo?></label>
                        </div>
                        <div class="col-md-9">
                            <select data-tipo_id="<?=$ph->tipo?>" name="<?=$ph->tipo?>" name="<?=$ph->tipo?>" class="data_prop form-control">
                                <option value="">Sin Valor</option>
                                <?php foreach($ph->options as $opt):?>
                                    <option value="<?=$opt->id?>"><?=$opt->valor?></option>
                                <?php endforeach;?>
                            </select>
                        </div>
                    </div>
                </div>
                <?php endforeach;?>
            <?php else:?>
                <?php foreach($producto->propiedades as $prop):?>
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label class="control-label panel-admin-text"><?=$prop->tipo?></label>
                        </div>
                        <div class="col-md-9">
                            <select name="<?=$prop->tipo?>" name="<?=$prop->tipo?>" class="form-control data_prop">
                                <option value="">Sin Valor</option>
                                <?php foreach($prop->options as $opt):?>
                                    <option 
                                        value="<?=$opt->id?>" 
                                        <?=$prop->propiedad_id == $opt->id ? 'selected' : ''?>>
                                    <?=$opt->valor?>
                                    </option>
                                <?php endforeach;?>
                            </select>
                        </div>
                    </div>
                </div>
                <?php endforeach;?>
            <?php endif;?>


            </div>
            <div class="modal-footer">
                <button type="button" id="btn_save" class="btn btn-primary" >Confirmar</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
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

            if($("#nombre").val() == '') {
                show_msg('warning', '<h4>Error. </h4><p>La plantilla debe tener al menos un nombre.</p>');
                return false;
            }
            

            var params = {
                'id': $("#producto_id").val(), 
                'nombre': $("#nombre").val(),
                'descripcion': $("#descripcion").val(),
                'propiedades': prepare_propiedades()
            };


            $("#btn_save").attr('disabled','disabled');
            $('#cargando_modal').modal('show');


            $.ajax({
            url: '<?= base_url()?>plantilla/save',
            type: 'POST',
            dataType:'json',
            data: params,
            success: function (data) {

                if(data.success=='1'){
                    show_msg('success', '<h4>Plantilla Guardada con exito</h4>');
                    $.ajax({
                        url: '<?= base_url()?>plantilla/index',
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

    function prepare_propiedades(){
        var props = $('.data_prop');
        var result = [];
        props.each(function(){
            if($(this).val() != "")
                result.push($(this).val());
        });

        return JSON.stringify(result);
    }
</script>