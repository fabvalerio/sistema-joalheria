

<?php

    include '../../db/db.class.php';
    include '../../App/php/function.php';

?>

<label for="grupo"> Grupo </label>

<?php echo select('grupo_produtos', 'nome_grupo', 'id', 'id > 0' , '', 'grupo')?>