<?php

use App\Models\Fabrica\Controller;

$id = $link[3] ?? null;

if( !empty($id) ) {

    $controller = new Controller();
    $pedido = $controller->registrar($id);
    echo '<meta http-equiv="refresh" content="0; url=' . $url . '!/Fabrica/aberto">';
    

}else{
    echo '<div class="alert alert-danger" role="alert">Ocorreu um erro, id não localizado.</div>';
}