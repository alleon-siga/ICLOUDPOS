<?php $ruta = base_url(); ?>


<ul class="breadcrumb breadcrumb-top">
    <li>Inventario </li>
    <li> <a href="">Plantilla </a> </li>
</ul>

<div class="block">

    <div class="row">
        <div class="form-group">
        	<a class="btn btn-primary" id="new_plantilla" onclick="agregarPlantilla()">
        		<i class="fa fa-plus">Nueva Plantilla</i>
        	</a>
        </div>
    </div>

    <div class="table-responsive" >
    	<table class="table table-striped dataTable table-bordered" id="example">
    		<thead>
    			<tr>
    				<th>ID</th>
    				<th>Nombre</th>
    				<th>Descripción</th>
    				<?php foreach($productos_header as $ph):?>
                        <th><?=$ph->tipo?></th>
                    <?php endforeach;?>
    				<th>Acciones</th>
    			</tr>
    		</thead>
    		<tbody>
            <?php foreach($productos as $p):?>
    			<tr>
                    <td><?=$p->id?></td>
    				<td><?=$p->nombre?></td>
    				<td><?=$p->descripcion?></td>
                <?php foreach($p->propiedades as $prop):?>
                        <td><?=$prop->propiedad_valor?></td>
                    <?php endforeach;?>  
                <td>
                    <a class="btn btn-default" data-toggle="tooltip"
                        title="Editar" data-original-title="fa fa-comment-o"
                        href="#" onclick="editar('<?=$p->id?>');">
                    <i class="fa fa-edit"></i>
                    </a>
                </td>  
    			</tr>
            <?php endforeach;?>
    		</tbody>
    	</table>

    </div>

</div>

<div class="modal fade" id="plantilla_form" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

</div>
<script src="<?php echo $ruta?>recursos/js/pages/tablesDatatables.js"></script>

<script type="text/javascript">

    $(function(){
        TablesDatatables.init(); 

        $("#new_plantilla").on('click', function(){
            $("#plantilla_form").html('');
            $("#plantilla_form").load('<?= $ruta ?>plantilla/form');
            $('#plantilla_form').modal('show');
        });

    });

    function editar($id){
        $("#plantilla_form").html('');
            $("#plantilla_form").load('<?= $ruta ?>plantilla/form/'+$id);
            $('#plantilla_form').modal('show');
    }

</script>