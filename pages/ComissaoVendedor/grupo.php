

<?php

    include '../../db/db.class.php';
    include '../../App/php/function.php';

?>
<div class="result">
<label class="form-label" for="grupo"> Grupo </label>

<?php echo select('grupo_produtos', 'nome_grupo', 'id', 'id > 0' , '', 'grupo')?>
</div>