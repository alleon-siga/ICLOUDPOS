<select id="garante">
   <?php foreach ($garantes as $gara) { ?>
     <option value="<?php echo $gara["dni"] ?>"><?php echo $gara["nombre_full"] ?></option>
   <?php  } ?>
</select>
