<?php
include '../../db/db.class.php';
include '../../App/php/function.php';
require_once '../../App/Models/Produtos/Controller.php';

$tipo_definicoes = $_POST['tipo'];
$nome_definicoes = $_POST['novoModelo'];

$db = new db();
// Corrigindo o SQL para utilizar o placeholder :tipo
$db->query("INSERT INTO produto_definicoes (nome, tipo) VALUES (:nome, :tipo)");
$db->bind(':nome', $nome_definicoes);
$db->bind(':tipo', $tipo_definicoes);

if ($db->execute()) {
    echo json_encode([
        'success' => true, 
        'message' => 'Modelo adicionado com sucesso.'
    ]);
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Erro ao adicionar o modelo.'
    ]);
}
?>
