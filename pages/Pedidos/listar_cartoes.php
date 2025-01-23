<?php
include '../../db/db.class.php';
include '../../App/php/function.php';
require_once '../../App/Models/Produtos/Controller.php';

$tipo = $_GET['tipo'] ?? ''; // Obtemos o tipo do cartão (Crédito/Débito)

if ($tipo) {
    $db = new db();
    $db->query("SELECT * FROM cartoes WHERE tipo = :tipo");
    $db->bind(':tipo', $tipo);
    $cartoes = $db->resultSet();

    echo json_encode($cartoes); // Retorna os cartões como JSON
}
