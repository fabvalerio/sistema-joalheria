

<?php

    include '../../db/db.class.php';
    include '../../App/php/function.php';

?>

<label for="usuario"> usuario </label>

<?php echo select('usuarios', 'nome_completo', 'id', 'id > 0' , '', 'usuario')?>