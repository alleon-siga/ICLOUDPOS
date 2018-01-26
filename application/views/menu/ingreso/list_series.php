<?php if (count($series) > 0): ?>
    <?php $n = 1;?>
    <h4>Series existentes de este producto:</h4>
    <?php foreach ($series as $s): ?>
        <div class="row">
            <div class="control-group">
                <div class="col-md-6">
                    <label class="control-label">Serie del Producto <?php echo $n++?>:</label>
                </div>

                <div class="col-md-6">
                    <input type="text" class="form-control serie-number" data-id="<?php echo 'serie'.$s['id'] ?>" readonly value="<?php echo $s['serie'] ?>"/>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
