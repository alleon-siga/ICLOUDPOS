<?php $ruta = base_url(); ?>
<?php $md = get_moneda_defecto() ?>
<div class="content-header content-header-media" style="height: 110px;">
    <div class="header-section">
        <div class="row">
            <!-- Main Title (hidden on small devices for the statistics to fit) -->
            <div class="col-md-4 col-lg-6 hidden-xs hidden-sm">
                <h1>Bienvenido <strong><?= $this->session->userdata('nombre')?></strong><br><small><?= $this->session->userdata('local_nombre')?></small></h1>
            </div>
            <!-- END Main Title -->

            <!-- Top Stats -->
          <div class="col-md-8 col-lg-6">
                <div class="row text-center">
                </div>
            </div>
            <!-- END Top Stats -->
        </div>
    </div>
</div>
<!-- END Dashboard Header -->
<!-- Mini Top Stats Row -->
<div class="row">
    <?php if($this->usuarios_grupos_model->user_has_perm($this->session->userdata('nUsuCodigo'), 'nuevoproducto')) {?>
    <div class="col-sm-6 col-lg-3">
        <!-- Widget -->
        <a href="<?=$ruta?>producto" class="widget widget-hover-effect1 menulink">
            <div class="widget-simple">
                <div class="widget-icon pull-left themed-background-autumn animation-fadeIn">
                    <i class="fa fa-file-text" ></i>
                </div>
                <h3 class="widget-content text-right animation-pullDown">
                    Nuevo <strong>Producto</strong><br>
                    <small></small>
                </h3>
            </div>
        </a>
        <!-- END Widget -->
    </div>
    <?php } ?>
</div>

<!-- Load and execute javascript code used only in this page -->
<script src="<?php echo $ruta; ?>recursos/js/pages/index.js"></script>

