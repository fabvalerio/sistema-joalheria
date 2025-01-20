

<?php

    include '../../db/db.class.php';
    include '../../App/php/function.php';

?>
<div class="result">
<label class="form-label" for="usuario">Usuario </label>
<?php echo select('usuarios', 'nome_completo', 'id', 'id > 0' , '', 'usuario')?>
</div>