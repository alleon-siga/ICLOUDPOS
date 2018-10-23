<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Error</h4>
        </div>
        <div class="modal-body ">

            <h4 class="text-center text-warning">
                <?= isset($error) ? $error : 'Ha ocurrido un error inesperado.' ?>
            </h4>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal">
                Cerrar
            </button>

        </div>
    </div>
</div>