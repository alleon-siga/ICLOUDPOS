<?php $ruta = base_url(); ?>


<ul class="breadcrumb breadcrumb-top">
    <li>Inventario </li>
    <li> <a href="">Series Calzado </a> </li>
</ul>

<div class="block">

    <div class="row">
        <div class="form-group">
        	<a class="btn btn-primary">
        		<i class="fa fa-plus" id="new_serie">Nueva Serie Calzando</i>
        	</a>
        </div>
    </div>

    <div class="table-responsive" >
    	<table class="table table-striped dataTable table-bordered" id="example">
    		<thead>
    			<tr>
    				<th>Serie</th>
    				<th>Rango</th>
    				<th>Acciones</th>
    			</tr>
    		</thead>
    		<tbody>
            <?php foreach($series as $s):?>
    			<tr>
    				<td><?=$s->serie?></td>
    				<td><?=str_replace("|", ", ", $s->rango)?></td>
                    <td>
                        <a class="btn btn-default" data-toggle="tooltip"
                            title="Editar" data-original-title="fa fa-comment-o"
                            href="#" onclick="editar('<?=$s->serie?>');">
                        <i class="fa fa-edit"></i>
                        </a>
                    </td>  
    			</tr>
            <?php endforeach;?>
    		</tbody>
    	</table>

    </div>

</div>

<div class="modal fade" id="serie_form" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

</div>
<script src="<?php echo $ruta?>recursos/js/pages/tablesDatatables.js"></script>

<script type="text/javascript">

    $(function(){
        MyTablesDatatables.init(); 

        $("#new_serie").on('click', function(){
            $("#serie_form").html('');
            $("#serie_form").load('<?= $ruta ?>seriescalzado/form');
            $('#serie_form').modal('show');
        });

    });

    function editar($id){
        $("#serie_form").html('');
        $("#serie_form").load('<?= $ruta ?>seriescalzado/form/'+$id);
        $('#serie_form').modal('show');
    }

</script>