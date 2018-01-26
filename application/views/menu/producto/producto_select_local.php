<select class="form-control" id="locales" onchange="getproductosbylocal()">

                    <?php foreach ($locales as $local) { ?>
                        <option value="<?= $local['int_local_id'] ?>"><?= $local['local_nombre'] ?></option>
                    <?php } ?>

                </select>

