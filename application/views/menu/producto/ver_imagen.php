<?php $ruta = base_url(); ?>

<script src="<?php echo $ruta; ?>recursos/js/vendor/modernizr-2.7.1-respond-1.4.2.min.js"></script>

<style>


    /**
     * Fix for elevateZoom with jQuery modal
     */
    div.modal-backdrop + div.zoomContainer {
        z-index: 1051;
    }

    /*set a border on the images to prevent shifting*/

    /*Change the colour*/
    #gal2 div a img{border:2px solid white;width: 96px;}
    .active img{border:2px solid #333 !important;}
</style>



<div class="modal-dialog modal-lg"  style="width: 90%">

    <div class="modal-content">

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Imagen del producto: <?php if (isset($producto['producto_nombre'])) echo $producto['producto_nombre'] ?></h4>
        </div>


        <div class="modal-body">
            <div class="tab-content" style="height: auto">

                <div class="form-group">
                        <div class="text-right">
                            <div class="col-md-12" >

                                <div class="col-md-6"  >

                                <?php if (isset($producto['producto_id'])):
                                    if(count($images)>0){
                                    $ruta_imagen = "uploads/" . $producto['producto_id'] . "/";
                                    ?>
                                    <div class="row">
                                        <div class="col-sm-4"  style="text-align: center; margin-bottom: 20px;">
                                            <img width="500" height="500" id="img_02"
                                                 src="<?php echo $ruta . $ruta_imagen . $images[0]; ?>"  data-zoom-image="<?php echo $ruta . $ruta_imagen . $images[0]; ?>">

                                            <br>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6" style=" border-style:outset;" >
                                    <div class="row">
                                        <div class="col-md-7" >
                                          <h5 ><?=  $producto['producto_nombre'] ?> </h5 >
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-1" ></div>
                                        <div class="col-md-5"  style="text-align: left;">
                                           C&oacute;digo: <?=  getCodigoValue(sumCod($producto['producto_id']),$producto['producto_codigo_interno']) ?>
                                            <br>
                                        </div>
                                    </div>
                                    <br>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-1" ></div>
                                        <div class="col-md-5"  style="text-align: left;">
                                           <a style="color: #00CC00 " >Disponible</a> /<a style="color: #CC0000 " >No Disponible</a>
                                            <br>
                                        </div>
                                    </div>
                                    <br>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-1" ></div>
                                        <div class="col-md-5"  style="text-align: left;">
                                            <?=  $producto['producto_titulo_imagen'] ?>
                                        </div>
                                    </div>
                                    <br>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-1" ></div>
                                        <div class="col-md-5" style="width:400px; text-align: left" >
                                            <P ALIGN=center>
                                                <?=  $producto['producto_descripcion_img'] ?>
                                            </P>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12" >
                                    <div class="row" >
                                        <div id="gal2">
                                            <?php foreach ($images as $img): ?>
                                                <div class="col-sm-1" style="text-align: center; margin-bottom: 20px;">

                                                    <a href="#" class="img_show"
                                                       data-image="<?php echo $ruta . $ruta_imagen . $img; ?>" data-zoom-image="<?php echo $ruta . $ruta_imagen . $img; ?>">
                                                        <img width="200" height="300" style="height: 60px; width: 60px"
                                                             src="<?php echo $ruta . $ruta_imagen . $img; ?>">
                                                    </a>
                                                    <br>

                                                </div>


                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                            </div>
                                    <?php  }
                                endif; ?>



                            </div>
                        </div>


                </div>
                <div class="modal-footer">
                    <button  class="btn btn-danger" data-dismiss="modal"
                        >Salir</button>
                </div>

        </div>



    </div>

</div>

<script>

    function addImage(e){
        var file = e.target.files[0],
            imageType = /image.*/;

        if (!file.type.match(imageType))
            return;

        var reader = new FileReader();
        reader.onload = fileOnload;
        reader.readAsDataURL(file);
    }

    function fileOnload(e) {
        var result=e.target.result;
        $('#imgSalida').attr("src",result);
    }



</script>
<script src="<?php echo base_url() ?>recursos/js/pages/uiDraggable.js"></script>
<script>
    $(function () {
       /* {constrainType:"height", constrainSize:274, zoomType: "lens",
            containLensZoom: true, gallery:'gallery_01', cursor: 'pointer',
            galleryActiveClass: "active"});

        {
            cursor: 'crosshair', galleryActiveClass: 'active',
            gallery:'gal1',
            zoomWindowFadeIn: 500,
            zoomWindowFadeOut: 500,
            lensFadeIn: 500,
            lensFadeOut: 500,
            scrollZoom : true
        }*/

        var zoomConfig = {
            cursor: 'crosshair', galleryActiveClass: 'active',
            gallery:'gal2',
            zoomWindowFadeIn: 500,
            zoomWindowFadeOut: 500,
            lensFadeIn: 500,
            lensFadeOut: 500,
            scrollZoom : true
        };
        var image = $('#gal2 div a');
        var zoomImage = $('#img_02');

        zoomImage.elevateZoom(zoomConfig);//initialise zoom

        image.on('click', function(){
            // Remove old instance od EZ
            $('.zoomContainer').remove();
            zoomImage.removeData('elevateZoom');
            // Update source for images
            zoomImage.attr('src', $(this).data('image'));
            zoomImage.data('zoom-image', $(this).data('zoom-image'));
            // Reinitialize EZ
            zoomImage.elevateZoom(zoomConfig);
        });


        $('#input-image').change(function(e) {
            addImage(e);
        });

    });





</script>

